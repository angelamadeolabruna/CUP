<?php

namespace App\Http\Controllers\Seguridad;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Rol;
use App\Models\Tps;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    // ==========================================
    // MÉTODOS DEL CU05: GESTIONAR CUENTAS Y ROLES (RBAC)
    // CTR_Admin
    // ==========================================


    // IU_Usuarios: mostrarPanelAdministracion()
    public function index()
    {
        $usuarios = Usuario::with('rol')->get();
        return view('admin.usuarios.index', compact('usuarios'));
    }

    // IU_Usuarios: Formulario para crear (capturarDatosPerfil)
    public function create()
    {
        $roles = Rol::all();
        return view('admin.usuarios.create', compact('roles'));
    }

    // 1: enviaParametrosPerfil -> crearNuevaCuenta() -> 2: actualizaDatosAcceso
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
            'apellido' => 'required',
            'correo' => 'required|email|unique:usuario,email',
            'nombre_usuario' => 'required|unique:usuario,nombre_usuario',
            'contrasenia' => 'required|min:6',
            'id_rol' => 'required|exists:rol,id_rol',
            'estado' => 'required|boolean'
        ]);

        try {
            // Sincronizar secuencia para evitar conflictos de clave duplicada
            \Illuminate\Support\Facades\DB::statement("SELECT setval('usuario_id_usuario_seq', COALESCE((SELECT MAX(id_usuario) FROM usuario), 0))");

            // CE_Usuario: crear
            $usuario = Usuario::create([
                'ci' => $request->ci,
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'email' => $request->correo,
                'password_hash' => bcrypt($request->contrasenia),
                'id_rol' => $request->id_rol,
                'activo' => $request->estado
            ]);

            // 4: persisteHuellaAuditoria -> CE_TPS
            $this->registrarAccionEnTPS('INSERT', 'usuario', "Usuario Creado: {$usuario->nombre} {$usuario->apellido}");

            // 6: confirmaCambios
            return redirect()->route('admin.usuarios.index')->with('success', 'Cuenta de usuario creada con éxito.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al crear el usuario: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Usuario $usuario)
    {
        $roles = Rol::all();
        return view('admin.usuarios.edit', compact('usuario', 'roles'));
    }

    // vincularUsuarioAPerfil(idUser, idRol) -> actualizar usuario
    public function update(Request $request, Usuario $usuario)
    {
        $request->validate([
            'nombre' => 'required',
            'apellido' => 'required',
            'correo' => 'required|email|unique:usuario,email,'.$usuario->id_usuario.',id_usuario',
            'id_rol' => 'required|exists:rol,id_rol',
            'estado' => 'required|boolean'
        ]);

        $usuario->update([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'email' => $request->correo,
            'id_rol' => $request->id_rol,
            'activo' => $request->estado
        ]);

        if ($request->filled('contrasenia')) {
            $usuario->update(['password_hash' => bcrypt($request->contrasenia)]);
        }

        $this->registrarAccionEnTPS('UPDATE', 'usuario', "Usuario Actualizado: {$usuario->nombre_usuario}");

        return redirect()->route('admin.usuarios.index')->with('success', 'Cuenta de usuario actualizada con éxito.');
    }

    // ==========================================
    // MÉTODOS PARA ROLES (CU05)
    // ==========================================
    
    public function rolesIndex()
    {
        $roles = Rol::all();
        return view('admin.roles.index', compact('roles'));
    }

    public function rolesCreate()
    {
        return view('admin.roles.create');
    }

    // 3: asignaPrivilegiosRBAC -> CE_Rol
    public function rolesStore(Request $request)
    {
        $request->validate([
            'nombre_rol' => 'required|unique:rol,nombre_rol',
            'jerarquia_nivel' => 'required|integer',
            'lista_permisos' => 'nullable|array' // chkPermisos: List
        ]);

        Rol::create([
            'nombre_rol' => $request->nombre_rol,
            'descripcion' => $request->descripcion,
            'jerarquia_nivel' => $request->jerarquia_nivel,
            'lista_permisos' => $request->lista_permisos ?? []
        ]);

        $this->registrarAccionEnTPS('INSERT', 'rol', "Rol Creado: {$request->nombre_rol}");

        return redirect()->route('admin.roles.index')->with('success', 'Rol creado exitosamente.');
    }

    public function rolesEdit(Rol $rol)
    {
        return view('admin.roles.edit', compact('rol'));
    }

    public function rolesUpdate(Request $request, Rol $rol)
    {
        $request->validate([
            'nombre_rol' => 'required|unique:rol,nombre_rol,'.$rol->id_rol.',id_rol',
            'jerarquia_nivel' => 'required|integer',
            'lista_permisos' => 'nullable|array'
        ]);

        $rol->update([
            'nombre_rol' => $request->nombre_rol,
            'descripcion' => $request->descripcion,
            'jerarquia_nivel' => $request->jerarquia_nivel,
            'lista_permisos' => $request->lista_permisos ?? []
        ]);

        $this->registrarAccionEnTPS('UPDATE', 'rol', "Rol Actualizado: {$rol->nombre_rol}");

        return redirect()->route('admin.roles.index')->with('success', 'Rol actualizado exitosamente.');
    }

    // Método encapsulado para la huella de auditoría (4: persisteHuellaAuditoria)
    private function registrarAccionEnTPS($operacion, $tabla, $descripcion)
    {
        Tps::create([
            'id_usuario' => Auth::id(),
            'accion' => $operacion,
            'tabla_afectada' => $tabla,
            'descripcion' => $descripcion,
            'ip_origen' => request()->ip(),
            'fecha_hora' => now()
        ]);
    }

    // ==========================================
    // MÉTODOS DEL CU31: IMPORTACIÓN MASIVA (CTR_Usuario)
    // ==========================================

    public function showImportForm()
    {
        $roles = Rol::all();
        return view('admin.usuarios.importar', compact('roles'));
    }

    // 2: procesarArchivo(file) -> procesarLoteFilas()
    public function procesarImportacion(Request $request)
    {
        $request->validate([
            'archivo_csv' => 'required|file|mimes:csv,txt',
            'id_rol' => 'required|exists:rol,id_rol'
        ]);

        $file = $request->file('archivo_csv');
        $handle = fopen($file->getRealPath(), "r");
        
        // Auto-detectar delimitador (Excel en español usa ; en lugar de ,)
        $firstLine = fgets($handle);
        $delimiter = strpos($firstLine, ';') !== false ? ';' : ',';
        rewind($handle);

        // 3: validarEstructuraColumnas() (Esperamos CI, Nombre, Apellido, Correo)
        $header = fgetcsv($handle, 1000, $delimiter);
        if (!$header || count($header) < 4) {
            return back()->with('error', 'Estructura de columnas inválida. Se espera: CI, Nombre, Apellido, Correo.');
        }

        $exitos = 0;
        $errores = [];
        $fila = 1;

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            while (($data = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
                $fila++;
                $ci = $data[0] ?? null;
                $nombre = $data[1] ?? null;
                $apellido = $data[2] ?? null;
                $correo = $data[3] ?? null;

                if (!$ci || !$correo) {
                    $errores[] = "Fila $fila: Faltan datos obligatorios.";
                    continue;
                }

                // 4: verificarCIDuplicado(ci) -> validarExistencia(ci)
                $existe = Usuario::where('ci', $ci)->orWhere('email', $correo)->exists();
                
                if ($existe) {
                    $errores[] = "Fila $fila: CI ($ci) o Correo ($correo) ya existe.";
                    continue;
                }

                // generarPasswordTemporal()
                $passwordTemporal = \Illuminate\Support\Str::random(8);

                // 6: crearCuentaRBAC(datos, rol) -> persistirLoteUsuarios() -> 5: crearCuentas(PostgreSQL)
                Usuario::create([
                    'ci' => $ci,
                    'nombre' => $nombre,
                    'apellido' => $apellido,
                    'email' => $correo,
                    'password_hash' => bcrypt($passwordTemporal),
                    'id_rol' => $request->id_rol, // 4: asignarPerfilRBAC() en diagrama de clases
                    'activo' => true
                ]);

                $exitos++;
            }
            \Illuminate\Support\Facades\DB::commit();

            $this->registrarAccionEnTPS('INSERT', 'usuario', "Importación masiva exitosa: $exitos cuentas creadas.");
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Ocurrió un error crítico procesando el archivo: ' . $e->getMessage());
        }

        fclose($handle);

        // 6: reportarResultados() / notificarFilasConError()
        if (count($errores) > 0) {
            return back()->with('warning', "Se importaron $exitos registros, pero hubo errores: " . implode(" | ", array_slice($errores, 0, 5)) . (count($errores) > 5 ? '...' : ''));
        }

        return redirect()->route('admin.usuarios.index')->with('success', "Importación masiva finalizada. $exitos cuentas creadas correctamente.");
    }
}
