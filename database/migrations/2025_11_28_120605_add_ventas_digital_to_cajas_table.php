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
        Schema::table('cajas', function (Blueprint $table) {
            $table->decimal('ventas_digital', 10, 2)->default(0)->after('ventas_transferencia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cajas', function (Blueprint $table) {
            $table->dropColumn('ventas_digital');
        });
    }
};
