<?php

namespace App\Domains\Core\Models;

use App\Domains\Core\Scopes\OrganizationScope;
use Database\Factories\SupplierFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = ['organization_id', 'name', 'contact_details', 'tax_number', 'status'];

    protected static function booted(): void
    {
        static::addGlobalScope(new OrganizationScope);
    }

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    protected static function newFactory()
    {
        return SupplierFactory::new();
    }
}
