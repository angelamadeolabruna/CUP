<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$sequences = [
    'docente_id_docente_seq' => 'docente' ,
    'grupo_id_grupo_seq' => 'grupo',
    'pago_id_pago_seq' => 'pago',
    'postulante_id_postulante_seq' => 'postulante',
    'usuario_id_usuario_seq' => 'usuario',
    'bitacora_auditoria_id_bitacora_seq' => 'bitacora_auditoria',
    'rol_id_rol_seq' => 'rol',
    'grupo_postulante_id_grupo_postulante_seq' => 'grupo_postulante',
];

$pkMap = [
    'docente' => 'id_docente',
    'grupo' => 'id_grupo',
    'pago' => 'id_pago',
    'postulante' => 'id_postulante',
    'usuario' => 'id_usuario',
    'bitacora_auditoria' => 'id_bitacora',
    'rol' => 'id_rol',
    'grupo_postulante' => 'id_grupo_postulante',
];

foreach ($sequences as $seq => $table) {
    try {
        $pk = $pkMap[$table];
        $max = DB::select("SELECT COALESCE(MAX($pk), 0) as max_val FROM $table")[0]->max_val;
        if ($max > 0) {
            DB::statement("SELECT setval('$seq', $max)");
        }
        $current = DB::select("SELECT last_value FROM $seq")[0]->last_value;
        echo "✓ $seq => fixed to $current (max in table: $max)\n";
    } catch (Exception $e) {
        echo "✗ $seq => ERROR: " . $e->getMessage() . "\n";
    }
}

echo "\nAll sequences synchronized!\n";
