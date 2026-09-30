<?php

namespace App\Domains\Core\Models;

use Illuminate\Database\Eloquent\Model;

class ItemCategory extends Model
{
    protected $fillable = ['business_unit_id', 'name', 'description', 'status'];

    public function items()
    {
        return $this->hasMany(Item::class, 'category_id');
    }
}
