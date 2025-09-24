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
        Schema::create('direcciones', function (Blueprint $table) {
            $table->id();
            $table->string('zona')->nullable();
            $table->string('tipo_calle')->nullable();
            $table->string('nombre_calle');
            $table->string('numero')->nullable();
            $table->string('codigo_postal')->nullable();
            $table->string('referencia')->nullable();
            $table->string('codigo_ubigeo');
            $table->foreign('codigo_ubigeo')->references('codigo_ubigeo')->on('ubigeos');

            $table->timestamp("fecha_registro");
            $table->timestamp("fecha_actualizacion")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('direcciones');
    }
};
