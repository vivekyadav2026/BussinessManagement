<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GatewayPayment extends Model
{
    protected $guarded = ['id'];

    public function payable()
    {
        return $this->morphTo('entity');
    }
}
