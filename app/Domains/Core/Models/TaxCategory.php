<?php

namespace App\Domains\Core\Models;

use App\Domains\Core\Exceptions\CrossOrganizationException;
use Database\Factories\TaxCategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaxCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'code',
        'rate',
        'description',
        'liability_account_id',
    ];

    protected $casts = [
        'rate' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function (TaxCategory $category) {
            if ($category->organization_id !== null && $category->liability_account_id !== null) {
                $account = Account::withoutGlobalScopes()->find($category->liability_account_id);
                if ($account && $account->organization_id !== $category->organization_id) {
                    throw new CrossOrganizationException("Tax liability account {$account->id} does not belong to Organization {$category->organization_id}.");
                }
            }
        });
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function liabilityAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'liability_account_id');
    }

    protected static function newFactory()
    {
        return TaxCategoryFactory::new();
    }
}
