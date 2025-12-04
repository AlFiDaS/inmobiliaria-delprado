# Script de configuración automática después de instalar XAMPP
Write-Host "=== Configuración Automática ===" -ForegroundColor Cyan
Write-Host ""

# 1. Verificar XAMPP
$xamppPath = "C:\xampp\php\php.exe"
if (-not (Test-Path $xamppPath)) {
    Write-Host "ERROR: XAMPP no encontrado en C:\xampp\" -ForegroundColor Red
    exit
}
Write-Host "[OK] XAMPP encontrado" -ForegroundColor Green

# 2. Verificar MySQL
Write-Host ""
Write-Host "Verificando MySQL..." -ForegroundColor Yellow
$mysqlRunning = Get-Process -Name "mysqld" -ErrorAction SilentlyContinue

if (-not $mysqlRunning) {
    Write-Host "[!] MySQL no esta corriendo" -ForegroundColor Yellow
    Write-Host "    Inicia MySQL desde el Panel de Control de XAMPP" -ForegroundColor White
    Write-Host "    Luego ejecuta este script nuevamente" -ForegroundColor White
    Write-Host ""
    Read-Host "Presiona Enter para salir"
    exit
}
Write-Host "[OK] MySQL esta corriendo" -ForegroundColor Green

# 3. Crear base de datos
Write-Host ""
Write-Host "Creando base de datos..." -ForegroundColor Yellow
$mysqlExe = "C:\xampp\mysql\bin\mysql.exe"
$dbName = "delprado_db"

if (Test-Path $mysqlExe) {
    try {
        # Crear base de datos
        $createDb = "CREATE DATABASE IF NOT EXISTS $dbName CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
        & $mysqlExe -u root -e $createDb 2>&1 | Out-Null
        
        if ($LASTEXITCODE -eq 0) {
            Write-Host "[OK] Base de datos '$dbName' creada" -ForegroundColor Green
            
            # Importar SQL
            Write-Host "Importando estructura..." -ForegroundColor Yellow
            $sqlFile = Join-Path $PSScriptRoot "database.sql"
            
            if (Test-Path $sqlFile) {
                Get-Content $sqlFile | & $mysqlExe -u root $dbName 2>&1 | Out-Null
                if ($LASTEXITCODE -eq 0) {
                    Write-Host "[OK] Estructura importada" -ForegroundColor Green
                } else {
                    Write-Host "[!] Error al importar. Importa manualmente desde phpMyAdmin" -ForegroundColor Yellow
                }
            } else {
                Write-Host "[!] Archivo database.sql no encontrado" -ForegroundColor Yellow
            }
        } else {
            Write-Host "[!] Error al crear base de datos. Crealo manualmente en phpMyAdmin" -ForegroundColor Yellow
        }
    } catch {
        Write-Host "[!] Error: $_" -ForegroundColor Red
        Write-Host "    Crea la base de datos manualmente en phpMyAdmin" -ForegroundColor Yellow
    }
} else {
    Write-Host "[!] MySQL no encontrado. Crea la base de datos manualmente en phpMyAdmin" -ForegroundColor Yellow
}

# 4. Verificar carpetas
Write-Host ""
Write-Host "Verificando carpetas..." -ForegroundColor Yellow
$folders = @("public\images\properties", "logs")
foreach ($folder in $folders) {
    if (-not (Test-Path $folder)) {
        New-Item -ItemType Directory -Path $folder -Force | Out-Null
        Write-Host "[OK] Carpeta creada: $folder" -ForegroundColor Green
    }
}

# 5. Resumen
Write-Host ""
Write-Host "=== Resumen ===" -ForegroundColor Cyan
Write-Host ""
Write-Host "Para iniciar el servidor, ejecuta:" -ForegroundColor Yellow
Write-Host "   C:\xampp\php\php.exe -S localhost:8000" -ForegroundColor White
Write-Host ""
Write-Host "Luego abre en tu navegador:" -ForegroundColor Yellow
Write-Host "   http://localhost:8000/admin/login.php" -ForegroundColor White
Write-Host ""
Write-Host "Credenciales:" -ForegroundColor Yellow
Write-Host "   Usuario: admin" -ForegroundColor White
Write-Host "   Contrasena: admin123" -ForegroundColor White
Write-Host ""

Read-Host "Presiona Enter para salir"

