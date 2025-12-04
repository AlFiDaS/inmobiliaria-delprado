<?php
/**
 * Página de login del panel de administración
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers/auth.php';

// Si ya está autenticado, redirigir al panel
if (isAuthenticated()) {
    header('Location: /admin/');
    exit;
}

$error = '';
$locked = false;
$remainingTime = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Verificar intentos de login
    $checkResult = checkLoginAttempts($username);
    if (is_array($checkResult) && isset($checkResult['locked'])) {
        $locked = true;
        $remainingTime = $checkResult['remaining'];
    } else {
        if (empty($username) || empty($password)) {
            $error = 'Por favor, completa todos los campos';
        } else {
            try {
                $db = getDB();
                $stmt = $db->prepare('SELECT id, username, password_hash FROM users WHERE username = :username LIMIT 1');
                $stmt->execute([':username' => $username]);
                $user = $stmt->fetch();
                
                if ($user && password_verify($password, $user['password_hash'])) {
                    // Login exitoso
                    startSession();
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    clearFailedLogins($username);
                    
                    header('Location: /admin/');
                    exit;
                } else {
                    // Login fallido
                    recordFailedLogin($username);
                    $error = 'Usuario o contraseña incorrectos';
                    
                    // Verificar si ahora está bloqueado
                    $checkResult = checkLoginAttempts($username);
                    if (is_array($checkResult) && isset($checkResult['locked'])) {
                        $locked = true;
                        $remainingTime = $checkResult['remaining'];
                    }
                }
            } catch (PDOException $e) {
                error_log('Error en login: ' . $e->getMessage());
                $error = 'Error al iniciar sesión. Por favor, intenta más tarde.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Panel de Administración | Del Prado Inmobiliaria</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl p-8 w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Del Prado</h1>
            <p class="text-gray-600">Panel de Administración</p>
        </div>
        
        <?php if ($error): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                <?= escape($error) ?>
            </div>
        <?php endif; ?>
        
        <?php if ($locked): ?>
            <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-lg mb-6">
                <p class="font-semibold">Cuenta bloqueada temporalmente</p>
                <p class="text-sm mt-1">Demasiados intentos fallidos. Intenta nuevamente en <?= ceil($remainingTime / 60) ?> minutos.</p>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="" class="space-y-6">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-2">Usuario</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    required 
                    autofocus
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition"
                    value="<?= escape($_POST['username'] ?? '') ?>"
                >
            </div>
            
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Contraseña</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition"
                >
            </div>
            
            <button 
                type="submit" 
                class="w-full bg-gradient-to-r from-orange-600 to-orange-700 text-white font-semibold py-3 rounded-lg hover:from-orange-700 hover:to-orange-800 transition-all duration-300 shadow-lg hover:shadow-xl"
                <?= $locked ? 'disabled' : '' ?>
            >
                Iniciar Sesión
            </button>
        </form>
        
        <div class="mt-6 text-center text-sm text-gray-500">
            <p>Credenciales por defecto: <code class="bg-gray-100 px-2 py-1 rounded">admin / admin123</code></p>
            <p class="mt-2 text-xs text-red-600">⚠️ Cambiar contraseña después del primer login</p>
        </div>
    </div>
</body>
</html>
