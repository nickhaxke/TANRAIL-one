<?php

namespace App\Policies;

use App\Domains\Modules\Production\Policies\BillOfMaterialsPolicy as CoreBillOfMaterialsPolicy;

class BillOfMaterialsPolicy extends CoreBillOfMaterialsPolicy
{
    // Shim class inheriting domain policy logic
}
