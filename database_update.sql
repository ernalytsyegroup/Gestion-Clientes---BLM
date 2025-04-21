-- Crear la tabla de empresas
CREATE TABLE IF NOT EXISTS empresas (
    id_empresa INT AUTO_INCREMENT PRIMARY KEY,
    nombre_empresa VARCHAR(100) NOT NULL,
    rubro VARCHAR(100) NOT NULL
);

-- Agregar campo id_empresa a la tabla clientes
ALTER TABLE clientes ADD COLUMN id_empresa INT;

-- Agregar la relación entre clientes y empresas
ALTER TABLE clientes ADD CONSTRAINT fk_cliente_empresa FOREIGN KEY (id_empresa) REFERENCES empresas(id_empresa) ON DELETE SET NULL;

-- Insertar algunas empresas de ejemplo
INSERT INTO empresas (nombre_empresa, rubro) VALUES 
('Empresa Ejemplo 1', 'Tecnología'),
('Empresa Ejemplo 2', 'Salud'),
('Empresa Ejemplo 3', 'Educación');

-- Actualizar algunos clientes existentes con empresas
UPDATE clientes SET id_empresa = 1 WHERE id_cliente = 1;
UPDATE clientes SET id_empresa = 2 WHERE id_cliente = 2;
UPDATE clientes SET id_empresa = 3 WHERE id_cliente = 3;
