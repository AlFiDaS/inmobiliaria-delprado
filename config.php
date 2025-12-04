<?php
/**
 * Configuración de la aplicación - Del Prado Inmobiliaria
 * Compatible con Hostinger
 * 
 * INSTRUCCIONES:
 * 1. Cambia las credenciales de base de datos según tu panel de Hostinger
 * 2. Actualiza SITE_URL con tu dominio
 * 3. En producción, cambia display_errors a 0
 */

// ============================================
// CONFIGURACIÓN DE BASE DE DATOS
// ============================================
// Para LOCAL (XAMPP): usuario 'root', contraseña vacía
// Para HOSTINGER: obtén estos valores desde el panel > Bases de datos MySQL
define('DB_HOST', 'localhost'); // Generalmente 'localhost' en Hostinger y XAMPP
define('DB_NAME', 'u161673556_DelPradoBD'); // Nombre de tu base de datos
define('DB_USER', 'u161673556_DelPrado'); // Para local: 'root', para Hostinger: tu_usuario_mysql (ej: u123456789_delprado)
define('DB_PASS', 'Delprado124!'); // Para local (XAMPP): vacío '', para Hostinger: tu_contraseña_mysql
define('DB_CHARSET', 'utf8mb4');

// ============================================
// CONFIGURACIÓN DEL SITIO
// ============================================
// Para LOCAL: 'http://localhost:8000'
// Para HOSTINGER: 'https://delprado.hechoencorrientes.com'
define('SITE_URL', 'https://delprado.hechoencorrientes.com'); // URL completa de tu sitio
define('SITE_NAME', 'Del Prado Inversión Inmobiliaria');

// ============================================
// CONFIGURACIÓN DE SESIONES
// ============================================
define('SESSION_NAME', 'delprado_admin');
define('SESSION_LIFETIME', 3600 * 8); // 8 horas

// ============================================
// CONFIGURACIÓN DE SEGURIDAD
// ============================================
define('CSRF_TOKEN_NAME', 'csrf_token');
define('MAX_LOGIN_ATTEMPTS', 5); // Intentos máximos de login
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutos en segundos

// ============================================
// CONFIGURACIÓN DE SUBIDA DE IMÁGENES
// ============================================
define('UPLOAD_MAX_SIZE', 3 * 1024 * 1024); // 3 MB en bytes
define('UPLOAD_ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/webp']);
define('UPLOAD_ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'webp']);
// Ruta base para imágenes
// En producción (Hostinger), las imágenes deben estar accesibles desde la web
// Usar DOCUMENT_ROOT para asegurar que las imágenes estén en la carpeta pública
if (isset($_SERVER['DOCUMENT_ROOT']) && $_SERVER['DOCUMENT_ROOT']) {
    // En producción, usar DOCUMENT_ROOT (generalmente public_html)
    $uploadBasePath = $_SERVER['DOCUMENT_ROOT'] . '/images/properties';
    // Verificar si existe, si no, intentar con la ruta relativa
    if (!is_dir($uploadBasePath) && is_dir(__DIR__ . '/public/images/properties')) {
        $uploadBasePath = __DIR__ . '/public/images/properties';
    }
} else {
    // En desarrollo local, usar ruta relativa
    $uploadBasePath = __DIR__ . '/public/images/properties';
}
define('UPLOAD_BASE_PATH', $uploadBasePath);
define('UPLOAD_MAX_IMAGES', 12); // Máximo de imágenes por propiedad

// ============================================
// CONFIGURACIÓN DE THUMBNAILS
// ============================================
define('THUMBNAIL_WIDTH', 200);
define('THUMBNAIL_HEIGHT', 150);
define('MAIN_IMAGE_WIDTH', 800);
define('MAIN_IMAGE_HEIGHT', 600);

// ============================================
// CONFIGURACIÓN DE EMAIL
// ============================================
define('CONTACT_EMAIL', 'info@delpradoinmobiliaria.com'); // Email para formulario de contacto

// ============================================
// CONFIGURACIÓN DE PHP
// ============================================
date_default_timezone_set('America/Argentina/Buenos_Aires');

// Mostrar errores (1 = desarrollo, 0 = producción)
error_reporting(E_ALL);
ini_set('display_errors', 1); // Cambiar a 0 en producción
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/php_errors.log');

// Crear directorio de logs si no existe
if (!is_dir(__DIR__ . '/logs')) {
    @mkdir(__DIR__ . '/logs', 0755, true);
}
