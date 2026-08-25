<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use \App\Traits\BelongsToOrganization;

    protected $fillable = [
        'organization_id', 'user_id', 'employee_code', 'first_name', 
        'last_name', 'email', 'phone', 'address', 'joining_date', 
        'designation', 'status', 'location_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function getFullNameAttribute()
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
    
    public function salaryStructure()
    {
        return $this->hasOne(SalaryStructure::class);
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }
}
