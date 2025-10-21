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
        Schema::create('modulo_roles', function (Blueprint $table) {
            $table->id("id_modulo_roles")->comment("Identificador de la relación entre el modulo y el rol");
            $table->unsignedBigInteger("id_modulo")->comment("Identificador del modulo al que pertenece la opción");
            $table->foreign('id_modulo')->references('id_modulo')->on('modulos')->onDelete('cascade');

            $table->unsignedBigInteger("id_rol")->comment("Identificador del rol al que pertenece la opción");
            $table->foreign('id_rol')->references('id_rol')->on('roles')->onDelete('cascade');
            $table->timestamp("fecha_registro")->nullable()->default(null)->comment("Fecha de creación del registro");
            $table->timestamp("fecha_actualizacion")->nullable()->default(null)->comment("Fecha de actualización del registro");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modulo_roles');
    }
};
