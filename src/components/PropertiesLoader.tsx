/**
 * Componente React para cargar propiedades dinámicamente desde la API
 */

import { useEffect, useState } from 'react';
import type { Property } from '../lib/api';
import { fetchProperties } from '../lib/api';

interface PropertiesLoaderProps {
  operation?: 'venta' | 'alquiler';
  type?: string;
  city?: string;
  highlight?: boolean;
  children: (properties: Property[], loading: boolean) => React.ReactNode;
}

export default function PropertiesLoader({ 
  operation, 
  type, 
  city, 
  highlight,
  children 
}: PropertiesLoaderProps) {
  const [properties, setProperties] = useState<Property[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    async function loadProperties() {
      setLoading(true);
      const data = await fetchProperties({ operation, type, city, highlight });
      setProperties(data);
      setLoading(false);
    }
    
    loadProperties();
  }, [operation, type, city, highlight]);

  return <>{children(properties, loading)}</>;
}

