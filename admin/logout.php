<?php
/**
 * Cerrar sesión
 */

require_once __DIR__ . '/../helpers/auth.php';

startSession();

// Destruir todas las variables de sesión
$_SESSION = [];

// Destruir la cookie de sesión
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destruir la sesión
session_destroy();

// Redirigir al login
header('Location: /admin/login.php');
exit;
