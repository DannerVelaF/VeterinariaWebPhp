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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id("id_cliente")
                ->comment("Llave primaria. Identificador único del cliente.");

            $table->unsignedBigInteger("id_persona")
                ->comment("Llave foránea hacia la tabla personas. Indica los datos personales asociados al cliente.");
            $table->foreign("id_persona")
                ->references("id_persona")
                ->on("personas")
                ->onDelete("restrict");

            $table->timestamp("fecha_registro")
                ->useCurrent()
                ->comment("Fecha en que se creó el registro del cliente.");

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
        Schema::dropIfExists('clientes');
    }
};
