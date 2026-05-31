@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-4xl text-center">
        <i class="fas fa-chalkboard text-5xl text-uagrm-red mb-4"></i>
        <h2 class="text-center text-3xl font-extrabold text-gray-900">Asignar Docente a Grupo (CU22)</h2>
        <p class="mt-2 text-center text-sm text-gray-600">
            Registro de Carga Horaria y control de topes por docente.
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-4xl">
        <div class="bg-white py-8 px-4 shadow-xl sm:rounded-lg sm:px-10 border-t-4 border-uagrm-red mb-8">
            
            @if(session('error_limite'))
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-5 mb-6 shadow-sm">
                    <div class="flex items-center">
                        <i class="fas fa-hand-paper text-2xl mr-3 text-red-500"></i>
                        <div>
                            <h3 class="font-bold">Límite de Grupos Excedido (CU22)</h3>
                            <p class="text-sm">{{ session('error_limite') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('error_horario'))
                <div class="bg-orange-50 border-l-4 border-orange-500 text-orange-700 p-5 mb-6 shadow-sm">
                    <div class="flex items-center">
                        <i class="fas fa-clock text-2xl mr-3 text-orange-500"></i>
                        <div>
                            <h3 class="font-bold">Cruce de Horarios Detectado (CU22)</h3>
                            <p class="text-sm">{{ session('error_horario') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 shadow-sm">
                    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                </div>
            @endif

            <form class="space-y-6" action="{{ route('docente.asignar.guardar') }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="id_docente" class="block text-sm font-medium text-gray-700">Seleccionar Docente Habilitado</label>
                        <select id="id_docente" name="id_docente" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-uagrm-red focus:border-uagrm-red sm:text-sm rounded-md shadow-sm">
                            <option value="">-- Elija un docente --</option>
                            @foreach($docentes as $docente)
                                <option value="{{ $docente->id_docente }}">{{ $docente->nombre_completo }} (CI: {{ $docente->ci }}) - {{ $docente->cantidad_grupos_actual }} grupos</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="id_grupo" class="block text-sm font-medium text-gray-700">Seleccionar Grupo Disponible</label>
                        <select id="id_grupo" name="id_grupo" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-uagrm-red focus:border-uagrm-red sm:text-sm rounded-md shadow-sm">
                            <option value="">-- Elija un grupo --</option>
                            @foreach($gruposDisponibles as $grupo)
                                <option value="{{ $grupo->id_grupo }}">{{ $grupo->nombre_grupo }} ({{ $grupo->turno }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="materia_asociada" class="block text-sm font-medium text-gray-700">Materia Asociada</label>
                        <input id="materia_asociada" name="materia_asociada" type="text" required class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-uagrm-red focus:border-uagrm-red sm:text-sm" placeholder="Ej. Matemáticas">
                    </div>

                    <div>
                        <label for="horario_asignado" class="block text-sm font-medium text-gray-700">Horario Asignado</label>
                        <select id="horario_asignado" name="horario_asignado" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-uagrm-red focus:border-uagrm-red sm:text-sm rounded-md shadow-sm">
                            <option value="">-- Elija un horario --</option>
                            <option value="Lunes 07:00 - 09:15">Lunes 07:00 - 09:15</option>
                            <option value="Martes 07:00 - 09:15">Martes 07:00 - 09:15</option>
                            <option value="Miercoles 07:00 - 09:15">Miercoles 07:00 - 09:15</option>
                            <option value="Lunes 09:30 - 11:45">Lunes 09:30 - 11:45</option>
                            <option value="Martes 09:30 - 11:45">Martes 09:30 - 11:45</option>
                            <option value="Viernes 14:00 - 16:15">Viernes 14:00 - 16:15</option>
                        </select>
                    </div>
                </div>
                
                <div>
                    <label for="gestion_academica" class="block text-sm font-medium text-gray-700">Gestión Académica</label>
                    <input id="gestion_academica" name="gestion_academica" type="text" value="1/2026" readonly class="mt-1 appearance-none block w-full px-3 py-2 border border-gray-300 bg-gray-100 rounded-md shadow-sm focus:outline-none focus:ring-uagrm-red focus:border-uagrm-red sm:text-sm">
                </div>

                @if($errors->any())
                    <div class="text-red-500 text-sm">
                        @foreach ($errors->all() as $error)
                            <p><i class="fas fa-times-circle"></i> {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <div>
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-bold text-white bg-uagrm-red hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-uagrm-red transition-all">
                        <i class="fas fa-calendar-check mr-2 mt-1"></i> Asignar Carga Horaria (CU22)
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabla de Reporte -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-bold text-gray-900">Historial de Asignaciones (Carga Horaria)</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-800">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Docente</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Grupo</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Materia</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Horario</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Gestión</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($cargaHoraria as $carga)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">{{ $carga->nombre_completo }}</div>
                                <div class="text-xs text-gray-500">CI: {{ $carga->ci }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $carga->nombre_grupo }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-medium">
                                {{ $carga->materia_asociada }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <i class="fas fa-clock text-gray-400 mr-1"></i> {{ $carga->horario_asignado }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $carga->gestion_academica }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">No hay asignaciones registradas aún.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
