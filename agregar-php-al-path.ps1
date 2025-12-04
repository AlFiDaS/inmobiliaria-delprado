# Script para agregar PHP de XAMPP al PATH del sistema
# Ejecutar como Administrador

$phpPath = "C:\xampp\php"

if (Test-Path $phpPath) {
    Write-Host "Agregando PHP al PATH del sistema..." -ForegroundColor Cyan
    
    # Obtener el PATH actual del sistema
    $currentPath = [Environment]::GetEnvironmentVariable("Path", "Machine")
    
    # Verificar si ya está en el PATH
    if ($currentPath -notlike "*$phpPath*") {
        # Agregar PHP al PATH
        $newPath = $currentPath + ";$phpPath"
        [Environment]::SetEnvironmentVariable("Path", $newPath, "Machine")
        Write-Host "✅ PHP agregado al PATH exitosamente" -ForegroundColor Green
        Write-Host ""
        Write-Host "⚠️  IMPORTANTE: Cierra y vuelve a abrir PowerShell para que los cambios surtan efecto" -ForegroundColor Yellow
    } else {
        Write-Host "✅ PHP ya está en el PATH" -ForegroundColor Green
    }
} else {
    Write-Host "❌ XAMPP no encontrado en C:\xampp\" -ForegroundColor Red
    Write-Host "Por favor instala XAMPP primero desde: https://www.apachefriends.org/" -ForegroundColor Yellow
}

Write-Host ""
Read-Host "Presiona Enter para salir"

