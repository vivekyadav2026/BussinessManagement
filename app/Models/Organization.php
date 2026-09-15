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
        'business_type',
        'gst_number', 'cgst_percent', 'sgst_percent',
        'upi_id',
        'logo',
        'default_check_in',
        'default_check_out',
        'is_active',
    ];


    public function getLogoUrlAttribute()
    {
        if (!$this->logo) {
            return null;
        }
        if (str_starts_with($this->logo, 'http://') || str_starts_with($this->logo, 'https://')) {
            return $this->logo;
        }
        if (file_exists(public_path('uploads/' . $this->logo))) {
            return asset('uploads/' . $this->logo);
        }
        return asset('storage/' . $this->logo);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(OrganizationSubscription::class)
            ->whereHas('plan', function($q) {
                $q->where('type', 'base');
            })
            ->whereIn('status', ['Active', 'Trial'])
            ->where(function($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()->toDateString());
            })->latest();
    }
    
    public function activeAddons()
    {
        return $this->hasMany(OrganizationSubscription::class)
            ->whereHas('plan', function($q) {
                $q->where('type', 'addon');
            })
            ->whereIn('status', ['Active', 'Trial'])
            ->where(function($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()->toDateString());
            });
    }

    public function subscriptions()
    {
        return $this->hasMany(OrganizationSubscription::class);
    }

    public function latestSubscription()
    {
        return $this->hasOne(OrganizationSubscription::class)->latest();
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
