<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryStructure extends Model
{
    use \App\Traits\BelongsToOrganization;
    
    protected $guarded = ['id'];

    protected $casts = [
        'allowances' => 'array',
        'deductions' => 'array',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    
    public function getGrossSalaryAttribute()
    {
        $allowanceTotal = collect($this->allowances ?? [])->sum('amount');
        return $this->basic_salary + $allowanceTotal;
    }

    public function getTotalDeductionsAttribute()
    {
        return collect($this->deductions ?? [])->sum('amount');
    }
}
