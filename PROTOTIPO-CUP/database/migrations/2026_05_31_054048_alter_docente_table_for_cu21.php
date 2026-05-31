<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('docente', function (Blueprint $table) {
            $table->unsignedBigInteger('id_usuario')->nullable()->change();
            
            $table->string('ci')->nullable();
            $table->string('nombre_completo')->nullable();
            $table->boolean('tiene_maestria')->default(false);
            $table->boolean('tiene_diplomado')->default(false);
            $table->boolean('estado_habilitado')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('docente', function (Blueprint $table) {
            $table->unsignedBigInteger('id_usuario')->nullable(false)->change();
            $table->dropColumn(['ci', 'nombre_completo', 'tiene_maestria', 'tiene_diplomado', 'estado_habilitado']);
        });
    }
};
