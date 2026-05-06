-- ============================================================
-- SIMULADOR DE LATENCIA OPERATIVA ORGANIZACIONAL
-- Script SQL Completo - Ejecutable en phpMyAdmin
-- ============================================================

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS simulador_latencia CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE simulador_latencia;

-- ============================================================
-- TABLA USUARIOS
-- ============================================================
CREATE TABLE usuarios (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  rol ENUM('DEV','CIO','CTO','CISO') NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_username (username),
  INDEX idx_rol (rol)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA PROYECTOS
-- ============================================================
CREATE TABLE proyectos (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(200) NOT NULL,
  descripcion TEXT,
  modelo_organizacional ENUM('Tradicional','Startup') NOT NULL,
  estado_actual VARCHAR(50) NOT NULL,
  creado_por INT UNSIGNED NOT NULL,
  fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  fecha_aprobacion TIMESTAMP NULL DEFAULT NULL,
  FOREIGN KEY (creado_por) REFERENCES usuarios(id) ON DELETE CASCADE,
  INDEX idx_estado (estado_actual),
  INDEX idx_modelo (modelo_organizacional),
  INDEX idx_creado_por (creado_por)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLA AUDITORIA_FLUJO
-- ============================================================
CREATE TABLE auditoria_flujo (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  proyecto_id INT UNSIGNED NOT NULL,
  usuario_id INT UNSIGNED NOT NULL,
  accion_realizada VARCHAR(255) NOT NULL,
  estado_anterior VARCHAR(50),
  estado_nuevo VARCHAR(50),
  ip_origen VARCHAR(45),
  timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (proyecto_id) REFERENCES proyectos(id) ON DELETE CASCADE,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  INDEX idx_proyecto (proyecto_id),
  INDEX idx_usuario (usuario_id),
  INDEX idx_timestamp (timestamp)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- INSERTAR USUARIOS DE PRUEBA
-- Password: password123 (hasheado con password_hash)
-- ============================================================
INSERT INTO usuarios (nombre, username, password, rol) VALUES
('Ana García', 'dev_ana', '$2y$10$KFvtxTRici1jw.47kGl16eR93EG920tVvrv76l5MfcnazPz8wJE/u', 'DEV'),
('Roberto López', 'cio_roberto', '$2y$10$DiWoIdJgjJnwqQQZK9cdQ.lo7dVRBeHbdm.P6.E9BfTKMqg9jpOAua', 'CIO'),
('Valentina Soto', 'cto_valentina', '$2y$10$P/zz3c9wdFgZfXmUxyPRl.53vqAxk8tOUXL00ugp5cfRRyeAWbNty', 'CTO'),
('Carlos Mendez', 'ciso_carlos', '$2y$10$Q2qNEVVEGvjrixTepXCT1uXrQyvq8iwQlmL8/rtDsSE95WqhN71ve', 'CISO'),
('Lucía Moreno', 'dev_lucia', '$2y$10$LFxqOSr5axuA//ylJ3S18u0nk8E6UXpWxfqA7Z/rv9uMGKn2s.z16', 'DEV');

-- ============================================================
-- INSERTAR PROYECTOS DE DEMO
-- ============================================================
INSERT INTO proyectos (titulo, descripcion, modelo_organizacional, estado_actual, creado_por) VALUES
('Portal E-Commerce Tradicional', 'Desarrollo de portal de ventas online con arquitectura tradicional en silos', 'Tradicional', 'Pendiente_CIO', 1),
('App Móvil Startup', 'Aplicación móvil desarrollada con metodología Squad', 'Startup', 'Revision_Squad', 1),
('Sistema Legado Migración', 'Migración de sistema legado con enfoque tradicional de cambio', 'Tradicional', 'Pendiente_CISO', 1);

-- ============================================================
-- INSERTAR REGISTROS DE AUDITORIA INICIALES
-- ============================================================
INSERT INTO auditoria_flujo (proyecto_id, usuario_id, accion_realizada, estado_anterior, estado_nuevo, ip_origen) VALUES
(1, 1, 'Creación de proyecto', NULL, 'Pendiente_CIO', '192.168.1.100'),
(2, 1, 'Creación de proyecto', NULL, 'Revision_Squad', '192.168.1.100'),
(3, 1, 'Creación de proyecto', NULL, 'Pendiente_CIO', '192.168.1.100'),
(3, 2, 'Aprobación CIO', 'Pendiente_CIO', 'Pendiente_CISO', '192.168.1.105');

-- ============================================================
-- HECHO: Script SQL completo y funcional
-- ============================================================
