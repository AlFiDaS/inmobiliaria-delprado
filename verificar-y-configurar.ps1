# Script simple de verificación y configuración
Write-Host "=== Verificación del Sistema ===" -ForegroundColor Cyan
Write-Host ""

# Verificar XAMPP
$xamppPath = "C:\xampp\php\php.exe"
$phpFound = $false

if (Test-Path $xamppPath) {
    Write-Host "✅ XAMPP encontrado!" -ForegroundColor Green
    $phpFound = $true
    $phpExe = $xamppPath
} else {
    Write-Host "❌ XAMPP no está instalado" -ForegroundColor Red
    Write-Host ""
    Write-Host "📥 Para instalar XAMPP:" -ForegroundColor Yellow
    Write-Host "   1. Ve a: https://www.apachefriends.org/download.html" -ForegroundColor White
    Write-Host "   2. Descarga XAMPP para Windows" -ForegroundColor White
    Write-Host "   3. Instala en C:\xampp\" -ForegroundColor White
    Write-Host "   4. Inicia Apache y MySQL desde el Panel de Control" -ForegroundColor White
    Write-Host "   5. Ejecuta este script nuevamente" -ForegroundColor White
    Write-Host ""
    Read-Host "Presiona Enter para salir"
    exit
}

# Verificar MySQL
Write-Host ""
Write-Host "Verificando MySQL..." -ForegroundColor Yellow
$mysqlRunning = Get-Process -Name "mysqld" -ErrorAction SilentlyContinue

if ($mysqlRunning) {
    Write-Host "✅ MySQL está corriendo" -ForegroundColor Green
} else {
    Write-Host "⚠️  MySQL no está corriendo" -ForegroundColor Yellow
    Write-Host "   Inicia MySQL desde el Panel de Control de XAMPP" -ForegroundColor White
    Write-Host ""
}

# Verificar config.php
Write-Host ""
Write-Host "Verificando config.php..." -ForegroundColor Yellow
$configFile = "config.php"

if (Test-Path $configFile) {
    $configContent = Get-Content $configFile -Raw
    
    # Actualizar para local si es necesario
    if ($configContent -notmatch "DB_USER.*root") {
        Write-Host "   Actualizando config.php para local..." -ForegroundColor Gray
        $configContent = $configContent -replace "define\('DB_USER', '[^']+'\);", "define('DB_USER', 'root');"
        $configContent = $configContent -replace "define\('DB_PASS', '[^']+'\);", "define('DB_PASS', '');"
        $configContent = $configContent -replace "define\('SITE_URL', '[^']+'\);", "define('SITE_URL', 'http://localhost:8000');"
        Set-Content -Path $configFile -Value $configContent -NoNewline
        Write-Host "✅ config.php actualizado" -ForegroundColor Green
    } else {
        Write-Host "✅ config.php está configurado para local" -ForegroundColor Green
    }
} else {
    Write-Host "❌ config.php no encontrado" -ForegroundColor Red
}

# Crear carpetas
Write-Host ""
Write-Host "Verificando carpetas..." -ForegroundColor Yellow
$folders = @("public\images\properties", "logs")

foreach ($folder in $folders) {
    if (-not (Test-Path $folder)) {
        New-Item -ItemType Directory -Path $folder -Force | Out-Null
        Write-Host "✅ Carpeta creada: $folder" -ForegroundColor Green
    } else {
        Write-Host "✅ Carpeta existe: $folder" -ForegroundColor Green
    }
}

# Verificar base de datos
Write-Host ""
Write-Host "Verificando base de datos..." -ForegroundColor Yellow
Write-Host "   Abre http://localhost/phpmyadmin y:" -ForegroundColor White
Write-Host "   1. Crea una base de datos llamada delprado_db" -ForegroundColor White
Write-Host "   2. Importa el archivo database.sql" -ForegroundColor White
Write-Host ""

# Resumen
Write-Host "=== Resumen ===" -ForegroundColor Cyan
Write-Host ""

if ($phpFound) {
    Write-Host "✅ PHP disponible en: $phpExe" -ForegroundColor Green
    Write-Host ""
    Write-Host "Para iniciar el servidor, ejecuta:" -ForegroundColor Yellow
    Write-Host "   $phpExe -S localhost:8000" -ForegroundColor White
    Write-Host ""
    Write-Host "O usa el script:" -ForegroundColor Yellow
    Write-Host "   .\start-server.ps1" -ForegroundColor White
} else {
    Write-Host "❌ PHP no disponible" -ForegroundColor Red
}

Write-Host ""
Write-Host "Una vez que la base de datos esté creada, accede a:" -ForegroundColor Yellow
Write-Host "   http://localhost:8000/admin/login.php" -ForegroundColor White
Write-Host ""
Write-Host "Credenciales:" -ForegroundColor Yellow
Write-Host "   Usuario: admin" -ForegroundColor White
Write-Host "   Contraseña: admin123" -ForegroundColor White
Write-Host ""

Read-Host "Presiona Enter para salir"

