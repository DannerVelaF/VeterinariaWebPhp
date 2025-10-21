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
        Schema::create('modulos', function (Blueprint $table) {
            $table->id("id_modulo")->comment("Identificador del modulo");
            $table->string("nombre_modulo")->comment("Nombre del modulo");
            $table->enum("estado", ["activo", "inactivo"])->default("activo")->comment("Estado del modulo");

            $table->unsignedBigInteger("id_usuario_registro")->comment("Identificador del usuario que registro el modulo");
            $table->foreign('id_usuario_registro')->references('id_usuario')->on('usuarios')->onDelete('cascade');

            $table->timestamp('fecha_registro')->nullable()->default(null)->comment("Fecha de creación del registro");
            $table->timestamp('fecha_actualizacion')->nullable()->default(null)->comment("Fecha de actualización del registro");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modulos');
    }
};
