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
        Schema::create('tipo_movimientos', function (Blueprint $table) {
            $table->id("id_tipo_movimiento")
                ->comment("Llave primaria del tipo de movimiento de inventario. Identificador único.");

            $table->string("nombre_tipo_movimiento", 25)
                ->comment("Nombre del tipo de movimiento de inventario. Ejemplos: ajuste, entrada, salida, devolución, uso_interno");

            $table->timestamp("fecha_registro")
                ->useCurrent()
                ->comment("Fecha de creación del registro del tipo de movimiento.");

            $table->timestamp("fecha_actualizacion")
                ->nullable()
                ->comment("Fecha de la última actualización del registro.");
        });

        DB::insert('insert into tipo_movimientos (id_tipo_movimiento, nombre_tipo_movimiento) values (1, "ajuste")');
        DB::insert('insert into tipo_movimientos (id_tipo_movimiento, nombre_tipo_movimiento) values (2, "entrada")');
        DB::insert('insert into tipo_movimientos (id_tipo_movimiento, nombre_tipo_movimiento) values (3, "salida")');
        DB::insert('insert into tipo_movimientos (id_tipo_movimiento, nombre_tipo_movimiento) values (4, "devolución")');
        DB::insert('insert into tipo_movimientos (id_tipo_movimiento, nombre_tipo_movimiento) values (5, "uso_interno")');
        DB::insert('insert into tipo_movimientos (id_tipo_movimiento, nombre_tipo_movimiento) values (6, "de_baja")');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipo_movimientos');
    }
};
