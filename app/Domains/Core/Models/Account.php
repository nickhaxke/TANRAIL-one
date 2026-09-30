<?php

namespace App\Domains\Core\Models;

use App\Domains\Core\Enums\AccountType;
use App\Domains\Core\Exceptions\AccountHierarchyException;
use App\Domains\Core\Exceptions\CrossOrganizationException;
use App\Domains\Core\Scopes\OrganizationScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'parent_id',
        'code',
        'name',
        'type',
        'is_active',
    ];

    protected $casts = [
        'type' => AccountType::class,
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new OrganizationScope);

        static::saving(function (Account $account) {
            if ($account->parent_id !== null) {
                // Prevent self-parenting
                if ($account->id && (int) $account->parent_id === (int) $account->id) {
                    throw new AccountHierarchyException('An account cannot be its own parent.');
                }

                $parent = Account::withoutGlobalScopes()->find($account->parent_id);
                if (! $parent) {
                    throw new AccountHierarchyException("Parent account {$account->parent_id} does not exist.");
                }

                // Prevent cross-Organization parent relationships
                if ($parent->organization_id !== $account->organization_id) {
                    throw new CrossOrganizationException("Parent account {$parent->id} does not belong to Organization {$account->organization_id}.");
                }

                // Prevent circular hierarchy
                $ancestor = $parent;
                $visited = $account->id ? [$account->id] : [];
                while ($ancestor) {
                    if ($account->id && $ancestor->id === $account->id) {
                        throw new AccountHierarchyException('Circular account hierarchy detected.');
                    }
                    if (in_array($ancestor->id, $visited, true)) {
                        throw new AccountHierarchyException('Circular account hierarchy detected.');
                    }
                    $visited[] = $ancestor->id;
                    $ancestor = $ancestor->parent_id ? Account::withoutGlobalScopes()->find($ancestor->parent_id) : null;
                }
            }
        });
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Account::class, 'parent_id');
    }
}
