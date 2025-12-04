-- Base de datos para Del Prado Inmobiliaria
-- Compatible con Hostinger MySQL
-- Ejecutar este script en phpMyAdmin o desde línea de comandos

-- Crear base de datos (si no existe, crear desde el panel de Hostinger)
-- CREATE DATABASE IF NOT EXISTS delprado_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE delprado_db;

-- Tabla de propiedades (estructura completa según tu modelo TypeScript)
CREATE TABLE IF NOT EXISTS properties (
  id VARCHAR(50) PRIMARY KEY,
  slug VARCHAR(255) UNIQUE NOT NULL,
  title VARCHAR(255) NOT NULL,
  address VARCHAR(255),
  city VARCHAR(100) NOT NULL,
  neighborhood VARCHAR(100),
  operation ENUM('venta', 'alquiler') NOT NULL,
  type ENUM('casa', 'departamento', 'local', 'oficina', 'terreno', 'ph', 'duplex') NOT NULL,
  price DECIMAL(12,2) NOT NULL,
  currency ENUM('ARS', 'USD') DEFAULT 'USD',
  coveredM2 INT,
  totalM2 INT,
  bedrooms INT,
  bathrooms INT,
  parking TINYINT(1) DEFAULT 0, -- 0 = false, 1 = true, o número si es cantidad
  expenses DECIMAL(10,2), -- expensas
  orientation VARCHAR(50),
  `condition` ENUM('a estrenar', 'reciclado', 'bueno', 'a refaccionar'),
  year INT,
  amenities JSON,
  features JSON,
  description TEXT,
  images JSON NOT NULL,
  videos JSON, -- Array de objetos {kind: 'file'|'youtube'|'vimeo', src: string}
  highlight TINYINT(1) DEFAULT 0,
  listedAt DATETIME NOT NULL,
  lat DECIMAL(10,6),
  lng DECIMAL(10,6),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_operation (operation),
  INDEX idx_city (city),
  INDEX idx_type (type),
  INDEX idx_highlight (highlight),
  INDEX idx_listedAt (listedAt),
  INDEX idx_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de usuarios administradores
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role VARCHAR(50) DEFAULT 'admin',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar usuario administrador por defecto
-- Contraseña por defecto: admin123 (CAMBIAR INMEDIATAMENTE después del primer login)
-- Para generar un nuevo hash: password_hash('tu_contraseña', PASSWORD_DEFAULT)
INSERT INTO users (username, password_hash, role) 
VALUES ('admin', '$2y$10$zIg1THl7/dMsw3pcl19ucus61kRK4Vo0pyBY7gJZWY86Gg4SwRjsK', 'admin')
ON DUPLICATE KEY UPDATE username=username;

-- Nota: El hash corresponde a la contraseña "admin123"
-- IMPORTANTE: Cambiar esta contraseña después del primer login
