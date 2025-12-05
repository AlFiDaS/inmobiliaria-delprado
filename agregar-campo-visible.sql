-- Agregar campo 'visible' a la tabla properties
-- Ejecutar este script en phpMyAdmin para agregar la funcionalidad de visibilidad

-- Agregar columna 'visible' si no existe
ALTER TABLE properties 
ADD COLUMN IF NOT EXISTS visible TINYINT(1) DEFAULT 0 AFTER highlight;

-- Crear índice para mejorar el rendimiento de las consultas
ALTER TABLE properties 
ADD INDEX IF NOT EXISTS idx_visible (visible);

-- Actualizar todas las propiedades existentes para que sean visibles por defecto
-- (Comenta esta línea si prefieres que las existentes queden invisibles)
UPDATE properties SET visible = 1 WHERE visible IS NULL OR visible = 0;

-- Nota: visible = 0 significa INVISIBLE (no aparece en la web)
--       visible = 1 significa VISIBLE (aparece en la web)

