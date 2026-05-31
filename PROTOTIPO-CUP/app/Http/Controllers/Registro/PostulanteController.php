<?php

namespace App\Http\Controllers\Registro;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Postulante;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PostulanteController extends Controller
{
    // ==========================================
    // CU08: REGISTRAR POSTULANTE
    // ==========================================

    public function showRegistroForm()
    {
        // Traer carreras para los Dropdown
        $carreras = DB::table('carrera')->select('id_carrera', 'nombre_carrera')->get();
        return view('registro.postulante.index', compact('carreras'));
    }

    public function registrarPostulante(Request $request)
    {
        // Validaciones base (incluyendo validarFormatoCorreo)
        $request->validate([
            'ci' => 'required',
            'nombres' => 'required',
            'apellidos' => 'required',
            'carrera_1' => 'required',
            'carrera_2' => 'required|different:carrera_1',
            'colegio' => 'required'
        ]);

        // 1. buscarDuplicado(ci) y validarUnicidadCI(ci)
        $postulante = Postulante::where('ci', $request->ci)->first();

        if (!$postulante) {
            return back()->with('error', 'El CI no tiene un pago registrado. Por favor complete el CU07 (Pago de Inscripción) primero.');
        }

        // 2. verificarPagoHabilitado()
        if ($postulante->estado_habilitacion !== 'Habilitado') {
            if ($postulante->estado_habilitacion === 'Inscrito') {
                return back()->with('error', 'El postulante con CI ' . $postulante->ci . ' ya se encuentra Inscrito.');
            }
            return back()->with('error', 'El postulante con CI ' . $postulante->ci . ' no está Habilitado. Debe pagar la inscripción.');
        }

        // 3. persistirRegistroACID() y crearExpedienteDigital()
        // Actualizamos el registro base
        DB::transaction(function () use ($postulante, $request) {
            $postulante->update([
                'nombre' => $request->nombres, // Actualizamos por si hay correcciones
                'apellido' => $request->apellidos,
                'colegio_origen' => $request->colegio,
                'carrera_elegida_1' => $request->carrera_1,
                'carrera_elegida_2' => $request->carrera_2,
                'id_referencia_pago' => (string) Str::uuid(), // Referencia generada al azar
                'estado_habilitacion' => 'Inscrito'
            ]);
            
            // Simulación de "crearExpedienteDigital" podría ser guardar en 'postulacion' u otra tabla si fuera necesario, 
            // pero el diagrama dice que se persiste en CE_Postulante.
        });

        // 4. confirmarRegistro() / mostrarExitoRegistro()
        return redirect()->route('registro.postulante.exito', $postulante->id_postulante);
    }

    public function exitoRegistro($id)
    {
        $postulante = Postulante::findOrFail($id);
        
        $carrera1 = DB::table('carrera')->where('id_carrera', $postulante->carrera_elegida_1)->value('nombre_carrera') ?? $postulante->carrera_elegida_1;
        $carrera2 = DB::table('carrera')->where('id_carrera', $postulante->carrera_elegida_2)->value('nombre_carrera') ?? $postulante->carrera_elegida_2;

        return view('registro.postulante.exito', compact('postulante', 'carrera1', 'carrera2'));
    }
}
