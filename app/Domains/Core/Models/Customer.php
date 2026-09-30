<?php

namespace App\Domains\Core\Models;

use App\Domains\Core\Enums\CustomerType;
use App\Domains\Core\Scopes\OrganizationScope;
use Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = ['organization_id', 'name', 'email', 'phone', 'type', 'status'];

    protected static function booted(): void
    {
        static::addGlobalScope(new OrganizationScope);
    }

    protected function casts(): array
    {
        return [
            'type' => CustomerType::class,
            'status' => 'boolean',
        ];
    }

    protected static function newFactory()
    {
        return CustomerFactory::new();
    }
}
