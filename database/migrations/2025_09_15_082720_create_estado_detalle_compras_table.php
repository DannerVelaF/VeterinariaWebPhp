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
        Schema::create('estado_detalle_compras', function (Blueprint $table) {
            $table->id("id_estado_detalle_compra")->comment("Llave primaria. Identificador único de estado de detalle de compra.");
            $table->string("nombre_estado_detalle_compra", 25)->comment("Nombre del estado de detalle de compra.");
            $table->timestamp("fecha_registro")
                ->useCurrent()
                ->comment("Fecha de creación del registro de la mascota.");

            $table->timestamp("fecha_actualizacion")
                ->nullable()
                ->comment("Fecha de la última actualización del registro.");
        });
        DB::insert('insert into estado_detalle_compras (id_estado_detalle_compra, nombre_estado_detalle_compra) values (1, "Pendiente")');
        DB::insert('insert into estado_detalle_compras (id_estado_detalle_compra, nombre_estado_detalle_compra) values (2, "Recibido")');
        DB::insert('insert into estado_detalle_compras (id_estado_detalle_compra, nombre_estado_detalle_compra) values (3, "Pagado")');
        DB::insert('insert into estado_detalle_compras (id_estado_detalle_compra, nombre_estado_detalle_compra) values (4, "Cancelado")');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estado_detalle_compras');
    }
};
