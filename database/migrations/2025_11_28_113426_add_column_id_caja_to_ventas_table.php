<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->unsignedBigInteger('id_caja')
                ->nullable()
                ->comment('Llave foránea que relaciona la venta con la caja creada');

            $table->foreign('id_caja')
                ->references('id_caja')
                ->on('cajas')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropForeign(['id_caja']);
            $table->dropColumn('id_caja');
        });
    }
};
