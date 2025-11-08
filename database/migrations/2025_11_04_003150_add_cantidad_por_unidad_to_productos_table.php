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
        Schema::table('productos', function (Blueprint $table) {
            $table->integer('cantidad_por_unidad')
                ->nullable()
                ->after('id_unidad')
                ->comment('Cantidad de productos contenidos en la unidad (ej: 12 unidades por caja). Solo aplica para unidades como Caja, Paquetes, Bolsas.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn('cantidad_por_unidad');
        });
    }
};
