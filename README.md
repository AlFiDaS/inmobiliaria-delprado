# Inmobiliaria Del Prado

Sitio web inmobiliario desarrollado con Astro, TypeScript y Tailwind CSS. Especializado en venta y alquiler de propiedades en Buenos Aires y zona norte.

## 🚀 Características

- **Diseño responsive** con Tailwind CSS
- **TypeScript** para mayor seguridad de tipos
- **SEO optimizado** con metadata y sitemap
- **Integración con WhatsApp** para contacto directo
- **Filtros avanzados** y paginación
- **Galería de imágenes** con soporte para videos
- **Accesibilidad** implementada
- **Rendimiento optimizado** con Astro

## 📁 Estructura del Proyecto

```
src/
├── components/          # Componentes reutilizables
│   ├── PropertyCard.astro
│   ├── Filters.tsx
│   ├── Gallery.tsx
│   ├── WhatsAppButton.astro
│   ├── Badge.astro
│   ├── Price.astro
│   └── Icon.astro
├── data/               # Datos de propiedades
│   └── properties.ts
├── layouts/            # Layouts de página
│   ├── Base.astro
│   └── Section.astro
├── lib/                # Utilidades
│   ├── whatsapp.ts
│   ├── format.ts
│   ├── filters.ts
│   └── pagination.ts
├── pages/              # Páginas del sitio
│   ├── index.astro
│   ├── ventas.astro
│   ├── alquileres.astro
│   ├── contacto.astro
│   ├── datos-de-interes.astro
│   └── propiedad/[slug].astro
└── styles/
    └── tailwind.css
```

## 🛠️ Instalación y Configuración

### Prerrequisitos

- Node.js 18+ 
- npm o pnpm

### Instalación

1. **Clonar el repositorio**
   ```bash
   git clone <repository-url>
   cd inmobiliaria-delprado
   ```

2. **Instalar dependencias**
   ```bash
   npm install
   # o
   pnpm install
   ```

3. **Configurar variables de entorno**
   ```bash
   cp env.example .env
   ```
   
   Editar `.env` y configurar:
   ```
   PUBLIC_WA_PHONE=5493790000000
   ```

4. **Generar imágenes placeholder** (opcional)
   ```bash
   node scripts/generate-placeholders.js
   ```

5. **Ejecutar en modo desarrollo**
   ```bash
   npm run dev
   # o
   pnpm dev
   ```

6. **Abrir en el navegador**
   ```
   http://localhost:4321
   ```

## 📝 Cómo Agregar Nuevas Propiedades

1. **Editar el archivo de datos**
   ```typescript
   // src/data/properties.ts
   export const properties: Property[] = [
     // ... propiedades existentes
     {
       id: 'PROP011',
       slug: 'nueva-propiedad',
       title: 'Nueva Propiedad',
       // ... resto de campos
     }
   ];
   ```

2. **Agregar imágenes** (opcional)
   - Colocar imágenes en `public/images/properties/`
   - Usar nombres descriptivos: `nueva-propiedad-1.jpg`, `nueva-propiedad-2.jpg`, etc.

3. **Actualizar el sitio**
   ```bash
   npm run build
   ```

## 🔧 Configuración de WhatsApp

Para configurar el número de WhatsApp:

1. **Editar el archivo `.env`**
   ```
   PUBLIC_WA_PHONE=5493790000000
   ```
   
   **Formato del número:**
   - Sin el símbolo `+`
   - Sin espacios
   - Incluir código de país (54 para Argentina)
   - Ejemplo: `5493791234567`

2. **Reiniciar el servidor de desarrollo**
   ```bash
   npm run dev
   ```

## 🎨 Personalización

### Colores

Los colores se pueden personalizar en `tailwind.config.cjs`:

```javascript
theme: {
  extend: {
    colors: {
      primary: {
        // Personalizar colores primarios
      }
    }
  }
}
```

### Tipografía

La fuente se puede cambiar en `src/styles/tailwind.css`:

```css
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
```

## 📱 Páginas del Sitio

- **`/`** - Página principal con hero, buscador y propiedades destacadas
- **`/ventas`** - Listado de propiedades en venta con filtros
- **`/alquileres`** - Listado de propiedades en alquiler con filtros
- **`/propiedad/[slug]`** - Detalle de propiedad individual
- **`/contacto`** - Formularios de contacto y publicación
- **`/datos-de-interes`** - Información útil sobre operaciones inmobiliarias

## 🔍 SEO y Metadatos

El sitio incluye:

- **Meta tags** optimizados por página
- **Open Graph** para redes sociales
- **Twitter Cards**
- **Schema.org** JSON-LD
- **Sitemap.xml**
- **Robots.txt**

## 🚀 Despliegue

### Build para producción

```bash
npm run build
```

### Verificar build

```bash
npm run preview
```

### Despliegue en Vercel

1. Conectar repositorio a Vercel
2. Configurar variables de entorno
3. Desplegar automáticamente

### Despliegue en Netlify

1. Conectar repositorio a Netlify
2. Configurar build command: `npm run build`
3. Configurar publish directory: `dist`
4. Configurar variables de entorno

## 🧪 Testing

```bash
# Verificar tipos TypeScript
npm run astro check

# Build completo
npm run build
```

## 📊 Rendimiento

El sitio está optimizado para:

- **Core Web Vitals** excelentes
- **Lazy loading** de imágenes
- **Compresión** de assets
- **Caching** optimizado
- **Bundle splitting** automático

## 🤝 Contribución

1. Fork el proyecto
2. Crear una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abrir un Pull Request

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

## 📞 Soporte

Para soporte técnico o consultas:

- **Email**: info@inmobiliariadelprado.com
- **WhatsApp**: +54 9 11 1234-5678

---

Desarrollado con ❤️ usando Astro, TypeScript y Tailwind CSS
