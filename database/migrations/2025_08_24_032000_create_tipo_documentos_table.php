<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tipo_documentos', function (Blueprint $table) {
            $table->id("id_tipo_documento")
                ->comment("Llave primaria de la tabla tipo_documentos. Identificador único del tipo de documento.");

            $table->string("nombre_tipo_documento", 100)
                ->unique()
                ->comment("Nombre del tipo de documento (ejemplo: DNI, RUC, Pasaporte). Longitud máxima: 100 caracteres.");

            $table->timestamp("fecha_registro")
                ->useCurrent()
                ->comment("Fecha de creación del registro. Se asigna automáticamente al insertar.");

            $table->timestamp("fecha_actualizacion")
                ->nullable()
                ->comment("Fecha de la última actualización/modificación del registro. Puede ser nula si nunca se actualizó.");
        });
        DB::insert('insert into tipo_documentos (id_tipo_documento, nombre_tipo_documento) values (1, "DNI")');
        DB::insert('insert into tipo_documentos (id_tipo_documento, nombre_tipo_documento) values (2, "CCEE")');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipo_documentos');
    }
};
