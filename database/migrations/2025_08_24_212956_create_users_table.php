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
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id("id_usuario")
                ->comment("Llave primaria de la tabla usuarios. Identificador único del usuario.");

            $table->string('usuario', 50)
                ->unique()
                ->comment("Nombre de usuario para autenticación. Máx. 50 caracteres. Debe ser único.");

            $table->string('contrasena', 255)
                ->comment("Contraseña encriptada del usuario. Longitud suficiente para bcrypt/argon2 (255 caracteres).");

            $table->enum("estado", ["activo", "inactivo"])
                ->default("activo")
                ->comment("Estado actual del usuario. Valores permitidos: 'activo', 'inactivo'.");

            $table->dateTime("ultimo_login")
                ->nullable()
                ->comment("Fecha y hora del último inicio de sesión. Puede ser nulo si nunca inició sesión.");

            $table->unsignedBigInteger("id_persona")
                ->nullable()
                ->comment("Llave foránea hacia la tabla personas. Relaciona el usuario con sus datos personales.");

            $table->foreign("id_persona")
                ->references("id_persona")
                ->on("personas")
                ->onUpdate("cascade")
                ->onDelete("set null");

            $table->unsignedBigInteger("id_rol")
                ->nullable()
                ->comment("Llave foránea hacia la tabla roles. Define el rol o perfil del usuario.");

            $table->foreign("id_rol")
                ->references("id_rol")
                ->on("roles")
                ->onUpdate("cascade")
                ->onDelete("set null");

            $table->timestamp("fecha_registro")
                ->useCurrent()
                ->comment("Fecha de creación del registro. Se asigna automáticamente al insertar.");

            $table->timestamp("fecha_actualizacion")
                ->nullable()
                ->comment("Fecha de última actualización/modificación del registro. Puede ser nula si nunca se actualizó.");
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email', 150)
                ->primary()
                ->comment("Correo electrónico asociado a la cuenta para la recuperación de contraseña. Máx. 150 caracteres.");

            $table->string('token', 255)
                ->comment("Token único y temporal generado para el restablecimiento de contraseña. Máx. 255 caracteres.");

            $table->timestamp('created_at')
                ->nullable()
                ->comment("Fecha y hora de creación del token. Puede ser nulo.");
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id', 255)
                ->primary()
                ->comment("Identificador único de la sesión.");

            $table->foreignId('user_id')
                ->nullable()
                ->index()
                ->comment("Usuario asociado a la sesión (si existe).");

            $table->string('ip_address', 45)
                ->nullable()
                ->comment("Dirección IP del cliente que inició la sesión. Soporta IPv4 e IPv6 (45 caracteres).");

            $table->text('user_agent')
                ->nullable()
                ->comment("Cadena del navegador/dispositivo usado en la sesión. Campo opcional.");

            $table->longText('payload')
                ->comment("Información serializada de la sesión.");

            $table->integer('last_activity')
                ->index()
                ->comment("Marca de tiempo UNIX de la última actividad de la sesión.");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
