<?php
/**
 * Funciones para manejar la subida de imágenes
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';

/**
 * Valida si un archivo es una imagen válida
 */
function validateImage($file) {
    $errors = [];
    
    // Verificar que se subió un archivo
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Error al subir el archivo. Código: ' . $file['error'];
        return $errors;
    }
    
    // Verificar tamaño
    if ($file['size'] > UPLOAD_MAX_SIZE) {
        $errors[] = 'El archivo es demasiado grande. Máximo: ' . (UPLOAD_MAX_SIZE / 1024 / 1024) . ' MB';
    }
    
    // Verificar tipo MIME
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, UPLOAD_ALLOWED_TYPES)) {
        $errors[] = 'Tipo de archivo no permitido. Solo se permiten: JPG, PNG, WEBP';
    }
    
    // Verificar extensión
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, UPLOAD_ALLOWED_EXTENSIONS)) {
        $errors[] = 'Extensión no permitida. Solo se permiten: .jpg, .jpeg, .png, .webp';
    }
    
    // Verificar que sea realmente una imagen
    $imageInfo = @getimagesize($file['tmp_name']);
    if ($imageInfo === false) {
        $errors[] = 'El archivo no es una imagen válida';
    }
    
    return $errors;
}

/**
 * Crea un thumbnail de una imagen
 */
function createThumbnail($sourcePath, $destinationPath, $maxWidth, $maxHeight) {
    if (!function_exists('imagecreatefromjpeg')) {
        error_log('GD library no está disponible');
        return false;
    }
    
    $imageInfo = getimagesize($sourcePath);
    if ($imageInfo === false) {
        return false;
    }
    
    $sourceWidth = $imageInfo[0];
    $sourceHeight = $imageInfo[1];
    $mimeType = $imageInfo['mime'];
    
    // Calcular dimensiones manteniendo proporción
    $ratio = min($maxWidth / $sourceWidth, $maxHeight / $sourceHeight);
    $newWidth = (int)($sourceWidth * $ratio);
    $newHeight = (int)($sourceHeight * $ratio);
    
    // Crear imagen desde archivo
    switch ($mimeType) {
        case 'image/jpeg':
            $sourceImage = imagecreatefromjpeg($sourcePath);
            break;
        case 'image/png':
            $sourceImage = imagecreatefrompng($sourcePath);
            break;
        case 'image/webp':
            $sourceImage = imagecreatefromwebp($sourcePath);
            break;
        default:
            return false;
    }
    
    if ($sourceImage === false) {
        return false;
    }
    
    // Crear imagen de destino
    $thumbnail = imagecreatetruecolor($newWidth, $newHeight);
    
    // Preservar transparencia para PNG
    if ($mimeType === 'image/png') {
        imagealphablending($thumbnail, false);
        imagesavealpha($thumbnail, true);
        $transparent = imagecolorallocatealpha($thumbnail, 255, 255, 255, 127);
        imagefilledrectangle($thumbnail, 0, 0, $newWidth, $newHeight, $transparent);
    }
    
    // Redimensionar
    imagecopyresampled(
        $thumbnail,
        $sourceImage,
        0, 0, 0, 0,
        $newWidth, $newHeight,
        $sourceWidth, $sourceHeight
    );
    
    // Guardar thumbnail
    $success = false;
    switch ($mimeType) {
        case 'image/jpeg':
            $success = imagejpeg($thumbnail, $destinationPath, 85);
            break;
        case 'image/png':
            $success = imagepng($thumbnail, $destinationPath, 6);
            break;
        case 'image/webp':
            $success = imagewebp($thumbnail, $destinationPath, 85);
            break;
    }
    
    imagedestroy($sourceImage);
    imagedestroy($thumbnail);
    
    return $success;
}

/**
 * Procesa y guarda una imagen subida
 */
function processUploadedImage($file, $propertySlug, $operation, $imageIndex) {
    $errors = validateImage($file);
    if (!empty($errors)) {
        return ['success' => false, 'errors' => $errors];
    }
    
    // Crear directorio si no existe
    $uploadDir = UPLOAD_BASE_PATH . '/' . $operation . '/' . $propertySlug;
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            return ['success' => false, 'errors' => ['No se pudo crear el directorio de imágenes']];
        }
    }
    
    // Generar nombre de archivo seguro
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = 'r' . $imageIndex . '.' . $extension;
    $filepath = $uploadDir . '/' . $filename;
    
    // Mover archivo subido
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => false, 'errors' => ['No se pudo guardar el archivo']];
    }
    
    // Crear thumbnail si GD está disponible
    if (function_exists('imagecreatefromjpeg')) {
        $thumbnailPath = $uploadDir . '/thumb_' . $filename;
        createThumbnail($filepath, $thumbnailPath, THUMBNAIL_WIDTH, THUMBNAIL_HEIGHT);
    }
    
    // Redimensionar imagen principal si es muy grande
    if (function_exists('imagecreatefromjpeg')) {
        $imageInfo = getimagesize($filepath);
        if ($imageInfo && ($imageInfo[0] > MAIN_IMAGE_WIDTH || $imageInfo[1] > MAIN_IMAGE_HEIGHT)) {
            $tempPath = $filepath . '.tmp';
            createThumbnail($filepath, $tempPath, MAIN_IMAGE_WIDTH, MAIN_IMAGE_HEIGHT);
            if (file_exists($tempPath)) {
                rename($tempPath, $filepath);
            }
        }
    }
    
    // Retornar ruta relativa desde la raíz del sitio
    $relativePath = '/images/properties/' . $operation . '/' . $propertySlug . '/' . $filename;
    
    return [
        'success' => true,
        'path' => $relativePath,
        'full_path' => $filepath
    ];
}

/**
 * Elimina una imagen y su thumbnail
 */
function deleteImage($imagePath) {
    // Convertir ruta relativa a absoluta si es necesario
    if (strpos($imagePath, '/') === 0) {
        $imagePath = $_SERVER['DOCUMENT_ROOT'] . $imagePath;
    }
    
    $deleted = false;
    if (file_exists($imagePath)) {
        $deleted = unlink($imagePath);
    }
    
    // Eliminar thumbnail
    $thumbnailPath = dirname($imagePath) . '/thumb_' . basename($imagePath);
    if (file_exists($thumbnailPath)) {
        @unlink($thumbnailPath);
    }
    
    return $deleted;
}

/**
 * Elimina todas las imágenes de una propiedad
 */
function deletePropertyImages($propertySlug, $operation) {
    $uploadDir = UPLOAD_BASE_PATH . '/' . $operation . '/' . $propertySlug;
    
    if (is_dir($uploadDir)) {
        $files = glob($uploadDir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }
        @rmdir($uploadDir);
        return true;
    }
    
    return false;
}
