# 🔧 Solucionar Error de Base de Datos en Hostinger

## Error Actual
```
Access denied for user 'u161673556_DelPrado'@'localhost' to database 'delprado_db'
```

## 🔍 Causa del Problema

En Hostinger, el nombre de la base de datos generalmente incluye el prefijo del usuario. Por ejemplo:
- Usuario: `u161673556_DelPrado`
- Base de datos probable: `u161673556_delprado_db` (no `delprado_db`)

## ✅ Solución Paso a Paso

### Paso 1: Verificar el Nombre Real de la Base de Datos

1. **Accede al Panel de Control de Hostinger** (hPanel)
2. Ve a **Bases de datos MySQL** o **MySQL Databases**
3. Busca la lista de bases de datos creadas
4. **Copia el nombre EXACTO** de tu base de datos (probablemente sea `u161673556_delprado_db` o similar)

### Paso 2: Verificar que el Usuario Tenga Permisos

1. En la misma sección de **Bases de datos MySQL**
2. Busca la sección **"Usuarios de MySQL"** o **"MySQL Users"**
3. Verifica que el usuario `u161673556_DelPrado` esté **asociado** a la base de datos
4. Si no está asociado:
   - Haz clic en **"Agregar usuario a base de datos"** o **"Add user to database"**
   - Selecciona el usuario: `u161673556_DelPrado`
   - Selecciona la base de datos: `u161673556_delprado_db` (o el nombre que veas)
   - Haz clic en **"Agregar"** o **"Add"**

### Paso 3: Actualizar config.php

Abre `config.php` y actualiza el nombre de la base de datos:

```php
// ANTES (incorrecto):
define('DB_NAME', 'delprado_db');

// DESPUÉS (correcto - usa el nombre real de Hostinger):
define('DB_NAME', 'u161673556_delprado_db'); // O el nombre exacto que veas en el panel
```

### Paso 4: Verificar Credenciales Completas

Asegúrate de que `config.php` tenga:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'u161673556_delprado_db'); // ← Nombre EXACTO de Hostinger
define('DB_USER', 'u161673556_DelPrado'); // ← Usuario EXACTO
define('DB_PASS', 'Delprado124!'); // ← Contraseña correcta
```

### Paso 5: Probar la Conexión

1. Sube el archivo `config.php` actualizado a Hostinger
2. Intenta acceder al panel admin: `https://delprado.hechoencorrientes.com/admin/login.php`
3. Si sigue fallando, verifica los logs en `logs/php_errors.log`

---

## 🐛 Si Aún No Funciona

### Opción A: Crear Nueva Base de Datos

1. En el panel de Hostinger, ve a **Bases de datos MySQL**
2. Haz clic en **"Crear nueva base de datos"**
3. Nombre sugerido: `u161673556_delprado` (Hostinger agregará el prefijo automáticamente)
4. Crea un usuario nuevo o usa el existente
5. **Asocia el usuario a la base de datos**
6. Importa `database.sql` desde phpMyAdmin
7. Actualiza `config.php` con el nuevo nombre

### Opción B: Verificar en phpMyAdmin

1. Accede a **phpMyAdmin** desde el panel de Hostinger
2. En el menú lateral, verás todas las bases de datos disponibles
3. **Copia el nombre exacto** de la base de datos que quieres usar
4. Actualiza `config.php` con ese nombre

### Opción C: Verificar Permisos del Usuario

En phpMyAdmin:
1. Ve a la pestaña **"Usuarios"** o **"User accounts"**
2. Busca el usuario `u161673556_DelPrado`
3. Haz clic en **"Editar privilegios"** o **"Edit privileges"**
4. Verifica que tenga permisos sobre la base de datos correcta
5. Si no tiene permisos, haz clic en **"Agregar privilegios"** y selecciona:
   - SELECT
   - INSERT
   - UPDATE
   - DELETE
   - CREATE
   - ALTER
   - INDEX

---

## 📝 Checklist

- [ ] Verifiqué el nombre exacto de la base de datos en el panel de Hostinger
- [ ] El usuario está asociado a la base de datos
- [ ] Actualicé `DB_NAME` en `config.php` con el nombre correcto
- [ ] Las credenciales (usuario y contraseña) son correctas
- [ ] Subí el archivo `config.php` actualizado al servidor
- [ ] Probé la conexión nuevamente

---

## 💡 Nota Importante

En Hostinger, los nombres de base de datos y usuarios suelen tener un prefijo como `u161673556_`. Asegúrate de usar el nombre **completo** tal como aparece en el panel, no solo la parte después del guion bajo.

**Ejemplo:**
- ❌ Incorrecto: `delprado_db`
- ✅ Correcto: `u161673556_delprado_db`

---

¡Con estos pasos deberías poder resolver el error de conexión!

