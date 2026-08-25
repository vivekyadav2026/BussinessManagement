<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'gst_number',
        'logo',
        'is_active',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Placeholder relationships for future modules
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(OrganizationSubscription::class)
            ->where('status', 'Active')
            ->where(function($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()->toDateString());
            })->latest();
    }

    public function products()
    {
        // return $this->hasMany(Product::class);
    }

    public function clients()
    {
        // return $this->hasMany(Client::class);
    }

    public function invoices()
    {
        // return $this->hasMany(Invoice::class);
    }
}
