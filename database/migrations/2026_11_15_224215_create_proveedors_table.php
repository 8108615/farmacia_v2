<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();

            // Identificación del proveedor
            $table->string('codigo', 30)->unique();
            $table->string('razon_social', 200);
            $table->string('nombre_comercial', 200)->nullable();
            $table->string('nit', 30)->nullable()->unique();

            // Información de contacto
            $table->string('contacto', 150)->nullable();
            $table->string('telefono', 30)->nullable();
            $table->string('celular', 30)->nullable();
            $table->string('email', 150)->nullable();

            // Ubicación
            $table->string('direccion', 255)->nullable();
            $table->string('ciudad', 100)->nullable();

            // Información adicional
            $table->text('observaciones')->nullable();

            // Estado del proveedor
            $table->enum('estado', ['Activo', 'Inactivo'])
                ->default('Activo');

            $table->timestamps();
            $table->softDeletes();

            // Índices para búsquedas
            $table->index('razon_social');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proveedores');
    }
};
