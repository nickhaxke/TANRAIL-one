<?php

namespace App\Domains\Core\Scopes;

use App\Domains\Core\Services\ContextManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class InventoryScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        if (app()->runningInConsole() && ! app()->runningUnitTests()) {
            return;
        }

        $contextManager = app(ContextManager::class);
        $buId = $contextManager->getActiveBusinessUnitId();

        if (! $buId) {
            $builder->whereRaw('1 = 0');

            return;
        }

        $table = $model->getTable();

        if ($table === 'inventory_locations') {
            $builder->whereIn("$table.branch_id", function ($query) use ($buId) {
                $query->select('id')->from('branches')->where('business_unit_id', $buId);
            });
        } else {
            // For stock_balances, stock_movements, and stock_transfers, they all contain item_id.
            // Items are strictly isolated to Business Units in Phase 3.1.
            $builder->whereIn("$table.item_id", function ($query) use ($buId) {
                $query->select('id')->from('items')->where('business_unit_id', $buId);
            });
        }
    }
}
