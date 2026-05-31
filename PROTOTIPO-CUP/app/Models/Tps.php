<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tps extends Model
{
    use HasFactory;
    
    // Este modelo representa CE_TPS del Diagrama CU02
    protected $table = 'bitacora_auditoria';
    protected $primaryKey = 'id_bitacora';
    public $timestamps = false; // Manejamos la fecha manualmente según el diagrama

    protected $fillable = [
        'id_usuario',
        'accion',
        'tabla_afectada',
        'descripcion',
        'ip_origen',
        'fecha_hora'
    ];
}
