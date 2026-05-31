<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayPal Checkout - UAGRM CUP</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Helvetica+Neue:wght@400;500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f5f7fa; }
        .paypal-blue { color: #003087; }
        .paypal-lightblue { color: #0079C1; }
        .paypal-btn { background-color: #ffc439; color: #111; font-weight: bold; border-radius: 24px; }
        .paypal-btn:hover { background-color: #f2b629; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

    <div class="max-w-4xl w-full bg-white rounded-xl shadow-2xl overflow-hidden flex flex-col md:flex-row border border-gray-200">
        
        <!-- Order Summary (Left side on desktop) -->
        <div class="w-full md:w-1/3 bg-gray-50 p-8 border-r border-gray-200">
            <div class="flex items-center mb-6">
                <!-- Mock PayPal Logo -->
                <i class="fab fa-paypal text-3xl paypal-blue mr-2"></i>
                <span class="text-2xl font-bold italic paypal-blue">Pay<span class="paypal-lightblue">Pal</span></span>
            </div>
            
            <p class="text-gray-500 text-sm mb-1">Pago a:</p>
            <h2 class="text-xl font-bold text-gray-800 mb-6">Universidad Autónoma Gabriel René Moreno (FICCT)</h2>
            
            <div class="border-t border-b border-gray-200 py-4 mb-6">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-gray-600">Matrícula CUP 2026</span>
                    <span class="text-gray-800 font-medium">$50.00 USD</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-500">Postulante CI:</span>
                    <span class="text-gray-600">{{ $postulante['ci'] ?? 'No detectado' }}</span>
                </div>
                <div class="flex justify-between items-center text-sm mt-1">
                    <span class="text-gray-500">Correo:</span>
                    <span class="text-gray-600 truncate ml-2">{{ $postulante['email'] ?? 'No detectado' }}</span>
                </div>
            </div>
            
            <div class="flex justify-between items-center">
                <span class="text-lg font-bold text-gray-800">Total</span>
                <span class="text-2xl font-bold text-gray-900">$50.00 USD</span>
            </div>
        </div>

        <!-- Payment Methods (Right side on desktop) -->
        <div class="w-full md:w-2/3 p-8">
            <h3 class="text-xl font-bold text-gray-800 mb-6">Elige tu forma de pago</h3>
            
            <!-- PayPal Express Checkout Mock -->
            <div class="mb-8">
                <button onclick="simularPagoExitoso()" class="w-full paypal-btn py-3 px-4 flex justify-center items-center shadow-sm transition-colors text-lg mb-4">
                    <i class="fab fa-paypal mr-2 text-xl" style="color: #003087;"></i> Pagar con PayPal
                </button>
                <div class="relative flex py-2 items-center">
                    <div class="flex-grow border-t border-gray-300"></div>
                    <span class="flex-shrink-0 mx-4 text-gray-400 text-sm">o paga con tarjeta de débito o crédito</span>
                    <div class="flex-grow border-t border-gray-300"></div>
                </div>
            </div>

            <!-- Credit Card Form Mock -->
            <form id="cardForm" onsubmit="event.preventDefault(); simularPagoExitoso();">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-medium mb-2">País / Región</label>
                    <select class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option>Bolivia</option>
                        <option>Estados Unidos</option>
                        <option>España</option>
                    </select>
                </div>
                
                <div class="mb-4 relative">
                    <label class="block text-gray-700 text-sm font-medium mb-2">Número de tarjeta</label>
                    <div class="relative">
                        <input type="text" placeholder="0000 0000 0000 0000" class="w-full border border-gray-300 rounded-md py-2 px-3 pl-10 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        <i class="far fa-credit-card absolute left-3 top-3 text-gray-400"></i>
                    </div>
                </div>

                <div class="flex mb-6 space-x-4">
                    <div class="w-1/2">
                        <label class="block text-gray-700 text-sm font-medium mb-2">Vencimiento</label>
                        <input type="text" placeholder="MM/AA" class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    </div>
                    <div class="w-1/2">
                        <label class="block text-gray-700 text-sm font-medium mb-2">CVC</label>
                        <div class="relative">
                            <input type="text" placeholder="123" class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            <i class="fas fa-question-circle absolute right-3 top-3 text-gray-400 cursor-pointer"></i>
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-3xl shadow-md transition-colors text-lg">
                    Continuar como invitado
                </button>
            </form>

            <div class="mt-8 text-center text-xs text-gray-500">
                <p>UAGRM CUP (Metodología PUDS - CU07 Pasarela de Pagos).</p>
                <p class="mt-1">Tus datos están protegidos y encriptados de forma segura.</p>
            </div>
        </div>
    </div>

    <!-- Modal Procesando Pago -->
    <div id="modalLoading" class="hidden fixed inset-0 bg-white bg-opacity-90 z-50 flex flex-col items-center justify-center">
        <i class="fab fa-paypal text-6xl paypal-blue animate-pulse mb-4"></i>
        <div class="w-64 bg-gray-200 rounded-full h-2 mb-4">
            <div class="bg-blue-600 h-2 rounded-full" style="width: 100%; animation: progress 3s ease-in-out;"></div>
        </div>
        <h2 class="text-xl text-gray-800 font-medium">Procesando pago con tu banco...</h2>
    </div>

    <script>
        function simularPagoExitoso() {
            document.getElementById('modalLoading').classList.remove('hidden');
            // Simulamos 3 segundos de procesamiento bancario real
            setTimeout(() => {
                alert('✅ ¡PAGO APROBADO EXITOSAMENTE POR PAYPAL!\n\nTransacción #PP-9876543210\n\nEl sistema enviará un correo al estudiante para el CU08 (Registrar Postulante).');
                // Aquí redirigiremos al formulario de Registro de Postulante
                window.location.href = '/'; 
            }, 3000);
        }
    </script>
    <style>
        @keyframes progress {
            0% { width: 0%; }
            100% { width: 100%; }
        }
    </style>
</body>
</html>
