@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-10 px-6 shadow-xl sm:rounded-2xl sm:px-10 border-t-8 border-green-500 text-center transform transition-all">
            <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-green-100 mb-6">
                <i class="fas fa-check-double text-5xl text-green-600"></i>
            </div>
            <h2 class="text-3xl font-extrabold text-gray-900 mb-2">¡Expediente Creado!</h2>
            <p class="text-gray-500 mb-8">El registro fue guardado exitosamente.</p>

            <div class="bg-gray-50 rounded-lg p-6 text-left border border-gray-100 mb-8 space-y-3">
                <p class="text-sm text-gray-600 flex justify-between"><span class="font-bold text-gray-800">CI:</span> <span>{{ $postulante->ci }}</span></p>
                <p class="text-sm text-gray-600 flex justify-between"><span class="font-bold text-gray-800">Postulante:</span> <span>{{ $postulante->nombre }} {{ $postulante->apellido }}</span></p>
                <p class="text-sm text-gray-600 flex justify-between"><span class="font-bold text-gray-800">Colegio:</span> <span>{{ $postulante->colegio_origen }}</span></p>
                <p class="text-sm text-gray-600 flex justify-between"><span class="font-bold text-gray-800">Opción 1:</span> <span>{{ $carrera1 }}</span></p>
                <p class="text-sm text-gray-600 flex justify-between"><span class="font-bold text-gray-800">Opción 2:</span> <span>{{ $carrera2 }}</span></p>
                <hr>
                <p class="text-sm text-gray-600 flex justify-between"><span class="font-bold text-gray-800">Estado Postulante:</span> <span class="text-green-600 font-bold uppercase">{{ $postulante->estado_habilitacion }}</span></p>
                <p class="text-sm text-gray-600 flex justify-between"><span class="font-bold text-gray-800">Ref. Pago:</span> <span class="text-gray-400 text-xs mt-1">{{ substr($postulante->id_referencia_pago, 0, 18) }}...</span></p>
            </div>

            <a href="{{ route('dashboard') }}" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-bold text-white bg-uagrm-blue hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-uagrm-blue transition-colors">
                Finalizar e ir al Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
