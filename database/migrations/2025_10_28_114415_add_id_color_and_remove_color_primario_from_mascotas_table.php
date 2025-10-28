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
        Schema::table('mascotas', function (Blueprint $table) {
            $table->unsignedBigInteger('id_color')
                ->nullable()
                ->after('sexo')
                ->comment('Llave foránea hacia la tabla colores. Color principal de la mascota.');

            $table->foreign('id_color')
                ->references('id_color')
                ->on('colores')
                ->onDelete('restrict');

            $table->dropColumn('color_primario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mascotas', function (Blueprint $table) {
            $table->string('color_primario', 50)
                ->after('sexo')
                ->comment('Color primario de la mascota. Máximo 50 caracteres.');

            $table->dropForeign(['id_color']);

            // 3. Eliminar la columna id_color
            $table->dropColumn('id_color');
        });
    }
};
