@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <i class="fas fa-credit-card text-5xl text-gray-700 mb-4"></i>
        <h2 class="text-center text-3xl font-extrabold text-gray-900">Pasarela de Pagos (Simulación)</h2>
        <p class="mt-2 text-center text-sm text-gray-600">
            Estás a punto de pagar la inscripción al CUP.
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10 border-t-4 border-uagrm-red">
            <div class="mb-6">
                <h3 class="text-lg font-bold border-b pb-2">Resumen de la Orden</h3>
                <ul class="mt-4 space-y-2 text-sm">
                    <li><span class="font-bold">Postulante:</span> {{ $postulante->nombre }} {{ $postulante->apellido }}</li>
                    <li><span class="font-bold">CI:</span> {{ $postulante->ci }}</li>
                    <li><span class="font-bold">Concepto:</span> {{ $pago->concepto }}</li>
                    <li><span class="font-bold text-lg text-uagrm-blue">Monto a Pagar: Bs. {{ $pago->monto }}</span></li>
                </ul>
            </div>

            <form class="space-y-6" action="{{ route('registro.pago.confirmar', $pago->id_pago) }}" method="POST">
                @csrf
                <div class="bg-gray-100 p-4 rounded text-center">
                    <p class="text-xs text-gray-500 mb-4">Al hacer clic en el botón inferior, se simulará una transacción exitosa y la pasarela retornará el Token de Confirmación al sistema.</p>
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-bold text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all">
                        <i class="fas fa-check-circle mr-2 mt-1"></i> SIMULAR PAGO EXITOSO
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
