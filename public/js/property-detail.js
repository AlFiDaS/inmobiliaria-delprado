/**
 * Script para cargar y renderizar el detalle de una propiedad
 */

// Función para formatear precio
function formatPrice(price, currency = 'ARS') {
  if (currency === 'USD') {
    return `USD ${price.toLocaleString()}`;
  }
  return `$${price.toLocaleString()}`;
}

// Función para formatear m²
function formatM2(m2) {
  return `${m2.toLocaleString()} m²`;
}

// Función para formatear fecha
function formatDate(dateString) {
  const date = new Date(dateString);
  return date.toLocaleDateString('es-AR', { 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric' 
  });
}

// Función para crear mensaje de WhatsApp
function createPropertyMessage(property) {
  const price = formatPrice(property.price, property.currency);
  return `Hola! Me interesa esta propiedad:\n\n${property.title}\n${price}\n${property.city}${property.neighborhood ? ', ' + property.neighborhood : ''}\n\n${window.location.href}`;
}

// Función para cargar propiedad desde la API
async function loadProperty(slug) {
  const isDevelopment = window.location.port === '4321' || window.location.hostname === 'localhost';
  const apiBase = isDevelopment ? 'http://localhost:8000' : '';
  const url = `${apiBase}/api/properties.php?slug=${slug}`;

  try {
    const response = await fetch(url);
    if (!response.ok) {
      throw new Error(`Error ${response.status}: ${response.statusText}`);
    }
    const properties = await response.json();
    if (!properties || properties.length === 0) {
      throw new Error('Propiedad no encontrada');
    }
    return properties[0];
  } catch (error) {
    console.error('Error al cargar propiedad:', error);
    throw error;
  }
}

// Función para cargar propiedades relacionadas
async function loadRelatedProperties(city, type, excludeId) {
  const isDevelopment = window.location.port === '4321' || window.location.hostname === 'localhost';
  const apiBase = isDevelopment ? 'http://localhost:8000' : '';
  const url = `${apiBase}/api/properties.php?city=${city}&type=${type}`;

  try {
    const response = await fetch(url);
    if (response.ok) {
      const properties = await response.json();
      if (Array.isArray(properties)) {
        return properties.filter((p) => p.id !== excludeId).slice(0, 4);
      }
    }
  } catch (error) {
    console.error('Error al cargar propiedades relacionadas:', error);
  }
  return [];
}

// Función para renderizar la propiedad
function renderProperty(property, relatedProperties = []) {
  const container = document.getElementById('property-content');
  const loadingState = document.getElementById('loading-state');
  const errorState = document.getElementById('error-state');

  if (!container) return;

  // Ocultar estados de carga y error
  if (loadingState) loadingState.classList.add('hidden');
  if (errorState) errorState.classList.add('hidden');

  // Mostrar contenido
  container.classList.remove('hidden');

  // Generar mensaje de WhatsApp
  const whatsappMessage = createPropertyMessage(property);

  // Renderizar contenido (código HTML muy largo, se mantiene igual que en [slug].astro)
  // Por brevedad, aquí va el HTML completo...
  container.innerHTML = `
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-gray-900 via-gray-800 to-black text-white">
      <div class="container-custom px-4 sm:px-6 py-12 sm:py-16">
        <div class="max-w-4xl mx-auto">
          <!-- Breadcrumb -->
          <nav class="mb-6">
            <div class="flex items-center space-x-2 text-sm text-gray-300">
              <a href="/" class="hover:text-white transition-colors">Inicio</a>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
              <a href="/${property.operation === 'venta' ? 'ventas' : 'alquileres'}" class="hover:text-white transition-colors capitalize">${property.operation}</a>
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
              <span class="text-white">${property.title}</span>
            </div>
          </nav>

          <!-- Título y precio -->
          <div class="text-center mb-8">
            <h1 class="text-xl sm:text-2xl md:text-3xl lg:text-4xl xl:text-5xl font-bold mb-4 leading-tight px-4" style="text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);">
              ${property.title}
            </h1>
            <div class="flex items-center justify-center text-gray-300 mb-6 px-4">
              <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              <span class="text-sm sm:text-base lg:text-lg text-center">
                ${property.address ? property.address + ', ' : ''}
                ${property.neighborhood ? property.neighborhood + ', ' : ''}
                ${property.city}
              </span>
            </div>
            <div class="flex flex-col sm:flex-row items-center justify-center space-y-2 sm:space-y-0 sm:space-x-4 px-4">
              <div class="text-2xl sm:text-3xl md:text-4xl font-bold text-white" style="text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);">
                ${formatPrice(property.price, property.currency)}
              </div>
              <div class="px-4 py-2 rounded-full text-sm font-semibold uppercase tracking-wider ${property.operation === 'venta' ? 'bg-orange-500/90 text-white' : 'bg-gold-500/90 text-white'}">
                ${property.operation}
              </div>
            </div>
          </div>

          <!-- Características principales -->
          <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-6 max-w-3xl mx-auto px-4">
            ${property.bedrooms ? `
            <div class="text-center bg-white/10 backdrop-blur-sm rounded-lg sm:rounded-xl p-3 sm:p-4">
              <svg class="w-8 h-8 mx-auto mb-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
              </svg>
              <div class="text-lg sm:text-2xl font-bold text-white">${property.bedrooms}</div>
              <div class="text-xs sm:text-sm text-gray-300">Dormitorios</div>
            </div>
            ` : ''}
            ${property.bathrooms ? `
            <div class="text-center bg-white/10 backdrop-blur-sm rounded-lg sm:rounded-xl p-3 sm:p-4">
              <svg class="w-8 h-8 mx-auto mb-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
              </svg>
              <div class="text-lg sm:text-2xl font-bold text-white">${property.bathrooms}</div>
              <div class="text-xs sm:text-sm text-gray-300">Baños</div>
            </div>
            ` : ''}
            ${(property.totalM2 || property.coveredM2) ? `
            <div class="text-center bg-white/10 backdrop-blur-sm rounded-lg sm:rounded-xl p-3 sm:p-4">
              <svg class="w-8 h-8 mx-auto mb-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
              </svg>
              <div class="text-lg sm:text-2xl font-bold text-white">${property.totalM2 || property.coveredM2}</div>
              <div class="text-xs sm:text-sm text-gray-300">m²</div>
            </div>
            ` : ''}
            ${property.parking ? `
            <div class="text-center bg-white/10 backdrop-blur-sm rounded-lg sm:rounded-xl p-3 sm:p-4">
              <svg class="w-8 h-8 mx-auto mb-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
              </svg>
              <div class="text-lg sm:text-2xl font-bold text-white">${typeof property.parking === 'number' ? property.parking : '1'}</div>
              <div class="text-xs sm:text-sm text-gray-300">Cochera</div>
            </div>
            ` : ''}
          </div>
        </div>
      </div>
    </section>

    <!-- Contenido principal -->
    <section class="py-8 sm:py-12 lg:py-16 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
              <!-- Contenido principal -->
              <div class="lg:col-span-2">
                <!-- Galería -->
                <div id="gallery-container" class="mb-8 w-full max-w-full overflow-hidden">
                  <!-- La galería se renderizará aquí con JavaScript -->
                </div>
            
            ${property.description ? `
            <!-- Descripción -->
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg border border-gray-100 p-4 sm:p-6 lg:p-8 mb-6 sm:mb-8">
              <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-4 sm:mb-6 flex items-center">
                <div class="w-1 h-6 sm:h-8 bg-gradient-to-b from-orange-500 to-gold-500 rounded-full mr-3 sm:mr-4"></div>
                Descripción
              </h2>
              <p class="text-gray-700 leading-relaxed text-sm sm:text-base lg:text-lg">${property.description}</p>
            </div>
            ` : ''}
            
            <!-- Características detalladas -->
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg border border-gray-100 p-4 sm:p-6 lg:p-8 mb-6 sm:mb-8">
              <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-4 sm:mb-6 lg:mb-8 flex items-center">
                <div class="w-1 h-6 sm:h-8 bg-gradient-to-b from-orange-500 to-gold-500 rounded-full mr-3 sm:mr-4"></div>
                Características
              </h2>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                ${property.type ? `
                <div class="flex justify-between items-center py-2 sm:py-3 border-b border-gray-100">
                  <span class="text-gray-600 font-medium text-sm sm:text-base">Tipo:</span>
                  <span class="font-bold text-gray-900 capitalize bg-gray-100 px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm">${property.type}</span>
                </div>
                ` : ''}
                ${property.condition ? `
                <div class="flex justify-between items-center py-2 sm:py-3 border-b border-gray-100">
                  <span class="text-gray-600 font-medium text-sm sm:text-base">Estado:</span>
                  <span class="font-bold text-gray-900 capitalize bg-gray-100 px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm">${property.condition}</span>
                </div>
                ` : ''}
                ${property.year ? `
                <div class="flex justify-between items-center py-2 sm:py-3 border-b border-gray-100">
                  <span class="text-gray-600 font-medium text-sm sm:text-base">Año:</span>
                  <span class="font-bold text-gray-900 text-sm sm:text-base">${property.year}</span>
                </div>
                ` : ''}
                ${property.orientation ? `
                <div class="flex justify-between items-center py-2 sm:py-3 border-b border-gray-100">
                  <span class="text-gray-600 font-medium text-sm sm:text-base">Orientación:</span>
                  <span class="font-bold text-gray-900 text-sm sm:text-base">${property.orientation}</span>
                </div>
                ` : ''}
                ${property.expenses ? `
                <div class="flex justify-between items-center py-2 sm:py-3 border-b border-gray-100">
                  <span class="text-gray-600 font-medium text-sm sm:text-base">Expensas:</span>
                  <span class="font-bold text-gray-900 text-sm sm:text-base">${formatPrice(property.expenses)}</span>
                </div>
                ` : ''}
                ${property.coveredM2 && property.totalM2 ? `
                <div class="flex justify-between items-center py-2 sm:py-3 border-b border-gray-100">
                  <span class="text-gray-600 font-medium text-sm sm:text-base">Superficie cubierta:</span>
                  <span class="font-bold text-gray-900 text-sm sm:text-base">${formatM2(property.coveredM2)}</span>
                </div>
                ` : ''}
                ${property.totalM2 && property.coveredM2 ? `
                <div class="flex justify-between items-center py-2 sm:py-3 border-b border-gray-100">
                  <span class="text-gray-600 font-medium text-sm sm:text-base">Superficie total:</span>
                  <span class="font-bold text-gray-900 text-sm sm:text-base">${formatM2(property.totalM2)}</span>
                </div>
                ` : ''}
                ${property.listedAt ? `
                <div class="flex justify-between items-center py-2 sm:py-3 border-b border-gray-100">
                  <span class="text-gray-600 font-medium text-sm sm:text-base">Publicado:</span>
                  <span class="font-bold text-gray-900 text-sm sm:text-base">${formatDate(property.listedAt)}</span>
                </div>
                ` : ''}
              </div>
            </div>
            
            ${property.amenities && property.amenities.length > 0 ? `
            <!-- Amenities -->
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg border border-gray-100 p-4 sm:p-6 lg:p-8 mb-6 sm:mb-8">
              <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-4 sm:mb-6 flex items-center">
                <div class="w-1 h-6 sm:h-8 bg-gradient-to-b from-orange-500 to-gold-500 rounded-full mr-3 sm:mr-4"></div>
                Amenities
              </h2>
              <div class="flex flex-wrap gap-2 sm:gap-3">
                ${property.amenities.map((amenity) => `
                  <div class="bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800 px-3 sm:px-4 py-1 sm:py-2 rounded-full text-xs sm:text-sm font-medium border border-gray-200">
                    ${amenity}
                  </div>
                `).join('')}
              </div>
            </div>
            ` : ''}
            
            ${property.features && property.features.length > 0 ? `
            <!-- Características adicionales -->
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg border border-gray-100 p-4 sm:p-6 lg:p-8 mb-6 sm:mb-8">
              <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-4 sm:mb-6 flex items-center">
                <div class="w-1 h-6 sm:h-8 bg-gradient-to-b from-orange-500 to-gold-500 rounded-full mr-3 sm:mr-4"></div>
                Características Adicionales
              </h2>
              <div class="flex flex-wrap gap-2 sm:gap-3">
                ${property.features.map((feature) => `
                  <div class="bg-gradient-to-r from-orange-100 to-gold-100 text-orange-800 px-3 sm:px-4 py-1 sm:py-2 rounded-full text-xs sm:text-sm font-medium border border-orange-200">
                    ${feature}
                  </div>
                `).join('')}
              </div>
            </div>
            ` : ''}
          </div>
          
          <!-- Sidebar -->
          <div class="lg:col-span-1">
            <div class="lg:sticky lg:top-4">
              <!-- CTA WhatsApp -->
              <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl sm:rounded-2xl shadow-lg border border-gray-100 p-4 sm:p-6 lg:p-8 mb-4 sm:mb-6">
                <div class="text-center mb-4 sm:mb-6">
                  <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                    <svg class="w-6 h-6 sm:w-8 sm:h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                    </svg>
                  </div>
                  <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-2">¿Te interesa esta propiedad?</h3>
                  <p class="text-gray-600 text-xs sm:text-sm">
                    Contacta con nosotros para más información o para agendar una visita.
                  </p>
                </div>
                <a href="https://wa.me/543794740207?text=${encodeURIComponent(whatsappMessage)}" target="_blank" rel="noopener noreferrer" class="w-full flex items-center justify-center px-6 py-3 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-xl transition-colors">
                  <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                  </svg>
                  Contactar por WhatsApp
                </a>
              </div>
              
              <!-- Información de contacto -->
              <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl sm:rounded-2xl shadow-lg border border-gray-100 p-4 sm:p-6 lg:p-8">
                <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-4 sm:mb-6 flex items-center">
                  <div class="w-1 h-5 sm:h-6 bg-gradient-to-b from-orange-500 to-gold-500 rounded-full mr-2 sm:mr-3"></div>
                  Contacto
                </h3>
                <div class="space-y-3 sm:space-y-4">
                  <div class="flex items-center bg-white rounded-lg sm:rounded-xl p-3 sm:p-4 border border-gray-100">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-orange-500 to-orange-600 rounded-full flex items-center justify-center mr-3 sm:mr-4 flex-shrink-0">
                      <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                      </svg>
                    </div>
                    <div class="min-w-0">
                      <div class="text-xs sm:text-sm text-gray-500">Teléfono</div>
                      <div class="font-semibold text-gray-900 text-sm sm:text-base">+54 9 3794 74 0207</div>
                    </div>
                  </div>
                  <div class="flex items-center bg-white rounded-lg sm:rounded-xl p-3 sm:p-4 border border-gray-100">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-gray-500 to-gray-600 rounded-full flex items-center justify-center mr-3 sm:mr-4 flex-shrink-0">
                      <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                      </svg>
                    </div>
                    <div class="min-w-0">
                      <div class="text-xs sm:text-sm text-gray-500">Ubicación</div>
                      <div class="font-semibold text-gray-900 text-sm sm:text-base">San Martin 1127, Corrientes Capital</div>
                    </div>
                  </div>
                  <div class="flex items-center bg-white rounded-lg sm:rounded-xl p-3 sm:p-4 border border-gray-100">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-orange-100 rounded-full flex items-center justify-center mr-3 sm:mr-4 flex-shrink-0">
                      <svg class="w-4 h-4 sm:w-5 sm:h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                    </div>
                    <div class="min-w-0">
                      <div class="text-xs sm:text-sm text-gray-500">Martillero Público</div>
                      <div class="font-semibold text-gray-900 text-sm sm:text-base">Gastón del Prado</div>
                      <div class="text-xs text-gray-400">M.P. N° 321</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    
    ${relatedProperties.length > 0 ? `
    <!-- Propiedades relacionadas -->
    <section class="hidden sm:block bg-gradient-to-br from-gray-50 to-white py-8 sm:py-12 lg:py-16">
      <div class="container-custom px-4 sm:px-6">
        <div class="max-w-7xl mx-auto">
          <div class="text-center mb-8 sm:mb-10 lg:mb-12">
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mb-3 sm:mb-4">
              Propiedades Relacionadas
            </h2>
            <p class="text-sm sm:text-base lg:text-lg text-gray-600 max-w-2xl mx-auto px-4">
              Otras opciones que podrían interesarte en la misma zona
            </p>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 lg:gap-8" id="related-properties-container">
            <!-- Las propiedades relacionadas se insertarán aquí -->
          </div>
        </div>
      </div>
    </section>
    ` : ''}
  `;

      // Actualizar título de la página
      document.title = `${property.title} | Del Prado Inmobiliaria`;

  // Renderizar propiedades relacionadas si existen
  if (relatedProperties.length > 0) {
    const relatedContainer = document.getElementById('related-properties-container');
    if (relatedContainer) {
      relatedContainer.innerHTML = relatedProperties.map((prop) => {
        const image = prop.images && prop.images.length > 0 ? prop.images[0] : '/images/placeholder.jpg';
        const price = formatPrice(prop.price, prop.currency);
        return `
          <div class="group">
            <a href="/propiedad/${prop.slug}" class="block bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden">
              <div class="relative h-48 overflow-hidden">
                <img src="${image}" alt="${prop.title}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110" loading="lazy" onerror="this.src='/images/placeholder.jpg'">
                ${prop.highlight ? '<div class="absolute top-2 left-2"><span class="px-2 py-1 bg-orange-600 text-white text-xs font-semibold rounded">⭐ Destacada</span></div>' : ''}
                <div class="absolute top-2 right-2">
                  <span class="px-2 py-1 bg-orange-600 text-white text-xs font-semibold rounded capitalize">${prop.operation}</span>
                </div>
              </div>
              <div class="p-4">
                <h3 class="font-bold text-gray-900 mb-2 line-clamp-2">${prop.title}</h3>
                <p class="text-sm text-gray-600 mb-2">${prop.city}${prop.neighborhood ? ', ' + prop.neighborhood : ''}</p>
                <p class="text-lg font-bold text-orange-600 mb-2">${price}</p>
                ${prop.bedrooms ? `<p class="text-sm text-gray-600">${prop.bedrooms} dormitorios</p>` : ''}
              </div>
            </a>
          </div>
        `;
      }).join('');
    }
  }
}

// Variable global para la galería
let galleryState = {
  currentImageIndex: 0,
  images: [],
  propertyTitle: ''
};

// Función para renderizar la galería con modal
function renderGalleryWithModal(property) {
  const galleryContainer = document.getElementById('gallery-container');
  if (!galleryContainer) return;
  
  if (!property.images || property.images.length === 0) {
    galleryContainer.innerHTML = `
      <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="h-64 bg-gray-200 rounded-lg flex items-center justify-center text-gray-400">
          Sin imágenes
        </div>
      </div>
    `;
    return;
  }
  
  // Inicializar estado de la galería
  galleryState.images = property.images;
  galleryState.propertyTitle = property.title;
  galleryState.currentImageIndex = 0;
  
  const renderGallery = () => {
    galleryContainer.innerHTML = `
      <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <!-- Imagen principal -->
        <div class="relative bg-gray-100 rounded-t-xl overflow-hidden" style="aspect-ratio: 16/9;">
          <img 
            id="main-gallery-image"
            src="${galleryState.images[galleryState.currentImageIndex]}" 
            alt="${galleryState.propertyTitle} - Imagen ${galleryState.currentImageIndex + 1}" 
            class="w-full h-full object-cover cursor-pointer hover:opacity-90 transition-opacity"
            loading="lazy" 
            onerror="this.src='/images/placeholder.jpg'"
            onclick="openImageModal(${galleryState.currentImageIndex})"
          />
          
          ${galleryState.images.length > 1 ? `
          <!-- Navegación -->
          <button
            onclick="galleryPrevious()"
            class="absolute left-2 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-75 transition-opacity z-10"
            aria-label="Imagen anterior"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
          </button>
          <button
            onclick="galleryNext()"
            class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-75 transition-opacity z-10"
            aria-label="Imagen siguiente"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </button>
          
          <!-- Contador -->
          <div class="absolute bottom-2 right-2 bg-black bg-opacity-50 text-white px-2 py-1 rounded text-sm">
            ${galleryState.currentImageIndex + 1} / ${galleryState.images.length}
          </div>
          ` : ''}
        </div>
        
        ${galleryState.images.length > 1 ? `
        <!-- Thumbnails -->
        <div class="p-2 flex space-x-2 overflow-x-auto scrollbar-hide">
          ${galleryState.images.map((img, idx) => `
            <button
              onclick="galleryGoTo(${idx})"
              class="flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden border-2 transition-all duration-200 ${
                idx === galleryState.currentImageIndex
                  ? 'border-orange-500 ring-2 ring-orange-200 shadow-lg'
                  : 'border-gray-200 hover:border-gray-300'
              }"
            >
              <img
                src="${img}"
                alt="Miniatura ${idx + 1}"
                class="w-full h-full object-cover transition-all duration-200 ${
                  idx === galleryState.currentImageIndex
                    ? 'opacity-100'
                    : 'opacity-70 hover:opacity-90'
                }"
                loading="lazy"
                onerror="this.src='/images/placeholder.jpg'"
              />
            </button>
          `).join('')}
        </div>
        ` : ''}
      </div>
    `;
  };
  
  // Funciones de navegación de la galería
  window.galleryPrevious = function() {
    galleryState.currentImageIndex = galleryState.currentImageIndex === 0 
      ? galleryState.images.length - 1 
      : galleryState.currentImageIndex - 1;
    renderGallery();
  };
  
  window.galleryNext = function() {
    galleryState.currentImageIndex = galleryState.currentImageIndex === galleryState.images.length - 1 
      ? 0 
      : galleryState.currentImageIndex + 1;
    renderGallery();
  };
  
  window.galleryGoTo = function(index) {
    galleryState.currentImageIndex = index;
    renderGallery();
  };
  
  window.openImageModal = function(index) {
    galleryState.currentImageIndex = index;
    showImageModal();
  };
  
  // Función para mostrar el modal
  const showImageModal = () => {
    // Crear o actualizar el modal
    let modal = document.getElementById('image-modal');
    if (!modal) {
      modal = document.createElement('div');
      modal.id = 'image-modal';
      modal.className = 'fixed inset-0 z-[9999] bg-black bg-opacity-90 flex items-center justify-center hidden';
      document.body.appendChild(modal);
    }
    
    modal.innerHTML = `
      <div class="relative w-full h-full flex items-center justify-center p-4">
        <!-- Botón cerrar -->
        <button
          onclick="closeImageModal()"
          class="absolute top-4 right-4 z-[10000] bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-75 transition-opacity"
          aria-label="Cerrar modal"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
        
        <!-- Imagen en pantalla completa -->
        <img
          id="modal-image"
          src="${galleryState.images[galleryState.currentImageIndex]}"
          alt="${galleryState.propertyTitle} - Imagen ${galleryState.currentImageIndex + 1}"
          class="max-w-full max-h-full object-contain"
        />
        
        ${galleryState.images.length > 1 ? `
        <!-- Navegación en modal -->
        <button
          onclick="modalPrevious()"
          class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-3 rounded-full hover:bg-opacity-75 transition-opacity"
          aria-label="Imagen anterior"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </button>
        <button
          onclick="modalNext()"
          class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-3 rounded-full hover:bg-opacity-75 transition-opacity"
          aria-label="Imagen siguiente"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </button>
        
        <!-- Contador en modal -->
        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 bg-black bg-opacity-50 text-white px-4 py-2 rounded-full text-lg">
          <span id="modal-counter">${galleryState.currentImageIndex + 1} / ${galleryState.images.length}</span>
        </div>
        ` : ''}
        
        <!-- Instrucciones en modal -->
        <div class="absolute top-4 left-4 bg-black bg-opacity-50 text-white px-3 py-2 rounded-lg text-sm">
          Usa ← → para navegar, ESC para cerrar
        </div>
      </div>
    `;
    
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
  };
  
  window.closeImageModal = function() {
    const modal = document.getElementById('image-modal');
    if (modal) {
      modal.classList.add('hidden');
      document.body.style.overflow = '';
    }
  };
  
  window.modalPrevious = function() {
    galleryState.currentImageIndex = galleryState.currentImageIndex === 0 
      ? galleryState.images.length - 1 
      : galleryState.currentImageIndex - 1;
    updateModalImage();
  };
  
  window.modalNext = function() {
    galleryState.currentImageIndex = galleryState.currentImageIndex === galleryState.images.length - 1 
      ? 0 
      : galleryState.currentImageIndex + 1;
    updateModalImage();
  };
  
  function updateModalImage() {
    const modalImage = document.getElementById('modal-image');
    const modalCounter = document.getElementById('modal-counter');
    if (modalImage) {
      modalImage.src = galleryState.images[galleryState.currentImageIndex];
      modalImage.alt = `${galleryState.propertyTitle} - Imagen ${galleryState.currentImageIndex + 1}`;
    }
    if (modalCounter) {
      modalCounter.textContent = `${galleryState.currentImageIndex + 1} / ${galleryState.images.length}`;
    }
  }
  
  // Manejar teclado para navegación
  const handleKeyDown = (e) => {
    const modal = document.getElementById('image-modal');
    if (modal && !modal.classList.contains('hidden')) {
      if (e.key === 'ArrowLeft') {
        e.preventDefault();
        modalPrevious();
      } else if (e.key === 'ArrowRight') {
        e.preventDefault();
        modalNext();
      } else if (e.key === 'Escape') {
        e.preventDefault();
        closeImageModal();
      }
    }
  };
  
  // Remover listener anterior si existe
  document.removeEventListener('keydown', handleKeyDown);
  document.addEventListener('keydown', handleKeyDown);
  
  // Renderizar galería inicial
  renderGallery();
}

// Función principal
document.addEventListener('DOMContentLoaded', async function() {
  // Obtener el slug de la URL
  // El .htaccess redirige /propiedad/[slug] a /propiedad.html?slug=[slug]
  let slug = null;
  
  // Primero intentar obtener del query string (viene del .htaccess)
  const urlParams = new URLSearchParams(window.location.search);
  slug = urlParams.get('slug');
  
  // Si no hay slug en el query string, intentar del pathname
  if (!slug) {
    const pathParts = window.location.pathname.split('/').filter(p => p);
    if (pathParts.length > 0 && pathParts[pathParts.length - 1] !== 'propiedad.html' && pathParts[pathParts.length - 1] !== 'propiedad') {
      slug = pathParts[pathParts.length - 1];
    }
  }

  if (!slug || slug === 'propiedad') {
    // Si no hay slug, mostrar error
    const loadingState = document.getElementById('loading-state');
    const errorState = document.getElementById('error-state');
    if (loadingState) loadingState.classList.add('hidden');
    if (errorState) errorState.classList.remove('hidden');
    return;
  }

  try {
    // Cargar propiedad
    const property = await loadProperty(slug);
    
    // Cargar propiedades relacionadas
    let relatedProperties = [];
    if (property.city && property.type) {
      relatedProperties = await loadRelatedProperties(property.city, property.type, property.id);
    }

    // Renderizar propiedad
    renderProperty(property, relatedProperties);
    
    // Renderizar galería con modal (después de que el HTML esté en el DOM)
    setTimeout(() => {
      renderGalleryWithModal(property);
    }, 100);
  } catch (error) {
    // Mostrar error
    const loadingState = document.getElementById('loading-state');
    const errorState = document.getElementById('error-state');
    if (loadingState) loadingState.classList.add('hidden');
    if (errorState) errorState.classList.remove('hidden');
  }
});

