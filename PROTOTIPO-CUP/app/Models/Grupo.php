<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    use HasFactory;

    // CE_Grupo
    protected $table = 'grupo';
    protected $primaryKey = 'id_grupo';

    // Desactivamos timestamps si la tabla no los tiene. 
    // Vamos a asumir que sí los tiene, pero si da error los quitamos.
    public $timestamps = false; // La estructura mostrada antes no tenía created_at/updated_at

    protected $fillable = [
        'nombre_grupo',
        'capacidad_maxima',
        'aula',
        'turno',
        'activo',
        
        // CU22
        'horario_asignado',
        'materia_asociada'
    ];
}
