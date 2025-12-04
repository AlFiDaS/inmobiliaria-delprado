/**
 * Funciones para obtener propiedades desde la API PHP
 */

export type Property = {
  id: string;
  slug: string;
  title: string;
  address?: string;
  city: string;
  neighborhood?: string;
  operation: 'venta' | 'alquiler';
  type: 'casa' | 'departamento' | 'local' | 'oficina' | 'terreno' | 'ph' | 'duplex';
  price: number;
  currency?: 'ARS' | 'USD';
  coveredM2?: number;
  totalM2?: number;
  bedrooms?: number;
  bathrooms?: number;
  parking?: boolean | number;
  expenses?: number;
  orientation?: string;
  condition?: 'a estrenar' | 'reciclado' | 'bueno' | 'a refaccionar';
  year?: number;
  amenities?: string[];
  features?: string[];
  description?: string;
  images: string[];
  videos?: Array<{ kind: 'file' | 'youtube' | 'vimeo'; src: string }>;
  highlight?: boolean;
  listedAt?: string;
  location?: { lat: number; lng: number };
};

interface FetchPropertiesOptions {
  operation?: 'venta' | 'alquiler';
  type?: string;
  city?: string;
  highlight?: boolean;
  slug?: string;
}

/**
 * Obtiene propiedades desde la API PHP
 */
export async function fetchProperties(options: FetchPropertiesOptions = {}): Promise<Property[]> {
  const params = new URLSearchParams();
  
  if (options.operation) params.append('operation', options.operation);
  if (options.type) params.append('type', options.type);
  if (options.city) params.append('city', options.city);
  if (options.highlight !== undefined) params.append('highlight', options.highlight ? '1' : '0');
  if (options.slug) params.append('slug', options.slug);
  
  const url = `/api/properties.php${params.toString() ? '?' + params.toString() : ''}`;
  
  try {
    const response = await fetch(url);
    if (!response.ok) {
      throw new Error(`Error al obtener propiedades: ${response.statusText}`);
    }
    return await response.json();
  } catch (error) {
    console.error('Error al obtener propiedades:', error);
    return [];
  }
}

/**
 * Obtiene una propiedad por slug
 */
export async function fetchPropertyBySlug(slug: string): Promise<Property | null> {
  const properties = await fetchProperties({ slug });
  return properties.length > 0 ? properties[0] : null;
}

