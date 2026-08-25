<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\SalaryStructure;

class SalaryStructureController extends Controller
{
    public function show(Employee $employee)
    {
        abort_if($employee->organization_id !== auth()->user()->organization_id, 403);
        
        $structure = $employee->salaryStructure;
        
        return view('organization.payroll.structure', compact('employee', 'structure'));
    }

    public function store(Request $request, Employee $employee)
    {
        abort_if($employee->organization_id !== auth()->user()->organization_id, 403);
        
        $request->validate([
            'basic_salary' => 'required|numeric|min:0',
            'allowances' => 'nullable|array',
            'deductions' => 'nullable|array',
        ]);
        
        // Clean up empty arrays
        $allowances = collect($request->allowances)->filter(fn($i) => !empty($i['name']) && !empty($i['amount']))->values()->toArray();
        $deductions = collect($request->deductions)->filter(fn($i) => !empty($i['name']) && !empty($i['amount']))->values()->toArray();

        SalaryStructure::updateOrCreate(
            ['employee_id' => $employee->id],
            [
                'organization_id' => $employee->organization_id,
                'basic_salary' => $request->basic_salary,
                'allowances' => $allowances,
                'deductions' => $deductions,
            ]
        );

        return back()->with('success', 'Salary structure updated successfully.');
    }
}
