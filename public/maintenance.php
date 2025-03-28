<?php
require_once __DIR__ . '/../config/config.php';

// Verificar si el modo mantenimiento está activo
$maintenance_mode = true; // Esto podría venir de la configuración

// Permitir acceso a IPs específicas (por ejemplo, la oficina)
$allowed_ips = [
    '127.0.0.1',
    // Agregar otras IPs permitidas
];

// Si la IP está permitida, redirigir al sitio normal
if (in_array($_SERVER['REMOTE_ADDR'], $allowed_ips)) {
    header('Location: ' . BASE_URL);
    exit;
}

// Establecer el código de estado HTTP correcto
header('HTTP/1.1 503 Service Temporarily Unavailable');
header('Status: 503 Service Temporarily Unavailable');
header('Retry-After: 3600'); // Sugiere al navegador volver a intentar en 1 hora

// Tiempo estimado de mantenimiento
$estimated_time = "30 minutos";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sitio en Mantenimiento - Octava Digital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .maintenance-animation {
            animation: spin 4s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .progress-animation {
            animation: progress 2s ease-in-out infinite;
            background: linear-gradient(90deg, #3b82f6 0%, #60a5fa 50%, #3b82f6 100%);
            background-size: 200% 100%;
        }
        @keyframes progress {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-2xl w-full">
        <!-- Logo y mensaje principal -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-blue-100 mb-6">
                <i class="fas fa-cog text-5xl text-blue-600 maintenance-animation"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-4">
                Sitio en Mantenimiento
            </h1>
            <p class="text-lg text-gray-600 mb-6">
                Estamos realizando mejoras para brindarte una mejor experiencia.
                Volveremos pronto.
            </p>
        </div>

        <!-- Tarjeta de estado -->
        <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
            <div class="space-y-6">
                <!-- Barra de progreso -->
                <div class="relative pt-1">
                    <div class="flex mb-2 items-center justify-between">
                        <div>
                            <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-blue-600 bg-blue-100">
                                Progreso
                            </span>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-semibold inline-block text-blue-600">
                                En proceso
                            </span>
                        </div>
                    </div>
                    <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-blue-100">
                        <div class="progress-animation shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center w-1/2 rounded"></div>
                    </div>
                </div>

                <!-- Detalles -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <i class="fas fa-clock text-blue-600 text-xl mb-2"></i>
                        <h3 class="font-semibold text-gray-900">Tiempo Estimado</h3>
                        <p class="text-gray-600"><?php echo $estimated_time; ?></p>
                    </div>
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <i class="fas fa-calendar-check text-blue-600 text-xl mb-2"></i>
                        <h3 class="font-semibold text-gray-900">Próxima Actualización</h3>
                        <p class="text-gray-600"><?php echo date('H:i', strtotime('+1 hour')); ?></p>
                    </div>
                </div>

                <!-- Mensaje adicional -->
                <div class="text-center text-sm text-gray-500">
                    <p>
                        Estamos trabajando para mejorar nuestros servicios.
                        Gracias por tu paciencia.
                    </p>
                </div>
            </div>
        </div>

        <!-- Enlaces alternativos -->
        <div class="text-center space-y-4">
            <p class="text-sm text-gray-600">
                Mientras tanto, puedes seguirnos en nuestras redes sociales:
            </p>
            <div class="flex justify-center space-x-4">
                <a href="#" class="text-gray-600 hover:text-blue-600 transition-colors duration-200">
                    <i class="fab fa-facebook text-2xl"></i>
                </a>
                <a href="#" class="text-gray-600 hover:text-blue-400 transition-colors duration-200">
                    <i class="fab fa-twitter text-2xl"></i>
                </a>
                <a href="#" class="text-gray-600 hover:text-pink-600 transition-colors duration-200">
                    <i class="fab fa-instagram text-2xl"></i>
                </a>
                <a href="#" class="text-gray-600 hover:text-blue-800 transition-colors duration-200">
                    <i class="fab fa-linkedin text-2xl"></i>
                </a>
            </div>
        </div>

        <!-- Pie de página -->
        <footer class="mt-8 text-center text-sm text-gray-500">
            <p>
                Para consultas urgentes, contáctanos en:
                <a href="mailto:soporte@octavadigital.cl" class="text-blue-600 hover:text-blue-800">
                    soporte@octavadigital.cl
                </a>
            </p>
        </footer>
    </div>

    <script>
        // Actualizar el tiempo estimado cada minuto
        setInterval(() => {
            const nextUpdate = new Date();
            nextUpdate.setHours(nextUpdate.getHours() + 1);
            document.querySelector('time').textContent = 
                nextUpdate.toLocaleTimeString('es-CL', { hour: '2-digit', minute: '2-digit' });
        }, 60000);

        // Recargar la página automáticamente cada 5 minutos
        setTimeout(() => {
            window.location.reload();
        }, 300000);
    </script>
</body>
</html>