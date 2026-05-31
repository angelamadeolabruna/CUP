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
        Schema::create('nota', function (Blueprint $table) {
            $table->uuid('id_nota')->primary();
            $table->string('ci_postulante');
            $table->string('materia');
            $table->float('valor_puntaje');
            $table->integer('nro_examen'); // 1, 2 o 3
            $table->string('gestion');
            
            $table->foreign('ci_postulante')->references('ci')->on('postulante')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nota');
    }
};
