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
        Schema::create('turno_horarios', function (Blueprint $table) {
            $table->id("id_turno_horario")->comment("Identificador único del turno horario");

            $table->enum("dia_semana", ["lunes", "martes", "miércoles", "jueves", "viernes", "sábado", "domingo"])->comment("Dia y semana del turno");

            $table->time("hora_inicio")->comment("Hora de inicio del turno");

            $table->time("hora_fin")->comment("Hora de fin del turno");

            $table->boolean("es_descanso")->default(false)->comment("Indica si el turno es descanso");

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
        Schema::dropIfExists('turno_horarios');
    }
};
