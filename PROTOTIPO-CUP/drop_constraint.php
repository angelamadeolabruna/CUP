<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    DB::statement('ALTER TABLE pago DROP CONSTRAINT pago_estado_pago_check;');
    echo "Constraint dropped successfully.\n";
} catch (\Exception $e) {
    echo "Error dropping constraint: " . $e->getMessage() . "\n";
}
