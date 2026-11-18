<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();

            // Relación con categorías
            $table->foreignId('categoria_id')
                ->constrained('categorias')
                ->restrictOnDelete();

            // Identificación
            $table->string('codigo', 20)->unique();
            $table->string('codigo_barras', 100)
                ->nullable()
                ->unique();

            // Información general
            $table->string('nombre', 200);
            $table->string('nombre_generico', 200)->nullable();
            $table->string('marca', 150)->nullable();
            $table->string('laboratorio', 150)->nullable();

            // Información farmacéutica
            $table->string('presentacion', 100)->nullable();
            $table->string('concentracion', 100)->nullable();
            $table->decimal('precio_venta', 12, 2)->nullable();
            $table->string('registro_sanitario', 100)->nullable();
            $table->boolean('requiere_receta')->default(false);

            // Información adicional
            $table->text('descripcion')->nullable();
            $table->string('imagen')->nullable();

            // Estado
            $table->enum('estado', ['Activo', 'Inactivo'])
                ->default('Activo');

            // Fechas
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
