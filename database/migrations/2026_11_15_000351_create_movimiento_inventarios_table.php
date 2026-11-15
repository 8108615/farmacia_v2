<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id();

            // Relación con el lote
            $table->foreignId('lote_id')
                ->constrained('lotes')
                ->restrictOnDelete();

            // Usuario responsable
            $table->foreignId('usuario_id')
                ->constrained('users')
                ->restrictOnDelete();

            // Tipo de movimiento
            $table->enum('tipo', [
                'Entrada',
                'Salida',
                'Ajuste Entrada',
                'Ajuste Salida',
            ]);

            // Cantidad del movimiento
            $table->unsignedInteger('cantidad');

            // Control de existencias
            $table->unsignedInteger('stock_anterior');
            $table->unsignedInteger('stock_posterior');

            // Información adicional
            $table->string('referencia', 150)->nullable();
            $table->text('observacion')->nullable();

            // Fecha y hora del registro
            $table->timestamps();

            // Índices para consultas
            $table->index(['lote_id', 'created_at']);
            $table->index(['tipo', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventario');
    }
};
