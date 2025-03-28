<?php
// Verificar si el sistema está instalado
if (!file_exists(__DIR__ . '/config/config.php')) {
    die('El sistema no está instalado. Por favor, siga las instrucciones en README.md para la instalación.');
}

// Cargar configuración
require_once __DIR__ . '/config/config.php';

// Verificar requisitos del sistema
$requirements = [
    'PHP Version >= 7.4' => version_compare(PHP_VERSION, '7.4.0', '>='),
    'PDO Extension' => extension_loaded('pdo'),
    'MySQL Extension' => extension_loaded('pdo_mysql'),
    'GD Extension' => extension_loaded('gd'),
    'FileInfo Extension' => extension_loaded('fileinfo'),
    'Uploads Directory Writable' => is_writable(__DIR__ . '/uploads'),
];

$missingRequirements = array_filter($requirements, function($met) {
    return !$met;
});

if (!empty($missingRequirements)) {
    echo '<!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Verificación del Sistema - CMS Octava Digital</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
        <style>body { font-family: Inter, sans-serif; }</style>
    </head>
    <body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
        <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-6">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-red-600">Requisitos no cumplidos</h1>
                <p class="text-gray-600 mt-2">Los siguientes requisitos deben ser satisfechos:</p>
            </div>
            <ul class="space-y-2">';
    
    foreach ($missingRequirements as $requirement => $met) {
        echo '<li class="flex items-center text-red-600">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                ' . htmlspecialchars($requirement) . '
            </li>';
    }
    
    echo '</ul>
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-500">Por favor, contacte al administrador del sistema.</p>
        </div>
        </div>
    </body>
    </html>';
    exit;
}

// Verificar si hay una sesión activa
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: ' . ADMIN_URL . '/dashboard.php');
    exit;
}

// Redirigir al login
header('Location: ' . ADMIN_URL . '/login.php');
exit;