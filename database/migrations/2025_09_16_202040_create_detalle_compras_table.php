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
        Schema::create('detalle_compras', function (Blueprint $table) {
            $table->id("id_detalle_compra")
                ->comment("Llave primaria. Identificador único del detalle de compra.");

            // Relación con compra
            $table->unsignedBigInteger("id_compra")
                ->comment("Llave foránea hacia la tabla compras. Indica a qué compra pertenece este detalle.");
            $table->foreign("id_compra")
                ->references("id_compra")
                ->on("compras")
                ->onDelete("cascade");

            // Relación con producto
            $table->unsignedBigInteger('id_producto')
                ->comment("Llave foránea hacia la tabla productos. Producto comprado en este detalle.");
            $table->foreign("id_producto")
                ->references("id_producto")
                ->on("productos")
                ->onDelete("restrict");

            /* $table->enum("estado", ["pendiente", "recibido", "pagado", "cancelado"])
                ->default("pendiente")
                ->comment("Estado del detalle de compra. Valores: pendiente, recibido, pagado, cancelado.");
*/
            $table->unsignedBigInteger("id_estado_detalle_compra")
                ->comment("Llave foránea hacia la tabla estado_compras. Indica el estado del detalle de compra.");
            $table->foreign("id_estado_detalle_compra")
                ->references("id_estado_detalle_compra")
                ->on("estado_detalle_compras")
                ->onDelete("restrict");

            $table->decimal("cantidad", 12, 2)
                ->comment("Cantidad del producto comprada en este detalle.");

            $table->decimal("precio_unitario", 12, 2)
                ->comment("Precio unitario del producto en esta compra.");

            $table->decimal("sub_total", 12, 2)
                ->comment("Subtotal del detalle de compra: cantidad * precio_unitario.");

            $table->timestamp("fecha_registro")
                ->useCurrent()
                ->comment("Fecha de creación del registro del detalle de compra.");

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
        Schema::dropIfExists('detalle_compras');
    }
};
