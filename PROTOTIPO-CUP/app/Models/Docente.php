<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Docente extends Model
{
    use HasFactory;

    // CE_Docente
    protected $table = 'docente';
    protected $primaryKey = 'id_docente';

    // Desactivamos updated_at porque la tabla solo tiene created_at
    const UPDATED_AT = null;
    public $timestamps = true;

    protected $fillable = [
        'id_usuario', // Puede ser nulo
        'especialidad',
        'titulo_academico',
        'telefono',
        'activo', // Por compatibilidad
        
        // Agregados para CU21 idénticos al diagrama
        'ci',
        'nombre_completo',
        'tiene_maestria',
        'tiene_diplomado',
        'estado_habilitado',
        
        // CU22
        'cantidad_grupos_actual'
    ];
}
