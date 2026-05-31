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
            $table->integer('cantidad_grupos_actual')->default(0);
        });

        Schema::table('grupo', function (Blueprint $table) {
            $table->string('horario_asignado')->nullable(); // Ej: Lunes 08:00 - 10:00
            $table->string('materia_asociada')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('docente', function (Blueprint $table) {
            $table->dropColumn('cantidad_grupos_actual');
        });

        Schema::table('grupo', function (Blueprint $table) {
            $table->dropColumn(['horario_asignado', 'materia_asociada']);
        });
    }
};
