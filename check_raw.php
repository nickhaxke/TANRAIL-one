<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$raw = \Illuminate\Support\Facades\DB::select("SELECT status FROM items WHERE name LIKE '%afya%'");
print_r($raw);
