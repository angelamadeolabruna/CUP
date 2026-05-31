@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <i class="fas fa-user-graduate text-5xl text-uagrm-blue mb-4"></i>
        <h2 class="text-center text-3xl font-extrabold text-gray-900">CU08: Registrar Postulante</h2>
        <p class="mt-2 text-center text-sm text-gray-600">
            Ingresa tu CI validado para completar tu expediente y elegir tus carreras.
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow-xl sm:rounded-lg sm:px-10 border-t-4 border-uagrm-red">
            
            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6">
                    <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
                </div>
            @endif

            <form class="space-y-6" action="{{ route('registro.postulante.registrar') }}" method="POST">
                @csrf
                
                <div>
                    <label for="ci" class="block text-sm font-medium text-gray-700">Carnet de Identidad (CI Pagado)</label>
                    <div class="mt-1">
                        <input id="ci" name="ci" type="text" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-uagrm-blue focus:border-uagrm-blue sm:text-sm">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Se validará tu pago (CU07) y unicidad con este CI.</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="nombres" class="block text-sm font-medium text-gray-700">Nombres</label>
                        <div class="mt-1">
                            <input id="nombres" name="nombres" type="text" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-uagrm-blue focus:border-uagrm-blue sm:text-sm">
                        </div>
                    </div>
                    <div>
                        <label for="apellidos" class="block text-sm font-medium text-gray-700">Apellidos</label>
                        <div class="mt-1">
                            <input id="apellidos" name="apellidos" type="text" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-uagrm-blue focus:border-uagrm-blue sm:text-sm">
                        </div>
                    </div>
                </div>

                <div>
                    <label for="colegio" class="block text-sm font-medium text-gray-700">Colegio de Origen</label>
                    <div class="mt-1">
                        <input id="colegio" name="colegio" type="text" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-uagrm-blue focus:border-uagrm-blue sm:text-sm">
                    </div>
                </div>

                <div>
                    <label for="carrera_1" class="block text-sm font-medium text-gray-700">Carrera 1ra Opción</label>
                    <select id="carrera_1" name="carrera_1" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-uagrm-blue focus:border-uagrm-blue sm:text-sm rounded-md">
                        <option value="">Seleccione una carrera...</option>
                        @foreach($carreras as $carrera)
                            <option value="{{ $carrera->id_carrera }}">{{ $carrera->nombre_carrera }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="carrera_2" class="block text-sm font-medium text-gray-700">Carrera 2da Opción</label>
                    <select id="carrera_2" name="carrera_2" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-uagrm-blue focus:border-uagrm-blue sm:text-sm rounded-md">
                        <option value="">Seleccione una carrera...</option>
                        @foreach($carreras as $carrera)
                            <option value="{{ $carrera->id_carrera }}">{{ $carrera->nombre_carrera }}</option>
                        @endforeach
                    </select>
                </div>

                @if($errors->any())
                    <div class="text-red-500 text-sm">
                        @foreach ($errors->all() as $error)
                            <p><i class="fas fa-times-circle"></i> {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <div>
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-uagrm-red hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-uagrm-red transition-all">
                        <i class="fas fa-save mr-2 mt-1"></i> Confirmar Registro
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
