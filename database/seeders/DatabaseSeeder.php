<?php

namespace Database\Seeders;

use App\Models\Persona;
use App\Models\Tipo_documento;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        Tipo_documento::create([
            'nombre' => 'DNI',
        ]);

        Persona::create([
            "numero_documento" => 12345678,
            'nombre' => 'Juan',
            'apellido_paterno' => 'Pérez',
            'apellido_materno' => 'Gómez',
            'fecha_nacimiento' => '1990-01-01',
            'sexo' => 'Masculino',
            'nacionalidad' => 'Peruana',
            "correo" => "alejandrovela09@gmail.com",
            'id_tipo_documento' => 1,
        ]);

        User::create([
            'username' => 'user',
            'password_hash' => bcrypt('user'),
            "estado" => "activo",
            "id_persona" => 1
        ]);
    }
}
