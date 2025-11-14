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
        Schema::create('ventas', function (Blueprint $table) {
            $table->id("id_venta")
                ->comment("Llave primaria. Identificador único de la venta.");

            $table->date("fecha_venta")
                ->comment("Fecha en que se realizó la venta.");

            $table->decimal("subtotal", 12, 2)
                ->comment("Subtotal de la venta antes de descuentos e impuestos.");

            $table->decimal("descuento", 12, 2)
                ->default(0)
                ->comment("Monto de descuento aplicado a la venta.");

            $table->decimal("impuesto", 12, 2)
                ->default(0)
                ->comment("Monto de impuestos aplicado a la venta.");

            // En la migración de ventas, agregar:
            $table->string('codigo', 20)
                ->unique()
                ->comment("Código interno único de la venta. Máximo 50 caracteres.");

            $table->decimal("total", 12, 2)
                ->comment("Monto total de la venta después de descuentos e impuestos.");

            $table->text("observacion")
                ->nullable()
                ->comment("Observaciones adicionales sobre la venta.");

            /* $table->enum("estado", ["pendiente", "entregado", "cancelado"])
                ->default("pendiente")
                ->comment("Estado de la venta. Valores: pendiente, entregado, cancelado."); */
            $table->unsignedBigInteger("id_estado_venta")
                ->comment("Llave foránea hacia la tabla estado_ventas_fisicas. Indica el estado de la venta.");
            $table->foreign("id_estado_venta")
                ->references("id_estado_venta_fisica")
                ->on("estado_ventas_fisicas")
                ->onDelete("cascade");

            $table->unsignedBigInteger("id_cliente")
                ->comment("Llave foránea hacia la tabla clientes. Indica el cliente asociado a la venta.");
            $table->foreign("id_cliente")
                ->references("id_cliente")
                ->on("clientes")
                ->onDelete("cascade");

            $table->unsignedBigInteger("id_trabajador")
                ->comment("Llave foránea hacia la tabla trabajadores. Indica el trabajador que realizó la venta.");
            $table->foreign("id_trabajador")
                ->references("id_trabajador")
                ->on("trabajadores")
                ->onDelete("cascade");

            $table->timestamp("fecha_registro")
                ->useCurrent()
                ->comment("Fecha de creación del registro de la venta.");

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
        Schema::dropIfExists('ventas');
    }
};
