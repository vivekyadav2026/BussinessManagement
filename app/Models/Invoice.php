<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use \App\Traits\BelongsToOrganization;
    use \App\Traits\BelongsToLocation;

    protected $guarded = ['id'];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function getAmountDueAttribute()
    {
        return max(0, $this->grand_total - $this->amount_paid);
    }

    public function getEffectiveCgstAttribute()
    {
        if (isset($this->cgst) && (float)$this->cgst > 0) {
            return (float) $this->cgst;
        }
        if (isset($this->tax) && (float)$this->tax > 0 && (!isset($this->sgst) || (float)$this->sgst <= 0)) {
            return round((float) $this->tax / 2, 2);
        }
        $cgstPercent = $this->organization ? (float) $this->organization->cgst_percent : 0;
        if ($cgstPercent > 0 && (float)$this->subtotal > 0) {
            return round(((float)$this->subtotal * $cgstPercent) / 100, 2);
        }
        return 0;
    }

    public function getEffectiveSgstAttribute()
    {
        if (isset($this->sgst) && (float)$this->sgst > 0) {
            return (float) $this->sgst;
        }
        if (isset($this->tax) && (float)$this->tax > 0 && (!isset($this->cgst) || (float)$this->cgst <= 0)) {
            return round((float) $this->tax / 2, 2);
        }
        $sgstPercent = $this->organization ? (float) $this->organization->sgst_percent : 0;
        if ($sgstPercent > 0 && (float)$this->subtotal > 0) {
            return round(((float)$this->subtotal * $sgstPercent) / 100, 2);
        }
        return 0;
    }
}
