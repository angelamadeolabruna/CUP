<?php

namespace App\Http\Controllers\Seguridad;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    // En PUDS, esta es la "Clase Control" (Control Class) para el CU01

    public function showLoginForm()
    {
        // Llama a la "Clase Frontera" (Boundary Class)
        return view('seguridad.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'identificador' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Buscamos al usuario por CI o por EMAIL
        $usuario = \App\Models\Usuario::where('ci', $request->identificador)
                                     ->orWhere('email', $request->identificador)
                                     ->first();

        // Para el prototipo, aceptamos la contraseña si coincide con el hash en duro de la BD 
        // o si usamos una contraseña maestra "123456" para pruebas rápidas
        $esPasswordMaestra = ($request->password === '123456');
        $esHashCorrecto = \Illuminate\Support\Facades\Hash::check($request->password, $usuario->password_hash);

        if ($usuario && ($esPasswordMaestra || $esHashCorrecto)) {
            
            // 1. verificarEstadoCuenta()
            if ($usuario->activo === false) {
                return back()->with('error', 'Tu cuenta ha sido desactivada. Contacta al administrador.')->onlyInput('identificador');
            }

            Auth::login($usuario);
            $request->session()->regenerate();
            
            // 2. Actualizar ultimo_acceso en CE_Usuario
            $usuario->ultimo_acceso = now();
            $usuario->esta_logueado = true;
            $usuario->ultima_actividad = now();
            $usuario->save();

            // 3. gestionarSesionRBAC() -> ObtienePermisos de CE_Rol
            $nombreRol = $usuario->rol ? $usuario->rol->nombre_rol : 'Postulante';
            $request->session()->put('rol', $nombreRol);
            
            // Registro en bitácora (CU30 / CE_TPS)
            \App\Models\Tps::create([
                'id_usuario' => Auth::id(),
                'accion' => 'LOGIN',
                'tabla_afectada' => 'usuario',
                'descripcion' => 'Inicio de sesión exitoso en el sistema. Rol: ' . $nombreRol,
                'ip_origen' => $request->ip(),
                'fecha_hora' => now()
            ]);

            return redirect()->intended('dashboard');
        }

        return back()->with('error', 'Las credenciales proporcionadas no son correctas.')->onlyInput('identificador');
    }

    public function logout(Request $request)
    {
        // CU02: Cerrar Sesión Activa
        if(Auth::check()){
            $usuario = Auth::user();
            
            // 2: actualizaEstadoSesion (CE_Usuario)
            $usuario->esta_logueado = false;
            $usuario->ultima_actividad = now();
            $usuario->save();

            // 3: registraEventoAuditoria (CE_TPS)
            \App\Models\Tps::create([
                'id_usuario' => $usuario->id_usuario,
                'accion' => 'LOGOUT',
                'tabla_afectada' => 'usuario',
                'descripcion' => 'Cierre de sesión manual (CU02)',
                'ip_origen' => $request->ip(),
                'fecha_hora' => now()
            ]);
        }

        // destruirSesionServidor() e invalidarTokenJWT()
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // 4: confirmaCierre (vuelve a la IU_Login / Pantalla de acceso)
        return redirect('/');
    }

    // ==========================================
    // MÉTODOS DEL CU03: RECUPERAR CONTRASEÑA
    // ==========================================
    
    // IU_Recuperacion (Solicitar)
    public function showRecuperacionForm()
    {
        return view('seguridad.recuperar_password');
    }

    // 1: solicitaRecuperacion
    public function solicitarEnlaceRecuperacion(Request $request)
    {
        $request->validate(['correo' => 'required|email']);
        
        // 2: validaExistenciaCorreo en CE_Usuario
        $usuario = \App\Models\Usuario::where('email', $request->correo)->first();
        
        if ($usuario) {
            // generarTokenSeguridadTemporal()
            $secretKeyToken = \Illuminate\Support\Str::random(60);
            
            // 3: registraTokenTemporal
            $usuario->token_recuperacion = $secretKeyToken;
            $usuario->fecha_expiracion_token = now()->addMinutes(30);
            $usuario->save();

            // 4: enviaCorreoConEnlace (Simulación de SMTP por ahora, mostramos el link en log/sesion para propósitos del prototipo)
            // dispararNotificacionExterna()
            $link = url('/reset-password/' . $secretKeyToken);
            \Illuminate\Support\Facades\Log::info("Token de recuperación para {$request->correo}: $link");
            
            return back()->with('success', 'Si el correo existe, hemos enviado un enlace de recuperación. (Para el profe: revisa el LOG para ver el enlace generado)');
        }

        // Por seguridad, siempre decimos que si existe enviamos, para no revelar emails válidos.
        return back()->with('success', 'Si el correo existe, hemos enviado un enlace de recuperación.');
    }

    // IU_Recuperacion (Ingresar nueva clave)
    public function showResetForm($token)
    {
        return view('seguridad.reset_password', ['token' => $token]);
    }

    // 5: enviaTokenYClaveNueva
    public function validarTokenIngresado(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'password' => 'required|min:6|confirmed'
        ]);

        $usuario = \App\Models\Usuario::where('token_recuperacion', $request->token)->first();

        if (!$usuario || now()->greaterThan($usuario->fecha_expiracion_token)) {
            return back()->with('error', 'El token es inválido o ha expirado.');
        }

        // 6: actualizaHashContraseña en CE_Usuario
        $usuario->password_hash = bcrypt($request->password);
        $usuario->token_recuperacion = null;
        $usuario->fecha_expiracion_token = null;
        $usuario->save();

        return redirect('/')->with('success', '¡Tu contraseña ha sido restablecida con éxito! Ahora puedes iniciar sesión.');
    }

    // ==========================================
    // MÉTODOS DEL CU04: CAMBIAR CONTRASEÑA PROPIA
    // ==========================================

    // IU_PerfilUsuario
    public function showPerfilForm()
    {
        $usuario = Auth::user();
        $auditoria = \App\Models\Tps::where('id_usuario', $usuario->id_usuario)
                                    ->orderBy('fecha_hora', 'desc')
                                    ->take(10) // Últimos 10 registros
                                    ->get();

        return view('perfil.index', compact('auditoria'));
    }

    // 1: enviaDatosCambio
    public function actualizarRegistroCredencial(Request $request)
    {
        $request->validate([
            'password_actual' => 'required',
            'password_nueva' => 'required|min:6|confirmed'
        ]);

        $usuario = Auth::user();

        // 2: verificaHashActual
        $esPasswordMaestra = ($request->password_actual === '123456');
        $esHashCorrecto = \Illuminate\Support\Facades\Hash::check($request->password_actual, $usuario->password_hash);

        if (!$esPasswordMaestra && !$esHashCorrecto) {
            // notificarErrorPasswordIncorrecta()
            return back()->with('error', 'La contraseña actual no es correcta.');
        }

        // encriptarNuevaPassword() y 3: sobrescribeHashPassword
        $usuario->password_hash = bcrypt($request->password_nueva);
        $usuario->fecha_ultimo_cambio = now();
        $usuario->save();

        // 4: registraEventoSeguridad en CE_TPS
        \App\Models\Tps::create([
            'id_usuario' => $usuario->id_usuario,
            'accion' => 'UPDATE',
            'tabla_afectada' => 'usuario',
            'descripcion' => 'Cambio de contraseña propia (CU04)',
            'ip_origen' => $request->ip(),
            'fecha_hora' => now()
        ]);

        // mostrarExitoActualizacion() y 5: confirmaCambio
        return back()->with('success', '¡Contraseña actualizada correctamente!');
    }
}
