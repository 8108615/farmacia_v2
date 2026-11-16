<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compras', function (Blueprint $table) {
            $table->id();

            // ========================================
            // IDENTIFICACIÓN
            // ========================================

            $table->string('codigo', 20)->unique();

            // ========================================
            // RELACIONES
            // ========================================

            $table->foreignId('proveedor_id')
                ->constrained('proveedores')
                ->restrictOnDelete();

            $table->foreignId('usuario_id')
                ->constrained('users')
                ->restrictOnDelete();

            // ========================================
            // INFORMACIÓN DE LA COMPRA
            // ========================================

            $table->date('fecha_compra');

            $table->string('numero_factura', 100)
                ->nullable();

            // ========================================
            // IMPORTES EN BOLIVIANOS
            // ========================================

            $table->decimal('subtotal', 15, 2)
                ->default(0);

            $table->decimal('descuento', 15, 2)
                ->default(0);

            $table->decimal('total', 15, 2)
                ->default(0);

            // ========================================
            // ESTADO DE LA COMPRA
            // ========================================

            $table->enum('estado', [
                'Borrador',
                'Confirmada',
                'Anulada'
            ])->default('Borrador');

            $table->dateTime('fecha_confirmacion')
                ->nullable();

            // ========================================
            // INFORMACIÓN ADICIONAL
            // ========================================

            $table->text('observaciones')
                ->nullable();

            // ========================================
            // FECHAS DEL SISTEMA
            // ========================================

            $table->timestamps();

            // ========================================
            // ÍNDICES
            // ========================================

            $table->index(['estado', 'fecha_compra']);
            $table->index(['proveedor_id', 'fecha_compra']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compras');
    }
};
