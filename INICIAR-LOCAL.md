# 🚀 Cómo Correr la Página Web en Local

## Requisitos Previos

1. ✅ **XAMPP instalado** (PHP + MySQL)
2. ✅ **Node.js instalado** (para Astro)
3. ✅ **Base de datos configurada** (`delprado_db` creada e importada)

---

## 📋 Pasos Rápidos

### 1️⃣ Iniciar MySQL (XAMPP)

1. Abre el **Panel de Control de XAMPP**
2. Haz clic en **Start** en el servicio **MySQL**
3. Debe aparecer en verde ✅

### 2️⃣ Iniciar Servidor PHP (Backend)

Abre una **terminal PowerShell** en la raíz del proyecto y ejecuta:

```powershell
.\start-server.ps1
```

O manualmente:

```powershell
C:\xampp\php\php.exe -S localhost:8000 router.php
```

**✅ Deberías ver:** `Servidor iniciado en http://localhost:8000`

**Mantén esta terminal abierta** (no la cierres)

---

### 3️⃣ Iniciar Servidor Astro (Frontend)

Abre una **segunda terminal PowerShell** en la raíz del proyecto y ejecuta:

```powershell
npm run dev
```

**✅ Deberías ver:** `Local: http://localhost:4321/`

**Mantén esta terminal abierta** (no la cierres)

---

## 🌐 URLs para Acceder

### Frontend (Astro)
- **Página principal**: http://localhost:4321
- **Ventas**: http://localhost:4321/ventas
- **Alquileres**: http://localhost:4321/alquileres
- **Propiedad**: http://localhost:4321/propiedad/[slug]

### Backend (PHP)
- **Panel Admin**: http://localhost:8000/admin/login.php
- **API de Propiedades**: http://localhost:8000/api/properties.php

---

## 🔑 Credenciales del Panel Admin

- **Usuario**: `admin`
- **Contraseña**: `admin123`

---

## ⚠️ Importante

1. **Ambos servidores deben estar corriendo al mismo tiempo:**
   - Terminal 1: Servidor PHP (puerto 8000)
   - Terminal 2: Servidor Astro (puerto 4321)

2. **No cierres las terminales** mientras trabajas

3. **Para detener los servidores:**
   - Presiona `Ctrl+C` en cada terminal

---

## 🐛 Solución de Problemas

### Error: "PHP no encontrado"
- Verifica que XAMPP esté instalado en `C:\xampp\`
- O ejecuta: `C:\xampp\php\php.exe -S localhost:8000 router.php`

### Error: "No se puede conectar a la base de datos"
- Verifica que MySQL esté corriendo en XAMPP
- Revisa `config.php` que tenga las credenciales correctas:
  ```php
  DB_USER = 'root'
  DB_PASS = ''  // Vacío para XAMPP
  ```

### Error: "npm no encontrado"
- Instala Node.js desde: https://nodejs.org/
- Reinicia la terminal después de instalar

### Las propiedades no aparecen
- Verifica que ambos servidores estén corriendo
- Abre la consola del navegador (F12) y revisa errores
- Verifica que la API responda: http://localhost:8000/api/properties.php

---

## ✅ Checklist Rápido

- [ ] MySQL corriendo en XAMPP
- [ ] Servidor PHP corriendo en puerto 8000
- [ ] Servidor Astro corriendo en puerto 4321
- [ ] Puedo acceder a http://localhost:4321
- [ ] Puedo acceder a http://localhost:8000/admin/login.php

---

¡Listo! Tu página web está corriendo en local 🎉

