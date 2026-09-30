<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$branches = \App\Domains\Core\Models\Branch::get()->toArray();
print_r($branches);
