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
        Schema::create('compras', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("id_proveedor");
            $table->foreign("id_proveedor")->references("id")->on("proveedores");

            $table->unsignedBigInteger('id_trabajador');
            $table->foreign("id_trabajador")->references("id")->on("trabajadores");

            $table->string("codigo");
            $table->string("numero_factura");
            $table->date("fecha_compra");
            $table->date("fecha_actualizacion")->nullable();
            $table->decimal("cantidad_total", 12, 2);
            $table->decimal('total', 12, 2);
            $table->text("observacion")->nullable();
            $table->enum("estado", ["pendiente", "aprobado", "recibido", "pagado", "cancelado"])->default("pendiente");

            $table->unsignedBigInteger("id_usuario_aprobador")->nullable();
            $table->foreign("id_usuario_aprobador")->references("id")->on("users");

            $table->timestamp("fecha_registro");
            $table->timestamp("fecha_actualizacion")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compras');
    }
};
