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
        Schema::create('turnos', function (Blueprint $table) {
            $table->id("id_turno")->comment("Identificador único del turno");
            $table->string("nombre_turno")->comment("Nombre del turno");
            $table->string("descripcion")->nullable()->comment("Descripción del turno");
            $table->enum("estado", ["activo", "inactivo"])->default("activo")->comment("Estado del turno");
            $table->timestamp("fecha_registro")->useCurrent()->comment("Fecha en que se creó el registro del turno");
            $table->timestamp("fecha_actualizacion")->nullable()->comment("Fecha de la última actualización del registro");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('turnos');
    }
};
