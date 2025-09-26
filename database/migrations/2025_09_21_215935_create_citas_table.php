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
            $table->id();
            $table->date("fecha_programada");
            $table->text("motivo");
            $table->enum("estado", ["pendiente", "confirmada", "en progreso", "completada", "cancelada", "no asistio"])->default("pendiente");

            $table->unsignedBigInteger("id_cliente");
            $table->foreign("id_cliente")->references("id")->on("clientes");

            $table->unsignedBigInteger("id_trabajador_asignado");
            $table->foreign("id_trabajador_asignado")->references("id")->on("trabajadores");

            $table->unsignedBigInteger("id_mascota");
            $table->foreign("id_mascota")->references("id")->on("mascotas");

            $table->timestamp("fecha_registro")->useCurrent();
            $table->timestamp("fecha_actualizacion")->nullable();
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
