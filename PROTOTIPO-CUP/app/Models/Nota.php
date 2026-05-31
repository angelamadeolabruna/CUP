<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nota extends Model
{
    use HasFactory;

    protected $table = 'nota';
    protected $primaryKey = 'id_nota';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_nota',
        'ci_postulante',
        'materia',
        'valor_puntaje',
        'nro_examen',
        'gestion'
    ];

    public function postulante()
    {
        return $this->belongsTo(Postulante::class, 'ci_postulante', 'ci');
    }
}
