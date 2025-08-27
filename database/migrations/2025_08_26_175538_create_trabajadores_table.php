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
        Schema::create('trabajadores', function (Blueprint $table) {
            $table->id();
            $table->date("fecha_ingreso");
            $table->date("fecha_salida")->nullable();
            $table->decimal("salario", 12, 2);
            $table->string('numero_seguro_social');


            $table->unsignedBigInteger("id_persona");
            $table->foreign("id_persona")->references("id")->on('personas');

            $table->unsignedBigInteger("id_puesto_trabajo");
            $table->foreign("id_puesto_trabajo")->references("id")->on('puesto_trabajadores');

            $table->unsignedBigInteger('id_estado_trabajador');
            $table->foreign("id_estado_trabajador")->references("id")->on("estado_trabajadores");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trabajadores');
    }
};
