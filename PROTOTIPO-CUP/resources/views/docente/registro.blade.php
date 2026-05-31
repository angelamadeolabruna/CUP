@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <i class="fas fa-chalkboard-teacher text-5xl text-uagrm-blue mb-4"></i>
        <h2 class="text-center text-3xl font-extrabold text-gray-900">Registro de Docentes (CU21)</h2>
        <p class="mt-2 text-center text-sm text-gray-600">
            Módulo de Contratación de la FICCT.
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-xl">
        <div class="bg-white py-8 px-4 shadow-xl sm:rounded-lg sm:px-10 border-t-4 border-uagrm-blue">
            
            @if(session('error_requisitos'))
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-5 mb-6 shadow-sm">
                    <div class="flex items-center">
                        <i class="fas fa-ban text-2xl mr-3 text-red-500"></i>
                        <div>
                            <h3 class="font-bold">Error de Validación FICCT</h3>
                            <p class="text-sm">{{ session('error_requisitos') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-orange-50 border-l-4 border-orange-500 text-orange-700 p-4 mb-6 shadow-sm">
                    <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 shadow-sm">
                    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                </div>
            @endif

            <form class="space-y-6" action="{{ route('docente.registrar') }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="txtCI" class="block text-sm font-medium text-gray-700">Carnet de Identidad</label>
                        <div class="mt-1">
                            <input id="txtCI" name="txtCI" type="text" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-uagrm-blue focus:border-uagrm-blue sm:text-sm">
                        </div>
                    </div>

                    <div>
                        <label for="txtNombres" class="block text-sm font-medium text-gray-700">Nombre Completo</label>
                        <div class="mt-1">
                            <input id="txtNombres" name="txtNombres" type="text" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-uagrm-blue focus:border-uagrm-blue sm:text-sm">
                        </div>
                    </div>
                </div>

                <div>
                    <label for="txtEspecialidad" class="block text-sm font-medium text-gray-700">Especialidad Principal</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-graduation-cap text-gray-400"></i>
                        </div>
                        <input id="txtEspecialidad" name="txtEspecialidad" type="text" required class="pl-10 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-uagrm-blue focus:border-uagrm-blue sm:text-sm" placeholder="Ej. Ingeniería de Software">
                    </div>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <h4 class="text-sm font-bold text-gray-800 mb-3"><i class="fas fa-check-double text-uagrm-red mr-2"></i>Requisitos Obligatorios FICCT</h4>
                    <p class="text-xs text-gray-500 mb-4">El docente debe poseer al menos uno de los siguientes títulos para ser habilitado en el sistema.</p>
                    
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="chkMaestria" name="chkMaestria" type="checkbox" class="focus:ring-uagrm-blue h-5 w-5 text-uagrm-blue border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="chkMaestria" class="font-medium text-gray-700">Posee título de Maestría</label>
                                <p class="text-gray-500 text-xs">Acreditado por el Ministerio de Educación.</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="chkDiplomado" name="chkDiplomado" type="checkbox" class="focus:ring-uagrm-blue h-5 w-5 text-uagrm-blue border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="chkDiplomado" class="font-medium text-gray-700">Posee título de Diplomado</label>
                                <p class="text-gray-500 text-xs">Especialidad o Educación Superior.</p>
                            </div>
                        </div>
                    </div>
                </div>

                @if($errors->any())
                    <div class="text-red-500 text-sm">
                        @foreach ($errors->all() as $error)
                            <p><i class="fas fa-times-circle"></i> {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <div>
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-bold text-white bg-uagrm-blue hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-uagrm-blue transition-all">
                        <i class="fas fa-save mr-2 mt-1"></i> Verificar y Registrar Docente
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
