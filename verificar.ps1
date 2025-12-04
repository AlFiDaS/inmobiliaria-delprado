# Script simple de verificación
Write-Host "=== Verificación del Sistema ===" -ForegroundColor Cyan
Write-Host ""

# Verificar XAMPP
$xamppPath = "C:\xampp\php\php.exe"

if (Test-Path $xamppPath) {
    Write-Host "✅ XAMPP encontrado!" -ForegroundColor Green
    Write-Host ""
    Write-Host "Para iniciar el servidor ejecuta:" -ForegroundColor Yellow
    Write-Host "   C:\xampp\php\php.exe -S localhost:8000" -ForegroundColor White
} else {
    Write-Host "❌ XAMPP no está instalado" -ForegroundColor Red
    Write-Host ""
    Write-Host "📥 Instala XAMPP desde: https://www.apachefriends.org/" -ForegroundColor Yellow
    Write-Host "   Luego inicia Apache y MySQL desde el Panel de Control" -ForegroundColor White
}

Write-Host ""
Write-Host "Verificando carpetas..." -ForegroundColor Yellow

# Crear carpetas si no existen
$folders = @("public\images\properties", "logs")
foreach ($folder in $folders) {
    if (-not (Test-Path $folder)) {
        New-Item -ItemType Directory -Path $folder -Force | Out-Null
        Write-Host "✅ Carpeta creada: $folder" -ForegroundColor Green
    } else {
        Write-Host "✅ Carpeta existe: $folder" -ForegroundColor Green
    }
}

Write-Host ""
Write-Host "=== Próximos Pasos ===" -ForegroundColor Cyan
Write-Host ""
Write-Host "1. Si XAMPP no está instalado, instálalo primero" -ForegroundColor White
Write-Host "2. Abre http://localhost/phpmyadmin" -ForegroundColor White
Write-Host "3. Crea base de datos: delprado_db" -ForegroundColor White
Write-Host "4. Importa el archivo database.sql" -ForegroundColor White
Write-Host "5. Inicia el servidor con el comando de arriba" -ForegroundColor White
Write-Host "6. Abre: http://localhost:8000/admin/login.php" -ForegroundColor White
Write-Host ""
Write-Host "Credenciales: admin / admin123" -ForegroundColor Yellow
Write-Host ""

Read-Host "Presiona Enter para salir"

