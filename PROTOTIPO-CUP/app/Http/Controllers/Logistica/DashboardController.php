<?php

namespace App\Http\Controllers\Logistica;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.index');
    }

    public function iniciarValidacion(Request $request)
    {
        // CU06: Operador en ventanilla recibe papeles
        $request->validate([
            'ci' => 'required',
            'email' => 'required|email'
        ]);

        // Guardamos temporalmente en sesión simulando que enviamos un correo
        session(['postulante_temporal' => [
            'ci' => $request->ci,
            'email' => $request->email
        ]]);

        // En lugar de enviar el correo (porque no tenemos SMTP real configurado),
        // Redirigimos directamente a la pantalla de la pasarela de PayPal simulada
        // para que la ingeniera lo vea en vivo.
        return redirect()->route('paypal.mock');
    }
}
