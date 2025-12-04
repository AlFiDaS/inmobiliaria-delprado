# Script PowerShell para iniciar el servidor PHP local
# Busca PHP en ubicaciones comunes

Write-Host "Buscando PHP..." -ForegroundColor Cyan

# Verificar si PHP está en el PATH
$phpPath = Get-Command php -ErrorAction SilentlyContinue
if ($phpPath) {
    Write-Host "✅ PHP encontrado en PATH: $($phpPath.Source)" -ForegroundColor Green
    Write-Host "Iniciando servidor en http://localhost:8000" -ForegroundColor Yellow
    Write-Host "Presiona Ctrl+C para detener el servidor" -ForegroundColor Gray
    Write-Host ""
    php -S localhost:8000 router.php
    exit
}

# Verificar XAMPP
$xamppPath = "C:\xampp\php\php.exe"
if (Test-Path $xamppPath) {
    Write-Host "✅ PHP encontrado en XAMPP" -ForegroundColor Green
    Write-Host "Iniciando servidor en http://localhost:8000" -ForegroundColor Yellow
    Write-Host "Presiona Ctrl+C para detener el servidor" -ForegroundColor Gray
    Write-Host ""
    & $xamppPath -S localhost:8000 router.php
    exit
}

# Verificar otras ubicaciones comunes
$commonPaths = @(
    "C:\php\php.exe",
    "C:\Program Files\PHP\php.exe",
    "C:\Program Files (x86)\PHP\php.exe"
)

foreach ($path in $commonPaths) {
    if (Test-Path $path) {
        Write-Host "✅ PHP encontrado en: $path" -ForegroundColor Green
        Write-Host "Iniciando servidor en http://localhost:8000" -ForegroundColor Yellow
        Write-Host "Presiona Ctrl+C para detener el servidor" -ForegroundColor Gray
        Write-Host ""
        & $path -S localhost:8000 router.php
        exit
    }
}

# Si no se encuentra PHP
Write-Host ""
Write-Host "❌ ERROR: PHP no encontrado" -ForegroundColor Red
Write-Host ""
Write-Host "Por favor instala PHP o XAMPP:" -ForegroundColor Yellow
Write-Host "1. Descarga XAMPP desde: https://www.apachefriends.org/" -ForegroundColor White
Write-Host "2. O instala PHP desde: https://windows.php.net/download/" -ForegroundColor White
Write-Host ""
Write-Host "Ver INSTALAR-PHP.md para instrucciones detalladas." -ForegroundColor Cyan
Write-Host ""
Read-Host "Presiona Enter para salir"

