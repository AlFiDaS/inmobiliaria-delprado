# 🚀 Pasos Siguientes - Configuración Local

## ✅ Checklist de Instalación

### Paso 1: Instalar XAMPP
- [ ] Descargar XAMPP desde: https://www.apachefriends.org/
- [ ] Instalar en `C:\xampp\`
- [ ] Iniciar Apache y MySQL desde el Panel de Control de XAMPP

### Paso 2: Agregar PHP al PATH
**Opción A - Script automático (Recomendado):**
1. Abre PowerShell **como Administrador** (clic derecho → Ejecutar como administrador)
2. Navega a la carpeta del proyecto:
   ```powershell
   cd C:\Users\alefi\Documents\Frontend_dev\303_DelPrado
   ```
3. Ejecuta:
   ```powershell
   .\agregar-php-al-path.ps1
   ```
4. **Cierra y vuelve a abrir PowerShell** (importante)

**Opción B - Manual:**
1. Presiona `Win + R`, escribe `sysdm.cpl` y presiona Enter
2. Ve a "Opciones avanzadas" → "Variables de entorno"
3. En "Variables del sistema", selecciona "Path" → "Editar"
4. Haz clic en "Nuevo" y agrega: `C:\xampp\php`
5. Acepta todos los cambios
6. **Cierra y vuelve a abrir PowerShell**

### Paso 3: Verificar PHP
Abre una **nueva terminal PowerShell** y ejecuta:
```powershell
php -v
```
Deberías ver la versión de PHP. Si funciona, continúa.

### Paso 4: Crear Base de Datos
1. Abre tu navegador y ve a: `http://localhost/phpmyadmin`
2. Haz clic en **"Nueva"** (New) en el menú lateral
3. Crea una base de datos:
   - **Nombre**: `delprado_db`
   - **Cotejamiento**: `utf8mb4_unicode_ci`
   - Haz clic en **"Crear"**
4. Selecciona la base de datos `delprado_db`
5. Ve a la pestaña **"Importar"** (Import)
6. Selecciona el archivo `database.sql` de este proyecto
7. Haz clic en **"Continuar"** (Go)

### Paso 5: Verificar Configuración
Ejecuta el script de verificación:
```powershell
php check-local.php
```

Este script te dirá si falta algo.

### Paso 6: Iniciar el Servidor
Desde la raíz del proyecto, ejecuta:
```powershell
php -S localhost:8000
```

O usa el script automático:
```powershell
.\start-server.ps1
```

### Paso 7: Acceder al Panel
Abre tu navegador en:
```
http://localhost:8000/admin/login.php
```

**Credenciales:**
- Usuario: `admin`
- Contraseña: `admin123`

---

## 🎯 Resumen Rápido

```powershell
# 1. Instalar XAMPP (descargar e instalar manualmente)
# 2. Agregar PHP al PATH (ejecutar como Admin):
.\agregar-php-al-path.ps1

# 3. Cerrar y abrir nueva terminal PowerShell

# 4. Verificar PHP:
php -v

# 5. Crear BD en phpMyAdmin (http://localhost/phpmyadmin)

# 6. Verificar configuración:
php check-local.php

# 7. Iniciar servidor:
php -S localhost:8000

# 8. Abrir en navegador:
# http://localhost:8000/admin/login.php
```

---

## ❓ ¿Problemas?

### "php no se reconoce"
- Asegúrate de haber cerrado y vuelto a abrir PowerShell después de agregar al PATH
- O usa la ruta completa: `C:\xampp\php\php.exe -S localhost:8000`

### "No se puede conectar a la base de datos"
- Verifica que MySQL esté corriendo en XAMPP
- Verifica las credenciales en `config.php` (usuario: `root`, contraseña: vacía)

### "Extension pdo_mysql not found"
- Edita `C:\xampp\php\php.ini`
- Busca `;extension=pdo_mysql` y quita el `;` (debe quedar `extension=pdo_mysql`)
- Reinicia Apache en XAMPP

---

## 📚 Archivos de Ayuda

- `INSTALAR-PHP.md` - Guía detallada de instalación
- `README-LOCAL.md` - Guía completa para probar en local
- `check-local.php` - Script de verificación
- `start-server.ps1` - Script para iniciar servidor automáticamente

---

¡Sigue estos pasos y estarás listo para probar el panel!

