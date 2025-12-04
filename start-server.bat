@echo off
REM Script para iniciar el servidor PHP local
REM Busca PHP en ubicaciones comunes

echo Buscando PHP...

REM Verificar si PHP está en el PATH
where php >nul 2>&1
if %errorlevel% == 0 (
    echo PHP encontrado en PATH
    php -S localhost:8000 router.php
    goto :end
)

REM Verificar XAMPP
if exist "C:\xampp\php\php.exe" (
    echo PHP encontrado en XAMPP
    C:\xampp\php\php.exe -S localhost:8000 router.php
    goto :end
)

REM Verificar PHP standalone
if exist "C:\php\php.exe" (
    echo PHP encontrado en C:\php
    C:\php\php.exe -S localhost:8000 router.php
    goto :end
)

echo.
echo ERROR: PHP no encontrado
echo.
echo Por favor instala PHP o XAMPP:
echo 1. Descarga XAMPP desde: https://www.apachefriends.org/
echo 2. O instala PHP desde: https://windows.php.net/download/
echo.
echo Ver INSTALAR-PHP.md para instrucciones detalladas.
echo.
pause

:end

