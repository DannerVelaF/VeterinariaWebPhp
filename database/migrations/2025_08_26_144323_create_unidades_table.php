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
        Schema::create('unidades', function (Blueprint $table) {
            $table->id("id_unidad")
                ->comment("Llave primaria de la tabla unidades. Identificador único de la unidad de medida.");

            $table->string("nombre_unidad", 50)
                ->unique()
                ->comment("Nombre o abreviatura de la unidad de medida. Ejemplos: 'Kilogramo', 'Litro', 'Unidad', 'm'. Máx. 50 caracteres. Debe ser único.");

            $table->timestamp("fecha_registro")
                ->useCurrent()
                ->comment("Fecha de creación del registro. Se asigna automáticamente al insertar.");

            $table->timestamp("fecha_actualizacion")
                ->nullable()
                ->comment("Fecha de la última actualización/modificación del registro. Puede ser nula si nunca se actualizó.");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unidades');
    }
};
