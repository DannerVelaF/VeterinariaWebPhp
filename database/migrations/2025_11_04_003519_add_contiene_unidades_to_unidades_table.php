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
        Schema::table('unidades', function (Blueprint $table) {
            $table->boolean('contiene_unidades')
                ->default(false)
                ->after('nombre_unidad')
                ->comment('Indica si esta unidad contiene múltiples unidades individuales (ej: Caja, Paquete, Bolsa)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('unidades', function (Blueprint $table) {
            $table->dropColumn('contiene_unidades');
        });
    }
};
