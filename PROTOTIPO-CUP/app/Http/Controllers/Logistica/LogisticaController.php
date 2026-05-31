<?php

namespace App\Http\Controllers\Logistica;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Postulante;
use App\Models\Grupo;
use Illuminate\Support\Facades\DB;

class LogisticaController extends Controller
{
    // ==========================================
    // CU19: Calcular Cantidad de Grupos Necesarios (Algoritmo CEIL)
    // ==========================================

    // 1: solicitarCalculoGrupos()
    public function index()
    {
        // 2 y 3: contarInscritos()
        $totalInscritos = $this->contarPostulantesHabilitados();
        
        // 5: aplicarCEIL(n / 80)
        $cantGruposCalculados = $this->aplicarAlgoritmoCEIL($totalInscritos);

        // Traer grupos ya creados para mostrarlos en la UI (IU_Logistica)
        $gruposActuales = Grupo::orderBy('nombre_grupo')->get();

        // CU20: Datos para el reporte de distribución
        // Cuántos alumnos están sin grupo asignado
        $postulantesSinGrupo = $this->recuperarInscritosSinGrupo()->count();
        
        // Reporte de ocupación por grupo
        $reporteGrupos = DB::table('grupo')
            ->leftJoin('grupo_postulante', 'grupo.id_grupo', '=', 'grupo_postulante.id_grupo')
            ->select('grupo.id_grupo', 'grupo.nombre_grupo', 'grupo.capacidad_maxima', 'grupo.aula', 'grupo.turno', 'grupo.activo', DB::raw('count(grupo_postulante.id_postulante) as cupo_actual'))
            ->groupBy('grupo.id_grupo', 'grupo.nombre_grupo', 'grupo.capacidad_maxima', 'grupo.aula', 'grupo.turno', 'grupo.activo')
            ->orderBy('grupo.nombre_grupo')
            ->get();

        return view('logistica.index', compact('totalInscritos', 'cantGruposCalculados', 'gruposActuales', 'postulantesSinGrupo', 'reporteGrupos'));
    }

    // Método encapsulado: cuentaAlumnosInscritos
    private function contarPostulantesHabilitados()
    {
        // Contamos los postulantes cuyo estado de inscripción indique que están inscritos firmemente.
        return Postulante::where('estado_habilitacion', 'Inscrito')->count();
    }

    // Método encapsulado: aplicarAlgoritmoCEIL(Total / 80)
    private function aplicarAlgoritmoCEIL($total)
    {
        if ($total == 0) return 0;
        return (int) ceil($total / 80.0);
    }

    // 4 (Clase): crearEstructuraGrupos() / generarInstanciasDeGrupos
    public function generarGruposMasivos(Request $request)
    {
        $request->validate([
            'cantidad_grupos' => 'required|integer|min:1|max:26'
        ]);

        $cantidadDeseada = (int) $request->input('cantidad_grupos');

        // Alfabeto base para los sufijos de los grupos (SA, SB, SC...)
        $letras = range('A', 'Z');
        
        $gruposCreados = 0;

        DB::transaction(function () use ($cantidadDeseada, $letras, &$gruposCreados) {
            $gruposExistentesCount = Grupo::count();
            $totalDeseado = $gruposExistentesCount + $cantidadDeseada;

            for ($i = 0; $i < $cantidadDeseada; $i++) {
                $indice = $gruposExistentesCount + $i;
                $letra = isset($letras[$indice]) ? $letras[$indice] : 'X' . $indice;
                $nombreGrupo = 'S' . $letra;

                Grupo::create([
                    'nombre_grupo' => $nombreGrupo,
                    'capacidad_maxima' => 80,
                    'aula' => 'Por Asignar',
                    'turno' => 'MAÑANA',
                    'activo' => true
                ]);

                $gruposCreados++;
            }
        });

        return back()->with('success', "Se crearon $gruposCreados grupos nuevos. Ahora debe configurar el Aula, Turno y Horario de cada uno usando el botón 'Editar'.");
    }

    // ==========================================
    // CU20: Asignar Postulantes a Grupos y Aulas
    // ==========================================

    // 1: recuperarInscritosSinGrupo()
    private function recuperarInscritosSinGrupo()
    {
        // Recuperamos los postulantes 'Inscrito' que NO están en la tabla grupo_postulante
        return Postulante::where('estado_habilitacion', 'Inscrito')
            ->whereNotIn('id_postulante', function ($query) {
                $query->select('id_postulante')->from('grupo_postulante');
            })->get();
    }

    // 2: validarLimite70PorAula()
    private function validarLimite70PorAula()
    {
        // El diagrama del CU20 exige un límite estricto de 70, a pesar de que la capacidad física es 80
        return 70;
    }

    // 3: persistirAsignacionMasiva()
    public function asignarPostulantes()
    {
        $postulantes = $this->recuperarInscritosSinGrupo();
        
        if ($postulantes->isEmpty()) {
            return back()->with('info', 'Todos los postulantes inscritos ya tienen un grupo asignado.');
        }

        $limitePorGrupo = $this->validarLimite70PorAula();
        
        // Obtenemos los grupos ordenados alfabéticamente
        $grupos = Grupo::where('activo', true)->orderBy('nombre_grupo')->get();
        
        if ($grupos->isEmpty()) {
            return back()->with('error', 'No existen grupos creados. Ejecute el CU19 primero.');
        }

        $asignaciones = [];
        $gruposSaturados = false;
        $now = now();

        DB::transaction(function () use ($postulantes, $grupos, $limitePorGrupo, &$asignaciones, &$gruposSaturados, $now) {
            foreach ($postulantes as $postulante) {
                $asignado = false;
                
                // Recorremos los grupos buscando uno con cupo disponible (<= 70)
                foreach ($grupos as $grupo) {
                    $ocupacionActual = DB::table('grupo_postulante')->where('id_grupo', $grupo->id_grupo)->count();
                    
                    if ($ocupacionActual < $limitePorGrupo) {
                        $asignaciones[] = [
                            'id_grupo' => $grupo->id_grupo,
                            'id_postulante' => $postulante->id_postulante,
                            'fecha_asignacion' => $now
                        ];
                        // Registramos de forma instantánea para que la ocupación se refleje en la siguiente iteración del foreach
                        DB::table('grupo_postulante')->insert([
                            'id_grupo' => $grupo->id_grupo,
                            'id_postulante' => $postulante->id_postulante,
                            'fecha_asignacion' => $now
                        ]);
                        $asignado = true;
                        break; // Pasamos al siguiente postulante
                    }
                }

                if (!$asignado) {
                    $gruposSaturados = true;
                    break; // No hay más espacio en ningún grupo
                }
            }
        });

        $cantidadAsignada = count($asignaciones);

        if ($gruposSaturados) {
            return back()->with('error', "Se asignaron $cantidadAsignada postulantes, pero se saturó la capacidad de todos los grupos al límite de 70 (Regla CU20). Debe crear más grupos (CU19).");
        }

        return back()->with('success', "Asignación masiva exitosa: Se asignaron $cantidadAsignada postulantes a sus respectivos grupos.");
    }

    public function editGrupo($id)
    {
        $grupo = Grupo::findOrFail($id);
        return view('logistica.edit_grupo', compact('grupo'));
    }

    public function updateGrupo(Request $request, $id)
    {
        $request->validate([
            'aula' => 'required|string|max:100',
            'turno' => 'required|in:MAÑANA,TARDE,NOCHE',
            'capacidad_maxima' => 'required|integer|min:1',
            'activo' => 'boolean'
        ]);

        $grupo = Grupo::findOrFail($id);
        $grupo->update([
            'aula' => $request->aula,
            'turno' => $request->turno,
            'capacidad_maxima' => $request->capacidad_maxima,
            'activo' => $request->has('activo')
        ]);

        return redirect()->route('logistica.index')->with('success', 'El grupo ' . $grupo->nombre_grupo . ' ha sido actualizado correctamente.');
    }
}
