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
        Schema::create('detalle_ventas', function (Blueprint $table) {
            $table->id("id_detalle_venta")
                ->comment("Llave primaria. Identificador único del detalle de venta.");

            $table->integer("cantidad")
                ->comment("Cantidad de unidades vendidas del producto o servicio.");

            $table->decimal("precio_unitario", 12, 2)
                ->comment("Precio unitario del producto o servicio.");

            /* $table->unsignedBigInteger("id_estado_detalle_venta_fisica")
                ->comment("Llave foránea hacia la tabla estado_detalle_ventas_fisicas. Indica el estado del detalle de venta.");
            $table->foreign("id_estado_detalle_venta_fisica")
                ->references("id_estado_detalle_venta_fisica")
                ->on("estado_detalle_ventas_fisicas")
                ->onDelete("cascade"); */

            $table->string("estado", 20)
                ->comment("Estado del detalle de venta (activo, cancelado, devuelto, etc.)."); 

            $table->decimal("subtotal", 12, 2)
                ->comment("Subtotal de este ítem (cantidad * precio_unitario).");

            $table->enum("tipo_item", ["producto", "servicio"])
                ->comment("Tipo de ítem vendido: producto o servicio.");

            $table->string('motivo_salida', 255)
                ->nullable()
                ->comment("Motivo de salida del producto. Campo opcional, aplicable para productos.");

            $table->unsignedBigInteger("id_venta")
                ->comment("Llave foránea hacia la tabla ventas. Indica a qué venta pertenece el detalle.");
            $table->foreign("id_venta")
                ->references("id_venta")
                ->on("ventas")
                ->onDelete("cascade");

            $table->unsignedBigInteger("id_producto")
                ->nullable()
                ->comment("Llave foránea hacia la tabla productos. Se usa si el ítem es un producto.");
            $table->foreign("id_producto")
                ->references("id_producto")
                ->on("productos")
                ->onDelete("cascade");

            $table->unsignedBigInteger("id_servicio")
                ->nullable()
                ->comment("Llave foránea hacia la tabla servicios. Se usa si el ítem es un servicio.");
            $table->foreign("id_servicio")
                ->references("id_servicio")
                ->on("servicios")
                ->onDelete("cascade");

            $table->timestamp("fecha_registro")
                ->useCurrent()
                ->comment("Fecha de creación del detalle de venta.");

            $table->timestamp("fecha_actualizacion")
                ->nullable()
                ->comment("Fecha de la última actualización del registro.");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_ventas');
    }
};
