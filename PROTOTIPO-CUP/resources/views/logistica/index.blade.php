@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-display font-bold text-gray-900"><i class="fas fa-boxes text-uagrm-red mr-2"></i> Logística Organizacional</h1>
            <p class="mt-2 text-sm text-gray-600">CU19: Calcular Cantidad de Grupos Necesarios (Algoritmo CEIL)</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r-lg shadow-sm">
            <p class="text-green-700 font-medium"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</p>
        </div>
    @endif
    @if(session('info'))
        <div class="bg-blue-50 border-l-4 border-uagrm-blue p-4 mb-6 rounded-r-lg shadow-sm">
            <p class="text-uagrm-blue font-medium"><i class="fas fa-info-circle mr-2"></i>{{ session('info') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg shadow-sm">
            <p class="text-red-700 font-medium"><i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3 mb-8">
        <!-- Tarjeta 1: Total Inscritos -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden transform transition hover:scale-105 border-b-4 border-uagrm-blue">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Inscritos</p>
                        <p class="mt-2 text-4xl font-extrabold text-gray-900">{{ $totalInscritos }}</p>
                    </div>
                    <div class="p-3 rounded-full bg-blue-100 text-uagrm-blue">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3 border-t">
                <span class="text-xs text-gray-500">Postulantes con matrícula pagada</span>
            </div>
        </div>

        <!-- Tarjeta 2: Algoritmo CEIL -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden transform transition hover:scale-105 border-b-4 border-uagrm-red">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Grupos Necesarios</p>
                        <p class="mt-2 text-4xl font-extrabold text-uagrm-red">{{ $cantGruposCalculados }}</p>
                    </div>
                    <div class="p-3 rounded-full bg-red-100 text-uagrm-red">
                        <i class="fas fa-calculator fa-2x"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3 border-t">
                <span class="text-xs text-gray-500 font-mono">ceil({{ $totalInscritos }} / 80)</span>
            </div>
        </div>
        <!-- Tarjeta 3: Sin Asignar (CU20) -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden transform transition hover:scale-105 border-b-4 border-orange-500">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Sin Asignar</p>
                        <p class="mt-2 text-4xl font-extrabold text-orange-500">{{ $postulantesSinGrupo }}</p>
                    </div>
                    <div class="p-3 rounded-full bg-orange-100 text-orange-500">
                        <i class="fas fa-user-tag fa-2x"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-3 border-t">
                <span class="text-xs text-gray-500">Pendientes de Asignación Física</span>
            </div>
        </div>
    </div>

    <!-- Generador CU19 y CU20 -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
        <!-- Generador de Aulas CU19 -->
        <div class="bg-white shadow-lg rounded-xl border border-gray-100">
            <div class="px-6 py-5 border-b border-gray-200 bg-gray-50 flex justify-between items-center rounded-t-xl">
                <h3 class="text-lg font-bold text-gray-800"><i class="fas fa-magic text-uagrm-blue mr-2"></i> Crear Grupos (CU19)</h3>
            </div>
            <div class="p-6">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-5">
                    <p class="text-sm text-blue-800"><i class="fas fa-robot mr-1"></i> <strong>Recomendación del Algoritmo CEIL:</strong></p>
                    <p class="text-sm text-blue-700 mt-1">Con <strong>{{ $totalInscritos }}</strong> inscritos y aulas de 80 cupos, se necesitan mínimo <strong>{{ $cantGruposCalculados }}</strong> grupo(s). <span class="font-mono text-xs">(ceil({{ $totalInscritos }}/80))</span></p>
                </div>
                <form action="{{ route('logistica.grupos.generar') }}" method="POST">
                    @csrf
                    <div class="mb-5 text-left">
                        <label class="block text-sm font-medium text-gray-700 mb-1">¿Cuántos grupos desea crear?</label>
                        <input type="number" name="cantidad_grupos" value="{{ $cantGruposCalculados }}" min="1" max="26" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-uagrm-blue focus:border-uagrm-blue">
                        <p class="text-xs text-gray-500 mt-1">Los grupos se crean vacíos. Luego usted configura el Aula, Turno y Horario de cada uno.</p>
                    </div>
                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-bold rounded-lg text-white bg-uagrm-blue hover:bg-blue-800 shadow-md transition-all focus:outline-none w-full">
                        <i class="fas fa-layer-group mr-2"></i> Generar Grupos
                    </button>
                </form>
            </div>
        </div>

        <!-- Asignador de Postulantes CU20 -->
        <div id="cu20" class="bg-white shadow-lg rounded-xl border border-gray-100">
            <div class="px-6 py-5 border-b border-gray-200 bg-gray-50 flex justify-between items-center rounded-t-xl">
                <h3 class="text-lg font-bold text-gray-800"><i class="fas fa-users-cog text-uagrm-red mr-2"></i> Asignación Física (CU20)</h3>
            </div>
            <div class="p-6 text-center">
                <p class="text-gray-600 mb-6">Distribuir automáticamente los <strong>{{ $postulantesSinGrupo }}</strong> alumnos sin grupo respetando el límite de 70.</p>
                <form action="{{ route('logistica.postulantes.asignar') }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-bold rounded-lg text-white bg-uagrm-red hover:bg-red-800 shadow-md transition-all focus:outline-none w-full">
                        <i class="fas fa-user-check mr-2"></i> 2. Distribuir Alumnos (CU20)
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Tabla de Grupos Resultantes -->
    <div class="bg-white shadow-lg rounded-xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 bg-gray-50 rounded-t-xl">
            <h3 class="text-lg font-bold text-gray-800"><i class="fas fa-list text-gray-500 mr-2"></i> Grupos en la Base de Datos</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Nombre</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Turno</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Aula</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Ocupación</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Capacidad</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">Estado</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($reporteGrupos as $grupo)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full bg-blue-100 text-blue-800">
                                {{ $grupo->nombre_grupo }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($grupo->turno == 'MAÑANA')
                                <span class="px-2 py-1 rounded-full bg-yellow-100 text-yellow-800 text-xs font-semibold"><i class="fas fa-sun mr-1"></i>MAÑANA</span>
                            @elseif($grupo->turno == 'TARDE')
                                <span class="px-2 py-1 rounded-full bg-orange-100 text-orange-800 text-xs font-semibold"><i class="fas fa-cloud-sun mr-1"></i>TARDE</span>
                            @else
                                <span class="px-2 py-1 rounded-full bg-indigo-100 text-indigo-800 text-xs font-semibold"><i class="fas fa-moon mr-1"></i>NOCHE</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($grupo->aula === 'Por Asignar')
                                <span class="text-red-500 font-medium"><i class="fas fa-exclamation-circle mr-1"></i>{{ $grupo->aula }}</span>
                            @else
                                <i class="fas fa-door-open mr-1 text-gray-400"></i>{{ $grupo->aula }}
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($grupo->cupo_actual >= 70)
                                <span class="font-bold text-red-600">{{ $grupo->cupo_actual }} / 70</span>
                            @elseif($grupo->cupo_actual > 0)
                                <span class="font-bold text-green-600">{{ $grupo->cupo_actual }} / 70</span>
                            @else
                                <span class="text-gray-400">0 / 70</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $grupo->capacidad_maxima }} cupos</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($grupo->activo)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Activo</span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactivo</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('logistica.grupos.edit', $grupo->id_grupo) }}" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-1 rounded-md hover:bg-indigo-100 transition-colors">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-3 text-gray-300 block"></i>
                            Aún no hay grupos. Use el botón azul para crearlos.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
