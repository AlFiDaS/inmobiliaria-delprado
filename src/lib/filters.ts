import type { Property } from '@/data/properties';

export interface FilterOptions {
  operation?: 'venta' | 'alquiler';
  type?: 'casa' | 'departamento' | 'local' | 'oficina' | 'terreno' | 'ph' | 'duplex';
  city?: string;
  neighborhood?: string;
  minPrice?: number;
  maxPrice?: number;
  minBedrooms?: number;
  minBathrooms?: number;
  minM2?: number;
  maxM2?: number;
  parking?: boolean;
  highlight?: boolean;
}

export function filterProperties(properties: Property[], filters: FilterOptions): Property[] {
  return properties.filter(property => {
    // Filtro por operación
    if (filters.operation && property.operation !== filters.operation) {
      return false;
    }

    // Filtro por tipo
    if (filters.type && property.type !== filters.type) {
      return false;
    }

    // Filtro por ciudad
    if (filters.city && property.city !== filters.city) {
      return false;
    }

    // Filtro por barrio
    if (filters.neighborhood && property.neighborhood !== filters.neighborhood) {
      return false;
    }

    // Filtro por precio mínimo
    if (filters.minPrice && property.price < filters.minPrice) {
      return false;
    }

    // Filtro por precio máximo
    if (filters.maxPrice && property.price > filters.maxPrice) {
      return false;
    }

    // Filtro por dormitorios mínimos
    if (filters.minBedrooms && (!property.bedrooms || property.bedrooms < filters.minBedrooms)) {
      return false;
    }

    // Filtro por baños mínimos
    if (filters.minBathrooms && (!property.bathrooms || property.bathrooms < filters.minBathrooms)) {
      return false;
    }

    // Filtro por superficie mínima
    if (filters.minM2 && (!property.totalM2 || property.totalM2 < filters.minM2)) {
      return false;
    }

    // Filtro por superficie máxima
    if (filters.maxM2 && (!property.totalM2 || property.totalM2 > filters.maxM2)) {
      return false;
    }

    // Filtro por cochera
    if (filters.parking !== undefined) {
      if (filters.parking && !property.parking) {
        return false;
      }
      if (!filters.parking && property.parking) {
        return false;
      }
    }

    // Filtro por destacado
    if (filters.highlight !== undefined && property.highlight !== filters.highlight) {
      return false;
    }

    return true;
  });
}

export function sortProperties(properties: Property[], sortBy: 'price' | 'date' | 'm2' = 'date', order: 'asc' | 'desc' = 'desc'): Property[] {
  return [...properties].sort((a, b) => {
    let comparison = 0;

    switch (sortBy) {
      case 'price':
        comparison = a.price - b.price;
        break;
      case 'date':
        const dateA = a.listedAt ? new Date(a.listedAt).getTime() : 0;
        const dateB = b.listedAt ? new Date(b.listedAt).getTime() : 0;
        comparison = dateA - dateB;
        break;
      case 'm2':
        const m2A = a.totalM2 || a.coveredM2 || 0;
        const m2B = b.totalM2 || b.coveredM2 || 0;
        comparison = m2A - m2B;
        break;
    }

    return order === 'asc' ? comparison : -comparison;
  });
}

export function searchProperties(properties: Property[], query: string): Property[] {
  if (!query.trim()) return properties;

  const searchTerm = query.toLowerCase().trim();
  
  return properties.filter(property => {
    const searchableText = [
      property.title,
      property.address,
      property.neighborhood,
      property.city,
      property.description,
      property.type,
      property.operation
    ].filter(Boolean).join(' ').toLowerCase();

    return searchableText.includes(searchTerm);
  });
}
