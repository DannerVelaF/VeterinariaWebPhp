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
        Schema::create('mascotas', function (Blueprint $table) {
            $table->id("id_mascota")
                ->comment("Llave primaria. Identificador único de la mascota.");

            $table->unsignedBigInteger("id_cliente")
                ->comment("Llave foránea hacia la tabla clientes. Indica el dueño de la mascota.");
            $table->foreign("id_cliente")
                ->references("id_cliente")
                ->on("clientes")
                ->onDelete("restrict");

            $table->unsignedBigInteger("id_raza")
                ->comment("Llave foránea hacia la tabla razas. Especifica la raza de la mascota.");
            $table->foreign("id_raza")
                ->references("id_raza")
                ->on("razas")
                ->onDelete("restrict");

            $table->string("nombre_mascota", 100)
                ->comment("Nombre de la mascota. Máximo 100 caracteres.");

            $table->date("fecha_nacimiento")
                ->comment("Fecha de nacimiento de la mascota.");

            $table->enum("sexo", ["macho", "hembra"])
                ->comment("Sexo de la mascota: macho o hembra.");

            $table->string("color_primario", 50)
                ->comment("Color primario de la mascota. Máximo 50 caracteres.");

            $table->decimal("peso_actual", 12, 2)
                ->comment("Peso actual de la mascota en kilogramos.");

            $table->text("observacion")
                ->nullable()
                ->comment("Observaciones adicionales sobre la mascota. Campo opcional.");

            $table->timestamp("fecha_registro")
                ->useCurrent()
                ->comment("Fecha de creación del registro de la mascota.");

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
        Schema::dropIfExists('mascotas');
    }
};
