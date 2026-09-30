<?php

namespace App\Policies;

use App\Domains\Modules\Production\Policies\ProductionOrderPolicy as CoreProductionOrderPolicy;

class ProductionOrderPolicy extends CoreProductionOrderPolicy
{
    // Shim class inheriting domain policy logic
}
