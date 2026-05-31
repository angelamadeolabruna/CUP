@extends('layouts.app')

@section('content')
<nav class="glass-header text-white shadow-lg sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center h-16">
            <a href="{{ route('admin.usuarios.index') }}" class="mr-4 text-sm font-medium hover:text-gray-300"><i class="fas fa-arrow-left"></i> Volver a Usuarios</a>
        </div>
    </div>
</nav>

<div class="max-w-3xl mx-auto px-4 py-10">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-uagrm-blue px-8 py-6 text-white">
            <h1 class="text-2xl font-bold">Importación Masiva de Usuarios (CU31)</h1>
            <p class="text-blue-100 mt-1">Sube un archivo CSV con el formato correcto</p>
        </div>
        <div class="p-8">
            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('warning'))
                <div class="bg-yellow-50 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6">
                    {{ session('warning') }}
                </div>
            @endif

            <div class="mb-6 bg-blue-50 p-4 rounded-lg border border-blue-200">
                <h3 class="font-bold text-uagrm-blue mb-2"><i class="fas fa-info-circle"></i> Formato Requerido (CSV)</h3>
                <p class="text-sm text-gray-700 mb-2">El archivo debe tener las siguientes 4 columnas (separadas por comas) en este orden exacto (la primera fila debe ser la cabecera):</p>
                <code class="block bg-gray-800 text-green-400 p-3 rounded text-sm">CI, Nombre, Apellido, Correo<br>1234567, Juan, Perez, juan@uagrm.edu.bo</code>
            </div>

            <form action="{{ route('admin.usuarios.importar.post') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Seleccionar Perfil/Rol a asignar masivamente</label>
                    <select name="id_rol" required class="w-full border border-gray-300 rounded-md p-2 focus:ring-uagrm-blue focus:border-uagrm-blue">
                        <option value="">Seleccione un Rol...</option>
                        @foreach($roles as $rol)
                            <option value="{{ $rol->id_rol }}">{{ $rol->nombre_rol }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Archivo CSV</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-uagrm-blue transition-colors bg-gray-50">
                        <div class="space-y-1 text-center">
                            <i class="fas fa-file-csv text-4xl text-gray-400 mb-3"></i>
                            <div class="flex text-sm text-gray-600 justify-center">
                                <label for="archivo_csv" class="relative cursor-pointer bg-white rounded-md font-medium text-uagrm-blue hover:text-blue-800 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-uagrm-blue">
                                    <span>Sube un archivo</span>
                                    <input id="archivo_csv" name="archivo_csv" type="file" accept=".csv" class="sr-only" required>
                                </label>
                            </div>
                            <p class="text-xs text-gray-500">CSV hasta 2MB</p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-4 border-t">
                    <button type="submit" class="bg-uagrm-blue text-white font-bold py-2 px-6 rounded-lg shadow hover:bg-blue-800 flex items-center">
                        <i class="fas fa-upload mr-2"></i> Procesar Importación
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('archivo_csv').addEventListener('change', function(e) {
        var fileName = e.target.files[0] ? e.target.files[0].name : 'Sube un archivo';
        var labelSpan = this.previousElementSibling;
        if(e.target.files[0]) {
            labelSpan.textContent = fileName;
            labelSpan.classList.add('text-green-600', 'font-bold');
            labelSpan.classList.remove('text-uagrm-blue');
        } else {
            labelSpan.textContent = 'Sube un archivo';
            labelSpan.classList.add('text-uagrm-blue');
            labelSpan.classList.remove('text-green-600', 'font-bold');
        }
    });
</script>
@endsection
