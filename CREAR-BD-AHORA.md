# Crear Base de Datos - Instrucciones Rápidas

## ✅ Paso 1: Abrir phpMyAdmin

1. Abre tu navegador
2. Ve a: **http://localhost/phpmyadmin**
3. Deberías ver la interfaz de phpMyAdmin

## ✅ Paso 2: Crear la Base de Datos

1. En el menú lateral izquierdo, haz clic en **"Nueva"** (o "New")
2. En la sección "Crear base de datos":
   - **Nombre de la base de datos**: `delprado_db`
   - **Cotejamiento**: Selecciona `utf8mb4_unicode_ci` del menú desplegable
3. Haz clic en el botón **"Crear"**

## ✅ Paso 3: Importar el Archivo SQL

1. En el menú lateral izquierdo, haz clic en **`delprado_db`** (la base de datos que acabas de crear)
2. En la parte superior, haz clic en la pestaña **"Importar"** (Import)
3. Haz clic en **"Elegir archivo"** o **"Browse"**
4. Navega a la carpeta del proyecto y selecciona el archivo **`database.sql`**
5. Desplázate hacia abajo y haz clic en **"Continuar"** (Go) o **"Ejecutar"**

## ✅ Paso 4: Verificar

Deberías ver un mensaje de éxito y en el menú lateral izquierdo deberías ver:
- `delprado_db`
  - `properties` (tabla)
  - `users` (tabla)

---

## 🚀 Siguiente Paso: Iniciar el Servidor

Una vez creada la base de datos, ejecuta en PowerShell:

```powershell
C:\xampp\php\php.exe -S localhost:8000
```

Luego abre: **http://localhost:8000/admin/login.php**

---

## ❓ ¿Problemas?

### phpMyAdmin no carga
- Verifica que Apache esté corriendo en XAMPP (debe estar verde)
- Intenta: http://127.0.0.1/phpmyadmin

### Error al importar
- Verifica que el archivo `database.sql` esté en la carpeta del proyecto
- Asegúrate de haber seleccionado la base de datos `delprado_db` antes de importar

### No aparece el botón "Nueva"
- Busca en el menú superior o lateral izquierdo
- También puedes escribir `delprado_db` en el campo de búsqueda y crear desde ahí

