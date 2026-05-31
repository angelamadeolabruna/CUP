<?php

use App\Http\Controllers\Seguridad\LoginController;
use Illuminate\Support\Facades\Route;

// CU01: Iniciar y Cerrar Sesión
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// CU03: Recuperar Contraseña
Route::get('/recuperar-password', [LoginController::class, 'showRecuperacionForm'])->name('password.request');
Route::post('/recuperar-password', [LoginController::class, 'solicitarEnlaceRecuperacion'])->name('password.email');
Route::get('/reset-password/{token}', [LoginController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [LoginController::class, 'validarTokenIngresado'])->name('password.update');

// Rutas protegidas (Requieren autenticación)
Route::middleware('auth')->group(function () {
    // CU29: Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\Logistica\DashboardController::class, 'index'])->name('dashboard');

    // CU04: Cambiar Contraseña Propia
    Route::get('/perfil', [LoginController::class, 'showPerfilForm'])->name('perfil.index');
    Route::post('/perfil/password', [LoginController::class, 'actualizarRegistroCredencial'])->name('perfil.password.update');
    
    // CU05: Gestionar Cuentas y Roles (RBAC) - CTR_Admin
    Route::get('/admin/usuarios', [\App\Http\Controllers\Seguridad\AdminController::class, 'index'])->name('admin.usuarios.index');
    Route::get('/admin/usuarios/crear', [\App\Http\Controllers\Seguridad\AdminController::class, 'create'])->name('admin.usuarios.create');
    Route::post('/admin/usuarios', [\App\Http\Controllers\Seguridad\AdminController::class, 'store'])->name('admin.usuarios.store');
    
    // CU31: Importar Cuentas
    Route::get('/admin/usuarios/importar', [\App\Http\Controllers\Seguridad\AdminController::class, 'showImportForm'])->name('admin.usuarios.importar');
    Route::post('/admin/usuarios/importar', [\App\Http\Controllers\Seguridad\AdminController::class, 'procesarImportacion'])->name('admin.usuarios.importar.post');

    // CU07: Procesar Pago de Inscripción (Paquete 2)
    Route::get('/registro/pago', [\App\Http\Controllers\Registro\TransaccionController::class, 'showPagoForm'])->name('registro.pago.index');
    Route::post('/registro/pago/procesar', [\App\Http\Controllers\Registro\TransaccionController::class, 'procesarPago'])->name('registro.pago.procesar');
    Route::post('/registro/pago/confirmar/{id_pago}', [\App\Http\Controllers\Registro\TransaccionController::class, 'confirmarPago'])->name('registro.pago.confirmar');
    // CU08: Registrar Postulante (Paquete 2)
    Route::get('/registro/postulante', [\App\Http\Controllers\Registro\PostulanteController::class, 'showRegistroForm'])->name('registro.postulante.index');
    Route::post('/registro/postulante', [\App\Http\Controllers\Registro\PostulanteController::class, 'registrarPostulante'])->name('registro.postulante.registrar');
    Route::get('/registro/postulante/exito/{id}', [\App\Http\Controllers\Registro\PostulanteController::class, 'exitoRegistro'])->name('registro.postulante.exito');
    Route::get('/registro/pago/exito/{id_pago}', [\App\Http\Controllers\Registro\TransaccionController::class, 'exitoPago'])->name('registro.pago.exito');

    // CU19 y CU20: Logística (Paquete 3)
    Route::get('/logistica', [\App\Http\Controllers\Logistica\LogisticaController::class, 'index'])->name('logistica.index');
    Route::post('/logistica/grupos/generar', [\App\Http\Controllers\Logistica\LogisticaController::class, 'generarGruposMasivos'])->name('logistica.grupos.generar');
    Route::get('/logistica/grupos/{grupo}/editar', [\App\Http\Controllers\Logistica\LogisticaController::class, 'editGrupo'])->name('logistica.grupos.edit');
    Route::put('/logistica/grupos/{grupo}', [\App\Http\Controllers\Logistica\LogisticaController::class, 'updateGrupo'])->name('logistica.grupos.update');
    Route::post('/logistica/postulantes/asignar', [\App\Http\Controllers\Logistica\LogisticaController::class, 'asignarPostulantes'])->name('logistica.postulantes.asignar');

    // CU21 y CU22: Gestión de Docentes (Paquete 4)
    Route::get('/docentes/registro', [\App\Http\Controllers\Docente\DocenteController::class, 'index'])->name('docente.registro');
    Route::post('/docentes/registro', [\App\Http\Controllers\Docente\DocenteController::class, 'registrar'])->name('docente.registrar');
    
    Route::get('/docentes/asignar', [\App\Http\Controllers\Docente\DocenteController::class, 'asignar'])->name('docente.asignar');
    Route::post('/docentes/asignar', [\App\Http\Controllers\Docente\DocenteController::class, 'guardarAsignacion'])->name('docente.asignar.guardar');

    // CU13: Evaluación Académica (Paquete 5)
    Route::get('/evaluacion/notas', [\App\Http\Controllers\Academico\AcademicoController::class, 'mostrarListaInscritos'])->name('academico.notas.index');
    Route::post('/evaluacion/notas', [\App\Http\Controllers\Academico\AcademicoController::class, 'capturarCalificaciones'])->name('academico.notas.guardar');

    Route::get('/admin/usuarios/{usuario}/editar', [\App\Http\Controllers\Seguridad\AdminController::class, 'edit'])->name('admin.usuarios.edit');
    Route::put('/admin/usuarios/{usuario}', [\App\Http\Controllers\Seguridad\AdminController::class, 'update'])->name('admin.usuarios.update');

    Route::get('/admin/roles', [\App\Http\Controllers\Seguridad\AdminController::class, 'rolesIndex'])->name('admin.roles.index');
    Route::get('/admin/roles/crear', [\App\Http\Controllers\Seguridad\AdminController::class, 'rolesCreate'])->name('admin.roles.create');
    Route::post('/admin/roles', [\App\Http\Controllers\Seguridad\AdminController::class, 'rolesStore'])->name('admin.roles.store');
    Route::get('/admin/roles/{rol}/editar', [\App\Http\Controllers\Seguridad\AdminController::class, 'rolesEdit'])->name('admin.roles.edit');
    Route::put('/admin/roles/{rol}', [\App\Http\Controllers\Seguridad\AdminController::class, 'rolesUpdate'])->name('admin.roles.update');

    // CU06: Validar Requisitos en Ventanilla
    Route::post('/dashboard/validar', [\App\Http\Controllers\Logistica\DashboardController::class, 'iniciarValidacion'])->name('simular.validacion');
});

// CU07: Pasarela de Pago (PayPal Simulator)
// Esta ruta la ponemos fuera del auth porque el postulante la abre desde su correo (no tiene cuenta aún)
Route::get('/pagos/paypal', function () {
    $postulante = session('postulante_temporal', ['ci' => 'No definido', 'email' => 'estudiante@correo.com']);
    return view('inscripcion.paypal', compact('postulante'));
})->name('paypal.mock');
