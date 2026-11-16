<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compra_detalles', function (Blueprint $table) {
            $table->id();

            // ========================================
            // RELACIONES
            // ========================================

            $table->foreignId('compra_id')
                ->constrained('compras')
                ->cascadeOnDelete();

            $table->foreignId('producto_id')
                ->constrained('productos')
                ->restrictOnDelete();

            // ========================================
            // INFORMACIÓN DEL LOTE
            // ========================================

            $table->string('numero_lote', 100);

            $table->date('fecha_fabricacion')
                ->nullable();

            $table->date('fecha_vencimiento');

            // ========================================
            // CANTIDADES Y COSTOS
            // ========================================

            $table->unsignedInteger('cantidad');

            $table->decimal('precio_compra', 15, 2);

            $table->decimal('subtotal', 15, 2);

            // ========================================
            // FECHAS DEL SISTEMA
            // ========================================

            $table->timestamps();

            // ========================================
            // ÍNDICES
            // ========================================

            $table->index(['compra_id', 'producto_id']);
            $table->index(['producto_id', 'numero_lote']);
            $table->index('fecha_vencimiento');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compra_detalles');
    }
};
