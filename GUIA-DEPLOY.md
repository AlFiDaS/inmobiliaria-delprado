# 📦 Guía de Despliegue - Qué NO Eliminar

## ⚠️ PROBLEMA COMÚN: Las imágenes se rompen después del deploy

Cuando haces cambios en la web, haces build, eliminas todos los archivos y subes todo desde cero a Hostinger, **las imágenes se rompen** porque:

1. Las imágenes subidas desde el panel admin se guardan directamente en el servidor en: `public_html/images/properties/`
2. Cuando haces build, solo se incluyen las imágenes que ya estaban en `public/images/` en ese momento
3. Si eliminas **TODO** antes de subir, también eliminas las imágenes nuevas que subiste desde el panel admin

---

## ✅ SOLUCIÓN: Qué carpetas NO debes eliminar

Al hacer deploy a Hostinger, **NUNCA elimines estas carpetas:**

### 1. 📁 `images/` (La más importante)
```
public_html/images/
├── properties/          ← AQUÍ están las imágenes subidas desde el panel admin
│   ├── venta/
│   │   └── [slug-propiedad]/
│   └── alquiler/
│       └── [slug-propiedad]/
├── alquiler.jpg
├── contacto.jpg
├── delprado-header.jpg
└── ... (otras imágenes estáticas)
```

**¿Por qué?** 
- Las imágenes de propiedades subidas desde el panel admin se guardan en `public_html/images/properties/`
- Estas imágenes NO están en el build de Astro, se suben directamente al servidor
- Si las eliminas, tendrás que volver a subirlas una por una

### 2. 📁 `admin/`
```
public_html/admin/
├── add.php
├── edit.php
├── list.php
└── ... (archivos del panel admin)
```

**¿Por qué?**
- El panel admin es independiente del build de Astro
- Necesitas mantenerlo funcionando

### 3. 📁 `api/`
```
public_html/api/
├── properties.php
└── sync.php
```

**¿Por qué?**
- Los endpoints de la API son necesarios para el panel admin y para cargar propiedades dinámicamente

### 4. 📁 `helpers/`
```
public_html/helpers/
├── auth.php
├── slugify.php
└── upload.php
```

**¿Por qué?**
- Contiene funciones PHP necesarias para el panel admin

### 5. 📁 `logs/`
```
public_html/logs/
└── php_errors.log
```

**¿Por qué?**
- Es útil para depurar errores
- Puedes eliminar el contenido pero mantener la carpeta

### 6. 📄 Archivos PHP de configuración
```
public_html/
├── config.php          ← NO eliminar
├── db.php             ← NO eliminar
└── router.php         ← NO eliminar (si lo usas)
```

---

## 🚀 PROCESO DE DEPLOY CORRECTO

### Opción 1: Deploy conservador (RECOMENDADO)

1. **Hacer build localmente:**
   ```bash
   npm run build
   ```

2. **En Hostinger, eliminar SOLO estas carpetas del build:**
   - `_astro/` (si existe)
   - `alquileres/`
   - `contacto/`
   - `datos-de-interes/`
   - `ventas/`
   - `propiedad.html/`
   - Archivos HTML en la raíz: `index.html`, etc.
   - `js/` (si está en la raíz, no confundir con el que puede estar dentro de public)
   - Archivos CSS generados

3. **Subir el contenido de `dist/`** (excepto `images/` si ya existe)

4. **Verificar que `images/` NO se haya eliminado**

### Opción 2: Deploy con backup de imágenes

1. **Antes de hacer nada, descargar la carpeta `images/`:**
   - Conectarse por FTP/File Manager
   - Descargar `public_html/images/` completa
   - Guardarla en un lugar seguro

2. **Hacer build:**
   ```bash
   npm run build
   ```

3. **Eliminar todo y subir `dist/` completo**

4. **Restaurar la carpeta `images/` descargada**

5. **Verificar que todo funcione**

### Opción 3: Deploy manual de archivos específicos

En lugar de eliminar todo, sube solo los archivos que cambiaron:

1. **Hacer build:**
   ```bash
   npm run build
   ```

2. **Subir solo los archivos nuevos/modificados:**
   - Archivos HTML nuevos o modificados
   - Archivos CSS/JS nuevos o modificados
   - NO tocar `images/`

---

## 📋 CHECKLIST ANTES DE ELIMINAR ARCHIVOS

Antes de eliminar cualquier cosa en Hostinger, verifica:

- [ ] ¿Hice backup de `images/`?
- [ ] ¿Sé qué archivos cambié exactamente?
- [ ] ¿Necesito realmente eliminar TODO o solo algunos archivos?

---

## 🔍 Cómo verificar dónde están las imágenes

### En el código:
Las imágenes subidas desde el panel admin se guardan en:
```php
// config.php línea 55
$uploadBasePath = $_SERVER['DOCUMENT_ROOT'] . '/images/properties';
```

En Hostinger, `$_SERVER['DOCUMENT_ROOT']` apunta a `public_html`, entonces:
- Ruta completa: `public_html/images/properties/`
- URL pública: `https://tu-dominio.com/images/properties/`

### En la base de datos:
Las rutas se guardan como rutas relativas:
```sql
/images/properties/venta/casa-belgrano/r0.jpg
/images/properties/alquiler/pje-torrent-970/r0.jpg
```

---

## 💡 RECOMENDACIÓN FINAL

**La forma más segura de hacer deploy:**

1. Usa un cliente FTP como FileZilla
2. Conecta a tu servidor de Hostinger
3. Ve a `public_html/`
4. **Selecciona SOLO los archivos que cambiaron** (archivos HTML, CSS, JS del build)
5. **NO toques la carpeta `images/`**
6. Sube los archivos nuevos

De esta forma:
- ✅ Las imágenes subidas desde el panel admin se mantienen
- ✅ Solo actualizas lo que realmente cambió
- ✅ Menos riesgo de romper algo

---

## ❓ ¿Qué pasa si ya eliminé las imágenes?

Si ya eliminaste las imágenes por error:

1. **NO es posible recuperarlas** (a menos que tengas un backup)
2. Tendrás que volver a subirlas desde el panel admin:
   - Edita cada propiedad
   - Vuelve a subir las imágenes
   - Guarda los cambios

3. **Para evitar esto en el futuro:** Sigue esta guía y NUNCA elimines la carpeta `images/`

---

## 📝 Resumen rápido

### ✅ SÍ puedes eliminar:
- Archivos HTML generados por Astro
- Archivos CSS/JS generados
- Carpetas de páginas (`alquileres/`, `ventas/`, etc.)

### ❌ NO elimines NUNCA:
- `images/` (especialmente `images/properties/`)
- `admin/`
- `api/`
- `helpers/`
- `config.php`
- `db.php`

---

**Última actualización:** Diciembre 2025

