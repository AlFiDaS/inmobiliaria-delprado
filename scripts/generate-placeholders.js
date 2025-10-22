import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Crear directorio de imágenes si no existe
const imagesDir = path.join(__dirname, '..', 'public', 'images', 'properties');
if (!fs.existsSync(imagesDir)) {
  fs.mkdirSync(imagesDir, { recursive: true });
}

// Lista de propiedades con sus imágenes
const properties = [
  { slug: 'casa-palermo', images: ['casa-palermo-1.jpg', 'casa-palermo-2.jpg', 'casa-palermo-3.jpg', 'casa-palermo-4.jpg'] },
  { slug: 'depto-recoleta', images: ['depto-recoleta-1.jpg', 'depto-recoleta-2.jpg', 'depto-recoleta-3.jpg'] },
  { slug: 'casa-san-isidro', images: ['casa-san-isidro-1.jpg', 'casa-san-isidro-2.jpg', 'casa-san-isidro-3.jpg', 'casa-san-isidro-4.jpg', 'casa-san-isidro-5.jpg'] },
  { slug: 'local-microcentro', images: ['local-microcentro-1.jpg', 'local-microcentro-2.jpg'] },
  { slug: 'depto-belgrano', images: ['depto-belgrano-1.jpg', 'depto-belgrano-2.jpg', 'depto-belgrano-3.jpg', 'depto-belgrano-4.jpg'] },
  { slug: 'casa-barrio-norte', images: ['casa-barrio-norte-1.jpg', 'casa-barrio-norte-2.jpg', 'casa-barrio-norte-3.jpg'] },
  { slug: 'oficina-torre', images: ['oficina-torre-1.jpg', 'oficina-torre-2.jpg', 'oficina-torre-3.jpg'] },
  { slug: 'terreno-pilar', images: ['terreno-pilar-1.jpg', 'terreno-pilar-2.jpg'] },
  { slug: 'ph-villa-crespo', images: ['ph-villa-crespo-1.jpg', 'ph-villa-crespo-2.jpg', 'ph-villa-crespo-3.jpg'] },
  { slug: 'duplex-nunez', images: ['duplex-nunez-1.jpg', 'duplex-nunez-2.jpg', 'duplex-nunez-3.jpg', 'duplex-nunez-4.jpg'] }
];

// SVG placeholder template con cuadrados negros estéticos
const createPlaceholderSVG = (width, height, text) => `
<svg width="${width}" height="${height}" viewBox="0 0 ${width} ${height}" fill="none" xmlns="http://www.w3.org/2000/svg">
  <rect width="${width}" height="${height}" fill="#1F2937"/>
  <rect x="${width/2 - 40}" y="${height/2 - 40}" width="80" height="80" fill="#374151" rx="8"/>
  <rect x="${width/2 - 30}" y="${height/2 - 30}" width="60" height="60" fill="#4B5563" rx="6"/>
  <rect x="${width/2 - 20}" y="${height/2 - 20}" width="40" height="40" fill="#6B7280" rx="4"/>
  <text x="${width/2}" y="${height/2 + 60}" text-anchor="middle" fill="#9CA3AF" font-family="system-ui" font-size="14" font-weight="500">${text}</text>
</svg>`;

// Generar imágenes placeholder
properties.forEach(property => {
  property.images.forEach((imageName, index) => {
    const svgContent = createPlaceholderSVG(400, 300, `${property.slug} ${index + 1}`);
    const filePath = path.join(imagesDir, imageName);
    fs.writeFileSync(filePath, svgContent);
    console.log(`Generated: ${imageName}`);
  });
});

// Generar imagen OG por defecto
const ogImage = createPlaceholderSVG(1200, 630, 'Del Prado Inmobiliaria');
fs.writeFileSync(path.join(__dirname, '..', 'public', 'images', 'og-default.jpg'), ogImage);
console.log('Generated: og-default.jpg');

console.log('✅ All placeholder images generated successfully!');
