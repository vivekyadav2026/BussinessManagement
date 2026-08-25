<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationSubscription extends Model
{
    use \App\Traits\BelongsToOrganization;

    protected $guarded = ['id'];

    protected $casts = [
        'starts_at' => 'date',
        'ends_at' => 'date',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
