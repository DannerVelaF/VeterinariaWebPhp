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
        Schema::create('permisos', function (Blueprint $table) {
            $table->id("id_permiso")
                ->comment("Llave primaria. Identificador único del permiso.");

            $table->string('nombre_permiso', 100)
                ->unique()
                ->comment("Nombre del permiso, por ejemplo: 'crear_usuario', 'editar_producto'.");

            $table->enum('estado', ['activo', 'inactivo'])
                ->default('activo')
                ->comment("Estado del permiso: activo o inactivo.");

            $table->timestamp("fecha_registro")
                ->useCurrent()
                ->comment("Fecha de creación del permiso.");

            $table->timestamp("fecha_actualizacion")
                ->nullable()
                ->comment("Fecha de la última actualización del permiso.");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permisos');
    }
};
