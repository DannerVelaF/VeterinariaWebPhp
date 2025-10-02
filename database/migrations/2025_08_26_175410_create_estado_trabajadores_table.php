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
        Schema::create('estado_trabajadores', function (Blueprint $table) {
            $table->id("id_estado_trabajador")
                ->comment("Llave primaria. Identificador único de cada estado de trabajador.");

            $table->string("nombre_estado_trabajador", 100)
                ->unique()
                ->comment("Nombre del estado del trabajador.");

            $table->timestamp("fecha_registro")
                ->useCurrent()
                ->comment("Fecha en que se creó el registro.");

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
        Schema::dropIfExists('estado_trabajadores');
    }
};
