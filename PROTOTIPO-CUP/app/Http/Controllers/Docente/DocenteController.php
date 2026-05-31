<?php

namespace App\Http\Controllers\Docente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Docente;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DocenteController extends Controller
{
    // ==========================================
    // CU21: Registrar Docente (Gestión de Docentes)
    // ==========================================

    public function index()
    {
        return view('docente.registro');
    }

    // 1: enviarPerfilDocente(request)
    public function registrar(Request $request)
    {
        // Validación base del formulario (IU_Docentes: capturarDatosPerfil)
        $request->validate([
            'txtCI' => 'required|string|max:15',
            'txtNombres' => 'required|string|max:255',
            'txtEspecialidad' => 'required|string|max:255',
        ]);

        $ci = $request->input('txtCI');
        $tieneMaestria = $request->has('chkMaestria');
        $tieneDiplomado = $request->has('chkDiplomado');

        // 2: verificarDuplicado(ci) -> validarUnicidadCI(ci)
        if (!$this->validarUnicidadCI($ci)) {
            return back()->with('error', 'El CI ingresado ya se encuentra registrado como docente.')->withInput();
        }

        // 3: validarRequisitosFICCT() -> validarTitulosObligatorios()
        if (!$this->validarRequisitosFICCT($tieneMaestria, $tieneDiplomado)) {
            // notificarFaltaRequisitos()
            return back()->with('error_requisitos', 'El docente no cumple con los requisitos mínimos de la FICCT. Debe poseer al menos una Maestría o un Diplomado para ser habilitado.')->withInput();
        }

        // 4: persistirNuevoDocente() -> crearRegistroContratacion()
        try {
            // Sincronizar secuencia de PostgreSQL para evitar conflictos de clave duplicada
            $this->sincronizarSecuencia();
            $this->persistirNuevoDocente($request, $tieneMaestria, $tieneDiplomado);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Error al registrar docente: ' . $e->getMessage());
            // Si es un error de clave duplicada, reintentar con secuencia corregida
            if (str_contains($e->getMessage(), '23505')) {
                try {
                    $this->sincronizarSecuencia();
                    $this->persistirNuevoDocente($request, $tieneMaestria, $tieneDiplomado);
                } catch (\Exception $e2) {
                    return back()->with('error', 'Error de base de datos al registrar el docente: ' . $e2->getMessage())->withInput();
                }
            } else {
                return back()->with('error', 'Error de base de datos al registrar el docente: ' . $e->getMessage())->withInput();
            }
        }

        // 5: confirmarHabilitacion()
        return back()->with('success', 'El Docente ha sido registrado y habilitado exitosamente en el sistema.');
    }

    /**
     * Sincroniza la secuencia de PostgreSQL para la tabla docente.
     * Esto previene errores de "duplicate key value violates unique constraint".
     */
    private function sincronizarSecuencia()
    {
        DB::statement("SELECT setval('docente_id_docente_seq', COALESCE((SELECT MAX(id_docente) FROM docente), 0))");
    }

    private function validarUnicidadCI($ci)
    {
        // Retorna false si existe, true si es único
        return !Docente::where('ci', $ci)->exists();
    }

    private function validarRequisitosFICCT($maestria, $diplomado)
    {
        // Regla estricta del diagrama: Debe tener al menos uno para ser habilitado
        return ($maestria || $diplomado);
    }

    private function persistirNuevoDocente(Request $request, $maestria, $diplomado)
    {
        // El constraint solo acepta: LICENCIATURA, MAESTRIA, DOCTORADO, DIPLOMADO
        // Priorizar el título más alto
        $tituloAcademico = 'LICENCIATURA'; // Default
        if ($diplomado) $tituloAcademico = 'DIPLOMADO';
        if ($maestria) $tituloAcademico = 'MAESTRIA';

        Docente::create([
            'ci' => $request->input('txtCI'),
            'nombre_completo' => $request->input('txtNombres'),
            'especialidad' => $request->input('txtEspecialidad'),
            'titulo_academico' => $tituloAcademico,
            'tiene_maestria' => $maestria,
            'tiene_diplomado' => $diplomado,
            'estado_habilitado' => true,
            'activo' => true,
            'cantidad_grupos_actual' => 0
        ]);
    }

    // ==========================================
    // CU22: Asignar Docente a Grupo (Carga Horaria)
    // ==========================================

    public function asignar()
    {
        // 1: mostrarGruposDisponibles() -> enviar a IU_Asignacion
        $docentes = Docente::where('estado_habilitado', true)->get();
        // Solo traemos grupos que no estén asignados a algún docente ya
        $gruposDisponibles = \App\Models\Grupo::where('activo', true)
            ->whereNotIn('id_grupo', function($q) {
                $q->select('id_grupo')->from('carga_horaria');
            })->get();
        
        // Reporte de carga académica (docentes y sus grupos)
        $cargaHoraria = \App\Models\CargaHoraria::join('docente', 'carga_horaria.id_docente', '=', 'docente.id_docente')
            ->join('grupo', 'carga_horaria.id_grupo', '=', 'grupo.id_grupo')
            ->select('carga_horaria.*', 'docente.nombre_completo', 'docente.ci', 'grupo.nombre_grupo', 'grupo.horario_asignado', 'grupo.materia_asociada')
            ->orderBy('carga_horaria.fecha_registro', 'desc')
            ->get();

        return view('docente.asignacion', compact('docentes', 'gruposDisponibles', 'cargaHoraria'));
    }

    // 1: capturarSeleccionDocente() y procesar
    public function guardarAsignacion(Request $request)
    {
        $request->validate([
            'id_docente' => 'required|exists:docente,id_docente',
            'id_grupo' => 'required|exists:grupo,id_grupo',
            'horario_asignado' => 'required|string',
            'materia_asociada' => 'required|string',
            'gestion_academica' => 'required|string'
        ]);

        $docente = Docente::find($request->id_docente);
        $grupo = \App\Models\Grupo::find($request->id_grupo);
        $horarioInput = $request->horario_asignado;

        // 2: verificarLimite4Grupos(ci) -> validarDisponibilidad(ci)
        if (!$this->verificarLimite4Grupos($docente)) {
            // notificarLimiteExcedido()
            return back()->with('error_limite', 'El docente ' . $docente->nombre_completo . ' ha excedido el límite máximo de 4 grupos permitidos.');
        }

        // 3: detectarCruceHorario(horario) -> verificarSolapamiento()
        if ($this->detectarCruceHorario($docente->id_docente, $horarioInput)) {
            // notificarCruceHorario()
            return back()->with('error_horario', 'Existe un cruce de horario. El docente ya tiene asignado un grupo en el horario: ' . $horarioInput);
        }

        // 4 y 5: registrarCargaAcademica() -> persistirVinculoACID()
        try {
            $this->registrarCargaAcademica($docente, $grupo, $request);
        } catch (\Exception $e) {
            Log::error('Error al asignar docente: ' . $e->getMessage());
            return back()->with('error_limite', 'Error al asignar la carga horaria: ' . $e->getMessage());
        }

        return back()->with('success', 'La carga horaria ha sido asignada exitosamente al docente.');
    }

    private function verificarLimite4Grupos(Docente $docente)
    {
        // La regla de negocio indica un máximo de 4 grupos por docente
        return $docente->cantidad_grupos_actual < 4;
    }

    private function detectarCruceHorario($idDocente, $horarioInput)
    {
        // Busca si el docente ya tiene una carga horaria con un grupo que tenga ese mismo horario_asignado
        $cruce = \App\Models\CargaHoraria::join('grupo', 'carga_horaria.id_grupo', '=', 'grupo.id_grupo')
            ->where('carga_horaria.id_docente', $idDocente)
            ->where('grupo.horario_asignado', $horarioInput)
            ->exists();
        
        return $cruce;
    }

    private function registrarCargaAcademica(Docente $docente, \App\Models\Grupo $grupo, Request $request)
    {
        DB::transaction(function () use ($docente, $grupo, $request) {
            // 1. Actualizar el Grupo con la materia y horario
            $grupo->update([
                'horario_asignado' => $request->horario_asignado,
                'materia_asociada' => $request->materia_asociada
            ]);

            // 2. Crear Carga Horaria (CE_CargaHoraria)
            \App\Models\CargaHoraria::create([
                'id_asignacion' => \Illuminate\Support\Str::uuid(),
                'gestion_academica' => $request->gestion_academica,
                'fecha_registro' => now(),
                'id_docente' => $docente->id_docente,
                'id_grupo' => $grupo->id_grupo
            ]);

            // 3. Incrementar contador de grupos del Docente (CE_Docente)
            $docente->increment('cantidad_grupos_actual');
        });
    }
}
