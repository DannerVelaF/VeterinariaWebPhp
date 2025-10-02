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
        Schema::create('categoria_servicios', function (Blueprint $table) {
            $table->id("id_categoria_servicio")
                ->comment("Llave primaria. Identificador único de la categoría de servicio.");

            $table->string("nombre_categoria_servicio", 100)
                ->unique()
                ->comment("Nombre de la categoría de servicio. Máximo 100 caracteres. Debe ser único.");

            $table->text("descripcion")
                ->nullable()
                ->comment("Descripción detallada de la categoría de servicio. Campo opcional.");

            $table->enum("estado", ["activo", "inactivo"])
                ->default("activo")
                ->comment("Estado de la categoría de servicio. Valores: activo o inactivo.");

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
        Schema::dropIfExists('categoria_servicios');
    }
};
