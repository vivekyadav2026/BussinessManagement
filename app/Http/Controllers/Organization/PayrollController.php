<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\SalaryStructure;
use App\Services\PayrollService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $orgId = auth()->user()->organization_id;
        
        $month = $request->input('month', now()->subMonth()->month);
        $year = $request->input('year', now()->subMonth()->year);
        $dateObj = Carbon::create($year, $month, 1);
        
        $employees = Employee::where('organization_id', $orgId)
            ->with(['payrolls' => function($q) use ($month, $year) {
                $q->where('month', $month)->where('year', $year);
            }, 'salaryStructure'])
            ->get();

        return view('organization.payroll.index', compact('employees', 'dateObj', 'month', 'year'));
    }

    public function generate(Request $request)
    {
        $orgId = auth()->user()->organization_id;
        $month = $request->input('month');
        $year = $request->input('year');
        
        $employees = Employee::where('organization_id', $orgId)
            ->whereHas('salaryStructure')
            ->get();

        $count = 0;
        foreach ($employees as $emp) {
            $payroll = PayrollService::calculateAndDraft($emp, $month, $year);
            if ($payroll) $count++;
        }

        return back()->with('success', "Drafted payroll for $count employees.");
    }
    
    public function show(Payroll $payroll)
    {
        abort_if($payroll->organization_id !== auth()->user()->organization_id, 403);
        $payroll->load('employee');
        $dateObj = Carbon::create($payroll->year, $payroll->month, 1);
        return view('organization.payroll.show', compact('payroll', 'dateObj'));
    }

    public function updateAdjustment(Request $request, Payroll $payroll)
    {
        abort_if($payroll->organization_id !== auth()->user()->organization_id, 403);
        
        if ($payroll->status === 'Paid') {
            return back()->with('error', 'Cannot modify a paid salary record.');
        }

        $request->validate([
            'manual_adjustment' => 'required|numeric',
            'adjustment_reason' => 'required_unless:manual_adjustment,0|string|nullable',
        ]);

        $netSalary = $payroll->earned_gross - $payroll->total_deductions + $request->manual_adjustment;

        $payroll->update([
            'manual_adjustment' => $request->manual_adjustment,
            'adjustment_reason' => $request->adjustment_reason,
            'net_salary' => max(0, $netSalary)
        ]);

        return back()->with('success', 'Payroll adjustment saved.');
    }

    public function markPaid(Request $request, Payroll $payroll)
    {
        abort_if($payroll->organization_id !== auth()->user()->organization_id, 403);
        
        $request->validate([
            'payment_method' => 'required|string',
            'payment_date' => 'required|date'
        ]);

        $payroll->update([
            'status' => 'Paid',
            'payment_method' => $request->payment_method,
            'payment_date' => $request->payment_date
        ]);

        return back()->with('success', 'Payroll marked as paid.');
    }
}
