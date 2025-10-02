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
        Schema::create('estado_citas', function (Blueprint $table) {
            $table->id("id_estado_cita")->comment("Llave primaria. Identificador único de estado de cita.");
            $table->string("nombre_estado_cita")->comment("Nombre del estado de cita.");
            $table->timestamp("fecha_registro")->useCurrent()->comment("Fecha de creación del registro de estado de cita.");
            $table->timestamp("fecha_actualizacion")->nullable()->comment("Fecha de la última actualización del registro.");
        });
        DB::insert('insert into estado_citas (id_estado_cita, nombre_estado_cita) values (1, "Pendiente")');
        DB::insert('insert into estado_citas (id_estado_cita, nombre_estado_cita) values (2, "En progreso")');
        DB::insert('insert into estado_citas (id_estado_cita, nombre_estado_cita) values (3, "Completada")');
        DB::insert('insert into estado_citas (id_estado_cita, nombre_estado_cita) values (4, "Cancelada")');
        DB::insert('insert into estado_citas (id_estado_cita, nombre_estado_cita) values (5, "No asistio")');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estado_citas');
    }
};
