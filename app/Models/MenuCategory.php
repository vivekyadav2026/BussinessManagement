<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuCategory extends Model
{
    use \App\Traits\BelongsToOrganization;
    use \App\Traits\BelongsToLocation;

    protected $guarded = ['id'];

    public function items()
    {
        return $this->hasMany(MenuItem::class)->orderBy('sort_order');
    }
}
