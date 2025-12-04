# Script para preparar el proyecto para producción en Hostinger
# Ejecuta: .\preparar-produccion.ps1

Write-Host "🚀 Preparando proyecto para producción en Hostinger..." -ForegroundColor Cyan
Write-Host ""

# Verificar que estamos en la raíz del proyecto
if (-not (Test-Path "astro.config.mjs")) {
    Write-Host "❌ Error: No se encontró astro.config.mjs. Ejecuta este script desde la raíz del proyecto." -ForegroundColor Red
    exit 1
}

# Paso 1: Cambiar Astro a modo estático
Write-Host "📝 Paso 1: Configurando Astro para modo estático..." -ForegroundColor Yellow
$astroConfig = Get-Content "astro.config.mjs" -Raw

if ($astroConfig -match "output:\s*['\`"]server['\`"]") {
    $astroConfig = $astroConfig -replace "output:\s*['\`"]server['\`"]", "output: 'static'"
    Set-Content "astro.config.mjs" -Value $astroConfig
    Write-Host "✅ Cambiado output de 'server' a 'static'" -ForegroundColor Green
} elseif ($astroConfig -match "output:\s*['\`"]static['\`"]") {
    Write-Host "✅ Astro ya está configurado en modo 'static'" -ForegroundColor Green
} else {
    Write-Host "⚠️  No se encontró configuración de output en astro.config.mjs" -ForegroundColor Yellow
    Write-Host "   Asegúrate de que tenga: output: 'static'" -ForegroundColor Yellow
}

Write-Host ""

# Paso 2: Verificar config.php
Write-Host "📝 Paso 2: Verificando config.php..." -ForegroundColor Yellow
if (Test-Path "config.php") {
    $configContent = Get-Content "config.php" -Raw
    
    if ($configContent -match "display_errors.*1") {
        Write-Host "⚠️  ADVERTENCIA: display_errors está en 1 (modo desarrollo)" -ForegroundColor Yellow
        Write-Host "   Cambia a 0 en producción para ocultar errores" -ForegroundColor Yellow
    }
    
    if ($configContent -match "SITE_URL.*localhost") {
        Write-Host "⚠️  ADVERTENCIA: SITE_URL apunta a localhost" -ForegroundColor Yellow
        Write-Host "   Actualiza con tu dominio de producción: https://tu-dominio.com" -ForegroundColor Yellow
    }
    
    if ($configContent -match "DB_USER.*root" -and $configContent -notmatch "DB_PASS.*['\`"].*['\`"]") {
        Write-Host "⚠️  ADVERTENCIA: DB_USER es 'root' y DB_PASS está vacío" -ForegroundColor Yellow
        Write-Host "   Actualiza con las credenciales de MySQL de Hostinger" -ForegroundColor Yellow
    }
    
    Write-Host "✅ config.php encontrado" -ForegroundColor Green
} else {
    Write-Host "❌ Error: No se encontró config.php" -ForegroundColor Red
}

Write-Host ""

# Paso 3: Verificar que node_modules existe
Write-Host "📝 Paso 3: Verificando dependencias..." -ForegroundColor Yellow
if (Test-Path "node_modules") {
    Write-Host "✅ node_modules encontrado" -ForegroundColor Green
} else {
    Write-Host "⚠️  node_modules no encontrado. Ejecutando npm install..." -ForegroundColor Yellow
    npm install
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✅ Dependencias instaladas" -ForegroundColor Green
    } else {
        Write-Host "❌ Error al instalar dependencias" -ForegroundColor Red
        exit 1
    }
}

Write-Host ""

# Paso 4: Generar build
Write-Host "📝 Paso 4: Generando build de producción..." -ForegroundColor Yellow
Write-Host "   Esto puede tardar unos minutos..." -ForegroundColor Gray

npm run build

if ($LASTEXITCODE -eq 0) {
    Write-Host "✅ Build generado correctamente" -ForegroundColor Green
    
    if (Test-Path "dist") {
        $distFiles = (Get-ChildItem -Path "dist" -Recurse -File).Count
        Write-Host "   Archivos generados: $distFiles" -ForegroundColor Gray
    }
} else {
    Write-Host "❌ Error al generar el build" -ForegroundColor Red
    Write-Host "   Revisa los errores arriba" -ForegroundColor Yellow
    exit 1
}

Write-Host ""

# Paso 5: Resumen
Write-Host "✅ Preparación completada!" -ForegroundColor Green
Write-Host ""
Write-Host "📋 Próximos pasos:" -ForegroundColor Cyan
Write-Host "   1. Verifica config.php y actualiza:" -ForegroundColor White
Write-Host "      - DB_USER y DB_PASS (credenciales de MySQL de Hostinger)" -ForegroundColor Gray
Write-Host "      - SITE_URL (tu dominio con HTTPS)" -ForegroundColor Gray
Write-Host "      - display_errors = 0 (ocultar errores)" -ForegroundColor Gray
Write-Host ""
Write-Host "   2. Sube archivos a Hostinger:" -ForegroundColor White
Write-Host "      - Backend: admin/, api/, helpers/, config.php, db.php, .htaccess" -ForegroundColor Gray
Write-Host "      - Frontend: Todo el contenido de dist/ a public_html/" -ForegroundColor Gray
Write-Host "      - Imágenes: public/images/properties/ a public_html/images/properties/" -ForegroundColor Gray
Write-Host ""
Write-Host "   3. Configura permisos:" -ForegroundColor White
Write-Host "      - images/properties/ → 755 o 777" -ForegroundColor Gray
Write-Host "      - logs/ → 755 o 777" -ForegroundColor Gray
Write-Host ""
Write-Host "   4. Verifica:" -ForegroundColor White
Write-Host "      - https://tu-dominio.com (frontend)" -ForegroundColor Gray
Write-Host "      - https://tu-dominio.com/api/properties.php (API)" -ForegroundColor Gray
Write-Host "      - https://tu-dominio.com/admin/login.php (panel admin)" -ForegroundColor Gray
Write-Host ""
Write-Host "📖 Para más detalles, consulta: DEPLOY-HOSTINGER-COMPLETO.md" -ForegroundColor Cyan
Write-Host ""

