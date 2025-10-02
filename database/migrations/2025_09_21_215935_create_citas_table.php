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
        Schema::create('citas', function (Blueprint $table) {
            $table->id("id_cita")
                ->comment("Llave primaria. Identificador único de la cita.");

            $table->date("fecha_programada")
                ->comment("Fecha programada para la cita.");

            $table->text("motivo")
                ->comment("Motivo de la cita, descripción detallada.");

            /* $table->enum("estado", ["pendiente", "confirmada", "en progreso", "completada", "cancelada", "no asistio"])
                ->default("pendiente")
                ->comment("Estado actual de la cita."); */

            $table->unsignedBigInteger("id_estado_cita")
                ->comment("Llave foránea hacia la tabla estado_citas. Indica el estado actual de la cita.");
            $table->foreign("id_estado_cita")
                ->references("id_estado_cita")
                ->on("estado_citas")
                ->onDelete("restrict");

            $table->unsignedBigInteger("id_cliente")
                ->comment("Llave foránea hacia la tabla clientes. Indica el cliente que solicita la cita.");
            $table->foreign("id_cliente")
                ->references("id_cliente")
                ->on("clientes")
                ->onDelete("restrict");

            $table->unsignedBigInteger("id_trabajador_asignado")
                ->comment("Llave foránea hacia la tabla trabajadores. Indica el trabajador asignado a la cita.");
            $table->foreign("id_trabajador_asignado")
                ->references("id_trabajador")
                ->on("trabajadores")
                ->onDelete("restrict");

            $table->unsignedBigInteger("id_mascota")
                ->comment("Llave foránea hacia la tabla mascotas. Indica la mascota asociada a la cita.");
            $table->foreign("id_mascota")
                ->references("id_mascota")
                ->on("mascotas")
                ->onDelete("restrict");

            $table->timestamp("fecha_registro")
                ->useCurrent()
                ->comment("Fecha de creación del registro de la cita.");

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
        Schema::dropIfExists('citas');
    }
};
