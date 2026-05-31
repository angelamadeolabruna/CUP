<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== TABLES ===\n";
$tables = DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' ORDER BY table_name");
foreach($tables as $t) echo $t->table_name . "\n";

echo "\n=== DOCENTE COLUMNS ===\n";
$cols = DB::select("SELECT column_name, data_type, is_nullable, column_default FROM information_schema.columns WHERE table_name = 'docente' ORDER BY ordinal_position");
foreach($cols as $c) echo "$c->column_name | $c->data_type | nullable:$c->is_nullable | default:$c->column_default\n";

echo "\n=== DOCENTE SEQUENCE ===\n";
try {
    $seq = DB::select("SELECT last_value, is_called FROM docente_id_docente_seq");
    foreach($seq as $s) echo "last_value: $s->last_value | is_called: " . ($s->is_called ? 'true' : 'false') . "\n";
} catch(Exception $e) { echo "Error: " . $e->getMessage() . "\n"; }

echo "\n=== DOCENTE DATA ===\n";
$data = DB::select("SELECT id_docente, ci, nombre_completo FROM docente ORDER BY id_docente");
foreach($data as $d) echo "id:$d->id_docente | ci:$d->ci | name:$d->nombre_completo\n";

echo "\n=== GRUPO COLUMNS ===\n";
$cols = DB::select("SELECT column_name, data_type, is_nullable, column_default FROM information_schema.columns WHERE table_name = 'grupo' ORDER BY ordinal_position");
foreach($cols as $c) echo "$c->column_name | $c->data_type | nullable:$c->is_nullable | default:$c->column_default\n";

echo "\n=== CARGA_HORARIA COLUMNS ===\n";
$cols = DB::select("SELECT column_name, data_type, is_nullable, column_default FROM information_schema.columns WHERE table_name = 'carga_horaria' ORDER BY ordinal_position");
foreach($cols as $c) echo "$c->column_name | $c->data_type | nullable:$c->is_nullable | default:$c->column_default\n";

echo "\n=== POSTULANTE COLUMNS ===\n";
$cols = DB::select("SELECT column_name, data_type, is_nullable, column_default FROM information_schema.columns WHERE table_name = 'postulante' ORDER BY ordinal_position");
foreach($cols as $c) echo "$c->column_name | $c->data_type | nullable:$c->is_nullable | default:$c->column_default\n";

echo "\n=== PAGO COLUMNS ===\n";
$cols = DB::select("SELECT column_name, data_type, is_nullable, column_default FROM information_schema.columns WHERE table_name = 'pago' ORDER BY ordinal_position");
foreach($cols as $c) echo "$c->column_name | $c->data_type | nullable:$c->is_nullable | default:$c->column_default\n";

echo "\n=== USUARIO COLUMNS ===\n";
$cols = DB::select("SELECT column_name, data_type, is_nullable, column_default FROM information_schema.columns WHERE table_name = 'usuario' ORDER BY ordinal_position");
foreach($cols as $c) echo "$c->column_name | $c->data_type | nullable:$c->is_nullable | default:$c->column_default\n";

echo "\n=== NOTA COLUMNS ===\n";
$cols = DB::select("SELECT column_name, data_type, is_nullable, column_default FROM information_schema.columns WHERE table_name = 'nota' ORDER BY ordinal_position");
foreach($cols as $c) echo "$c->column_name | $c->data_type | nullable:$c->is_nullable | default:$c->column_default\n";

echo "\n=== GRUPO_POSTULANTE COLUMNS ===\n";
$cols = DB::select("SELECT column_name, data_type, is_nullable, column_default FROM information_schema.columns WHERE table_name = 'grupo_postulante' ORDER BY ordinal_position");
foreach($cols as $c) echo "$c->column_name | $c->data_type | nullable:$c->is_nullable | default:$c->column_default\n";

echo "\n=== BITACORA_AUDITORIA COLUMNS ===\n";
$cols = DB::select("SELECT column_name, data_type, is_nullable, column_default FROM information_schema.columns WHERE table_name = 'bitacora_auditoria' ORDER BY ordinal_position");
foreach($cols as $c) echo "$c->column_name | $c->data_type | nullable:$c->is_nullable | default:$c->column_default\n";

echo "\n=== ROL COLUMNS ===\n";
$cols = DB::select("SELECT column_name, data_type, is_nullable, column_default FROM information_schema.columns WHERE table_name = 'rol' ORDER BY ordinal_position");
foreach($cols as $c) echo "$c->column_name | $c->data_type | nullable:$c->is_nullable | default:$c->column_default\n";

echo "\n=== CARRERA TABLE ===\n";
try {
    $cols = DB::select("SELECT column_name, data_type, is_nullable FROM information_schema.columns WHERE table_name = 'carrera' ORDER BY ordinal_position");
    foreach($cols as $c) echo "$c->column_name | $c->data_type | nullable:$c->is_nullable\n";
} catch(Exception $e) { echo "No carrera table\n"; }

echo "\n=== ALL CONSTRAINTS ON DOCENTE ===\n";
$constraints = DB::select("SELECT conname, pg_get_constraintdef(oid) as def FROM pg_constraint WHERE conrelid = 'docente'::regclass");
foreach($constraints as $c) echo "$c->conname: $c->def\n";
