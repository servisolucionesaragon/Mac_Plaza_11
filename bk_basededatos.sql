-- MySQL dump 10.13  Distrib 8.0.46, for Linux (aarch64)
--
-- Host: localhost    Database: tiendacelulares_crm
-- ------------------------------------------------------
-- Server version	8.0.46-0ubuntu0.24.04.3

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `almacenamientos`
--

DROP TABLE IF EXISTS `almacenamientos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `almacenamientos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `almacenamientos`
--

LOCK TABLES `almacenamientos` WRITE;
/*!40000 ALTER TABLE `almacenamientos` DISABLE KEYS */;
INSERT INTO `almacenamientos` VALUES (1,'32GB',1,'2026-07-07 23:30:02','2026-07-07 23:30:02'),(2,'64GB',1,'2026-07-07 23:30:02','2026-07-07 23:30:02'),(3,'128GB',1,'2026-07-07 23:30:02','2026-07-07 23:30:02'),(4,'256GB',1,'2026-07-07 23:30:02','2026-07-07 23:30:02'),(5,'512GB',1,'2026-07-07 23:30:02','2026-07-07 23:30:02'),(6,'1TB',1,'2026-07-07 23:30:02','2026-07-07 23:30:02');
/*!40000 ALTER TABLE `almacenamientos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `catalogo_tipos`
--

DROP TABLE IF EXISTS `catalogo_tipos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `catalogo_tipos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icono` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `catalogo_tipos_nombre_unique` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `catalogo_tipos`
--

LOCK TABLES `catalogo_tipos` WRITE;
/*!40000 ALTER TABLE `catalogo_tipos` DISABLE KEYS */;
INSERT INTO `catalogo_tipos` VALUES (1,'Tamaño','Tamaño','fa-boxes',1,'2026-07-08 08:24:27','2026-07-08 08:24:27');
/*!40000 ALTER TABLE `catalogo_tipos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `catalogo_valores`
--

DROP TABLE IF EXISTS `catalogo_valores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `catalogo_valores` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `catalogo_tipo_id` bigint unsigned NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `catalogo_valores_catalogo_tipo_id_nombre_unique` (`catalogo_tipo_id`,`nombre`),
  CONSTRAINT `catalogo_valores_catalogo_tipo_id_foreign` FOREIGN KEY (`catalogo_tipo_id`) REFERENCES `catalogo_tipos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `catalogo_valores`
--

LOCK TABLES `catalogo_valores` WRITE;
/*!40000 ALTER TABLE `catalogo_valores` DISABLE KEYS */;
INSERT INTO `catalogo_valores` VALUES (1,1,'Grande',1,'2026-07-08 08:24:37','2026-07-08 08:24:37'),(2,1,'Mediano',1,'2026-07-08 08:24:46','2026-07-08 08:24:46'),(3,1,'Pequeño',1,'2026-07-08 08:24:59','2026-07-08 08:24:59');
/*!40000 ALTER TABLE `catalogo_valores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categorias`
--

DROP TABLE IF EXISTS `categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categorias` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias`
--

LOCK TABLES `categorias` WRITE;
/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
INSERT INTO `categorias` VALUES (1,'Smartphones',NULL,1,'2026-06-25 20:47:02','2026-06-25 20:47:02'),(2,'Tablets',NULL,1,'2026-06-25 20:47:02','2026-06-25 20:47:02'),(3,'Accesorios',NULL,1,'2026-06-25 20:47:02','2026-06-25 20:47:02'),(4,'Audífonos',NULL,1,'2026-06-25 20:47:02','2026-06-25 20:47:02'),(5,'Cargadores',NULL,1,'2026-06-25 20:47:02','2026-06-25 20:47:02'),(6,'Cases y Fundas',NULL,1,'2026-06-25 20:47:02','2026-06-25 20:47:02'),(7,'Repuestos',NULL,1,'2026-06-25 20:47:02','2026-06-25 20:47:02');
/*!40000 ALTER TABLE `categorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clientes`
--

DROP TABLE IF EXISTS `clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clientes` (
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
  `tipo_documento` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `departamento` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `clientes_email_unique` (`email`),
  UNIQUE KEY `clientes_dni_unique` (`dni`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes`
--

LOCK TABLES `clientes` WRITE;
/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
INSERT INTO `clientes` VALUES (1,'Yorbis','Aragon Bedoya','info@ssaragon.com','3015005084',NULL,'1038103291','Centro de negocios SSA','Medellin - Antioquia','1988-10-05','particular',NULL,NULL,NULL,1,'2026-07-05 17:00:01','2026-07-05 17:00:01',NULL,NULL),(2,'Pepito','Perez','pepe@gmail.com','99999','311398745','11111111','Cra 8 # 21 - 16','Caucasia','2000-02-21','particular',NULL,NULL,NULL,1,'2026-07-07 22:41:00','2026-07-07 22:41:00',NULL,NULL),(3,'JOSE DANIEL','HERNANDEZ ZURITA','admi@colombianexa.com','9989889','3114795047','1066522772',NULL,'Caucasia',NULL,'empresa','NEXA MHG COLOMBIA S.A.S.','902068627-2',NULL,1,'2026-07-07 22:45:53','2026-07-07 22:45:53',NULL,NULL);
/*!40000 ALTER TABLE `clientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `condiciones`
--

DROP TABLE IF EXISTS `condiciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `condiciones` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `condiciones`
--

LOCK TABLES `condiciones` WRITE;
/*!40000 ALTER TABLE `condiciones` DISABLE KEYS */;
INSERT INTO `condiciones` VALUES (1,'Nuevo',1,'2026-07-07 23:30:02','2026-07-07 23:30:02'),(2,'Reacondicionado',1,'2026-07-07 23:30:02','2026-07-07 23:30:02'),(3,'Usado',1,'2026-07-07 23:30:02','2026-07-07 23:30:02');
/*!40000 ALTER TABLE `condiciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `configuracion`
--

DROP TABLE IF EXISTS `configuracion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `configuracion` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre_tienda` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ruc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `departamento` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ciudad` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pagina_web` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `igv` decimal(5,2) NOT NULL DEFAULT '18.00',
  `moneda` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PEN',
  `simbolo_moneda` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'S/.',
  `terminos_garantia` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `timezone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'America/Bogota',
  `color_primario` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#a855f7',
  `color_secundario` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#ec4899',
  `color_acento` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#06b6d4',
  `color_sidebar` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#1a0a3e',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `configuracion`
--

LOCK TABLES `configuracion` WRITE;
/*!40000 ALTER TABLE `configuracion` DISABLE KEYS */;
INSERT INTO `configuracion` VALUES (1,'Mac Plaza 11','900444082-4','Mall Olimpica','Antioquia','Caucasia','3112135753','info@macplaza11.com','www.macplaza11.com','configuracion/mpqfzdvN2kzgNbG4poraJbtKMq6ozGRl1bbhLMwM.png',18.00,'COP','$',NULL,'2026-07-07 22:11:00','2026-07-08 12:13:38','America/Bogota','#a855f7','#ec4899','#06b6d4','#1a0a3e');
/*!40000 ALTER TABLE `configuracion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detalle_ventas`
--

DROP TABLE IF EXISTS `detalle_ventas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detalle_ventas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `venta_id` bigint unsigned NOT NULL,
  `producto_id` bigint unsigned NOT NULL,
  `cantidad` int NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `descuento` decimal(10,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(10,2) NOT NULL,
  `imei_vendido` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `serial_vendido` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `detalle_ventas_venta_id_foreign` (`venta_id`),
  KEY `detalle_ventas_producto_id_foreign` (`producto_id`),
  CONSTRAINT `detalle_ventas_producto_id_foreign` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `detalle_ventas_venta_id_foreign` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_ventas`
--

LOCK TABLES `detalle_ventas` WRITE;
/*!40000 ALTER TABLE `detalle_ventas` DISABLE KEYS */;
INSERT INTO `detalle_ventas` VALUES (1,1,1,3,0.00,0.00,0.00,NULL,NULL,'2026-07-05 17:00:29','2026-07-05 17:00:29'),(2,2,1,1,2.00,0.00,2.00,NULL,NULL,'2026-07-07 23:12:51','2026-07-07 23:12:51'),(3,3,1,1,2.00,0.00,2.00,'569595525',NULL,'2026-07-08 10:45:42','2026-07-08 10:45:42'),(4,4,3,2,8900.00,900.00,16900.00,NULL,NULL,'2026-07-08 11:00:26','2026-07-08 11:00:26'),(5,5,3,1,8900.00,900.00,8000.00,NULL,NULL,'2026-07-08 11:51:27','2026-07-08 11:51:27');
/*!40000 ALTER TABLE `detalle_ventas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `marcas`
--

DROP TABLE IF EXISTS `marcas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `marcas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `marcas`
--

LOCK TABLES `marcas` WRITE;
/*!40000 ALTER TABLE `marcas` DISABLE KEYS */;
INSERT INTO `marcas` VALUES (1,'Samsung',NULL,1,'2026-06-25 20:47:02','2026-06-25 20:47:02'),(2,'Apple',NULL,1,'2026-06-25 20:47:02','2026-06-25 20:47:02'),(3,'Xiaomi',NULL,1,'2026-06-25 20:47:02','2026-06-25 20:47:02'),(4,'Motorola',NULL,1,'2026-06-25 20:47:02','2026-06-25 20:47:02'),(5,'Huawei',NULL,1,'2026-06-25 20:47:03','2026-06-25 20:47:03'),(6,'OPPO',NULL,1,'2026-06-25 20:47:03','2026-06-25 20:47:03'),(7,'Realme',NULL,1,'2026-06-25 20:47:03','2026-06-25 20:47:03'),(8,'OnePlus',NULL,1,'2026-06-25 20:47:03','2026-06-25 20:47:03');
/*!40000 ALTER TABLE `marcas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `metodos_pago`
--

DROP TABLE IF EXISTS `metodos_pago`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `metodos_pago` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `metodos_pago`
--

LOCK TABLES `metodos_pago` WRITE;
/*!40000 ALTER TABLE `metodos_pago` DISABLE KEYS */;
INSERT INTO `metodos_pago` VALUES (1,'Efectivo',1,'2026-07-07 23:30:03','2026-07-07 23:30:03'),(2,'Tarjeta',1,'2026-07-07 23:30:03','2026-07-07 23:30:03'),(3,'Transferencia',1,'2026-07-07 23:30:03','2026-07-07 23:30:03');
/*!40000 ALTER TABLE `metodos_pago` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2019_12_14_000001_create_personal_access_tokens_table',1),(2,'2024_01_01_000001_create_users_table',1),(3,'2024_01_01_000002_create_clientes_table',1),(4,'2024_01_01_000003_create_categorias_table',1),(5,'2024_01_01_000004_create_marcas_table',1),(6,'2024_01_01_000005_create_productos_table',1),(7,'2024_01_01_000006_create_ventas_table',1),(8,'2024_01_01_000007_create_detalle_ventas_table',1),(9,'2024_01_01_000008_create_reparaciones_table',1),(10,'2024_01_01_000009_create_configuracion_table',1),(11,'2026_07_07_000001_add_timezone_to_configuracion_table',2),(12,'2026_07_08_000001_create_condiciones_table',3),(13,'2026_07_08_000002_create_almacenamientos_table',3),(14,'2026_07_08_000003_create_rams_table',3),(15,'2026_07_08_000004_add_catalogos_fk_to_productos_table',3),(16,'2026_07_08_000005_create_metodos_pago_table',3),(17,'2026_07_08_000006_add_metodo_pago_fk_to_ventas_table',3),(18,'2026_07_08_000007_create_catalogo_tipos_table',4),(19,'2026_07_08_000008_create_catalogo_valores_table',4),(20,'2026_07_08_000009_create_permisos_rol_table',5),(21,'2026_07_08_000010_add_colores_to_configuracion_table',6),(22,'2026_07_08_000011_add_departamento_tipo_documento_to_clientes_table',7),(23,'2026_07_08_000012_replace_imei_with_requiere_imei_in_productos_table',8),(24,'2026_07_08_000013_add_requiere_serial_and_serial_vendido',8),(25,'2026_07_08_000014_add_precio_incluye_impuesto_to_ventas_table',9),(26,'2026_07_08_000015_create_reparacion_historial_table',10),(27,'2026_07_08_000016_replace_precio_incluye_impuesto_with_modo_precio',11),(28,'2026_07_08_000017_add_datos_negocio_to_configuracion_table',12);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permisos_rol`
--

DROP TABLE IF EXISTS `permisos_rol`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permisos_rol` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `rol` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `modulo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `permitido` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permisos_rol_rol_modulo_unique` (`rol`,`modulo`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permisos_rol`
--

LOCK TABLES `permisos_rol` WRITE;
/*!40000 ALTER TABLE `permisos_rol` DISABLE KEYS */;
INSERT INTO `permisos_rol` VALUES (1,'vendedor','dashboard',1,'2026-07-08 08:37:44','2026-07-08 08:37:44'),(2,'vendedor','clientes',1,'2026-07-08 08:37:44','2026-07-08 08:37:44'),(3,'vendedor','productos',0,'2026-07-08 08:37:44','2026-07-08 08:37:44'),(4,'vendedor','ventas',1,'2026-07-08 08:37:44','2026-07-08 08:37:44'),(5,'vendedor','reparaciones',1,'2026-07-08 08:37:44','2026-07-08 08:37:44'),(6,'vendedor','reportes',1,'2026-07-08 08:37:44','2026-07-08 08:37:44'),(7,'tecnico','dashboard',0,'2026-07-08 08:37:44','2026-07-08 08:37:44'),(8,'tecnico','clientes',0,'2026-07-08 08:37:44','2026-07-08 08:37:44'),(9,'tecnico','productos',0,'2026-07-08 08:37:44','2026-07-08 08:37:44'),(10,'tecnico','ventas',0,'2026-07-08 08:37:44','2026-07-08 08:37:44'),(11,'tecnico','reparaciones',1,'2026-07-08 08:37:44','2026-07-08 08:37:44'),(12,'tecnico','reportes',0,'2026-07-08 08:37:44','2026-07-08 08:37:44');
/*!40000 ALTER TABLE `permisos_rol` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `productos`
--

DROP TABLE IF EXISTS `productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `productos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `categoria_id` bigint unsigned NOT NULL,
  `marca_id` bigint unsigned NOT NULL,
  `modelo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `almacenamiento_id` bigint unsigned DEFAULT NULL,
  `ram_id` bigint unsigned DEFAULT NULL,
  `precio_compra` decimal(10,2) NOT NULL DEFAULT '0.00',
  `precio_venta` decimal(10,2) NOT NULL DEFAULT '0.00',
  `stock` int NOT NULL DEFAULT '0',
  `stock_minimo` int NOT NULL DEFAULT '5',
  `imagen` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `requiere_imei` tinyint(1) NOT NULL DEFAULT '0',
  `requiere_serial` tinyint(1) NOT NULL DEFAULT '0',
  `condicion_id` bigint unsigned DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `productos_codigo_unique` (`codigo`),
  KEY `productos_categoria_id_foreign` (`categoria_id`),
  KEY `productos_marca_id_foreign` (`marca_id`),
  KEY `productos_condicion_id_foreign` (`condicion_id`),
  KEY `productos_almacenamiento_id_foreign` (`almacenamiento_id`),
  KEY `productos_ram_id_foreign` (`ram_id`),
  CONSTRAINT `productos_almacenamiento_id_foreign` FOREIGN KEY (`almacenamiento_id`) REFERENCES `almacenamientos` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `productos_categoria_id_foreign` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `productos_condicion_id_foreign` FOREIGN KEY (`condicion_id`) REFERENCES `condiciones` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `productos_marca_id_foreign` FOREIGN KEY (`marca_id`) REFERENCES `marcas` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `productos_ram_id_foreign` FOREIGN KEY (`ram_id`) REFERENCES `rams` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productos`
--

LOCK TABLES `productos` WRITE;
/*!40000 ALTER TABLE `productos` DISABLE KEYS */;
INSERT INTO `productos` VALUES (1,'CP01','Celular de prueba','Celular de prueba',1,2,'2026','Negro',3,2,1.00,2.00,5,5,'productos/DSLiWCcgw1ua6AD0XgDEGWsnJLaAVsvE4PIzvU3N.jpg',1,1,1,1,'2026-07-05 16:58:29','2026-07-08 10:47:48'),(2,'est123','Cargador de prueba',NULL,5,1,'CA 25W','Blanco',NULL,NULL,25000.00,36000.00,15,3,'productos/oa4ax18vIrMqvKMKf9GBe2po88DgRjDjluXpuHTM.jpg',0,1,1,1,'2026-07-08 10:50:31','2026-07-08 10:53:15'),(3,'C987','Cable Tipo C 1.5 mts','Clable Tipo C 1.5 mts',3,6,'C987','Negro',NULL,NULL,5600.00,8900.00,17,5,'productos/8TJ4PiTLYxlrK5F5JZPr7zpMQM6lvxNqdvyLfPxw.jpg',0,0,1,1,'2026-07-08 10:52:31','2026-07-08 11:51:27');
/*!40000 ALTER TABLE `productos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rams`
--

DROP TABLE IF EXISTS `rams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rams` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rams`
--

LOCK TABLES `rams` WRITE;
/*!40000 ALTER TABLE `rams` DISABLE KEYS */;
INSERT INTO `rams` VALUES (1,'2GB',1,'2026-07-07 23:30:02','2026-07-07 23:30:02'),(2,'3GB',1,'2026-07-07 23:30:02','2026-07-07 23:30:02'),(3,'4GB',1,'2026-07-07 23:30:02','2026-07-07 23:30:02'),(4,'6GB',1,'2026-07-07 23:30:02','2026-07-07 23:30:02'),(5,'8GB',1,'2026-07-07 23:30:02','2026-07-07 23:30:02'),(6,'12GB',1,'2026-07-07 23:30:02','2026-07-07 23:30:02'),(7,'16GB',1,'2026-07-07 23:30:02','2026-07-07 23:30:02');
/*!40000 ALTER TABLE `rams` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reparacion_historial`
--

DROP TABLE IF EXISTS `reparacion_historial`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reparacion_historial` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reparacion_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `estado_anterior` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado_nuevo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nota` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reparacion_historial_reparacion_id_foreign` (`reparacion_id`),
  KEY `reparacion_historial_user_id_foreign` (`user_id`),
  CONSTRAINT `reparacion_historial_reparacion_id_foreign` FOREIGN KEY (`reparacion_id`) REFERENCES `reparaciones` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reparacion_historial_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reparacion_historial`
--

LOCK TABLES `reparacion_historial` WRITE;
/*!40000 ALTER TABLE `reparacion_historial` DISABLE KEYS */;
INSERT INTO `reparacion_historial` VALUES (1,3,5,NULL,'recibido','Orden creada.','2026-07-08 11:30:35','2026-07-08 11:30:35'),(2,3,5,'recibido','en_diagnostico',NULL,'2026-07-08 11:31:20','2026-07-08 11:31:20'),(3,3,5,'en_diagnostico','en_reparacion','se restablece a configuracion de fabrica','2026-07-08 11:32:58','2026-07-08 11:32:58'),(4,3,5,'en_reparacion','listo','a la espera que el cliente lo recoja','2026-07-08 11:33:28','2026-07-08 11:33:28'),(5,3,5,'listo','entregado','se entrega al cliente revisado y funcionando','2026-07-08 11:33:52','2026-07-08 11:33:52');
/*!40000 ALTER TABLE `reparacion_historial` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reparaciones`
--

DROP TABLE IF EXISTS `reparaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reparaciones` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `numero_orden` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cliente_id` bigint unsigned NOT NULL,
  `tecnico_id` bigint unsigned DEFAULT NULL,
  `dispositivo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `marca` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `modelo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `imei` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `falla_reportada` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `diagnostico` text COLLATE utf8mb4_unicode_ci,
  `solucion` text COLLATE utf8mb4_unicode_ci,
  `presupuesto` decimal(10,2) DEFAULT NULL,
  `costo_final` decimal(10,2) DEFAULT NULL,
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
  CONSTRAINT `reparaciones_tecnico_id_foreign` FOREIGN KEY (`tecnico_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reparaciones`
--

LOCK TABLES `reparaciones` WRITE;
/*!40000 ALTER TABLE `reparaciones` DISABLE KEYS */;
INSERT INTO `reparaciones` VALUES (1,'REP-000001',3,7,'Iphone 16 pro max','Iphone','pro max','989562665646','Naranja','Vidrio frontal quebrado',NULL,NULL,1.00,NULL,'recibido','media','2026-07-07 22:53:36','2026-07-21 00:00:00',NULL,'Estuche blanco',0,30,'2026-07-07 22:53:36','2026-07-07 22:53:36'),(2,'REP-000002',2,7,'Tablet','Samsung','Galaxi Tap 7','65487966464','Blanca','no enciende','habia un integrado quemado','se cambia integrado y funciona correctamente',10000.00,15000.00,'entregado','alta','2026-07-07 22:54:48','2026-07-19 00:00:00','2026-07-08 11:13:37','cargador y Cable',1,15,'2026-07-07 22:54:48','2026-07-08 11:28:53'),(3,'REP-000003',1,7,'Celular','Iphone','16 Pro de 512GB','3456870','Azul','No entran las llamdas','se revisa si tiene desvio de llamadas activado','se restablece la a estado de fabrica y funciona correctamente',50000.00,60000.00,'entregado','urgente','2026-07-08 11:30:35','2026-07-10 00:00:00','2026-07-08 11:33:52',NULL,1,10,'2026-07-08 11:30:35','2026-07-08 11:33:52');
/*!40000 ALTER TABLE `reparaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Administrador','admin@tienda.com','$2y$12$hpLF/SU8mWRyJ1uSua3EkOzPcull04/kWN4tbq3FsGNdDTUqJEbFG','admin',NULL,NULL,1,NULL,NULL,'2026-06-25 20:47:02','2026-06-25 20:47:02'),(4,'Eliana','eliana@macplaza11.com','$2y$12$wJBXdWQYoKZr5NZdX.q9D.o0v1OJCrHXT9PYxZ30w/gKi3oq1gtiy','admin','3112135753',NULL,1,NULL,NULL,'2026-07-07 22:13:59','2026-07-07 22:13:59'),(5,'Yorbis Aragon Bedoya','servisolucionesti@gmail.com','$2y$12$7lPSq6iMZMWOxKigfQILyuOk.672mlSxwGsIS2sG.B3iGhXlEObIu','admin','3246282324',NULL,1,NULL,NULL,'2026-07-07 22:16:42','2026-07-07 22:16:42'),(6,'Ventas','ventas@macplaza11.com','$2y$12$dbdJr5ZflmyODKgK.yI/VOzx0KVnlva2y9LUbdhmX7RPS0yHTRtp2','vendedor',NULL,NULL,1,NULL,NULL,'2026-07-07 22:21:35','2026-07-07 22:21:35'),(7,'Tecnico 1','tecnico1@macplaza11.com','$2y$12$eRLA5w6quoivtMXqV6xvNOWb9TTToDltVRDtAZ0aNlDIZ0uSyDCty','tecnico',NULL,NULL,1,NULL,NULL,'2026-07-07 22:22:11','2026-07-07 22:22:11');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ventas`
--

DROP TABLE IF EXISTS `ventas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ventas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `numero_venta` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cliente_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `fecha_venta` datetime NOT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `descuento` decimal(10,2) NOT NULL DEFAULT '0.00',
  `impuesto` decimal(10,2) NOT NULL DEFAULT '0.00',
  `modo_precio` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'subtotal_impuesto',
  `total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `metodo_pago_id` bigint unsigned DEFAULT NULL,
  `estado` enum('pendiente','completada','cancelada','devuelta') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'completada',
  `notas` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ventas_numero_venta_unique` (`numero_venta`),
  KEY `ventas_cliente_id_foreign` (`cliente_id`),
  KEY `ventas_user_id_foreign` (`user_id`),
  KEY `ventas_metodo_pago_id_foreign` (`metodo_pago_id`),
  CONSTRAINT `ventas_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `ventas_metodo_pago_id_foreign` FOREIGN KEY (`metodo_pago_id`) REFERENCES `metodos_pago` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `ventas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ventas`
--

LOCK TABLES `ventas` WRITE;
/*!40000 ALTER TABLE `ventas` DISABLE KEYS */;
INSERT INTO `ventas` VALUES (1,'VTA-000001',1,1,'2026-07-05 17:00:29',0.00,0.00,0.00,'subtotal_impuesto',0.00,3,'completada','ojo con eso','2026-07-05 17:00:29','2026-07-05 17:00:29'),(2,'VTA-000002',3,1,'2026-07-07 23:12:51',2.00,0.50,0.27,'subtotal_impuesto',1.77,3,'completada','Imei: 654684651564','2026-07-07 23:12:51','2026-07-07 23:12:51'),(3,'VTA-000003',1,5,'2026-07-08 10:45:42',2.00,0.00,0.36,'subtotal_impuesto',2.36,1,'completada',NULL,'2026-07-08 10:45:42','2026-07-08 10:45:42'),(4,'VTA-000004',2,5,'2026-07-08 11:00:26',16900.00,1000.00,2862.00,'subtotal_impuesto',18762.00,3,'completada',NULL,'2026-07-08 11:00:26','2026-07-08 11:00:26'),(5,'VTA-000005',1,5,'2026-07-08 11:51:27',6355.93,500.00,1144.07,'incluido',7500.00,1,'completada',NULL,'2026-07-08 11:51:27','2026-07-08 11:51:27');
/*!40000 ALTER TABLE `ventas` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-07-08 18:53:29
