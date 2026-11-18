<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();

            // Código único de la venta: VENT-000001
            $table->string('codigo', 30)->unique();

            // Usuario responsable de registrar la venta
            $table->foreignId('usuario_id')
                ->constrained('users')
                ->restrictOnDelete();

            // Fecha y hora de la venta
            $table->dateTime('fecha_venta');

            // Importes en bolivianos (Bs)
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('descuento', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);

            // Estado del proceso de venta
            $table->enum('estado', [
                'Borrador',
                'Confirmada',
                'Anulada',
            ])->default('Borrador');

            // Información adicional
            $table->text('observacion')->nullable();

            // Preparado para agregar clientes posteriormente
            $table->unsignedBigInteger('cliente_id')->nullable();

            // Fecha y hora de creación y actualización
            $table->timestamps();

            // Índices para consultas y reportes
            $table->index(['estado', 'fecha_venta']);
            $table->index('usuario_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
