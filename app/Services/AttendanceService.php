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
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();

        $presentCount = $attendances->where('status', 'Present')->count();
        $absentCount = $attendances->where('status', 'Absent')->count();
        $halfDayCount = $attendances->where('status', 'Half Day')->count();
        $leaveCount = $attendances->where('status', 'Leave')->count();

        // Additive Logic: Only explicitly recorded Present and Paid Leave days count, plus 0.5 for Half Days.
        $effectiveWorkingDays = $presentCount + $leaveCount + ($halfDayCount * 0.5);

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
