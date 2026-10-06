<?php

use App\Domains\Core\Models\Branch;
use Illuminate\Contracts\Console\Kernel;

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();
$branches = Branch::get()->toArray();
print_r($branches);
