<?php
/**
 * Header del panel de administración
 */

require_once __DIR__ . '/../../helpers/auth.php';
requireAuth();

$currentPage = basename($_SERVER['PHP_SELF']);
// Si es index.php o está vacío, considerar como dashboard
if ($currentPage === 'index.php' || empty($currentPage) || $_SERVER['REQUEST_URI'] === '/admin/' || $_SERVER['REQUEST_URI'] === '/admin') {
    $currentPage = '';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? escape($pageTitle) : 'Panel de Administración' ?> | Del Prado Inmobiliaria</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="/admin/" class="text-xl font-bold text-gray-900">Del Prado Admin</a>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="/admin/" class="<?= $currentPage === '' ? 'border-orange-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Dashboard
                        </a>
                        <a href="/admin/list.php" class="<?= $currentPage === 'list.php' ? 'border-orange-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Propiedades
                        </a>
                        <a href="/admin/add.php" class="<?= $currentPage === 'add.php' ? 'border-orange-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' ?> inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            Agregar Propiedad
                        </a>
                    </div>
                </div>
                <div class="flex items-center">
                    <span class="text-sm text-gray-700 mr-4"><?= escape(getCurrentUsername()) ?></span>
                    <a href="/admin/logout.php" class="text-sm text-red-600 hover:text-red-800">Cerrar Sesión</a>
                </div>
            </div>
        </div>
    </nav>
    
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
