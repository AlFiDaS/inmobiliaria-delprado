# 🚀 Pasos Rápidos - Ya tienes XAMPP instalado

## ✅ Paso 1: Iniciar MySQL

1. Abre el **Panel de Control de XAMPP**
2. Busca **MySQL** en la lista
3. Haz clic en el botón **"Start"** (debería ponerse verde)
4. Si aparece un error, cierra y vuelve a abrir XAMPP como Administrador

## ✅ Paso 2: Crear Base de Datos

Tienes dos opciones:

### Opción A: Automático (desde PowerShell)
Una vez que MySQL esté corriendo, ejecuta:
```powershell
C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS delprado_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### Opción B: Manual (phpMyAdmin - Más fácil)
1. Abre tu navegador: `http://localhost/phpmyadmin`
2. Haz clic en **"Nueva"** (New) en el menú lateral izquierdo
3. En "Nombre de la base de datos", escribe: `delprado_db`
4. En "Cotejamiento", selecciona: `utf8mb4_unicode_ci`
5. Haz clic en **"Crear"**
6. Selecciona la base de datos `delprado_db` que acabas de crear
7. Ve a la pestaña **"Importar"** (Import)
8. Haz clic en **"Elegir archivo"** y selecciona `database.sql` de este proyecto
9. Haz clic en **"Continuar"** (Go) al final de la página

## ✅ Paso 3: Iniciar el Servidor PHP

Abre PowerShell en esta carpeta y ejecuta:

```powershell
C:\xampp\php\php.exe -S localhost:8000
```

Deberías ver algo como:
```
PHP 8.x.x Development Server started
Listening on http://localhost:8000
```

**⚠️ Deja esta ventana abierta** (no la cierres)

## ✅ Paso 4: Acceder al Panel

Abre tu navegador y ve a:

```
http://localhost:8000/admin/login.php
```

**Credenciales:**
- Usuario: `admin`
- Contraseña: `admin123`

---

## 🎯 Resumen Visual

```
1. XAMPP Panel → Start MySQL ✅
2. phpMyAdmin → Crear BD → Importar database.sql ✅
3. PowerShell → C:\xampp\php\php.exe -S localhost:8000 ✅
4. Navegador → http://localhost:8000/admin/login.php ✅
```

---

## ❓ ¿Problemas?

### "Can't connect to MySQL server"
- **Solución:** Asegúrate de que MySQL esté corriendo en XAMPP (botón verde)

### "Access denied for user 'root'"
- **Solución:** En XAMPP, el usuario root no tiene contraseña por defecto. Si te pide contraseña, déjala vacía.

### "php no se reconoce"
- **Solución:** Usa la ruta completa: `C:\xampp\php\php.exe -S localhost:8000`

### El servidor no inicia
- **Solución:** Verifica que el puerto 8000 no esté en uso. Puedes cambiar el puerto:
  ```powershell
  C:\xampp\php\php.exe -S localhost:8080
  ```
  Y luego accede a: `http://localhost:8080/admin/login.php`

---

¡Sigue estos pasos y estarás listo!

