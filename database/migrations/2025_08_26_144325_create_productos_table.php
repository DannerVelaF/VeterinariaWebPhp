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
        Schema::create('productos', function (Blueprint $table) {
            $table->id("id_producto")
                ->comment("Llave primaria de la tabla productos. Identificador único de cada producto.");

            $table->string("nombre_producto", 150)
                ->comment("Nombre del producto. Máximo 150 caracteres.");

            $table->text("descripcion")
                ->nullable()
                ->comment("Descripción detallada del producto. Puede ser nula si no se requiere.");

            $table->enum('estado', ['activo', 'inactivo'])
                ->default('activo')
                ->comment("Estado del producto: activo o inactivo.");

            $table->string("codigo_barras", 50)
                ->unique()
                ->comment("Código de barras único del producto. Máximo 50 caracteres.");

            $table->unsignedBigInteger("id_categoria_producto")
                ->comment("Relación con la categoría del producto.");
            $table->foreign('id_categoria_producto')
                ->references('id_categoria_producto')
                ->on('categoria_productos')
                ->onDelete('restrict');

            $table->string("ruta_imagen", 255)
                ->nullable()
                ->comment("Ruta o URL de la imagen del producto. Puede ser nula.");

            $table->unsignedBigInteger("id_proveedor")
                ->comment("Relación con el proveedor del producto.");
            $table->foreign('id_proveedor')
                ->references('id_proveedor')
                ->on('proveedores')
                ->onDelete('restrict');

            $table->unsignedBigInteger("id_unidad")
                ->comment("Relación con la unidad de medida del producto.");
            $table->foreign('id_unidad')
                ->references('id_unidad')
                ->on('unidades')
                ->onDelete('restrict');

            $table->timestamp("fecha_registro")
                ->useCurrent()
                ->comment("Fecha de creación del registro.");

            $table->timestamp("fecha_actualizacion")
                ->nullable()
                ->comment("Fecha de la última modificación del registro.");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
