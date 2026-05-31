@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Editar Rol: {{ $rol->nombre_rol }}</h1>
        <a href="{{ route('admin.roles.index') }}" class="text-gray-500 hover:text-gray-700 flex items-center">
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
            <form action="{{ route('admin.roles.update', $rol->id_rol) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nombre del Rol</label>
                        <input type="text" name="nombre_rol" value="{{ $rol->nombre_rol }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uagrm-blue focus:border-uagrm-blue sm:text-sm px-4 py-2 border uppercase">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Descripción</label>
                        <input type="text" name="descripcion" value="{{ $rol->descripcion }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uagrm-blue focus:border-uagrm-blue sm:text-sm px-4 py-2 border">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nivel Jerárquico</label>
                        <input type="number" name="jerarquia_nivel" value="{{ $rol->jerarquia_nivel }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uagrm-blue focus:border-uagrm-blue sm:text-sm px-4 py-2 border">
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex justify-end">
                        <button type="submit" class="bg-uagrm-blue hover:bg-blue-800 text-white font-bold py-2 px-6 rounded-lg transition-colors shadow">
                            Actualizar Rol
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
