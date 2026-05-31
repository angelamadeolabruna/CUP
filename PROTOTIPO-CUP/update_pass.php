<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$u = \App\Models\Usuario::find(1);
if($u) {
    $u->password_hash = bcrypt('123456');
    $u->save();
    echo "OK";
}
