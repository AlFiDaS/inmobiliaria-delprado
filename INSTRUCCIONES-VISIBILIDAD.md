# 📋 Instrucciones para Activar la Funcionalidad "Visible/Invisible"

## ✅ Cambios Implementados

Se ha agregado la funcionalidad de visibilidad para propiedades. Ahora puedes:
- Marcar propiedades como **visibles** o **invisibles**
- Las propiedades **invisibles** NO aparecen en el sitio web público
- Las propiedades **visibles** aparecen normalmente en la web
- Por defecto, las nuevas propiedades son **invisibles** (para que puedas cargarlas antes de publicarlas)

## 🔧 Paso 1: Agregar el Campo a la Base de Datos

Si tu base de datos ya existe, necesitas ejecutar este script SQL:

### Opción A: Desde phpMyAdmin (Recomendado)

1. Accede a phpMyAdmin en tu servidor de Hostinger
2. Selecciona tu base de datos (`u161673556_DelPradoBD` o similar)
3. Ve a la pestaña **"SQL"**
4. Copia y pega este script:

```sql
-- Agregar campo 'visible' a la tabla properties
ALTER TABLE properties 
ADD COLUMN visible TINYINT(1) DEFAULT 0 AFTER highlight;

-- Crear índice para mejorar el rendimiento
ALTER TABLE properties 
ADD INDEX idx_visible (visible);

-- Hacer visibles todas las propiedades existentes (opcional)
-- Si quieres que las propiedades actuales queden invisibles, NO ejecutes esta línea
UPDATE properties SET visible = 1 WHERE visible IS NULL OR visible = 0;
```

5. Haz clic en **"Continuar"** o **"Ejecutar"**

### Opción B: Usar el Script Incluido

Se ha creado el archivo `agregar-campo-visible.sql` en la raíz del proyecto. Puedes ejecutarlo desde phpMyAdmin.

## 📝 Paso 2: Subir Archivos Actualizados

Sube estos archivos actualizados a Hostinger:

### Archivos del Panel Admin:
- ✅ `admin/add.php`
- ✅ `admin/edit.php`
- ✅ `admin/list.php`
- ✅ `helpers/cache-bust.php` (si no lo subiste antes)

### Archivos del API:
- ✅ `api/properties.php` (ya filtra solo propiedades visibles)

### Archivos de Base de Datos:
- ✅ `database.sql` (actualizado para nuevas instalaciones)
- ✅ `agregar-campo-visible.sql` (para bases de datos existentes)

## 🎯 Cómo Usar la Funcionalidad

### Al Agregar una Nueva Propiedad:

1. Ve a `/admin/add.php`
2. Llena todos los campos de la propiedad
3. Al final del formulario verás el checkbox **"Visible en la web"**
4. **NO lo marques** si aún no tienes las fotos o quieres prepararla primero
5. Guarda la propiedad
6. Cuando esté lista, edítala y marca **"Visible en la web"**
7. ¡La propiedad aparecerá en el sitio!

### Al Editar una Propiedad Existente:

1. Ve a `/admin/list.php`
2. Haz clic en **"Editar"** en la propiedad que quieras modificar
3. En el formulario verás:
   - **✓ Visible** (verde) - si la propiedad está visible
   - **○ Oculta** (gris) - si la propiedad está oculta
4. Marca o desmarca **"Visible en la web"** según necesites
5. Guarda los cambios

### En la Lista de Propiedades:

En `/admin/list.php` verás indicadores:
- **⭐ Destacada** (naranja) - Propiedad destacada
- **✓ Visible** (verde) - Propiedad visible en la web
- **○ Oculta** (gris) - Propiedad oculta (no aparece en la web)

## 🔍 Cómo Funciona

### En el Sitio Web Público:
- Solo se muestran propiedades con `visible = 1`
- Las propiedades con `visible = 0` NO aparecen en:
  - `/ventas`
  - `/alquileres`
  - Página principal
  - Búsquedas
  - API

### En el Panel Admin:
- Se muestran **TODAS** las propiedades (visibles e invisibles)
- Puedes editarlas normalmente
- Los indicadores te muestran cuáles están visibles

## ⚠️ Importante

### Para Bases de Datos Existentes:

Si tu base de datos ya tiene propiedades, después de ejecutar el script SQL:
- **Todas las propiedades existentes quedarán INVISIBLES** por defecto (visible = 0)
- Deberás editarlas una por una y marcar "Visible en la web" para que aparezcan

**O** puedes ejecutar esta consulta SQL para hacer visibles todas las existentes:

```sql
UPDATE properties SET visible = 1;
```

## 📋 Resumen de Archivos Modificados

- ✅ `database.sql` - Agregado campo `visible`
- ✅ `agregar-campo-visible.sql` - Script para bases de datos existentes
- ✅ `admin/add.php` - Agregado checkbox de visibilidad
- ✅ `admin/edit.php` - Agregado checkbox de visibilidad
- ✅ `admin/list.php` - Agregado indicador visual de visibilidad
- ✅ `api/properties.php` - Filtro para solo mostrar propiedades visibles

## 🎉 Ventajas

1. **Preparar propiedades sin publicarlas**: Puedes cargar toda la información y fotos antes de hacerla visible
2. **Ocultar temporalmente**: Si necesitas ocultar una propiedad temporalmente, solo desmarca "Visible"
3. **Control total**: Tú decides cuándo aparece cada propiedad en la web

---

**Última actualización:** Diciembre 2025

