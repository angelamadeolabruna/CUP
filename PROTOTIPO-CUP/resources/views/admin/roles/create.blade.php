@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Crear Nuevo Rol (CU05)</h1>
        <a href="{{ route('admin.roles.index') }}" class="text-gray-500 hover:text-gray-700 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Volver a la lista
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 sm:p-8">
            <form action="{{ route('admin.roles.store') }}" method="POST">
                @csrf
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nombre del Rol (Ej: CAJERO)</label>
                        <input type="text" name="nombre_rol" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uagrm-blue focus:border-uagrm-blue sm:text-sm px-4 py-2 border uppercase">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Descripción (Opcional)</label>
                        <input type="text" name="descripcion" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uagrm-blue focus:border-uagrm-blue sm:text-sm px-4 py-2 border">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nivel Jerárquico (Mayor número = Más permisos)</label>
                        <input type="number" name="jerarquia_nivel" value="1" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uagrm-blue focus:border-uagrm-blue sm:text-sm px-4 py-2 border">
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex justify-end">
                        <button type="submit" class="bg-uagrm-blue hover:bg-blue-800 text-white font-bold py-2 px-6 rounded-lg transition-colors shadow">
                            Guardar Rol
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
