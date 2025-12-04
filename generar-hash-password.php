<?php
/**
 * Script para generar hash de contraseña
 * Ejecutar: php generar-hash-password.php
 * O acceder desde navegador: http://localhost:8000/generar-hash-password.php?password=tu_nueva_contraseña
 */

// Obtener contraseña desde parámetro GET o argumento de línea de comandos
$password = $_GET['password'] ?? $argv[1] ?? null;

if (!$password) {
    echo "Uso:\n";
    echo "  Desde navegador: http://localhost:8000/generar-hash-password.php?password=tu_contraseña\n";
    echo "  Desde terminal: php generar-hash-password.php tu_contraseña\n\n";
    echo "Ejemplo:\n";
    echo "  php generar-hash-password.php miNuevaContraseña123\n";
    exit(1);
}

// Generar hash
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "\n";
echo "========================================\n";
echo "  Hash generado para la contraseña\n";
echo "========================================\n\n";
echo "Contraseña: " . $password . "\n";
echo "Hash: " . $hash . "\n\n";
echo "========================================\n";
echo "  SQL para actualizar en la base de datos:\n";
echo "========================================\n\n";
echo "UPDATE users SET password_hash = '" . $hash . "' WHERE username = 'admin';\n\n";
echo "========================================\n";
echo "  Instrucciones:\n";
echo "========================================\n\n";
echo "1. Copia el hash de arriba\n";
echo "2. Accede a phpMyAdmin en Hostinger\n";
echo "3. Selecciona tu base de datos\n";
echo "4. Ve a la tabla 'users'\n";
echo "5. Haz clic en 'SQL' o 'Editar' en el usuario 'admin'\n";
echo "6. Ejecuta el comando UPDATE mostrado arriba\n";
echo "7. O manualmente cambia el campo password_hash por el hash generado\n\n";

