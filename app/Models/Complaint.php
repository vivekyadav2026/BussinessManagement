<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use \App\Traits\BelongsToOrganization;
    
    protected $guarded = ['id'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function reporter()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function assignee()
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }
}
