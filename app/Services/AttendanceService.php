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

        // Prevent looking into the future
        if ($endDate->isFuture()) {
            $endDate = now()->endOfDay();
        }

        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get();

        $presentCount = $attendances->where('status', 'Present')->count();
        $absentCount = $attendances->where('status', 'Absent')->count();
        $halfDayCount = $attendances->where('status', 'Half Day')->count();
        $leaveCount = $attendances->where('status', 'Leave')->count();

        // Payroll effective working days logic (1 half day = 0.5 days)
        $effectiveWorkingDays = $presentCount + ($halfDayCount * 0.5);

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
