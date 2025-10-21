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
        Schema::create('estado_compras', function (Blueprint $table) {
            $table->id("id_estado_compra")->comment("Identificador único del turno horario");;
            $table->string("nombre_estado_compra")->comment("Nombre del estado de compra");
            $table->timestamp("fecha_registro")->useCurrent()->comment("Fecha en que se creó el registro del turno");
            $table->timestamp("fecha_actualizacion")->nullable()->comment("Fecha de la última actualización del registro");
        });
        DB::table('estado_compras')->insert([
            'nombre_estado_compra' => 'pendiente',
            'fecha_registro' => now(),
            'fecha_actualizacion' => now(),
        ]);
        DB::table('estado_compras')->insert([
            'nombre_estado_compra' => 'aprobado',
            'fecha_registro' => now(),
            'fecha_actualizacion' => now(),
        ]);
        DB::table('estado_compras')->insert([
            'nombre_estado_compra' => 'recibido',
            'fecha_registro' => now(),
            'fecha_actualizacion' => now(),
        ]);
        DB::table('estado_compras')->insert([
            'nombre_estado_compra' => 'cancelado',
            'fecha_registro' => now(),
            'fecha_actualizacion' => now(),
        ]);
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estado_compras');
    }
};
