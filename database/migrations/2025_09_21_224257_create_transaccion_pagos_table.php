<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaccion_pagos', function (Blueprint $table) {
            $table->id("id_transaccion_pago")
                ->comment("Llave primaria. Identificador único de la transacción de pago.");

            $table->foreignId("id_venta")
                ->constrained("ventas", "id_venta")
                ->onDelete("cascade")
                ->comment("Venta asociada a esta transacción");

            $table->foreignId("id_metodo_pago")
                ->constrained("metodo_pagos", "id_metodo_pago")
                ->comment("Método de pago utilizado");

            $table->decimal("monto", 10, 2)
                ->comment("Monto total de la transacción");

            $table->string("referencia", 100)
                ->nullable()
                ->comment("Número de referencia, operación, voucher, etc.");

            $table->enum("estado", [
                'pendiente',
                'completado',
                'fallido',
                'refundido'
            ])->default('pendiente')
                ->comment("Estado actual de la transacción");

            $table->timestamp("fecha_pago")
                ->nullable()
                ->comment("Fecha y hora en que se realizó el pago");

            $table->string("comprobante_url", 500)
                ->nullable()
                ->comment("URL del comprobante de pago subido por el cliente");

            $table->json("datos_adicionales")
                ->nullable()
                ->comment("Datos adicionales en formato JSON (IP, user agent, etc.)");

            $table->timestamp("fecha_registro")
                ->useCurrent();

            $table->timestamp("fecha_actualizacion")
                ->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaccion_pagos');
    }
};
