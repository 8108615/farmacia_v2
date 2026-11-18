
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venta_detalles', function (Blueprint $table) {
            $table->id();

            // Venta a la que pertenece el detalle
            $table->foreignId('venta_id')
                ->constrained('ventas')
                ->cascadeOnDelete();

            // Producto vendido
            $table->foreignId('producto_id')
                ->constrained('productos')
                ->restrictOnDelete();

            // Lote del medicamento vendido
            // Puede quedar pendiente mientras la venta está en Borrador
            $table->foreignId('lote_id')
                ->nullable()
                ->constrained('lotes')
                ->restrictOnDelete();

            // Cantidad de unidades vendidas
            $table->unsignedInteger('cantidad');

            // Precio unitario de venta en bolivianos
            $table->decimal('precio_unitario', 12, 2);

            // Descuento aplicado a esta línea
            $table->decimal('descuento', 12, 2)->default(0);

            // Subtotal final de la línea
            $table->decimal('subtotal', 12, 2);

            // Fechas de creación y actualización
            $table->timestamps();

            // Índices para consultas
            $table->index(['venta_id', 'producto_id']);
            $table->index('lote_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venta_detalles');
    }
};
