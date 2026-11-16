<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ========================================
        // CREAR TABLA DE SECUENCIAS
        // ========================================

        Schema::create('compra_secuencias', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();

            // Último número utilizado
            $table->unsignedBigInteger('ultimo_numero')
                ->default(0);

            // Fechas del sistema
            $table->timestamps();
        });

        // ========================================
        // INICIALIZAR SECUENCIA
        // ========================================

        DB::table('compra_secuencias')->insert([
            'id' => 1,
            'ultimo_numero' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('compra_secuencias');
    }
};
