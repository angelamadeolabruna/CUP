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
        Schema::table('rol', function (Blueprint $table) {
            $table->json('lista_permisos')->nullable();
            $table->integer('jerarquia_nivel')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rol', function (Blueprint $table) {
            $table->dropColumn(['lista_permisos', 'jerarquia_nivel']);
        });
    }
};
