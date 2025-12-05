<?php
/**
 * Lista de propiedades - Panel de administración
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/cache-bust.php';
require_once __DIR__ . '/_inc/header.php';

$pageTitle = 'Lista de Propiedades';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 50;
$offset = ($page - 1) * $perPage;

try {
    $db = getDB();
    
    // Contar total de propiedades
    $countStmt = $db->query('SELECT COUNT(*) as total FROM properties');
    $totalProperties = $countStmt->fetch()['total'];
    $totalPages = ceil($totalProperties / $perPage);
    
    // Obtener propiedades con paginación (mostrar todas, visibles e invisibles)
    $stmt = $db->prepare('
        SELECT id, slug, title, price, currency, operation, city, images, highlight, visible
        FROM properties
        ORDER BY visible DESC, highlight DESC, listedAt DESC
        LIMIT :limit OFFSET :offset
    ');
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $properties = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log('Error al obtener propiedades: ' . $e->getMessage());
    $properties = [];
    $totalPages = 0;
}
?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Propiedades</h1>
        <p class="text-gray-600 mt-1">Total: <?= $totalProperties ?> propiedades</p>
    </div>
    <a href="/admin/add.php" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-orange-600 to-orange-700 text-white font-semibold rounded-lg hover:from-orange-700 hover:to-orange-800 transition-all">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Agregar Propiedad
    </a>
</div>

<div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Imagen</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Título</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Operación</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ciudad</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($properties)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            No hay propiedades registradas. <a href="/admin/add.php" class="text-orange-600 hover:text-orange-800">Agregar primera propiedad</a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($properties as $property): 
                        $images = json_decode($property['images'], true) ?? [];
                        $thumbnail = !empty($images) ? $images[0] : '/images/placeholder.jpg';
                        // Asegurar que la ruta comience con / y use SITE_URL si es necesario
                        if (strpos($thumbnail, 'http') !== 0 && strpos($thumbnail, '/') !== 0) {
                            $thumbnail = '/' . $thumbnail;
                        }
                        // Agregar cache busting basado en tiempo de modificación del archivo
                        $thumbnail = addCacheBustToImage($thumbnail, $property['listedAt'] ?? null);
                        $thumbnailUrl = (strpos($thumbnail, 'http') === 0) ? $thumbnail : SITE_URL . $thumbnail;
                        $priceText = $property['currency'] === 'USD' 
                            ? 'USD ' . number_format($property['price'], 0, ',', '.')
                            : '$' . number_format($property['price'], 0, ',', '.');
                    ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <img src="<?= escape($thumbnailUrl) ?>" alt="" class="h-16 w-24 object-cover rounded" onerror="this.src='<?= SITE_URL ?>/images/placeholder.jpg'">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium text-gray-900"><?= escape($property['id']) ?></span>
                                <div class="flex flex-wrap gap-1 mt-1">
                                    <?php if ($property['highlight'] ?? false): ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800">⭐ Destacada</span>
                                    <?php endif; ?>
                                    <?php if (isset($property['visible']) && $property['visible']): ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">✓ Visible</span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">○ Oculta</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900"><?= escape($property['title']) ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-900"><?= escape($priceText) ?></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $property['operation'] === 'venta' ? 'bg-orange-100 text-orange-800' : 'bg-green-100 text-green-800' ?>">
                                    <?= escape(ucfirst($property['operation'])) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= escape($property['city']) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="/admin/edit.php?id=<?= escape($property['id']) ?>" class="text-orange-600 hover:text-orange-900 mr-4">Editar</a>
                                <a href="/admin/delete.php?id=<?= escape($property['id']) ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('¿Estás seguro de eliminar esta propiedad? Esta acción no se puede deshacer.');">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <?php if ($totalPages > 1): ?>
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            <div class="flex-1 flex justify-between sm:hidden">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Anterior
                    </a>
                <?php endif; ?>
                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?= $page + 1 ?>" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Siguiente
                    </a>
                <?php endif; ?>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Mostrando <span class="font-medium"><?= $offset + 1 ?></span> a <span class="font-medium"><?= min($offset + $perPage, $totalProperties) ?></span> de <span class="font-medium"><?= $totalProperties ?></span> resultados
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?= $page - 1 ?>" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                Anterior
                            </a>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                            <a href="?page=<?= $i ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium <?= $i === $page ? 'text-orange-600 bg-orange-50' : 'text-gray-700 hover:bg-gray-50' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?= $page + 1 ?>" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                Siguiente
                            </a>
                        <?php endif; ?>
                    </nav>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/_inc/footer.php'; ?>
