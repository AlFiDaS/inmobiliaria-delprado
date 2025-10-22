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
  price: number; // en ARS
  currency?: 'ARS' | 'USD';
  coveredM2?: number;
  totalM2?: number;
  bedrooms?: number;
  bathrooms?: number;
  parking?: boolean | number;
  expenses?: number; // expensas
  orientation?: string;
  condition?: 'a estrenar' | 'reciclado' | 'bueno' | 'a refaccionar';
  year?: number;
  amenities?: string[];
  features?: string[];
  description?: string;
  images: string[]; // rutas en /public/images/...
  videos?: Array<{ kind: 'file' | 'youtube' | 'vimeo'; src: string }>;
  highlight?: boolean;
  listedAt?: string; // ISO
  location?: { lat: number; lng: number };
};

export const properties: Property[] = [
  {
    id: 'PROP001',
    slug: 'casa-moderna-centro-corrientes',
    title: 'Casa moderna en Centro de Corrientes',
    address: 'Av. 3 de Abril 1200',
    city: 'Corrientes',
    neighborhood: 'Centro',
    operation: 'venta',
    type: 'casa',
    price: 105000,
    currency: 'USD',
    coveredM2: 120,
    totalM2: 200,
    bedrooms: 3,
    bathrooms: 2,
    parking: 1,
    orientation: 'Norte',
    condition: 'bueno',
    year: 2015,
    amenities: ['Jardín', 'Parrilla', 'Quincho'],
    features: ['Aire acondicionado', 'Calefacción', 'Alarma'],
    description: 'Hermosa casa en el centro de Corrientes, con amplios espacios y un jardín privado. Ideal para familias que buscan tranquilidad en la ciudad.',
    images: [
      '/images/properties/base_1.jpg',
      '/images/properties/base_1.jpg',
      '/images/properties/base_1.jpg',
      '/images/properties/base_1.jpg'
    ],
    highlight: true,
    listedAt: '2024-01-15T10:00:00Z',
    location: { lat: -34.5889, lng: -58.3974 }
  },
  {
    id: 'PROP002',
    slug: 'depto-2-ambientes-centro-corrientes',
    title: 'Departamento 2 ambientes en Centro de Corrientes',
    address: 'Av. Sarmiento 800',
    city: 'Corrientes',
    neighborhood: 'Centro',
    operation: 'alquiler',
    type: 'departamento',
    price: 450000,
    currency: 'ARS',
    coveredM2: 65,
    bedrooms: 1,
    bathrooms: 1,
    parking: false,
    expenses: 25000,
    orientation: 'Este',
    condition: 'bueno',
    year: 2018,
    amenities: ['Balcón', 'Ascensor', 'Seguridad 24hs'],
    features: ['Aire acondicionado', 'Calefacción central'],
    description: 'Departamento luminoso y moderno en el centro de Corrientes, cerca de todos los servicios y transporte público.',
    images: [
      '/images/properties/base_1.jpg',
      '/images/properties/base_1.jpg',
      '/images/properties/base_1.jpg'
    ],
    listedAt: '2024-01-10T14:30:00Z',
    location: { lat: -34.5895, lng: -58.3974 }
  },
  {
    id: 'PROP003',
    slug: 'casa-quinta-costanera-corrientes',
    title: 'Casa quinta en Costanera de Corrientes',
    address: 'Av. Costanera 2000',
    city: 'Corrientes',
    operation: 'venta',
    type: 'casa',
    price: 150000,
    currency: 'USD',
    coveredM2: 200,
    totalM2: 800,
    bedrooms: 4,
    bathrooms: 3,
    parking: 3,
    orientation: 'Norte',
    condition: 'a estrenar',
    year: 2023,
    amenities: ['Pileta', 'Jardín', 'Parrilla', 'Quincho', 'Cancha de tenis'],
    features: ['Aire acondicionado', 'Calefacción', 'Alarma', 'Domótica'],
    description: 'Exclusiva casa quinta en la costanera de Corrientes con amplios jardines y todas las comodidades. Perfecta para familias que buscan espacio y tranquilidad.',
    images: [
      '/images/properties/base_1.jpg',
      '/images/properties/base_1.jpg',
      '/images/properties/base_1.jpg',
      '/images/properties/base_1.jpg',
      '/images/properties/base_1.jpg'
    ],
    videos: [
      { kind: 'youtube', src: 'https://www.youtube.com/embed/example1' }
    ],
    highlight: true,
    listedAt: '2024-01-20T09:15:00Z',
    location: { lat: -34.4732, lng: -58.5277 }
  },
  {
    id: 'PROP004',
    slug: 'local-comercial-centro-corrientes',
    title: 'Local comercial en Centro de Corrientes',
    address: 'Av. 3 de Abril 500',
    city: 'Corrientes',
    neighborhood: 'Centro',
    operation: 'alquiler',
    type: 'local',
    price: 800000,
    currency: 'ARS',
    coveredM2: 45,
    totalM2: 45,
    orientation: 'Oeste',
    condition: 'bueno',
    year: 2010,
    amenities: ['Vidriera', 'Baño', 'Aire acondicionado'],
    features: ['Excelente ubicación', 'Alto tránsito peatonal'],
    description: 'Local comercial en el centro de Corrientes, ideal para comercio. Excelente ubicación con alto tránsito peatonal.',
    images: [
      '/images/properties/base_1.jpg',
      '/images/properties/base_1.jpg'
    ],
    listedAt: '2024-01-12T16:45:00Z',
    location: { lat: -34.6037, lng: -58.3816 }
  },
  {
    id: 'PROP005',
    slug: 'depto-3-ambientes-villa-san-martin',
    title: 'Departamento 3 ambientes en Villa San Martín',
    address: 'Av. Sarmiento 1500',
    city: 'Corrientes',
    neighborhood: 'Villa San Martín',
    operation: 'venta',
    type: 'departamento',
    price: 75000,
    currency: 'USD',
    coveredM2: 85,
    bedrooms: 2,
    bathrooms: 2,
    parking: 1,
    expenses: 40000,
    orientation: 'Norte',
    condition: 'reciclado',
    year: 2020,
    amenities: ['Balcón', 'Ascensor', 'Seguridad 24hs', 'Gimnasio'],
    features: ['Aire acondicionado', 'Calefacción', 'Pisos de madera'],
    description: 'Departamento completamente reciclado en Villa San Martín, Corrientes, con excelente distribución y todas las comodidades modernas.',
    images: [
      '/images/properties/base_1.jpg',
      '/images/properties/base_1.jpg',
      '/images/properties/base_1.jpg',
      '/images/properties/base_1.jpg'
    ],
    listedAt: '2024-01-18T11:20:00Z',
    location: { lat: -34.5627, lng: -58.4584 }
  },
  {
    id: 'PROP006',
    slug: 'casa-barrio-sur-corrientes',
    title: 'Casa en Barrio Sur de Corrientes',
    address: 'Av. Independencia 2500',
    city: 'Corrientes',
    neighborhood: 'Barrio Sur',
    operation: 'alquiler',
    type: 'casa',
    price: 650000,
    currency: 'ARS',
    coveredM2: 150,
    totalM2: 180,
    bedrooms: 3,
    bathrooms: 2,
    parking: 1,
    orientation: 'Sur',
    condition: 'bueno',
    year: 2012,
    amenities: ['Jardín', 'Parrilla'],
    features: ['Aire acondicionado', 'Calefacción', 'Alarma'],
    description: 'Casa cómoda en Barrio Sur de Corrientes, con jardín y parrilla. Ideal para familias.',
    images: [
      '/images/properties/base_1.jpg',
      '/images/properties/base_1.jpg',
      '/images/properties/base_1.jpg'
    ],
    listedAt: '2024-01-14T13:10:00Z',
    location: { lat: -34.5895, lng: -58.3974 }
  },
  {
    id: 'PROP007',
    slug: 'oficina-centro-corrientes',
    title: 'Oficina en centro de Corrientes',
    address: 'Av. 3 de Abril 1000',
    city: 'Corrientes',
    neighborhood: 'Centro',
    operation: 'alquiler',
    type: 'oficina',
    price: 1200000,
    currency: 'ARS',
    coveredM2: 80,
    totalM2: 80,
    orientation: 'Norte',
    condition: 'a estrenar',
    year: 2023,
    amenities: ['Vista panorámica', 'Ascensor', 'Seguridad 24hs'],
    features: ['Aire acondicionado', 'Internet', 'Sistema de climatización central'],
    description: 'Oficina moderna en el centro de Corrientes con vista panorámica de la ciudad. Ideal para empresas.',
    images: [
      '/images/properties/base_1.jpg',
      '/images/properties/base_1.jpg',
      '/images/properties/base_1.jpg'
    ],
    highlight: true,
    listedAt: '2024-01-16T08:30:00Z',
    location: { lat: -34.6037, lng: -58.3816 }
  },
  {
    id: 'PROP008',
    slug: 'terreno-residencial-corrientes',
    title: 'Terreno residencial en Corrientes',
    address: 'Ruta Nacional 12 km 5',
    city: 'Corrientes',
    operation: 'venta',
    type: 'terreno',
    price: 35000,
    currency: 'USD',
    totalM2: 1000,
    orientation: 'Norte',
    condition: 'bueno',
    amenities: ['Servicios', 'Acceso asfaltado'],
    features: ['Zona residencial', 'Cerca de colegios'],
    description: 'Terreno residencial en Corrientes, con todos los servicios y acceso asfaltado. Ideal para construir tu casa.',
    images: [
      '/images/properties/base_1.jpg',
      '/images/properties/base_1.jpg'
    ],
    listedAt: '2024-01-22T15:45:00Z',
    location: { lat: -34.4581, lng: -58.9142 }
  },
  {
    id: 'PROP009',
    slug: 'ph-centro-corrientes',
    title: 'PH en Centro de Corrientes',
    address: 'Av. Sarmiento 600',
    city: 'Corrientes',
    neighborhood: 'Centro',
    operation: 'venta',
    type: 'ph',
    price: 55000,
    currency: 'USD',
    coveredM2: 95,
    totalM2: 120,
    bedrooms: 2,
    bathrooms: 2,
    parking: 1,
    orientation: 'Este',
    condition: 'bueno',
    year: 2016,
    amenities: ['Patio', 'Parrilla'],
    features: ['Aire acondicionado', 'Calefacción', 'Pisos de madera'],
    description: 'PH cómodo en el centro de Corrientes con patio y parrilla. Excelente ubicación cerca del centro comercial.',
    images: [
      '/images/properties/base_1.jpg',
      '/images/properties/base_1.jpg',
      '/images/properties/base_1.jpg'
    ],
    listedAt: '2024-01-19T12:00:00Z',
    location: { lat: -34.6000, lng: -58.4333 }
  },
  {
    id: 'PROP010',
    slug: 'duplex-costanera-corrientes',
    title: 'Duplex en Costanera de Corrientes',
    address: 'Av. Costanera 1500',
    city: 'Corrientes',
    neighborhood: 'Costanera',
    operation: 'alquiler',
    type: 'duplex',
    price: 900000,
    currency: 'ARS',
    coveredM2: 110,
    totalM2: 110,
    bedrooms: 3,
    bathrooms: 2,
    parking: 1,
    expenses: 30000,
    orientation: 'Norte',
    condition: 'bueno',
    year: 2019,
    amenities: ['Terraza', 'Ascensor', 'Seguridad 24hs'],
    features: ['Aire acondicionado', 'Calefacción', 'Balcón'],
    description: 'Duplex moderno en la costanera de Corrientes con terraza privada. Excelente distribución y luminosidad.',
    images: [
      '/images/properties/base_1.jpg',
      '/images/properties/base_1.jpg',
      '/images/properties/base_1.jpg',
      '/images/properties/base_1.jpg'
    ],
    highlight: true,
    listedAt: '2024-01-17T10:15:00Z',
    location: { lat: -34.5472, lng: -58.4600 }
  },
  {
    id: 'PROP011',
    slug: 'casa-quinta-residencial-corrientes',
    title: 'Casa quinta residencial en Corrientes',
    address: 'Ruta Nacional 12 km 8',
    city: 'Corrientes',
    neighborhood: 'Zona Norte',
    operation: 'venta',
    type: 'casa',
    price: 125000,
    currency: 'USD',
    coveredM2: 180,
    totalM2: 600,
    bedrooms: 4,
    bathrooms: 3,
    parking: 2,
    orientation: 'Norte',
    condition: 'bueno',
    year: 2018,
    amenities: ['Pileta', 'Jardín', 'Parrilla', 'Quincho', 'Cancha de fútbol'],
    features: ['Aire acondicionado', 'Calefacción', 'Alarma', 'Cochera cubierta'],
    description: 'Hermosa casa quinta en zona residencial de Corrientes, con amplios espacios verdes y todas las comodidades para la familia.',
    images: [
      '/images/properties/base_1.jpg',
      '/images/properties/base_1.jpg',
      '/images/properties/base_1.jpg',
      '/images/properties/base_1.jpg',
      '/images/properties/base_1.jpg'
    ],
    highlight: true,
    listedAt: '2024-01-20T09:30:00Z',
    location: { lat: -27.4692, lng: -58.8306 }
  },
  {
    id: 'PROP012',
    slug: 'local-comercial-peatonal-corrientes',
    title: 'Local comercial en peatonal de Corrientes',
    address: 'Peatonal Junín 200',
    city: 'Corrientes',
    neighborhood: 'Centro',
    operation: 'venta',
    type: 'local',
    price: 85000,
    currency: 'USD',
    coveredM2: 60,
    totalM2: 60,
    orientation: 'Norte',
    condition: 'bueno',
    year: 2015,
    amenities: ['Vidriera', 'Baño', 'Aire acondicionado', 'Depósito'],
    features: ['Excelente ubicación', 'Alto tránsito peatonal', 'Frente a plaza'],
    description: 'Local comercial en la peatonal de Corrientes, con excelente ubicación y alto tránsito peatonal. Ideal para comercio.',
    images: [
      '/images/properties/base_1.jpg',
      '/images/properties/base_1.jpg',
      '/images/properties/base_1.jpg'
    ],
    listedAt: '2024-01-21T16:20:00Z',
    location: { lat: -27.4692, lng: -58.8306 }
  },
  {
    id: 'PROP013',
    slug: 'depto-pasaje-la-rosada-1842',
    title: 'Departamento en venta - Luminoso y con excelente vista en balcón',
    address: 'Pasaje La Rosada 1842',
    city: 'Corrientes',
    neighborhood: 'La Rosada',
    operation: 'venta',
    type: 'departamento',
    price: 45000,
    currency: 'USD',
    coveredM2: 38,
    bedrooms: 1,
    bathrooms: 1,
    orientation: 'Frente',
    condition: 'bueno',
    amenities: ['Balcón', 'Ascensor', 'Excelente vista'],
    features: ['Luminoso', 'Vista panorámica', 'Ubicado en tercer piso'],
    description: 'Departamento luminoso y con excelente vista en balcón, ubicado en el tercer piso (frente). Consta de un dormitorio y tiene aproximadamente 38 m2. El edificio cuenta con ascensor.',
    images: [
      '/images/properties/venta/Pje La Rosada 1842/r0.jpg',
      '/images/properties/venta/Pje La Rosada 1842/r1.jpg',
      '/images/properties/venta/Pje La Rosada 1842/r2.jpg',
      '/images/properties/venta/Pje La Rosada 1842/r3.jpg',
      '/images/properties/venta/Pje La Rosada 1842/r4.jpg',
      '/images/properties/venta/Pje La Rosada 1842/r5.jpg',
      '/images/properties/venta/Pje La Rosada 1842/r6.jpg',
      '/images/properties/venta/Pje La Rosada 1842/r7.jpg',
      '/images/properties/venta/Pje La Rosada 1842/r8.jpg',
      '/images/properties/venta/Pje La Rosada 1842/r9.jpg'
    ],
    highlight: true,
    listedAt: '2024-01-25T10:00:00Z',
    location: { lat: -27.4692, lng: -58.8306 }
  }
];

// Export type is already declared above
