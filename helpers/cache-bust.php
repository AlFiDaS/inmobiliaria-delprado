<?php
/**
 * Helper para cache busting de imágenes
 * Agrega un parámetro de versión basado en el tiempo de modificación del archivo
 */

require_once __DIR__ . '/../config.php';

/**
 * Agrega cache busting a una URL de imagen
 * @param string $imagePath Ruta de la imagen (relativa o absoluta)
 * @param string|null $listedAt Fecha de listado opcional (formato MySQL datetime)
 * @return string URL con cache busting
 */
function addCacheBustToImage($imagePath, $listedAt = null) {
    // Si es placeholder, no agregar cache busting
    if (empty($imagePath) || $imagePath === '/images/placeholder.jpg' || strpos($imagePath, 'placeholder.jpg') !== false) {
        return $imagePath;
    }
    
    // Si ya tiene parámetros de query, detectar el separador
    $separator = strpos($imagePath, '?') !== false ? '&' : '?';
    
    // Intentar obtener el tiempo de modificación del archivo
    $timestamp = null;
    
    // Construir ruta completa del archivo
    $filePath = null;
    
    // Si la ruta empieza con /, es relativa desde DOCUMENT_ROOT
    if (strpos($imagePath, '/') === 0) {
        if (isset($_SERVER['DOCUMENT_ROOT']) && $_SERVER['DOCUMENT_ROOT']) {
            $filePath = $_SERVER['DOCUMENT_ROOT'] . $imagePath;
        } elseif (defined('UPLOAD_BASE_PATH')) {
            // Intentar construir desde UPLOAD_BASE_PATH
            $relativePath = str_replace('/images/properties/', '', $imagePath);
            if ($relativePath !== $imagePath) {
                // Es una imagen de propiedades
                $filePath = UPLOAD_BASE_PATH . '/' . dirname($relativePath) . '/' . basename($imagePath);
            }
        }
    }
    
    // Si el archivo existe, usar su tiempo de modificación
    if ($filePath && file_exists($filePath)) {
        $timestamp = filemtime($filePath);
    } elseif ($listedAt) {
        // Fallback: usar listedAt
        try {
            $date = new DateTime($listedAt);
            $timestamp = $date->getTimestamp();
        } catch (Exception $e) {
            // Si falla, usar timestamp actual
            $timestamp = time();
        }
    } else {
        // Último fallback: timestamp actual
        $timestamp = time();
    }
    
    // Agregar cache busting
    return $imagePath . $separator . 'v=' . $timestamp;
}

/**
 * Agrega cache busting a múltiples URLs de imágenes
 * @param array $imagePaths Array de rutas de imágenes
 * @param string|null $listedAt Fecha de listado opcional
 * @return array Array de URLs con cache busting
 */
function addCacheBustToImages($imagePaths, $listedAt = null) {
    if (!is_array($imagePaths)) {
        return $imagePaths;
    }
    
    return array_map(function($path) use ($listedAt) {
        return addCacheBustToImage($path, $listedAt);
    }, $imagePaths);
}

