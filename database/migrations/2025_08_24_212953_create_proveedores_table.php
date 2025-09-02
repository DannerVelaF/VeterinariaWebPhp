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
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('ruc')->unique();
            $table->string('correo_electronico_empresa')->nullable();
            $table->string("telefono_contacto")->nullable();
            $table->string('pais')->nullable();
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->string("telefono_secundario")->nullable();
            $table->string("correo_electronico_encargado")->nullable();

            $table->unsignedBigInteger("id_direccion")->nullable();
            $table->foreign('id_direccion')->references('id')->on('direcciones');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proveedores');
    }
};
