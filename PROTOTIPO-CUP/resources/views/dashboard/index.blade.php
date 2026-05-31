@extends('layouts.app')

@section('content')
<!-- Barra de Navegación -->
<nav class="glass-header text-white shadow-lg sticky top-0 z-50 animate-fade-in">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <div class="bg-white p-2 rounded-full mr-3 shadow-md">
                    <i class="fas fa-university text-2xl text-uagrm-red"></i>
                </div>
                <span class="font-display font-bold text-2xl tracking-wide">CUP <span class="font-light">FICCT</span></span>
            </div>
            <div class="flex items-center">
                <a href="{{ route('perfil.index') }}" class="mr-4 text-sm font-medium hover:text-gray-300 transition-colors">
                    <i class="fas fa-user-circle mr-1"></i> Mi Perfil ({{ Auth::user()->nombre ?? 'Operador' }})
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm bg-uagrm-darkred hover:bg-red-900 px-3 py-2 rounded-lg transition-colors">
                        Cerrar Sesión <i class="fas fa-sign-out-alt ml-1"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 animate-slide-up">
    
    <div class="mb-10">
        <h1 class="text-4xl font-display font-bold text-gray-900 tracking-tight">Dashboard Administrativo (CU06)</h1>
        <p class="text-gray-500 mt-2 text-lg">Panel de control centralizado para la validación de postulantes.</p>
    </div>

    <!-- Acciones Rápidas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-8">
        
        <!-- Tarjeta: Gestión de Usuarios (Admin) -->
        @if(Auth::user()->rol && Auth::user()->rol->nombre_rol === 'Administrador')
        <a href="{{ route('admin.usuarios.index') }}" class="group bg-white rounded-2xl shadow-lg p-6 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center space-x-4 mb-4">
                <div class="bg-indigo-100 text-indigo-600 p-4 rounded-full group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                    <i class="fas fa-users-cog text-3xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Gestionar Cuentas</h2>
                    <p class="text-sm text-gray-500">CU05: Roles y Usuarios</p>
                </div>
            </div>
            <p class="text-gray-600 text-sm">Crea nuevas cuentas, asigna perfiles RBAC, revisa bitácoras y administra el sistema.</p>
        </a>
        @endif

        <!-- Tarjeta: Validar Documentos Físicos -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-uagrm-blue opacity-5 rounded-bl-full -mr-10 -mt-10 transition-transform group-hover:scale-110"></div>
            <div class="flex items-center justify-between mb-6 relative z-10">
                <div class="bg-blue-50 p-3 rounded-lg text-uagrm-blue">
                    <i class="fas fa-folder-open text-2xl"></i>
                </div>
                <span class="text-xs font-semibold px-2 py-1 bg-green-100 text-green-800 rounded-full">Operador</span>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Validar Documentos en Físico</h3>
            <p class="text-sm text-gray-500 mb-4">Registra a un postulante cuyos papeles (CI, Libreta, Título) fueron aprobados en ventanilla.</p>
            
            <button onclick="document.getElementById('modalValidar').classList.remove('hidden')" class="w-full text-center bg-white border-2 border-uagrm-blue text-uagrm-blue hover:bg-uagrm-blue hover:text-white font-medium py-2 px-4 rounded-lg transition-colors">
                Iniciar Validación <i class="fas fa-arrow-right ml-1"></i>
            </button>
        </div>

        <!-- Tarjeta: Indicadores -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-red-50 p-3 rounded-lg text-uagrm-red">
                    <i class="fas fa-chart-pie text-2xl"></i>
                </div>
                <span class="text-xs font-semibold px-2 py-1 bg-gray-100 text-gray-800 rounded-full">Estadísticas</span>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Métricas del CUP (CU29)</h3>
            <p class="text-sm text-gray-500 mb-4">Resumen de postulantes registrados, pagos confirmados y avance.</p>
            <div class="mt-2 text-2xl font-bold text-gray-900">0 <span class="text-sm font-normal text-gray-500">Postulantes</span></div>
        </div>

    </div>

</div>

<!-- MODAL: Validar Documentos Físicos -->
<div id="modalValidar" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center backdrop-blur-sm">
    <div class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-md relative">
        <button onclick="document.getElementById('modalValidar').classList.add('hidden')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
            <i class="fas fa-times text-xl"></i>
        </button>
        
        <h2 class="text-2xl font-display font-bold text-gray-900 mb-2">Recepción en Ventanilla</h2>
        <p class="text-sm text-gray-500 mb-6">Confirma que el postulante entregó todos sus documentos físicos correctamente.</p>
        
        <form action="{{ route('simular.validacion') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Carnet de Identidad (CI)</label>
                <input type="text" name="ci" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-uagrm-blue focus:border-uagrm-blue">
            </div>
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico del Postulante</label>
                <input type="email" name="email" required placeholder="ejemplo@correo.com" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-uagrm-blue focus:border-uagrm-blue">
                <p class="text-xs text-gray-500 mt-2"><i class="fas fa-info-circle"></i> Se enviará a este correo el enlace a la pasarela de pagos (PayPal).</p>
            </div>

            <div class="flex items-center mb-6">
                <input type="checkbox" required id="check_docs" class="h-4 w-4 text-uagrm-blue rounded border-gray-300">
                <label for="check_docs" class="ml-2 text-sm text-gray-700">Confirmo que he recibido y verificado los documentos originales.</label>
            </div>

            <button type="submit" class="w-full bg-uagrm-blue text-white font-bold py-3 rounded-lg hover:bg-blue-800 transition-colors">
                Validar y Generar Enlace de Pago
            </button>
        </form>
    </div>
</div>
@endsection
