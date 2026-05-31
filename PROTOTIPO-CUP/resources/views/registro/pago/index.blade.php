@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <i class="fas fa-file-invoice-dollar text-5xl text-uagrm-blue mb-4"></i>
        <h2 class="text-center text-3xl font-extrabold text-gray-900">CU07: Procesar Pago</h2>
        <p class="mt-2 text-center text-sm text-gray-600">
            Ingresa los datos básicos para iniciar el proceso de pago de inscripción.
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            @if(session('info'))
                <div class="bg-blue-50 border-l-4 border-uagrm-blue text-uagrm-blue p-4 mb-6">
                    {{ session('info') }}
                </div>
            @endif

            <form class="space-y-6" action="{{ route('registro.pago.procesar') }}" method="POST">
                @csrf
                <div>
                    <label for="ci" class="block text-sm font-medium text-gray-700">Carnet de Identidad (CI)</label>
                    <div class="mt-1">
                        <input id="ci" name="ci" type="text" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-uagrm-blue focus:border-uagrm-blue sm:text-sm">
                    </div>
                </div>

                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700">Nombres</label>
                    <div class="mt-1">
                        <input id="nombre" name="nombre" type="text" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-uagrm-blue focus:border-uagrm-blue sm:text-sm">
                    </div>
                </div>

                <div>
                    <label for="apellido" class="block text-sm font-medium text-gray-700">Apellidos</label>
                    <div class="mt-1">
                        <input id="apellido" name="apellido" type="text" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-uagrm-blue focus:border-uagrm-blue sm:text-sm">
                    </div>
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-uagrm-blue hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-uagrm-blue">
                        Ir a la Pasarela de Pago
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
