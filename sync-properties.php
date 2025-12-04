<?php
/**
 * Script para sincronizar propiedades de MySQL a properties.ts
 * Ejecutar después de agregar/editar propiedades en el panel admin
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

try {
    $db = getDB();
    $stmt = $db->query('SELECT * FROM properties ORDER BY highlight DESC, listedAt DESC');
    $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $output = "export type Operation = 'venta' | 'alquiler';\n\n";
    $output .= "export type Property = {\n";
    $output .= "  id: string;\n";
    $output .= "  slug: string;\n";
    $output .= "  title: string;\n";
    $output .= "  address?: string;\n";
    $output .= "  city: string;\n";
    $output .= "  neighborhood?: string;\n";
    $output .= "  operation: Operation;\n";
    $output .= "  type: 'casa' | 'departamento' | 'local' | 'oficina' | 'terreno' | 'ph' | 'duplex';\n";
    $output .= "  price: number;\n";
    $output .= "  currency?: 'ARS' | 'USD';\n";
    $output .= "  coveredM2?: number;\n";
    $output .= "  totalM2?: number;\n";
    $output .= "  bedrooms?: number;\n";
    $output .= "  bathrooms?: number;\n";
    $output .= "  parking?: boolean | number;\n";
    $output .= "  expenses?: number;\n";
    $output .= "  orientation?: string;\n";
    $output .= "  condition?: 'a estrenar' | 'reciclado' | 'bueno' | 'a refaccionar';\n";
    $output .= "  year?: number;\n";
    $output .= "  amenities?: string[];\n";
    $output .= "  features?: string[];\n";
    $output .= "  description?: string;\n";
    $output .= "  images: string[];\n";
    $output .= "  videos?: Array<{ kind: 'file' | 'youtube' | 'vimeo'; src: string }>;\n";
    $output .= "  highlight?: boolean;\n";
    $output .= "  listedAt?: string;\n";
    $output .= "  location?: { lat: number; lng: number };\n";
    $output .= "};\n\n";
    $output .= "export const properties: Property[] = [\n";
    
    foreach ($properties as $prop) {
        $output .= "  {\n";
        $output .= "    id: '" . addslashes($prop['id']) . "',\n";
        $output .= "    slug: '" . addslashes($prop['slug']) . "',\n";
        $output .= "    title: '" . addslashes($prop['title']) . "',\n";
        
        if (!empty($prop['address'])) {
            $output .= "    address: '" . addslashes($prop['address']) . "',\n";
        }
        
        $output .= "    city: '" . addslashes($prop['city']) . "',\n";
        
        if (!empty($prop['neighborhood'])) {
            $output .= "    neighborhood: '" . addslashes($prop['neighborhood']) . "',\n";
        }
        
        $output .= "    operation: '" . $prop['operation'] . "',\n";
        $output .= "    type: '" . $prop['type'] . "',\n";
        $output .= "    price: " . floatval($prop['price']) . ",\n";
        $output .= "    currency: '" . $prop['currency'] . "',\n";
        
        if (!empty($prop['coveredM2'])) {
            $output .= "    coveredM2: " . intval($prop['coveredM2']) . ",\n";
        }
        
        if (!empty($prop['totalM2'])) {
            $output .= "    totalM2: " . intval($prop['totalM2']) . ",\n";
        }
        
        if (!empty($prop['bedrooms'])) {
            $output .= "    bedrooms: " . intval($prop['bedrooms']) . ",\n";
        }
        
        if (!empty($prop['bathrooms'])) {
            $output .= "    bathrooms: " . intval($prop['bathrooms']) . ",\n";
        }
        
        if (!empty($prop['parking'])) {
            $parking = is_numeric($prop['parking']) ? intval($prop['parking']) : ($prop['parking'] ? 1 : 0);
            $output .= "    parking: " . $parking . ",\n";
        }
        
        if (!empty($prop['expenses'])) {
            $output .= "    expenses: " . floatval($prop['expenses']) . ",\n";
        }
        
        if (!empty($prop['orientation'])) {
            $output .= "    orientation: '" . addslashes($prop['orientation']) . "',\n";
        }
        
        if (!empty($prop['condition'])) {
            $output .= "    condition: '" . addslashes($prop['condition']) . "',\n";
        }
        
        if (!empty($prop['year'])) {
            $output .= "    year: " . intval($prop['year']) . ",\n";
        }
        
        // Amenities
        $amenities = json_decode($prop['amenities'], true) ?? [];
        if (!empty($amenities)) {
            $amenitiesStr = array_map(function($a) {
                return "'" . addslashes($a) . "'";
            }, $amenities);
            $output .= "    amenities: [" . implode(', ', $amenitiesStr) . "],\n";
        }
        
        // Features
        $features = json_decode($prop['features'], true) ?? [];
        if (!empty($features)) {
            $featuresStr = array_map(function($f) {
                return "'" . addslashes($f) . "'";
            }, $features);
            $output .= "    features: [" . implode(', ', $featuresStr) . "],\n";
        }
        
        if (!empty($prop['description'])) {
            $output .= "    description: '" . addslashes($prop['description']) . "',\n";
        }
        
        // Images
        $images = json_decode($prop['images'], true) ?? [];
        if (!empty($images)) {
            $imagesStr = array_map(function($img) {
                return "'" . addslashes($img) . "'";
            }, $images);
            $output .= "    images: [" . implode(', ', $imagesStr) . "],\n";
        }
        
        if ($prop['highlight']) {
            $output .= "    highlight: true,\n";
        }
        
        if (!empty($prop['listedAt'])) {
            $output .= "    listedAt: '" . addslashes($prop['listedAt']) . "',\n";
        }
        
        if (!empty($prop['lat']) && !empty($prop['lng'])) {
            $output .= "    location: { lat: " . floatval($prop['lat']) . ", lng: " . floatval($prop['lng']) . " },\n";
        }
        
        $output .= "  },\n";
    }
    
    $output .= "];\n";
    
    // Guardar en el archivo
    $filePath = __DIR__ . '/src/data/properties.ts';
    if (file_put_contents($filePath, $output)) {
        echo "✅ Archivo properties.ts actualizado correctamente\n";
        echo "Total de propiedades sincronizadas: " . count($properties) . "\n";
        echo "\n";
        echo "Ahora ejecuta: npm run build\n";
        echo "Para regenerar el sitio con las nuevas propiedades.\n";
    } else {
        echo "❌ Error al escribir el archivo properties.ts\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Error al obtener propiedades: " . $e->getMessage() . "\n";
}

