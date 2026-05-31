<?php

namespace App\Http\Controllers\Registro;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Postulante;
use App\Models\Pago;
use Illuminate\Support\Str;

class TransaccionController extends Controller
{
    // ==========================================
    // CU07: PROCESAR PAGO DE INSCRIPCIÓN
    // ==========================================

    // 1: seleccionarRealizarPago() / solicitaProcesamiento
    public function showPagoForm()
    {
        return view('registro.pago.index');
    }

    // 2: generarOrdenPago() / invocaPago(API)
    public function procesarPago(Request $request)
    {
        $request->validate([
            'ci' => 'required',
            'nombre' => 'required',
            'apellido' => 'required'
        ]);

        // 1: Verificar que el usuario exista en la base de datos
        $usuario = \App\Models\Usuario::where('ci', $request->ci)->first();
        if (!$usuario) {
            return back()->with('error', 'No existe una cuenta de usuario con el CI ' . $request->ci . '. Por favor, regístrelo primero en el módulo de usuarios.');
        }

        // Buscamos si ya existe el postulante, o lo creamos vinculándolo a su cuenta
        $postulante = Postulante::firstOrCreate(
            ['ci' => $request->ci],
            [
                'id_usuario' => $usuario->id_usuario,
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'estado_habilitacion' => 'Pendiente'
            ]
        );

        if ($postulante->estado_habilitacion === 'Habilitado' || $postulante->estado_habilitacion === 'Inscrito') {
            return back()->with('info', 'El postulante con CI ' . $postulante->ci . ' ya se encuentra habilitado para el registro.');
        }

        // 3: redirigirPasarela(idOrden)
        // Simulamos la pasarela creando un Pago "Pendiente"
        $pago = Pago::create([
            'id_postulante' => $postulante->id_postulante,
            'monto' => 350.00, // Monto inscripción
            'concepto' => 'Pago de Inscripción CUP FICCT',
            'estado_pago' => 'PENDIENTE'
        ]);

        return view('registro.pago.pasarela', compact('pago', 'postulante'));
    }

    // 4: recibirConfirmacion(token) / retornaConfirmacion
    public function confirmarPago(Request $request, $id_pago)
    {
        $pago = Pago::findOrFail($id_pago);
        $postulante = $pago->postulante;

        // Simulamos que recibimos un token de la pasarela
        $tokenSimulado = 'ACID-' . strtoupper(Str::random(10));

        // 5: registrarPago(ACID) / creaRegistroTransaccion
        $pago->update([
            'codigo_comprobante' => $tokenSimulado,
            'estado_pago' => 'COMPLETADO',
            'fecha_pago' => now()
        ]);

        // 6: habilitarParaRegistro() / actualizaHabilitacion
        $postulante->update([
            'estado_habilitacion' => 'Habilitado'
        ]);

        return redirect()->route('registro.pago.exito', $pago->id_pago);
    }

    public function exitoPago($id_pago)
    {
        $pago = Pago::with('postulante')->findOrFail($id_pago);
        return view('registro.pago.exito', compact('pago'));
    }
}
