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
        Schema::create('modulo_opciones', function (Blueprint $table) {
            $table->id("id_modulo_opcion")->comment("Identificador de la opción del modulo");
            $table->string("nombre_opcion")->comment("Nombre de la opción");
            $table->enum('estado', ['activo', 'inactivo'])->default('activo')->comment("Estado de la opción");

            $table->unsignedBigInteger("id_modulo")->comment("Identificador del modulo al que pertenece la opción");
            $table->foreign('id_modulo')->references('id_modulo')->on('modulos')->onDelete('cascade');

            $table->string("ruta_laravel", 70)->comment("Ruta para el acceso a la opcion que se declara en route.php");
            $table->integer("orden")->comment("Orden de la opción");

            $table->unsignedBigInteger("id_usuario_registro")->comment("Identificador del usuario que registro la opción");
            $table->foreign('id_usuario_registro')->references('id_usuario')->on('usuarios')->onDelete('cascade');

            $table->unsignedBigInteger("id_permiso")->comment("Identificador del permiso que se necesita para acceder a la opción");
            $table->foreign('id_permiso')->references('id_permiso')->on('permisos')->onDelete('cascade');

            $table->unsignedBigInteger('id_opcion_padre')->nullable()->comment('Opción padre si existe');
            $table->foreign('id_opcion_padre')
                ->references('id_modulo_opcion')
                ->on('modulo_opciones')
                ->onDelete('cascade');

            $table->timestamp("fecha_registro")->nullable()->default(null)->comment("Fecha del registro realizado");
            $table->timestamp("fecha_actualizacion")->nullable()->default(null)->comment("Fecha de actualización del registro");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modulo_opciones');
    }
};
