# Instalación de PHP para Windows

Tienes dos opciones para instalar PHP en Windows:

---

## Opción 1: XAMPP (Recomendado - Más fácil)

XAMPP incluye PHP, MySQL y phpMyAdmin en un solo paquete.

### Pasos:

1. **Descargar XAMPP:**
   - Ve a: https://www.apachefriends.org/download.html
   - Descarga la versión para Windows (PHP 8.x recomendado)
   - Ejecuta el instalador

2. **Instalar:**
   - Instala en `C:\xampp\` (ubicación por defecto)
   - Durante la instalación, marca Apache y MySQL

3. **Iniciar servicios:**
   - Abre el Panel de Control de XAMPP
   - Haz clic en "Start" para Apache
   - Haz clic en "Start" para MySQL

4. **Agregar PHP al PATH (opcional pero recomendado):**
   
   Abre PowerShell como Administrador y ejecuta:
   
   ```powershell
   # Agregar PHP al PATH del sistema
   [Environment]::SetEnvironmentVariable("Path", $env:Path + ";C:\xampp\php", "Machine")
   ```
   
   **O manualmente:**
   - Presiona `Win + R`, escribe `sysdm.cpl` y presiona Enter
   - Ve a la pestaña "Opciones avanzadas"
   - Haz clic en "Variables de entorno"
   - En "Variables del sistema", selecciona "Path" y haz clic en "Editar"
   - Haz clic en "Nuevo" y agrega: `C:\xampp\php`
   - Haz clic en "Aceptar" en todas las ventanas
   - **Reinicia PowerShell** para que los cambios surtan efecto

5. **Verificar instalación:**
   
   Abre una nueva terminal PowerShell y ejecuta:
   ```powershell
   php -v
   ```
   
   Deberías ver la versión de PHP.

---

## Opción 2: PHP Standalone (Solo PHP)

Si prefieres instalar solo PHP sin XAMPP:

### Pasos:

1. **Descargar PHP:**
   - Ve a: https://windows.php.net/download/
   - Descarga la versión "Thread Safe" en ZIP (ej: `php-8.2.x-Win32-vs16-x64.zip`)
   - Extrae el ZIP en `C:\php\`

2. **Configurar PHP:**
   - Copia `php.ini-development` y renómbralo a `php.ini`
   - Edita `php.ini` y descomenta (quita el `;`) estas líneas:
     ```
     extension=pdo_mysql
     extension=gd
     extension=mbstring
     ```

3. **Agregar PHP al PATH:**
   
   Abre PowerShell como Administrador:
   ```powershell
   [Environment]::SetEnvironmentVariable("Path", $env:Path + ";C:\php", "Machine")
   ```
   
   O manualmente (ver instrucciones en Opción 1, paso 4)

4. **Instalar MySQL por separado:**
   - Necesitarás instalar MySQL desde: https://dev.mysql.com/downloads/installer/

---

## Verificar que todo funciona

Después de instalar, abre una **nueva terminal PowerShell** y ejecuta:

```powershell
# Verificar PHP
php -v

# Verificar extensiones
php -m | Select-String -Pattern "pdo_mysql|gd|mbstring"
```

---

## Iniciar el servidor

Una vez que PHP esté instalado y en el PATH, desde la raíz del proyecto ejecuta:

```powershell
php -S localhost:8000
```

---

## Si prefieres usar XAMPP sin agregar al PATH

Si instalaste XAMPP pero no quieres agregar PHP al PATH, puedes usar la ruta completa:

```powershell
C:\xampp\php\php.exe -S localhost:8000
```

O crear un script `.bat` para facilitarlo (ver `start-server.bat`).

---

## Problemas comunes

### "php no se reconoce como comando"
- **Solución:** Agregaste PHP al PATH pero no reiniciaste PowerShell. Cierra y abre una nueva terminal.
- **Alternativa:** Usa la ruta completa: `C:\xampp\php\php.exe -S localhost:8000`

### "Extension pdo_mysql not found"
- **Solución:** Edita `php.ini` y descomenta `extension=pdo_mysql`
- En XAMPP: `C:\xampp\php\php.ini`
- Reinicia Apache si usas XAMPP

### "MySQL no está corriendo"
- Si usas XAMPP: Inicia MySQL desde el Panel de Control de XAMPP
- Si instalaste MySQL por separado: Inícialo desde Servicios de Windows

---

¡Una vez instalado, vuelve a ejecutar `php -S localhost:8000`!

