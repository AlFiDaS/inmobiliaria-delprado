export type Operation = 'venta' | 'alquiler';

export type Property = {
  id: string;
  slug: string;
  title: string;
  address?: string;
  city: string;
  neighborhood?: string;
  operation: Operation;
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

export const properties: Property[] = [
  {
    id: 'PROP1',
    slug: 'casa-belgrano',
    title: 'Belgrano 2380',
    address: 'Belgrano 2300',
    city: 'Corrientes',
    operation: 'venta',
    type: 'casa',
    price: 500000,
    currency: 'USD',
    coveredM2: 300,
    totalM2: 500,
    bedrooms: 3,
    bathrooms: 5,
    parking: 1,
    orientation: 'sur',
    condition: 'bueno',
    year: 2015,
    amenities: ['Pileta'],
    features: ['Luminoso'],
    description: 'Hola hola hola',
    images: ['/images/properties/venta/casa-belgrano/r0.jpg'],
    highlight: true,
    listedAt: '2025-12-04 01:07:00',
  },
  {
    id: 'ALQUI001',
    slug: 'pje-torrent-970',
    title: 'Departamento 1 dormitorio',
    address: 'Pasaje Torrent 970',
    city: 'Corrientes',
    operation: 'alquiler',
    type: 'departamento',
    price: 500000,
    currency: 'ARS',
    coveredM2: 40,
    totalM2: 40,
    bedrooms: 1,
    bathrooms: 1,
    expenses: 50000,
    condition: 'reciclado',
    year: 2024,
    images: ['/images/properties/alquiler/pje-torrent-970/r0.jpg'],
    listedAt: '2025-12-04 01:09:00',
  },
];
