<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Payroll;
use App\Services\AttendanceService;
use Carbon\Carbon;

class PayrollService
{
    /**
     * Calculates and drafts a payroll record for a given employee and month.
     * Does NOT update if the payroll is already Paid.
     */
    public static function calculateAndDraft(Employee $employee, $month, $year): ?Payroll
    {
        $existing = Payroll::where('employee_id', $employee->id)
            ->where('month', $month)
            ->where('year', $year)
            ->first();

        // Lock if paid
        if ($existing && $existing->status === 'Paid') {
            return $existing;
        }

        $structure = $employee->salaryStructure;
        if (!$structure) {
            // Cannot process payroll without a structure
            return null;
        }

        $attendanceSummary = AttendanceService::getMonthlySummary($employee, $month, $year);
        $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;
        
        // Capped at days in month in case of data oddities
        $effectiveWorkingDays = min($attendanceSummary['effective_working_days'], $daysInMonth);
        
        $baseGross = $structure->gross_salary;
        $totalDeductions = $structure->total_deductions;
        
        // Prorated Gross based on attendance
        $dailyWage = $baseGross / $daysInMonth;
        $earnedGross = round($dailyWage * $effectiveWorkingDays, 2);
        
        // Base Net Calculation
        $manualAdjustment = $existing ? $existing->manual_adjustment : 0;
        $netSalary = $earnedGross - $totalDeductions + $manualAdjustment;

        // Prevent negative salary
        if ($netSalary < 0) {
            $netSalary = 0;
        }

        $attributes = [
            'organization_id' => $employee->organization_id,
            'basic_salary' => $structure->basic_salary,
            'allowances' => $structure->allowances,
            'deductions' => $structure->deductions,
            'days_in_month' => $daysInMonth,
            'effective_working_days' => $effectiveWorkingDays,
            'earned_gross' => $earnedGross,
            'total_deductions' => $totalDeductions,
            'net_salary' => $netSalary,
        ];

        if ($existing) {
            $existing->update($attributes);
            return $existing;
        } else {
            return Payroll::create(array_merge([
                'employee_id' => $employee->id,
                'month' => $month,
                'year' => $year,
            ], $attributes));
        }
    }
}
