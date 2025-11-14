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
        Schema::create('estado_ventas_fisicas', function (Blueprint $table) {
            $table->id("id_estado_venta_fisica")->comment("Identificador único del estado de venta física");
            $table->string("nombre_estado_venta_fisica")->comment("Nombre del estado de venta física");
            $table->timestamp("fecha_registro")->useCurrent()->comment("Fecha en que se creó el registro del estado de venta física");
            $table->timestamp("fecha_actualizacion")->nullable()->comment("Fecha de la última actualización del registro");
        });
        DB::table('estado_ventas_fisicas')->insert([
            'nombre_estado_venta_fisica' => 'pendiente',
            'fecha_registro' => now(),
            'fecha_actualizacion' => now(),
        ]);
        DB::table('estado_ventas_fisicas')->insert([
            'nombre_estado_venta_fisica' => 'completado',
            'fecha_registro' => now(),
            'fecha_actualizacion' => now(),
        ]);
        DB::table('estado_ventas_fisicas')->insert([
            'nombre_estado_venta_fisica' => 'cancelado',
            'fecha_registro' => now(),
            'fecha_actualizacion' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estado_ventas_fisicas');
    }
};
