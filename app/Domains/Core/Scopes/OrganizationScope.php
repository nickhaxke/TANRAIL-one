<?php

namespace App\Domains\Core\Scopes;

use App\Domains\Core\Services\ContextManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class OrganizationScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        // Only apply in HTTP context where app() can resolve the session.
        if (app()->runningInConsole() && ! app()->runningUnitTests()) {
            return;
        }

        $contextManager = app(ContextManager::class);
        $orgId = $contextManager->getActiveOrganizationId();

        if ($orgId) {
            $builder->where($model->getTable().'.organization_id', $orgId);
        } else {
            // Fail securely if no context is active but scope is applied
            $builder->whereRaw('1 = 0');
        }
    }
}
