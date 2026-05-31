@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-4xl text-center">
        <i class="fas fa-file-signature text-5xl text-uagrm-blue mb-4"></i>
        <h2 class="text-center text-3xl font-extrabold text-gray-900">Registrar Notas (CU13)</h2>
        <p class="mt-2 text-center text-sm text-gray-600">
            Formulario de evaluación para docentes
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-4xl">
        <div class="bg-white py-8 px-4 shadow-xl sm:rounded-lg sm:px-10 border-t-4 border-uagrm-blue mb-8">
            
            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-5 mb-6 shadow-sm">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-2xl mr-3 text-red-500"></i>
                        <div>
                            <h3 class="font-bold">Error de Validación</h3>
                            <p class="text-sm">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 shadow-sm">
                    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                </div>
            @endif

            <!-- Filtro de Grupo (1: seleccionarPostulante) -->
            <form action="{{ route('academico.notas.index') }}" method="GET" class="mb-8 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <div class="flex items-end gap-4">
                    <div class="flex-1">
                        <label for="grupo_id" class="block text-sm font-medium text-gray-700">Seleccione su Grupo Asignado</label>
                        <select id="grupo_id" name="grupo_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-uagrm-blue focus:border-uagrm-blue sm:text-sm rounded-md shadow-sm">
                            <option value="">-- Ver todos o seleccione un grupo --</option>
                            @foreach($grupos as $grupo)
                                <option value="{{ $grupo->id_grupo }}" {{ $grupoSeleccionado == $grupo->id_grupo ? 'selected' : '' }}>
                                    {{ $grupo->nombre_grupo }} - {{ $grupo->materia_asociada ?? 'Sin materia asignada' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition">Filtrar</button>
                </div>
            </form>

            <hr class="mb-8">

            <form class="space-y-6" action="{{ route('academico.notas.guardar') }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Postulante (2: mostrarFormularioNotas) -->
                    <div>
                        <label for="ci_postulante" class="block text-sm font-medium text-gray-700">Seleccionar Alumno</label>
                        <select id="ci_postulante" name="ci_postulante" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-uagrm-blue focus:border-uagrm-blue sm:text-sm rounded-md shadow-sm">
                            <option value="">-- Elija al postulante --</option>
                            @forelse($postulantes as $postulante)
                                <option value="{{ $postulante->ci }}">
                                    {{ $postulante->nombre }} {{ $postulante->apellido }} 
                                    (CI: {{ $postulante->ci }}) 
                                    - Prom: {{ $postulante->promedio_final !== null ? number_format($postulante->promedio_final, 2) : 'N/A' }}
                                    - Estado: {{ $postulante->estado ?? 'Pendiente' }}
                                </option>
                            @empty
                                <option value="" disabled>Seleccione un grupo primero o no hay inscritos</option>
                            @endforelse
                        </select>
                    </div>

                    <!-- Nro Examen -->
                    <div>
                        <label for="nro_examen" class="block text-sm font-medium text-gray-700">Número de Examen</label>
                        <select id="nro_examen" name="nro_examen" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-uagrm-blue focus:border-uagrm-blue sm:text-sm rounded-md shadow-sm">
                            <option value="1">Primer Examen</option>
                            <option value="2">Segundo Examen</option>
                            <option value="3">Tercer Examen</option>
                        </select>
                    </div>
                </div>

                <div class="bg-blue-50 p-4 rounded-lg mt-6 border border-blue-100">
                    <h3 class="text-sm font-bold text-blue-800 mb-4 uppercase">Calificaciones (0-100)</h3>
                    <p class="text-xs text-blue-600 mb-4">Complete únicamente la nota de la materia que usted imparte. El sistema validará que el rango esté entre 0 y 100.</p>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- IU_Evaluacion: inputComputacion -->
                        <div>
                            <label for="inputComputacion" class="block text-sm font-medium text-gray-700">Computación</label>
                            <input type="number" id="inputComputacion" name="inputComputacion" min="0" max="100" step="0.01" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-uagrm-blue focus:border-uagrm-blue sm:text-sm" placeholder="Ej. 85.5">
                        </div>
                        
                        <!-- IU_Evaluacion: inputMatematica -->
                        <div>
                            <label for="inputMatematica" class="block text-sm font-medium text-gray-700">Matemáticas</label>
                            <input type="number" id="inputMatematica" name="inputMatematica" min="0" max="100" step="0.01" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-uagrm-blue focus:border-uagrm-blue sm:text-sm" placeholder="Ej. 90.0">
                        </div>

                        <!-- IU_Evaluacion: inputIngles -->
                        <div>
                            <label for="inputIngles" class="block text-sm font-medium text-gray-700">Inglés</label>
                            <input type="number" id="inputIngles" name="inputIngles" min="0" max="100" step="0.01" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-uagrm-blue focus:border-uagrm-blue sm:text-sm" placeholder="Ej. 100">
                        </div>

                        <!-- IU_Evaluacion: inputFisica -->
                        <div>
                            <label for="inputFisica" class="block text-sm font-medium text-gray-700">Física</label>
                            <input type="number" id="inputFisica" name="inputFisica" min="0" max="100" step="0.01" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-uagrm-blue focus:border-uagrm-blue sm:text-sm" placeholder="Ej. 75">
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <label for="gestion" class="block text-sm font-medium text-gray-700">Gestión Académica</label>
                    <input id="gestion" name="gestion" type="text" value="1/2026" readonly class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 bg-gray-100 rounded-md shadow-sm focus:outline-none focus:ring-uagrm-blue focus:border-uagrm-blue sm:text-sm">
                </div>

                @if($errors->any())
                    <div class="text-red-500 text-sm mt-2">
                        @foreach ($errors->all() as $error)
                            <p><i class="fas fa-times-circle"></i> {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <div class="pt-4">
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-bold text-white bg-uagrm-blue hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-uagrm-blue transition-all">
                        <i class="fas fa-save mr-2 mt-1"></i> Guardar Calificaciones (CU13)
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabla de Reporte: Notas Subidas -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-bold text-gray-900">Historial de Notas Registradas (CE_Nota)</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Postulante</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Materia</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Nro Examen</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Puntaje</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Gestión</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($notasRegistradas as $nota)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">{{ $nota->postulante->nombre ?? 'N/A' }} {{ $nota->postulante->apellido ?? '' }}</div>
                                <div class="text-xs text-gray-500">CI: {{ $nota->ci_postulante }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-medium">
                                {{ $nota->materia }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                Examen {{ $nota->nro_examen }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full {{ $nota->valor_puntaje >= 51 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ number_format($nota->valor_puntaje, 2) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $nota->gestion }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">Aún no hay calificaciones registradas.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
