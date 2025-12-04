# 🚀 Guía Completa de Deployment en Hostinger
## Del Prado Inmobiliaria - Frontend Astro + Backend PHP

Esta guía te ayudará a desplegar tanto el **frontend Astro** como el **backend PHP** en Hostinger.

---

## 📋 Índice

1. [Preparación Local](#1-preparación-local)
2. [Configuración de Base de Datos](#2-configuración-de-base-de-datos)
3. [Configuración para Producción](#3-configuración-para-producción)
4. [Build del Frontend](#4-build-del-frontend)
5. [Subir Archivos a Hostinger](#5-subir-archivos-a-hostinger)
6. [Configuración Final](#6-configuración-final)
7. [Verificación](#7-verificación)

---

## 1. Preparación Local

### Paso 1.1: Cambiar Astro a Modo Estático

Hostinger no soporta SSR (Server-Side Rendering) de Astro, así que necesitamos generar un sitio estático.

**Edita `astro.config.mjs`:**

```javascript
import { defineConfig } from 'astro/config';
import tailwind from '@astrojs/tailwind';
import react from '@astrojs/react';

export default defineConfig({
  integrations: [tailwind(), react()],
  output: 'static', // Cambiar de 'server' a 'static'
  site: 'https://delprado.hechoencorrientes.com',
  compressHTML: true,
  build: {
    inlineStylesheets: 'auto'
  }
});
```

### Paso 1.2: Ajustar Página de Detalle de Propiedad

La página `/propiedad/[slug].astro` necesita cargarse dinámicamente con JavaScript en modo estático.

**Ya está configurada para funcionar así**, pero verifica que el JavaScript del cliente esté cargando las propiedades correctamente.

---

## 2. Configuración de Base de Datos

### Paso 2.1: Crear Base de Datos en Hostinger

1. Accede al **Panel de Control de Hostinger** (hPanel)
2. Ve a **Bases de datos MySQL** o **MySQL Databases**
3. Haz clic en **Crear nueva base de datos**
4. Completa:
   - **Nombre de la base de datos**: `delprado_db` (o el nombre que prefieras)
   - **Usuario**: Crea un usuario nuevo o usa uno existente
   - **Contraseña**: Genera una contraseña segura (guárdala)
5. Haz clic en **Crear**

**⚠️ IMPORTANTE:** Guarda estas credenciales:
- Nombre de la base de datos
- Usuario de MySQL
- Contraseña de MySQL
- Host (generalmente `localhost`)

### Paso 2.2: Importar Estructura de Base de Datos

1. En el panel de Hostinger, ve a **phpMyAdmin**
2. Selecciona la base de datos que creaste (`delprado_db`)
3. Ve a la pestaña **Importar** (Import)
4. Haz clic en **Elegir archivo** y selecciona `database.sql` de tu proyecto
5. Haz clic en **Continuar** (Go) para importar

**✅ Verificación:** Deberías ver las tablas `properties` y `users` creadas.

---

## 3. Configuración para Producción

### Paso 3.1: Actualizar `config.php`

Abre `config.php` y actualiza las siguientes líneas:

```php
// ============================================
// CONFIGURACIÓN DE BASE DE DATOS
// ============================================
define('DB_HOST', 'localhost'); // Generalmente 'localhost' en Hostinger
define('DB_NAME', 'delprado_db'); // Tu nombre de base de datos
define('DB_USER', 'u123456789_delprado'); // Tu usuario de MySQL (ejemplo de Hostinger)
define('DB_PASS', 'tu_contraseña_segura'); // Tu contraseña de MySQL

// ============================================
// CONFIGURACIÓN DEL SITIO
// ============================================
define('SITE_URL', 'https://delprado.hechoencorrientes.com'); // Tu dominio con HTTPS

// ============================================
// CONFIGURACIÓN DE PHP
// ============================================
ini_set('display_errors', 0); // Cambiar a 0 en producción (ocultar errores)
```

**⚠️ IMPORTANTE:**
- Reemplaza `u123456789_delprado` con tu usuario real de MySQL
- Reemplaza `tu_contraseña_segura` con tu contraseña real
- Reemplaza `https://delprado.hechoencorrientes.com` con tu dominio real
- Cambia `display_errors` a `0` para ocultar errores en producción

### Paso 3.2: Actualizar API para Producción

Abre `api/properties.php` y verifica que las rutas de imágenes sean correctas:

```php
// Asegúrate de que las imágenes usen rutas absolutas
$processedImages = array_map(function($imagePath) {
    if (strpos($imagePath, 'http') === 0 || strpos($imagePath, '//') === 0) {
        return $imagePath;
    }
    return SITE_URL . $imagePath;
}, $images);
```

### Paso 3.3: Actualizar JavaScript del Frontend

Abre `public/js/properties-loader.js` y verifica que detecte correctamente el entorno:

```javascript
// Debe detectar automáticamente si está en producción o desarrollo
const isDevelopment = window.location.port === '4321' || window.location.hostname === 'localhost';
const apiBase = isDevelopment ? 'http://localhost:8000' : '';
```

En producción, `apiBase` será vacío, así que las peticiones irán al mismo dominio.

---

## 4. Build del Frontend

### Paso 4.1: Instalar Dependencias (si no lo has hecho)

```bash
npm install
```

### Paso 4.2: Generar Build Estático

```bash
npm run build
```

**✅ Esto generará:**
- Carpeta `dist/` con todos los archivos estáticos
- HTML, CSS, JavaScript optimizados
- Imágenes y assets copiados

### Paso 4.3: Verificar el Build

```bash
npm run preview
```

Abre `http://localhost:4321` y verifica que todo funcione correctamente.

**⚠️ Nota:** En preview, las propiedades no se cargarán desde la API (porque el servidor PHP no está corriendo), pero puedes verificar que la estructura HTML esté correcta.

---

## 5. Subir Archivos a Hostinger

### Paso 5.1: Conectar por FTP/SFTP

1. En el panel de Hostinger, ve a **FTP** o **File Manager**
2. Obtén tus credenciales FTP:
   - **Host**: `ftp.tu-dominio.com` o la IP del servidor
   - **Usuario**: Tu usuario FTP
   - **Contraseña**: Tu contraseña FTP
   - **Puerto**: 21 (FTP) o 22 (SFTP)

3. Conecta usando un cliente FTP como:
   - **FileZilla** (gratis): https://filezilla-project.org/
   - **WinSCP** (Windows): https://winscp.net/
   - O el **File Manager** del panel de Hostinger

### Paso 5.2: Estructura de Carpetas en Hostinger

En Hostinger, la carpeta raíz del sitio web es generalmente:
- `public_html/` (para el dominio principal)
- O `public_html/subdominio/` (para subdominios)

### Paso 5.3: Subir Archivos del Backend PHP

Sube estos archivos y carpetas a `public_html/`:

```
public_html/
├── config.php              ✅ (con credenciales actualizadas)
├── db.php                  ✅
├── .htaccess               ✅
├── admin/                  ✅ (toda la carpeta)
│   ├── login.php
│   ├── index.php
│   ├── list.php
│   ├── add.php
│   ├── edit.php
│   ├── delete.php
│   ├── logout.php
│   └── _inc/
├── api/                    ✅ (toda la carpeta)
│   └── properties.php
├── helpers/                 ✅ (toda la carpeta)
│   ├── auth.php
│   ├── slugify.php
│   └── upload.php
└── logs/                    ✅ (crear carpeta vacía)
```

### Paso 5.4: Subir Archivos del Frontend Astro

Sube **todo el contenido** de la carpeta `dist/` a `public_html/`:

```
public_html/
├── index.html              ✅ (desde dist/)
├── favicon.svg              ✅
├── robots.txt               ✅
├── sitemap.xml              ✅
├── js/                      ✅ (carpeta completa desde dist/)
├── images/                  ✅ (carpeta completa desde dist/)
├── ventas/                  ✅ (carpeta completa desde dist/)
├── alquileres/              ✅ (carpeta completa desde dist/)
├── contacto/                ✅ (carpeta completa desde dist/)
├── datos-de-interes/        ✅ (carpeta completa desde dist/)
├── propiedad/               ✅ (carpeta completa desde dist/)
└── _astro/                  ✅ (carpeta completa desde dist/)
```

**⚠️ IMPORTANTE:**
- **NO** subas la carpeta `dist/` completa, solo su **contenido**
- Si ya subiste archivos del backend, **fusiona** los archivos del frontend
- Los archivos del frontend pueden sobrescribir algunos del backend (como `index.html`), eso está bien

### Paso 5.5: Subir Imágenes de Propiedades

Asegúrate de subir también las imágenes de propiedades:

```
public_html/
└── images/
    └── properties/          ✅ (toda la carpeta con subcarpetas)
        ├── venta/
        ├── alquiler/
        └── ...
```

**Nota:** Si las imágenes ya están en `public/images/properties/` localmente, cópialas a `dist/images/properties/` antes de hacer el build, o súbelas directamente a `public_html/images/properties/` en el servidor.

---

## 6. Configuración Final

### Paso 6.1: Configurar Permisos de Carpetas

Desde el **File Manager** de Hostinger o por FTP:

1. **Carpeta `images/properties/`**: Permisos `755` o `777` (para subir imágenes)
2. **Carpeta `logs/`**: Permisos `755` o `777` (para escribir logs)

**Cómo cambiar permisos:**
- En File Manager: Clic derecho en la carpeta → **Cambiar permisos** → `755` o `777`
- Por FTP: Clic derecho → **Permisos de archivo** → `755` o `777`

### Paso 6.2: Habilitar HTTPS/SSL

1. En el panel de Hostinger, ve a **SSL**
2. Activa el certificado SSL gratuito (Let's Encrypt)
3. Espera a que se active (puede tardar unos minutos)

### Paso 6.3: Forzar HTTPS (Opcional)

Una vez que SSL esté activo, edita `.htaccess` en `public_html/` y descomenta estas líneas:

```apache
# Forzar HTTPS
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

---

## 7. Verificación

### Paso 7.1: Probar el Frontend

1. Abre tu navegador y ve a: `https://tu-dominio.com`
2. Verifica que:
   - ✅ La página principal carga correctamente
   - ✅ Las propiedades se muestran (cargadas desde la API)
   - ✅ Los enlaces funcionan (`/ventas`, `/alquileres`, etc.)
   - ✅ Las imágenes se muestran correctamente

### Paso 7.2: Probar la API

1. Abre: `https://tu-dominio.com/api/properties.php`
2. Deberías ver un JSON con las propiedades
3. Si hay errores, revisa los logs en `logs/php_errors.log`

### Paso 7.3: Probar el Panel Admin

1. Ve a: `https://tu-dominio.com/admin/login.php`
2. Inicia sesión con:
   - **Usuario**: `admin`
   - **Contraseña**: `admin123`
3. **⚠️ IMPORTANTE:** Cambia la contraseña inmediatamente después del primer login

### Paso 7.4: Probar Funcionalidades

1. **Agregar propiedad:**
   - Ve a `/admin/add.php`
   - Completa el formulario
   - Sube imágenes
   - Guarda la propiedad

2. **Verificar en el frontend:**
   - Ve a `/ventas` o `/alquileres`
   - La nueva propiedad debería aparecer automáticamente (sin necesidad de rebuild)

3. **Editar propiedad:**
   - Ve a `/admin/list.php`
   - Haz clic en "Editar"
   - Modifica campos
   - Guarda los cambios

4. **Eliminar propiedad:**
   - Haz clic en "Eliminar"
   - Confirma la eliminación

---

## 🐛 Solución de Problemas

### Error: "No se puede conectar a la base de datos"

**Causas posibles:**
1. Credenciales incorrectas en `config.php`
2. MySQL no está corriendo
3. Host incorrecto (debe ser `localhost` en Hostinger)

**Solución:**
- Verifica las credenciales en el panel de Hostinger
- Asegúrate de que el usuario tenga permisos sobre la base de datos
- Verifica que `DB_HOST` sea `localhost`

### Error: "Las propiedades no aparecen en el frontend"

**Causas posibles:**
1. La API no está respondiendo
2. Error de CORS
3. JavaScript no está cargando

**Solución:**
1. Abre la consola del navegador (F12)
2. Verifica errores en la pestaña **Console**
3. Verifica peticiones en la pestaña **Network**
4. Prueba la API directamente: `https://tu-dominio.com/api/properties.php`

### Error: "Las imágenes no se muestran"

**Causas posibles:**
1. Rutas incorrectas
2. Imágenes no subidas
3. Permisos incorrectos

**Solución:**
1. Verifica que las imágenes existan en `public_html/images/properties/`
2. Verifica las rutas en la base de datos (deben ser relativas: `/images/properties/...`)
3. Verifica permisos de lectura (644 para archivos, 755 para carpetas)

### Error: "No se pueden subir imágenes"

**Causas posibles:**
1. Permisos incorrectos en `images/properties/`
2. Límite de tamaño de archivo

**Solución:**
1. Cambia permisos de `images/properties/` a `777` temporalmente
2. Verifica el límite de `upload_max_filesize` en PHP (puede ser 2MB por defecto)
3. Contacta a Hostinger si necesitas aumentar el límite

### Error: "Página 404 en `/propiedad/[slug]`"

**Causa:** En modo estático, las rutas dinámicas no se generan automáticamente.

**Solución:** Las propiedades se cargan dinámicamente con JavaScript. Verifica:
1. Que `properties-loader.js` esté cargando correctamente
2. Que la API esté respondiendo
3. Que el JavaScript esté creando las rutas dinámicamente

---

## ✅ Checklist Final

Antes de considerar el deployment completo:

- [ ] Base de datos creada e importada en Hostinger
- [ ] Credenciales actualizadas en `config.php`
- [ ] `display_errors` cambiado a `0` en `config.php`
- [ ] `SITE_URL` actualizado con tu dominio en `config.php`
- [ ] Astro configurado en modo `static`
- [ ] Build del frontend generado (`npm run build`)
- [ ] Archivos del backend subidos a `public_html/`
- [ ] Archivos del frontend (de `dist/`) subidos a `public_html/`
- [ ] Imágenes de propiedades subidas
- [ ] Permisos de carpetas configurados (755 o 777)
- [ ] SSL/HTTPS habilitado
- [ ] Frontend funcionando (`https://tu-dominio.com`)
- [ ] API funcionando (`https://tu-dominio.com/api/properties.php`)
- [ ] Panel admin funcionando (`https://tu-dominio.com/admin/login.php`)
- [ ] Contraseña de admin cambiada
- [ ] Propiedad de prueba agregada y visible en el frontend

---

## 🎯 URLs Finales

Una vez desplegado, accede a:

- **Frontend**: `https://tu-dominio.com`
- **Ventas**: `https://tu-dominio.com/ventas`
- **Alquileres**: `https://tu-dominio.com/alquileres`
- **Panel Admin**: `https://tu-dominio.com/admin/login.php`
- **API**: `https://tu-dominio.com/api/properties.php`

---

## 📝 Notas Importantes

1. **Modo Estático:** El frontend está en modo estático, pero las propiedades se cargan dinámicamente desde la API PHP usando JavaScript. Esto significa que:
   - No necesitas hacer rebuild cada vez que agregues una propiedad
   - Las propiedades aparecen automáticamente después de agregarlas en el panel admin

2. **Rutas Dinámicas:** Las páginas de detalle (`/propiedad/[slug]`) se generan dinámicamente con JavaScript. Si una propiedad no existe, mostrará un 404.

3. **Backups:** Configura backups automáticos de la base de datos desde el panel de Hostinger.

4. **Seguridad:** 
   - Cambia la contraseña del admin después del primer login
   - Considera renombrar la carpeta `/admin/` a algo menos obvio
   - Mantén `display_errors` en `0` en producción

---

¡Listo! Tu sitio web debería estar funcionando correctamente en Hostinger. 🎉

