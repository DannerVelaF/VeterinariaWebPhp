<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categoria_productos', function (Blueprint $table) {
            $table->id("id_categoria_producto")
                ->comment("Llave primaria de la tabla categoria_productos. Identificador único de la categoría.");

            $table->string("nombre_categoria_producto", 100)
                ->unique()
                ->comment("Nombre de la categoría de productos. Ejemplo: 'Electrónica', 'Textiles'. Máx. 100 caracteres. Debe ser único.");

            $table->text("descripcion")
                ->nullable()
                ->comment("Descripción detallada de la categoría de productos. Campo opcional, sin límite fijo de caracteres.");

            $table->enum('estado', ['activo', 'inactivo'])
                ->default('activo')
                ->comment("Estado actual de la categoría. Valores: 'activo', 'inactivo'. Por defecto 'activo'.");

            $table->timestamp("fecha_registro")
                ->useCurrent()
                ->comment("Fecha de creación del registro. Se asigna automáticamente al insertar.");

            $table->timestamp("fecha_actualizacion")
                ->nullable()
                ->comment("Fecha de la última actualización/modificación del registro. Puede ser nula si nunca se actualizó.");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categoria_productos');
    }
};
