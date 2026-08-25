<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use \App\Traits\BelongsToOrganization;
    
    protected $guarded = ['id'];

    protected $casts = [
        'allowances' => 'array',
        'deductions' => 'array',
        'payment_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
