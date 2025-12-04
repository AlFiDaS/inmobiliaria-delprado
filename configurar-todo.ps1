# Script de configuración automática completa
# Ejecutar como Administrador después de instalar XAMPP

Write-Host "=== Configuración Automática - Del Prado Inmobiliaria ===" -ForegroundColor Cyan
Write-Host ""

# 1. Verificar XAMPP
Write-Host "1. Verificando XAMPP..." -ForegroundColor Yellow
$xamppPath = "C:\xampp"
$phpPath = "C:\xampp\php\php.exe"
$mysqlPath = "C:\xampp\mysql\bin\mysql.exe"

if (-not (Test-Path $phpPath)) {
    Write-Host "   ❌ XAMPP no encontrado en C:\xampp\" -ForegroundColor Red
    Write-Host "   Por favor instala XAMPP primero desde: https://www.apachefriends.org/" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "   Después de instalar, ejecuta este script nuevamente." -ForegroundColor Cyan
    Read-Host "Presiona Enter para salir"
    exit
}

Write-Host "   ✅ XAMPP encontrado" -ForegroundColor Green

# 2. Agregar PHP al PATH
Write-Host ""
Write-Host "2. Agregando PHP al PATH del sistema..." -ForegroundColor Yellow
$phpDir = "C:\xampp\php"
$currentPath = [Environment]::GetEnvironmentVariable("Path", "Machine")

if ($currentPath -notlike "*$phpDir*") {
    try {
        $newPath = $currentPath + ";$phpDir"
        [Environment]::SetEnvironmentVariable("Path", $newPath, "Machine")
        Write-Host "   ✅ PHP agregado al PATH" -ForegroundColor Green
        Write-Host "   ⚠️  Cierra y vuelve a abrir PowerShell para que surta efecto" -ForegroundColor Yellow
    } catch {
        Write-Host "   ❌ Error al agregar al PATH: $_" -ForegroundColor Red
        Write-Host "   Asegúrate de ejecutar como Administrador" -ForegroundColor Yellow
    }
} else {
    Write-Host "   ✅ PHP ya está en el PATH" -ForegroundColor Green
}

# 3. Verificar que MySQL esté corriendo
Write-Host ""
Write-Host "3. Verificando MySQL..." -ForegroundColor Yellow
$mysqlRunning = Get-Process -Name "mysqld" -ErrorAction SilentlyContinue

if (-not $mysqlRunning) {
    Write-Host "   ⚠️  MySQL no está corriendo" -ForegroundColor Yellow
    Write-Host "   Inicia MySQL desde el Panel de Control de XAMPP" -ForegroundColor Cyan
    Write-Host "   Luego ejecuta este script nuevamente" -ForegroundColor Cyan
    Read-Host "Presiona Enter para salir"
    exit
}

Write-Host "   ✅ MySQL está corriendo" -ForegroundColor Green

# 4. Crear base de datos
Write-Host ""
Write-Host "4. Creando base de datos..." -ForegroundColor Yellow

$dbName = "delprado_db"
$dbUser = "root"
$dbPass = "" # XAMPP por defecto no tiene contraseña

# Intentar crear la base de datos
try {
    $sqlCreate = "CREATE DATABASE IF NOT EXISTS $dbName CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    
    if ($dbPass) {
        $result = & $mysqlPath -u $dbUser -p$dbPass -e $sqlCreate 2>&1
    } else {
        $result = & $mysqlPath -u $dbUser -e $sqlCreate 2>&1
    }
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host "   ✅ Base de datos '$dbName' creada o ya existe" -ForegroundColor Green
    } else {
        Write-Host "   ⚠️  No se pudo crear automáticamente. Créala manualmente en phpMyAdmin" -ForegroundColor Yellow
    }
} catch {
    Write-Host "   ⚠️  No se pudo crear automáticamente. Créala manualmente en phpMyAdmin" -ForegroundColor Yellow
    Write-Host "   Error: $_" -ForegroundColor Gray
}

# 5. Importar estructura SQL
Write-Host ""
Write-Host "5. Importando estructura de base de datos..." -ForegroundColor Yellow

$sqlFile = Join-Path $PSScriptRoot "database.sql"

if (Test-Path $sqlFile) {
    try {
        if ($dbPass) {
            $result = Get-Content $sqlFile | & $mysqlPath -u $dbUser -p$dbPass $dbName 2>&1
        } else {
            $result = Get-Content $sqlFile | & $mysqlPath -u $dbUser $dbName 2>&1
        }
        
        if ($LASTEXITCODE -eq 0) {
            Write-Host "   ✅ Estructura de base de datos importada" -ForegroundColor Green
        } else {
            Write-Host "   ⚠️  Error al importar. Importa manualmente desde phpMyAdmin" -ForegroundColor Yellow
            Write-Host "   Archivo: $sqlFile" -ForegroundColor Gray
        }
    } catch {
        Write-Host "   ⚠️  Error al importar. Importa manualmente desde phpMyAdmin" -ForegroundColor Yellow
        Write-Host "   Archivo: $sqlFile" -ForegroundColor Gray
    }
} else {
    Write-Host "   ❌ Archivo database.sql no encontrado" -ForegroundColor Red
}

# 6. Verificar config.php
Write-Host ""
Write-Host "6. Verificando config.php..." -ForegroundColor Yellow

$configFile = Join-Path $PSScriptRoot "config.php"

if (Test-Path $configFile) {
    # Leer y actualizar config.php para local
    $configContent = Get-Content $configFile -Raw
    
    # Actualizar valores para local
    $configContent = $configContent -replace "define\('DB_USER', '[^']+'\);", "define('DB_USER', 'root');"
    $configContent = $configContent -replace "define\('DB_PASS', '[^']+'\);", "define('DB_PASS', '');"
    $configContent = $configContent -replace "define\('SITE_URL', '[^']+'\);", "define('SITE_URL', 'http://localhost:8000');"
    
    Set-Content -Path $configFile -Value $configContent -NoNewline
    Write-Host "   ✅ config.php actualizado para entorno local" -ForegroundColor Green
} else {
    Write-Host "   ❌ config.php no encontrado" -ForegroundColor Red
}

# 7. Crear carpetas necesarias
Write-Host ""
Write-Host "7. Creando carpetas necesarias..." -ForegroundColor Yellow

$folders = @(
    "public\images\properties",
    "logs"
)

foreach ($folder in $folders) {
    $fullPath = Join-Path $PSScriptRoot $folder
    if (-not (Test-Path $fullPath)) {
        New-Item -ItemType Directory -Path $fullPath -Force | Out-Null
        Write-Host "   ✅ Carpeta creada: $folder" -ForegroundColor Green
    } else {
        Write-Host "   ✅ Carpeta existe: $folder" -ForegroundColor Green
    }
}

# 8. Verificar extensiones PHP
Write-Host ""
Write-Host "8. Verificando extensiones PHP..." -ForegroundColor Yellow

$phpIni = "C:\xampp\php\php.ini"
if (Test-Path $phpIni) {
    $phpIniContent = Get-Content $phpIni -Raw
    $extensionsToEnable = @("pdo_mysql", "gd", "mbstring")
    $needsRestart = $false
    
    foreach ($ext in $extensionsToEnable) {
        if ($phpIniContent -match ";extension=$ext") {
            $phpIniContent = $phpIniContent -replace ";extension=$ext", "extension=$ext"
            $needsRestart = $true
            Write-Host "   ✅ Extensión '$ext' habilitada" -ForegroundColor Green
        } elseif ($phpIniContent -match "extension=$ext") {
            Write-Host "   ✅ Extensión '$ext' ya está habilitada" -ForegroundColor Green
        }
    }
    
    if ($needsRestart) {
        Set-Content -Path $phpIni -Value $phpIniContent -NoNewline
        Write-Host "   ⚠️  Reinicia Apache en XAMPP para aplicar cambios" -ForegroundColor Yellow
    }
} else {
    Write-Host "   ⚠️  php.ini no encontrado" -ForegroundColor Yellow
}

# Resumen
Write-Host ""
Write-Host "=== Resumen ===" -ForegroundColor Cyan
Write-Host ""
Write-Host "✅ Configuración completada" -ForegroundColor Green
Write-Host ""
Write-Host "Próximos pasos:" -ForegroundColor Yellow
Write-Host "1. Cierra y vuelve a abrir PowerShell (para que PHP esté en el PATH)" -ForegroundColor White
Write-Host "2. Si creaste la BD manualmente, importa database.sql desde phpMyAdmin" -ForegroundColor White
Write-Host "3. Inicia el servidor con: php -S localhost:8000" -ForegroundColor White
Write-Host "4. Abre: http://localhost:8000/admin/login.php" -ForegroundColor White
Write-Host ""
Write-Host "Credenciales:" -ForegroundColor Yellow
Write-Host "  Usuario: admin" -ForegroundColor White
Write-Host "  Contraseña: admin123" -ForegroundColor White
Write-Host ""

Read-Host "Presiona Enter para salir"

