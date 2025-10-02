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
        Schema::create('servicios', function (Blueprint $table) {
            $table->id("id_servicio")
                ->comment("Llave primaria. Identificador único del servicio.");

            $table->string("nombre_servicio", 100)
                ->unique()
                ->comment("Nombre del servicio. Máximo 100 caracteres. Debe ser único.");

            $table->text("descripcion")
                ->nullable()
                ->comment("Descripción detallada del servicio. Campo opcional.");

            $table->integer("duracion_estimada")
                ->comment("Duración estimada del servicio en minutos.");

            $table->decimal("precio_unitario", 12, 2)
                ->comment("Precio unitario del servicio.");

            $table->enum("estado", ["activo", "inactivo"])
                ->default("activo")
                ->comment("Estado del servicio. Valores: activo o inactivo.");

            $table->unsignedBigInteger("id_categoria_servicio")
                ->comment("Llave foránea hacia la tabla categoria_servicios.");
            $table->foreign("id_categoria_servicio")
                ->references("id_categoria_servicio")
                ->on("categoria_servicios")
                ->onDelete("restrict");

            $table->timestamp("fecha_registro")
                ->useCurrent()
                ->comment("Fecha de creación del registro del servicio.");

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
        Schema::dropIfExists('servicios');
    }
};
