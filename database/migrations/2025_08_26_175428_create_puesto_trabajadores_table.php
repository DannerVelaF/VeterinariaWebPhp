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
        Schema::create('puesto_trabajadores', function (Blueprint $table) {
            $table->id("id_puesto_trabajo");
            $table->string("nombre_puesto");
            $table->string("descripcion")->nullable();
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->timestamp("fecha_registro")->useCurrent();
            $table->timestamp("fecha_actualizacion")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('puesto_trabajadores');
    }
};
