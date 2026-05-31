@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 animate-slide-up">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-uagrm-blue px-8 py-6 text-white flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Perfil de Usuario</h1>
                <p class="text-blue-100 mt-1">Configuración y seguridad de la cuenta</p>
            </div>
            <i class="fas fa-user-shield text-5xl opacity-80"></i>
        </div>

        <div class="p-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Sección de Datos Personales -->
            <div>
                <h2 class="text-xl font-bold text-gray-800 mb-6 border-b pb-2">Información Personal</h2>
                <div class="space-y-4">
                    <div>
                        <span class="block text-sm font-medium text-gray-500">Nombre Completo</span>
                        <div class="text-lg font-semibold text-gray-800">{{ Auth::user()->nombre }} {{ Auth::user()->apellido }}</div>
                    </div>
                    <div>
                        <span class="block text-sm font-medium text-gray-500">Carnet de Identidad (CI)</span>
                        <div class="text-lg font-semibold text-gray-800">{{ Auth::user()->ci }}</div>
                    </div>
                    <div>
                        <span class="block text-sm font-medium text-gray-500">Correo Electrónico</span>
                        <div class="text-lg font-semibold text-gray-800">{{ Auth::user()->email }}</div>
                    </div>
                    <div>
                        <span class="block text-sm font-medium text-gray-500">Rol en el Sistema</span>
                        <div class="text-lg font-semibold text-gray-800">
                            <span class="px-3 py-1 bg-uagrm-blue text-white rounded-full text-xs uppercase">{{ Auth::user()->rol ? Auth::user()->rol->nombre_rol : 'Sin Rol' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulario de Contraseña -->
            <div>
                <h2 class="text-xl font-bold text-gray-800 mb-6 border-b pb-2">Cambiar Contraseña (CU04)</h2>
            
            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r-lg">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-3 text-xl"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r-lg">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-3 text-xl"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r-lg">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('perfil.password.update') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña Actual</label>
                    <input type="password" name="password_actual" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-uagrm-blue focus:border-uagrm-blue transition-colors">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nueva Contraseña</label>
                        <input type="password" name="password_nueva" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-uagrm-blue focus:border-uagrm-blue transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar Nueva Contraseña</label>
                        <input type="password" name="password_nueva_confirmation" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-uagrm-blue focus:border-uagrm-blue transition-colors">
                    </div>
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="submit" class="bg-uagrm-blue text-white font-bold py-2 px-6 rounded-lg hover:bg-blue-800 transition-colors shadow-md flex items-center w-full justify-center">
                        <i class="fas fa-save mr-2"></i> Actualizar Credenciales
                    </button>
                </div>
            </form>
            </div>
        </div>

        <!-- SECCIÓN DE HUELLA DE AUDITORÍA (TPS) -->
        <div class="p-8 border-t border-gray-100 bg-gray-50">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-history text-uagrm-blue mr-2"></i> Huella de Auditoría (Actividad Reciente)
            </h2>
            <p class="text-sm text-gray-500 mb-6">Este es un registro seguro de todas las acciones que has realizado en el sistema.</p>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha y Hora</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acción</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP de Origen</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($auditoria ?? [] as $tps)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($tps->fecha_hora)->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $tps->accion == 'LOGIN' ? 'bg-green-100 text-green-800' : ($tps->accion == 'LOGOUT' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                                    {{ $tps->accion }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $tps->descripcion }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400 font-mono">
                                {{ $tps->ip_origen }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                No hay actividad reciente registrada.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
