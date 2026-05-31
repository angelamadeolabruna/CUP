<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CUP FICCT - Dashboard</title>
    
    <!-- Google Fonts: Poppins (Elegante y muy profesional) -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (via CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Configuración de Tailwind y Animaciones -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        uagrm: { red: '#c8102e', darkred: '#8B0000', blue: '#0033a0', dark: '#111827' }
                    },
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                        display: ['Poppins', 'sans-serif'],
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-out',
                        'slide-up': 'slideUp 0.6s ease-out',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8fafc; }
        .glass-header { background: rgba(200, 16, 46, 0.95); backdrop-filter: blur(8px); }
    </style>
</head>
<body class="bg-slate-50 antialiased text-gray-800 flex h-screen overflow-hidden">

    @auth
        <!-- SIDEBAR -->
        <aside class="w-64 bg-uagrm-dark text-white flex flex-col h-full shadow-2xl z-50">
            <!-- Logo / Header del Sidebar -->
            <div class="h-16 flex items-center px-6 bg-gray-900 border-b border-gray-800">
                <div class="bg-white p-1.5 rounded-full mr-3 shadow-md">
                    <i class="fas fa-university text-lg text-uagrm-red"></i>
                </div>
                <span class="font-display font-bold text-xl tracking-wide">CUP <span class="font-light">FICCT</span></span>
            </div>

            <!-- Menú de Navegación por Paquetes -->
            <div class="flex-1 overflow-y-auto py-4">
                <nav class="px-4 space-y-4">
                    
                    <!-- Dashboard General -->
                    <div>
                        <a href="{{ route('dashboard') }}" class="flex items-center text-gray-300 hover:text-white hover:bg-gray-800 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-gray-800 text-white' : '' }}">
                            <i class="fas fa-home w-6 text-center"></i>
                            <span class="ml-2 font-medium">Dashboard Central</span>
                        </a>
                    </div>

                    <!-- PAQUETE 1: SEGURIDAD -->
                    <div>
                        <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Pqte 1: Seguridad</h3>
                        <div class="space-y-1">
                            <a href="{{ route('perfil.index') }}" class="flex items-center text-sm text-gray-300 hover:text-white hover:bg-gray-800 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('perfil.*') ? 'bg-gray-800 text-white' : '' }}">
                                <i class="fas fa-user-shield w-6 text-center"></i>
                                <span class="ml-2">Mi Perfil (CU04)</span>
                            </a>
                            
                            @if(Auth::user()->rol && in_array(Auth::user()->rol->nombre_rol, ['Administrador', 'ADMIN']))
                                <a href="{{ route('admin.usuarios.index') }}" class="flex items-center text-sm text-gray-300 hover:text-white hover:bg-gray-800 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('admin.usuarios.index') ? 'bg-gray-800 text-white' : '' }}">
                                    <i class="fas fa-users-cog w-6 text-center"></i>
                                    <span class="ml-2">Usuarios y Roles (CU05)</span>
                                </a>
                                <a href="{{ route('admin.usuarios.importar') }}" class="flex items-center text-sm text-gray-300 hover:text-white hover:bg-gray-800 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('admin.usuarios.importar') ? 'bg-gray-800 text-white' : '' }}">
                                    <i class="fas fa-file-csv w-6 text-center"></i>
                                    <span class="ml-2">Importar Cuentas (CU31)</span>
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- PAQUETE 2: REGISTRO Y ADMISIÓN -->
                    <div>
                        <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 mt-4">Pqte 2: Registro</h3>
                        <div class="space-y-1">
                            <a href="{{ route('registro.pago.index') }}" class="flex items-center text-sm text-gray-400 hover:text-white hover:bg-gray-800 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('registro.pago.*') ? 'bg-gray-800 text-white' : '' }}">
                                <i class="fas fa-file-invoice-dollar w-6 text-center"></i>
                                <span class="ml-2">Procesar Pago (CU07)</span>
                            </a>
                            <a href="{{ route('registro.postulante.index') }}" class="flex items-center text-sm text-gray-400 hover:text-white hover:bg-gray-800 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('registro.postulante.*') ? 'bg-gray-800 text-white' : '' }}">
                                <i class="fas fa-user-graduate w-6 text-center"></i>
                                <span class="ml-2">Completar Registro (CU08)</span>
                            </a>
                        </div>
                    </div>

                    <!-- PAQUETE 3: LOGÍSTICA ORGANIZACIONAL -->
                    @if(Auth::user()->rol && in_array(Auth::user()->rol->nombre_rol, ['Administrador', 'ADMIN']))
                    <div>
                        <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 mt-4">Pqte 3: Logística</h3>
                        <div class="space-y-1">
                            <a href="{{ route('logistica.index') }}" class="flex items-center text-sm text-gray-400 hover:text-white hover:bg-gray-800 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('logistica.*') ? 'bg-gray-800 text-white' : '' }}">
                                <i class="fas fa-boxes w-6 text-center"></i>
                                <span class="ml-2">Cálculo de Grupos (CU19)</span>
                            </a>
                            <a href="{{ route('logistica.index') }}" class="flex items-center text-sm text-gray-400 hover:text-white hover:bg-gray-800 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('logistica.*') ? 'bg-gray-800 text-white' : '' }}">
                                <i class="fas fa-user-tag w-6 text-center"></i>
                                <span class="ml-2">Asignar Postulantes (CU20)</span>
                            </a>
                        </div>
                    </div>
                    @endif

                    <!-- PAQUETE 4: GESTIÓN DE DOCENTES -->
                    @if(Auth::user()->rol && in_array(Auth::user()->rol->nombre_rol, ['Administrador', 'ADMIN']))
                    <div>
                        <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 mt-4">Pqte 4: Docentes</h3>
                        <div class="space-y-1">
                            <a href="{{ route('docente.registro') }}" class="flex items-center text-sm text-gray-400 hover:text-white hover:bg-gray-800 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('docente.registro') ? 'bg-gray-800 text-white' : '' }}">
                                <i class="fas fa-chalkboard-teacher w-6 text-center"></i>
                                <span class="ml-2">Registrar Docente (CU21)</span>
                            </a>
                            <a href="{{ route('docente.asignar') }}" class="flex items-center text-sm text-gray-400 hover:text-white hover:bg-gray-800 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('docente.asignar') ? 'bg-gray-800 text-white' : '' }}">
                                <i class="fas fa-chalkboard w-6 text-center"></i>
                                <span class="ml-2">Asignar Docente (CU22)</span>
                            </a>
                        </div>
                    </div>
                    @endif

                    <!-- PAQUETE 5: EVALUACIÓN ACADÉMICA -->
                    @if(Auth::user()->rol && in_array(Auth::user()->rol->nombre_rol, ['Administrador', 'ADMIN', 'Docente', 'DOCENTE']))
                    <div>
                        <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 mt-4">Pqte 5: Evaluación</h3>
                        <div class="space-y-1">
                            <a href="{{ route('academico.notas.index') }}" class="flex items-center text-sm text-gray-400 hover:text-white hover:bg-gray-800 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('academico.notas.*') ? 'bg-gray-800 text-white' : '' }}">
                                <i class="fas fa-file-signature w-6 text-center"></i>
                                <span class="ml-2">Registrar Notas (CU13)</span>
                            </a>
                        </div>
                    </div>
                    @endif

                </nav>
            </div>

            <!-- Footer del Sidebar (Usuario y Logout) -->
            <div class="p-4 border-t border-gray-800 bg-gray-900">
                <div class="flex items-center mb-3">
                    <div class="h-8 w-8 rounded-full bg-uagrm-red flex items-center justify-center text-white font-bold">
                        {{ substr(Auth::user()->nombre, 0, 1) }}
                    </div>
                    <div class="ml-3 truncate">
                        <p class="text-sm font-medium text-white truncate">{{ Auth::user()->nombre_usuario }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ Auth::user()->rol->nombre_rol ?? 'Usuario' }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center text-sm text-gray-300 hover:text-white hover:bg-red-600 border border-gray-700 hover:border-transparent px-3 py-2 rounded-lg transition-all">
                        <i class="fas fa-sign-out-alt mr-2"></i> Cerrar Sesión
                    </button>
                </form>
            </div>
        </aside>

        <!-- CONTENIDO PRINCIPAL -->
        <main class="flex-1 flex flex-col h-full overflow-hidden bg-slate-50 relative">
            <div class="flex-1 overflow-y-auto">
                @yield('content')
            </div>
        </main>

    @else
        <!-- VISTA PARA INVITADOS (Ej. Login) -->
        <main class="w-full h-full overflow-y-auto">
            @yield('content')
        </main>
    @endauth

</body>
</html>
