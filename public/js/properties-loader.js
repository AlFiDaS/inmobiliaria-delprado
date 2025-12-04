/**
 * Script para cargar propiedades dinámicamente desde la API PHP
 */

async function loadProperties(options = {}) {
  const params = new URLSearchParams();
  
  if (options.operation) params.append('operation', options.operation);
  if (options.type) params.append('type', options.type);
  if (options.city) params.append('city', options.city);
  if (options.highlight !== undefined) params.append('highlight', options.highlight ? '1' : '0');
  if (options.slug) params.append('slug', options.slug);
  
  // Detectar si estamos en desarrollo (puerto 4321) o producción
  const isDevelopment = window.location.port === '4321' || window.location.hostname === 'localhost';
  const apiBase = isDevelopment ? 'http://localhost:8000' : '';
  const url = `${apiBase}/api/properties.php${params.toString() ? '?' + params.toString() : ''}`;
  
  console.log('Cargando propiedades desde:', url);
  console.log('Opciones:', options);
  
  try {
    const response = await fetch(url);
    console.log('Respuesta recibida:', response.status, response.statusText);
    if (!response.ok) {
      throw new Error(`Error al obtener propiedades: ${response.statusText}`);
    }
    const data = await response.json();
    console.log('Datos recibidos:', data);
    return data;
  } catch (error) {
    console.error('Error al obtener propiedades:', error);
    return [];
  }
}

// Función para renderizar propiedades en el slider de la página principal
async function renderFeaturedProperties() {
  const container = document.getElementById('slider-container');
  if (!container) return;
  
  const properties = await loadProperties({ highlight: true });
  
  if (properties.length === 0) {
    container.innerHTML = '<p class="text-gray-500 text-center">No hay propiedades destacadas disponibles</p>';
    return;
  }
  
  // Limitar a 6 propiedades para el slider
  const limitedProperties = properties.slice(0, 6);
  
  container.innerHTML = limitedProperties.map((prop, index) => {
    const image = prop.images && prop.images.length > 0 ? prop.images[0] : '/images/placeholder.jpg';
    const price = prop.currency === 'USD' 
      ? `USD ${prop.price.toLocaleString()}`
      : `$${prop.price.toLocaleString()}`;
    
    return `
      <div class="property-card-slide flex-shrink-0 w-full sm:w-1/2 lg:w-1/3 px-2" data-index="${index}">
        <a href="/propiedad/${prop.slug}" class="block bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden h-full">
          <div class="relative h-48 sm:h-56 overflow-hidden">
            <img src="${image}" alt="${prop.title}" class="w-full h-full object-cover" loading="lazy">
            <div class="absolute top-2 right-2">
              <span class="px-2 py-1 bg-${prop.operation === 'venta' ? 'orange' : 'emerald'}-600 text-white text-xs font-semibold rounded">
                ${prop.operation === 'venta' ? 'Venta' : 'Alquiler'}
              </span>
            </div>
          </div>
          <div class="p-4">
            <h3 class="font-bold text-gray-900 mb-2 line-clamp-2">${prop.title}</h3>
            <p class="text-sm text-gray-600 mb-2">${prop.city}${prop.neighborhood ? ', ' + prop.neighborhood : ''}</p>
            <p class="text-lg font-bold text-${prop.operation === 'venta' ? 'orange' : 'emerald'}-600">${price}</p>
          </div>
        </a>
      </div>
    `;
  }).join('');
  
  // Actualizar contador de páginas
  const totalPages = Math.ceil(limitedProperties.length / 3);
  const totalPagesEl = document.getElementById('total-pages');
  if (totalPagesEl) {
    totalPagesEl.textContent = String(totalPages).padStart(2, '0');
  }
}

// Función para renderizar propiedades en páginas de listado
async function renderPropertiesList(containerId, options = {}) {
  const container = document.getElementById(containerId);
  if (!container) return;
  
  const properties = await loadProperties(options);
  
  if (properties.length === 0) {
    container.innerHTML = '<p class="text-gray-500 text-center py-12">No hay propiedades disponibles</p>';
    return;
  }
  
  container.innerHTML = properties.map(prop => {
    const image = prop.images && prop.images.length > 0 ? prop.images[0] : '/images/placeholder.jpg';
    const price = prop.currency === 'USD' 
      ? `USD ${prop.price.toLocaleString()}`
      : `$${prop.price.toLocaleString()}`;
    
    return `
      <div class="property-card">
        <a href="/propiedad/${prop.slug}" class="block bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden">
          <div class="relative h-48 overflow-hidden">
            <img src="${image}" alt="${prop.title}" class="w-full h-full object-cover" loading="lazy">
            ${prop.highlight ? '<div class="absolute top-2 left-2"><span class="px-2 py-1 bg-orange-600 text-white text-xs font-semibold rounded">⭐ Destacada</span></div>' : ''}
            <div class="absolute top-2 right-2">
              <span class="px-2 py-1 bg-${prop.operation === 'venta' ? 'orange' : 'emerald'}-600 text-white text-xs font-semibold rounded">
                ${prop.operation === 'venta' ? 'Venta' : 'Alquiler'}
              </span>
            </div>
          </div>
          <div class="p-4">
            <h3 class="font-bold text-gray-900 mb-2">${prop.title}</h3>
            <p class="text-sm text-gray-600 mb-2">${prop.city}${prop.neighborhood ? ', ' + prop.neighborhood : ''}</p>
            <p class="text-lg font-bold text-${prop.operation === 'venta' ? 'orange' : 'emerald'}-600 mb-2">${price}</p>
            ${prop.bedrooms ? `<p class="text-sm text-gray-600">${prop.bedrooms} dormitorios</p>` : ''}
          </div>
        </a>
      </div>
    `;
  }).join('');
}

// Cargar propiedades destacadas cuando el DOM esté listo
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('slider-container')) {
      renderFeaturedProperties();
    }
  });
} else {
  if (document.getElementById('slider-container')) {
    renderFeaturedProperties();
  }
}

