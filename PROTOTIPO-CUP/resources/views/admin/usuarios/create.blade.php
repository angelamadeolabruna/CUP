@extends('layouts.app')

@section('content')
<nav class="glass-header text-white shadow-lg sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center h-16">
            <a href="{{ route('admin.usuarios.index') }}" class="mr-4 text-sm font-medium hover:text-gray-300"><i class="fas fa-arrow-left"></i> Volver a Usuarios</a>
        </div>
    </div>
</nav>

<div class="max-w-3xl mx-auto px-4 py-10">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-uagrm-blue px-8 py-6 text-white">
            <h1 class="text-2xl font-bold">Crear Nueva Cuenta</h1>
        </div>
        <div class="p-8">
            <form action="{{ route('admin.usuarios.store') }}" method="POST" class="space-y-6">
                @csrf
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nombre</label>
                        <input type="text" name="nombre" required class="mt-1 w-full border border-gray-300 rounded-md p-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Apellido</label>
                        <input type="text" name="apellido" required class="mt-1 w-full border border-gray-300 rounded-md p-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Correo (Email)</label>
                        <input type="email" name="correo" required class="mt-1 w-full border border-gray-300 rounded-md p-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Login (Nombre de Usuario) (txtNombreUsuario)</label>
                        <input type="text" name="nombre_usuario" required class="mt-1 w-full border border-gray-300 rounded-md p-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Contraseña Temporal</label>
                        <input type="password" name="contrasenia" required class="mt-1 w-full border border-gray-300 rounded-md p-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Perfil (selPerfilRol)</label>
                        <select name="id_rol" required class="mt-1 w-full border border-gray-300 rounded-md p-2">
                            @foreach($roles as $rol)
                                <option value="{{ $rol->id_rol }}">{{ $rol->nombre_rol }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Estado de la Cuenta</label>
                        <select name="estado" required class="mt-1 w-full border border-gray-300 rounded-md p-2">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end pt-4 border-t">
                    <button type="submit" class="bg-uagrm-red text-white font-bold py-2 px-6 rounded-lg shadow hover:bg-red-800">
                        Crear Cuenta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
