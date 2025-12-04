# Guía para Probar en Local - Del Prado Inmobiliaria
## Panel de Administración PHP + MySQL

Esta guía te ayudará a probar el panel de administración en tu computadora antes de subirlo a Hostinger.

---

## 📋 Requisitos Previos

Necesitas tener instalado en tu computadora:

1. **PHP 7.4 o superior** (recomendado PHP 8.0+)
2. **MySQL 5.7 o superior** (o MariaDB 10.2+)
3. **Extensiones PHP necesarias:**
   - `pdo_mysql`
   - `gd` (para procesamiento de imágenes)
   - `json` (generalmente incluida)

### Opción 1: XAMPP (Recomendado para Windows)

**XAMPP** incluye PHP, MySQL y phpMyAdmin en un solo paquete.

1. **Descargar XAMPP:**
   - Ve a: https://www.apachefriends.org/
   - Descarga la versión para Windows
   - Instala XAMPP en `C:\xampp\` (o la ubicación que prefieras)

2. **Iniciar servicios:**
   - Abre el **Panel de Control de XAMPP**
   - Inicia **Apache** (servidor web)
   - Inicia **MySQL** (base de datos)

3. **Verificar instalación:**
   - Abre tu navegador y ve a: `http://localhost`
   - Deberías ver la página de bienvenida de XAMPP

### Opción 2: PHP y MySQL por separado

Si ya tienes PHP y MySQL instalados:

1. **Verificar PHP:**
   ```bash
   php -v
   ```

2. **Verificar MySQL:**
   ```bash
   mysql --version
   ```

---

## 🚀 Pasos para Configurar el Proyecto Local

### Paso 1: Crear la Base de Datos

#### Con XAMPP (phpMyAdmin):

1. Abre tu navegador y ve a: `http://localhost/phpmyadmin`
2. Haz clic en **Nueva** (New) en el menú lateral
3. Crea una base de datos llamada: `delprado_db`
   - **Nombre**: `delprado_db`
   - **Cotejamiento**: `utf8mb4_unicode_ci`
   - Haz clic en **Crear**

4. Selecciona la base de datos `delprado_db` en el menú lateral
5. Ve a la pestaña **Importar** (Import)
6. Selecciona el archivo `database.sql` de este proyecto
7. Haz clic en **Continuar** (Go)

#### Con línea de comandos:

```bash
# Conectar a MySQL
mysql -u root -p

# Crear base de datos
CREATE DATABASE delprado_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Salir de MySQL
exit;

# Importar el SQL
mysql -u root -p delprado_db < database.sql
```

**Nota:** En XAMPP, el usuario por defecto es `root` y la contraseña está vacía (presiona Enter).

### Paso 2: Configurar `config.php` para Local

Abre el archivo `config.php` y actualiza las siguientes líneas:

```php
// Para XAMPP (usuario root, sin contraseña)
define('DB_HOST', 'localhost');
define('DB_NAME', 'delprado_db');
define('DB_USER', 'root');
define('DB_PASS', ''); // Vacío en XAMPP por defecto

// URL local
define('SITE_URL', 'http://localhost:8000'); // O el puerto que uses
```

**Si usas MySQL con contraseña:**
```php
define('DB_USER', 'root');
define('DB_PASS', 'tu_contraseña');
```

### Paso 3: Crear Carpetas Necesarias

Asegúrate de que existan estas carpetas:

```bash
# Desde la raíz del proyecto
mkdir -p public/images/properties
mkdir -p logs
```

**En Windows (PowerShell):**
```powershell
New-Item -ItemType Directory -Force -Path "public\images\properties"
New-Item -ItemType Directory -Force -Path "logs"
```

### Paso 4: Iniciar el Servidor PHP

Abre una terminal en la **raíz del proyecto** (donde está `config.php`):

#### Opción A: Servidor PHP integrado (Recomendado)

```bash
php -S localhost:8000
```

O si quieres que el servidor esté en la carpeta `public`:

```bash
php -S localhost:8000 -t public
```

**Nota:** Si usas la opción con `-t public`, necesitarás ajustar las rutas en `config.php`:
```php
define('UPLOAD_BASE_PATH', __DIR__ . '/public/images/properties');
```

#### Opción B: Con XAMPP

1. Copia todo el proyecto a `C:\xampp\htdocs\delprado\`
2. Accede desde: `http://localhost/delprado/admin/login.php`

---

## 🧪 Probar el Panel de Administración

### 1. Acceder al Login

Abre tu navegador y ve a:

```
http://localhost:8000/admin/login.php
```

O si usas XAMPP:

```
http://localhost/delprado/admin/login.php
```

### 2. Credenciales por Defecto

- **Usuario**: `admin`
- **Contraseña**: `admin123`

**⚠️ IMPORTANTE:** Cambia esta contraseña después del primer login.

### 3. Probar Funcionalidades

#### Dashboard (`/admin/` o `/admin/index.php`)
- Deberías ver estadísticas del sitio
- Total de propiedades, ventas, alquileres, etc.

#### Agregar Propiedad (`/admin/add.php`)
1. Completa el formulario
2. Sube imágenes (máximo 12)
3. Guarda la propiedad
4. Verifica que las imágenes se suban correctamente

#### Listar Propiedades (`/admin/list.php`)
- Deberías ver todas las propiedades
- Con opciones para editar y eliminar

#### Editar Propiedad (`/admin/edit.php?id=PROP001`)
- Modifica campos
- Agrega o elimina imágenes
- Guarda los cambios

#### Eliminar Propiedad (`/admin/delete.php?id=PROP001`)
- Confirma la eliminación
- Verifica que se eliminen las imágenes del servidor

---

## 🔧 Solución de Problemas

### Error: "No se puede conectar a la base de datos"

**Causas posibles:**
1. MySQL no está corriendo
   - **Solución:** Inicia MySQL desde el Panel de Control de XAMPP

2. Credenciales incorrectas en `config.php`
   - **Solución:** Verifica `DB_USER` y `DB_PASS`

3. Base de datos no existe
   - **Solución:** Crea la base de datos `delprado_db` e importa `database.sql`

### Error: "Call to undefined function imagecreatefromjpeg()"

**Causa:** La extensión GD de PHP no está habilitada.

**Solución:**
1. Abre `php.ini` (ubicación en XAMPP: `C:\xampp\php\php.ini`)
2. Busca la línea: `;extension=gd`
3. Quita el punto y coma: `extension=gd`
4. Reinicia Apache

### Error: "Permission denied" al subir imágenes

**Causa:** La carpeta `public/images/properties/` no tiene permisos de escritura.

**Solución (Windows):**
- Asegúrate de que la carpeta exista
- Verifica que no esté protegida por antivirus

**Solución (Linux/Mac):**
```bash
chmod -R 755 public/images/properties
```

### Error: "Class 'PDO' not found"

**Causa:** La extensión PDO de MySQL no está habilitada.

**Solución:**
1. Abre `php.ini`
2. Busca: `;extension=pdo_mysql`
3. Quita el punto y coma: `extension=pdo_mysql`
4. Reinicia Apache

### Las imágenes no se muestran

**Causa:** Rutas incorrectas o imágenes no subidas.

**Solución:**
1. Verifica que las imágenes existan en `public/images/properties/`
2. Verifica las rutas en la base de datos (deben ser relativas: `/images/properties/...`)
3. Asegúrate de que el servidor esté sirviendo archivos estáticos correctamente

---

## 📝 Verificar que Todo Funciona

### Checklist:

- [ ] MySQL está corriendo
- [ ] Base de datos `delprado_db` creada
- [ ] Tablas `properties` y `users` importadas
- [ ] `config.php` configurado con credenciales correctas
- [ ] Servidor PHP iniciado (`php -S localhost:8000`)
- [ ] Puedo acceder a `/admin/login.php`
- [ ] Puedo iniciar sesión con `admin` / `admin123`
- [ ] Veo el dashboard con estadísticas
- [ ] Puedo agregar una propiedad de prueba
- [ ] Las imágenes se suben correctamente
- [ ] Puedo editar una propiedad
- [ ] Puedo eliminar una propiedad

---

## 🎯 URLs Locales

Una vez configurado, accede a:

- **Login**: `http://localhost:8000/admin/login.php`
- **Dashboard**: `http://localhost:8000/admin/` o `http://localhost:8000/admin/index.php`
- **Lista de propiedades**: `http://localhost:8000/admin/list.php`
- **Agregar propiedad**: `http://localhost:8000/admin/add.php`

---

## 💡 Tips Adicionales

### Ver Logs de Errores

Los errores de PHP se guardan en:
```
logs/php_errors.log
```

### Cambiar Contraseña del Admin

Puedes cambiar la contraseña directamente en la base de datos:

```sql
-- Conectar a MySQL
mysql -u root -p delprado_db

-- Generar nuevo hash (ejecuta esto en PHP)
-- <?php echo password_hash('nueva_contraseña', PASSWORD_DEFAULT); ?>

-- Actualizar en MySQL
UPDATE users 
SET password_hash = '$2y$10$TU_NUEVO_HASH_AQUI' 
WHERE username = 'admin';
```

### Probar con Datos de Prueba

Puedes insertar propiedades de prueba directamente en la base de datos:

```sql
INSERT INTO properties (id, slug, title, city, operation, type, price, currency, images, listedAt)
VALUES (
  'PROP001',
  'propiedad-prueba',
  'Propiedad de Prueba',
  'Corrientes',
  'venta',
  'casa',
  100000,
  'USD',
  '["/images/properties/venta/propiedad-prueba/r0.jpg"]',
  NOW()
);
```

---

## ✅ Siguiente Paso: Deployment

Una vez que hayas probado todo en local y esté funcionando correctamente, sigue la guía `README-DEPLOY-HOSTINGER.md` para subir el proyecto a Hostinger.

---

¡Listo! Ahora puedes probar el panel de administración en tu computadora antes de subirlo al servidor.

