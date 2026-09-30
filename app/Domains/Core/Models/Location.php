<?php

namespace App\Domains\Core\Models;

use App\Domains\Core\Scopes\OrganizationScope;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $guarded = [];

    protected static function booted(): void
    {
        static::addGlobalScope(new OrganizationScope);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
