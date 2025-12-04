<?php
/**
 * Script para ejecutar el build del sitio Astro
 * Se ejecuta en segundo plano después de sincronizar propiedades
 */

$rootDir = __DIR__;
$logFile = $rootDir . '/logs/build.log';

// Cambiar al directorio del proyecto
chdir($rootDir);

// Registrar inicio
file_put_contents($logFile, date('Y-m-d H:i:s') . " - Iniciando build...\n", FILE_APPEND);

// Verificar si npm está disponible
$npmCheck = shell_exec('npm --version 2>&1');
if (strpos($npmCheck, 'npm') === false) {
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - ERROR: npm no encontrado\n", FILE_APPEND);
    exit(1);
}

// Ejecutar build
$command = 'npm run build 2>&1';
$output = [];
$returnCode = 0;
exec($command, $output, $returnCode);

// Registrar resultado
$logMessage = date('Y-m-d H:i:s') . " - Build " . ($returnCode === 0 ? "completado" : "fallido") . "\n";
file_put_contents($logFile, $logMessage, FILE_APPEND);
file_put_contents($logFile, implode("\n", $output) . "\n", FILE_APPEND);

if ($returnCode === 0) {
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - Sitio regenerado correctamente\n", FILE_APPEND);
} else {
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - ERROR en build (código: $returnCode)\n", FILE_APPEND);
}

exit($returnCode);

