<?php
/**
 * API para obtener propiedades desde MySQL
 * Endpoint: /api/properties.php
 * Parámetros opcionales:
 *   - operation: 'venta' | 'alquiler'
 *   - type: 'casa' | 'departamento' | etc.
 *   - city: nombre de la ciudad
 *   - highlight: 1 para solo destacadas
 *   - slug: slug específico de una propiedad
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

try {
    $db = getDB();
    
    // Obtener parámetros
    $operation = $_GET['operation'] ?? null;
    $type = $_GET['type'] ?? null;
    $city = $_GET['city'] ?? null;
    $highlight = isset($_GET['highlight']) ? intval($_GET['highlight']) : null;
    $slug = $_GET['slug'] ?? null;
    
    // Construir query
    $query = 'SELECT * FROM properties WHERE 1=1';
    $params = [];
    
    if ($operation) {
        $query .= ' AND operation = :operation';
        $params[':operation'] = $operation;
    }
    
    if ($type) {
        $query .= ' AND type = :type';
        $params[':type'] = $type;
    }
    
    if ($city) {
        $query .= ' AND city = :city';
        $params[':city'] = $city;
    }
    
    if ($highlight !== null) {
        $query .= ' AND highlight = :highlight';
        $params[':highlight'] = $highlight;
    }
    
    if ($slug) {
        $query .= ' AND slug = :slug';
        $params[':slug'] = $slug;
    }
    
    $query .= ' ORDER BY highlight DESC, listedAt DESC';
    
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Transformar datos al formato esperado por el frontend
    $result = array_map(function($prop) {
        $amenities = !empty($prop['amenities']) ? json_decode($prop['amenities'], true) : [];
        $amenities = is_array($amenities) ? $amenities : [];
        
        $features = !empty($prop['features']) ? json_decode($prop['features'], true) : [];
        $features = is_array($features) ? $features : [];
        
        $images = !empty($prop['images']) ? json_decode($prop['images'], true) : [];
        $images = is_array($images) ? $images : [];
        // Asegurar que las rutas de imágenes sean absolutas
        if (!empty($images)) {
            $images = array_map(function($img) {
                // Si la imagen ya tiene http:// o https://, dejarla como está
                if (strpos($img, 'http://') === 0 || strpos($img, 'https://') === 0) {
                    return $img;
                }
                // Si empieza con /, agregar el dominio completo
                if (strpos($img, '/') === 0) {
                    // Detectar si estamos en desarrollo o producción
                    $isDevelopment = strpos($_SERVER['HTTP_HOST'] ?? '', 'localhost') !== false;
                    $baseUrl = $isDevelopment ? 'http://localhost:8000' : SITE_URL;
                    return $baseUrl . $img;
                }
                return $img;
            }, $images);
        }
        
        $videos = !empty($prop['videos']) ? json_decode($prop['videos'], true) : null;
        $videos = is_array($videos) ? $videos : null;
        
        $property = [
            'id' => $prop['id'],
            'slug' => $prop['slug'],
            'title' => $prop['title'],
            'city' => $prop['city'],
            'operation' => $prop['operation'],
            'type' => $prop['type'],
            'price' => floatval($prop['price']),
            'currency' => $prop['currency'],
            'images' => $images,
            'highlight' => (bool)$prop['highlight'],
            'listedAt' => $prop['listedAt'],
        ];
        
        if (!empty($prop['address'])) {
            $property['address'] = $prop['address'];
        }
        
        if (!empty($prop['neighborhood'])) {
            $property['neighborhood'] = $prop['neighborhood'];
        }
        
        if (!empty($prop['coveredM2'])) {
            $property['coveredM2'] = intval($prop['coveredM2']);
        }
        
        if (!empty($prop['totalM2'])) {
            $property['totalM2'] = intval($prop['totalM2']);
        }
        
        if (!empty($prop['bedrooms'])) {
            $property['bedrooms'] = intval($prop['bedrooms']);
        }
        
        if (!empty($prop['bathrooms'])) {
            $property['bathrooms'] = intval($prop['bathrooms']);
        }
        
        if (!empty($prop['parking'])) {
            $property['parking'] = is_numeric($prop['parking']) ? intval($prop['parking']) : (bool)$prop['parking'];
        }
        
        if (!empty($prop['expenses'])) {
            $property['expenses'] = floatval($prop['expenses']);
        }
        
        if (!empty($prop['orientation'])) {
            $property['orientation'] = $prop['orientation'];
        }
        
        if (!empty($prop['condition'])) {
            $property['condition'] = $prop['condition'];
        }
        
        if (!empty($prop['year'])) {
            $property['year'] = intval($prop['year']);
        }
        
        if (!empty($amenities)) {
            $property['amenities'] = $amenities;
        }
        
        if (!empty($features)) {
            $property['features'] = $features;
        }
        
        if (!empty($prop['description'])) {
            $property['description'] = $prop['description'];
        }
        
        if (!empty($videos)) {
            $property['videos'] = $videos;
        }
        
        if (!empty($prop['lat']) && !empty($prop['lng'])) {
            $property['location'] = [
                'lat' => floatval($prop['lat']),
                'lng' => floatval($prop['lng'])
            ];
        }
        
        return $property;
    }, $properties);
    
    echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtener propiedades: ' . $e->getMessage()]);
}

