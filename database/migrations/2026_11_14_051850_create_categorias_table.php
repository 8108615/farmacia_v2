<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar las migraciones.
     */
    public function up(): void
    {
        Schema::create('categorias', function (Blueprint $table) {

            $table->id();

            // Información de la categoría
            $table->string('nombre', 150)->unique();

            $table->text('descripcion')->nullable();

            // Estado de la categoría
            $table->enum('estado', [
                'Activo',
                'Inactivo'
            ])->default('Activo');

            // Fechas de registro y actualización
            $table->timestamps();

            // Eliminación lógica
            $table->softDeletes();

        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('categorias');
    }
};
