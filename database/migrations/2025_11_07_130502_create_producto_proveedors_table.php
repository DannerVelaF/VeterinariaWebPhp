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
        Schema::create('producto_proveedors', function (Blueprint $table) {
            $table->id("id_producto_proveedor");
            $table->unsignedBigInteger("id_producto")
                ->comment("Relación con el producto.");
            $table->foreign('id_producto')
                ->references('id_producto')
                ->on('productos')
                ->onDelete('cascade');
            $table->unsignedBigInteger("id_proveedor")
                ->comment("Relación con el proveedor.");
            $table->foreign('id_proveedor')
                ->references('id_proveedor')
                ->on('proveedores');
            $table->timestamp("fecha_registro")
                ->useCurrent()
                ->comment("Fecha de creación del registro. Se asigna automáticamente al insertar.");

            $table->timestamp("fecha_actualizacion")
                ->nullable()
                ->comment("Fecha de última actualización/modificación del registro. Puede ser nula si nunca se actualizó.");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('producto_proveedors');
    }
};
