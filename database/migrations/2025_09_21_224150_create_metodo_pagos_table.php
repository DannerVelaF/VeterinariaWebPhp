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
        Schema::create('metodo_pagos', function (Blueprint $table) {
            $table->id("id_metodo_pago")
                ->comment("Llave primaria. Identificador único del método de pago.");

            $table->string("nombre_metodo", 100)
                ->comment("Nombre del método de pago, por ejemplo: 'Efectivo', 'Tarjeta', 'Transferencia'.");

            $table->text("observacion")
                ->nullable()
                ->comment("Observaciones adicionales sobre el método de pago.");

            $table->timestamp("fecha_registro")
                ->useCurrent()
                ->comment("Fecha de creación del registro del método de pago.");

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
        Schema::dropIfExists('metodo_pagos');
    }
};
