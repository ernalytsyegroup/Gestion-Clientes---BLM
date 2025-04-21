-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS client_management;
USE client_management;

-- Tabla de roles
CREATE TABLE IF NOT EXISTS roles (
    id_rol INT AUTO_INCREMENT PRIMARY KEY,
    nombre_rol VARCHAR(50) NOT NULL,
    descripcion_rol TEXT
);

-- Tabla de tipos de planes
CREATE TABLE IF NOT EXISTS planes (
    id_plan INT AUTO_INCREMENT PRIMARY KEY,
    nombre_plan VARCHAR(100) NOT NULL,
    descripcion_plan TEXT,
    precio DECIMAL(10,2) NOT NULL
);

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre_usuario VARCHAR(100) NOT NULL,
    correo_usuario VARCHAR(100) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    id_rol INT,
    FOREIGN KEY (id_rol) REFERENCES roles(id_rol) ON DELETE SET NULL
);

-- Tabla de clientes
CREATE TABLE IF NOT EXISTS clientes (
    id_cliente INT AUTO_INCREMENT PRIMARY KEY,
    nombre_cliente VARCHAR(100) NOT NULL,
    fecha_inicio DATE,
    cumpleaños DATE,
    fecha_pago DATE,
    id_plan INT,
    FOREIGN KEY (id_plan) REFERENCES planes(id_plan) ON DELETE SET NULL
);

-- Tabla de Instagram
CREATE TABLE IF NOT EXISTS instagram (
    id_instagram INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT,
    usuario_instagram VARCHAR(100),
    correo_instagram VARCHAR(100),
    FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente) ON DELETE CASCADE
);

-- Tabla de Facebook
CREATE TABLE IF NOT EXISTS facebook (
    id_facebook INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT,
    usuario_facebook VARCHAR(100),
    correo_facebook VARCHAR(100),
    FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente) ON DELETE CASCADE
);

-- Tabla de YouTube
CREATE TABLE IF NOT EXISTS youtube (
    id_youtube INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT,
    usuario_youtube VARCHAR(100),
    correo_youtube VARCHAR(100),
    FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente) ON DELETE CASCADE
);

-- Tabla de relaciones (usuarios-clientes)
CREATE TABLE IF NOT EXISTS relaciones (
    id_relacion INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT,
    id_cliente INT,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente) ON DELETE CASCADE
);

-- Insertar roles básicos
INSERT INTO roles (nombre_rol, descripcion_rol) VALUES 
('Administrador', 'Acceso completo al sistema, puede gestionar usuarios, roles y todos los clientes'),
('Colaborador', 'Acceso limitado, solo puede gestionar los clientes asignados');

-- Insertar tipos de planes básicos
INSERT INTO planes (nombre_plan, descripcion_plan, precio) VALUES 
('Básico', 'Gestión de una red social', 99.99),
('Estándar', 'Gestión de hasta tres redes sociales', 199.99),
('Premium', 'Gestión completa de todas las redes sociales', 299.99);

-- Insertar usuario administrador por defecto
-- Contraseña: admin123 (hash bcrypt)
INSERT INTO usuarios (nombre_usuario, correo_usuario, contrasena, id_rol) VALUES 
('Administrador', 'admin@example.com', '$2y$10$8MILqZd4xvBQQM1nWmTMZeXWL.GQ8RbQZV5wz7YIkHMZVpgGpQwXi', 1);

-- Insertar algunos clientes de ejemplo
INSERT INTO clientes (nombre_cliente, fecha_inicio, cumpleaños, fecha_pago, id_plan) VALUES 
('Cliente Ejemplo 1', '2023-01-01', '1990-05-15', '2023-01-15', 1),
('Cliente Ejemplo 2', '2023-02-01', '1985-07-22', '2023-02-15', 2),
('Cliente Ejemplo 3', '2023-03-01', '1992-11-10', '2023-03-15', 3);

-- Asignar clientes al administrador
INSERT INTO relaciones (id_usuario, id_cliente) VALUES 
(1, 1),
(1, 2),
(1, 3);

-- Insertar algunas redes sociales de ejemplo
INSERT INTO instagram (id_cliente, usuario_instagram, correo_instagram) VALUES 
(1, 'cliente1_insta', 'cliente1_insta@example.com'),
(2, 'cliente2_insta', 'cliente2_insta@example.com');

INSERT INTO facebook (id_cliente, usuario_facebook, correo_facebook) VALUES 
(1, 'cliente1_fb', 'cliente1_fb@example.com'),
(3, 'cliente3_fb', 'cliente3_fb@example.com');

INSERT INTO youtube (id_cliente, usuario_youtube, correo_youtube) VALUES 
(2, 'cliente2_yt', 'cliente2_yt@example.com'),
(3, 'cliente3_yt', 'cliente3_yt@example.com');
