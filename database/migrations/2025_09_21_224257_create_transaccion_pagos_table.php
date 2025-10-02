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
        Schema::create('transaccion_pagos', function (Blueprint $table) {
            $table->id("id_transaccion_pago")
                ->comment("Llave primaria. Identificador único de la transacción de pago.");

            $table->unsignedBigInteger("id_venta")
                ->comment("Llave foránea hacia la tabla ventas. Indica la venta asociada a la transacción.");
            $table->foreign("id_venta")
                ->references("id_venta")
                ->on("ventas")
                ->onDelete("cascade");

            $table->unsignedBigInteger("id_metodo")
                ->comment("Llave foránea hacia la tabla metodo_pagos. Indica el método de pago utilizado.");
            $table->foreign("id_metodo")
                ->references("id_metodo_pago")
                ->on("metodo_pagos")
                ->onDelete("restrict");

            $table->decimal("monto", 12, 2)
                ->comment("Monto de la transacción.");

            $table->string("referencia", 100)
                ->comment("Referencia de la transacción, por ejemplo número de tarjeta o comprobante.");

            $table->enum("estado", ["pendiente", "completada", "rechazada", "reversada"])
                ->default("pendiente")
                ->comment("Estado actual de la transacción de pago.");

            $table->text("datos_adicionales")
                ->nullable()
                ->comment("Información adicional de la transacción. Campo opcional.");

            $table->timestamp("fecha_registro")
                ->useCurrent()
                ->comment("Fecha de creación del registro de la transacción de pago.");

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
        Schema::dropIfExists('transaccion_pagos');
    }
};
