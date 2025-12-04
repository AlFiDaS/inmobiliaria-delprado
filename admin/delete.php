<?php
/**
 * Eliminar propiedad
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/upload.php';

requireAuth();

$propertyId = $_GET['id'] ?? '';

if (empty($propertyId)) {
    header('Location: /admin/list.php');
    exit;
}

// Solo permitir POST para eliminar
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Mostrar página de confirmación
    try {
        $db = getDB();
        $stmt = $db->prepare('SELECT id, title, slug, operation FROM properties WHERE id = :id');
        $stmt->execute([':id' => $propertyId]);
        $property = $stmt->fetch();
        
        if (!$property) {
            header('Location: /admin/list.php?error=notfound');
            exit;
        }
    } catch (PDOException $e) {
        error_log('Error al cargar propiedad: ' . $e->getMessage());
        header('Location: /admin/list.php?error=load');
        exit;
    }
    
    $csrfToken = generateCSRFToken();
    require_once __DIR__ . '/_inc/header.php';
    $pageTitle = 'Eliminar Propiedad';
    ?>
    
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Eliminar Propiedad</h1>
    </div>
    
    <div class="bg-white shadow rounded-lg p-6 max-w-2xl">
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <h2 class="text-lg font-semibold text-red-800 mb-2">⚠️ Confirmar eliminación</h2>
            <p class="text-red-700">Estás a punto de eliminar la propiedad:</p>
            <p class="text-lg font-bold text-red-900 mt-2"><?= escape($property['title']) ?></p>
            <p class="text-sm text-red-600 mt-2">Esta acción no se puede deshacer. Se eliminarán:</p>
            <ul class="list-disc list-inside text-sm text-red-600 mt-2">
                <li>La propiedad de la base de datos</li>
                <li>Todas las imágenes asociadas</li>
            </ul>
        </div>
        
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
            <div class="flex justify-end space-x-4">
                <a href="/admin/list.php" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancelar</a>
                <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Sí, eliminar propiedad
                </button>
            </div>
        </form>
    </div>
    
    <?php require_once __DIR__ . '/_inc/footer.php';
    exit;
}

// Verificar CSRF token
if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    header('Location: /admin/list.php?error=csrf');
    exit;
}

try {
    $db = getDB();
    
    // Obtener información de la propiedad antes de eliminar
    $stmt = $db->prepare('SELECT slug, operation FROM properties WHERE id = :id');
    $stmt->execute([':id' => $propertyId]);
    $property = $stmt->fetch();
    
    if (!$property) {
        header('Location: /admin/list.php?error=notfound');
        exit;
    }
    
    // Eliminar imágenes
    deletePropertyImages($property['slug'], $property['operation']);
    
    // Eliminar de la base de datos
    $stmt = $db->prepare('DELETE FROM properties WHERE id = :id');
    $stmt->execute([':id' => $propertyId]);
    
    // Sincronizar propiedades automáticamente
    @file_get_contents(SITE_URL . '/api/sync.php');
    
    header('Location: /admin/list.php?success=deleted');
    exit;
} catch (PDOException $e) {
    error_log('Error al eliminar propiedad: ' . $e->getMessage());
    header('Location: /admin/list.php?error=delete');
    exit;
}
