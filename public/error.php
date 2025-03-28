<?php
require_once __DIR__ . '/../config/config.php';

// Obtener código de error
$error_code = $_GET['code'] ?? '404';

// Definir mensajes de error
$error_messages = [
    '400' => [
        'title' => 'Solicitud incorrecta',
        'message' => 'La solicitud no pudo ser procesada debido a un error en la sintaxis.',
        'icon' => 'fa-exclamation-circle'
    ],
    '401' => [
        'title' => 'No autorizado',
        'message' => 'No tiene permisos para acceder a este recurso.',
        'icon' => 'fa-lock'
    ],
    '403' => [
        'title' => 'Acceso prohibido',
        'message' => 'No tiene permiso para acceder a este recurso.',
        'icon' => 'fa-ban'
    ],
    '404' => [
        'title' => 'Página no encontrada',
        'message' => 'Lo sentimos, la página que está buscando no existe o ha sido movida.',
        'icon' => 'fa-search'
    ],
    '500' => [
        'title' => 'Error del servidor',
        'message' => 'Lo sentimos, ha ocurrido un error interno del servidor.',
        'icon' => 'fa-exclamation-triangle'
    ],
    '503' => [
        'title' => 'Servicio no disponible',
        'message' => 'El servicio no está disponible temporalmente. Por favor, intente más tarde.',
        'icon' => 'fa-clock'
    ]
];

// Obtener mensaje de error
$error = $error_messages[$error_code] ?? $error_messages['404'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $error['title']; ?> - Octava Digital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-animation {
            animation: bounce 1s infinite;
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        .waves {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 200px;
            background-color: #3b82f6;
            clip-path: polygon(
                0% 0%,
                15% 20%,
                30% 0%,
                45% 20%,
                60% 0%,
                75% 20%,
                90% 0%,
                100% 20%,
                100% 100%,
                0% 100%
            );
            z-index: -1;
        }
    </style>
</head>
<body class="bg-gray-100 px-4">
    <!-- Fondo con ondas -->
    <div class="waves opacity-10"></div>

    <div class="max-w-lg w-full">
        <!-- Logo y título -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-blue-100 mb-4 error-animation">
                <i class="fas <?php echo $error['icon']; ?> text-4xl text-blue-600"></i>
            </div>
            <h1 class="text-6xl font-bold text-blue-600 mb-4">
                <?php echo $error_code; ?>
            </h1>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">
                <?php echo $error['title']; ?>
            </h2>
            <p class="text-gray-600">
                <?php echo $error['message']; ?>
            </p>
        </div>

        <!-- Tarjeta de acciones -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <div class="space-y-4">
                <div class="flex justify-center">
                    <a href="<?php echo BASE_URL; ?>" 
                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        <i class="fas fa-home mr-2"></i>
                        Volver al inicio
                    </a>
                </div>
                <div class="flex justify-center space-x-4 text-sm">
                    <button onclick="window.history.back()" 
                            class="text-gray-600 hover:text-gray-900 transition-colors duration-200">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Página anterior
                    </button>
                    <span class="text-gray-300">|</span>
                    <a href="#" onclick="reportError()" class="text-gray-600 hover:text-gray-900 transition-colors duration-200">
                        <i class="fas fa-flag mr-1"></i>
                        Reportar error
                    </a>
                </div>
            </div>
        </div>

        <!-- Enlaces útiles -->
        <div class="text-center">
            <p class="text-sm text-gray-500 mb-2">
                ¿Necesita ayuda? Pruebe estos enlaces:
            </p>
            <div class="flex justify-center space-x-4 text-sm">
                <a href="<?php echo BASE_URL; ?>/noticias" class="text-blue-600 hover:text-blue-800 transition-colors duration-200">
                    Noticias
                </a>
                <span class="text-gray-300">•</span>
                <a href="<?php echo BASE_URL; ?>/contacto" class="text-blue-600 hover:text-blue-800 transition-colors duration-200">
                    Contacto
                </a>
                <span class="text-gray-300">•</span>
                <a href="<?php echo BASE_URL; ?>/buscar" class="text-blue-600 hover:text-blue-800 transition-colors duration-200">
                    Búsqueda
                </a>
            </div>
        </div>
    </div>

    <script>
        // Función para reportar errores
        function reportError() {
            const errorDetails = {
                code: '<?php echo $error_code; ?>',
                url: window.location.href,
                timestamp: new Date().toISOString(),
                userAgent: navigator.userAgent
            };

            // Aquí se podría implementar el envío del reporte
            console.log('Error reportado:', errorDetails);
            alert('Gracias por reportar este error. Nuestro equipo técnico ha sido notificado.');
        }

        // Redirección automática para error 500
        <?php if ($error_code === '500'): ?>
        setTimeout(() => {
            window.location.href = '<?php echo BASE_URL; ?>';
        }, 10000);
        <?php endif; ?>

        // Registrar el error en la consola para debugging
        console.error('Error <?php echo $error_code; ?>:', {
            title: '<?php echo addslashes($error['title']); ?>',
            message: '<?php echo addslashes($error['message']); ?>',
            path: window.location.pathname
        });
    </script>
</body>
</html>