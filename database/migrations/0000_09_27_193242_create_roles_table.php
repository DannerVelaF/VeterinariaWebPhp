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
        Schema::create('roles', function (Blueprint $table) {
            $table->id('id_rol')
                ->comment('Llave primaria de la tabla roles. Identificador único del rol.');

            $table->string('nombre_rol', 100)
                ->unique()
                ->comment('Nombre del rol (ejemplo: Administrador, Supervisor, Usuario). Longitud máxima: 100 caracteres.');

            $table->enum('estado', ['activo', 'inactivo'])
                ->default('activo')
                ->comment("Estado actual del rol. Valores permitidos: 'activo', 'inactivo'.");

            $table->timestamp('fecha_registro')
                ->useCurrent()
                ->comment("Fecha de creación del registro. Se genera automáticamente al insertar.");

            $table->timestamp('fecha_actualizacion')
                ->nullable()
                ->comment("Fecha de la última actualización/modificación del registro. Puede ser nula si nunca se actualizó.");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
