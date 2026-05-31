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
        Schema::create('carga_horaria', function (Blueprint $table) {
            $table->uuid('id_asignacion')->primary();
            $table->string('gestion_academica');
            $table->date('fecha_registro');
            $table->unsignedBigInteger('id_docente');
            $table->unsignedBigInteger('id_grupo');
            
            $table->foreign('id_docente')->references('id_docente')->on('docente')->onDelete('cascade');
            $table->foreign('id_grupo')->references('id_grupo')->on('grupo')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carga_horaria');
    }
};
