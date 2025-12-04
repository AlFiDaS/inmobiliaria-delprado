<?php
/**
 * Dashboard del panel de administración
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/_inc/header.php';

$pageTitle = 'Panel de Administración';

// Obtener estadísticas
try {
    $db = getDB();
    
    // Total de propiedades
    $stmt = $db->query('SELECT COUNT(*) as total FROM properties');
    $totalProperties = $stmt->fetch()['total'];
    
    // Propiedades en venta
    $stmt = $db->query('SELECT COUNT(*) as total FROM properties WHERE operation = "venta"');
    $ventaProperties = $stmt->fetch()['total'];
    
    // Propiedades en alquiler
    $stmt = $db->query('SELECT COUNT(*) as total FROM properties WHERE operation = "alquiler"');
    $alquilerProperties = $stmt->fetch()['total'];
    
    // Propiedades destacadas
    $stmt = $db->query('SELECT COUNT(*) as total FROM properties WHERE highlight = 1');
    $highlightProperties = $stmt->fetch()['total'];
    
    // Últimas propiedades agregadas
    $stmt = $db->query('
        SELECT id, title, operation, city, images, created_at 
        FROM properties 
        ORDER BY created_at DESC 
        LIMIT 5
    ');
    $recentProperties = $stmt->fetchAll();
    
    // Decodificar imágenes
    foreach ($recentProperties as &$prop) {
        $prop['images'] = json_decode($prop['images'], true) ?? [];
    }
} catch (PDOException $e) {
    error_log('Error al obtener estadísticas: ' . $e->getMessage());
    $totalProperties = 0;
    $ventaProperties = 0;
    $alquilerProperties = 0;
    $highlightProperties = 0;
    $recentProperties = [];
}
?>

<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Panel de Administración</h1>
    <p class="text-gray-600 mt-1">Bienvenido, <?= escape(getCurrentUsername()) ?></p>
</div>

<!-- Estadísticas -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Propiedades</p>
                <p class="text-2xl font-semibold text-gray-900"><?= $totalProperties ?></p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">En Venta</p>
                <p class="text-2xl font-semibold text-gray-900"><?= $ventaProperties ?></p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">En Alquiler</p>
                <p class="text-2xl font-semibold text-gray-900"><?= $alquilerProperties ?></p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Destacadas</p>
                <p class="text-2xl font-semibold text-gray-900"><?= $highlightProperties ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Acciones rápidas -->
<div class="bg-white rounded-lg shadow p-6 mb-8">
    <h2 class="text-xl font-bold text-gray-900 mb-4">Acciones Rápidas</h2>
    <div class="flex flex-wrap gap-4">
        <a href="/admin/add.php" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-orange-600 to-orange-700 text-white font-semibold rounded-lg hover:from-orange-700 hover:to-orange-800 transition-all">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Agregar Propiedad
        </a>
        <a href="/admin/list.php" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white font-semibold rounded-lg hover:bg-gray-700 transition-all">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
            </svg>
            Ver Todas las Propiedades
        </a>
    </div>
</div>

<!-- Últimas propiedades -->
<?php if (!empty($recentProperties)): ?>
<div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b border-gray-200">
        <h2 class="text-xl font-bold text-gray-900">Últimas Propiedades Agregadas</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Imagen</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Título</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Operación</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ciudad</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($recentProperties as $property): 
                    $thumbnail = !empty($property['images']) ? $property['images'][0] : '/images/placeholder.jpg';
                ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <img src="<?= escape($thumbnail) ?>" alt="" class="h-12 w-16 object-cover rounded">
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900"><?= escape($property['title']) ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $property['operation'] === 'venta' ? 'bg-orange-100 text-orange-800' : 'bg-green-100 text-green-800' ?>">
                                <?= escape(ucfirst($property['operation'])) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?= escape($property['city']) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?= date('d/m/Y', strtotime($property['created_at'])) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="/admin/edit.php?id=<?= escape($property['id']) ?>" class="text-orange-600 hover:text-orange-900">Editar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php else: ?>
<div class="bg-white rounded-lg shadow p-12 text-center">
    <p class="text-gray-500 mb-4">No hay propiedades registradas aún.</p>
    <a href="/admin/add.php" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-orange-600 to-orange-700 text-white font-semibold rounded-lg hover:from-orange-700 hover:to-orange-800 transition-all">
        Agregar Primera Propiedad
    </a>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/_inc/footer.php'; ?>
