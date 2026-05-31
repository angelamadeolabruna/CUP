<?php

namespace App\Http\Controllers\Academico;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Postulante;
use App\Models\Nota;
use App\Models\Grupo;
use Illuminate\Support\Str;

class AcademicoController extends Controller
{
    // ==========================================
    // CU13: Registrar Notas de Materias
    // CTR_Academico
    // ==========================================

    // 2: mostrarFormularioNotas(idPostulante) -> En este caso, mostraremos la lista y el form
    public function mostrarListaInscritos(Request $request)
    {
        // Obtener grupos para filtrar
        $grupos = Grupo::where('activo', true)->get();
        $grupoSeleccionado = $request->grupo_id;
        
        $postulantes = [];
        if ($grupoSeleccionado) {
            // Traer postulantes del grupo seleccionado
            $postulantes = Postulante::join('grupo_postulante', 'postulante.ci', '=', 'grupo_postulante.ci_postulante')
                ->where('grupo_postulante.id_grupo', $grupoSeleccionado)
                ->select('postulante.*')
                ->get();
        }

        // Recuperar notas existentes para mostrarlas en la vista
        $notasRegistradas = Nota::with('postulante')->orderBy('ci_postulante')->get();

        return view('evaluacion.notas', compact('postulantes', 'grupos', 'grupoSeleccionado', 'notasRegistradas'));
    }

    // 1: enviarNotas(calif) -> capturarCalificaciones()
    public function capturarCalificaciones(Request $request)
    {
        $request->validate([
            'ci_postulante' => 'required|exists:postulante,ci',
            'nro_examen' => 'required|integer|min:1|max:3',
            'gestion' => 'required|string',
            // Atributos definidos en IU_Evaluacion:
            'inputComputacion' => 'nullable|numeric',
            'inputMatematica' => 'nullable|numeric',
            'inputIngles' => 'nullable|numeric',
            'inputFisica' => 'nullable|numeric',
        ]);

        $ci = $request->ci_postulante;
        $nro_examen = $request->nro_examen;
        $gestion = $request->gestion;

        $materiasIngresadas = 0;

        // Validar y persistir Computación
        if ($request->filled('inputComputacion')) {
            $this->validarRangoNota($request->inputComputacion);
            $this->persistirCalificaciones($ci, 'Computación', $request->inputComputacion, $nro_examen, $gestion);
            $materiasIngresadas++;
        }

        // Validar y persistir Matemáticas
        if ($request->filled('inputMatematica')) {
            $this->validarRangoNota($request->inputMatematica);
            $this->persistirCalificaciones($ci, 'Matemáticas', $request->inputMatematica, $nro_examen, $gestion);
            $materiasIngresadas++;
        }

        // Validar y persistir Inglés
        if ($request->filled('inputIngles')) {
            $this->validarRangoNota($request->inputIngles);
            $this->persistirCalificaciones($ci, 'Inglés', $request->inputIngles, $nro_examen, $gestion);
            $materiasIngresadas++;
        }

        // Validar y persistir Física
        if ($request->filled('inputFisica')) {
            $this->validarRangoNota($request->inputFisica);
            $this->persistirCalificaciones($ci, 'Física', $request->inputFisica, $nro_examen, $gestion);
            $materiasIngresadas++;
        }

        if ($materiasIngresadas === 0) {
            return back()->with('error', 'Debe ingresar al menos la calificación de una materia para el postulante.');
        }

        // 6: dispararCalculo(CU15) - Se implementará en el futuro
        $this->dispararCalculoPromedios($ci);

        // 4: notificarExito()
        return back()->with('success', 'Las calificaciones del postulante han sido registradas correctamente (Examen ' . $nro_examen . ').');
    }

    // 3: validarRango(nota, 0-100)
    private function validarRangoNota($nota)
    {
        if ($nota < 0 || $nota > 100) {
            abort(redirect()->back()->with('error', "La nota ingresada ($nota) es inválida. El rango permitido es de 0 a 100."));
        }
        return true; // 4: rangoValido()
    }

    // 5: persistirNota(materia, valor) -> 2: crearRegistros(PostgreSQL)
    private function persistirCalificaciones($ci, $materia, $valorPuntaje, $nroExamen, $gestion)
    {
        // Si ya existe la nota para esa materia y nro de examen, la actualizamos
        $notaExistente = Nota::where('ci_postulante', $ci)
            ->where('materia', $materia)
            ->where('nro_examen', $nroExamen)
            ->where('gestion', $gestion)
            ->first();

        if ($notaExistente) {
            $notaExistente->update(['valor_puntaje' => $valorPuntaje]);
        } else {
            Nota::create([
                'id_nota' => Str::uuid(),
                'ci_postulante' => $ci, // 3: verificarIdentidad(ci) con CE_Postulante
                'materia' => $materia,
                'valor_puntaje' => $valorPuntaje,
                'nro_examen' => $nroExamen,
                'gestion' => $gestion
            ]);
        }
    }

    private function dispararCalculoPromedios($ci)
    {
        // 1: solicitaCalculo (CU15)
        $this->aplicarAlgoritmoPromedios($ci);
    }

    // 3: aplicarAlgoritmoPromedios() -> (N1+N2+N3)/3
    private function aplicarAlgoritmoPromedios($ci)
    {
        // 2: recuperarNotas(ci, materia) / obtieneTresNotas(ci)
        // Agrupamos por materia para sacar el promedio (N1+N2+N3)/3
        $notas = Nota::where('ci_postulante', $ci)->get();

        if ($notas->isEmpty()) {
            return;
        }

        // Agrupamos las notas por materia
        $notasPorMateria = $notas->groupBy('materia');
        
        $sumaPromediosMaterias = 0;
        $cantidadMaterias = 0;

        foreach ($notasPorMateria as $materia => $notasDeLaMateria) {
            // (N1 + N2 + N3) / cantidad
            $promedioMateria = $notasDeLaMateria->avg('valor_puntaje');
            $sumaPromediosMaterias += $promedioMateria;
            $cantidadMaterias++;
        }

        // Promedio Global del Postulante
        $promedioFinal = $cantidadMaterias > 0 ? ($sumaPromediosMaterias / $cantidadMaterias) : 0;

        // 4: actualizaPromedio() -> persistirPromedioFinal()
        $this->persistirPromedioFinal($ci, $promedioFinal);
    }

    private function persistirPromedioFinal($ci, $promedioFinal)
    {
        $postulante = Postulante::where('ci', $ci)->first();
        if ($postulante) {
            $postulante->update(['promedio_final' => $promedioFinal]);
            
            // 6: dispararVerificacionAprobacion() (CU16)
            $this->dispararVerificacionAprobacion($ci);
        }
    }

    // ==========================================
    // CU16: Verificar Estado de Aprobación
    // ==========================================

    private function dispararVerificacionAprobacion($ci)
    {
        $postulante = Postulante::where('ci', $ci)->first();
        if (!$postulante) return;

        // 2: recuperarPromedio(ci)
        $promedio = $postulante->promedio_final;

        // 3 y 4: aplicarReglaAprobacion(promedio >= 60)
        $estado = $this->aplicarReglaAprobacion($promedio);

        // 5: actualizarEstado(APROBADO/REPROBADO)
        $this->actualizarEstadoPostulante($postulante, $estado);
    }

    private function aplicarReglaAprobacion($promedio)
    {
        return $promedio >= 60 ? 'APROBADO' : 'REPROBADO';
    }

    private function actualizarEstadoPostulante($postulante, $estado)
    {
        $postulante->update(['estado' => $estado]);
    }
}
