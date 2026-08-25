<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use \App\Traits\BelongsToOrganization;

    protected $fillable = ['organization_id', 'name', 'slug', 'description', 'is_active'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
