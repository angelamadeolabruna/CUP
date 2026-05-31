@extends('layouts.app')

@section('content')
<nav class="glass-header text-white shadow-lg sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="{{ route('dashboard') }}" class="flex items-center">
                    <div class="bg-white p-2 rounded-full mr-3 shadow-md">
                        <i class="fas fa-university text-2xl text-uagrm-red"></i>
                    </div>
                    <span class="font-display font-bold text-2xl tracking-wide">CUP <span class="font-light">FICCT</span></span>
                </a>
            </div>
            <div class="flex items-center">
                <a href="{{ route('dashboard') }}" class="mr-4 text-sm font-medium hover:text-gray-300">Volver</a>
            </div>
        </div>
    </div>
</nav>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-uagrm-blue px-8 py-6 text-white flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Gestión de Usuarios (CU05)</h1>
                <p class="text-blue-100 mt-1">Administración de Cuentas y Privilegios</p>
            </div>
            <div>
                <a href="{{ route('admin.usuarios.importar') }}" class="bg-green-500 text-white font-bold py-2 px-4 rounded-lg shadow hover:bg-green-600 mr-2">
                    <i class="fas fa-file-upload mr-1"></i> Importar
                </a>
                <a href="{{ route('admin.usuarios.create') }}" class="bg-white text-uagrm-blue font-bold py-2 px-4 rounded-lg shadow hover:bg-gray-100">
                    <i class="fas fa-user-plus mr-1"></i> Nuevo Usuario
                </a>
                <a href="{{ route('admin.roles.index') }}" class="bg-uagrm-red text-white font-bold py-2 px-4 rounded-lg shadow hover:bg-red-800 ml-2">
                    <i class="fas fa-shield-alt mr-1"></i> Roles
                </a>
            </div>
        </div>

        <div class="p-8">
            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Login</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rol Sistema</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nivel Académico</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado Cuenta</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($usuarios as $user)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $user->nombre_usuario }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->nombre }} {{ $user->apellido }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $user->rol->nombre_rol ?? 'Sin Rol' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if(($user->rol->nombre_rol ?? '') != 'POSTULANTE' && ($user->rol->nombre_rol ?? '') != 'Postulante')
                                <span class="text-gray-300 text-xs">- No Aplica -</span>
                            @elseif($user->postulante && $user->postulante->estado_habilitacion == 'Inscrito')
                                <span class="px-2 inline-flex text-xs leading-5 font-bold rounded-full bg-purple-100 text-purple-800">
                                    <i class="fas fa-user-graduate mr-1 mt-0.5"></i> POSTULANTE OFICIAL
                                </span>
                            @else
                                <span class="text-gray-400 italic text-xs">Sin Inscribir</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($user->estado || $user->activo)
                                <span class="text-green-600 font-bold"><i class="fas fa-check-circle"></i> Activo</span>
                            @else
                                <span class="text-red-600 font-bold"><i class="fas fa-times-circle"></i> Inactivo</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.usuarios.edit', $user->id_usuario) }}" class="text-indigo-600 hover:text-indigo-900">Editar</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
