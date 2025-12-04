# 🔧 Preparar Proyecto para Producción en Hostinger

## Pasos Rápidos

### 1. Cambiar Astro a Modo Estático

Edita `astro.config.mjs`:

```javascript
output: 'static', // Cambiar de 'server' a 'static'
```

### 2. Actualizar config.php

Cambia estas líneas en `config.php`:

```php
define('DB_USER', 'tu_usuario_mysql_hostinger');
define('DB_PASS', 'tu_contraseña_mysql');
define('SITE_URL', 'https://delprado.hechoencorrientes.com');
ini_set('display_errors', 0); // Ocultar errores en producción
```

### 3. Generar Build

```bash
npm run build
```

### 4. Subir Archivos

- **Backend PHP**: Sube `admin/`, `api/`, `helpers/`, `config.php`, `db.php`, `.htaccess` a `public_html/`
- **Frontend Astro**: Sube todo el contenido de `dist/` a `public_html/`
- **Imágenes**: Sube `public/images/properties/` a `public_html/images/properties/`

---

## ⚠️ Nota Importante sobre Páginas Dinámicas

La página `/propiedad/[slug].astro` actualmente usa fetch en el servidor, lo cual no funciona en modo estático.

**Solución:** Necesitamos ajustar esta página para que cargue los datos con JavaScript del lado del cliente.

Ver `DEPLOY-HOSTINGER-COMPLETO.md` para instrucciones detalladas.

