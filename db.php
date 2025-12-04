<?php
/**
 * Conexión a la base de datos usando PDO
 * Compatible con Hostinger MySQL
 */

require_once __DIR__ . '/config.php';

class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        try {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                DB_HOST,
                DB_NAME,
                DB_CHARSET
            );
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ];

            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log('Error de conexión a la base de datos: ' . $e->getMessage());
            
            // Mensaje amigable en desarrollo
            if (ini_get('display_errors')) {
                $errorMsg = '<div style="padding: 20px; background: #fee; border: 2px solid #f00; margin: 20px; border-radius: 8px;">';
                $errorMsg .= '<h2 style="color: #c00; margin-top: 0;">Error de conexión a la base de datos</h2>';
                
                if (DB_USER === 'tu_usuario_mysql' || DB_PASS === 'tu_contraseña_mysql') {
                    $errorMsg .= '<p><strong>⚠️ Configuración pendiente:</strong></p>';
                    $errorMsg .= '<p>Por favor, actualiza las credenciales de la base de datos en <code>config.php</code></p>';
                    $errorMsg .= '<ul>';
                    $errorMsg .= '<li>DB_HOST: ' . htmlspecialchars(DB_HOST) . '</li>';
                    $errorMsg .= '<li>DB_NAME: ' . htmlspecialchars(DB_NAME) . '</li>';
                    $errorMsg .= '<li>DB_USER: ' . htmlspecialchars(DB_USER) . '</li>';
                    $errorMsg .= '<li>DB_PASS: ' . (DB_PASS === 'tu_contraseña_mysql' ? '<em>No configurado</em>' : '***') . '</li>';
                    $errorMsg .= '</ul>';
                } else {
                    $errorMsg .= '<p><strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
                    $errorMsg .= '<p>Verifica que:</p>';
                    $errorMsg .= '<ul>';
                    $errorMsg .= '<li>La base de datos existe</li>';
                    $errorMsg .= '<li>Las credenciales son correctas</li>';
                    $errorMsg .= '<li>El usuario tiene permisos sobre la base de datos</li>';
                    $errorMsg .= '</ul>';
                }
                $errorMsg .= '</div>';
                die($errorMsg);
            } else {
                die('Error de conexión a la base de datos. Por favor, contacta al administrador.');
            }
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }

    // Prevenir clonación
    private function __clone() {}
    
    // Prevenir deserialización
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}

// Función helper para obtener la conexión
function getDB() {
    return Database::getInstance()->getConnection();
}
