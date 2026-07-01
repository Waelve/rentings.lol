-- ============================================================
--  Rentings.lol — Base de Datos MySQL
--  Importar en phpMyAdmin de Synology NAS DS120j
-- ============================================================

CREATE DATABASE IF NOT EXISTS `rentings_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `rentings_db`;

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id`             INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre`         VARCHAR(100)     NOT NULL,
  `email`          VARCHAR(150)     NOT NULL UNIQUE,
  `password`       VARCHAR(255)     NOT NULL,
  `telefono`       VARCHAR(20)      DEFAULT NULL,
  `avatar`         VARCHAR(255)     DEFAULT NULL,
  `rol`            ENUM('usuario','anunciante','admin') NOT NULL DEFAULT 'usuario',
  `verificado`     TINYINT(1)       NOT NULL DEFAULT 0,
  `token_verif`    VARCHAR(100)     DEFAULT NULL,
  `activo`         TINYINT(1)       NOT NULL DEFAULT 1,
  `created_at`     DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de propiedades
CREATE TABLE IF NOT EXISTS `propiedades` (
  `id`             INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `usuario_id`     INT(11) UNSIGNED NOT NULL,
  `titulo`         VARCHAR(200)     NOT NULL,
  `descripcion`    TEXT             NOT NULL,
  `tipo`           ENUM('casa','departamento','terreno','local','bodega','quinta','rancho','oficina') NOT NULL,
  `operacion`      ENUM('venta','renta','venta_renta') NOT NULL DEFAULT 'venta',
  `precio`         DECIMAL(12,2)    NOT NULL,
  `moneda`         ENUM('MXN','USD') NOT NULL DEFAULT 'MXN',
  `recamaras`      TINYINT(3)       DEFAULT 0,
  `banos`          TINYINT(3)       DEFAULT 0,
  `estacionamiento` TINYINT(3)      DEFAULT 0,
  `metros_c`       DECIMAL(10,2)    DEFAULT NULL COMMENT 'Metros construcción',
  `metros_t`       DECIMAL(10,2)    DEFAULT NULL COMMENT 'Metros terreno',
  `direccion`      VARCHAR(300)     NOT NULL,
  `colonia`        VARCHAR(100)     DEFAULT NULL,
  `ciudad`         VARCHAR(100)     NOT NULL DEFAULT 'Montemorelos',
  `estado`         VARCHAR(100)     NOT NULL DEFAULT 'Nuevo León',
  `cp`             VARCHAR(10)      DEFAULT NULL,
  `lat`            DECIMAL(10,8)    DEFAULT NULL,
  `lng`            DECIMAL(11,8)    DEFAULT NULL,
  `imagen_portada` VARCHAR(255)     DEFAULT NULL,
  `destacado`      TINYINT(1)       NOT NULL DEFAULT 0,
  `activo`         TINYINT(1)       NOT NULL DEFAULT 1,
  `vistas`         INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `created_at`     DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE,
  INDEX `idx_tipo` (`tipo`),
  INDEX `idx_operacion` (`operacion`),
  INDEX `idx_ciudad` (`ciudad`),
  INDEX `idx_activo` (`activo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de imágenes de propiedades
CREATE TABLE IF NOT EXISTS `imagenes` (
  `id`             INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `propiedad_id`   INT(11) UNSIGNED NOT NULL,
  `url`            VARCHAR(255)     NOT NULL,
  `orden`          TINYINT(3)       NOT NULL DEFAULT 0,
  `created_at`     DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`propiedad_id`) REFERENCES `propiedades`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de contactos/mensajes
CREATE TABLE IF NOT EXISTS `contactos` (
  `id`             INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `propiedad_id`   INT(11) UNSIGNED NOT NULL,
  `nombre`         VARCHAR(100)     NOT NULL,
  `email`          VARCHAR(150)     NOT NULL,
  `telefono`       VARCHAR(20)      DEFAULT NULL,
  `mensaje`        TEXT             NOT NULL,
  `leido`          TINYINT(1)       NOT NULL DEFAULT 0,
  `created_at`     DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`propiedad_id`) REFERENCES `propiedades`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de planes/publicaciones
CREATE TABLE IF NOT EXISTS `publicaciones` (
  `id`             INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `usuario_id`     INT(11) UNSIGNED NOT NULL,
  `propiedad_id`   INT(11) UNSIGNED NOT NULL,
  `plan`           ENUM('basico','destacado','premium') NOT NULL DEFAULT 'basico',
  `precio_pagado`  DECIMAL(8,2)     NOT NULL DEFAULT 0,
  `activo`         TINYINT(1)       NOT NULL DEFAULT 1,
  `fecha_inicio`   DATE             NOT NULL,
  `fecha_fin`      DATE             NOT NULL,
  `created_at`     DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`propiedad_id`) REFERENCES `propiedades`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ===============================
-- Datos de ejemplo
-- ===============================

-- Usuario administrador (contraseña: Admin123!)
INSERT INTO `usuarios` (`nombre`, `email`, `password`, `rol`, `verificado`, `activo`) VALUES
('Administrador', 'admin@rentings.lol', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.usfutpV92', 'admin', 1, 1),
('Carlos Hernández', 'carlos@ejemplo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.usfutpV92', 'anunciante', 1, 1),
('María López', 'maria@ejemplo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.usfutpV92', 'anunciante', 1, 1);

-- Propiedades de ejemplo
INSERT INTO `propiedades` (`usuario_id`, `titulo`, `descripcion`, `tipo`, `operacion`, `precio`, `recamaras`, `banos`, `metros_c`, `direccion`, `colonia`, `ciudad`, `destacado`, `activo`) VALUES
(2, 'Casa en Col. Los Sabinos', 'Hermosa casa en zona residencial con excelente ubicación, acabados de lujo, cochera techada para 2 autos y jardín amplio.', 'casa', 'venta', 2350000.00, 3, 2, 160, 'Calle Roble 123', 'Los Sabinos', 'Montemorelos', 1, 1),
(2, 'Departamento en Centro', 'Departamento moderno con vista panorámica, cocina integral, closets en todas las recámaras y balcón privado.', 'departamento', 'renta', 8500.00, 2, 1, 85, 'Av. Hidalgo 456', 'Centro', 'Montemorelos', 0, 1),
(3, 'Terreno en Fraccionamiento', 'Terreno en esquina con servicios, ideal para construir casa o local comercial en zona de alto desarrollo.', 'terreno', 'venta', 650000.00, 0, 0, 0, 'Blvd. Zaragoza s/n', 'Fraccionamientos', 'Montemorelos', 1, 1),
(3, 'Local Comercial en Centro', 'Local en avenida principal con alto flujo peatonal, instalaciones eléctricas y sanitarias en perfecto estado.', 'local', 'renta', 12000.00, 0, 1, 45, 'Morelos 789', 'Centro', 'Montemorelos', 0, 1),
(2, 'Casa en Zona UM', 'Casa familiar cerca de la Universidad de Montemorelos, ideal para familias o inversión. Área de lavandería y cuarto de servicio.', 'casa', 'venta_renta', 1850000.00, 4, 2, 210, 'Calle Estudiantes 15', 'Zona UM', 'Montemorelos', 1, 1),
(3, 'Quinta con Alberca', 'Espectacular quinta con alberca, asador, jardín de 1,200 m², sala de juegos y cuarto de visitas independiente.', 'quinta', 'venta', 4200000.00, 5, 3, 380, 'Km 3 Carretera a Iturbide', 'Comunidades rurales', 'Montemorelos', 1, 1);
