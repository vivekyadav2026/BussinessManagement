<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Attendance;
use App\Services\LocationManager;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $orgId = auth()->user()->organization_id;
        $locationId = LocationManager::getActiveLocationId();
        
        $date = $request->input('date', now()->toDateString());
        $dateObj = Carbon::parse($date);

        $employees = Employee::where('organization_id', $orgId)
            ->where('location_id', $locationId)
            ->where('status', 'Active')
            ->with(['attendances' => function($q) use ($date) {
                $q->where('date', $date);
            }])
            ->orderBy('first_name')
            ->get();

        return view('organization.attendance.index', compact('employees', 'date', 'dateObj'));
    }

    public function storeBulk(Request $request)
    {
        $orgId = auth()->user()->organization_id;
        $locationId = LocationManager::getActiveLocationId();
        
        $request->validate([
            'date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*.status' => 'required|in:Present,Absent,Half Day,Leave',
        ]);

        $date = $request->date;
        DB::transaction(function () use ($request, $orgId, $locationId, $date) {
            foreach ($request->attendance as $employeeId => $data) {
                // Ensure employee belongs to org and location
                $emp = Employee::where('id', $employeeId)
                    ->where('organization_id', $orgId)
                    ->where('location_id', $locationId)
                    ->first();
                    
                if (!$emp) dd("NOT FOUND", $employeeId, $orgId, $locationId);

                $existing = Attendance::where('employee_id', $emp->id)->whereDate('date', $date)->first();

                if ($existing) {
                    $existing->status = $data['status'];
                    $existing->check_in = $data['check_in'] ?? null;
                    $existing->check_out = $data['check_out'] ?? null;
                    $existing->save();
                } else {
                    Attendance::create([
                        'employee_id' => $emp->id,
                        'date' => $date,
                        'organization_id' => $orgId,
                        'location_id' => $locationId,
                        'status' => $data['status'],
                        'check_in' => $data['check_in'] ?? null,
                        'check_out' => $data['check_out'] ?? null,
                    ]);
                }
                
            }
        });

        return back()->with('success', 'Attendance recorded successfully for ' . Carbon::parse($date)->format('M d, Y'));
    }

    public function show(Employee $employee, Request $request)
    {
        abort_if($employee->organization_id !== auth()->user()->organization_id, 403);

        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        $dateObj = Carbon::create($year, $month, 1);

        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get()
            ->keyBy(function($item) {
                return $item->date->format('Y-m-d');
            });

        $summary = AttendanceService::getMonthlySummary($employee, $month, $year);

        return view('organization.attendance.show', compact('employee', 'dateObj', 'attendances', 'summary'));
    }

    public function report(Request $request)
    {
        $orgId = auth()->user()->organization_id;
        $locationId = LocationManager::getActiveLocationId();
        
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        
        $employees = Employee::where('organization_id', $orgId)
            ->where('location_id', $locationId)
            ->get();
            
        $reportData = [];
        foreach ($employees as $emp) {
            $reportData[] = [
                'employee' => $emp,
                'summary' => AttendanceService::getMonthlySummary($emp, $month, $year)
            ];
        }

        $dateObj = Carbon::create($year, $month, 1);

        return view('organization.attendance.report', compact('reportData', 'dateObj'));
    }
}
