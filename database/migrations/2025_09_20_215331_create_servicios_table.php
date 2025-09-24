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
        Schema::create('servicios', function (Blueprint $table) {
            $table->id();
            $table->string("nombre_servicio");
            $table->text("descripcion");
            $table->integer("duracion_estimada");
            $table->double("precio_unitario", 12, 2);
            $table->enum("estado", ["activo", "inactivo"])->default("activo");

            $table->unsignedBigInteger("id_categoria_servicio");
            $table->foreign("id_categoria_servicio")->references("id")->on("categoria_servicios");

            $table->timestamp("fecha_registro");
            $table->timestamp("fecha_actualizacion")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servicios');
    }
};
