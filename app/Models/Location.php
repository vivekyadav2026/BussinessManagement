<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use \App\Traits\BelongsToOrganization;

    protected $fillable = ['organization_id', 'name', 'address', 'phone', 'is_active'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_locations');
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
