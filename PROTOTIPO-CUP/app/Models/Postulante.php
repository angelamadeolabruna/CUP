<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Postulante extends Model
{
    use HasFactory;

    // CE_Postulante
    protected $table = 'postulante';
    protected $primaryKey = 'id_postulante';
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'ci',
        'nombre',
        'apellido',
        'fecha_nacimiento',
        'telefono',
        'colegio_origen',
        'estado_habilitacion',
        'carrera_elegida_1',
        'carrera_elegida_2',
        'id_referencia_pago',
        'promedio_final',
        'estado'
    ];

    public function notas()
    {
        return $this->hasMany(Nota::class, 'ci_postulante', 'ci');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'id_postulante', 'id_postulante');
    }
}
