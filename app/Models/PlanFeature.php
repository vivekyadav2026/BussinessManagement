<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanFeature extends Model
{
    protected $guarded = ['id'];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
