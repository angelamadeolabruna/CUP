@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-uagrm-blue px-8 py-6 text-white flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold"><i class="fas fa-edit mr-2"></i> Editar Grupo (CU19)</h1>
                <p class="text-blue-100 mt-1">Configuración individual de aula y turno</p>
            </div>
            <a href="{{ route('logistica.index') }}" class="bg-white text-uagrm-blue font-bold py-2 px-4 rounded-lg shadow hover:bg-gray-100">
                <i class="fas fa-arrow-left mr-1"></i> Volver
            </a>
        </div>

        <div class="p-8">
            <form action="{{ route('logistica.grupos.update', $grupo->id_grupo) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nombre del Grupo</label>
                        <input type="text" disabled value="{{ $grupo->nombre_grupo }}" class="mt-1 block w-full bg-gray-100 border-gray-300 rounded-md shadow-sm">
                        <p class="text-xs text-gray-500 mt-1">Generado automáticamente por el algoritmo.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Capacidad Máxima</label>
                        <input type="number" name="capacidad_maxima" value="{{ $grupo->capacidad_maxima }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uagrm-blue focus:border-uagrm-blue">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Aula Asignada</label>
                        <input type="text" name="aula" value="{{ $grupo->aula }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uagrm-blue focus:border-uagrm-blue">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Turno</label>
                        <select name="turno" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-uagrm-blue focus:border-uagrm-blue">
                            <option value="MAÑANA" {{ $grupo->turno == 'MAÑANA' ? 'selected' : '' }}>MAÑANA</option>
                            <option value="TARDE" {{ $grupo->turno == 'TARDE' ? 'selected' : '' }}>TARDE</option>
                            <option value="NOCHE" {{ $grupo->turno == 'NOCHE' ? 'selected' : '' }}>NOCHE</option>
                        </select>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="activo" class="form-checkbox h-5 w-5 text-uagrm-blue" value="1" {{ $grupo->activo ? 'checked' : '' }}>
                        <span class="ml-2 text-gray-700 font-medium">Grupo Activo</span>
                    </label>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-uagrm-blue text-white font-bold py-3 px-6 rounded-lg shadow-md hover:bg-blue-800 transition-colors">
                        <i class="fas fa-save mr-2"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
