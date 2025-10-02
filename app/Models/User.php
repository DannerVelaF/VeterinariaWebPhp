<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        "id_usuario",
        "usuario",
        "contrasena",
        'estado',
        'id_persona',
        'fecha_registro',
        'fecha_actualizacion',
        'id_rol',
    ];

    public $timestamps = true;

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'contrasena',
        'remember_token',
    ];
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'contrasena' => 'hashed',
        ];
    }

    public function getAuthPassword()
    {
        return $this->contrasena;
    }

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'id_persona');
    }

    public function comprasAprobadas()
    {
        return $this->hasMany(Compra::class, "id_usuario_aprobador", "id_usuario");
    }
    public function rol()
    {
        return $this->belongsTo(Roles::class, 'id_rol');
    }

    public function tienePermiso($permiso)
    {
        return $this->rol
            && $this->rol->permisos->where('nombre_permiso', $permiso)->isNotEmpty();
    }
}
