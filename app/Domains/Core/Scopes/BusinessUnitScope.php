<?php

namespace App\Domains\Core\Scopes;

use App\Domains\Core\Services\ContextManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class BusinessUnitScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        if (app()->runningInConsole() && ! app()->runningUnitTests()) {
            return;
        }

        $contextManager = app(ContextManager::class);
        $buId = $contextManager->getActiveBusinessUnitId();

        if ($buId) {
            $builder->where($model->getTable().'.business_unit_id', $buId);
        } else {
            // Fail securely if no context is active but scope is applied
            $builder->whereRaw('1 = 0');
        }
    }
}
