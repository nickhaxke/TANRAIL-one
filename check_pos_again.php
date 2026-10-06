<?php

use App\Domains\Core\Models\PurchaseOrder;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

// Just fetch POs without scopes
$pos = PurchaseOrder::withoutGlobalScopes()->get();
echo 'Total POs: '.$pos->count()."\n";
foreach ($pos as $po) {
    echo "ID: {$po->id}, BU: {$po->business_unit_id}, Status: {$po->status->value}\n";
}
