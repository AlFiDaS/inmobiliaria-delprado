<?php
/**
 * Router simple para el servidor PHP integrado
 * Sirve archivos estáticos y redirige requests a PHP
 */

$requestUri = $_SERVER['REQUEST_URI'];
$requestPath = parse_url($requestUri, PHP_URL_PATH);

// Definir tipos MIME
$mimeTypes = [
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png',
    'gif' => 'image/gif',
    'webp' => 'image/webp',
    'css' => 'text/css',
    'js' => 'application/javascript',
    'svg' => 'image/svg+xml',
    'ico' => 'image/x-icon',
    'woff' => 'font/woff',
    'woff2' => 'font/woff2',
    'ttf' => 'font/ttf',
    'eot' => 'application/vnd.ms-fontobject'
];

// Si es un archivo estático (imágenes, CSS, JS, etc.)
if (preg_match('/\.(jpg|jpeg|png|gif|webp|css|js|svg|ico|woff|woff2|ttf|eot)$/i', $requestPath)) {
    // Primero intentar en public/ (más común)
    $publicPath = __DIR__ . '/public' . $requestPath;
    if (file_exists($publicPath) && is_file($publicPath)) {
        $extension = strtolower(pathinfo($publicPath, PATHINFO_EXTENSION));
        $mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';
        
        header('Content-Type: ' . $mimeType);
        header('Content-Length: ' . filesize($publicPath));
        readfile($publicPath);
        exit;
    }
    
    // Si no existe en public/, intentar en la raíz
    $filePath = __DIR__ . $requestPath;
    if (file_exists($filePath) && is_file($filePath)) {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';
        
        header('Content-Type: ' . $mimeType);
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    }
    
    // Si no se encuentra, devolver 404
    http_response_code(404);
    echo 'File not found: ' . htmlspecialchars($requestPath);
    exit;
}

// Si es una ruta de admin, servir el archivo PHP correspondiente
if (strpos($requestPath, '/admin/') === 0) {
    $phpFile = __DIR__ . $requestPath;
    
    // Si es un directorio, buscar index.php
    if (is_dir($phpFile)) {
        $phpFile .= '/index.php';
    }
    
    // Si el archivo PHP existe, incluirlo
    if (file_exists($phpFile) && is_file($phpFile)) {
        return false; // Dejar que PHP lo maneje normalmente
    }
}

// Para cualquier otra ruta, dejar que PHP la maneje
return false;

