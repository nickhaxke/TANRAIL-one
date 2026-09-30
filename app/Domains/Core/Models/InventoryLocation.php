<?php

namespace App\Domains\Core\Models;

use App\Domains\Core\Scopes\InventoryScope;
use Database\Factories\InventoryLocationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryLocation extends Model
{
    /** @use HasFactory<InventoryLocationFactory> */
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'name',
        'code',
        'status',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new InventoryScope);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    protected static function newFactory()
    {
        return InventoryLocationFactory::new();
    }

    public function stockBalances(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StockBalance::class);
    }
}
