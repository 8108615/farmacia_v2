
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venta_auditorias', function (Blueprint $table) {
            $table->id();

            // Venta relacionada con la auditoría
            $table->foreignId('venta_id')
                ->constrained('ventas')
                ->restrictOnDelete();

            // Usuario que realizó la operación
            $table->foreignId('usuario_id')
                ->constrained('users')
                ->restrictOnDelete();

            // Acción realizada sobre la venta
            $table->enum('accion', [
                'CREACION',
                'EDICION',
                'CONFIRMACION',
                'ANULACION',
            ]);

            // Información anterior a la modificación
            $table->json('datos_anteriores')->nullable();

            // Información posterior a la modificación
            $table->json('datos_nuevos')->nullable();

            // Descripción de la operación realizada
            $table->text('descripcion')->nullable();

            // Fecha y hora de la auditoría
            $table->timestamps();

            // Índices para consultar el historial
            $table->index(['venta_id', 'created_at']);
            $table->index(['usuario_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venta_auditorias');
    }
};
