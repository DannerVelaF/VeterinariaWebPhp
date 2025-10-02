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
        Schema::create('especies', function (Blueprint $table) {
            $table->id("id_especie")
                ->comment("Llave primaria. Identificador único de la especie.");

            $table->string("nombre_especie", 100)
                ->unique()
                ->comment("Nombre de la especie. Máximo 100 caracteres. Debe ser único.");

            $table->text("descripcion")
                ->nullable()
                ->comment("Descripción detallada de la especie. Campo opcional.");

            $table->enum("estado", ["activo", "inactivo"])
                ->default("activo")
                ->comment("Estado de la especie. Valores: activo o inactivo.");

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
        Schema::dropIfExists('especies');
    }
};
