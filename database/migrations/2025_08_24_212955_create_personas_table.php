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
        Schema::create('personas', function (Blueprint $table) {
            $table->id();
            $table->string("numero_documento");
            $table->string("nombre");
            $table->string("apellido_paterno");
            $table->string("apellido_materno");
            $table->date("fecha_nacimiento");
            $table->enum("sexo", ["M", "F", "Otro"]);
            $table->string("nacionalidad");
            $table->string("correo_electronico_personal")->unique();
            $table->string("correo_electronico_secundario")->nullable();
            $table->string("numero_telefono_personal");
            $table->string("numero_telefono_secundario")->nullable();


            $table->unsignedBigInteger('id_direccion');
            $table->foreign('id_direccion')->references('id')->on('direcciones');

            $table->unsignedBigInteger("id_tipo_documento")->nullable();
            $table->foreign("id_tipo_documento")->references("id")->on("tipo_documentos")->onDelete("set null");

            $table->timestamp("fecha_registro")->useCurrent();
            $table->timestamp("fecha_actualizacion")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personas');
    }
};
