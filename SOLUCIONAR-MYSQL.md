# Solucionar Error de Conexión MySQL en phpMyAdmin

## 🔴 Problema
phpMyAdmin muestra: "No se puede establecer una conexión ya que el equipo de destino denegó expresamente dicha conexión"

Esto significa que MySQL no está aceptando conexiones, aunque aparezca activo en XAMPP.

## ✅ Soluciones

### Solución 1: Reiniciar MySQL en XAMPP

1. **Abre el Panel de Control de XAMPP**
2. Si MySQL está en verde, haz clic en **"Stop"** (detener)
3. Espera 5 segundos
4. Haz clic en **"Start"** nuevamente
5. Espera a que se ponga verde completamente
6. Intenta acceder a phpMyAdmin de nuevo: `http://localhost/phpmyadmin`

### Solución 2: Verificar que MySQL esté realmente corriendo

1. En el Panel de Control de XAMPP, verifica que MySQL muestre:
   - ✅ Botón verde (Running)
   - ✅ Puerto: 3306
   - ✅ Sin errores en rojo

2. Si hay errores, haz clic en "Logs" junto a MySQL para ver qué está pasando

### Solución 3: Ejecutar XAMPP como Administrador

1. Cierra XAMPP completamente
2. Haz clic derecho en **XAMPP Control Panel**
3. Selecciona **"Ejecutar como administrador"**
4. Inicia MySQL nuevamente
5. Intenta phpMyAdmin de nuevo

### Solución 4: Verificar configuración de phpMyAdmin (si las anteriores no funcionan)

El archivo de configuración está en: `C:\xampp\phpMyAdmin\config.inc.php`

Por defecto en XAMPP debería estar configurado así:
- Host: `127.0.0.1` o `localhost`
- Usuario: `root`
- Contraseña: (vacía)

Si modificaste algo, revierte los cambios.

### Solución 5: Usar MySQL desde línea de comandos

Si phpMyAdmin sigue sin funcionar, puedes crear la base de datos directamente:

1. Abre PowerShell
2. Ejecuta:
   ```powershell
   C:\xampp\mysql\bin\mysql.exe -u root
   ```

3. Si se conecta, ejecuta:
   ```sql
   CREATE DATABASE delprado_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   exit;
   ```

4. Luego importa el SQL:
   ```powershell
   C:\xampp\mysql\bin\mysql.exe -u root delprado_db < database.sql
   ```

---

## 🎯 Pasos Recomendados (en orden)

1. **Reinicia MySQL** en XAMPP (Stop → Start)
2. Espera 10 segundos
3. Intenta phpMyAdmin de nuevo
4. Si no funciona, ejecuta XAMPP como Administrador
5. Si aún no funciona, usa la línea de comandos (Solución 5)

---

## ✅ Verificar que Funciona

Una vez que MySQL esté funcionando correctamente:

1. Deberías poder acceder a `http://localhost/phpmyadmin` sin errores
2. O desde PowerShell, este comando debería funcionar:
   ```powershell
   C:\xampp\mysql\bin\mysql.exe -u root -e "SHOW DATABASES;"
   ```

---

## 📝 Nota

A veces MySQL tarda unos segundos en estar completamente listo después de iniciarse. Si acabas de iniciarlo, espera 10-15 segundos antes de intentar conectarte.

