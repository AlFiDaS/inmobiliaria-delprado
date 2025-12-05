/**
 * Función helper para cache busting de imágenes
 * Agrega un query string con timestamp a las URLs de imágenes para forzar la recarga
 */

/**
 * Agrega cache busting a una URL de imagen
 * @param {string} imageUrl - URL de la imagen
 * @param {string|number} timestamp - Timestamp opcional (si no se provee, usa la fecha actual)
 * @returns {string} URL con cache busting
 */
function addCacheBust(imageUrl, timestamp = null) {
  // Si la URL ya tiene query parameters, usar &, si no, usar ?
  const separator = imageUrl.includes('?') ? '&' : '?';
  
  // Si no se proporciona timestamp, usar la fecha actual en segundos
  const cacheParam = timestamp || Math.floor(Date.now() / 1000);
  
  // Agregar parámetro de cache busting
  return `${imageUrl}${separator}v=${cacheParam}`;
}

/**
 * Agrega cache busting a múltiples URLs de imágenes
 * @param {string[]} imageUrls - Array de URLs de imágenes
 * @param {string|number} timestamp - Timestamp opcional
 * @returns {string[]} Array de URLs con cache busting
 */
function addCacheBustToArray(imageUrls, timestamp = null) {
  if (!Array.isArray(imageUrls)) {
    return imageUrls;
  }
  return imageUrls.map(url => addCacheBust(url, timestamp));
}

/**
 * Versión global de caché que se puede actualizar al hacer deploy
 * Cambia este valor cuando hagas un deploy para invalidar todas las cachés
 */
const CACHE_VERSION = '2025.12.04';

/**
 * Agrega cache busting usando la versión global
 * @param {string} imageUrl - URL de la imagen
 * @returns {string} URL con cache busting
 */
function addCacheBustVersion(imageUrl) {
  const separator = imageUrl.includes('?') ? '&' : '?';
  return `${imageUrl}${separator}v=${CACHE_VERSION}`;
}

/**
 * Agrega cache busting usando timestamp de última modificación de la propiedad
 * Ideal para imágenes de propiedades que tienen un campo listedAt
 * @param {string} imageUrl - URL de la imagen
 * @param {string} listedAt - Fecha de listado en formato ISO (ej: '2025-12-04 01:07:00')
 * @returns {string} URL con cache busting basado en fecha de listado
 */
function addCacheBustFromDate(imageUrl, listedAt) {
  if (!listedAt) {
    return addCacheBustVersion(imageUrl);
  }
  
  // Convertir fecha a timestamp
  const date = new Date(listedAt);
  const timestamp = Math.floor(date.getTime() / 1000);
  
  return addCacheBust(imageUrl, timestamp);
}

