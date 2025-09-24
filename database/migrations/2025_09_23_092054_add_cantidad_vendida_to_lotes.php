<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('lotes', function (Blueprint $table) {
            if (!Schema::hasColumn('lotes','cantidad_vendida')) {
                $table->decimal('cantidad_vendida', 12, 2)->default(0)->after('cantidad_total');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lotes', function (Blueprint $table) {
            //
        });
    }
};
