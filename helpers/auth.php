<?php
/**
 * Funciones de autenticación y seguridad
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';

/**
 * Inicia la sesión si no está iniciada
 */
function startSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_name(SESSION_NAME);
        session_set_cookie_params([
            'lifetime' => SESSION_LIFETIME,
            'path' => '/',
            'domain' => '',
            'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
        session_start();
    }
}

/**
 * Verifica si el usuario está autenticado
 */
function isAuthenticated() {
    startSession();
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}

/**
 * Requiere autenticación, redirige a login si no está autenticado
 */
function requireAuth() {
    if (!isAuthenticated()) {
        header('Location: /admin/login.php');
        exit;
    }
}

/**
 * Obtiene el ID del usuario actual
 */
function getCurrentUserId() {
    startSession();
    return $_SESSION['user_id'] ?? null;
}

/**
 * Obtiene el username del usuario actual
 */
function getCurrentUsername() {
    startSession();
    return $_SESSION['username'] ?? null;
}

/**
 * Genera un token CSRF
 */
function generateCSRFToken() {
    startSession();
    if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

/**
 * Verifica un token CSRF
 */
function verifyCSRFToken($token) {
    startSession();
    return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

/**
 * Verifica intentos de login fallidos
 */
function checkLoginAttempts($username) {
    startSession();
    $key = 'login_attempts_' . md5($username);
    
    if (!isset($_SESSION[$key])) {
        return true; // No hay intentos previos
    }
    
    $attempts = $_SESSION[$key];
    
    if ($attempts['count'] >= MAX_LOGIN_ATTEMPTS) {
        $timeElapsed = time() - $attempts['time'];
        if ($timeElapsed < LOGIN_LOCKOUT_TIME) {
            $remaining = LOGIN_LOCKOUT_TIME - $timeElapsed;
            return ['locked' => true, 'remaining' => $remaining];
        } else {
            // Resetear intentos después del tiempo de bloqueo
            unset($_SESSION[$key]);
            return true;
        }
    }
    
    return true;
}

/**
 * Registra un intento de login fallido
 */
function recordFailedLogin($username) {
    startSession();
    $key = 'login_attempts_' . md5($username);
    
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = ['count' => 1, 'time' => time()];
    } else {
        $_SESSION[$key]['count']++;
        $_SESSION[$key]['time'] = time();
    }
}

/**
 * Limpia los intentos de login fallidos
 */
function clearFailedLogins($username) {
    startSession();
    $key = 'login_attempts_' . md5($username);
    unset($_SESSION[$key]);
}

/**
 * Escapa output para prevenir XSS
 */
function escape($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
