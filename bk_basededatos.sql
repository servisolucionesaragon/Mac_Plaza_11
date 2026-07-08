-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         8.4.3 - MySQL Community Server - GPL
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Volcando estructura de base de datos para tiendacelulares_crm
CREATE DATABASE IF NOT EXISTS `tiendacelulares_crm` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `tiendacelulares_crm`;

-- Volcando estructura para tabla tiendacelulares_crm.categorias
CREATE TABLE IF NOT EXISTS `categorias` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla tiendacelulares_crm.categorias: ~7 rows (aproximadamente)
DELETE FROM `categorias`;
INSERT INTO `categorias` (`id`, `nombre`, `descripcion`, `activo`, `created_at`, `updated_at`) VALUES
	(1, 'Smartphones', NULL, 1, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(2, 'Tablets', NULL, 1, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(3, 'Accesorios', NULL, 1, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(4, 'Audífonos', NULL, 1, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(5, 'Cargadores', NULL, 1, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(6, 'Cases y Fundas', NULL, 1, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(7, 'Repuestos', NULL, 1, '2026-05-02 03:41:03', '2026-05-02 03:41:03');

-- Volcando estructura para tabla tiendacelulares_crm.clientes
CREATE TABLE IF NOT EXISTS `clientes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `celular` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dni` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` text COLLATE utf8mb4_unicode_ci,
  `ciudad` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `tipo` enum('particular','empresa') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'particular',
  `empresa` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ruc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notas` text COLLATE utf8mb4_unicode_ci,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `clientes_email_unique` (`email`),
  UNIQUE KEY `clientes_dni_unique` (`dni`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla tiendacelulares_crm.clientes: ~10 rows (aproximadamente)
DELETE FROM `clientes`;
INSERT INTO `clientes` (`id`, `nombre`, `apellido`, `email`, `telefono`, `celular`, `dni`, `direccion`, `ciudad`, `fecha_nacimiento`, `tipo`, `empresa`, `ruc`, `notas`, `activo`, `created_at`, `updated_at`) VALUES
	(1, 'María', 'García', 'maria.garcia@gmail.com', '987654321', NULL, '45123456', NULL, 'Lima', NULL, 'particular', NULL, NULL, NULL, 1, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(2, 'Carlos', 'López', 'carlos.lopez@gmail.com', '965432187', NULL, '32145678', NULL, 'Lima', NULL, 'particular', NULL, NULL, NULL, 1, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(3, 'Ana', 'Martínez', 'ana.martinez@hotmail.com', '974561230', NULL, '56789012', NULL, 'Lima', NULL, 'particular', NULL, NULL, NULL, 1, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(4, 'Pedro', 'Sánchez', 'pedro.sanchez@outlook.com', '912345678', NULL, '78901234', NULL, 'Lima', NULL, 'particular', NULL, NULL, NULL, 1, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(5, 'Lucía', 'Torres', NULL, '998765432', NULL, '89012345', NULL, 'Lima', NULL, 'particular', NULL, NULL, NULL, 1, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(6, 'Roberto', 'Flores', 'roberto.flores@gmail.com', '945678901', NULL, '12345671', NULL, 'Lima', NULL, 'particular', NULL, NULL, NULL, 1, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(7, 'Elena', 'Vásquez', 'elena.vasquez@techcorp.pe', '934567890', NULL, '23456782', NULL, 'Lima', NULL, 'empresa', 'TechCorp SAC', '20512345671', NULL, 1, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(8, 'Miguel', 'Quispe', 'miguel.quispe@outlook.com', '923456789', NULL, '34567893', NULL, 'Lima', NULL, 'particular', NULL, NULL, NULL, 1, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(9, 'Sofía', 'Mendoza', 'sofia.mendoza@gmail.com', '912345670', NULL, '45678904', NULL, 'Lima', NULL, 'particular', NULL, NULL, NULL, 1, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(10, 'Diego', 'Herrera', 'diego.herrera@movilstore.pe', '901234567', NULL, '56789015', NULL, 'Lima', NULL, 'empresa', 'MovilStore EIRL', '20612345672', NULL, 1, '2026-05-02 03:41:03', '2026-05-02 03:41:03');

-- Volcando estructura para tabla tiendacelulares_crm.configuracion
CREATE TABLE IF NOT EXISTS `configuracion` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre_tienda` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ruc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `igv` decimal(5,2) NOT NULL DEFAULT '18.00',
  `moneda` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PEN',
  `simbolo_moneda` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'S/.',
  `terminos_garantia` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla tiendacelulares_crm.configuracion: ~0 rows (aproximadamente)
DELETE FROM `configuracion`;

-- Volcando estructura para tabla tiendacelulares_crm.detalle_ventas
CREATE TABLE IF NOT EXISTS `detalle_ventas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `venta_id` bigint unsigned NOT NULL,
  `producto_id` bigint unsigned NOT NULL,
  `cantidad` int NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `descuento` decimal(10,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(10,2) NOT NULL,
  `imei_vendido` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `detalle_ventas_venta_id_foreign` (`venta_id`),
  KEY `detalle_ventas_producto_id_foreign` (`producto_id`),
  CONSTRAINT `detalle_ventas_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `detalle_ventas_venta_id_foreign` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla tiendacelulares_crm.detalle_ventas: ~26 rows (aproximadamente)
DELETE FROM `detalle_ventas`;
INSERT INTO `detalle_ventas` (`id`, `venta_id`, `producto_id`, `cantidad`, `precio_unitario`, `descuento`, `subtotal`, `imei_vendido`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 1, 899.00, 0.00, 899.00, NULL, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(2, 1, 8, 2, 25.00, 0.00, 50.00, NULL, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(3, 2, 3, 1, 3499.00, 0.00, 3499.00, NULL, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(4, 3, 4, 1, 999.00, 0.00, 999.00, NULL, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(5, 3, 6, 1, 199.00, 0.00, 199.00, NULL, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(6, 4, 5, 2, 699.00, 0.00, 1398.00, NULL, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(7, 5, 2, 1, 1299.00, 0.00, 1299.00, NULL, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(8, 5, 7, 2, 35.00, 0.00, 70.00, NULL, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(9, 6, 9, 1, 849.00, 0.00, 849.00, NULL, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(10, 6, 6, 1, 199.00, 0.00, 199.00, NULL, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(11, 7, 10, 1, 629.00, 0.00, 629.00, NULL, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(12, 7, 8, 3, 25.00, 0.00, 75.00, NULL, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(13, 8, 3, 1, 3499.00, 0.00, 3499.00, NULL, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(14, 8, 7, 1, 35.00, 0.00, 35.00, NULL, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(15, 9, 1, 1, 899.00, 0.00, 899.00, NULL, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(16, 10, 2, 1, 1299.00, 0.00, 1299.00, NULL, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(17, 11, 4, 2, 999.00, 0.00, 1998.00, NULL, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(18, 12, 5, 1, 699.00, 0.00, 699.00, NULL, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(19, 12, 6, 2, 199.00, 0.00, 398.00, NULL, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(20, 13, 9, 2, 849.00, 0.00, 1698.00, NULL, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(21, 14, 3, 1, 3499.00, 0.00, 3499.00, NULL, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(22, 14, 8, 1, 25.00, 0.00, 25.00, NULL, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(23, 15, 10, 1, 629.00, 0.00, 629.00, NULL, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(24, 15, 7, 3, 35.00, 0.00, 105.00, NULL, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(25, 16, 1, 1, 899.00, 0.00, 899.00, NULL, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(26, 16, 6, 1, 199.00, 0.00, 199.00, NULL, '2026-05-02 03:41:03', '2026-05-02 03:41:03');

-- Volcando estructura para tabla tiendacelulares_crm.marcas
CREATE TABLE IF NOT EXISTS `marcas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla tiendacelulares_crm.marcas: ~8 rows (aproximadamente)
DELETE FROM `marcas`;
INSERT INTO `marcas` (`id`, `nombre`, `logo`, `activo`, `created_at`, `updated_at`) VALUES
	(1, 'Samsung', NULL, 1, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(2, 'Apple', NULL, 1, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(3, 'Xiaomi', NULL, 1, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(4, 'Motorola', NULL, 1, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(5, 'Huawei', NULL, 1, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(6, 'OPPO', NULL, 1, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(7, 'Realme', NULL, 1, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(8, 'OnePlus', NULL, 1, '2026-05-02 03:41:03', '2026-05-02 03:41:03');

-- Volcando estructura para tabla tiendacelulares_crm.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla tiendacelulares_crm.migrations: ~0 rows (aproximadamente)
DELETE FROM `migrations`;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '2019_12_14_000001_create_personal_access_tokens_table', 1),
	(2, '2024_01_01_000001_create_users_table', 1),
	(3, '2024_01_01_000002_create_clientes_table', 1),
	(4, '2024_01_01_000003_create_categorias_table', 1),
	(5, '2024_01_01_000004_create_marcas_table', 1),
	(6, '2024_01_01_000005_create_productos_table', 1),
	(7, '2024_01_01_000006_create_ventas_table', 1),
	(8, '2024_01_01_000007_create_detalle_ventas_table', 1),
	(9, '2024_01_01_000008_create_reparaciones_table', 1),
	(10, '2024_01_01_000009_create_configuracion_table', 1);

-- Volcando estructura para tabla tiendacelulares_crm.personal_access_tokens
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla tiendacelulares_crm.personal_access_tokens: ~0 rows (aproximadamente)
DELETE FROM `personal_access_tokens`;

-- Volcando estructura para tabla tiendacelulares_crm.productos
CREATE TABLE IF NOT EXISTS `productos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `categoria_id` bigint unsigned NOT NULL,
  `marca_id` bigint unsigned NOT NULL,
  `modelo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `almacenamiento` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ram` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `precio_compra` decimal(10,2) NOT NULL DEFAULT '0.00',
  `precio_venta` decimal(10,2) NOT NULL DEFAULT '0.00',
  `stock` int NOT NULL DEFAULT '0',
  `stock_minimo` int NOT NULL DEFAULT '5',
  `imagen` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `imei` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `condicion` enum('nuevo','reacondicionado','usado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'nuevo',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `productos_codigo_unique` (`codigo`),
  KEY `productos_categoria_id_foreign` (`categoria_id`),
  KEY `productos_marca_id_foreign` (`marca_id`),
  CONSTRAINT `productos_categoria_id_foreign` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `productos_marca_id_foreign` FOREIGN KEY (`marca_id`) REFERENCES `marcas` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla tiendacelulares_crm.productos: ~10 rows (aproximadamente)
DELETE FROM `productos`;
INSERT INTO `productos` (`id`, `codigo`, `nombre`, `descripcion`, `categoria_id`, `marca_id`, `modelo`, `color`, `almacenamiento`, `ram`, `precio_compra`, `precio_venta`, `stock`, `stock_minimo`, `imagen`, `imei`, `condicion`, `activo`, `created_at`, `updated_at`) VALUES
	(1, 'SAM-A54-128', 'Samsung Galaxy A54', NULL, 1, 1, 'A54', NULL, '128GB', '8GB', 650.00, 899.00, 12, 3, NULL, NULL, 'nuevo', 1, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(2, 'SAM-S24-256', 'Samsung Galaxy S24', NULL, 1, 1, 'S24', NULL, '256GB', '12GB', 950.00, 1299.00, 6, 3, NULL, NULL, 'nuevo', 1, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(3, 'APP-IPH15-128', 'iPhone 15', NULL, 1, 2, '15', NULL, '128GB', '6GB', 2500.00, 3499.00, 2, 3, NULL, NULL, 'nuevo', 1, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(4, 'XIA-13T-256', 'Xiaomi 13T', NULL, 1, 3, '13T', NULL, '256GB', '12GB', 700.00, 999.00, 9, 3, NULL, NULL, 'nuevo', 1, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(5, 'MOT-G84-256', 'Motorola Moto G84', NULL, 1, 4, 'G84', NULL, '256GB', '12GB', 480.00, 699.00, 7, 3, NULL, NULL, 'nuevo', 1, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(6, 'AUD-SAM-TW', 'Samsung Galaxy Buds2', NULL, 4, 1, 'Buds2', NULL, NULL, NULL, 120.00, 199.00, 15, 3, NULL, NULL, 'nuevo', 1, '2026-05-02 03:41:03', '2026-05-02 03:41:04'),
	(7, 'CAR-USB-C-65', 'Cargador USB-C 65W', NULL, 5, 3, NULL, NULL, NULL, NULL, 18.00, 35.00, 44, 3, NULL, NULL, 'nuevo', 1, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(8, 'CASE-IPH15', 'Case iPhone 15 Pro', NULL, 6, 2, NULL, NULL, NULL, NULL, 8.00, 25.00, 24, 3, NULL, NULL, 'nuevo', 1, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(9, 'HUA-NOV11-128', 'Huawei Nova 11', NULL, 1, 5, 'Nova 11', NULL, '128GB', '8GB', 580.00, 849.00, 15, 3, NULL, NULL, 'nuevo', 1, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(10, 'OPP-A58-128', 'OPPO A58', NULL, 1, 6, 'A58', NULL, '128GB', '6GB', 420.00, 629.00, 18, 3, NULL, NULL, 'nuevo', 1, '2026-05-02 03:41:03', '2026-05-02 03:41:03');

-- Volcando estructura para tabla tiendacelulares_crm.reparaciones
CREATE TABLE IF NOT EXISTS `reparaciones` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `numero_orden` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cliente_id` bigint unsigned NOT NULL,
  `tecnico_id` bigint unsigned NOT NULL,
  `dispositivo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `marca` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `modelo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `imei` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `falla_reportada` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `diagnostico` text COLLATE utf8mb4_unicode_ci,
  `solucion` text COLLATE utf8mb4_unicode_ci,
  `presupuesto` decimal(10,2) NOT NULL DEFAULT '0.00',
  `costo_final` decimal(10,2) NOT NULL DEFAULT '0.00',
  `estado` enum('recibido','en_diagnostico','esperando_repuesto','en_reparacion','listo','entregado','no_reparable') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'recibido',
  `prioridad` enum('baja','media','alta','urgente') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'media',
  `fecha_recepcion` datetime NOT NULL,
  `fecha_estimada` datetime DEFAULT NULL,
  `fecha_entrega` datetime DEFAULT NULL,
  `notas` text COLLATE utf8mb4_unicode_ci,
  `garantia` tinyint(1) NOT NULL DEFAULT '0',
  `dias_garantia` int NOT NULL DEFAULT '30',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reparaciones_numero_orden_unique` (`numero_orden`),
  KEY `reparaciones_cliente_id_foreign` (`cliente_id`),
  KEY `reparaciones_tecnico_id_foreign` (`tecnico_id`),
  CONSTRAINT `reparaciones_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `reparaciones_tecnico_id_foreign` FOREIGN KEY (`tecnico_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla tiendacelulares_crm.reparaciones: ~6 rows (aproximadamente)
DELETE FROM `reparaciones`;
INSERT INTO `reparaciones` (`id`, `numero_orden`, `cliente_id`, `tecnico_id`, `dispositivo`, `marca`, `modelo`, `imei`, `color`, `falla_reportada`, `diagnostico`, `solucion`, `presupuesto`, `costo_final`, `estado`, `prioridad`, `fecha_recepcion`, `fecha_estimada`, `fecha_entrega`, `notas`, `garantia`, `dias_garantia`, `created_at`, `updated_at`) VALUES
	(1, 'REP-000001', 1, 3, 'Samsung Galaxy A32', 'Samsung', 'A32', NULL, NULL, 'Pantalla rota por caída', 'LCD fragmentado, táctil sin respuesta', 'Reemplazo módulo LCD + táctil', 180.00, 160.00, 'entregado', 'media', '2026-02-10 09:00:00', '2026-02-13 00:00:00', '2026-02-13 17:30:00', NULL, 1, 30, '2026-05-02 03:41:04', '2026-05-02 03:41:04'),
	(2, 'REP-000002', 2, 3, 'iPhone 13', 'Apple', '13', NULL, NULL, 'Batería no carga, apagados repentinos', 'Batería degradada al 64%', 'Cambio batería original Apple', 220.00, 200.00, 'entregado', 'alta', '2026-02-18 10:30:00', '2026-02-21 00:00:00', '2026-02-20 15:00:00', NULL, 1, 90, '2026-05-02 03:41:04', '2026-05-02 03:41:04'),
	(3, 'REP-000003', 3, 3, 'Xiaomi Redmi Note 11', 'Xiaomi', 'Redmi Note 11', NULL, NULL, 'Se apaga solo cada 30 minutos', 'Software corrupto + batería débil', 'Flash firmware MIUI + reemplazo batería', 150.00, 130.00, 'entregado', 'baja', '2026-03-01 11:00:00', '2026-03-06 00:00:00', '2026-03-05 16:45:00', NULL, 0, 0, '2026-05-02 03:41:04', '2026-05-02 03:41:04'),
	(4, 'REP-000004', 4, 3, 'Samsung Galaxy S21', 'Samsung', 'S21', NULL, NULL, 'Cámara trasera no enfoca', 'Módulo de cámara principal dañado', 'Reemplazo módulo cámara 108MP Samsung', 350.00, 320.00, 'entregado', 'urgente', '2026-03-15 09:30:00', '2026-03-19 00:00:00', '2026-03-18 14:00:00', NULL, 1, 60, '2026-05-02 03:41:04', '2026-05-02 03:41:04'),
	(5, 'REP-000005', 5, 3, 'Motorola Moto G52', 'Motorola', 'Moto G52', NULL, NULL, 'Micrófono no funciona en llamadas', 'Micrófono MEMS dañado por humedad', 'Reemplazo micrófono + limpieza placa', 120.00, 100.00, 'listo', 'media', '2026-04-03 10:00:00', '2026-04-07 00:00:00', NULL, NULL, 1, 30, '2026-05-02 03:41:04', '2026-05-02 03:41:04'),
	(6, 'REP-000006', 1, 3, 'Xiaomi Redmi 10C', 'Xiaomi', 'Redmi 10C', NULL, NULL, 'Conector de carga suelto', 'Puerto USB-C desgastado, pines doblados', 'Reemplazo módulo USB-C', 80.00, 70.00, 'listo', 'media', '2026-04-22 14:30:00', '2026-04-27 00:00:00', NULL, NULL, 1, 30, '2026-05-02 03:41:04', '2026-05-02 03:41:04');

-- Volcando estructura para tabla tiendacelulares_crm.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol` enum('admin','vendedor','tecnico') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'vendedor',
  `telefono` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla tiendacelulares_crm.users: ~2 rows (aproximadamente)
DELETE FROM `users`;
INSERT INTO `users` (`id`, `name`, `email`, `password`, `rol`, `telefono`, `foto`, `activo`, `email_verified_at`, `remember_token`, `created_at`, `updated_at`) VALUES
	(1, 'Administrador', 'admin@tienda.com', '$2y$12$CGzcMDCA7tnl7ALvHQHZAuYqxthxKIwmzQQUtYz5RVsuU.wpM93QC', 'admin', NULL, NULL, 1, NULL, NULL, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(2, 'Juan Vendedor', 'vendedor@tienda.com', '$2y$12$EdDpqx/pGBHW1csdC3iAKuwc.PCSwyvDvuwjrIPKsG2mR.HbEQ97y', 'vendedor', NULL, NULL, 1, NULL, NULL, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(3, 'Carlos Técnico', 'tecnico@tienda.com', '$2y$12$WRaLOWy/HknDu/jDpoVJQ.ONCWtZq9gnLf3MPh7a/IGZMP4PK4rcm', 'tecnico', NULL, NULL, 1, NULL, NULL, '2026-05-02 03:41:03', '2026-05-02 03:41:03');

-- Volcando estructura para tabla tiendacelulares_crm.ventas
CREATE TABLE IF NOT EXISTS `ventas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `numero_venta` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cliente_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `fecha_venta` datetime NOT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `descuento` decimal(10,2) NOT NULL DEFAULT '0.00',
  `impuesto` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `metodo_pago` enum('efectivo','tarjeta','transferencia','cuotas','yape','plin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'efectivo',
  `estado` enum('pendiente','completada','cancelada','devuelta') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'completada',
  `notas` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ventas_numero_venta_unique` (`numero_venta`),
  KEY `ventas_cliente_id_foreign` (`cliente_id`),
  KEY `ventas_user_id_foreign` (`user_id`),
  CONSTRAINT `ventas_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `ventas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla tiendacelulares_crm.ventas: ~16 rows (aproximadamente)
DELETE FROM `ventas`;
INSERT INTO `ventas` (`id`, `numero_venta`, `cliente_id`, `user_id`, `fecha_venta`, `subtotal`, `descuento`, `impuesto`, `total`, `metodo_pago`, `estado`, `notas`, `created_at`, `updated_at`) VALUES
	(1, 'VTA-000001', 1, 2, '2025-11-05 10:20:00', 949.00, 0.00, 170.82, 1119.82, 'efectivo', 'completada', NULL, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(2, 'VTA-000002', 2, 1, '2025-11-18 15:45:00', 3499.00, 0.00, 629.82, 4128.82, 'tarjeta', 'completada', NULL, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(3, 'VTA-000003', 3, 2, '2025-12-03 11:00:00', 1198.00, 0.00, 215.64, 1413.64, 'yape', 'completada', NULL, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(4, 'VTA-000004', 4, 1, '2025-12-20 16:30:00', 1398.00, 0.00, 251.64, 1649.64, 'efectivo', 'completada', NULL, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(5, 'VTA-000005', 5, 2, '2026-01-08 09:15:00', 1369.00, 0.00, 246.42, 1615.42, 'transferencia', 'completada', NULL, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(6, 'VTA-000006', 1, 1, '2026-01-22 14:00:00', 1048.00, 0.00, 188.64, 1236.64, 'tarjeta', 'completada', NULL, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(7, 'VTA-000007', 2, 2, '2026-02-04 10:30:00', 704.00, 0.00, 126.72, 830.72, 'yape', 'completada', NULL, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(8, 'VTA-000008', 3, 1, '2026-02-14 13:00:00', 3534.00, 0.00, 636.12, 4170.12, 'efectivo', 'completada', NULL, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(9, 'VTA-000009', 4, 2, '2026-02-28 17:10:00', 899.00, 0.00, 161.82, 1060.82, 'plin', 'completada', NULL, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(10, 'VTA-000010', 5, 1, '2026-03-05 09:45:00', 1299.00, 0.00, 233.82, 1532.82, 'tarjeta', 'completada', NULL, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(11, 'VTA-000011', 1, 2, '2026-03-14 11:30:00', 1998.00, 0.00, 359.64, 2357.64, 'efectivo', 'completada', NULL, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(12, 'VTA-000012', 2, 1, '2026-03-25 16:00:00', 1097.00, 0.00, 197.46, 1294.46, 'yape', 'completada', NULL, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(13, 'VTA-000013', 3, 2, '2026-04-02 10:00:00', 1698.00, 0.00, 305.64, 2003.64, 'transferencia', 'completada', NULL, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(14, 'VTA-000014', 4, 1, '2026-04-15 14:30:00', 3524.00, 0.00, 634.32, 4158.32, 'tarjeta', 'completada', NULL, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(15, 'VTA-000015', 5, 2, '2026-04-28 09:00:00', 734.00, 0.00, 132.12, 866.12, 'efectivo', 'completada', NULL, '2026-05-02 03:41:03', '2026-05-02 03:41:03'),
	(16, 'VTA-000016', 1, 1, '2026-05-01 09:30:00', 1098.00, 0.00, 197.64, 1295.64, 'yape', 'completada', NULL, '2026-05-02 03:41:03', '2026-05-02 03:41:03');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
