<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CargaHoraria extends Model
{
    use HasFactory;

    // CE_CargaHoraria
    protected $table = 'carga_horaria';
    protected $primaryKey = 'id_asignacion';
    public $incrementing = false; // Because it's a UUID
    protected $keyType = 'string';

    public $timestamps = false; // Usamos fecha_registro manualmente

    protected $fillable = [
        'id_asignacion',
        'gestion_academica',
        'fecha_registro',
        'id_docente',
        'id_grupo'
    ];
}
