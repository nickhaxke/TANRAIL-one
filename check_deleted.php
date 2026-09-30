<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$item = \App\Domains\Core\Models\Item::withoutGlobalScopes()->where('name', 'like', '%afya%')->first();
echo "deleted_at: " . $item->deleted_at . "\n";
