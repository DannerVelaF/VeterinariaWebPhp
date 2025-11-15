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
        Schema::table('ventas', function (Blueprint $table) {

            // Agregar la columna
            $table->unsignedBigInteger('id_estado_venta')
                ->after('observacion')
                ->default(1) // opcional, para que no falle si ya hay datos
                ->comment("Llave foránea hacia la tabla estado_ventas_fisicas. Indica el estado de la venta.");

            // Agregar la Foreign Key
            $table->foreign('id_estado_venta')
                ->references('id_estado_venta_fisica')
                ->on('estado_ventas_fisicas')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropForeign(['id_estado_venta']);
            $table->dropColumn('id_estado_venta');
        });
    }
};
