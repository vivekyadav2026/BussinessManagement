<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Client extends Model
{
    use Notifiable, \App\Traits\BelongsToOrganization;

    protected $fillable = [
        'organization_id', 'name', 'phone', 'email', 'address', 'gst_number', 'notes', 'is_active'
    ];

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function getTotalPurchasedAttribute()
    {
        return \App\Services\ClientFinancialService::getTotalPurchased($this);
    }

    public function getTotalPaidAttribute()
    {
        return \App\Services\ClientFinancialService::getTotalPaid($this);
    }

    public function getOutstandingAmountAttribute()
    {
        return \App\Services\ClientFinancialService::getOutstandingAmount($this);
    }

    public function getOverdueAmountAttribute()
    {
        return \App\Services\ClientFinancialService::getOverdueAmount($this);
    }
}
