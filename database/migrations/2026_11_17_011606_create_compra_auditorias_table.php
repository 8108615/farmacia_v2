<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compra_auditorias', function (Blueprint $table) {
            $table->id();

            // ========================================
            // RELACIONES
            // ========================================

            $table->foreignId('compra_id')
                ->constrained('compras')
                ->restrictOnDelete();

            $table->foreignId('usuario_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // ========================================
            // INFORMACIÓN DE AUDITORÍA
            // ========================================

            $table->string('accion', 30);

            $table->json('datos_anteriores')
                ->nullable();

            $table->json('datos_nuevos')
                ->nullable();

            $table->text('descripcion')
                ->nullable();

            // ========================================
            // FECHAS DEL SISTEMA
            // ========================================

            $table->timestamps();

            // ========================================
            // ÍNDICES
            // ========================================

            $table->index(['compra_id', 'created_at']);
            $table->index(['accion', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compra_auditorias');
    }
};
