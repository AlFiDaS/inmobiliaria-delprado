<?php
/**
 * Agregar nueva propiedad
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/slugify.php';
require_once __DIR__ . '/../helpers/upload.php';

requireAuth();

$pageTitle = 'Agregar Propiedad';
$errors = [];
$formData = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Token de seguridad inválido. Por favor, recarga la página.';
    } else {
        // Recoger datos del formulario
        $formData = [
            'id' => trim($_POST['id'] ?? ''),
            'slug' => trim($_POST['slug'] ?? ''),
            'title' => trim($_POST['title'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'city' => trim($_POST['city'] ?? ''),
            'neighborhood' => trim($_POST['neighborhood'] ?? ''),
            'operation' => $_POST['operation'] ?? 'venta',
            'type' => $_POST['type'] ?? '',
            'price' => floatval($_POST['price'] ?? 0),
            'currency' => $_POST['currency'] ?? 'USD',
            'coveredM2' => !empty($_POST['coveredM2']) ? intval($_POST['coveredM2']) : null,
            'totalM2' => !empty($_POST['totalM2']) ? intval($_POST['totalM2']) : null,
            'bedrooms' => !empty($_POST['bedrooms']) ? intval($_POST['bedrooms']) : null,
            'bathrooms' => !empty($_POST['bathrooms']) ? intval($_POST['bathrooms']) : null,
            'parking' => !empty($_POST['parking']) ? (is_numeric($_POST['parking']) ? intval($_POST['parking']) : 1) : 0,
            'expenses' => !empty($_POST['expenses']) ? floatval($_POST['expenses']) : null,
            'orientation' => trim($_POST['orientation'] ?? ''),
            'condition' => trim($_POST['condition'] ?? ''),
            'year' => !empty($_POST['year']) ? intval($_POST['year']) : null,
            'amenities' => !empty($_POST['amenities']) ? array_filter(array_map('trim', explode(',', $_POST['amenities']))) : [],
            'features' => !empty($_POST['features']) ? array_filter(array_map('trim', explode(',', $_POST['features']))) : [],
            'description' => trim($_POST['description'] ?? ''),
            'highlight' => isset($_POST['highlight']),
            'listedAt' => !empty($_POST['listedAt']) ? $_POST['listedAt'] : date('Y-m-d H:i:s'),
            'lat' => !empty($_POST['lat']) ? floatval($_POST['lat']) : null,
            'lng' => !empty($_POST['lng']) ? floatval($_POST['lng']) : null,
        ];
        
        // Validaciones
        if (empty($formData['title'])) {
            $errors[] = 'El título es obligatorio';
        }
        
        if (empty($formData['city'])) {
            $errors[] = 'La ciudad es obligatoria';
        }
        
        if (empty($formData['operation']) || !in_array($formData['operation'], ['venta', 'alquiler'])) {
            $errors[] = 'La operación es obligatoria';
        }
        
        if (empty($formData['type'])) {
            $errors[] = 'El tipo de propiedad es obligatorio';
        }
        
        if ($formData['price'] <= 0) {
            $errors[] = 'El precio debe ser mayor a 0';
        }
        
        // Generar slug si está vacío
        if (empty($formData['slug']) && !empty($formData['title'])) {
            $formData['slug'] = generateUniqueSlug($formData['title']);
        } elseif (!empty($formData['slug'])) {
            $formData['slug'] = generateUniqueSlug($formData['slug']);
        }
        
        // Generar ID si está vacío
        if (empty($formData['id'])) {
            try {
                $db = getDB();
                $stmt = $db->query('SELECT COUNT(*) as count FROM properties');
                $count = $stmt->fetch()['count'];
                $formData['id'] = 'PROP' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
            } catch (PDOException $e) {
                $formData['id'] = 'PROP' . str_pad(time() % 10000, 3, '0', STR_PAD_LEFT);
            }
        }
        
        // Verificar que el ID sea único
        try {
            $db = getDB();
            $stmt = $db->prepare('SELECT id FROM properties WHERE id = :id');
            $stmt->execute([':id' => $formData['id']]);
            if ($stmt->rowCount() > 0) {
                $errors[] = 'El ID ya existe. Por favor, elige otro.';
            }
        } catch (PDOException $e) {
            $errors[] = 'Error al verificar el ID: ' . $e->getMessage();
        }
        
        // Procesar imágenes subidas
        $uploadedImages = [];
        if (!empty($_FILES['images']['name'][0])) {
            $imageCount = count($_FILES['images']['name']);
            if ($imageCount > UPLOAD_MAX_IMAGES) {
                $errors[] = 'Máximo ' . UPLOAD_MAX_IMAGES . ' imágenes permitidas';
            } else {
                for ($i = 0; $i < $imageCount; $i++) {
                    if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
                        $file = [
                            'name' => $_FILES['images']['name'][$i],
                            'type' => $_FILES['images']['type'][$i],
                            'tmp_name' => $_FILES['images']['tmp_name'][$i],
                            'error' => $_FILES['images']['error'][$i],
                            'size' => $_FILES['images']['size'][$i]
                        ];
                        
                        $result = processUploadedImage($file, $formData['slug'], $formData['operation'], $i);
                        if ($result['success']) {
                            $uploadedImages[] = $result['path'];
                        } else {
                            $errors = array_merge($errors, $result['errors']);
                        }
                    }
                }
            }
        }
        
        // Agregar imágenes desde URLs si se proporcionaron
        if (!empty($_POST['image_urls'])) {
            $urls = array_filter(array_map('trim', explode("\n", $_POST['image_urls'])));
            foreach ($urls as $url) {
                if (filter_var($url, FILTER_VALIDATE_URL) || (strpos($url, '/') === 0)) {
                    $uploadedImages[] = $url;
                }
            }
        }
        
        if (empty($uploadedImages)) {
            $errors[] = 'Debes subir al menos una imagen';
        }
        
        // Si no hay errores, guardar en la base de datos
        if (empty($errors)) {
            try {
                $db = getDB();
                $stmt = $db->prepare('
                    INSERT INTO properties (
                        id, slug, title, address, city, neighborhood, operation, type,
                        price, currency, coveredM2, totalM2, bedrooms, bathrooms, parking,
                        expenses, orientation, `condition`, year, amenities, features, description,
                        images, videos, highlight, listedAt, lat, lng
                    ) VALUES (
                        :id, :slug, :title, :address, :city, :neighborhood, :operation, :type,
                        :price, :currency, :coveredM2, :totalM2, :bedrooms, :bathrooms, :parking,
                        :expenses, :orientation, :condition, :year, :amenities, :features, :description,
                        :images, :videos, :highlight, :listedAt, :lat, :lng
                    )
                ');
                
                $stmt->execute([
                    ':id' => $formData['id'],
                    ':slug' => $formData['slug'],
                    ':title' => $formData['title'],
                    ':address' => $formData['address'] ?: null,
                    ':city' => $formData['city'],
                    ':neighborhood' => $formData['neighborhood'] ?: null,
                    ':operation' => $formData['operation'],
                    ':type' => $formData['type'],
                    ':price' => $formData['price'],
                    ':currency' => $formData['currency'],
                    ':coveredM2' => $formData['coveredM2'],
                    ':totalM2' => $formData['totalM2'],
                    ':bedrooms' => $formData['bedrooms'],
                    ':bathrooms' => $formData['bathrooms'],
                    ':parking' => $formData['parking'],
                    ':expenses' => $formData['expenses'],
                    ':orientation' => $formData['orientation'] ?: null,
                    ':condition' => $formData['condition'] ?: null,
                    ':year' => $formData['year'],
                    ':amenities' => json_encode($formData['amenities']),
                    ':features' => json_encode($formData['features']),
                    ':description' => $formData['description'] ?: null,
                    ':images' => json_encode($uploadedImages),
                    ':videos' => null, // Por ahora no se manejan videos
                    ':highlight' => $formData['highlight'] ? 1 : 0,
                    ':listedAt' => $formData['listedAt'],
                    ':lat' => $formData['lat'],
                    ':lng' => $formData['lng'],
                ]);
                
                // Sincronizar propiedades automáticamente
                @file_get_contents(SITE_URL . '/api/sync.php');
                
                header('Location: /admin/list.php?success=added');
                exit;
            } catch (PDOException $e) {
                error_log('Error al guardar propiedad: ' . $e->getMessage());
                $errors[] = 'Error al guardar la propiedad. Por favor, intenta nuevamente.';
            }
        }
    }
}

$csrfToken = generateCSRFToken();
require_once __DIR__ . '/_inc/header.php';
?>

<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Agregar Propiedad</h1>
</div>

<?php if (!empty($errors)): ?>
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
        <ul class="list-disc list-inside">
            <?php foreach ($errors as $error): ?>
                <li><?= escape($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="bg-white shadow rounded-lg p-6 space-y-6">
    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- ID -->
        <div>
            <label for="id" class="block text-sm font-medium text-gray-700 mb-2">ID</label>
            <input type="text" id="id" name="id" value="<?= escape($formData['id'] ?? '') ?>" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
            <p class="mt-1 text-sm text-gray-500">Dejar vacío para generar automáticamente (PROP001, PROP002, etc.)</p>
        </div>
        
        <!-- Slug -->
        <div>
            <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">Slug (URL)</label>
            <input type="text" id="slug" name="slug" value="<?= escape($formData['slug'] ?? '') ?>" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
            <p class="mt-1 text-sm text-gray-500">Dejar vacío para generar desde el título</p>
        </div>
        
        <!-- Título -->
        <div class="md:col-span-2">
            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Título *</label>
            <input type="text" id="title" name="title" required value="<?= escape($formData['title'] ?? '') ?>" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
        </div>
        
        <!-- Dirección -->
        <div>
            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Dirección</label>
            <input type="text" id="address" name="address" value="<?= escape($formData['address'] ?? '') ?>" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
        </div>
        
        <!-- Ciudad -->
        <div>
            <label for="city" class="block text-sm font-medium text-gray-700 mb-2">Ciudad *</label>
            <input type="text" id="city" name="city" required value="<?= escape($formData['city'] ?? '') ?>" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
        </div>
        
        <!-- Barrio -->
        <div>
            <label for="neighborhood" class="block text-sm font-medium text-gray-700 mb-2">Barrio</label>
            <input type="text" id="neighborhood" name="neighborhood" value="<?= escape($formData['neighborhood'] ?? '') ?>" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
        </div>
        
        <!-- Operación -->
        <div>
            <label for="operation" class="block text-sm font-medium text-gray-700 mb-2">Operación *</label>
            <select id="operation" name="operation" required 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                <option value="venta" <?= ($formData['operation'] ?? 'venta') === 'venta' ? 'selected' : '' ?>>Venta</option>
                <option value="alquiler" <?= ($formData['operation'] ?? '') === 'alquiler' ? 'selected' : '' ?>>Alquiler</option>
            </select>
        </div>
        
        <!-- Tipo -->
        <div>
            <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Tipo *</label>
            <select id="type" name="type" required 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                <option value="">Seleccionar...</option>
                <option value="casa" <?= ($formData['type'] ?? '') === 'casa' ? 'selected' : '' ?>>Casa</option>
                <option value="departamento" <?= ($formData['type'] ?? '') === 'departamento' ? 'selected' : '' ?>>Departamento</option>
                <option value="local" <?= ($formData['type'] ?? '') === 'local' ? 'selected' : '' ?>>Local</option>
                <option value="oficina" <?= ($formData['type'] ?? '') === 'oficina' ? 'selected' : '' ?>>Oficina</option>
                <option value="terreno" <?= ($formData['type'] ?? '') === 'terreno' ? 'selected' : '' ?>>Terreno</option>
                <option value="ph" <?= ($formData['type'] ?? '') === 'ph' ? 'selected' : '' ?>>PH</option>
                <option value="duplex" <?= ($formData['type'] ?? '') === 'duplex' ? 'selected' : '' ?>>Duplex</option>
            </select>
        </div>
        
        <!-- Precio -->
        <div>
            <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Precio *</label>
            <input type="number" id="price" name="price" step="0.01" required value="<?= escape($formData['price'] ?? '') ?>" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
        </div>
        
        <!-- Moneda -->
        <div>
            <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">Moneda *</label>
            <select id="currency" name="currency" required 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                <option value="USD" <?= ($formData['currency'] ?? 'USD') === 'USD' ? 'selected' : '' ?>>USD</option>
                <option value="ARS" <?= ($formData['currency'] ?? '') === 'ARS' ? 'selected' : '' ?>>ARS</option>
            </select>
        </div>
        
        <!-- M2 Cubiertos -->
        <div>
            <label for="coveredM2" class="block text-sm font-medium text-gray-700 mb-2">M² Cubiertos</label>
            <input type="number" id="coveredM2" name="coveredM2" value="<?= escape($formData['coveredM2'] ?? '') ?>" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
        </div>
        
        <!-- M2 Totales -->
        <div>
            <label for="totalM2" class="block text-sm font-medium text-gray-700 mb-2">M² Totales</label>
            <input type="number" id="totalM2" name="totalM2" value="<?= escape($formData['totalM2'] ?? '') ?>" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
        </div>
        
        <!-- Dormitorios -->
        <div>
            <label for="bedrooms" class="block text-sm font-medium text-gray-700 mb-2">Dormitorios</label>
            <input type="number" id="bedrooms" name="bedrooms" min="0" value="<?= escape($formData['bedrooms'] ?? '') ?>" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
        </div>
        
        <!-- Baños -->
        <div>
            <label for="bathrooms" class="block text-sm font-medium text-gray-700 mb-2">Baños</label>
            <input type="number" id="bathrooms" name="bathrooms" min="0" value="<?= escape($formData['bathrooms'] ?? '') ?>" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
        </div>
        
        <!-- Parking -->
        <div>
            <label for="parking" class="block text-sm font-medium text-gray-700 mb-2">Cocheras</label>
            <input type="number" id="parking" name="parking" min="0" value="<?= escape($formData['parking'] ?? '0') ?>" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
            <p class="mt-1 text-sm text-gray-500">0 = sin cochera, número = cantidad de cocheras</p>
        </div>
        
        <!-- Expensas -->
        <div>
            <label for="expenses" class="block text-sm font-medium text-gray-700 mb-2">Expensas</label>
            <input type="number" id="expenses" name="expenses" step="0.01" value="<?= escape($formData['expenses'] ?? '') ?>" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
        </div>
        
        <!-- Orientación -->
        <div>
            <label for="orientation" class="block text-sm font-medium text-gray-700 mb-2">Orientación</label>
            <input type="text" id="orientation" name="orientation" value="<?= escape($formData['orientation'] ?? '') ?>" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
        </div>
        
        <!-- Condición -->
        <div>
            <label for="condition" class="block text-sm font-medium text-gray-700 mb-2">Condición</label>
            <select id="condition" name="condition" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                <option value="">Seleccionar...</option>
                <option value="a estrenar" <?= ($formData['condition'] ?? '') === 'a estrenar' ? 'selected' : '' ?>>A estrenar</option>
                <option value="reciclado" <?= ($formData['condition'] ?? '') === 'reciclado' ? 'selected' : '' ?>>Reciclado</option>
                <option value="bueno" <?= ($formData['condition'] ?? '') === 'bueno' ? 'selected' : '' ?>>Bueno</option>
                <option value="a refaccionar" <?= ($formData['condition'] ?? '') === 'a refaccionar' ? 'selected' : '' ?>>A refaccionar</option>
            </select>
        </div>
        
        <!-- Año -->
        <div>
            <label for="year" class="block text-sm font-medium text-gray-700 mb-2">Año</label>
            <input type="number" id="year" name="year" min="1900" max="<?= date('Y') ?>" value="<?= escape($formData['year'] ?? '') ?>" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
        </div>
        
        <!-- Amenities -->
        <div>
            <label for="amenities" class="block text-sm font-medium text-gray-700 mb-2">Amenities</label>
            <input type="text" id="amenities" name="amenities" value="<?= escape(implode(', ', $formData['amenities'] ?? [])) ?>" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                placeholder="Separar con comas: Balcón, Ascensor, Pileta">
        </div>
        
        <!-- Features -->
        <div>
            <label for="features" class="block text-sm font-medium text-gray-700 mb-2">Características</label>
            <input type="text" id="features" name="features" value="<?= escape(implode(', ', $formData['features'] ?? [])) ?>" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                placeholder="Separar con comas: Luminoso, Vista panorámica">
        </div>
        
        <!-- Descripción -->
        <div class="md:col-span-2">
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
            <textarea id="description" name="description" rows="4" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"><?= escape($formData['description'] ?? '') ?></textarea>
        </div>
        
        <!-- Coordenadas -->
        <div>
            <label for="lat" class="block text-sm font-medium text-gray-700 mb-2">Latitud</label>
            <input type="number" id="lat" name="lat" step="0.000001" value="<?= escape($formData['lat'] ?? '') ?>" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
        </div>
        
        <div>
            <label for="lng" class="block text-sm font-medium text-gray-700 mb-2">Longitud</label>
            <input type="number" id="lng" name="lng" step="0.000001" value="<?= escape($formData['lng'] ?? '') ?>" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
        </div>
        
        <!-- Fecha de publicación -->
        <div>
            <label for="listedAt" class="block text-sm font-medium text-gray-700 mb-2">Fecha de Publicación</label>
            <input type="datetime-local" id="listedAt" name="listedAt" value="<?= escape($formData['listedAt'] ?? date('Y-m-d\TH:i')) ?>" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
        </div>
        
        <!-- Destacada -->
        <div class="md:col-span-2">
            <label class="flex items-center">
                <input type="checkbox" name="highlight" value="1" <?= ($formData['highlight'] ?? false) ? 'checked' : '' ?> 
                    class="rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                <span class="ml-2 text-sm text-gray-700">Propiedad destacada</span>
            </label>
        </div>
    </div>
    
    <!-- Imágenes -->
    <div class="border-t pt-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Imágenes</h3>
        
        <div class="mb-4">
            <label for="images" class="block text-sm font-medium text-gray-700 mb-2">Subir imágenes (máximo <?= UPLOAD_MAX_IMAGES ?>)</label>
            <input type="file" id="images" name="images[]" multiple accept="image/jpeg,image/png,image/webp" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
            <p class="mt-1 text-sm text-gray-500">Formatos permitidos: JPG, PNG, WEBP. Máximo 3 MB por imagen.</p>
        </div>
        
        <div>
            <label for="image_urls" class="block text-sm font-medium text-gray-700 mb-2">O agregar URLs de imágenes existentes (una por línea)</label>
            <textarea id="image_urls" name="image_urls" rows="3" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                placeholder="/images/properties/venta/casa-1.jpg"></textarea>
        </div>
    </div>
    
    <div class="flex justify-end space-x-4 pt-6 border-t">
        <a href="/admin/list.php" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancelar</a>
        <button type="submit" class="px-6 py-2 bg-gradient-to-r from-orange-600 to-orange-700 text-white rounded-lg hover:from-orange-700 hover:to-orange-800">
            Guardar Propiedad
        </button>
    </div>
</form>

<?php require_once __DIR__ . '/_inc/footer.php'; ?>
