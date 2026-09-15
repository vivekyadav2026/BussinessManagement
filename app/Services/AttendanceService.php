<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AttendanceService
{
    /**
     * Get a summary of attendance for an employee for a given month and year.
     * Prepares data suitable for payroll calculations.
     */
    public static function getMonthlySummary(Employee $employee, $month, $year): array
    {
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get();

        $presentCount = $attendances->where('status', 'Present')->count();
        $absentCount = $attendances->where('status', 'Absent')->count();
        $halfDayCount = $attendances->where('status', 'Half Day')->count();
        $leaveCount = $attendances->where('status', 'Leave')->count();

        // SME Deductive Logic: Unrecorded days are assumed Present/Paid. Only deduct explicit Absents and Half Days.
        $daysInMonth = $startDate->daysInMonth;
        
        $daysBeforeJoining = 0;
        if ($employee->joining_date) {
            $joiningDate = Carbon::parse($employee->joining_date)->startOfDay();
            if ($joiningDate->gt($startDate)) {
                $daysBeforeJoining = min($startDate->diffInDays($joiningDate), $daysInMonth);
            }
        }

        $effectiveWorkingDays = $daysInMonth - $daysBeforeJoining - $absentCount - ($halfDayCount * 0.5);
        if ($effectiveWorkingDays < 0) {
            $effectiveWorkingDays = 0;
        }

        return [
            'present' => $presentCount,
            'absent' => $absentCount,
            'half_days' => $halfDayCount,
            'leaves' => $leaveCount,
            'effective_working_days' => $effectiveWorkingDays,
            'total_recorded_days' => $attendances->count(),
        ];
    }
}
