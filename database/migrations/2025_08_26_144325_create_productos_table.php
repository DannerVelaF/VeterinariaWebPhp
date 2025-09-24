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
            $table->id();
            $table->string("nombre_producto");
            $table->text("descripcion");
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->string("codigo_barras");

            $table->unsignedBigInteger("id_categoria_producto");
            $table->foreign('id_categoria_producto')->references('id')->on('categoria_productos');
            $table->string("ruta_imagen")->nullable();
            $table->unsignedBigInteger("id_proveedor");
            $table->foreign('id_proveedor')->references('id')->on('proveedores');

            $table->unsignedBigInteger("id_unidad");
            $table->foreign('id_unidad')->references('id')->on('unidades');

            $table->timestamp("fecha_registro");
            $table->timestamp("fecha_actualizacion")->nullable();
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
