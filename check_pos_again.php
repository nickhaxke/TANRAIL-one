<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Just fetch POs without scopes
$pos = App\Domains\Core\Models\PurchaseOrder::withoutGlobalScopes()->get();
echo "Total POs: " . $pos->count() . "\n";
foreach($pos as $po) {
    echo "ID: {$po->id}, BU: {$po->business_unit_id}, Status: {$po->status->value}\n";
}
