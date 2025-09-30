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
            $table->id('id_rol')->comment('Llave primaria de la tabla roles');
            $table->string('nombre_rol')->unique()->comment('Nombre del rol');
            $table->enum('estado', ['activo', 'inactivo'])->default('activo')->comment("Estado actual del rol");
            $table->timestamp('fecha_registro')->useCurrent()->comment(" Fecha del registro realizado");
            $table->timestamp('fecha_actualizacion')->nullable()->comment("Fecha de la actulización / 
modificacion del registro");
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
