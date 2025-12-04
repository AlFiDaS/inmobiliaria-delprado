<?php
/**
 * Funciones para generar slugs URL-friendly
 */

/**
 * Convierte texto a slug (URL-friendly)
 */
function slugify($text) {
    // Convertir a minúsculas
    $text = mb_strtolower($text, 'UTF-8');
    
    // Reemplazar caracteres especiales en español
    $text = str_replace(
        ['á', 'é', 'í', 'ó', 'ú', 'ñ', 'ü'],
        ['a', 'e', 'i', 'o', 'u', 'n', 'u'],
        $text
    );
    
    // Remover caracteres especiales, dejar solo letras, números y espacios
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    
    // Reemplazar espacios y guiones múltiples por un solo guion
    $text = preg_replace('/[\s-]+/', '-', $text);
    
    // Remover guiones al inicio y final
    $text = trim($text, '-');
    
    return $text;
}

/**
 * Genera un slug único agregando un sufijo numérico si es necesario
 */
function generateUniqueSlug($text, $excludeId = null) {
    $baseSlug = slugify($text);
    $slug = $baseSlug;
    $counter = 1;
    
    try {
        $db = getDB();
        
        while (true) {
            $query = 'SELECT id FROM properties WHERE slug = :slug';
            $params = [':slug' => $slug];
            
            if ($excludeId) {
                $query .= ' AND id != :exclude_id';
                $params[':exclude_id'] = $excludeId;
            }
            
            $stmt = $db->prepare($query);
            $stmt->execute($params);
            
            if ($stmt->rowCount() === 0) {
                return $slug;
            }
            
            $slug = $baseSlug . '-' . $counter;
            $counter++;
            
            // Prevenir loops infinitos
            if ($counter > 1000) {
                $slug = $baseSlug . '-' . time();
                break;
            }
        }
        
        return $slug;
    } catch (PDOException $e) {
        error_log('Error al generar slug único: ' . $e->getMessage());
        // Si hay error, retornar slug con timestamp
        return $baseSlug . '-' . time();
    }
}
