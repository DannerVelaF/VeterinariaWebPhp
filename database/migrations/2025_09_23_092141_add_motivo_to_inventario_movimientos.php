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
        Schema::table('inventario_movimientos', function (Blueprint $table) {
            if (!Schema::hasColumn('inventario_movimientos', 'motivo')) {
                $table->text('motivo')
                    ->nullable()
                    ->after('ubicacion')
                    ->comment('Motivo del movimiento de inventario (opcional)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventario_movimientos', function (Blueprint $table) {
            if (Schema::hasColumn('inventario_movimientos', 'motivo')) {
                $table->dropColumn('motivo');
            }
        });
    }
};
