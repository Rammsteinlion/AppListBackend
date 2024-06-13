CREATE DATABASE IF NOT EXISTS listapi;
USE listapi;

-- Crear la tabla category
CREATE TABLE IF NOT EXISTS category (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Insertar categorías por defecto
INSERT INTO category (nombre) VALUES ('Desarrollo'), ('Marketing'), ('Administración');

-- Crear la tabla roles
CREATE TABLE IF NOT EXISTS roles (
    id_rol INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Insertar roles por defecto
INSERT INTO roles (role_name) VALUES ('Admin'), ('User'), ('Guest');

-- Crear la tabla user con referencia a roles
CREATE TABLE IF NOT EXISTS user (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    username VARCHAR(20) NOT NULL,
    email VARCHAR(200) NOT NULL,
    password VARCHAR(200) NOT NULL,
    rol INT NOT NULL DEFAULT 1,
    idToken VARCHAR(500) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (rol) REFERENCES roles(id_rol)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Crear la tabla task con referencia a user y category
CREATE TABLE IF NOT EXISTS task (
    task_id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    user_assigned INT NOT NULL,
    description VARCHAR(255) NOT NULL,
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    state ENUM('pending', 'in_process', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expiration_date DATE,
    date_completed TIMESTAMP NULL,
    FOREIGN KEY (category_id) REFERENCES category(id),
    FOREIGN KEY (user_assigned) REFERENCES user(id_user)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Insertar tareas de ejemplo
INSERT INTO task (category_id, description, priority, state, expiration_date, user_assigned) 
VALUES 
(1, 'Implementar la funcionalidad de login', 'alta', 'pendiente', '2024-06-15', 1),
(2, 'Crear campaña publicitaria para el nuevo producto', 'media', 'pendiente', '2024-07-01', 2),
(3, 'Actualizar la política de privacidad', 'baja', 'pendiente', '2024-08-10', 3);
