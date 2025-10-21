<?php

namespace Database\Seeders;

use App\Models\Persona;
use App\Models\Roles;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $rol =  Roles::create([
            'nombre_rol' => 'Administrador',
            'fecha_creacion' => now(),
            'fecha_modificacion' => now(),
            'estado' => 'activo',
        ]);

        $p =  Persona::create([
            'numero_documento' => 12345677,
            'nombre' => 'Eberth',
            'apellido_paterno' => 'x',
            'apellido_materno' => 'x',
            'fecha_nacimiento' => '1990-01-01',
            'sexo' => 'M', // en minúsculas porque tu enum lo define así
            'nacionalidad' => 'Peruana',
            "correo_electronico_personal" => "eberthsn.01@gmail.com",
            "correo_electronico_secundario" => null,
            "numero_telefono_personal" => "987654321",
            "numero_telefono_secundario" => null,
            'id_tipo_documento' => 1,
            'id_direccion' => 1,
        ]);

        User::create([
            'username' => 'eberth',
            'password_hash' => Hash::make('eberth'),
            'estado' => 'activo',
            'id_persona' => $p->id,
            'rol_id' => $rol->id,
        ]);
    }
}
