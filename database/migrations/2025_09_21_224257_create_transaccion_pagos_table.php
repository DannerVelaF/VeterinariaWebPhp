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
            $table->id("id_transaccion_pago");
            $table->unsignedBigInteger("id_venta");
            $table->foreign("id_venta")->references("id_venta")->on("ventas");
            $table->unsignedBigInteger("id_metodo");
            $table->foreign("id_metodo")->references("id_metodo_pago")->on("metodo_pagos");
            $table->decimal("monto", 12, 2);
            $table->string("referencia");
            $table->enum("estado", ["pendiente", "completada", "rechazada", "reversada"])->default("pendiente");
            $table->text("datos_adicionales")->nullable();
            $table->timestamp("fecha_registro")->useCurrent();
            $table->timestamp("fecha_actualizacion")->nullable();
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
