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
        Schema::create('cita_servicios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("id_cita");
            $table->foreign("id_cita")->references("id")->on("citas");

            $table->unsignedBigInteger("id_servicio");
            $table->foreign("id_servicio")->references("id")->on("servicios");

            $table->double("precio_aplicado", 12, 2);
            $table->integer("cantidad");
            $table->text("diagnostico");
            $table->text("medicamentos");
            $table->text("recomendaciones");

            $table->timestamp("fecha_registro");
            $table->timestamp("fecha_actualizacion")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cita_servicios');
    }
};
