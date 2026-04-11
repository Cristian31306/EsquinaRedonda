<?php
require dirname(__DIR__) . '/vendor/autoload.php';
$app = require_once dirname(__DIR__) . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Tenant;
use Illuminate\Support\Str;

$updated = 0;
Tenant::whereNull('api_token')->get()->each(function($t) use (&$updated) {
    if (empty($t->api_token)) {
        $t->api_token = Str::random(60);
        $t->save();
        $updated++;
    }
});

echo "Tokens actualizados para $updated inquilinos.\n";
