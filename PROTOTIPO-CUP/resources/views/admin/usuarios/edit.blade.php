@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Editar Usuario (CU05)</h1>
        <a href="{{ route('admin.usuarios.index') }}" class="text-gray-500 hover:text-gray-700 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Volver a la lista
        </a>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 sm:p-8">
            <form action="{{ route('admin.usuarios.update', $usuario->id_usuario) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Columna 1 -->
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nombre</label>
                            <input type="text" name="nombre" value="{{ $usuario->nombre }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uagrm-blue focus:border-uagrm-blue sm:text-sm px-4 py-2 border">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Apellido</label>
                            <input type="text" name="apellido" value="{{ $usuario->apellido }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uagrm-blue focus:border-uagrm-blue sm:text-sm px-4 py-2 border">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                            <input type="email" name="correo" value="{{ $usuario->correo ?? $usuario->email }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uagrm-blue focus:border-uagrm-blue sm:text-sm px-4 py-2 border">
                        </div>
                    </div>

                    <!-- Columna 2 -->
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Rol en el Sistema</label>
                            <select name="id_rol" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uagrm-blue focus:border-uagrm-blue sm:text-sm px-4 py-2 border bg-gray-50">
                                <option value="">Seleccione un Rol</option>
                                @foreach($roles as $rol)
                                    <option value="{{ $rol->id_rol }}" {{ $usuario->id_rol == $rol->id_rol ? 'selected' : '' }}>
                                        {{ $rol->nombre_rol }} (Nivel: {{ $rol->jerarquia_nivel }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Estado de la Cuenta</label>
                            <select name="estado" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uagrm-blue focus:border-uagrm-blue sm:text-sm px-4 py-2 border bg-gray-50">
                                <option value="1" {{ $usuario->estado ? 'selected' : '' }}>Activo (Permitir Acceso)</option>
                                <option value="0" {{ !$usuario->estado ? 'selected' : '' }}>Inactivo (Bloqueado)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nueva Contraseña <span class="text-gray-400 font-normal">(Opcional, dejar en blanco para mantener)</span></label>
                            <input type="password" name="contrasenia" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uagrm-blue focus:border-uagrm-blue sm:text-sm px-4 py-2 border">
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100 flex justify-end">
                    <button type="submit" class="bg-uagrm-blue hover:bg-blue-800 text-white font-bold py-2 px-8 rounded-lg transition-colors shadow">
                        <i class="fas fa-save mr-2"></i> Actualizar Cuenta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
