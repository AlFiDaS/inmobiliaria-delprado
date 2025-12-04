# 🔧 Solucionar Error 404 de Imágenes

## Problema
Las imágenes aparecen rotas (404) cuando se suben propiedades en producción.

## Causa
Las imágenes se están guardando en una ubicación diferente a donde el servidor web las busca.

## ✅ Solución

### Paso 1: Verificar Estructura de Carpetas en Hostinger

En Hostinger, la estructura debería ser:
```
public_html/
├── images/
│   └── properties/
│       ├── venta/
│       └── alquiler/
├── admin/
├── api/
└── ...
```

### Paso 2: Verificar que las Imágenes Existan

1. Accede al **File Manager** de Hostinger
2. Navega a `public_html/images/properties/`
3. Verifica que existan las carpetas:
   - `venta/`
   - `alquiler/`
4. Dentro de cada carpeta deberían estar las subcarpetas por propiedad (ej: `alquiler-1/`)

### Paso 3: Verificar Permisos

Las carpetas deben tener permisos de escritura:
- `images/properties/` → `755` o `777`
- Las subcarpetas también deben tener permisos de escritura

### Paso 4: Verificar Rutas en la Base de Datos

1. Accede a **phpMyAdmin**
2. Selecciona tu base de datos
3. Ve a la tabla `properties`
4. Revisa la columna `images` de una propiedad
5. Las rutas deberían ser: `/images/properties/venta/[slug]/r0.jpg`

### Paso 5: Si las Imágenes No Existen

Si las imágenes no existen físicamente en el servidor:

1. **Opción A: Subir las imágenes manualmente**
   - Sube las imágenes a `public_html/images/properties/[operation]/[slug]/`
   - Usa nombres como `r0.jpg`, `r1.jpg`, etc.

2. **Opción B: Volver a subir la propiedad**
   - Elimina la propiedad desde el panel admin
   - Vuelve a agregarla con las imágenes
   - Esto creará las carpetas y subirá las imágenes correctamente

### Paso 6: Verificar Configuración

El archivo `config.php` ahora detecta automáticamente si está en producción y ajusta la ruta. Verifica que:

```php
define('UPLOAD_BASE_PATH', ...); // Debe apuntar a public_html/images/properties
```

### Paso 7: Probar Subida de Nueva Imagen

1. Ve a `/admin/add.php`
2. Agrega una propiedad de prueba
3. Sube una imagen
4. Verifica que:
   - La imagen se guarde en `public_html/images/properties/[operation]/[slug]/r0.jpg`
   - La ruta en la base de datos sea `/images/properties/[operation]/[slug]/r0.jpg`
   - La imagen se muestre correctamente en el panel admin

---

## 🐛 Si Aún No Funciona

### Verificar Logs

Revisa los logs de PHP en `logs/php_errors.log` para ver errores de subida.

### Verificar que la Carpeta Exista

Crea manualmente la carpeta si no existe:
```
public_html/images/properties/
```

Y dale permisos `755` o `777`.

### Verificar Ruta en config.php

Puedes agregar un script de prueba temporal:

```php
<?php
require_once 'config.php';
echo "UPLOAD_BASE_PATH: " . UPLOAD_BASE_PATH . "\n";
echo "Existe: " . (is_dir(UPLOAD_BASE_PATH) ? 'Sí' : 'No') . "\n";
echo "Es escribible: " . (is_writable(UPLOAD_BASE_PATH) ? 'Sí' : 'No') . "\n";
echo "DOCUMENT_ROOT: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
?>
```

---

¡Con estos pasos deberías poder resolver el problema de las imágenes 404!

