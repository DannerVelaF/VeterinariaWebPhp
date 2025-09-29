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
            $table->id("id_mascota");
            $table->unsignedBigInteger("id_cliente");
            $table->foreign("id_cliente")->references("id_cliente")->on("clientes");
            $table->unsignedBigInteger("id_raza");
            $table->foreign("id_raza")->references("id_raza")->on("razas");
            $table->string("nombre_mascota");
            $table->date("fecha_nacimiento");
            $table->enum("sexo", ["macho", "hembra"]);
            $table->string("color_primario");
            $table->double("peso_actual", 12, 2);
            $table->text("observacion")->nullable();

            $table->timestamp("fecha_registro")->useCurrent();
            $table->timestamp("fecha_actualizacion")->nullable();
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
