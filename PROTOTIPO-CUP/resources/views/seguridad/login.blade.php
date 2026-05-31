<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CUP FICCT - Portal de Admisión</title>

    <!-- Google Fonts: Montserrat (Súper Premium y Corporativa) -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Tailwind CSS (via CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        uagrm: { red: '#c8102e', blue: '#0033a0', dark: '#1e293b' }
                    },
                    fontFamily: {
                        sans: ['Montserrat', 'sans-serif'],
                        display: ['Montserrat', 'sans-serif'],
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'slide-up': 'slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards',
                        'fade-in': 'fadeIn 1s ease-out forwards',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-20px)' },
                        },
                        slideUp: {
                            '0%': { opacity: '0', transform: 'translateY(40px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .glass-panel {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .bg-gradient-uagrm {
            background: linear-gradient(135deg, #c8102e 0%, #8B0000 50%, #0033a0 100%);
        }

        /* Custom Input Styling */
        .input-group {
            position: relative;
        }

        .input-group input {
            border: none;
            border-bottom: 2px solid #e2e8f0;
            border-radius: 0;
            padding: 1rem 0 0.5rem 2rem;
            background: transparent;
            transition: all 0.3s;
        }

        .input-group input:focus {
            border-bottom-color: #c8102e;
            outline: none;
            box-shadow: none;
        }

        .input-group i {
            position: absolute;
            left: 0;
            bottom: 0.75rem;
            color: #94a3b8;
            transition: color 0.3s;
        }

        .input-group input:focus+i {
            color: #c8102e;
        }

        .input-group label {
            position: absolute;
            left: 2rem;
            top: 1rem;
            color: #94a3b8;
            font-size: 1rem;
            pointer-events: none;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .input-group input:focus~label,
        .input-group input:not(:placeholder-shown)~label {
            top: -0.5rem;
            left: 0;
            font-size: 0.75rem;
            color: #c8102e;
            font-weight: 600;
        }
    </style>
</head>

<body class="bg-gradient-uagrm min-h-screen flex items-center justify-center p-4 antialiased overflow-hidden">

    <!-- Elementos flotantes decorativos en el fondo -->
    <div class="absolute top-10 left-10 w-32 h-32 bg-white rounded-full mix-blend-overlay filter blur-xl opacity-50 animate-float"
        style="animation-delay: 0s;"></div>
    <div class="absolute bottom-10 right-20 w-64 h-64 bg-uagrm-blue rounded-full mix-blend-overlay filter blur-2xl opacity-40 animate-float"
        style="animation-delay: 2s;"></div>

    <div
        class="w-full max-w-5xl flex flex-col md:flex-row rounded-3xl overflow-hidden glass-panel opacity-0 animate-slide-up relative z-10">

        <!-- Lado Izquierdo (Branding Institucional) -->
        <div
            class="w-full md:w-5/12 bg-white/95 p-10 flex flex-col justify-center items-center text-center relative overflow-hidden">
            <div class="relative z-10">
                <!-- LOGO DINÁMICO: Carga la imagen local si existe, sino muestra texto -->
                <div
                    class="w-48 h-48 flex items-center justify-center mb-8 mx-auto transform hover:scale-105 transition-transform duration-500">
                    <img src="{{ asset('img/logo.png') }}" alt="Logo Facultad"
                        class="w-full h-full object-contain drop-shadow-xl"
                        onerror="this.outerHTML='<h2 class=\'text-uagrm-blue font-black text-4xl tracking-tighter\'>UAGRM<br>FICCT</h2>'">
                </div>

                <h1 class="text-uagrm-blue font-black text-4xl mb-4 tracking-tight">CUP FICCT</h1>
                <p class="text-gray-600 text-lg font-medium tracking-wide">SISTEMA DE GESTIÓN Y ADMISIÓN DEL CURSO
                    PREUNIVERSITARIO (CUP) DE LA FICCT</p>
                <div class="w-16 h-1 bg-uagrm-red mx-auto mt-6 rounded-full"></div>
            </div>
        </div>

        <!-- Lado Derecho (Formulario de Login) -->
        <div class="w-full md:w-7/12 bg-uagrm-blue p-10 lg:p-16 flex flex-col justify-center relative shadow-inner">
            <!-- Capa decorativa -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-5 rounded-bl-full pointer-events-none"></div>

            <h2 class="text-3xl font-bold text-white mb-2 relative z-10">Bienvenido</h2>
            <p class="text-blue-200 font-medium mb-10 relative z-10">Accede al portal administrativo y académico.</p>

            @if(session('error'))
                <div
                    class="bg-red-500/20 text-white p-4 rounded-xl mb-6 text-sm font-semibold flex items-center border border-red-500/50 animate-fade-in relative z-10">
                    <i class="fas fa-exclamation-triangle mr-3 text-lg text-red-300"></i> {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-8 relative z-10">
                @csrf

                <!-- Input Identificador -->
                <div class="input-group">
                    <input type="text" id="identificador" name="identificador" placeholder=" " required
                        value="{{ old('identificador') }}" class="w-full text-white font-medium text-lg focus:border-uagrm-red" style="border-bottom-color: rgba(255,255,255,0.3);">
                    <i class="fas fa-id-card text-xl text-blue-200" style="color: rgba(255,255,255,0.6);"></i>
                    <label for="identificador" style="color: rgba(255,255,255,0.6);">Carnet de Identidad o Correo</label>
                </div>

                <!-- Input Contraseña -->
                <div class="input-group">
                    <input type="password" id="password" name="password" placeholder=" " required
                        class="w-full text-white font-medium text-lg focus:border-uagrm-red" style="border-bottom-color: rgba(255,255,255,0.3);">
                    <i class="fas fa-lock text-xl text-blue-200" style="color: rgba(255,255,255,0.6);"></i>
                    <label for="password" style="color: rgba(255,255,255,0.6);">Contraseña</label>
                </div>

                <div class="flex items-center justify-between mt-6">
                    <label class="flex items-center cursor-pointer group">
                        <div class="relative">
                            <input type="checkbox" class="sr-only">
                            <div
                                class="block w-10 h-6 bg-blue-900/50 rounded-full transition-colors group-hover:bg-blue-800">
                            </div>
                            <div
                                class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform transform">
                            </div>
                        </div>
                        <span class="ml-3 text-sm font-medium text-blue-100">Recordar sesión</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="text-sm font-bold text-blue-300 hover:text-white transition-colors">¿Olvidaste
                        tu contraseña?</a>
                </div>

                <button type="submit"
                    class="w-full bg-uagrm-red hover:bg-uagrm-darkred text-white font-bold text-lg py-4 rounded-xl shadow-[0_10px_20px_rgba(200,16,46,0.3)] hover:shadow-[0_10px_20px_rgba(200,16,46,0.5)] transform hover:-translate-y-1 transition-all duration-300 flex items-center justify-center group">
                    <span>Ingresar al Sistema</span>
                    <i class="fas fa-arrow-right ml-3 transform group-hover:translate-x-2 transition-transform"></i>
                </button>
            </form>

            <div class="absolute bottom-6 right-10 text-right">
                <p class="text-xs font-semibold text-blue-200 opacity-70">Ciclo 1 &copy; {{ date('Y') }} FICCT</p>
            </div>
        </div>
    </div>

    <style>
        /* Ajustes de hover para inputs oscuros */
        .input-group input:focus ~ i, .input-group input:not(:placeholder-shown) ~ i { color: #fff !important; }
        .input-group input:focus ~ label, .input-group input:not(:placeholder-shown) ~ label { color: #fff !important; }
        
        input:checked~.block {
            background-color: #c8102e;
        }

        input:checked~.dot {
            transform: translateX(100%);
        }
    </style>
</body>

</html>