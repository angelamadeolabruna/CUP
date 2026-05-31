@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-xl shadow-lg">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Restablecer Contraseña
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Ingresa tu nueva contraseña para el sistema.
            </p>
        </div>
        
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <form class="mt-8 space-y-6" action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            
            <div class="rounded-md shadow-sm -space-y-px">
                <div class="mb-4">
                    <label for="password" class="sr-only">Nueva Contraseña</label>
                    <input id="password" name="password" type="password" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-uagrm-blue focus:border-uagrm-blue focus:z-10 sm:text-sm" placeholder="Nueva Contraseña">
                </div>
                <div>
                    <label for="password_confirmation" class="sr-only">Confirmar Contraseña</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-uagrm-blue focus:border-uagrm-blue focus:z-10 sm:text-sm" placeholder="Confirmar Contraseña">
                </div>
            </div>

            <div>
                <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-uagrm-blue hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-uagrm-blue">
                    Actualizar Contraseña
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
