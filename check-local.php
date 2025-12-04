<?php
/**
 * Script de verificación para entorno local
 * Ejecuta este archivo para verificar que todo esté configurado correctamente
 * 
 * Uso: php check-local.php
 */

echo "=== Verificación de Configuración Local ===\n\n";

// 1. Verificar PHP
echo "1. Verificando versión de PHP...\n";
$phpVersion = phpversion();
echo "   PHP versión: $phpVersion\n";
if (version_compare($phpVersion, '7.4.0', '<')) {
    echo "   ⚠️  ADVERTENCIA: Se requiere PHP 7.4 o superior\n";
} else {
    echo "   ✅ Versión de PHP OK\n";
}
echo "\n";

// 2. Verificar extensiones necesarias
echo "2. Verificando extensiones PHP...\n";
$requiredExtensions = ['pdo', 'pdo_mysql', 'gd', 'json', 'mbstring'];
$missing = [];
foreach ($requiredExtensions as $ext) {
    if (extension_loaded($ext)) {
        echo "   ✅ $ext está instalada\n";
    } else {
        echo "   ❌ $ext NO está instalada\n";
        $missing[] = $ext;
    }
}
if (!empty($missing)) {
    echo "\n   ⚠️  ADVERTENCIA: Faltan extensiones. Edita php.ini y habilita:\n";
    foreach ($missing as $ext) {
        echo "      extension=$ext\n";
    }
}
echo "\n";

// 3. Verificar archivo config.php
echo "3. Verificando config.php...\n";
if (file_exists(__DIR__ . '/config.php')) {
    echo "   ✅ config.php existe\n";
    require_once __DIR__ . '/config.php';
    
    // Verificar constantes
    $constants = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS', 'SITE_URL'];
    foreach ($constants as $const) {
        if (defined($const)) {
            $value = constant($const);
            if ($const === 'DB_PASS') {
                $value = $value === '' ? '(vacío)' : '***';
            }
            echo "   ✅ $const = $value\n";
        } else {
            echo "   ❌ $const NO está definida\n";
        }
    }
} else {
    echo "   ❌ config.php NO existe\n";
}
echo "\n";

// 4. Verificar conexión a base de datos
echo "4. Verificando conexión a MySQL...\n";
try {
    if (file_exists(__DIR__ . '/db.php')) {
        require_once __DIR__ . '/db.php';
        $db = getDB();
        echo "   ✅ Conexión a MySQL exitosa\n";
        
        // Verificar que la base de datos existe
        $stmt = $db->query("SELECT DATABASE()");
        $currentDb = $stmt->fetchColumn();
        echo "   ✅ Base de datos actual: $currentDb\n";
        
        // Verificar tablas
        $tables = ['properties', 'users'];
        foreach ($tables as $table) {
            $stmt = $db->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() > 0) {
                echo "   ✅ Tabla '$table' existe\n";
            } else {
                echo "   ❌ Tabla '$table' NO existe. Ejecuta database.sql\n";
            }
        }
    } else {
        echo "   ❌ db.php NO existe\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error de conexión: " . $e->getMessage() . "\n";
    echo "   💡 Verifica:\n";
    echo "      - MySQL está corriendo\n";
    echo "      - Credenciales en config.php son correctas\n";
    echo "      - La base de datos 'delprado_db' existe\n";
}
echo "\n";

// 5. Verificar carpetas necesarias
echo "5. Verificando carpetas...\n";
$folders = [
    'public/images/properties' => 'Carpeta para imágenes de propiedades',
    'logs' => 'Carpeta para logs de errores',
    'admin' => 'Carpeta del panel de administración',
    'helpers' => 'Carpeta de helpers'
];
foreach ($folders as $folder => $description) {
    $path = __DIR__ . '/' . $folder;
    if (is_dir($path)) {
        $writable = is_writable($path) ? ' (escribible)' : ' (no escribible)';
        echo "   ✅ $folder existe$writable\n";
        if (!is_writable($path) && in_array($folder, ['public/images/properties', 'logs'])) {
            echo "      ⚠️  ADVERTENCIA: Esta carpeta debe ser escribible\n";
        }
    } else {
        echo "   ❌ $folder NO existe\n";
        echo "      💡 Crea la carpeta: mkdir -p $folder\n";
    }
}
echo "\n";

// 6. Verificar archivos principales
echo "6. Verificando archivos principales...\n";
$files = [
    'db.php' => 'Conexión a base de datos',
    'admin/login.php' => 'Página de login',
    'admin/index.php' => 'Dashboard',
    'admin/list.php' => 'Lista de propiedades',
    'admin/add.php' => 'Agregar propiedad',
    'admin/edit.php' => 'Editar propiedad',
    'helpers/auth.php' => 'Autenticación',
    'helpers/upload.php' => 'Subida de imágenes'
];
foreach ($files as $file => $description) {
    if (file_exists(__DIR__ . '/' . $file)) {
        echo "   ✅ $file existe\n";
    } else {
        echo "   ❌ $file NO existe\n";
    }
}
echo "\n";

// Resumen
echo "=== Resumen ===\n";
echo "Si todos los checks están ✅, puedes iniciar el servidor con:\n";
echo "   php -S localhost:8000\n\n";
echo "Luego accede a:\n";
echo "   http://localhost:8000/admin/login.php\n\n";
echo "Credenciales por defecto:\n";
echo "   Usuario: admin\n";
echo "   Contraseña: admin123\n\n";

