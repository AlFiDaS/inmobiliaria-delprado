# Iniciar Apache en XAMPP

## ✅ Pasos para Iniciar Apache

1. **Abre el Panel de Control de XAMPP**
   - Busca "XAMPP Control Panel" en el menú de inicio
   - O ve a: `C:\xampp\xampp-control.exe`

2. **Inicia Apache**
   - Busca **"Apache"** en la lista
   - Haz clic en el botón **"Start"** (a la derecha de Apache)
   - Debería ponerse **verde** y mostrar "Running"

3. **Si aparece un error:**
   - **Error de puerto 80 ocupado:**
     - Otro programa (como Skype o IIS) está usando el puerto 80
     - Cierra Skype o IIS
     - O cambia el puerto de Apache en la configuración
   
   - **Error de permisos:**
     - Cierra XAMPP
     - Haz clic derecho en XAMPP Control Panel
     - Selecciona "Ejecutar como administrador"
     - Intenta iniciar Apache nuevamente

4. **Verifica que funcione:**
   - Abre tu navegador
   - Ve a: `http://localhost`
   - Deberías ver la página de bienvenida de XAMPP

5. **Ahora accede a phpMyAdmin:**
   - Ve a: `http://localhost/phpmyadmin`
   - Deberías ver la interfaz de phpMyAdmin

---

## 🎯 Estado Ideal en XAMPP

Deberías ver ambos servicios en **verde**:
- ✅ **Apache** - Running (verde)
- ✅ **MySQL** - Running (verde)

---

## 🚀 Después de Iniciar Apache

Una vez que Apache esté corriendo y hayas creado la base de datos:

1. **Inicia el servidor PHP** (en PowerShell):
   ```powershell
   C:\xampp\php\php.exe -S localhost:8000
   ```

2. **Abre el panel admin:**
   ```
   http://localhost:8000/admin/login.php
   ```

---

## ❓ ¿Problemas?

### "Port 80 already in use"
- Cierra Skype u otros programas que usen el puerto 80
- O cambia el puerto de Apache (Config → httpd.conf → cambia Listen 80 a otro puerto)

### "Apache won't start"
- Ejecuta XAMPP como Administrador
- Verifica que no haya otro servidor web corriendo

### "phpMyAdmin still doesn't load"
- Espera unos segundos después de iniciar Apache
- Intenta: `http://127.0.0.1/phpmyadmin`
- Verifica que Apache esté realmente en verde (Running)

