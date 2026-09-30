<?php

namespace App\Domains\Core\Models;

use Database\Factories\OrganizationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function businessUnits()
    {
        return $this->hasMany(BusinessUnit::class);
    }

    protected static function newFactory()
    {
        return OrganizationFactory::new();
    }
}
