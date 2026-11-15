<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lotes', function (Blueprint $table) {
            $table->id();

            // Relación con productos
            $table->foreignId('producto_id')
                ->constrained('productos')
                ->restrictOnDelete();

            // Identificación del lote
            $table->string('numero_lote', 100);

            // Fechas farmacéuticas
            $table->date('fecha_fabricacion')->nullable();
            $table->date('fecha_vencimiento');

            // Existencias
            $table->unsignedInteger('stock_actual')
                ->default(0);

            // Estado administrativo
            $table->enum('estado', [
                'Activo',
                'Inactivo'
            ])->default('Activo');

            // Fechas del sistema
            $table->timestamps();
            $table->softDeletes();

            // Evitar lotes duplicados por producto
            $table->unique([
                'producto_id',
                'numero_lote'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lotes');
    }
};
