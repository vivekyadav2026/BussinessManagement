<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RestaurantTable extends Model
{
    use \App\Traits\BelongsToOrganization;
    use \App\Traits\BelongsToLocation;

    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->public_token)) {
                $model->public_token = Str::random(16);
            }
        });
    }

    public function generateNewToken()
    {
        $this->update(['public_token' => Str::random(16)]);
    }
}
