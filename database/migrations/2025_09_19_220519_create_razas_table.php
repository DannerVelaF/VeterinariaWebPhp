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
        Schema::create('razas', function (Blueprint $table) {
            $table->id("id_raza")
                ->comment("Llave primaria. Identificador único de la raza.");

            $table->unsignedBigInteger("id_especie")
                ->comment("Llave foránea hacia la tabla especies. Indica a qué especie pertenece la raza.");
            $table->foreign("id_especie")
                ->references("id_especie")
                ->on("especies")
                ->onDelete("restrict");

            $table->string("nombre_raza", 100)
                ->unique()
                ->comment("Nombre de la raza. Máximo 100 caracteres. Debe ser único dentro del sistema.");

            $table->text("descripcion")
                ->nullable()
                ->comment("Descripción detallada de la raza. Campo opcional.");

            $table->enum("estado", ["activo", "inactivo"])
                ->default("activo")
                ->comment("Estado de la raza. Valores: activo o inactivo.");

            $table->timestamp("fecha_registro")
                ->useCurrent()
                ->comment("Fecha de creación del registro de la raza.");

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
        Schema::dropIfExists('razas');
    }
};
