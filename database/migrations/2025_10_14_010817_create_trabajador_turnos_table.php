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
        Schema::create('trabajador_turnos', function (Blueprint $table) {
            $table->id("id_trabajador_turno")->comment("Identificador único del turno");

            $table->date('fecha_inicio')->comment('Fecha desde la que aplica este turno');
            $table->date('fecha_fin')->nullable()->comment('Fecha hasta la que aplica este turno');

            $table->unsignedBigInteger("id_trabajador")->constrained()->comment("Identificador único del trabajador");

            $table->foreign("id_trabajador")->references("id_trabajador")->on("trabajadores")->onDelete("cascade");

            $table->unsignedBigInteger("id_turno")->constrained()->comment("Identificador único del turno");

            $table->foreign("id_turno")->references("id_turno")->on("turnos")->onDelete("cascade");

            $table->timestamp("fecha_registro")->useCurrent()->comment("Fecha en que se creó el registro del turno");
            $table->timestamp("fecha_actualizacion")->nullable()->comment("Fecha de la última actualización del registro");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trabajador_turnos');
    }
};
