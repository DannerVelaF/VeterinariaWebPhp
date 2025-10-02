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
        Schema::create('tipo_ubicacion', function (Blueprint $table) {
            $table->id("id_tipo_ubicacion")
                ->comment("Llave primaria del tipo de ubicación. Identificador único.");

            $table->string("nombre_tipo_ubicacion", 25)
                ->comment("Nombre del tipo de ubicación. Ejemplos: almacen, mostrador.");

            $table->timestamp("fecha_registro")
                ->useCurrent()
                ->comment("Fecha de creación del registro del tipo de ubicación.");

            $table->timestamp("fecha_actualizacion")
                ->nullable()
                ->comment("Fecha de la última actualización del registro.");
        });
        DB::insert('insert into tipo_ubicacion (id_tipo_ubicacion, nombre_tipo_ubicacion) values (1, "almacen")');
        DB::insert('insert into tipo_ubicacion (id_tipo_ubicacion, nombre_tipo_ubicacion) values (2, "mostrador")');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipo_ubicacion');
    }
};
