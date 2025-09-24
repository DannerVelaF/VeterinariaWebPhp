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
            $table->id();
            $table->unsignedBigInteger("id_venta");
            $table->foreign("id_venta")->references("id")->on("ventas");
            $table->unsignedBigInteger("id_metodo");
            $table->foreign("id_metodo")->references("id")->on("metodo_pagos");
            $table->decimal("monto", 12, 2);
            $table->string("referencia");
            $table->enum("estado", ["pendietne", "completada", "rechazada", "reversada"])->default("pendietne");
            $table->text("datos_adicionales")->nullable();
            $table->timestamp("fecha_registro");
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
