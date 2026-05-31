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
        Schema::table('postulante', function (Blueprint $table) {
            $table->string('carrera_elegida_1')->nullable();
            $table->string('carrera_elegida_2')->nullable();
            $table->string('id_referencia_pago')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('postulante', function (Blueprint $table) {
            $table->dropColumn(['carrera_elegida_1', 'carrera_elegida_2', 'id_referencia_pago']);
        });
    }
};
