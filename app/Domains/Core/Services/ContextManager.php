<?php

namespace App\Domains\Core\Services;

use App\Domains\Core\Models\Branch;
use App\Domains\Core\Models\BusinessUnit;
use Illuminate\Support\Facades\Session;

class ContextManager
{
    public const SESSION_BU_KEY = 'active_bu_id';

    public const SESSION_BRANCH_KEY = 'active_branch_id';

    public const SESSION_ORG_KEY = 'active_org_id';

    public function setActiveBusinessUnit(BusinessUnit $bu): void
    {
        Session::put(self::SESSION_BU_KEY, $bu->id);
        Session::put(self::SESSION_ORG_KEY, $bu->organization_id);
    }

    public function getActiveBusinessUnitId(): ?int
    {
        return Session::get(self::SESSION_BU_KEY);
    }

    public function getActiveOrganizationId(): ?int
    {
        return Session::get(self::SESSION_ORG_KEY);
    }

    public function setActiveBranch(Branch $branch): void
    {
        Session::put(self::SESSION_BRANCH_KEY, $branch->id);
    }

    public function getActiveBranchId(): ?int
    {
        return Session::get(self::SESSION_BRANCH_KEY);
    }

    public function clear(): void
    {
        Session::forget([self::SESSION_BU_KEY, self::SESSION_BRANCH_KEY, self::SESSION_ORG_KEY]);
    }
}
