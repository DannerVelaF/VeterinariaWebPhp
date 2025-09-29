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
            $table->id("id_rol_permiso");
            $table->unsignedBigInteger("id_rol"); 
            $table->foreign("id_rol")->references("id_rol")->on("roles");
            $table->unsignedBigInteger("id_permiso");
            $table->foreign("id_permiso")->references("id_permiso")->on("permisos");
            $table->timestamp("fecha_registro")->useCurrent();
            $table->timestamp("fecha_actualizacion")->nullable();
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
