<?php
/**
 * Script de diagnóstico para verificar configuración de imágenes
 * Acceder desde: https://tu-dominio.com/verificar-imagenes.php
 */

require_once __DIR__ . '/config.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnóstico de Imágenes</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; padding: 20px; }
        .ok { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto; }
        .section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>🔍 Diagnóstico de Configuración de Imágenes</h1>
    
    <div class="section">
        <h2>1. Configuración de Rutas</h2>
        <p><strong>UPLOAD_BASE_PATH:</strong> <code><?= UPLOAD_BASE_PATH ?></code></p>
        <p><strong>DOCUMENT_ROOT:</strong> <code><?= $_SERVER['DOCUMENT_ROOT'] ?? 'No definido' ?></code></p>
        <p><strong>SITE_URL:</strong> <code><?= SITE_URL ?></code></p>
        <p><strong>__DIR__:</strong> <code><?= __DIR__ ?></code></p>
    </div>
    
    <div class="section">
        <h2>2. Verificación de Carpetas</h2>
        <?php
        $uploadPath = UPLOAD_BASE_PATH;
        $exists = is_dir($uploadPath);
        $writable = $exists && is_writable($uploadPath);
        ?>
        <p><strong>Carpeta de imágenes:</strong> 
            <span class="<?= $exists ? 'ok' : 'error' ?>">
                <?= $exists ? '✅ Existe' : '❌ No existe' ?>
            </span>
        </p>
        <p><strong>Permisos de escritura:</strong> 
            <span class="<?= $writable ? 'ok' : 'error' ?>">
                <?= $writable ? '✅ Escribible' : '❌ No escribible' ?>
            </span>
        </p>
        <?php if ($exists): ?>
            <p><strong>Permisos:</strong> <code><?= substr(sprintf('%o', fileperms($uploadPath)), -4) ?></code></p>
        <?php endif; ?>
    </div>
    
    <div class="section">
        <h2>3. Propiedades en la Base de Datos</h2>
        <?php
        try {
            require_once __DIR__ . '/db.php';
            $db = getDB();
            $stmt = $db->query('SELECT id, slug, title, images, operation FROM properties ORDER BY listedAt DESC LIMIT 5');
            $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($properties)) {
                echo '<p class="warning">⚠️ No hay propiedades en la base de datos</p>';
            } else {
                echo '<table border="1" cellpadding="10" style="width: 100%; border-collapse: collapse;">';
                echo '<tr><th>ID</th><th>Slug</th><th>Título</th><th>Rutas de Imágenes</th><th>¿Existen?</th></tr>';
                
                foreach ($properties as $prop) {
                    $images = json_decode($prop['images'], true) ?? [];
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($prop['id']) . '</td>';
                    echo '<td>' . htmlspecialchars($prop['slug']) . '</td>';
                    echo '<td>' . htmlspecialchars($prop['title']) . '</td>';
                    echo '<td><pre style="font-size: 11px;">';
                    if (empty($images)) {
                        echo 'Sin imágenes';
                    } else {
                        foreach ($images as $img) {
                            echo htmlspecialchars($img) . "\n";
                        }
                    }
                    echo '</pre></td>';
                    echo '<td>';
                    if (empty($images)) {
                        echo '<span class="warning">Sin imágenes</span>';
                    } else {
                        $allExist = true;
                        foreach ($images as $img) {
                            // Convertir ruta relativa a absoluta
                            if (strpos($img, '/') === 0) {
                                $fullPath = $_SERVER['DOCUMENT_ROOT'] . $img;
                            } else {
                                $fullPath = $uploadPath . '/' . $img;
                            }
                            $exists = file_exists($fullPath);
                            if (!$exists) {
                                $allExist = false;
                                echo '<span class="error">❌</span> ' . basename($img) . '<br>';
                            }
                        }
                        if ($allExist) {
                            echo '<span class="ok">✅ Todas existen</span>';
                        }
                    }
                    echo '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            }
        } catch (Exception $e) {
            echo '<p class="error">❌ Error al conectar a la base de datos: ' . htmlspecialchars($e->getMessage()) . '</p>';
        }
        ?>
    </div>
    
    <div class="section">
        <h2>4. Recomendaciones</h2>
        <ul>
            <?php if (!$exists): ?>
                <li class="error">⚠️ <strong>Crear la carpeta:</strong> <code><?= $uploadPath ?></code></li>
                <li class="error">⚠️ <strong>Dar permisos:</strong> <code>chmod 755</code> o <code>chmod 777</code></li>
            <?php elseif (!$writable): ?>
                <li class="error">⚠️ <strong>Cambiar permisos:</strong> <code>chmod 755</code> o <code>chmod 777</code> a la carpeta</li>
            <?php else: ?>
                <li class="ok">✅ La carpeta existe y es escribible</li>
            <?php endif; ?>
            
            <li>Verificar que las imágenes subidas estén físicamente en el servidor</li>
            <li>Si las imágenes no existen, volver a subir la propiedad desde el panel admin</li>
            <li>Verificar que las rutas en la base de datos sean relativas: <code>/images/properties/...</code></li>
        </ul>
    </div>
    
    <div class="section">
        <h2>5. Prueba de Escritura</h2>
        <?php
        if ($writable) {
            $testFile = $uploadPath . '/test_write.txt';
            $testWrite = @file_put_contents($testFile, 'test');
            if ($testWrite !== false) {
                @unlink($testFile);
                echo '<p class="ok">✅ Puede escribir archivos en la carpeta</p>';
            } else {
                echo '<p class="error">❌ No puede escribir archivos en la carpeta</p>';
            }
        } else {
            echo '<p class="error">❌ La carpeta no es escribible, no se puede probar</p>';
        }
        ?>
    </div>
    
    <p style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
        <small>Este archivo puede ser eliminado después de resolver los problemas.</small>
    </p>
</body>
</html>

