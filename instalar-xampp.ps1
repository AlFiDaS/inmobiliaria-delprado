# Script para descargar e instalar XAMPP automáticamente
# Requiere ejecutar como Administrador

Write-Host "=== Instalador Automático de XAMPP ===" -ForegroundColor Cyan
Write-Host ""

# URL de descarga de XAMPP
$downloadUrl = "https://sourceforge.net/projects/xampp/files/XAMPP%20Windows/8.2.12/xampp-windows-x64-8.2.12-0-VS16-installer.exe/download"
$installerPath = "$env:TEMP\xampp-installer.exe"
$xamppPath = "C:\xampp"

# Verificar si ya está instalado
if (Test-Path "$xamppPath\php\php.exe") {
    Write-Host "✅ XAMPP ya está instalado en $xamppPath" -ForegroundColor Green
    Write-Host ""
    Write-Host "Ejecuta 'configurar-todo.ps1' para configurar el proyecto" -ForegroundColor Cyan
    Read-Host "Presiona Enter para salir"
    exit
}

Write-Host "Este script descargará e instalará XAMPP automáticamente." -ForegroundColor Yellow
Write-Host ""
Write-Host "⚠️  IMPORTANTE:" -ForegroundColor Red
Write-Host "   - Necesitas ejecutar este script como Administrador" -ForegroundColor White
Write-Host "   - El instalador se abrirá y necesitarás hacer clic en 'Next' varias veces" -ForegroundColor White
Write-Host "   - La instalación puede tardar varios minutos" -ForegroundColor White
Write-Host ""
$confirm = Read-Host "¿Continuar? (S/N)"

if ($confirm -ne "S" -and $confirm -ne "s") {
    Write-Host "Cancelado." -ForegroundColor Yellow
    exit
}

# Descargar XAMPP
Write-Host ""
Write-Host "Descargando XAMPP..." -ForegroundColor Yellow
Write-Host "Esto puede tardar varios minutos..." -ForegroundColor Gray

try {
    # Usar Invoke-WebRequest para descargar
    $ProgressPreference = 'SilentlyContinue'
    Invoke-WebRequest -Uri $downloadUrl -OutFile $installerPath -UseBasicParsing
    
    if (Test-Path $installerPath) {
        Write-Host "✅ Descarga completada" -ForegroundColor Green
        Write-Host ""
        Write-Host "Iniciando instalador..." -ForegroundColor Yellow
        Write-Host ""
        Write-Host "📝 INSTRUCCIONES:" -ForegroundColor Cyan
        Write-Host "   1. En el instalador, haz clic en 'Next' varias veces" -ForegroundColor White
        Write-Host "   2. Acepta los términos y condiciones" -ForegroundColor White
        Write-Host "   3. Selecciona los componentes: Apache y MySQL (marca ambos)" -ForegroundColor White
        Write-Host "   4. Elige la ubicación: C:\xampp (por defecto)" -ForegroundColor White
        Write-Host "   5. Completa la instalación" -ForegroundColor White
        Write-Host ""
        Write-Host "Después de instalar, ejecuta 'configurar-todo.ps1'" -ForegroundColor Yellow
        Write-Host ""
        
        # Ejecutar instalador
        Start-Process -FilePath $installerPath -Wait
        
        Write-Host ""
        Write-Host "✅ Instalación completada" -ForegroundColor Green
        Write-Host ""
        Write-Host "Ahora ejecuta: .\configurar-todo.ps1" -ForegroundColor Cyan
        
    } else {
        Write-Host "❌ Error al descargar XAMPP" -ForegroundColor Red
        Write-Host ""
        Write-Host "Descarga manualmente desde: https://www.apachefriends.org/" -ForegroundColor Yellow
    }
} catch {
    Write-Host "❌ Error: $_" -ForegroundColor Red
    Write-Host ""
    Write-Host "Descarga manualmente desde: https://www.apachefriends.org/" -ForegroundColor Yellow
    Write-Host "Luego ejecuta: .\configurar-todo.ps1" -ForegroundColor Cyan
}

Write-Host ""
Read-Host "Presiona Enter para salir"

