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
        Schema::create('roles_permisos', function (Blueprint $table) {
            $table->id("id_rol_permiso")
                ->comment("Llave primaria. Identificador único de la relación rol-permiso.");

            $table->unsignedBigInteger("id_rol")
                ->comment("Llave foránea hacia la tabla roles. Indica el rol asociado.");
            $table->foreign("id_rol")
                ->references("id_rol")
                ->on("roles")
                ->onDelete("cascade");

            $table->unsignedBigInteger("id_permiso")
                ->comment("Llave foránea hacia la tabla permisos. Indica el permiso asignado.");
            $table->foreign("id_permiso")
                ->references("id_permiso")
                ->on("permisos")
                ->onDelete("cascade");

            $table->timestamp("fecha_registro")
                ->useCurrent()
                ->comment("Fecha de creación del registro de relación rol-permiso.");

            $table->timestamp("fecha_actualizacion")
                ->nullable()
                ->comment("Fecha de la última actualización del registro.");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles_permisos');
    }
};
