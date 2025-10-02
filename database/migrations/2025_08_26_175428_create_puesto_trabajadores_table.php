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
        Schema::create('puesto_trabajadores', function (Blueprint $table) {
            $table->id("id_puesto_trabajo")
                ->comment("Llave primaria. Identificador único del puesto de trabajo.");

            $table->string("nombre_puesto", 150)
                ->unique()
                ->comment("Nombre del puesto de trabajo. Ejemplo: Analista, Supervisor. Máximo 150 caracteres.");

            $table->string("descripcion", 255)
                ->nullable()
                ->comment("Descripción breve del puesto de trabajo. Máximo 255 caracteres.");

            $table->enum('estado', ['activo', 'inactivo'])
                ->default('activo')
                ->comment("Estado actual del puesto de trabajo. Valores: activo, inactivo.");

            $table->timestamp("fecha_registro")
                ->useCurrent()
                ->comment("Fecha en que se creó el registro.");

            $table->timestamp("fecha_actualizacion")
                ->nullable()
                ->comment("Fecha de la última modificación del registro.");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('puesto_trabajadores');
    }
};
