<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('metodo_pagos', function (Blueprint $table) {
            $table->id("id_metodo_pago")
                ->comment("Llave primaria. Identificador único del método de pago.");

            $table->string("nombre_metodo", 100)
                ->comment("Nombre del método de pago: 'Yape', 'Plin', 'Transferencia', 'Contra entrega'");

            $table->string("tipo_metodo", 50)
                ->comment("Tipo: 'digital', 'transferencia', 'efectivo'");

            $table->string("numero_cuenta", 50)
                ->nullable()
                ->comment("Número de cuenta, teléfono (Yape/Plin), o información de pago");

            $table->string("nombre_titular", 100)
                ->nullable()
                ->comment("Nombre del titular de la cuenta/tarjeta");

            $table->string("entidad_financiera", 100)
                ->nullable()
                ->comment("Banco o entidad financiera: 'BCP', 'Interbank', 'BBVA', etc.");

            $table->string("tipo_cuenta", 50)
                ->nullable()
                ->comment("Tipo de cuenta: 'ahorros', 'corriente', 'digital'");

            $table->string("codigo_qr", 255)
                ->nullable()
                ->comment("URL o path de la imagen QR para pagos digitales");

            $table->text("instrucciones")
                ->nullable()
                ->comment("Instrucciones específicas para el cliente");

            $table->boolean("estado")
                ->enum(["activo", "inactivo"])
                ->default("activo")
                ->comment("Si el método de pago está activo");

            $table->integer("orden")
                ->default(0)
                ->comment("Orden de visualización");

            $table->text("observacion")
                ->nullable()
                ->comment("Observaciones internas");

            $table->timestamp("fecha_registro")
                ->useCurrent();

            $table->timestamp("fecha_actualizacion")
                ->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('metodo_pagos');
    }
};
