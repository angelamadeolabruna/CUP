<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable;

    // En PUDS, esta es la "Clase Entidad" (Entity Class)
    protected $table = 'usuario';
    protected $primaryKey = 'id_usuario';
    public $timestamps = true;

    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'password_hash',
        'id_rol',
        'activo',
        'ultimo_acceso',
        'esta_logueado',
        'ultima_actividad',
        'token_recuperacion',
        'fecha_expiracion_token',
        'fecha_ultimo_cambio',
        'ci'
    ];

    protected $hidden = [
        'password_hash',
    ];

    // Laravel espera "password" para autenticación, mapeamos nuestro campo
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol', 'id_rol');
    }

    public function postulante()
    {
        return $this->hasOne(Postulante::class, 'id_usuario', 'id_usuario');
    }
}
