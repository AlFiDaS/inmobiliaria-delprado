# Guía de Deployment - Del Prado Inmobiliaria
## Panel de Administración PHP + MySQL para Hostinger

Esta guía te ayudará a desplegar el panel de administración en Hostinger paso a paso.

---

## 📋 Checklist de Deployment

### 1. Crear Base de Datos MySQL en Hostinger

1. Accede al panel de control de Hostinger (hPanel)
2. Ve a **Bases de datos MySQL** o **MySQL Databases**
3. Crea una nueva base de datos:
   - **Nombre de la base de datos**: `delprado_db` (o el nombre que prefieras)
   - **Usuario**: Crea un usuario nuevo o usa uno existente
   - **Contraseña**: Genera una contraseña segura
   - **⚠️ IMPORTANTE**: Guarda estas credenciales, las necesitarás en el paso 3

### 2. Importar Estructura de Base de Datos

1. En el panel de Hostinger, ve a **phpMyAdmin**
2. Selecciona la base de datos que creaste
3. Ve a la pestaña **Importar** (Import)
4. Selecciona el archivo `database.sql` de este proyecto
5. Haz clic en **Continuar** (Go) para importar

**Alternativa (línea de comandos):**
```bash
mysql -u tu_usuario -p delprado_db < database.sql
```

### 3. Configurar Credenciales de Base de Datos

1. Abre el archivo `config.php` en tu editor
2. Actualiza las siguientes líneas con tus credenciales de MySQL de Hostinger:

```php
define('DB_HOST', 'localhost'); // Generalmente 'localhost' en Hostinger
define('DB_NAME', 'delprado_db'); // Nombre de tu base de datos
define('DB_USER', 'u123456789_delprado'); // Tu usuario de MySQL (ejemplo de Hostinger)
define('DB_PASS', 'tu_contraseña_segura'); // Tu contraseña de MySQL
```

3. Actualiza también la URL del sitio:

```php
define('SITE_URL', 'https://delprado.hechoencorrientes.com');
```

### 4. Subir Archivos al Servidor

1. Conecta por **FTP/SFTP** a tu servidor Hostinger
2. Navega a la carpeta `public_html` (o `htdocs` según tu hosting)
3. Sube todos los archivos del proyecto manteniendo la estructura de carpetas:

```
public_html/
├── config.php
├── db.php
├── database.sql (opcional, no es necesario subirlo)
├── .htaccess
├── admin/
│   ├── login.php
│   ├── index.php
│   ├── list.php
│   ├── add.php
│   ├── edit.php
│   ├── delete.php
│   ├── logout.php
│   └── _inc/
│       ├── header.php
│       └── footer.php
├── helpers/
│   ├── auth.php
│   ├── slugify.php
│   └── upload.php
└── public/
    └── images/
        └── properties/
```

**Nota**: Los archivos PHP del panel admin van directamente en `public_html/admin/`, no en una subcarpeta `public/`.

### 5. Configurar Permisos de Carpetas

1. Asegúrate de que la carpeta `public/images/properties/` tenga permisos de escritura:
   - Desde el **Administrador de archivos** de Hostinger
   - Clic derecho en `images/properties`
   - Cambia permisos a `755` o `777` (temporalmente para crear subcarpetas)

2. Crea la carpeta `logs/` si no existe y dale permisos de escritura:
   - Permisos: `755`

### 6. Cambiar Contraseña del Administrador

1. Accede a `https://tu-dominio.com/admin/login.php`
2. Usa las credenciales por defecto:
   - **Usuario**: `admin`
   - **Contraseña**: `admin123`
3. **⚠️ IMPORTANTE**: Cambia la contraseña inmediatamente después del primer login

**Para cambiar la contraseña manualmente en la base de datos:**

```sql
UPDATE users 
SET password_hash = '$2y$10$TU_HASH_AQUI' 
WHERE username = 'admin';
```

Para generar un nuevo hash en PHP:
```php
<?php
echo password_hash('tu_nueva_contraseña', PASSWORD_DEFAULT);
?>
```

### 7. Probar el Sistema

1. **Probar login:**
   - Ve a `https://tu-dominio.com/admin/login.php`
   - Inicia sesión con las credenciales por defecto

2. **Probar dashboard:**
   - Deberías ver el dashboard con estadísticas

3. **Probar agregar propiedad:**
   - Ve a `/admin/add.php`
   - Completa el formulario
   - Sube imágenes
   - Guarda la propiedad

4. **Probar listado:**
   - Ve a `/admin/list.php`
   - Verifica que la propiedad aparezca

5. **Probar editar:**
   - Haz clic en "Editar" de una propiedad
   - Modifica algunos campos
   - Guarda los cambios

6. **Probar eliminar:**
   - Haz clic en "Eliminar" de una propiedad
   - Confirma la eliminación

### 8. Habilitar HTTPS (Recomendado)

1. En el panel de Hostinger, ve a **SSL**
2. Activa el certificado SSL gratuito (Let's Encrypt)
3. Fuerza redirección HTTPS desde `.htaccess` (descomentar las líneas en el archivo)

---

## 🔒 Seguridad Adicional

### Recomendaciones:

1. **Mover config.php fuera de public_html:**
   - Si es posible, mueve `config.php` a un nivel superior
   - Actualiza las rutas en `db.php` y otros archivos

2. **Cambiar nombre de carpeta admin:**
   - Renombra `/admin/` a algo menos obvio como `/panel/` o `/gestor/`
   - Actualiza todas las referencias en el código

3. **Limitar intentos de login:**
   - Ya está implementado (5 intentos, bloqueo de 15 minutos)
   - Puedes ajustar en `config.php`:
     ```php
     define('MAX_LOGIN_ATTEMPTS', 5);
     define('LOGIN_LOCKOUT_TIME', 900); // 15 minutos
     ```

4. **Backup regular:**
   - Configura backups automáticos de la base de datos desde el panel de Hostinger
   - Guarda también las imágenes en `/images/properties/`

---

## 🐛 Solución de Problemas

### Error: "No se puede conectar a la base de datos"
- Verifica las credenciales en `config.php`
- Asegúrate de que el usuario de MySQL tenga permisos sobre la base de datos
- Verifica que el host sea correcto (generalmente `localhost` en Hostinger)

### Error: "No se puede crear el directorio de imágenes"
- Verifica permisos de la carpeta `images/properties/`
- Asegúrate de que el servidor web tenga permisos de escritura (755 o 777)

### Error: "Token CSRF inválido"
- Limpia las cookies del navegador
- Verifica que las sesiones estén funcionando correctamente

### Las imágenes no se muestran
- Verifica que las rutas en la base de datos sean correctas
- Asegúrate de que las imágenes existan en el servidor
- Verifica permisos de lectura de archivos

### Error: "GD library no está disponible"
- Las thumbnails no se crearán, pero las imágenes se subirán normalmente
- Contacta a Hostinger para habilitar la extensión GD de PHP

---

## 📞 Soporte

Si encuentras problemas durante el deployment, verifica:

1. **Logs de PHP** en `logs/php_errors.log`
2. **Logs del servidor** en el panel de Hostinger
3. **Permisos de archivos y carpetas**
4. **Configuración de PHP** (versión mínima: PHP 7.4)

---

## ✅ Checklist Final

- [ ] Base de datos creada e importada
- [ ] Credenciales actualizadas en `config.php`
- [ ] Archivos subidos al servidor
- [ ] Permisos de carpetas configurados
- [ ] Login de administrador probado
- [ ] Contraseña de administrador cambiada
- [ ] Propiedad de prueba agregada
- [ ] Panel admin funcionando correctamente
- [ ] HTTPS habilitado
- [ ] Backups configurados

---

## 🎯 URLs del Panel

Una vez desplegado, accede al panel en:

- **Login**: `https://tu-dominio.com/admin/login.php`
- **Dashboard**: `https://tu-dominio.com/admin/` o `https://tu-dominio.com/admin/index.php`
- **Lista de propiedades**: `https://tu-dominio.com/admin/list.php`
- **Agregar propiedad**: `https://tu-dominio.com/admin/add.php`

---

¡Listo! Tu panel de administración debería estar funcionando correctamente en Hostinger.

