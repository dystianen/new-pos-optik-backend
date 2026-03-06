-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.4.3 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for pos_optik
CREATE DATABASE IF NOT EXISTS `pos_optik` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `pos_optik`;

-- Dumping structure for table pos_optik.carts
CREATE TABLE IF NOT EXISTS `carts` (
  `cart_id` char(36) NOT NULL,
  `customer_id` char(36) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`cart_id`),
  KEY `carts_customer_id_foreign` (`customer_id`),
  CONSTRAINT `carts_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table pos_optik.carts: ~1 rows (approximately)
INSERT INTO `carts` (`cart_id`, `customer_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
	('397ab3ad-e99c-41f5-81b2-c5863108b13f', '17f27383-11c0-4a1d-84a6-49c1af625b2b', '2026-02-03 16:49:33', '2026-02-03 16:49:33', NULL),
	('491abc4b-a607-4512-83ef-0133e4a767d6', 'c88af1e9-b882-4597-82ca-d414942926e0', '2026-03-06 11:05:42', '2026-03-06 11:05:42', NULL),
	('6af4a270-82a0-406f-8d5c-92a794eb3de3', '025f77f1-dbb3-49d8-bb35-01e908a8e181', '2025-12-31 04:20:23', '2025-12-31 04:20:23', NULL),
	('a0acd4e4-bcf3-4e33-a4f7-c8d7589f68fd', '04c82b5b-ea89-4a15-9fa8-8deec59610b2', '2026-01-13 02:51:29', '2026-01-13 02:51:29', NULL);

-- Dumping structure for table pos_optik.cart_items
CREATE TABLE IF NOT EXISTS `cart_items` (
  `cart_item_id` char(36) NOT NULL,
  `cart_id` char(36) NOT NULL,
  `product_id` char(36) DEFAULT NULL,
  `variant_id` char(36) DEFAULT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `price` decimal(10,2) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`cart_item_id`),
  KEY `cart_items_cart_id_foreign` (`cart_id`),
  KEY `cart_items_product_id_foreign` (`product_id`),
  KEY `cart_items_variant_id_foreign` (`variant_id`),
  CONSTRAINT `cart_items_cart_id_foreign` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`cart_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `cart_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE SET NULL,
  CONSTRAINT `cart_items_variant_id_foreign` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`variant_id`) ON DELETE CASCADE ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table pos_optik.cart_items: ~25 rows (approximately)
INSERT INTO `cart_items` (`cart_item_id`, `cart_id`, `product_id`, `variant_id`, `quantity`, `price`, `created_at`, `updated_at`, `deleted_at`) VALUES
	('05101213-caac-4a84-bffb-ce33e144d770', '491abc4b-a607-4512-83ef-0133e4a767d6', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', 'e9f2f55c-81a2-418c-b85d-6e2e4e3c0e61', 1, 1200000.00, '2026-03-06 11:06:03', '2026-03-06 11:07:07', '2026-03-06 11:07:07'),
	('15ea098c-bec8-48cd-a2f5-0a49f1170d6f', '6af4a270-82a0-406f-8d5c-92a794eb3de3', '95f03222-664a-4a4c-8dc4-ac006464bbf5', NULL, 1, 1000000.00, '2026-01-12 06:56:39', '2026-01-12 06:56:45', '2026-01-12 06:56:45'),
	('179d3eec-6c5f-40b2-ae72-0b2a426435c5', '491abc4b-a607-4512-83ef-0133e4a767d6', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', '229eff63-140c-4ceb-9d5c-76ed55953372', 1, 1500000.00, '2026-03-06 15:08:29', '2026-03-06 15:08:29', NULL),
	('2a805a38-81c5-417c-aaa0-64199b2d1f89', '491abc4b-a607-4512-83ef-0133e4a767d6', '95f03222-664a-4a4c-8dc4-ac006464bbf5', '37feb422-6150-4909-9384-5c632bdd417f', 1, 1200000.00, '2026-03-06 14:20:52', '2026-03-06 14:21:39', '2026-03-06 14:21:39'),
	('38e047a3-8488-495e-b3af-adb217dd833c', '6af4a270-82a0-406f-8d5c-92a794eb3de3', '95f03222-664a-4a4c-8dc4-ac006464bbf5', NULL, 1, 1000000.00, '2026-03-05 11:33:38', '2026-03-05 11:33:38', NULL),
	('57987f02-3463-457b-8035-76956872f49a', '6af4a270-82a0-406f-8d5c-92a794eb3de3', '95f03222-664a-4a4c-8dc4-ac006464bbf5', NULL, 1, 1000000.00, '2026-01-14 06:56:56', '2026-01-19 09:43:43', '2026-01-19 09:43:43'),
	('602b6c99-4c0a-4efb-bc4e-0cfc816b4a38', '6af4a270-82a0-406f-8d5c-92a794eb3de3', '95f03222-664a-4a4c-8dc4-ac006464bbf5', NULL, 1, 1000000.00, '2026-03-05 10:53:01', '2026-03-05 10:54:36', '2026-03-05 10:54:36'),
	('63a33e58-8b72-4541-add2-f554b305cfa1', 'a0acd4e4-bcf3-4e33-a4f7-c8d7589f68fd', '95f03222-664a-4a4c-8dc4-ac006464bbf5', NULL, 1, 1000000.00, '2026-02-03 16:13:49', '2026-02-03 16:20:41', '2026-02-03 16:20:41'),
	('6898ac0e-ab89-419c-a41c-7f15ff9b0f19', 'a0acd4e4-bcf3-4e33-a4f7-c8d7589f68fd', '95f03222-664a-4a4c-8dc4-ac006464bbf5', 'd767a352-7841-4ae0-8326-1392efedd61b', 1, 1300000.00, '2026-01-13 02:51:38', '2026-01-13 02:56:03', '2026-01-13 02:56:03'),
	('6e102d7d-2417-4ed8-86e2-da3345f9673d', '6af4a270-82a0-406f-8d5c-92a794eb3de3', '95f03222-664a-4a4c-8dc4-ac006464bbf5', 'd767a352-7841-4ae0-8326-1392efedd61b', 1, 1300000.00, '2026-01-12 06:56:35', '2026-01-12 06:56:45', '2026-01-12 06:56:45'),
	('6f46034c-68f4-49ad-839b-e8bd1e9bdcef', '6af4a270-82a0-406f-8d5c-92a794eb3de3', 'c0e93d11-b193-4355-bc9a-6d00d0492fb4', NULL, 1, 620000.00, '2026-01-21 13:20:31', '2026-01-21 13:20:51', '2026-01-21 13:20:51'),
	('72db99fe-6db2-4e7c-8a26-8998803dbd8b', '6af4a270-82a0-406f-8d5c-92a794eb3de3', '95f03222-664a-4a4c-8dc4-ac006464bbf5', 'd767a352-7841-4ae0-8326-1392efedd61b', 1, 1300000.00, '2026-01-09 03:48:53', '2026-01-09 03:49:11', '2026-01-09 03:49:11'),
	('7c9909df-fba4-4ce3-94c8-42b0f73f6e6b', '6af4a270-82a0-406f-8d5c-92a794eb3de3', '95f03222-664a-4a4c-8dc4-ac006464bbf5', 'd767a352-7841-4ae0-8326-1392efedd61b', 1, 1300000.00, '2026-01-12 10:19:10', '2026-01-12 10:19:26', '2026-01-12 10:19:26'),
	('7ce1ee2b-a74d-466e-9ba5-2465a79111e1', '6af4a270-82a0-406f-8d5c-92a794eb3de3', '95f03222-664a-4a4c-8dc4-ac006464bbf5', '37feb422-6150-4909-9384-5c632bdd417f', 1, 1200000.00, '2026-01-14 06:57:47', '2026-01-19 09:37:11', '2026-01-19 09:37:11'),
	('83b65d85-ff95-4b5c-bcd3-c1ba481090d0', '491abc4b-a607-4512-83ef-0133e4a767d6', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', 'e9f2f55c-81a2-418c-b85d-6e2e4e3c0e61', 1, 1200000.00, '2026-03-06 11:08:09', '2026-03-06 11:10:20', '2026-03-06 11:10:20'),
	('8cc04d61-e67a-420d-8c59-39f6374d3d42', 'a0acd4e4-bcf3-4e33-a4f7-c8d7589f68fd', '95f03222-664a-4a4c-8dc4-ac006464bbf5', '37feb422-6150-4909-9384-5c632bdd417f', 1, 1200000.00, '2026-01-13 02:51:29', '2026-01-13 02:56:03', '2026-01-13 02:56:03'),
	('9ec47616-52da-4874-b0fc-a8c49c11790c', '491abc4b-a607-4512-83ef-0133e4a767d6', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', '229eff63-140c-4ceb-9d5c-76ed55953372', 1, 1500000.00, '2026-03-06 11:06:41', '2026-03-06 11:10:20', '2026-03-06 11:10:20'),
	('a5ceb433-a630-4978-92ca-8aca686d64f0', 'a0acd4e4-bcf3-4e33-a4f7-c8d7589f68fd', 'c0e93d11-b193-4355-bc9a-6d00d0492fb4', NULL, 1, 620000.00, '2026-02-03 16:14:01', '2026-02-03 16:20:41', '2026-02-03 16:20:41'),
	('ac448742-39b9-4424-ac85-0e5edc4620d1', '6af4a270-82a0-406f-8d5c-92a794eb3de3', '95f03222-664a-4a4c-8dc4-ac006464bbf5', '37feb422-6150-4909-9384-5c632bdd417f', 1, 1200000.00, '2026-01-12 04:09:47', '2026-01-12 04:10:13', '2026-01-12 04:10:13'),
	('adf7e3d3-2f26-4c6a-9ce0-01fa62020430', '397ab3ad-e99c-41f5-81b2-c5863108b13f', 'c0e93d11-b193-4355-bc9a-6d00d0492fb4', NULL, 1, 620000.00, '2026-02-03 16:49:38', '2026-02-03 16:50:24', '2026-02-03 16:50:24'),
	('b0aee4f5-eb6d-47be-8f08-b342375f2cda', '6af4a270-82a0-406f-8d5c-92a794eb3de3', '95f03222-664a-4a4c-8dc4-ac006464bbf5', 'd767a352-7841-4ae0-8326-1392efedd61b', 1, 1300000.00, '2026-01-09 04:07:58', '2026-01-09 04:08:16', '2026-01-09 04:08:16'),
	('c737dd64-0f45-4e50-8931-9728d0a88ba8', '491abc4b-a607-4512-83ef-0133e4a767d6', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', '229eff63-140c-4ceb-9d5c-76ed55953372', 1, 1500000.00, '2026-03-06 11:05:42', '2026-03-06 11:06:27', '2026-03-06 11:06:27'),
	('c986d63b-16ca-4f33-af5c-e419cca68ff1', '397ab3ad-e99c-41f5-81b2-c5863108b13f', '95f03222-664a-4a4c-8dc4-ac006464bbf5', '37feb422-6150-4909-9384-5c632bdd417f', 1, 1200000.00, '2026-02-03 16:49:33', '2026-02-03 16:50:24', '2026-02-03 16:50:24'),
	('cf049ecd-9b79-4929-b714-4b6290b9b214', '6af4a270-82a0-406f-8d5c-92a794eb3de3', '95f03222-664a-4a4c-8dc4-ac006464bbf5', '37feb422-6150-4909-9384-5c632bdd417f', 1, 1200000.00, '2026-01-09 04:07:50', '2026-01-09 04:08:16', '2026-01-09 04:08:16'),
	('d463b45a-5d12-4026-a5f4-d570fe8953da', '491abc4b-a607-4512-83ef-0133e4a767d6', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', 'e9f2f55c-81a2-418c-b85d-6e2e4e3c0e61', 1, 1200000.00, '2026-03-06 11:07:18', '2026-03-06 11:07:58', '2026-03-06 11:07:58'),
	('e1294ff8-9325-4539-b059-cbd3afe7f92f', '6af4a270-82a0-406f-8d5c-92a794eb3de3', '95f03222-664a-4a4c-8dc4-ac006464bbf5', '37feb422-6150-4909-9384-5c632bdd417f', 1, 1200000.00, '2026-01-23 14:14:54', '2026-01-23 14:15:22', '2026-01-23 14:15:22'),
	('e3935d3d-bd70-4555-9047-004d63a8f721', '6af4a270-82a0-406f-8d5c-92a794eb3de3', '95f03222-664a-4a4c-8dc4-ac006464bbf5', '37feb422-6150-4909-9384-5c632bdd417f', 1, 1200000.00, '2026-01-09 03:48:47', '2026-01-09 03:49:11', '2026-01-09 03:49:11');

-- Dumping structure for table pos_optik.cart_item_prescriptions
CREATE TABLE IF NOT EXISTS `cart_item_prescriptions` (
  `prescription_id` char(36) NOT NULL,
  `cart_item_id` char(36) NOT NULL,
  `right_sph` decimal(4,2) DEFAULT NULL,
  `right_cyl` decimal(4,2) DEFAULT NULL,
  `right_axis` int DEFAULT NULL,
  `right_add` decimal(4,2) DEFAULT NULL,
  `left_sph` decimal(4,2) DEFAULT NULL,
  `left_cyl` decimal(4,2) DEFAULT NULL,
  `left_axis` int DEFAULT NULL,
  `left_add` decimal(4,2) DEFAULT NULL,
  `pd_single` decimal(4,1) DEFAULT NULL,
  `pd_left` decimal(4,1) DEFAULT NULL,
  `pd_right` decimal(4,1) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`prescription_id`),
  KEY `cart_item_prescriptions_cart_item_id_foreign` (`cart_item_id`),
  CONSTRAINT `cart_item_prescriptions_cart_item_id_foreign` FOREIGN KEY (`cart_item_id`) REFERENCES `cart_items` (`cart_item_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table pos_optik.cart_item_prescriptions: ~2 rows (approximately)
INSERT INTO `cart_item_prescriptions` (`prescription_id`, `cart_item_id`, `right_sph`, `right_cyl`, `right_axis`, `right_add`, `left_sph`, `left_cyl`, `left_axis`, `left_add`, `pd_single`, `pd_left`, `pd_right`, `created_at`, `updated_at`) VALUES
	('04631149-4b83-4975-a14d-62035f14fde8', '2a805a38-81c5-417c-aaa0-64199b2d1f89', 1.00, 2.00, 1, NULL, 1.00, 1.00, 1, NULL, NULL, 1.0, 1.0, '2026-03-06 14:20:52', '2026-03-06 14:20:52'),
	('07e76ce9-9fd7-4a9d-82b4-ac1f9d79acb4', 'e3935d3d-bd70-4555-9047-004d63a8f721', 1.00, 1.00, 1, NULL, 1.00, 1.00, 1, NULL, NULL, 1.0, 1.0, '2026-01-09 03:48:47', '2026-01-09 03:48:47'),
	('7e84e4fd-f23d-4c49-89c8-7fb204e5f5be', 'e1294ff8-9325-4539-b059-cbd3afe7f92f', 1.00, 1.00, 1, NULL, 1.00, 1.00, 1, NULL, NULL, 1.0, 1.0, '2026-01-23 14:14:54', '2026-01-23 14:14:54'),
	('9fb6206c-fc37-4abc-985b-f609df6de9d0', 'b0aee4f5-eb6d-47be-8f08-b342375f2cda', 1.00, 1.00, 2, NULL, 1.00, 2.00, 3, NULL, NULL, 4.0, 1.0, '2026-01-09 04:07:58', '2026-01-09 04:07:58'),
	('c568e782-9969-4d96-8e2c-582eb8761fbb', '6898ac0e-ab89-419c-a41c-7f15ff9b0f19', 1.00, 1.00, 1, NULL, 1.00, 1.00, 1, NULL, NULL, 1.0, 1.0, '2026-01-13 02:51:38', '2026-01-13 02:51:38'),
	('ea6ddc90-64c4-4267-ae3f-9462fb98fa27', '83b65d85-ff95-4b5c-bcd3-c1ba481090d0', 1.00, 2.00, 1, NULL, 1.00, 1.00, 1, NULL, NULL, 1.0, 1.0, '2026-03-06 11:08:09', '2026-03-06 11:08:09'),
	('ef0f3f64-b280-461f-aba9-e8805c96e894', 'ac448742-39b9-4424-ac85-0e5edc4620d1', 1.00, 1.00, 1, NULL, 1.00, 1.00, 1, NULL, NULL, 1.0, 1.0, '2026-01-12 04:09:47', '2026-01-12 04:09:47');

-- Dumping structure for table pos_optik.coupons
CREATE TABLE IF NOT EXISTS `coupons` (
  `coupon_id` char(36) NOT NULL,
  `code` varchar(50) NOT NULL,
  `description` text,
  `discount_type` varchar(20) NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `min_order_amount` decimal(10,2) DEFAULT NULL,
  `max_discount` decimal(10,2) DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `usage_limit` int DEFAULT NULL,
  `per_user_limit` int DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`coupon_id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table pos_optik.coupons: ~0 rows (approximately)

-- Dumping structure for table pos_optik.customers
CREATE TABLE IF NOT EXISTS `customers` (
  `customer_id` char(36) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(100) NOT NULL,
  `customer_password` varchar(255) NOT NULL,
  `customer_phone` varchar(20) NOT NULL,
  `customer_dob` date DEFAULT NULL,
  `customer_gender` enum('male','female','other') NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`customer_id`),
  UNIQUE KEY `customer_email` (`customer_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table pos_optik.customers: ~20 rows (approximately)
INSERT INTO `customers` (`customer_id`, `customer_name`, `customer_email`, `customer_password`, `customer_phone`, `customer_dob`, `customer_gender`, `created_at`, `updated_at`, `deleted_at`) VALUES
	('025f77f1-dbb3-49d8-bb35-01e908a8e181', 'Fitria Zulaika', 'adis@gmail.com', '$2y$10$j8kZdIrzshpZ33FCtabSk.2miWzHrFFKz9fSm3jXGDvVO/41M96ya', '(+62) 20 6931 7066', '1977-08-10', 'male', '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL),
	('04c82b5b-ea89-4a15-9fa8-8deec59610b2', 'Novi Anggraini', 'zulaikha.samosir@nuraini.biz.id', '$2y$10$9FDa4/V0UMNaH/NJcB/EW.GCfHqEeXpuWcgNuX4BXvA8OkL633qkG', '0845 1199 1445', '1994-07-21', 'female', '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL),
	('08b1939f-24fa-4a8f-8f48-4f1a9ee7eb5b', 'Nadine Kuswandari', 'nurdiyanti.empluk@gmail.com', '$2y$10$z2rlZbSpQw6obErrh9Nc.uwx/0HFIOwnTm//X0OTYZGIP3eyJ6qC6', '(+62) 721 7997 5029', '1995-12-18', 'female', '2025-12-31 03:41:33', '2025-12-31 03:41:33', NULL),
	('091b8d94-d266-49dd-bcb6-5f896b990dd9', 'Kayun Situmorang', 'ami.usada@yahoo.co.id', '$2y$10$n/xMRbhAqwMBbgr/dkZ6vuMYNbhjxKIyRC4HIEi5rULb.0IvMC3c6', '(+62) 613 6914 354', '1972-09-24', 'female', '2025-12-31 03:41:33', '2025-12-31 03:41:33', NULL),
	('0f8121bb-e3b7-4437-9c3f-6e589df3b3a1', 'Cawisono Dagel Siregar', 'mulyani.galih@laksmiwati.desa.id', '$2y$10$GRKqomVXg6SlnnPu/YR5ieZmD2MrAVoArco1j2BhNwzqzr/cmRKqy', '0516 4710 721', '1961-10-08', 'male', '2025-12-31 03:41:33', '2025-12-31 03:41:33', NULL),
	('17f27383-11c0-4a1d-84a6-49c1af625b2b', 'Ghaliyati Laksmiwati', 'diana30@yahoo.co.id', '$2y$10$n9RwlrdLCFSBPLUNRg7o0OHmxiwSt9t7T.GqXtY6alogTrTUo/LJ6', '(+62) 990 6551 856', '1998-01-14', 'female', '2025-12-31 03:41:33', '2025-12-31 03:41:33', NULL),
	('3cb8e173-3916-4ab2-87d0-d928c3b58310', 'Puspa Wulandari', 'hprakasa@hassanah.ac.id', '$2y$10$7apfRfTxABnxuw6IU5pS.OWAVxUN3Wvk9DCwozefPg6ZrjJmlKXk.', '0476 6618 2347', '1989-11-16', 'male', '2025-12-31 03:41:33', '2025-12-31 03:41:33', NULL),
	('41b18f0b-30bd-4ac7-9772-fe0642a45951', 'Hasna Lailasari', 'umansur@gmail.co.id', '$2y$10$9L6k.kzhOJvEuALjmJN1wOSsHiy/Sen3st5zTPBtIxcYkUjGJQSk2', '0986 0643 7786', '1961-07-22', 'female', '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL),
	('4f67c971-ff30-4379-8ee7-59bb6f5b7804', 'Dodo Iswahyudi S.Kom', 'sinaga.ismail@wasita.org', '$2y$10$3Mwo9V9JLbG5UjP4uFPSFOCUhjjtH1KTVvPhsKeFiA0cdFYAOJ3KC', '0723 3002 150', '1986-06-20', 'male', '2025-12-31 03:41:33', '2025-12-31 03:41:33', NULL),
	('7581963a-6c05-4e9d-a714-b849e71ef254', 'Mala Novitasari M.TI.', 'puspa.puspita@rahayu.web.id', '$2y$10$07WYQYdlZrm/5zfrLUSKdeheg2IEFszrFvCT2lXbOmO90gHCwFF.K', '(+62) 889 320 811', '1962-10-11', 'female', '2025-12-31 03:41:33', '2025-12-31 03:41:33', NULL),
	('776e4afc-284f-4e88-b853-4be49ccd277e', 'Puput Yuliarti S.IP', 'julia90@pertiwi.my.id', '$2y$10$1Vf0dBpUqvFQ/lawLwikgOQ034Ay3knKAwWK/b65jSKBTCE3wijj.', '(+62) 803 9578 448', '1966-12-02', 'male', '2025-12-31 03:41:33', '2025-12-31 03:41:33', NULL),
	('85d53c25-a7ad-445b-9b62-65ea3b929b9f', 'Kalim Balamantri Mahendra', 'iswahyudi.farhunnisa@safitri.tv', '$2y$10$/H9aI4n9DwCRFYM2TudFzuEJosdLLywlJnZj4gOtCDukruTWrZRXi', '0684 6991 285', '1994-06-16', 'female', '2025-12-31 03:41:33', '2025-12-31 03:41:33', NULL),
	('880eafbd-ff62-4d4f-86cf-184d34400708', 'Putri Sabrina Lailasari', 'saragih.oni@haryanti.com', '$2y$10$Fe3IBbpTOWPtsg6Vl/TKVu.mBYN1oUArNZz8EKzHKYFaNVdQUmqbq', '0759 2213 648', '2001-06-14', 'female', '2025-12-31 03:41:33', '2025-12-31 03:41:33', NULL),
	('9ce40811-2b26-4e96-8783-04024bff98d8', 'Safina Handayani', 'harsanto36@gmail.co.id', '$2y$10$oy7vKtU91Esoa6h.dc59gOeML0JQf2fKPuDYuXHv3t0zri07XQJjK', '0516 3547 585', '1972-08-03', 'male', '2025-12-31 03:41:33', '2025-12-31 03:41:33', NULL),
	('a523de15-cdb3-4d4c-89e6-2f6aac95286d', 'Lamar Hamzah Maulana S.Kom', 'karta68@yuliarti.mil.id', '$2y$10$9ISrE10rS6KKHowojPQD5.UEHBBczVBhRjvZqMJNp8KU6aGT3kU4y', '0505 8689 352', '2007-11-28', 'male', '2025-12-31 03:41:33', '2025-12-31 03:41:33', NULL),
	('b1a462a9-18e1-462f-a8e2-4cf7bab7c6a0', 'Tedi Gamani Widodo', 'situmorang.azalea@najmudin.biz.id', '$2y$10$Lvf0nLipn1cJBy3ghoQcZ.hMqiMnLMkdx/lhlZlW52L6PUk9phvvq', '0257 1052 9641', '1995-03-24', 'female', '2025-12-31 03:41:33', '2025-12-31 03:41:33', NULL),
	('c5cd2dc5-00b2-4d45-8e59-6f75d4b0ae95', 'Tasdik Dabukke S.Kom', 'yprayoga@mangunsong.ac.id', '$2y$10$vP/Px.tJDMc4FVQpSnRVxeAkcT2lA0lq/Z79GLnDLcBcjH0eAdCjq', '(+62) 26 4276 9240', '1961-08-07', 'female', '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL),
	('c88af1e9-b882-4597-82ca-d414942926e0', 'Imam Kadir Mustofa', 'ika52@gmail.co.id', '$2y$10$uJVfvCZP/abLtqsFQ3oy4Oi6XZ//llqnykPidCpwxM7uFSJ4yAiWq', '(+62) 733 2961 930', '1970-06-03', 'male', '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL),
	('e20dd7c1-9731-4bcd-b574-9169111538ac', 'Jatmiko Wacana', 'tampubolon.harja@hutapea.in', '$2y$10$ky4nKQ01qsppBG1sIEqI6.prcOtglRH5xTDU9j0LuQDLB6ZLtTahW', '0935 0525 235', '1968-01-03', 'female', '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL),
	('fd9caba4-4803-4bca-b9ef-409b03298384', 'Dalima Uyainah', 'yahya.mandasari@nababan.tv', '$2y$10$W0zx8kr8e.89/pz08Qp8YOBhzSJK85/7pW7wVyXd.HM2a92x1TJES', '0925 5172 832', '1978-01-14', 'male', '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL);

-- Dumping structure for table pos_optik.customer_shipping_addresses
CREATE TABLE IF NOT EXISTS `customer_shipping_addresses` (
  `csa_id` char(36) NOT NULL,
  `customer_id` char(36) NOT NULL,
  `recipient_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `city` varchar(100) NOT NULL,
  `province` varchar(100) NOT NULL,
  `postal_code` varchar(10) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`csa_id`),
  KEY `customer_shipping_addresses_customer_id_foreign` (`customer_id`),
  CONSTRAINT `customer_shipping_addresses_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table pos_optik.customer_shipping_addresses: ~0 rows (approximately)
INSERT INTO `customer_shipping_addresses` (`csa_id`, `customer_id`, `recipient_name`, `phone`, `address`, `city`, `province`, `postal_code`, `created_at`, `updated_at`, `deleted_at`) VALUES
	('012b9bbc-f08f-4a17-9821-dd655a0bff0a', 'c88af1e9-b882-4597-82ca-d414942926e0', 'Imam Kadir Mustofa', '08192891829833', 'Kpg. Baranang No. 630', 'Batu', 'Lampung', '58427', '2026-03-06 11:10:05', '2026-03-06 11:10:05', NULL),
	('59362567-b780-4fd8-a617-185c91cc6e89', '17f27383-11c0-4a1d-84a6-49c1af625b2b', 'Bagus Mulya', '08198293839', 'Tebet Barat Dalam XE No.10', 'Jakarta Pusat', 'DKI Jakarta', '12810', '2026-02-03 16:50:17', '2026-02-03 16:50:17', NULL),
	('9ef4a650-a4ac-4169-bbaa-8513a08bed9d', '04c82b5b-ea89-4a15-9fa8-8deec59610b2', 'Dystian En Yusgiantoro', '0819281992819', 'Jl. Kh. Abdul Rochim No.1, RT.9/RW.1, Kuningan Bar., Kec. Mampang Prpt., Kota Jakarta Selatan, Daerah Khusus Ibukota Jakarta 12710', 'Jakarta Selatan', 'Daerah Khusus Ibukota Jakarta', '58427', '2025-12-31 04:30:14', '2026-01-13 02:55:52', NULL),
	('d6d4f463-7dd0-4c54-80f4-a67759b76427', '025f77f1-dbb3-49d8-bb35-01e908a8e181', 'Dystian En Yusgiantoro', '081928938932812', 'Kpg. Baranang No. 630', 'Batu', 'Lampung', '58427', '2026-01-19 09:38:56', '2026-01-19 09:38:56', NULL);

-- Dumping structure for table pos_optik.eye_examinations
CREATE TABLE IF NOT EXISTS `eye_examinations` (
  `eye_examination_id` char(36) NOT NULL,
  `customer_id` char(36) NOT NULL,
  `left_eye_sphere` float DEFAULT NULL,
  `left_eye_cylinder` float DEFAULT NULL,
  `left_eye_axis` float DEFAULT NULL,
  `right_eye_sphere` float DEFAULT NULL,
  `right_eye_cylinder` float DEFAULT NULL,
  `right_eye_axis` float DEFAULT NULL,
  `symptoms` text,
  `diagnosis` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`eye_examination_id`),
  KEY `eye_examinations_customer_id_foreign` (`customer_id`),
  CONSTRAINT `eye_examinations_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table pos_optik.eye_examinations: ~0 rows (approximately)
INSERT INTO `eye_examinations` (`eye_examination_id`, `customer_id`, `left_eye_sphere`, `left_eye_cylinder`, `left_eye_axis`, `right_eye_sphere`, `right_eye_cylinder`, `right_eye_axis`, `symptoms`, `diagnosis`, `created_at`, `updated_at`, `deleted_at`) VALUES
	('5744dc11-a057-40f3-a6e6-b1040a3d5361', '025f77f1-dbb3-49d8-bb35-01e908a8e181', 0, 1, 1, 2, 1, 1, '1', '2', '2026-01-13 07:36:28', '2026-01-13 07:36:28', NULL);

-- Dumping structure for table pos_optik.inventory_transactions
CREATE TABLE IF NOT EXISTS `inventory_transactions` (
  `inventory_transaction_id` char(36) NOT NULL,
  `user_id` char(36) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `variant_id` char(36) NOT NULL,
  `product_id` char(36) NOT NULL,
  `transaction_type` enum('in','out') NOT NULL,
  `reference_type` enum('order','adjustment','return','transfer','initial') DEFAULT NULL,
  `reference_id` char(36) DEFAULT NULL,
  `quantity` int unsigned NOT NULL,
  `transaction_date` datetime NOT NULL,
  `description` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`inventory_transaction_id`),
  KEY `inventory_transactions_product_id_foreign` (`product_id`),
  KEY `inventory_transactions_variant_id_foreign` (`variant_id`),
  KEY `inventory_transactions_user_id_foreign` (`user_id`),
  KEY `idx_inventory_reference` (`reference_type`,`reference_id`),
  CONSTRAINT `inventory_transactions_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `inventory_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `inventory_transactions_variant_id_foreign` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`variant_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table pos_optik.inventory_transactions: ~14 rows (approximately)
INSERT INTO `inventory_transactions` (`inventory_transaction_id`, `user_id`, `variant_id`, `product_id`, `transaction_type`, `reference_type`, `reference_id`, `quantity`, `transaction_date`, `description`, `created_at`, `updated_at`, `deleted_at`) VALUES
	('39972b2c-6870-4c3b-90b9-a8de1aa08848', 'bcd30656-71bc-4553-a2d5-2c72c2299ef4', '37feb422-6150-4909-9384-5c632bdd417f', '95f03222-664a-4a4c-8dc4-ac006464bbf5', 'out', 'order', 'fa59964a-e5b7-4b4d-b3c4-bb058cfa703d', 1, '2026-01-12 04:58:41', 'Order payment approved', '2026-01-12 04:10:40', '2026-01-12 04:58:41', NULL),
	('436e7611-f1ff-495f-ba12-c0e6abbe5588', 'bcd30656-71bc-4553-a2d5-2c72c2299ef4', 'd767a352-7841-4ae0-8326-1392efedd61b', '95f03222-664a-4a4c-8dc4-ac006464bbf5', 'out', 'order', '9e4477ed-f16a-4602-ab61-7b2edfbe695d', 1, '2026-01-13 02:59:17', 'Order payment approved', '2026-01-13 02:59:17', '2026-01-13 02:59:17', NULL),
	('4ccf1f73-d5c4-4365-9398-9c1d1dbffb8e', 'bcd30656-71bc-4553-a2d5-2c72c2299ef4', '37feb422-6150-4909-9384-5c632bdd417f', '95f03222-664a-4a4c-8dc4-ac006464bbf5', 'in', 'order', '', 100, '2026-01-23 08:57:24', 'Masuk', '2026-01-23 08:57:24', '2026-01-23 08:57:24', NULL),
	('64306eb2-f209-4984-9701-6b02c5b63499', 'bcd30656-71bc-4553-a2d5-2c72c2299ef4', 'e9f2f55c-81a2-418c-b85d-6e2e4e3c0e61', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', 'in', 'initial', '', 50, '2026-03-06 08:39:52', '', '2026-03-06 08:39:52', '2026-03-06 08:39:52', NULL),
	('6ad30b6a-4e20-4a6f-b401-94c13831bbd0', 'bcd30656-71bc-4553-a2d5-2c72c2299ef4', '37feb422-6150-4909-9384-5c632bdd417f', '95f03222-664a-4a4c-8dc4-ac006464bbf5', 'out', 'order', '93f984e1-6a91-4f17-959e-cd0ae8a1816d', 1, '2026-03-06 14:22:01', 'Order payment approved', '2026-03-06 14:22:01', '2026-03-06 14:22:01', NULL),
	('6d8db871-721c-4bc1-bec8-035f56b04017', 'bcd30656-71bc-4553-a2d5-2c72c2299ef4', 'e9f2f55c-81a2-418c-b85d-6e2e4e3c0e61', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', 'out', 'order', '3493a45a-826b-4918-afd0-e50d59951809', 1, '2026-03-06 11:10:58', 'Order payment approved', '2026-03-06 11:10:58', '2026-03-06 11:10:58', NULL),
	('820b4733-cc40-4d69-b737-39e5d559b11b', 'bcd30656-71bc-4553-a2d5-2c72c2299ef4', 'd767a352-7841-4ae0-8326-1392efedd61b', '95f03222-664a-4a4c-8dc4-ac006464bbf5', 'in', NULL, NULL, 20, '2026-01-09 03:48:39', 'Masuk', '2026-01-09 03:48:39', '2026-01-09 03:48:39', NULL),
	('8ec793f9-b256-4b2d-afcb-5e60c1a3a3e5', 'bcd30656-71bc-4553-a2d5-2c72c2299ef4', 'd767a352-7841-4ae0-8326-1392efedd61b', '95f03222-664a-4a4c-8dc4-ac006464bbf5', 'out', 'order', '6d60a746-751f-4ceb-97f0-e5f7a03f9e0c', 1, '2026-01-13 02:53:56', 'Order payment approved', '2026-01-13 02:53:56', '2026-01-13 02:53:56', NULL),
	('976d4684-908d-4526-b14a-9b5337c5419a', 'bcd30656-71bc-4553-a2d5-2c72c2299ef4', '37feb422-6150-4909-9384-5c632bdd417f', '95f03222-664a-4a4c-8dc4-ac006464bbf5', 'out', 'order', 'fefe6693-ab41-4540-85e2-47953e0a9ca4', 1, '2026-01-23 14:17:45', 'Order payment approved', '2026-01-23 14:17:45', '2026-01-23 14:17:45', NULL),
	('997403d1-af7e-420a-987a-006165c6f256', 'bcd30656-71bc-4553-a2d5-2c72c2299ef4', 'd767a352-7841-4ae0-8326-1392efedd61b', '95f03222-664a-4a4c-8dc4-ac006464bbf5', 'out', 'order', 'cacba380-b39e-41a7-b535-a9040778ec5a', 1, '2026-01-12 08:03:10', 'Order payment approved', '2026-01-12 08:03:10', '2026-01-12 08:03:10', NULL),
	('9b338948-2fc1-407a-850d-77c9b53316bc', 'bcd30656-71bc-4553-a2d5-2c72c2299ef4', '37feb422-6150-4909-9384-5c632bdd417f', '95f03222-664a-4a4c-8dc4-ac006464bbf5', 'in', 'adjustment', '', 1, '2026-01-13 09:06:35', 'Masuk', '2026-01-13 09:06:35', '2026-01-13 09:06:35', NULL),
	('b805c00b-605c-4728-b7da-378413fc681c', 'bcd30656-71bc-4553-a2d5-2c72c2299ef4', '229eff63-140c-4ceb-9d5c-76ed55953372', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', 'out', 'order', '3493a45a-826b-4918-afd0-e50d59951809', 1, '2026-03-06 11:10:58', 'Order payment approved', '2026-03-06 11:10:58', '2026-03-06 11:10:58', NULL),
	('d9bf4b74-5e5d-48ee-a526-09cd44c1d9c2', 'bcd30656-71bc-4553-a2d5-2c72c2299ef4', '37feb422-6150-4909-9384-5c632bdd417f', '95f03222-664a-4a4c-8dc4-ac006464bbf5', 'out', 'order', 'e3d118a5-9b0e-43d5-a9ad-20454a75709d', 1, '2026-02-03 16:50:58', 'Order payment approved', '2026-02-03 16:50:58', '2026-02-03 16:50:58', NULL),
	('e6022a09-7346-486b-a422-12a227711a0c', 'bcd30656-71bc-4553-a2d5-2c72c2299ef4', '229eff63-140c-4ceb-9d5c-76ed55953372', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', 'in', 'initial', '', 100, '2026-03-06 08:39:35', 'Barang Masuk', '2026-03-06 08:39:35', '2026-03-06 08:39:35', NULL),
	('e6dd4345-690d-473a-9c99-876abf664bd0', 'bcd30656-71bc-4553-a2d5-2c72c2299ef4', '37feb422-6150-4909-9384-5c632bdd417f', '95f03222-664a-4a4c-8dc4-ac006464bbf5', 'out', 'order', '9e4477ed-f16a-4602-ab61-7b2edfbe695d', 1, '2026-01-13 02:59:17', 'Order payment approved', '2026-01-13 02:59:17', '2026-01-13 02:59:17', NULL),
	('ebc2c294-0efc-4592-aed7-ba53d368a699', 'bcd30656-71bc-4553-a2d5-2c72c2299ef4', '37feb422-6150-4909-9384-5c632bdd417f', '95f03222-664a-4a4c-8dc4-ac006464bbf5', 'in', NULL, NULL, 10, '2026-01-09 03:48:28', 'Barang Masuk', '2026-01-09 03:48:28', '2026-01-09 03:48:28', NULL);

-- Dumping structure for table pos_optik.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int NOT NULL,
  `batch` int unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb3;

-- Dumping data for table pos_optik.migrations: ~37 rows (approximately)
INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
	(1, '2025-06-02-019900', 'App\\Database\\Migrations\\CreateRolesTable', 'default', 'App', 1767152411, 1),
	(2, '2025-06-02-020018', 'App\\Database\\Migrations\\CreateCustomersTable', 'default', 'App', 1767152411, 1),
	(3, '2025-06-02-020033', 'App\\Database\\Migrations\\CreateProductCategoriesTable', 'default', 'App', 1767152411, 1),
	(4, '2025-06-02-020045', 'App\\Database\\Migrations\\CreateProductsTable', 'default', 'App', 1767152411, 1),
	(5, '2025-06-02-020050', 'App\\Database\\Migrations\\CreateOrderStatuses', 'default', 'App', 1767152411, 1),
	(6, '2025-06-02-020040', 'App\\Database\\Migrations\\CreateShippingMethods', 'default', 'App', 1767152481, 2),
	(7, '2025-06-02-020056', 'App\\Database\\Migrations\\CreateOrdersTable', 'default', 'App', 1767152482, 2),
	(8, '2025-06-02-020105', 'App\\Database\\Migrations\\CreateProductVariants', 'default', 'App', 1767152482, 2),
	(9, '2025-06-02-020110', 'App\\Database\\Migrations\\CreateOrderItemsTable', 'default', 'App', 1767152482, 2),
	(10, '2025-06-02-020135', 'App\\Database\\Migrations\\CreateEyeExaminationsTable', 'default', 'App', 1767152482, 2),
	(11, '2025-06-02-020150', 'App\\Database\\Migrations\\CreateReviews', 'default', 'App', 1767152482, 2),
	(12, '2025-06-02-032255', 'App\\Database\\Migrations\\CreateUsers', 'default', 'App', 1767152482, 2),
	(13, '2025-11-19-081701', 'App\\Database\\Migrations\\ProductAttributes', 'default', 'App', 1767152482, 2),
	(14, '2025-11-19-081806', 'App\\Database\\Migrations\\ProductAttributeValues', 'default', 'App', 1767152482, 2),
	(15, '2025-11-19-082013', 'App\\Database\\Migrations\\CreateProductImages', 'default', 'App', 1767152482, 2),
	(16, '2025-11-19-082035', 'App\\Database\\Migrations\\CreateProductVariantImages', 'default', 'App', 1767152482, 2),
	(17, '2025-11-19-082103', 'App\\Database\\Migrations\\CreateCarts', 'default', 'App', 1767152482, 2),
	(18, '2025-11-19-082134', 'App\\Database\\Migrations\\CreateCartItems', 'default', 'App', 1767152482, 2),
	(19, '2025-11-19-082817', 'App\\Database\\Migrations\\CreatePaymentMethods', 'default', 'App', 1767152482, 2),
	(20, '2025-11-19-082829', 'App\\Database\\Migrations\\CreatePayments', 'default', 'App', 1767152482, 2),
	(21, '2025-11-19-082936', 'App\\Database\\Migrations\\CreateShippingRates', 'default', 'App', 1767152482, 2),
	(22, '2025-11-19-083138', 'App\\Database\\Migrations\\CreateProductDiscounts', 'default', 'App', 1767152482, 2),
	(24, '2025-11-19-083226', 'App\\Database\\Migrations\\CreateOrderCoupons', 'default', 'App', 1767152482, 2),
	(25, '2025-11-19-083245', 'App\\Database\\Migrations\\CreateWishlists', 'default', 'App', 1767152482, 2),
	(26, '2025-11-19-083323', 'App\\Database\\Migrations\\CreateUserActivities', 'default', 'App', 1767152482, 2),
	(27, '2025-11-25-044329', 'App\\Database\\Migrations\\CreateProductVariantValues', 'default', 'App', 1767152482, 2),
	(28, '2025-11-26-095216', 'App\\Database\\Migrations\\CreateProductAttributeMasterValues', 'default', 'App', 1767152482, 2),
	(29, '2025-12-19-072235', 'App\\Database\\Migrations\\CreateProductVariantAttributes', 'default', 'App', 1767152482, 2),
	(30, '2025-12-23-042235', 'App\\Database\\Migrations\\CreateInventoryTransactions', 'default', 'App', 1767152482, 2),
	(31, '2025-12-24-023451', 'App\\Database\\Migrations\\CreateCartItemPrescriptions', 'default', 'App', 1767152483, 2),
	(32, '2025-12-24-023544', 'App\\Database\\Migrations\\CreateOrderItemPrescriptions', 'default', 'App', 1767152483, 2),
	(33, '2025-12-24-091214', 'App\\Database\\Migrations\\CreateOrderShippingAddresses', 'default', 'App', 1767152483, 2),
	(34, '2025-12-24-091331', 'App\\Database\\Migrations\\CreateCustomerShippingAddresses', 'default', 'App', 1767152483, 2),
	(35, '2026-01-12-100726', 'App\\Database\\Migrations\\CreateNotificationsTable', 'default', 'App', 1768212553, 3),
	(39, '2026-01-21-063951', 'App\\Database\\Migrations\\CreateUserRefundAccountsTable', 'default', 'App', 1768980125, 4),
	(42, '2026-01-21-064052', 'App\\Database\\Migrations\\CreateOrderRefundsTable', 'default', 'App', 1769764977, 5),
	(43, '2026-01-30-000001', 'App\\Database\\Migrations\\CreateOrderRefundItemsTable', 'default', 'App', 1769764977, 5),
	(44, '2026-02-03-102700', 'App\\Database\\Migrations\\CreateOrderCancellationsTable', 'default', 'App', 1770089308, 6),
	(45, '2026-02-03-082006', 'App\\Database\\Migrations\\AddShippingToRefunds', 'default', 'App', 1770106822, 7),
	(46, '2026-02-03-094336', 'App\\Database\\Migrations\\AddPartiallyRefundedStatus', 'default', 'App', 1770111831, 8),
	(47, '2026-03-06-000000', 'App\\Database\\Migrations\\CreateReviewMediaTable', 'default', 'App', 1772779135, 9),
	(48, '2025-11-19-083153', 'App\\Database\\Migrations\\CreateCoupons', 'default', 'App', 1772785137, 10);

-- Dumping structure for table pos_optik.notifications
CREATE TABLE IF NOT EXISTS `notifications` (
  `notification_id` char(36) NOT NULL,
  `type` varchar(50) NOT NULL COMMENT 'Jenis notifikasi: low_stock, new_order, etc',
  `message` text NOT NULL COMMENT 'Pesan notifikasi yang ditampilkan',
  `related_id` char(36) DEFAULT NULL COMMENT 'ID terkait (misal: order_id atau item_id)',
  `is_read` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Status notifikasi dibaca (0 = unread, 1 = read)',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`notification_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table pos_optik.notifications: ~31 rows (approximately)
INSERT INTO `notifications` (`notification_id`, `type`, `message`, `related_id`, `is_read`, `created_at`, `updated_at`, `deleted_at`) VALUES
	('01202f83-4af8-49f3-92b1-99cccde8d822', 'new_order', 'Pembayaran baru dari Novi Anggraini', '60a6af8c-f357-4dc8-9f04-6bd239281731', 0, '2026-02-03 16:32:35', '2026-02-03 16:32:35', NULL),
	('08293eca-2009-416f-867c-24cae15e7e48', 'new_order', 'Pesanan baru dari Novi Anggraini', '60a6af8c-f357-4dc8-9f04-6bd239281731', 1, '2026-02-03 16:20:41', '2026-02-03 16:29:00', NULL),
	('17b05007-8d91-43ff-95f5-2b7daaa88b17', 'new_order', 'Pembayaran baru dari Imam Kadir Mustofa', '3493a45a-826b-4918-afd0-e50d59951809', 1, '2026-03-06 11:10:41', '2026-03-06 11:10:50', NULL),
	('1d9de7e4-9af5-4c5c-b1f3-561c22997140', 'new_order', 'Pembayaran baru dari Novi Anggraini', '9e4477ed-f16a-4602-ab61-7b2edfbe695d', 1, '2026-01-13 02:58:53', '2026-01-13 04:31:27', NULL),
	('39cb1550-2050-41b1-8c64-57f6851258ea', 'new_order', 'Pesanan baru dari Fitria Zulaika', '66cd861c-2ba4-4c7c-9f74-82686dad0c55', 1, '2026-01-19 09:43:43', '2026-01-19 16:57:54', NULL),
	('40deaea2-466d-4d53-8a0d-a44555245222', 'new_order', 'Pembayaran baru dari Ghaliyati Laksmiwati', 'e3d118a5-9b0e-43d5-a9ad-20454a75709d', 1, '2026-02-03 16:50:48', '2026-02-03 16:50:54', NULL),
	('466e753e-f764-427c-acc4-37dc6968a94d', 'new_order', 'Pembayaran baru dari Imam Kadir Mustofa', '93f984e1-6a91-4f17-959e-cd0ae8a1816d', 1, '2026-03-06 14:21:46', '2026-03-06 14:21:56', NULL),
	('48b52096-50ce-4e6c-8b76-771940743753', 'new_order', 'Pembayaran baru dari Fitria Zulaika', '461c7088-0252-416d-9e4c-33058d21116c', 1, '2026-01-21 15:57:10', '2026-01-21 15:57:57', NULL),
	('4a467c97-946d-450d-a541-bfcb289fa0f3', 'cancel_order', 'New cancellation request from Fitria Zulaika', '56c597a7-f7d2-495b-bf24-b1a5b6fee39e', 0, '2026-01-30 16:27:24', '2026-01-30 16:27:24', NULL),
	('4b054fad-910f-4125-85c3-e6660e371ad0', 'new_order', 'Pesanan baru dari Imam Kadir Mustofa', '93f984e1-6a91-4f17-959e-cd0ae8a1816d', 0, '2026-03-06 14:21:39', '2026-03-06 14:21:39', NULL),
	('4f1355a8-5350-4a5f-b4ea-87d46dbb9b36', 'new_order', 'Pesanan baru dari Fitria Zulaika', 'fefe6693-ab41-4540-85e2-47953e0a9ca4', 0, '2026-01-23 14:15:22', '2026-01-23 14:15:22', NULL),
	('534b1a27-e55a-4a13-9857-e89e0c023c9e', 'new_order', 'Pembayaran baru dari Fitria Zulaika', '461c7088-0252-416d-9e4c-33058d21116c', 0, '2026-01-21 15:49:53', '2026-01-21 15:49:53', NULL),
	('593d8964-ab2e-4e8b-a2bc-3f8c142c48a2', 'cancel_order', 'New cancellation request from Imam Kadir Mustofa', 'a8bad1a4-797b-4ad6-9782-620d1e1e5eae', 1, '2026-03-06 11:11:42', '2026-03-06 11:15:09', NULL),
	('5a365eef-7cdf-41be-8135-eaa51c362a13', 'cancel_order', 'New cancellation request from Fitria Zulaika', '5171ccc8-a95e-407f-a538-1ec476bd47e4', 0, '2026-01-29 11:11:50', '2026-01-29 11:11:50', NULL),
	('5b31c24d-c594-430d-a4bd-a3c63e31c086', 'cancel_order', 'New cancellation request from Fitria Zulaika', '278fe156-70bf-4f77-adef-9a25891df0f7', 1, '2026-03-05 10:57:23', '2026-03-05 10:59:56', NULL),
	('5b382b24-7d03-4393-9735-6d643fd11eac', 'low_stock', 'Stok barang \'Orange - Single Vision\' tinggal 4', '37feb422-6150-4909-9384-5c632bdd417f', 1, '2026-01-13 02:59:17', '2026-01-13 04:30:57', NULL),
	('70056214-9dfa-4f3f-93f1-b5bbaa3860d8', 'new_order', 'Pesanan baru dari Ghaliyati Laksmiwati', 'e3d118a5-9b0e-43d5-a9ad-20454a75709d', 0, '2026-02-03 16:50:24', '2026-02-03 16:50:24', NULL),
	('76fd9f13-dc46-498e-920b-65dd3f8beae2', 'cancel_order', 'New cancellation request from Imam Kadir Mustofa', '2b59233d-58a2-410a-8a2d-e8569ab3a131', 1, '2026-03-06 14:22:31', '2026-03-06 14:22:42', NULL),
	('86d3fd78-59b8-4ac2-8092-30eaa2ee067e', 'new_order', 'Pembayaran baru dari Fitria Zulaika', '6d60a746-751f-4ceb-97f0-e5f7a03f9e0c', 1, '2026-01-12 10:22:03', '2026-01-13 04:31:31', NULL),
	('8d1028c9-90b6-482f-b804-bea5f67a8fee', 'new_order', 'Pembayaran baru dari Fitria Zulaika', '47c0572e-c720-4bc1-bde2-e9abef8ecebe', 0, '2026-03-05 10:54:45', '2026-03-05 10:54:45', NULL),
	('9b69b25c-4952-4157-85fd-6532ef849ef1', 'new_order', 'Pesanan baru dari Imam Kadir Mustofa', '3493a45a-826b-4918-afd0-e50d59951809', 0, '2026-03-06 11:10:20', '2026-03-06 11:10:20', NULL),
	('b8030269-0e8b-4a46-9a10-196936110b74', 'stock_empty', 'Stok variant "Orange - Single Vision" telah habis', '37feb422-6150-4909-9384-5c632bdd417f', 1, '2026-01-13 07:01:07', '2026-01-13 07:06:39', NULL),
	('baf66027-9651-4291-bc5b-57266f4b0d51', 'cancel_order', 'New cancellation request from Fitria Zulaika', '7f498a06-5213-485e-bcd9-31b17e2e7522', 0, '2026-01-29 11:11:23', '2026-01-29 11:11:23', NULL),
	('c628ef91-3254-4e75-a12f-95843443ab0c', 'cancel_order', 'New cancellation request from Fitria Zulaika', '2255e218-dc12-43f1-b463-01dbf51f149b', 0, '2026-01-29 11:03:31', '2026-01-29 11:03:31', NULL),
	('ccb88adf-a698-4286-b155-1ebe25c8b2cd', 'new_order', 'Pembayaran baru dari Fitria Zulaika', '66cd861c-2ba4-4c7c-9f74-82686dad0c55', 1, '2026-01-19 16:48:21', '2026-01-19 16:48:29', NULL),
	('ce8d19b3-121e-4a40-bfc1-b69122c5bcf5', 'new_order', 'Pesanan baru dari Fitria Zulaika', '47c0572e-c720-4bc1-bde2-e9abef8ecebe', 0, '2026-03-05 10:54:36', '2026-03-05 10:54:36', NULL),
	('cf1b1b60-3c40-4ed3-9059-dee0c3a4a779', 'cancel_order', 'New cancellation request from Fitria Zulaika', '636cafc3-c8bb-4b77-9049-12336eb273a9', 1, '2026-01-29 11:12:08', '2026-01-29 17:00:43', NULL),
	('dd168c6b-4cc9-4831-9a38-9a9e8edc361f', 'low_stock', 'Stok barang \'F NJ 5095AF 016 51\' tinggal 0', '95f03222-664a-4a4c-8dc4-ac006464bbf5', 1, '2026-01-19 16:48:46', '2026-01-19 16:57:55', NULL),
	('e0817662-b0d7-489c-b01b-c19d6a1f952c', 'new_order', 'Pesanan baru dari Fitria Zulaika', '6d60a746-751f-4ceb-97f0-e5f7a03f9e0c', 1, '2026-01-12 10:19:26', '2026-01-13 04:31:31', NULL),
	('e11096ac-658d-48e2-a572-21c8bcf42086', 'new_order', 'Pesanan baru dari Fitria Zulaika', '461c7088-0252-416d-9e4c-33058d21116c', 0, '2026-01-21 13:20:51', '2026-01-21 13:20:51', NULL),
	('e6e38764-3e0a-40bd-b24a-3f12616999ae', 'new_order', 'Pembayaran baru dari Fitria Zulaika', '461c7088-0252-416d-9e4c-33058d21116c', 0, '2026-01-21 14:57:44', '2026-01-21 14:57:44', NULL),
	('ef8da316-2126-4990-a8f6-3c9b6356d798', 'new_order', 'Pembayaran baru dari Fitria Zulaika', 'fefe6693-ab41-4540-85e2-47953e0a9ca4', 1, '2026-01-23 14:15:31', '2026-01-23 14:15:36', NULL),
	('f43fa066-cec1-4ac9-8580-4ccebc58348c', 'new_order', 'Pesanan baru dari Novi Anggraini', '9e4477ed-f16a-4602-ab61-7b2edfbe695d', 1, '2026-01-13 02:56:03', '2026-01-13 04:31:31', NULL),
	('f6c64c87-61aa-4b53-aa67-212d6877bccc', 'new_order', 'Pembayaran baru dari Fitria Zulaika', '66cd861c-2ba4-4c7c-9f74-82686dad0c55', 1, '2026-01-19 09:43:59', '2026-01-19 09:44:22', NULL);

-- Dumping structure for table pos_optik.orders
CREATE TABLE IF NOT EXISTS `orders` (
  `order_id` char(36) NOT NULL,
  `customer_id` char(36) NOT NULL,
  `order_type` enum('online','offline') NOT NULL DEFAULT 'online',
  `status_id` char(36) NOT NULL,
  `shipping_method_id` char(36) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `shipping_cost` decimal(10,2) DEFAULT NULL,
  `coupon_discount` decimal(10,2) DEFAULT NULL,
  `grand_total` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `tracking_number` varchar(100) DEFAULT NULL,
  `courier` varchar(50) DEFAULT NULL,
  `shipped_at` datetime DEFAULT NULL,
  PRIMARY KEY (`order_id`),
  KEY `orders_customer_id_foreign` (`customer_id`),
  KEY `orders_status_id_foreign` (`status_id`),
  KEY `orders_shipping_method_id_foreign` (`shipping_method_id`),
  CONSTRAINT `orders_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `orders_shipping_method_id_foreign` FOREIGN KEY (`shipping_method_id`) REFERENCES `shipping_methods` (`shipping_method_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `orders_status_id_foreign` FOREIGN KEY (`status_id`) REFERENCES `order_statuses` (`status_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table pos_optik.orders: ~22 rows (approximately)
INSERT INTO `orders` (`order_id`, `customer_id`, `order_type`, `status_id`, `shipping_method_id`, `shipping_cost`, `coupon_discount`, `grand_total`, `created_at`, `updated_at`, `deleted_at`, `tracking_number`, `courier`, `shipped_at`) VALUES
	('0a009259-784d-44bb-97b6-8b8ae9100fed', '091b8d94-d266-49dd-bcb6-5f896b990dd9', 'offline', '8d434de4-ba22-4698-8438-8318ef3f6d8f', NULL, 0.00, 0.00, '6300000', '2026-01-13 05:05:05', '2026-01-13 05:05:05', NULL, NULL, NULL, NULL),
	('303ce892-d3f3-4755-ac71-548216d0ac53', '025f77f1-dbb3-49d8-bb35-01e908a8e181', 'offline', '8d434de4-ba22-4698-8438-8318ef3f6d8f', NULL, 0.00, 0.00, '2600000', '2026-01-09 09:57:52', '2026-01-09 09:57:52', NULL, NULL, NULL, NULL),
	('3493a45a-826b-4918-afd0-e50d59951809', 'c88af1e9-b882-4597-82ca-d414942926e0', 'online', '8d434de4-ba22-4698-8438-8318ef3f6d8f', '3e08ee99-750a-4437-a3a9-922437410f6e', 0.00, 0.00, '2700000', '2026-03-06 11:10:20', '2026-03-06 13:46:37', NULL, '323232423312121', 'JNE', NULL),
	('461c7088-0252-416d-9e4c-33058d21116c', '025f77f1-dbb3-49d8-bb35-01e908a8e181', 'online', '7f39039d-d2ef-46d1-93f5-8dbc0b5211fe', '3e08ee99-750a-4437-a3a9-922437410f6e', 0.00, 0.00, '620000', '2026-01-21 13:20:51', '2026-01-21 15:57:10', NULL, NULL, NULL, NULL),
	('47c0572e-c720-4bc1-bde2-e9abef8ecebe', '025f77f1-dbb3-49d8-bb35-01e908a8e181', 'online', 'cc46d2a8-436c-42fc-96a1-ffb537dbabed', '3e08ee99-750a-4437-a3a9-922437410f6e', 0.00, 0.00, '1000000', '2026-03-05 10:54:36', '2026-03-05 10:55:13', NULL, NULL, NULL, NULL),
	('55e9d366-dd67-4b56-b0a2-21b1139ec9fa', '025f77f1-dbb3-49d8-bb35-01e908a8e181', 'offline', '8d434de4-ba22-4698-8438-8318ef3f6d8f', NULL, 0.00, 0.00, '2400000', '2026-01-12 09:15:08', '2026-01-12 09:15:08', NULL, NULL, NULL, NULL),
	('57c71c0d-1508-43ca-b5b7-31d894d5f7f9', '08b1939f-24fa-4a8f-8f48-4f1a9ee7eb5b', 'offline', '8d434de4-ba22-4698-8438-8318ef3f6d8f', NULL, 0.00, 0.00, '3600000', '2026-01-13 05:00:17', '2026-01-13 05:00:17', NULL, NULL, NULL, NULL),
	('60a6af8c-f357-4dc8-9f04-6bd239281731', '04c82b5b-ea89-4a15-9fa8-8deec59610b2', 'online', 'ae12a448-98b3-4dc1-9c71-87468abc7bb5', '3e08ee99-750a-4437-a3a9-922437410f6e', 20000.00, 0.00, '1640000', '2026-02-03 16:20:41', '2026-02-03 16:38:46', NULL, '12123232212121', 'JNE', NULL),
	('66cd861c-2ba4-4c7c-9f74-82686dad0c55', '025f77f1-dbb3-49d8-bb35-01e908a8e181', 'online', 'cc46d2a8-436c-42fc-96a1-ffb537dbabed', '3e08ee99-750a-4437-a3a9-922437410f6e', 0.00, 0.00, '1000000', '2026-01-19 09:43:43', '2026-01-19 16:48:46', NULL, NULL, NULL, NULL),
	('6a305cd3-c99c-4549-9278-c8360030ec2a', '0f8121bb-e3b7-4437-9c3f-6e589df3b3a1', 'offline', '8d434de4-ba22-4698-8438-8318ef3f6d8f', NULL, 0.00, 0.00, '9000000', '2026-01-13 06:23:52', '2026-01-13 06:23:52', NULL, NULL, NULL, NULL),
	('6d60a746-751f-4ceb-97f0-e5f7a03f9e0c', '025f77f1-dbb3-49d8-bb35-01e908a8e181', 'online', 'cc46d2a8-436c-42fc-96a1-ffb537dbabed', '3e08ee99-750a-4437-a3a9-922437410f6e', 20000.00, 0.00, '1320000', '2026-01-12 10:19:26', '2026-01-13 02:53:56', NULL, NULL, NULL, NULL),
	('6f25a2b9-2535-4731-9e95-b1ad858c2109', '025f77f1-dbb3-49d8-bb35-01e908a8e181', 'online', '4d609622-8392-469b-acd1-c7859424633a', '3e08ee99-750a-4437-a3a9-922437410f6e', 20000.00, 0.00, '2520000', '2026-01-09 04:08:16', '2026-01-09 06:08:56', NULL, '12123232212121', 'jnt', NULL),
	('93f984e1-6a91-4f17-959e-cd0ae8a1816d', 'c88af1e9-b882-4597-82ca-d414942926e0', 'online', 'ae12a448-98b3-4dc1-9c71-87468abc7bb5', '3e08ee99-750a-4437-a3a9-922437410f6e', 0.00, 0.00, '1200000', '2026-03-06 14:21:39', '2026-03-06 14:54:18', NULL, '12123232212121', 'J&T', NULL),
	('963a6fc5-18c2-4fa3-bcfb-af71f29f07cf', '08b1939f-24fa-4a8f-8f48-4f1a9ee7eb5b', 'offline', '8d434de4-ba22-4698-8438-8318ef3f6d8f', NULL, 0.00, 0.00, '5000000', '2026-01-13 06:32:21', '2026-01-13 06:32:21', NULL, NULL, NULL, NULL),
	('9d7dcfe0-8022-4b39-bf7d-c2070b0abf5d', '04c82b5b-ea89-4a15-9fa8-8deec59610b2', 'offline', '8d434de4-ba22-4698-8438-8318ef3f6d8f', NULL, 0.00, 0.00, '3700000', '2026-01-13 06:07:55', '2026-01-13 06:07:55', NULL, NULL, NULL, NULL),
	('9e4477ed-f16a-4602-ab61-7b2edfbe695d', '04c82b5b-ea89-4a15-9fa8-8deec59610b2', 'online', '4d609622-8392-469b-acd1-c7859424633a', '3e08ee99-750a-4437-a3a9-922437410f6e', 20000.00, 0.00, '2520000', '2026-01-13 02:56:03', '2026-01-13 02:59:43', NULL, '12123232212121', 'SiCepat', NULL),
	('cacba380-b39e-41a7-b535-a9040778ec5a', '025f77f1-dbb3-49d8-bb35-01e908a8e181', 'online', 'ae12a448-98b3-4dc1-9c71-87468abc7bb5', '3e08ee99-750a-4437-a3a9-922437410f6e', 20000.00, 0.00, '2320000', '2026-01-12 06:56:45', '2026-02-03 15:53:15', NULL, 'sasawew1q2w121212', 'J&T', NULL),
	('d82d6fae-34df-4573-a4d6-8ca95692a655', '4f67c971-ff30-4379-8ee7-59bb6f5b7804', 'offline', '8d434de4-ba22-4698-8438-8318ef3f6d8f', NULL, 0.00, 0.00, '1200000', '2026-01-13 07:01:07', '2026-01-13 07:01:07', NULL, NULL, NULL, NULL),
	('e3d118a5-9b0e-43d5-a9ad-20454a75709d', '17f27383-11c0-4a1d-84a6-49c1af625b2b', 'online', '09137a62-99b7-48ba-bf27-8c4177ddc185', '3e08ee99-750a-4437-a3a9-922437410f6e', 20000.00, 0.00, '1840000', '2026-02-03 16:50:24', '2026-02-03 16:53:03', NULL, '12123232212121', 'JNE', NULL),
	('ef492371-2bfe-469d-bab6-78d7dd85ecc8', '3cb8e173-3916-4ab2-87d0-d928c3b58310', 'offline', '8d434de4-ba22-4698-8438-8318ef3f6d8f', NULL, 0.00, 0.00, '2400000', '2026-01-13 04:47:06', '2026-01-13 04:47:06', NULL, NULL, NULL, NULL),
	('f84d74bc-c236-46d5-ae54-f86d19598a84', '7581963a-6c05-4e9d-a714-b849e71ef254', 'offline', '8d434de4-ba22-4698-8438-8318ef3f6d8f', NULL, 0.00, 0.00, '2500000', '2026-01-13 06:30:59', '2026-01-13 06:30:59', NULL, NULL, NULL, NULL),
	('fa59964a-e5b7-4b4d-b3c4-bb058cfa703d', '025f77f1-dbb3-49d8-bb35-01e908a8e181', 'online', 'cc46d2a8-436c-42fc-96a1-ffb537dbabed', '3e08ee99-750a-4437-a3a9-922437410f6e', 20000.00, 0.00, '1220000', '2026-01-12 04:10:13', '2026-01-12 04:10:40', NULL, NULL, NULL, NULL),
	('fefe6693-ab41-4540-85e2-47953e0a9ca4', '025f77f1-dbb3-49d8-bb35-01e908a8e181', 'online', '0ab780fe-49da-4a95-ad73-56c3c74f2416', '3e08ee99-750a-4437-a3a9-922437410f6e', 0.00, 0.00, '1200000', '2026-01-23 14:15:22', '2026-01-29 10:54:32', NULL, NULL, NULL, NULL);

-- Dumping structure for table pos_optik.order_cancellations
CREATE TABLE IF NOT EXISTS `order_cancellations` (
  `order_cancellation_id` char(36) NOT NULL,
  `order_id` char(36) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `additional_note` text,
  `status` enum('requested','approved','rejected') NOT NULL DEFAULT 'requested',
  `processed_by` char(36) DEFAULT NULL,
  `processed_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`order_cancellation_id`),
  KEY `order_cancellations_processed_by_foreign` (`processed_by`),
  KEY `order_id` (`order_id`),
  KEY `status` (`status`),
  KEY `created_at` (`created_at`),
  CONSTRAINT `order_cancellations_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `order_cancellations_processed_by_foreign` FOREIGN KEY (`processed_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table pos_optik.order_cancellations: ~2 rows (approximately)
INSERT INTO `order_cancellations` (`order_cancellation_id`, `order_id`, `reason`, `additional_note`, `status`, `processed_by`, `processed_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
	('278fe156-70bf-4f77-adef-9a25891df0f7', '47c0572e-c720-4bc1-bde2-e9abef8ecebe', 'Changed my mind', 'sasasa\n[Admin Reject Note]: sasasas', 'rejected', NULL, '2026-03-05 11:08:11', '2026-03-05 10:57:23', '2026-03-05 11:08:11', NULL),
	('2b59233d-58a2-410a-8a2d-e8569ab3a131', '93f984e1-6a91-4f17-959e-cd0ae8a1816d', 'Changed my mind', 'sasasasa\n[Admin Reject Note]: sasasas', 'rejected', NULL, '2026-03-06 14:22:53', '2026-03-06 14:22:31', '2026-03-06 14:22:53', NULL),
	('a8bad1a4-797b-4ad6-9782-620d1e1e5eae', '3493a45a-826b-4918-afd0-e50d59951809', 'Changed my mind', 'sasasa\n[Admin Reject Note]: Paket sudah di kirim tidak bisa di cancel', 'rejected', NULL, '2026-03-06 11:18:00', '2026-03-06 11:11:42', '2026-03-06 11:18:00', NULL);

-- Dumping structure for table pos_optik.order_coupons
CREATE TABLE IF NOT EXISTS `order_coupons` (
  `order_coupon_id` char(36) NOT NULL,
  `order_id` char(36) NOT NULL,
  `coupon_id` char(36) NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  KEY `order_coupons_order_id_foreign` (`order_id`),
  KEY `order_coupons_coupon_id_foreign` (`coupon_id`),
  CONSTRAINT `order_coupons_coupon_id_foreign` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`coupon_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `order_coupons_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table pos_optik.order_coupons: ~0 rows (approximately)

-- Dumping structure for table pos_optik.order_items
CREATE TABLE IF NOT EXISTS `order_items` (
  `order_item_id` char(36) NOT NULL,
  `order_id` char(36) NOT NULL,
  `product_id` char(36) NOT NULL,
  `variant_id` char(36) DEFAULT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`order_item_id`),
  KEY `order_items_order_id_foreign` (`order_id`),
  KEY `order_items_product_id_foreign` (`product_id`),
  KEY `order_items_variant_id_foreign` (`variant_id`),
  CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `order_items_variant_id_foreign` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`variant_id`) ON DELETE CASCADE ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table pos_optik.order_items: ~33 rows (approximately)
INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `variant_id`, `quantity`, `price`, `created_at`, `updated_at`, `deleted_at`) VALUES
	('106732f5-3ce8-4dd0-91db-30a4a365aa86', '303ce892-d3f3-4755-ac71-548216d0ac53', '95f03222-664a-4a4c-8dc4-ac006464bbf5', 'd767a352-7841-4ae0-8326-1392efedd61b', 2, 1300000.00, '2026-01-09 09:57:52', '2026-01-09 09:57:52', NULL),
	('136d1126-4af6-4a17-b7f3-32066e193ee1', '93f984e1-6a91-4f17-959e-cd0ae8a1816d', '95f03222-664a-4a4c-8dc4-ac006464bbf5', '37feb422-6150-4909-9384-5c632bdd417f', 1, 1200000.00, '2026-03-06 14:21:39', '2026-03-06 14:21:39', NULL),
	('1d81e874-e58e-4e96-8cc7-222960f74f61', 'ef492371-2bfe-469d-bab6-78d7dd85ecc8', '95f03222-664a-4a4c-8dc4-ac006464bbf5', '37feb422-6150-4909-9384-5c632bdd417f', 2, 1200000.00, '2026-01-13 04:47:06', '2026-01-13 04:47:06', NULL),
	('1ee7cf43-c989-458c-bcaa-b9d40ee3987d', 'cacba380-b39e-41a7-b535-a9040778ec5a', '95f03222-664a-4a4c-8dc4-ac006464bbf5', NULL, 1, 1000000.00, '2026-01-12 06:56:45', '2026-01-12 06:56:45', NULL),
	('2143e087-d7ef-449d-936d-a8cafe0ada6e', '3493a45a-826b-4918-afd0-e50d59951809', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', '229eff63-140c-4ceb-9d5c-76ed55953372', 1, 1500000.00, '2026-03-06 11:10:20', '2026-03-06 11:10:20', NULL),
	('224ee939-ab1d-4551-9c23-b6b0be3bae79', '9d7dcfe0-8022-4b39-bf7d-c2070b0abf5d', '95f03222-664a-4a4c-8dc4-ac006464bbf5', 'd767a352-7841-4ae0-8326-1392efedd61b', 1, 1300000.00, '2026-01-13 06:07:55', '2026-01-13 06:07:55', NULL),
	('39741835-bce8-43d5-bac2-c8e68a92d1ee', '963a6fc5-18c2-4fa3-bcfb-af71f29f07cf', '95f03222-664a-4a4c-8dc4-ac006464bbf5', 'd767a352-7841-4ae0-8326-1392efedd61b', 2, 1300000.00, '2026-01-13 06:32:21', '2026-01-13 06:32:21', NULL),
	('502a42e2-d0b4-4189-8982-dc66f116a181', '963a6fc5-18c2-4fa3-bcfb-af71f29f07cf', '95f03222-664a-4a4c-8dc4-ac006464bbf5', '37feb422-6150-4909-9384-5c632bdd417f', 2, 1200000.00, '2026-01-13 06:32:21', '2026-01-13 06:32:21', NULL),
	('512df146-ce08-4872-bbee-bb3648998c51', '9d7dcfe0-8022-4b39-bf7d-c2070b0abf5d', '95f03222-664a-4a4c-8dc4-ac006464bbf5', '37feb422-6150-4909-9384-5c632bdd417f', 2, 1200000.00, '2026-01-13 06:07:55', '2026-01-13 06:07:55', NULL),
	('538abdb2-cc76-408f-8212-6ccac3260d49', 'f84d74bc-c236-46d5-ae54-f86d19598a84', '95f03222-664a-4a4c-8dc4-ac006464bbf5', '37feb422-6150-4909-9384-5c632bdd417f', 1, 1200000.00, '2026-01-13 06:30:59', '2026-01-13 06:30:59', NULL),
	('5b517605-a56a-4865-869d-085907dc5dad', '57c71c0d-1508-43ca-b5b7-31d894d5f7f9', '95f03222-664a-4a4c-8dc4-ac006464bbf5', '37feb422-6150-4909-9384-5c632bdd417f', 2, 1200000.00, '2026-01-13 05:00:17', '2026-01-13 05:00:17', NULL),
	('5be32196-a6ec-4bb6-a495-5501e5a38323', '9e4477ed-f16a-4602-ab61-7b2edfbe695d', '95f03222-664a-4a4c-8dc4-ac006464bbf5', 'd767a352-7841-4ae0-8326-1392efedd61b', 1, 1300000.00, '2026-01-13 02:56:03', '2026-01-13 02:56:03', NULL),
	('5e238ec6-e0c0-4577-8d7b-962e3e436cbe', '6a305cd3-c99c-4549-9278-c8360030ec2a', '95f03222-664a-4a4c-8dc4-ac006464bbf5', '37feb422-6150-4909-9384-5c632bdd417f', 1, 1200000.00, '2026-01-13 06:23:52', '2026-01-13 06:23:52', NULL),
	('68bfa123-8a79-4e83-b056-d9d15dfb5b04', 'e3d118a5-9b0e-43d5-a9ad-20454a75709d', 'c0e93d11-b193-4355-bc9a-6d00d0492fb4', NULL, 1, 620000.00, '2026-02-03 16:50:24', '2026-02-03 16:50:24', NULL),
	('892b69b1-50b4-4314-86d8-d78dbc009f7f', '6f25a2b9-2535-4731-9e95-b1ad858c2109', '95f03222-664a-4a4c-8dc4-ac006464bbf5', 'd767a352-7841-4ae0-8326-1392efedd61b', 1, 1300000.00, '2026-01-09 04:08:16', '2026-01-09 04:08:16', NULL),
	('91f4f420-895f-454e-92e4-466afd54ca2f', '0a009259-784d-44bb-97b6-8b8ae9100fed', '95f03222-664a-4a4c-8dc4-ac006464bbf5', 'd767a352-7841-4ae0-8326-1392efedd61b', 3, 1300000.00, '2026-01-13 05:05:05', '2026-01-13 05:05:05', NULL),
	('92e9eb7f-4418-48e3-bb7f-f7c9ec90ab4e', '55e9d366-dd67-4b56-b0a2-21b1139ec9fa', '95f03222-664a-4a4c-8dc4-ac006464bbf5', '37feb422-6150-4909-9384-5c632bdd417f', 2, 1200000.00, '2026-01-12 09:15:08', '2026-01-12 09:15:08', NULL),
	('932cace1-7a57-42b3-9115-cb6c3080171d', 'e3d118a5-9b0e-43d5-a9ad-20454a75709d', '95f03222-664a-4a4c-8dc4-ac006464bbf5', '37feb422-6150-4909-9384-5c632bdd417f', 1, 1200000.00, '2026-02-03 16:50:24', '2026-02-03 16:50:24', NULL),
	('95f50d8b-7da5-4d08-81f1-5a43e15aea4e', '6d60a746-751f-4ceb-97f0-e5f7a03f9e0c', '95f03222-664a-4a4c-8dc4-ac006464bbf5', 'd767a352-7841-4ae0-8326-1392efedd61b', 1, 1300000.00, '2026-01-12 10:19:26', '2026-01-12 10:19:26', NULL),
	('9c473891-ce1a-4212-9ff3-5608b584eb69', 'f84d74bc-c236-46d5-ae54-f86d19598a84', '95f03222-664a-4a4c-8dc4-ac006464bbf5', 'd767a352-7841-4ae0-8326-1392efedd61b', 1, 1300000.00, '2026-01-13 06:30:59', '2026-01-13 06:30:59', NULL),
	('9e23f7f7-18fd-4274-9117-9c3701db80e7', '9e4477ed-f16a-4602-ab61-7b2edfbe695d', '95f03222-664a-4a4c-8dc4-ac006464bbf5', '37feb422-6150-4909-9384-5c632bdd417f', 1, 1200000.00, '2026-01-13 02:56:03', '2026-01-13 02:56:03', NULL),
	('a56f643b-85ec-46bc-9f5c-35e3a0e73374', '0a009259-784d-44bb-97b6-8b8ae9100fed', '95f03222-664a-4a4c-8dc4-ac006464bbf5', '37feb422-6150-4909-9384-5c632bdd417f', 2, 1200000.00, '2026-01-13 05:05:05', '2026-01-13 05:05:05', NULL),
	('abb6347c-b0e1-4ef3-986b-5f5f63e7be8c', '6a305cd3-c99c-4549-9278-c8360030ec2a', '95f03222-664a-4a4c-8dc4-ac006464bbf5', 'd767a352-7841-4ae0-8326-1392efedd61b', 6, 1300000.00, '2026-01-13 06:23:52', '2026-01-13 06:23:52', NULL),
	('c2316090-fd05-42ad-887a-c3e209c1029c', '47c0572e-c720-4bc1-bde2-e9abef8ecebe', '95f03222-664a-4a4c-8dc4-ac006464bbf5', NULL, 1, 1000000.00, '2026-03-05 10:54:36', '2026-03-05 10:54:36', NULL),
	('d0fa84e4-4f2f-4197-9aba-abe50898928e', '66cd861c-2ba4-4c7c-9f74-82686dad0c55', '95f03222-664a-4a4c-8dc4-ac006464bbf5', NULL, 1, 1000000.00, '2026-01-19 09:43:43', '2026-01-19 09:43:43', NULL),
	('da8285fe-65fb-487c-a55c-ac461474613b', 'fa59964a-e5b7-4b4d-b3c4-bb058cfa703d', '95f03222-664a-4a4c-8dc4-ac006464bbf5', '37feb422-6150-4909-9384-5c632bdd417f', 1, 1200000.00, '2026-01-12 04:10:13', '2026-01-12 04:10:13', NULL),
	('dabe7eb1-2213-46e7-a356-c49732c9646f', 'd82d6fae-34df-4573-a4d6-8ca95692a655', '95f03222-664a-4a4c-8dc4-ac006464bbf5', '37feb422-6150-4909-9384-5c632bdd417f', 1, 1200000.00, '2026-01-13 07:01:07', '2026-01-13 07:01:07', NULL),
	('db78977c-e0ad-4217-b9bc-2b0e3bc1d5c4', 'fefe6693-ab41-4540-85e2-47953e0a9ca4', '95f03222-664a-4a4c-8dc4-ac006464bbf5', '37feb422-6150-4909-9384-5c632bdd417f', 1, 1200000.00, '2026-01-23 14:15:22', '2026-01-23 14:15:22', NULL),
	('e3915dcc-53c6-4a0f-9959-88036c76e962', '57c71c0d-1508-43ca-b5b7-31d894d5f7f9', '95f03222-664a-4a4c-8dc4-ac006464bbf5', '37feb422-6150-4909-9384-5c632bdd417f', 1, 1200000.00, '2026-01-13 05:00:17', '2026-01-13 05:00:17', NULL),
	('e734e6bf-faff-4291-8e3f-d295cc8ff9a8', '461c7088-0252-416d-9e4c-33058d21116c', 'c0e93d11-b193-4355-bc9a-6d00d0492fb4', NULL, 1, 620000.00, '2026-01-21 13:20:51', '2026-01-21 13:20:51', NULL),
	('edac2e53-d2fc-4fa9-9b44-5b1d57b7808f', '6f25a2b9-2535-4731-9e95-b1ad858c2109', '95f03222-664a-4a4c-8dc4-ac006464bbf5', '37feb422-6150-4909-9384-5c632bdd417f', 1, 1200000.00, '2026-01-09 04:08:16', '2026-01-09 04:08:16', NULL),
	('ef18b7b8-a028-4ff0-933e-554d47a1b52f', '60a6af8c-f357-4dc8-9f04-6bd239281731', '95f03222-664a-4a4c-8dc4-ac006464bbf5', NULL, 1, 1000000.00, '2026-02-03 16:20:41', '2026-02-03 16:20:41', NULL),
	('f543a593-c989-4e30-bf84-0aff1434fce6', '60a6af8c-f357-4dc8-9f04-6bd239281731', 'c0e93d11-b193-4355-bc9a-6d00d0492fb4', NULL, 1, 620000.00, '2026-02-03 16:20:41', '2026-02-03 16:20:41', NULL),
	('fac0a92a-a9c8-4498-a155-725646d53aee', 'cacba380-b39e-41a7-b535-a9040778ec5a', '95f03222-664a-4a4c-8dc4-ac006464bbf5', 'd767a352-7841-4ae0-8326-1392efedd61b', 1, 1300000.00, '2026-01-12 06:56:45', '2026-01-12 06:56:45', NULL),
	('fd8ddfe9-e4d6-42cf-b9e8-a6d5fe3684cc', '3493a45a-826b-4918-afd0-e50d59951809', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', 'e9f2f55c-81a2-418c-b85d-6e2e4e3c0e61', 1, 1200000.00, '2026-03-06 11:10:20', '2026-03-06 11:10:20', NULL);

-- Dumping structure for table pos_optik.order_item_prescriptions
CREATE TABLE IF NOT EXISTS `order_item_prescriptions` (
  `order_item_prescription_id` char(36) NOT NULL,
  `order_item_id` char(36) NOT NULL,
  `right_sph` decimal(4,2) DEFAULT NULL,
  `right_cyl` decimal(4,2) DEFAULT NULL,
  `right_axis` int DEFAULT NULL,
  `right_add` decimal(4,2) DEFAULT NULL,
  `left_sph` decimal(4,2) DEFAULT NULL,
  `left_cyl` decimal(4,2) DEFAULT NULL,
  `left_axis` int DEFAULT NULL,
  `left_add` decimal(4,2) DEFAULT NULL,
  `pd_single` decimal(4,1) DEFAULT NULL,
  `pd_left` decimal(4,1) DEFAULT NULL,
  `pd_right` decimal(4,1) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`order_item_prescription_id`),
  KEY `order_item_prescriptions_order_item_id_foreign` (`order_item_id`),
  CONSTRAINT `order_item_prescriptions_order_item_id_foreign` FOREIGN KEY (`order_item_id`) REFERENCES `order_items` (`order_item_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table pos_optik.order_item_prescriptions: ~5 rows (approximately)
INSERT INTO `order_item_prescriptions` (`order_item_prescription_id`, `order_item_id`, `right_sph`, `right_cyl`, `right_axis`, `right_add`, `left_sph`, `left_cyl`, `left_axis`, `left_add`, `pd_single`, `pd_left`, `pd_right`, `created_at`, `updated_at`, `deleted_at`) VALUES
	('30616765-7c1b-4b11-8d28-ee7d05551425', '5e238ec6-e0c0-4577-8d7b-962e3e436cbe', 1.00, 1.00, 1, NULL, 1.00, 1.00, 1, NULL, NULL, 1.0, 1.0, '2026-01-13 06:23:52', '2026-01-13 06:23:52', NULL),
	('32475b66-63db-4344-a0c6-4fe08b0d0031', '502a42e2-d0b4-4189-8982-dc66f116a181', 2.00, 4.00, 5, NULL, 5.00, 5.00, 5, NULL, NULL, 5.0, 5.0, '2026-01-13 06:32:21', '2026-01-13 06:32:21', NULL),
	('358363fb-2602-464c-86c7-c5fe991640b1', '538abdb2-cc76-408f-8212-6ccac3260d49', 1.00, 1.00, 1, NULL, 1.00, 2.00, 1, NULL, NULL, 2.0, 1.0, '2026-01-13 06:30:59', '2026-01-13 06:30:59', NULL),
	('467015f7-89f3-456c-b9bf-0af6277ec27d', 'fd8ddfe9-e4d6-42cf-b9e8-a6d5fe3684cc', 1.00, 2.00, 1, NULL, 1.00, 1.00, 1, NULL, NULL, 1.0, 1.0, '2026-03-06 11:10:20', '2026-03-06 11:10:20', NULL),
	('54742c4c-9dac-4120-af78-93beb89c0c36', '136d1126-4af6-4a17-b7f3-32066e193ee1', 1.00, 2.00, 1, NULL, 1.00, 1.00, 1, NULL, NULL, 1.0, 1.0, '2026-03-06 14:21:39', '2026-03-06 14:21:39', NULL),
	('559fdf9d-ef32-4800-9dd4-6564d9cd09c5', 'db78977c-e0ad-4217-b9bc-2b0e3bc1d5c4', 1.00, 1.00, 1, NULL, 1.00, 1.00, 1, NULL, NULL, 1.0, 1.0, '2026-01-23 14:15:22', '2026-01-23 14:15:22', NULL),
	('d79e3750-1373-4749-94c5-c2c065f7556f', '9c473891-ce1a-4212-9ff3-5608b584eb69', 2.00, 1.00, 1, NULL, 3.00, 2.00, 1, NULL, NULL, 2.0, 1.0, '2026-01-13 06:30:59', '2026-01-13 06:30:59', NULL),
	('d7d773cc-776a-410a-b9a7-240f165930b4', '39741835-bce8-43d5-bac2-c8e68a92d1ee', 1.00, 2.00, 3, NULL, 2.00, 1.00, 2, NULL, NULL, 3.0, 1.0, '2026-01-13 06:32:21', '2026-01-13 06:32:21', NULL);

-- Dumping structure for table pos_optik.order_refunds
CREATE TABLE IF NOT EXISTS `order_refunds` (
  `order_refund_id` char(36) NOT NULL,
  `order_id` char(36) NOT NULL,
  `user_refund_account_id` char(36) DEFAULT NULL,
  `refund_amount` decimal(15,2) DEFAULT NULL COMMENT 'Jumlah yang di-refund, null = full refund',
  `reason` varchar(100) NOT NULL,
  `additional_note` text,
  `status` enum('requested','request_rejected','return_approved','return_shipped','return_received','return_rejected','approved','refunded','expired') NOT NULL DEFAULT 'requested',
  `return_courier` varchar(50) DEFAULT NULL,
  `return_tracking_number` varchar(100) DEFAULT NULL,
  `return_shipped_at` datetime DEFAULT NULL,
  `refund_type` enum('full','partial') DEFAULT NULL COMMENT 'Full refund atau partial (per-item)',
  `admin_note` text,
  `evidence_url` varchar(1024) NOT NULL,
  `processed_by` char(36) DEFAULT NULL COMMENT 'Admin ID yang memproses refund',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL COMMENT 'Waktu saat refund approved/rejected',
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`order_refund_id`),
  KEY `order_refunds_processed_by_foreign` (`processed_by`),
  KEY `order_id` (`order_id`),
  KEY `user_refund_account_id` (`user_refund_account_id`),
  KEY `status` (`status`),
  KEY `created_at_status` (`created_at`,`status`),
  CONSTRAINT `order_refunds_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `order_refunds_processed_by_foreign` FOREIGN KEY (`processed_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE SET NULL,
  CONSTRAINT `order_refunds_user_refund_account_id_foreign` FOREIGN KEY (`user_refund_account_id`) REFERENCES `user_refund_accounts` (`user_refund_account_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table pos_optik.order_refunds: ~3 rows (approximately)
INSERT INTO `order_refunds` (`order_refund_id`, `order_id`, `user_refund_account_id`, `refund_amount`, `reason`, `additional_note`, `status`, `return_courier`, `return_tracking_number`, `return_shipped_at`, `refund_type`, `admin_note`, `evidence_url`, `processed_by`, `created_at`, `updated_at`, `completed_at`, `deleted_at`) VALUES
	('318b7d2b-f3c5-4bd8-92fe-d8370515f691', '93f984e1-6a91-4f17-959e-cd0ae8a1816d', '5c25c14c-bebf-4ae9-b670-726c75e0d68c', 1200000.00, 'Defective product', 'sasasasasasas', 'refunded', 'JNE', '1221232323', '2026-03-06 14:53:49', 'full', 'sasasa', 'https://cdn.adefoodwaste.biz.id/1772781866_f0773f44572eaab34ed4.png', 'bcd30656-71bc-4553-a2d5-2c72c2299ef4', '2026-03-06 14:24:27', '2026-03-06 14:54:18', '2026-03-06 14:54:09', NULL),
	('7f8958f9-f411-4c36-9a56-455f392fdf10', 'cacba380-b39e-41a7-b535-a9040778ec5a', '0d947b70-1180-4dd1-958f-0008095378f9', 1000000.00, 'Better price elsewhere', 'sasasasasasas', 'refunded', 'JNE', '2121323232323232323', '2026-02-03 15:41:08', 'partial', NULL, 'https://cdn.adefoodwaste.biz.id/1770093887_b43ea6e13488f0bd011a.mp4', 'bcd30656-71bc-4553-a2d5-2c72c2299ef4', '2026-02-03 11:44:59', '2026-02-03 15:53:15', '2026-02-03 15:42:21', NULL),
	('ab231493-6b25-49af-8180-c1fa026451f2', '60a6af8c-f357-4dc8-9f04-6bd239281731', '34343708-2418-41e3-ab9a-c7ba2620d893', 620000.00, 'Defective product', 'Produk tidak sesuai dengan yang di gambar', 'refunded', 'JNE', '1982989838982', '2026-02-03 16:38:14', 'partial', NULL, 'https://cdn.adefoodwaste.biz.id/1770111263_4f084cf7558f52137b5a.jpg', 'bcd30656-71bc-4553-a2d5-2c72c2299ef4', '2026-02-03 16:34:24', '2026-02-03 16:38:46', '2026-02-03 16:38:36', NULL),
	('e360ae9e-92b8-4b04-a16d-71345348b3d4', 'e3d118a5-9b0e-43d5-a9ad-20454a75709d', 'de32d740-8243-4a54-92b4-db70bac6faaf', 620000.00, 'Defective product', 'Product tidak sesuai dengan yang di gambaer', 'refunded', 'J&T', '29898929883', '2026-02-03 16:52:37', 'partial', 'Oke', 'https://cdn.adefoodwaste.biz.id/1770112315_6b20f8d12f5d3b445132.jpg', 'bcd30656-71bc-4553-a2d5-2c72c2299ef4', '2026-02-03 16:51:55', '2026-02-03 16:53:03', '2026-02-03 16:52:55', NULL);

-- Dumping structure for table pos_optik.order_refund_items
CREATE TABLE IF NOT EXISTS `order_refund_items` (
  `order_refund_item_id` char(36) NOT NULL,
  `order_refund_id` char(36) NOT NULL,
  `order_item_id` char(36) NOT NULL,
  `qty_refunded` int NOT NULL COMMENT 'Jumlah item yang di-refund',
  `price_per_item` decimal(15,2) NOT NULL COMMENT 'Harga per item saat di-refund',
  `subtotal_refunded` decimal(15,2) NOT NULL COMMENT 'Subtotal refund untuk item ini (qty * price)',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`order_refund_item_id`),
  KEY `order_refund_id` (`order_refund_id`),
  KEY `order_item_id` (`order_item_id`),
  CONSTRAINT `order_refund_items_order_item_id_foreign` FOREIGN KEY (`order_item_id`) REFERENCES `order_items` (`order_item_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `order_refund_items_order_refund_id_foreign` FOREIGN KEY (`order_refund_id`) REFERENCES `order_refunds` (`order_refund_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table pos_optik.order_refund_items: ~3 rows (approximately)
INSERT INTO `order_refund_items` (`order_refund_item_id`, `order_refund_id`, `order_item_id`, `qty_refunded`, `price_per_item`, `subtotal_refunded`, `created_at`, `updated_at`) VALUES
	('11279d35-2970-46bc-ac71-544ccaebf302', '7f8958f9-f411-4c36-9a56-455f392fdf10', '1ee7cf43-c989-458c-bcaa-b9d40ee3987d', 1, 1000000.00, 1000000.00, '2026-02-03 11:45:00', '2026-02-03 11:45:00'),
	('5db09ba3-7132-4a66-8f01-1cfa568ca744', 'e360ae9e-92b8-4b04-a16d-71345348b3d4', '68bfa123-8a79-4e83-b056-d9d15dfb5b04', 1, 620000.00, 620000.00, '2026-02-03 16:51:55', '2026-02-03 16:51:55'),
	('89c70870-af3b-4684-a233-37b19af89e51', 'ab231493-6b25-49af-8180-c1fa026451f2', 'f543a593-c989-4e30-bf84-0aff1434fce6', 1, 620000.00, 620000.00, '2026-02-03 16:34:24', '2026-02-03 16:34:24');

-- Dumping structure for table pos_optik.order_shipping_addresses
CREATE TABLE IF NOT EXISTS `order_shipping_addresses` (
  `osa_id` char(36) NOT NULL,
  `order_id` char(36) NOT NULL,
  `recipient_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `city` varchar(100) NOT NULL,
  `province` varchar(100) NOT NULL,
  `postal_code` varchar(10) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`osa_id`),
  KEY `order_shipping_addresses_order_id_foreign` (`order_id`),
  CONSTRAINT `order_shipping_addresses_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table pos_optik.order_shipping_addresses: ~11 rows (approximately)
INSERT INTO `order_shipping_addresses` (`osa_id`, `order_id`, `recipient_name`, `phone`, `address`, `city`, `province`, `postal_code`, `created_at`, `updated_at`, `deleted_at`) VALUES
	('0f4e07fb-a7a3-4fc1-a6c4-d6e13eaa0db4', 'fefe6693-ab41-4540-85e2-47953e0a9ca4', 'Dystian En Yusgiantoro', '081928938932812', 'Kpg. Baranang No. 630', 'Batu', 'Lampung', '58427', '2026-01-23 14:15:22', '2026-01-23 14:15:22', NULL),
	('0fe4b5de-5893-435d-8ee7-f6f32d76c491', 'cacba380-b39e-41a7-b535-a9040778ec5a', 'Dystian En Yusgiantoro', '0819281992819', 'Jl. Kh. Abdul Rochim No.1, RT.9/RW.1, Kuningan Bar., Kec. Mampang Prpt., Kota Jakarta Selatan, Daerah Khusus Ibukota Jakarta 12710', 'Jakarta Selatan', 'Daerah Khusus Ibukota Jakarta', '58427', '2026-01-12 06:56:45', '2026-01-12 06:56:45', NULL),
	('17bf5bec-1082-402a-ab27-2bcdcacace89', '6d60a746-751f-4ceb-97f0-e5f7a03f9e0c', 'Dystian En Yusgiantoro', '0819281992819', 'Jl. Kh. Abdul Rochim No.1, RT.9/RW.1, Kuningan Bar., Kec. Mampang Prpt., Kota Jakarta Selatan, Daerah Khusus Ibukota Jakarta 12710', 'Jakarta Selatan', 'Daerah Khusus Ibukota Jakarta', '58427', '2026-01-12 10:19:26', '2026-01-12 10:19:26', NULL),
	('25b96cbe-3771-4bc0-beae-291232941e39', '9e4477ed-f16a-4602-ab61-7b2edfbe695d', 'Dystian En Yusgiantoro', '0819281992819', 'Jl. Kh. Abdul Rochim No.1, RT.9/RW.1, Kuningan Bar., Kec. Mampang Prpt., Kota Jakarta Selatan, Daerah Khusus Ibukota Jakarta 12710', 'Jakarta Selatan', 'Daerah Khusus Ibukota Jakarta', '58427', '2026-01-13 02:56:03', '2026-01-13 02:56:03', NULL),
	('2d8182ee-62f5-4dcc-a00d-fd857348e63e', '461c7088-0252-416d-9e4c-33058d21116c', 'Dystian En Yusgiantoro', '081928938932812', 'Kpg. Baranang No. 630', 'Batu', 'Lampung', '58427', '2026-01-21 13:20:51', '2026-01-21 13:20:51', NULL),
	('363f658a-c6ee-4dd5-8b39-33d2710401b1', '3493a45a-826b-4918-afd0-e50d59951809', 'Imam Kadir Mustofa', '08192891829833', 'Kpg. Baranang No. 630', 'Batu', 'Lampung', '58427', '2026-03-06 11:10:20', '2026-03-06 11:10:20', NULL),
	('4494d17e-9793-4508-80cd-d3e02dbc609d', '6f25a2b9-2535-4731-9e95-b1ad858c2109', 'Dystian En Yusgiantoro', '0819281992819', 'Jl. Kh. Abdul Rochim No.1, RT.9/RW.1, Kuningan Bar., Kec. Mampang Prpt., Kota Jakarta Selatan, Daerah Khusus Ibukota Jakarta 12710', 'Jakarta Selatan', 'Daerah Khusus Ibukota Jakarta', '58427', '2026-01-09 04:08:16', '2026-01-09 04:08:16', NULL),
	('8d878b50-f080-4bda-a766-954cfdab1ee5', '66cd861c-2ba4-4c7c-9f74-82686dad0c55', 'Dystian En Yusgiantoro', '081928938932812', 'Kpg. Baranang No. 630', 'Batu', 'Lampung', '58427', '2026-01-19 09:43:43', '2026-01-19 09:43:43', NULL),
	('a5c7e4e0-a472-4528-8f46-47f9898e3ee6', 'e3d118a5-9b0e-43d5-a9ad-20454a75709d', 'Bagus Mulya', '08198293839', 'Tebet Barat Dalam XE No.10', 'Jakarta Pusat', 'DKI Jakarta', '12810', '2026-02-03 16:50:24', '2026-02-03 16:50:24', NULL),
	('bb94ef6b-e282-4107-99a4-7f43eb0cfe25', '93f984e1-6a91-4f17-959e-cd0ae8a1816d', 'Imam Kadir Mustofa', '08192891829833', 'Kpg. Baranang No. 630', 'Batu', 'Lampung', '58427', '2026-03-06 14:21:39', '2026-03-06 14:21:39', NULL),
	('c8880b2a-e11f-4fbf-bec4-b04a10d48650', '60a6af8c-f357-4dc8-9f04-6bd239281731', 'Dystian En Yusgiantoro', '0819281992819', 'Jl. Kh. Abdul Rochim No.1, RT.9/RW.1, Kuningan Bar., Kec. Mampang Prpt., Kota Jakarta Selatan, Daerah Khusus Ibukota Jakarta 12710', 'Jakarta Selatan', 'Daerah Khusus Ibukota Jakarta', '58427', '2026-02-03 16:20:41', '2026-02-03 16:20:41', NULL),
	('db6e1d14-1e5a-43d3-8538-c804aed165ca', '47c0572e-c720-4bc1-bde2-e9abef8ecebe', 'Dystian En Yusgiantoro', '081928938932812', 'Kpg. Baranang No. 630', 'Batu', 'Lampung', '58427', '2026-03-05 10:54:36', '2026-03-05 10:54:36', NULL),
	('fd2f5e85-bd4f-46cf-8970-722cfacba04f', 'fa59964a-e5b7-4b4d-b3c4-bb058cfa703d', 'Dystian En Yusgiantoro', '0819281992819', 'Jl. Kh. Abdul Rochim No.1, RT.9/RW.1, Kuningan Bar., Kec. Mampang Prpt., Kota Jakarta Selatan, Daerah Khusus Ibukota Jakarta 12710', 'Jakarta Selatan', 'Daerah Khusus Ibukota Jakarta', '58427', '2026-01-12 04:10:13', '2026-01-12 04:10:13', NULL);

-- Dumping structure for table pos_optik.order_statuses
CREATE TABLE IF NOT EXISTS `order_statuses` (
  `status_id` char(36) NOT NULL,
  `status_code` varchar(20) NOT NULL,
  `status_name` varchar(50) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table pos_optik.order_statuses: ~8 rows (approximately)
INSERT INTO `order_statuses` (`status_id`, `status_code`, `status_name`, `created_at`, `updated_at`, `deleted_at`) VALUES
	('09137a62-99b7-48ba-bf27-8c4177ddc185', 'partially_refunded', 'Partially Refunded', '2026-02-03 16:43:51', '2026-02-03 16:43:51', NULL),
	('0ab780fe-49da-4a95-ad73-56c3c74f2416', 'cancelled', 'Order Cancelled', NULL, NULL, NULL),
	('2aa5c9be-906c-402c-a5fc-a16663125c3a', 'pending', 'Pending Payment', NULL, NULL, NULL),
	('4d609622-8392-469b-acd1-c7859424633a', 'shipped', 'Shipped to Courier', NULL, NULL, NULL),
	('7f39039d-d2ef-46d1-93f5-8dbc0b5211fe', 'waiting_confirmation', 'Waiting Payment Confirmation', NULL, NULL, NULL),
	('8d434de4-ba22-4698-8438-8318ef3f6d8f', 'completed', 'Order Completed', NULL, NULL, NULL),
	('ae12a448-98b3-4dc1-9c71-87468abc7bb5', 'refunded', 'Order Refunded', NULL, NULL, NULL),
	('cc46d2a8-436c-42fc-96a1-ffb537dbabed', 'processing', 'Order Processing', NULL, NULL, NULL),
	('f1a3c2b4-9e77-4e8d-9b12-2c5a7e8f91ab', 'rejected', 'Payment Rejected', NULL, NULL, NULL);

-- Dumping structure for table pos_optik.payments
CREATE TABLE IF NOT EXISTS `payments` (
  `payment_id` char(36) NOT NULL,
  `order_id` char(36) NOT NULL,
  `payment_method_id` char(36) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `proof` varchar(1024) DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`payment_id`),
  KEY `payments_order_id_foreign` (`order_id`),
  KEY `payments_payment_method_id_foreign` (`payment_method_id`),
  CONSTRAINT `payments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `payments_payment_method_id_foreign` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`payment_method_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table pos_optik.payments: ~27 rows (approximately)
INSERT INTO `payments` (`payment_id`, `order_id`, `payment_method_id`, `amount`, `proof`, `paid_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
	('0fa5feda-4d5f-47b8-afca-aa5cd30e9f7f', '6d60a746-751f-4ceb-97f0-e5f7a03f9e0c', 'e2914263-7e0f-4e3c-9425-0958c9581215', 1320000.00, 'https://cdn.adefoodwaste.biz.id/payments/6d60a746-751f-4ceb-97f0-e5f7a03f9e0c/1768213322_da9b5df72932ee32a7b1.png', '2026-01-12 10:22:03', '2026-01-12 10:22:03', '2026-01-12 10:22:03', NULL),
	('187f9e45-d3fe-42e4-9218-4979c0aa8f8a', '6f25a2b9-2535-4731-9e95-b1ad858c2109', 'e2914263-7e0f-4e3c-9425-0958c9581215', 2520000.00, 'https://cdn.adefoodwaste.biz.id/payments/6f25a2b9-2535-4731-9e95-b1ad858c2109/1767931892_24fb0e381080476ed267.jpg', '2026-01-09 04:11:32', '2026-01-09 04:11:32', '2026-01-09 04:11:32', NULL),
	('18e01e12-46d4-4578-ad2f-667d0c6af807', '47c0572e-c720-4bc1-bde2-e9abef8ecebe', 'e2914263-7e0f-4e3c-9425-0958c9581215', 1000000.00, 'https://cdn.adefoodwaste.biz.id/payments/47c0572e-c720-4bc1-bde2-e9abef8ecebe/1772682884_8773f2ff4d26b522e89b.png', '2026-03-05 10:54:45', '2026-03-05 10:54:45', '2026-03-05 10:54:45', NULL),
	('1c9d83b1-026d-4cd6-a787-2d3ac9cf2637', 'cacba380-b39e-41a7-b535-a9040778ec5a', 'e2914263-7e0f-4e3c-9425-0958c9581215', 2320000.00, 'https://cdn.adefoodwaste.biz.id/payments/cacba380-b39e-41a7-b535-a9040778ec5a/1768202990_c9c6a42c11e13554de9d.jpg', '2026-01-12 07:29:51', '2026-01-12 07:29:51', '2026-01-12 07:29:51', NULL),
	('1dc7891b-08b2-4208-8cc9-3e266c4fd185', 'fa59964a-e5b7-4b4d-b3c4-bb058cfa703d', 'e2914263-7e0f-4e3c-9425-0958c9581215', 1220000.00, 'https://cdn.adefoodwaste.biz.id/payments/fa59964a-e5b7-4b4d-b3c4-bb058cfa703d/1768191021_df70566780924ad89b03.jpg', '2026-01-12 04:10:22', '2026-01-12 04:10:22', '2026-01-12 04:10:22', NULL),
	('2189c962-8625-431a-a101-f50cc57186f2', 'cacba380-b39e-41a7-b535-a9040778ec5a', 'e2914263-7e0f-4e3c-9425-0958c9581215', 2320000.00, 'https://cdn.adefoodwaste.biz.id/payments/cacba380-b39e-41a7-b535-a9040778ec5a/1768201738_044c204071d28819196e.jpg', '2026-01-12 07:08:58', '2026-01-12 07:08:58', '2026-01-12 07:08:58', NULL),
	('264b5a25-e0e1-48b3-820d-1de1cb04d990', 'cacba380-b39e-41a7-b535-a9040778ec5a', 'e2914263-7e0f-4e3c-9425-0958c9581215', 2320000.00, 'https://cdn.adefoodwaste.biz.id/payments/cacba380-b39e-41a7-b535-a9040778ec5a/1768204681_d4b5aa2792d2361a229d.jpg', '2026-01-12 07:58:01', '2026-01-12 07:58:01', '2026-01-12 07:58:01', NULL),
	('2bf6d45b-333f-49b0-aeaa-d5bb6e3371fb', 'cacba380-b39e-41a7-b535-a9040778ec5a', 'e2914263-7e0f-4e3c-9425-0958c9581215', 2320000.00, 'https://cdn.adefoodwaste.biz.id/payments/cacba380-b39e-41a7-b535-a9040778ec5a/1768204920_24a6cc0b75a7af50767e.jpg', '2026-01-12 08:02:01', '2026-01-12 08:02:01', '2026-01-12 08:02:01', NULL),
	('354c878f-fec1-4bbf-acca-78c4e3a42070', 'fefe6693-ab41-4540-85e2-47953e0a9ca4', 'e2914263-7e0f-4e3c-9425-0958c9581215', 1200000.00, 'https://cdn.adefoodwaste.biz.id/payments/fefe6693-ab41-4540-85e2-47953e0a9ca4/1769152530_6ea4f74eac72a7136603.jpg', '2026-01-23 14:15:31', '2026-01-23 14:15:31', '2026-01-23 14:15:31', NULL),
	('37a2dcbc-a12e-4d67-9aeb-25455210e1ae', 'cacba380-b39e-41a7-b535-a9040778ec5a', 'e2914263-7e0f-4e3c-9425-0958c9581215', 2320000.00, 'https://cdn.adefoodwaste.biz.id/payments/cacba380-b39e-41a7-b535-a9040778ec5a/1768201404_8e1ed086fe5228e67523.jpg', '2026-01-12 07:03:25', '2026-01-12 07:03:25', '2026-01-12 07:03:25', NULL),
	('37c6c907-4d0c-41d2-83a2-85cd5b42a31c', 'cacba380-b39e-41a7-b535-a9040778ec5a', 'e2914263-7e0f-4e3c-9425-0958c9581215', 2320000.00, 'https://cdn.adefoodwaste.biz.id/payments/cacba380-b39e-41a7-b535-a9040778ec5a/1768202397_0c310b3481122ae1b4a6.png', '2026-01-12 07:19:58', '2026-01-12 07:19:58', '2026-01-12 07:19:58', NULL),
	('3d6f3154-fdb4-45b2-b220-2ec26f88ebca', 'cacba380-b39e-41a7-b535-a9040778ec5a', 'e2914263-7e0f-4e3c-9425-0958c9581215', 2320000.00, 'https://cdn.adefoodwaste.biz.id/payments/cacba380-b39e-41a7-b535-a9040778ec5a/1768201492_30d4606cd47c7698443b.jpg', '2026-01-12 07:04:53', '2026-01-12 07:04:53', '2026-01-12 07:04:53', NULL),
	('3fa59dc2-f825-4da3-84dc-d68f80221c1d', 'cacba380-b39e-41a7-b535-a9040778ec5a', 'e2914263-7e0f-4e3c-9425-0958c9581215', 2320000.00, 'https://cdn.adefoodwaste.biz.id/payments/cacba380-b39e-41a7-b535-a9040778ec5a/1768201379_58fd9543b56747ae9391.jpg', '2026-01-12 07:03:00', '2026-01-12 07:03:00', '2026-01-12 07:03:00', NULL),
	('56b3c949-d9cd-4101-b8e6-2eb0f64d605b', 'cacba380-b39e-41a7-b535-a9040778ec5a', 'e2914263-7e0f-4e3c-9425-0958c9581215', 2320000.00, 'https://cdn.adefoodwaste.biz.id/payments/cacba380-b39e-41a7-b535-a9040778ec5a/1768204807_702c2b675a5bcc864963.png', '2026-01-12 08:00:08', '2026-01-12 08:00:08', '2026-01-12 08:00:08', NULL),
	('5bf4605b-c426-4989-9f53-ea63b1276090', '461c7088-0252-416d-9e4c-33058d21116c', 'e2914263-7e0f-4e3c-9425-0958c9581215', 620000.00, 'https://cdn.adefoodwaste.biz.id/payments/461c7088-0252-416d-9e4c-33058d21116c/1768985829_e2be1887a8d08568c921.jpg', '2026-01-21 15:57:10', '2026-01-21 15:57:10', '2026-01-21 15:57:10', NULL),
	('625a8e20-10b3-4b5d-850f-f5c2d5df4b89', 'cacba380-b39e-41a7-b535-a9040778ec5a', 'e2914263-7e0f-4e3c-9425-0958c9581215', 2320000.00, 'https://cdn.adefoodwaste.biz.id/payments/cacba380-b39e-41a7-b535-a9040778ec5a/1768201010_a5de13a31bcc8eff44f5.jpg', '2026-01-12 06:56:50', '2026-01-12 06:56:50', '2026-01-12 06:56:50', NULL),
	('6c159dd9-4f94-4285-be39-61e524f71836', 'cacba380-b39e-41a7-b535-a9040778ec5a', 'e2914263-7e0f-4e3c-9425-0958c9581215', 2320000.00, 'https://cdn.adefoodwaste.biz.id/payments/cacba380-b39e-41a7-b535-a9040778ec5a/1768204411_211ba11e82268cacb1e0.jpg', '2026-01-12 07:53:32', '2026-01-12 07:53:32', '2026-01-12 07:53:32', NULL),
	('6c5aa304-486c-4f16-9d7e-7400a8e61e68', '66cd861c-2ba4-4c7c-9f74-82686dad0c55', 'e2914263-7e0f-4e3c-9425-0958c9581215', 1000000.00, 'https://cdn.adefoodwaste.biz.id/payments/66cd861c-2ba4-4c7c-9f74-82686dad0c55/1768816100_55f48543b87b8b7d0f8f.jpg', '2026-01-19 16:48:20', '2026-01-19 16:48:21', '2026-01-19 16:48:21', NULL),
	('75a82f3e-7d1e-4b5d-ad1e-d14dccf6044a', '461c7088-0252-416d-9e4c-33058d21116c', 'e2914263-7e0f-4e3c-9425-0958c9581215', 620000.00, 'https://cdn.adefoodwaste.biz.id/payments/461c7088-0252-416d-9e4c-33058d21116c/1768982263_735ef7c2eb8f4a8056a2.png', '2026-01-21 14:57:44', '2026-01-21 14:57:44', '2026-01-21 14:57:44', NULL),
	('78ebd670-0b4e-4698-9986-e2712abb82fd', '3493a45a-826b-4918-afd0-e50d59951809', 'e2914263-7e0f-4e3c-9425-0958c9581215', 2700000.00, 'https://cdn.adefoodwaste.biz.id/payments/3493a45a-826b-4918-afd0-e50d59951809/1772770239_f854c881275a871c444d.jpeg', '2026-03-06 11:10:41', '2026-03-06 11:10:41', '2026-03-06 11:10:41', NULL),
	('7a6879fc-8314-42ed-8b92-bdb473372383', 'cacba380-b39e-41a7-b535-a9040778ec5a', 'e2914263-7e0f-4e3c-9425-0958c9581215', 2320000.00, 'https://cdn.adefoodwaste.biz.id/payments/cacba380-b39e-41a7-b535-a9040778ec5a/1768202706_59afbb819655705aeeda.png', '2026-01-12 07:25:07', '2026-01-12 07:25:07', '2026-01-12 07:25:07', NULL),
	('7c19de24-3229-494a-8927-6fbb5fa7a9b7', 'cacba380-b39e-41a7-b535-a9040778ec5a', 'e2914263-7e0f-4e3c-9425-0958c9581215', 2320000.00, 'https://cdn.adefoodwaste.biz.id/payments/cacba380-b39e-41a7-b535-a9040778ec5a/1768202256_a212b792e04149727839.jpg', '2026-01-12 07:17:36', '2026-01-12 07:17:36', '2026-01-12 07:17:36', NULL),
	('82ec633b-9601-4937-997c-2696d67758b0', '93f984e1-6a91-4f17-959e-cd0ae8a1816d', 'e2914263-7e0f-4e3c-9425-0958c9581215', 1200000.00, 'https://cdn.adefoodwaste.biz.id/payments/93f984e1-6a91-4f17-959e-cd0ae8a1816d/1772781706_3948224111af6f94df85.png', '2026-03-06 14:21:46', '2026-03-06 14:21:46', '2026-03-06 14:21:46', NULL),
	('8ab63182-a95a-4d78-bf20-367d11e35bc3', '60a6af8c-f357-4dc8-9f04-6bd239281731', 'e2914263-7e0f-4e3c-9425-0958c9581215', 1640000.00, 'https://cdn.adefoodwaste.biz.id/payments/60a6af8c-f357-4dc8-9f04-6bd239281731/1770111155_d983181ef86ae97acf3f.jpg', '2026-02-03 16:32:35', '2026-02-03 16:32:35', '2026-02-03 16:32:35', NULL),
	('8e0c0284-3231-4976-ae7a-d6f8d73cc1ac', 'cacba380-b39e-41a7-b535-a9040778ec5a', 'e2914263-7e0f-4e3c-9425-0958c9581215', 2320000.00, 'https://cdn.adefoodwaste.biz.id/payments/cacba380-b39e-41a7-b535-a9040778ec5a/1768201431_ffc47ba36a87df51adce.jpg', '2026-01-12 07:03:52', '2026-01-12 07:03:52', '2026-01-12 07:03:52', NULL),
	('95c4a2cf-e2b1-4e8e-95cf-26b4eb715e90', 'cacba380-b39e-41a7-b535-a9040778ec5a', 'e2914263-7e0f-4e3c-9425-0958c9581215', 2320000.00, 'https://cdn.adefoodwaste.biz.id/payments/cacba380-b39e-41a7-b535-a9040778ec5a/1768202649_df542a16943bd7ad84eb.jpg', '2026-01-12 07:24:09', '2026-01-12 07:24:09', '2026-01-12 07:24:09', NULL),
	('aafee2f5-e643-48c0-80b2-71da09c88df5', 'cacba380-b39e-41a7-b535-a9040778ec5a', 'e2914263-7e0f-4e3c-9425-0958c9581215', 2320000.00, 'https://cdn.adefoodwaste.biz.id/payments/cacba380-b39e-41a7-b535-a9040778ec5a/1768202820_00d7c18729b98958cf06.jpg', '2026-01-12 07:27:01', '2026-01-12 07:27:01', '2026-01-12 07:27:01', NULL),
	('b49b8b7b-8d00-44d6-ad87-6ff83a24a719', 'cacba380-b39e-41a7-b535-a9040778ec5a', 'e2914263-7e0f-4e3c-9425-0958c9581215', 2320000.00, 'https://cdn.adefoodwaste.biz.id/payments/cacba380-b39e-41a7-b535-a9040778ec5a/1768201336_71a68872d9d7e47b808f.jpg', '2026-01-12 07:02:17', '2026-01-12 07:02:17', '2026-01-12 07:02:17', NULL),
	('bd83f236-96b7-4a64-9384-da3dad70cf45', 'cacba380-b39e-41a7-b535-a9040778ec5a', 'e2914263-7e0f-4e3c-9425-0958c9581215', 2320000.00, 'https://cdn.adefoodwaste.biz.id/payments/cacba380-b39e-41a7-b535-a9040778ec5a/1768201279_6da3c5b9020ac7b2c422.jpg', '2026-01-12 07:01:20', '2026-01-12 07:01:20', '2026-01-12 07:01:20', NULL),
	('d7d0f89b-c5d0-4fcc-b9cf-7733cdd3de1a', '9e4477ed-f16a-4602-ab61-7b2edfbe695d', 'e2914263-7e0f-4e3c-9425-0958c9581215', 2520000.00, 'https://cdn.adefoodwaste.biz.id/payments/9e4477ed-f16a-4602-ab61-7b2edfbe695d/1768273131_7174e9874f6f486e4ca5.jpg', '2026-01-13 02:58:53', '2026-01-13 02:58:53', '2026-01-13 02:58:53', NULL),
	('dec34767-291f-4b4a-973d-b0ef87899b68', '66cd861c-2ba4-4c7c-9f74-82686dad0c55', 'e2914263-7e0f-4e3c-9425-0958c9581215', 1000000.00, 'https://cdn.adefoodwaste.biz.id/payments/66cd861c-2ba4-4c7c-9f74-82686dad0c55/1768815837_520a7d02c3f13d74201f.jpg', '2026-01-19 09:43:59', '2026-01-19 09:43:59', '2026-01-19 09:43:59', NULL),
	('dfa538f6-33b3-40e1-b612-faed9285a269', 'cacba380-b39e-41a7-b535-a9040778ec5a', 'e2914263-7e0f-4e3c-9425-0958c9581215', 2320000.00, 'https://cdn.adefoodwaste.biz.id/payments/cacba380-b39e-41a7-b535-a9040778ec5a/1768204543_6342ec7dcb600924fc84.jpg', '2026-01-12 07:55:43', '2026-01-12 07:55:43', '2026-01-12 07:55:43', NULL),
	('e3b0d355-7163-4cff-8b44-fb942cc6a0b6', '461c7088-0252-416d-9e4c-33058d21116c', 'e2914263-7e0f-4e3c-9425-0958c9581215', 620000.00, 'https://cdn.adefoodwaste.biz.id/payments/461c7088-0252-416d-9e4c-33058d21116c/1768985392_1bc4af2b7d835de8f194.jpg', '2026-01-21 15:49:53', '2026-01-21 15:49:53', '2026-01-21 15:49:53', NULL),
	('e4641da4-1eda-4bef-b8fb-64f9f2c58d9f', 'cacba380-b39e-41a7-b535-a9040778ec5a', 'e2914263-7e0f-4e3c-9425-0958c9581215', 2320000.00, 'https://cdn.adefoodwaste.biz.id/payments/cacba380-b39e-41a7-b535-a9040778ec5a/1768202951_16042cc992555f7f943c.jpg', '2026-01-12 07:29:12', '2026-01-12 07:29:12', '2026-01-12 07:29:12', NULL),
	('fc4747e2-9711-4c67-a7ae-bc64fe22ce41', 'e3d118a5-9b0e-43d5-a9ad-20454a75709d', 'e2914263-7e0f-4e3c-9425-0958c9581215', 1840000.00, 'https://cdn.adefoodwaste.biz.id/payments/e3d118a5-9b0e-43d5-a9ad-20454a75709d/1770112247_85a1f69dd0fa9b82b2d5.png', '2026-02-03 16:50:48', '2026-02-03 16:50:48', '2026-02-03 16:50:48', NULL);

-- Dumping structure for table pos_optik.payment_methods
CREATE TABLE IF NOT EXISTS `payment_methods` (
  `payment_method_id` char(36) NOT NULL,
  `method_name` varchar(100) NOT NULL,
  `method_type` varchar(50) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`payment_method_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table pos_optik.payment_methods: ~4 rows (approximately)
INSERT INTO `payment_methods` (`payment_method_id`, `method_name`, `method_type`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
	('581c746b-0084-4ac3-9c2e-2c00ea5d6ab7', 'Cash', 'cash', 1, '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL),
	('7aeb3cfe-7ab5-4adf-a1ae-66f1d583ae56', 'BCA Transfer', 'bank_transfer', 1, '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL),
	('b24366c0-bada-479c-a678-0e9434375a8d', 'Mandiri Transfer', 'bank_transfer', 1, '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL),
	('e2914263-7e0f-4e3c-9425-0958c9581215', 'Manual Transfer', 'manual_transfer', 1, '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL);

-- Dumping structure for table pos_optik.products
CREATE TABLE IF NOT EXISTS `products` (
  `product_id` char(36) NOT NULL,
  `category_id` char(36) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `product_stock` int NOT NULL DEFAULT '0',
  `product_brand` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`product_id`),
  KEY `products_category_id_foreign` (`category_id`),
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table pos_optik.products: ~3 rows (approximately)
INSERT INTO `products` (`product_id`, `category_id`, `product_name`, `product_price`, `product_stock`, `product_brand`, `created_at`, `updated_at`, `deleted_at`) VALUES
	('95f03222-664a-4a4c-8dc4-ac006464bbf5', '8a94d72c-a8f2-4205-9e2e-e6a0bd00927a', 'F NJ 5095AF 016 51', 1000000.00, 98, 'NIKE', '2026-01-09 03:39:20', '2026-03-05 10:55:13', NULL),
	('b2c61bfb-e9dd-4f06-b722-c1706c9e1743', '8a94d72c-a8f2-4205-9e2e-e6a0bd00927a', 'Kacamata Uhuy', 1200000.00, 148, 'ADIDAS', '2026-03-06 08:38:42', '2026-03-06 10:53:43', NULL),
	('c0e93d11-b193-4355-bc9a-6d00d0492fb4', '8da318e2-5454-4346-a95b-2f28208e6ea6', '1 DAY ACUVUE DEFINE', 620000.00, 98, 'DEFINE', '2026-01-14 08:15:21', '2026-02-03 16:50:58', NULL);

-- Dumping structure for table pos_optik.product_attributes
CREATE TABLE IF NOT EXISTS `product_attributes` (
  `attribute_id` char(36) NOT NULL,
  `attribute_name` varchar(50) NOT NULL,
  `attribute_type` varchar(20) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table pos_optik.product_attributes: ~8 rows (approximately)
INSERT INTO `product_attributes` (`attribute_id`, `attribute_name`, `attribute_type`, `created_at`, `updated_at`, `deleted_at`) VALUES
	('00cbc3c6-f421-4714-b509-e9770e3182d1', 'Temple Length', 'text', '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL),
	('17d811ef-8002-4db7-8cbd-6f012ad12028', 'Bridge Size', 'text', '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL),
	('331f5339-1774-4b06-9e19-bb88b603c5a2', 'Color', 'dropdown', '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL),
	('77e03517-d3b5-4a73-9066-0b7c21338c0a', 'Frame Material', 'dropdown', '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL),
	('dbc661e8-ad9c-4dfe-8fe5-40707210c3f3', 'Frame Size (Width)', 'text', '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL),
	('edfee81e-0a02-4e09-b3c4-a4a8cdcee514', 'Lens Type', 'dropdown', '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL),
	('fab9a0b6-5633-43a5-b78a-cd6523e4c406', 'Lens Material', 'dropdown', '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL),
	('fe556900-64e2-4f9a-b9cd-8b7e023a72c6', 'Frame Shape', 'dropdown', '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL);

-- Dumping structure for table pos_optik.product_attribute_master_values
CREATE TABLE IF NOT EXISTS `product_attribute_master_values` (
  `attribute_master_id` char(36) NOT NULL,
  `attribute_id` char(36) NOT NULL,
  `value` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`attribute_master_id`),
  KEY `product_attribute_master_values_attribute_id_foreign` (`attribute_id`),
  CONSTRAINT `product_attribute_master_values_attribute_id_foreign` FOREIGN KEY (`attribute_id`) REFERENCES `product_attributes` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table pos_optik.product_attribute_master_values: ~30 rows (approximately)
INSERT INTO `product_attribute_master_values` (`attribute_master_id`, `attribute_id`, `value`, `created_at`, `updated_at`, `deleted_at`) VALUES
	('2580345f-fe34-4c6a-95d5-97bf41183559', 'fe556900-64e2-4f9a-b9cd-8b7e023a72c6', 'Square', '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL),
	('28d696fb-01b8-4234-a842-83228404dec3', 'fe556900-64e2-4f9a-b9cd-8b7e023a72c6', 'Aviator', '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL),
	('380eab23-4266-414c-8d15-f4f4eb75e6af', 'fe556900-64e2-4f9a-b9cd-8b7e023a72c6', 'Rectangle', '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL),
	('3c8c9832-0c64-4b1c-a5dc-441594fa7c8f', '331f5339-1774-4b06-9e19-bb88b603c5a2', 'Blue', '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL),
	('4aa9dadc-8e56-4779-9bd8-efc35982dc1c', '77e03517-d3b5-4a73-9066-0b7c21338c0a', 'Acetate', '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL),
	('57ea0506-34c6-45d3-b327-7abe69ee7781', 'fe556900-64e2-4f9a-b9cd-8b7e023a72c6', 'Round', '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL),
	('6473ef98-f376-4483-aae6-90c94ab12dd7', '77e03517-d3b5-4a73-9066-0b7c21338c0a', 'Titanium', '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL),
	('70de4ba2-3e59-4c33-af91-d5f3c27631c5', 'edfee81e-0a02-4e09-b3c4-a4a8cdcee514', 'Single Vision', '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL),
	('71173f0b-2fe5-427f-90a1-fa4fedccfeac', 'edfee81e-0a02-4e09-b3c4-a4a8cdcee514', 'Photochromic Lens', '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL),
	('74cd180f-032f-4036-a210-73370ef62136', 'fab9a0b6-5633-43a5-b78a-cd6523e4c406', 'Polycarbonate', '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL),
	('76b7d8ef-b921-4229-8f81-a2086d9f4b46', '331f5339-1774-4b06-9e19-bb88b603c5a2', 'Orange', '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL),
	('7893e717-9d3c-459f-aaca-56877118f041', 'edfee81e-0a02-4e09-b3c4-a4a8cdcee514', 'Blue Light Blocking Lens', '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL),
	('7cbc85f9-642c-4df9-8ca2-2150959963e0', 'fab9a0b6-5633-43a5-b78a-cd6523e4c406', 'Digital / Freeform Lens', '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL),
	('8352fdec-1e25-4072-937c-7824f91d96b1', 'fab9a0b6-5633-43a5-b78a-cd6523e4c406', 'High-Index Plastic (1.61 / 1.67 / 1.74)', '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL),
	('9068bfc3-20fc-41c0-85ac-0fcf8805bc61', '77e03517-d3b5-4a73-9066-0b7c21338c0a', 'Aluminum', '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL),
	('912d27f6-efc6-4b0a-a0ed-16c8e238496e', '331f5339-1774-4b06-9e19-bb88b603c5a2', 'White', '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL),
	('93834a31-efd5-40eb-be1d-202209d28537', '331f5339-1774-4b06-9e19-bb88b603c5a2', 'Black', '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL),
	('9ddf1bb2-e281-4afa-8dc5-134f7260a8e7', 'edfee81e-0a02-4e09-b3c4-a4a8cdcee514', 'Polycarbonate Lens', '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL),
	('a0a4ca4e-aa6a-4892-bc17-e0ac76bff434', 'fab9a0b6-5633-43a5-b78a-cd6523e4c406', 'CR-39', '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL),
	('a59fcb63-8ce1-47ca-801a-900b23ddd555', '331f5339-1774-4b06-9e19-bb88b603c5a2', 'Brown', '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL),
	('b3970f64-d191-46c3-993c-7ed8dbb04c18', '77e03517-d3b5-4a73-9066-0b7c21338c0a', 'Carbon Fiber', '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL),
	('b87a1784-9c11-4107-ae87-557ab8ad7f7f', 'fab9a0b6-5633-43a5-b78a-cd6523e4c406', 'Trivex', '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL),
	('d184fdcf-0205-4731-9de8-ba9b506325ad', '331f5339-1774-4b06-9e19-bb88b603c5a2', 'Red', '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL),
	('d5b30609-e166-4a07-b624-f49acf198882', 'fe556900-64e2-4f9a-b9cd-8b7e023a72c6', 'Cat Eye', '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL),
	('dc4383ac-45e4-4516-8bc2-ff18ad8a4ccf', '77e03517-d3b5-4a73-9066-0b7c21338c0a', 'Stainless Steel', '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL),
	('dd855520-63f2-4713-8275-19ce41fddd0a', '77e03517-d3b5-4a73-9066-0b7c21338c0a', 'Polycarbonate', '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL),
	('ddbfbb11-4eb0-4db3-b666-e8a5e56acac3', 'fab9a0b6-5633-43a5-b78a-cd6523e4c406', 'Glass', '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL),
	('ecdf262b-184b-427a-85c6-11e078eb8a7b', 'edfee81e-0a02-4e09-b3c4-a4a8cdcee514', 'Progressive Lens', '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL),
	('f9f708db-ab84-4a51-a23b-621b2c6cad0e', '331f5339-1774-4b06-9e19-bb88b603c5a2', 'Gold', '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL),
	('fd4258c3-9f3a-4f51-9fb4-924735551ab7', 'fab9a0b6-5633-43a5-b78a-cd6523e4c406', 'Aspheric Lens', '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL);

-- Dumping structure for table pos_optik.product_attribute_values
CREATE TABLE IF NOT EXISTS `product_attribute_values` (
  `pav_id` char(36) NOT NULL,
  `product_id` char(36) NOT NULL,
  `attribute_id` char(36) NOT NULL,
  `value` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`pav_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table pos_optik.product_attribute_values: ~45 rows (approximately)
INSERT INTO `product_attribute_values` (`pav_id`, `product_id`, `attribute_id`, `value`, `created_at`, `updated_at`, `deleted_at`) VALUES
	('047b8f23-9513-47b2-9476-3c9eb7cfcd48', 'c9b49df6-f9fa-4bed-a1c9-66f983210825', 'fab9a0b6-5633-43a5-b78a-cd6523e4c406', 'Polycarbonate', '2025-12-31 04:06:54', '2025-12-31 04:06:54', NULL),
	('05075515-b2e4-4ec3-a516-ad1c5d9a1bc6', 'c9b49df6-f9fa-4bed-a1c9-66f983210825', '17d811ef-8002-4db7-8cbd-6f012ad12028', '1', '2025-12-31 04:06:54', '2025-12-31 04:06:54', NULL),
	('0c67c9b9-f4b1-4ffa-8248-f6be27a67ec0', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', 'fe556900-64e2-4f9a-b9cd-8b7e023a72c6', 'Square', '2026-03-06 10:53:43', '2026-03-06 10:53:43', NULL),
	('0c9aaddd-6f9d-4bc2-acee-a2ce1df6880a', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', '331f5339-1774-4b06-9e19-bb88b603c5a2', 'Brown', '2026-03-06 08:38:45', '2026-03-06 10:53:26', '2026-03-06 10:53:26'),
	('0de71b66-b0cd-4615-8ed0-b64275748133', '95f03222-664a-4a4c-8dc4-ac006464bbf5', '77e03517-d3b5-4a73-9066-0b7c21338c0a', 'Titanium', '2026-01-09 03:39:23', '2026-01-09 03:39:23', NULL),
	('14e63983-6bf0-4324-9d23-ebddb472ccae', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', '77e03517-d3b5-4a73-9066-0b7c21338c0a', 'Titanium', '2026-03-06 10:53:43', '2026-03-06 10:53:43', NULL),
	('17f161f7-75e5-4bf0-baff-48fcac3618f7', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', 'edfee81e-0a02-4e09-b3c4-a4a8cdcee514', 'Blue Light Blocking Lens', '2026-03-06 10:53:43', '2026-03-06 10:53:43', NULL),
	('19845266-e68c-4233-b8fc-f5fddab4673f', 'c9b49df6-f9fa-4bed-a1c9-66f983210825', '331f5339-1774-4b06-9e19-bb88b603c5a2', 'Brown', '2025-12-31 04:06:54', '2025-12-31 04:06:54', NULL),
	('2c7c3b87-dae8-4c89-8a7c-3d3e6013bfd0', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', '331f5339-1774-4b06-9e19-bb88b603c5a2', 'Black', '2026-03-06 10:53:43', '2026-03-06 10:53:43', NULL),
	('2e363ad1-79c0-496b-9c72-b9c24fbb03a5', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', '331f5339-1774-4b06-9e19-bb88b603c5a2', 'Black', '2026-03-06 10:53:26', '2026-03-06 10:53:43', '2026-03-06 10:53:43'),
	('428fb7f8-4c8d-45e9-bfd3-59863279ac5d', 'c9b49df6-f9fa-4bed-a1c9-66f983210825', '77e03517-d3b5-4a73-9066-0b7c21338c0a', 'Acetate', '2025-12-31 04:06:54', '2025-12-31 04:06:54', NULL),
	('45062bd2-6986-4399-b136-d755b8feca25', '95f03222-664a-4a4c-8dc4-ac006464bbf5', 'fe556900-64e2-4f9a-b9cd-8b7e023a72c6', 'Rectangle', '2026-01-09 03:39:23', '2026-01-09 03:39:23', NULL),
	('50e25b5f-fdf8-42a3-b432-1ef151352f9a', 'c9b49df6-f9fa-4bed-a1c9-66f983210825', '00cbc3c6-f421-4714-b509-e9770e3182d1', '20', '2025-12-31 04:06:54', '2025-12-31 04:06:54', NULL),
	('58585dec-be7a-4c18-99d8-7c67e1dc2336', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', '331f5339-1774-4b06-9e19-bb88b603c5a2', 'Orange', '2026-03-06 08:38:45', '2026-03-06 10:53:26', '2026-03-06 10:53:26'),
	('59843629-0db1-42cd-8c6a-1937c62c4692', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', 'fab9a0b6-5633-43a5-b78a-cd6523e4c406', 'Polycarbonate', '2026-03-06 08:38:45', '2026-03-06 10:53:26', '2026-03-06 10:53:26'),
	('6254658c-9211-4fad-a8aa-bc217aaa53c9', '95f03222-664a-4a4c-8dc4-ac006464bbf5', 'edfee81e-0a02-4e09-b3c4-a4a8cdcee514', 'Single Vision', '2026-01-09 03:39:23', '2026-01-09 03:39:23', NULL),
	('63fbdb33-e493-46d4-b124-5a8be9894e61', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', 'fab9a0b6-5633-43a5-b78a-cd6523e4c406', 'Polycarbonate', '2026-03-06 10:53:43', '2026-03-06 10:53:43', NULL),
	('652d3ccb-8dc8-4214-9a9f-89e01187cea6', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', '77e03517-d3b5-4a73-9066-0b7c21338c0a', 'Titanium', '2026-03-06 08:38:45', '2026-03-06 10:53:26', '2026-03-06 10:53:26'),
	('6df5a743-0d35-4475-8427-a5a6b680a1a9', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', '00cbc3c6-f421-4714-b509-e9770e3182d1', '10', '2026-03-06 08:38:45', '2026-03-06 10:53:43', NULL),
	('6ee5c3af-9117-41c2-ae48-afd3c8fec611', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', 'fe556900-64e2-4f9a-b9cd-8b7e023a72c6', 'Square', '2026-03-06 08:38:45', '2026-03-06 10:53:26', '2026-03-06 10:53:26'),
	('72ed1ec7-06d4-48ee-8671-8063bc441106', '95f03222-664a-4a4c-8dc4-ac006464bbf5', 'fab9a0b6-5633-43a5-b78a-cd6523e4c406', 'Polycarbonate', '2026-01-09 03:39:23', '2026-01-09 03:39:23', NULL),
	('79b682d2-c2d5-45bc-825c-5bddfe2299d8', 'c9b49df6-f9fa-4bed-a1c9-66f983210825', 'edfee81e-0a02-4e09-b3c4-a4a8cdcee514', 'Photochromic Lens', '2025-12-31 04:06:54', '2025-12-31 04:06:54', NULL),
	('7c89ca0c-319c-4b79-b813-7034b0c03dba', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', 'edfee81e-0a02-4e09-b3c4-a4a8cdcee514', 'Blue Light Blocking Lens', '2026-03-06 10:53:26', '2026-03-06 10:53:43', '2026-03-06 10:53:43'),
	('89c2b0d3-0c6c-4209-9b92-5a9802796d81', 'c9b49df6-f9fa-4bed-a1c9-66f983210825', 'fe556900-64e2-4f9a-b9cd-8b7e023a72c6', 'Square', '2025-12-31 04:06:54', '2025-12-31 04:06:54', NULL),
	('9081f50d-797f-4152-bfe9-b0fa3d54427c', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', '77e03517-d3b5-4a73-9066-0b7c21338c0a', 'Titanium', '2026-03-06 10:53:26', '2026-03-06 10:53:43', '2026-03-06 10:53:43'),
	('93794e52-9c63-43a6-afa3-46f680a73e35', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', '331f5339-1774-4b06-9e19-bb88b603c5a2', 'Black', '2026-03-06 08:38:45', '2026-03-06 10:53:26', '2026-03-06 10:53:26'),
	('9eefda96-055f-4c17-8c23-3c1375b04118', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', 'edfee81e-0a02-4e09-b3c4-a4a8cdcee514', 'Photochromic Lens', '2026-03-06 10:53:43', '2026-03-06 10:53:43', NULL),
	('ad35b711-500e-49b4-97ac-0af24fbeac1a', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', '17d811ef-8002-4db7-8cbd-6f012ad12028', '1', '2026-03-06 08:38:45', '2026-03-06 10:53:43', NULL),
	('b1dabc93-c1af-49d6-9ab4-86a35cf432ae', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', '331f5339-1774-4b06-9e19-bb88b603c5a2', 'Orange', '2026-03-06 10:53:43', '2026-03-06 10:53:43', NULL),
	('ba7ad5d3-ccac-4fca-9b13-c2cd2a91b619', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', '331f5339-1774-4b06-9e19-bb88b603c5a2', 'Orange', '2026-03-06 10:53:26', '2026-03-06 10:53:43', '2026-03-06 10:53:43'),
	('bee87501-d5eb-49e1-a964-f3464fde951f', '95f03222-664a-4a4c-8dc4-ac006464bbf5', 'dbc661e8-ad9c-4dfe-8fe5-40707210c3f3', '2', '2026-01-09 03:39:23', '2026-01-09 03:39:23', NULL),
	('c12228f2-6acb-4039-a8b9-ea93bec43f63', '95f03222-664a-4a4c-8dc4-ac006464bbf5', '17d811ef-8002-4db7-8cbd-6f012ad12028', '1', '2026-01-09 03:39:23', '2026-01-09 03:39:23', NULL),
	('c2b87a80-3510-4e4f-bfc7-401ea1582eb9', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', 'edfee81e-0a02-4e09-b3c4-a4a8cdcee514', 'Blue Light Blocking Lens', '2026-03-06 08:38:45', '2026-03-06 10:53:26', '2026-03-06 10:53:26'),
	('c4516dc8-a002-49ad-8cb1-8a14ab194eaa', 'c9b49df6-f9fa-4bed-a1c9-66f983210825', '331f5339-1774-4b06-9e19-bb88b603c5a2', 'Black', '2025-12-31 04:06:54', '2025-12-31 04:06:54', NULL),
	('cf5ba2a6-8254-4be8-ba3c-c7eb7b9232a3', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', 'edfee81e-0a02-4e09-b3c4-a4a8cdcee514', 'Photochromic Lens', '2026-03-06 10:53:26', '2026-03-06 10:53:43', '2026-03-06 10:53:43'),
	('d172243a-9308-4ebd-99b3-ee5177f53001', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', '331f5339-1774-4b06-9e19-bb88b603c5a2', 'Brown', '2026-03-06 10:53:43', '2026-03-06 10:53:43', NULL),
	('d7f39532-295a-4a03-b85b-27deae983f24', '95f03222-664a-4a4c-8dc4-ac006464bbf5', '00cbc3c6-f421-4714-b509-e9770e3182d1', '20', '2026-01-09 03:39:23', '2026-01-09 03:39:23', NULL),
	('d9f99c04-edbb-43d3-a5f9-4fbc69e5148f', '95f03222-664a-4a4c-8dc4-ac006464bbf5', '331f5339-1774-4b06-9e19-bb88b603c5a2', 'Orange', '2026-01-09 03:39:23', '2026-01-09 03:39:23', NULL),
	('da6df2d4-191c-4b77-898f-73c17470f9ef', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', 'edfee81e-0a02-4e09-b3c4-a4a8cdcee514', 'Photochromic Lens', '2026-03-06 08:38:45', '2026-03-06 10:53:26', '2026-03-06 10:53:26'),
	('dc66466c-ce7d-4eec-ac2b-328a1a4299da', '95f03222-664a-4a4c-8dc4-ac006464bbf5', '331f5339-1774-4b06-9e19-bb88b603c5a2', 'Brown', '2026-01-09 03:39:23', '2026-01-09 03:39:23', NULL),
	('e6c5d439-276a-458e-ba8b-a06ae93e96e2', 'c9b49df6-f9fa-4bed-a1c9-66f983210825', 'dbc661e8-ad9c-4dfe-8fe5-40707210c3f3', '2', '2025-12-31 04:06:54', '2025-12-31 04:06:54', NULL),
	('e8e3af99-aed1-4612-bc59-1b5458861197', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', 'dbc661e8-ad9c-4dfe-8fe5-40707210c3f3', '2', '2026-03-06 08:38:45', '2026-03-06 10:53:43', NULL),
	('ec008579-feef-4c84-8e77-454ef4426ecd', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', 'fab9a0b6-5633-43a5-b78a-cd6523e4c406', 'Polycarbonate', '2026-03-06 10:53:26', '2026-03-06 10:53:43', '2026-03-06 10:53:43'),
	('fda6d99a-40b2-4805-a1b4-97cd88a0985a', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', '331f5339-1774-4b06-9e19-bb88b603c5a2', 'Brown', '2026-03-06 10:53:26', '2026-03-06 10:53:43', '2026-03-06 10:53:43'),
	('ff6cd206-8f81-4347-b64c-9a95a3c7d532', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', 'fe556900-64e2-4f9a-b9cd-8b7e023a72c6', 'Square', '2026-03-06 10:53:26', '2026-03-06 10:53:43', '2026-03-06 10:53:43');

-- Dumping structure for table pos_optik.product_categories
CREATE TABLE IF NOT EXISTS `product_categories` (
  `category_id` char(36) NOT NULL,
  `category_name` varchar(50) NOT NULL,
  `category_description` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table pos_optik.product_categories: ~5 rows (approximately)
INSERT INTO `product_categories` (`category_id`, `category_name`, `category_description`, `created_at`, `updated_at`, `deleted_at`) VALUES
	('2f78a0dd-71da-4b07-a7be-e79f913ba9d3', 'Softlens', 'Daily and specialty contact lenses', '2025-12-31 03:41:34', '2026-01-14 01:57:04', NULL),
	('8a94d72c-a8f2-4205-9e2e-e6a0bd00927a', 'Sunglasses', 'Various kinds of men\'s and women\'s glasses', '2025-12-31 03:41:34', '2026-01-14 01:56:50', NULL),
	('8da318e2-5454-4346-a95b-2f28208e6ea6', 'Contact Lens', 'Various contact lens brands tailored to your daily comfort.', '2026-01-14 02:01:01', '2026-01-14 02:01:01', NULL),
	('c32aaaf0-fb8b-4c6a-8fd1-87f9f8cb3f86', 'Brand', 'Various trusted brands to suit your vision and style needs', '2026-01-14 01:59:22', '2026-01-14 02:03:02', '2026-01-14 02:03:02'),
	('d949b9a9-71a9-4d29-b274-e8574dcd4fec', 'Frames', 'Various eyeglass frames for your style needs', '2025-12-31 03:41:34', '2026-01-14 01:58:58', NULL);

-- Dumping structure for table pos_optik.product_discounts
CREATE TABLE IF NOT EXISTS `product_discounts` (
  `product_discount_id` char(36) NOT NULL,
  `product_id` char(36) NOT NULL,
  `discount_type` varchar(20) NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`product_discount_id`),
  KEY `product_discounts_product_id_foreign` (`product_id`),
  CONSTRAINT `product_discounts_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table pos_optik.product_discounts: ~0 rows (approximately)

-- Dumping structure for table pos_optik.product_images
CREATE TABLE IF NOT EXISTS `product_images` (
  `product_image_id` char(36) NOT NULL,
  `product_id` char(36) NOT NULL,
  `url` varchar(1024) NOT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `type` enum('gallery','variant') NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `mime_type` varchar(50) DEFAULT NULL,
  `size_bytes` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`product_image_id`),
  KEY `product_images_product_id_foreign` (`product_id`),
  CONSTRAINT `product_images_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table pos_optik.product_images: ~14 rows (approximately)
INSERT INTO `product_images` (`product_image_id`, `product_id`, `url`, `alt_text`, `sort_order`, `type`, `is_primary`, `mime_type`, `size_bytes`, `created_at`, `updated_at`, `deleted_at`) VALUES
	('2597e5e7-e79c-465d-9e0a-25ed49f5d3c1', 'c0e93d11-b193-4355-bc9a-6d00d0492fb4', 'https://cdn.adefoodwaste.biz.id/1768378522_c8ecec6de7ec76f2e43a.jpg', '1 DAY ACUVUE DEFINE', 0, 'gallery', 0, 'image/jpeg', 80627, '2026-01-14 08:15:22', '2026-01-14 08:15:22', NULL),
	('302d7bf8-1d6f-46c2-9307-47ac2d868e9c', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', 'https://cdn.adefoodwaste.biz.id/1772769223_13b97a80172a244e87af.jpg', 'Blue Light Blocking Lens', 0, 'variant', 0, 'image/jpeg', 83829, '2026-03-06 10:53:44', '2026-03-06 10:53:44', NULL),
	('3d19334e-b184-4642-9d7a-48bb9589dad5', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', 'https://cdn.adefoodwaste.biz.id/1772761123_e3001128986d8b787b78.png', 'Kacamata Uhuy', 0, 'gallery', 1, 'image/png', 272764, '2026-03-06 08:38:44', '2026-03-06 08:38:44', NULL),
	('5e6244fb-6bb6-4300-bd0e-fd4f2d0be785', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', 'https://cdn.adefoodwaste.biz.id/1772761125_68278c5d997ec42cd051.png', 'Kacamata Uhuy', 0, 'gallery', 0, 'image/png', 278271, '2026-03-06 08:38:45', '2026-03-06 08:38:45', NULL),
	('67838bb9-ea2b-4983-9c4b-c1d125b8dc7b', '95f03222-664a-4a4c-8dc4-ac006464bbf5', 'https://cdn.adefoodwaste.biz.id/1767929962_71838c6946e7b01f2abc.png', 'F NJ 5095AF 016 51', 0, 'gallery', 1, 'image/png', 226526, '2026-01-09 03:39:23', '2026-01-09 03:39:23', NULL),
	('6a7f8274-afa7-4326-b5a1-fd70ed031d03', '95f03222-664a-4a4c-8dc4-ac006464bbf5', 'https://cdn.adefoodwaste.biz.id/1767929963_aa9aac288546c65ea244.png', 'Orange - Single Vision', 0, 'variant', 0, 'image/png', 208537, '2026-01-09 03:39:23', '2026-01-09 03:39:23', NULL),
	('7512f468-7551-4c7e-a38e-36a963aac4ce', 'c0e93d11-b193-4355-bc9a-6d00d0492fb4', 'https://cdn.adefoodwaste.biz.id/1768378522_64ca744ff784431f0850.jpg', '1 DAY ACUVUE DEFINE', 0, 'gallery', 0, 'image/jpeg', 101246, '2026-01-14 08:15:23', '2026-01-14 08:15:23', NULL),
	('960192d6-ab80-47fc-9849-db78ab78057d', '95f03222-664a-4a4c-8dc4-ac006464bbf5', 'https://cdn.adefoodwaste.biz.id/1767929960_10c6a60f9188cbc4c249.png', 'F NJ 5095AF 016 51', 0, 'gallery', 0, 'image/png', 236352, '2026-01-09 03:39:21', '2026-01-09 03:39:21', NULL),
	('991e848d-335f-4461-9dc7-7085c4e4cc1b', 'c0e93d11-b193-4355-bc9a-6d00d0492fb4', 'https://cdn.adefoodwaste.biz.id/1768378521_3b420e43f012ed066f94.jpg', '1 DAY ACUVUE DEFINE', 0, 'gallery', 1, 'image/jpeg', 87754, '2026-01-14 08:15:22', '2026-01-14 08:15:22', NULL),
	('9bce3c8d-ae85-495f-b023-cf4dc56b897d', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', 'https://cdn.adefoodwaste.biz.id/1772761124_52878a0551e3d499bd37.png', 'Kacamata Uhuy', 0, 'gallery', 0, 'image/png', 270471, '2026-03-06 08:38:45', '2026-03-06 08:38:45', NULL),
	('a47a1eb7-48ea-444e-a41a-75b20b1f8e27', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', 'https://cdn.adefoodwaste.biz.id/1772761125_ca6bfde2e0a9cbd024a4.png', 'Photochromic Lens', 0, 'variant', 0, 'image/png', 159098, '2026-03-06 08:38:46', '2026-03-06 08:38:46', NULL),
	('bad9d52d-47fa-42c8-b12e-e68a38b95825', '95f03222-664a-4a4c-8dc4-ac006464bbf5', 'https://cdn.adefoodwaste.biz.id/1767929961_7ca56e47e0509d1dcc52.png', 'F NJ 5095AF 016 51', 0, 'gallery', 0, 'image/png', 234838, '2026-01-09 03:39:22', '2026-01-09 03:39:22', NULL),
	('f400bd0c-dd5b-4aca-8088-26ea33e661c9', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', 'https://cdn.adefoodwaste.biz.id/1772761126_914c4b3a8b8ed39e2e77.png', 'Blue Light Blocking Lens', 0, 'variant', 0, 'image/png', 245865, '2026-03-06 08:38:46', '2026-03-06 10:53:43', '2026-03-06 10:53:43'),
	('fedde569-2aa8-4916-ae57-7cfa01612e92', '95f03222-664a-4a4c-8dc4-ac006464bbf5', 'https://cdn.adefoodwaste.biz.id/1767929963_e613acb50e00729a4777.png', 'Brown - Single Vision', 0, 'variant', 0, 'image/png', 156450, '2026-01-09 03:39:24', '2026-01-09 03:39:24', NULL);

-- Dumping structure for table pos_optik.product_variants
CREATE TABLE IF NOT EXISTS `product_variants` (
  `variant_id` char(36) NOT NULL,
  `product_id` char(36) NOT NULL,
  `variant_name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`variant_id`),
  KEY `product_variants_product_id_foreign` (`product_id`),
  CONSTRAINT `product_variants_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table pos_optik.product_variants: ~4 rows (approximately)
INSERT INTO `product_variants` (`variant_id`, `product_id`, `variant_name`, `price`, `stock`, `created_at`, `updated_at`, `deleted_at`) VALUES
	('229eff63-140c-4ceb-9d5c-76ed55953372', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', 'Photochromic Lens', 1500000.00, 99, '2026-03-06 08:38:45', '2026-03-06 11:10:58', NULL),
	('37feb422-6150-4909-9384-5c632bdd417f', '95f03222-664a-4a4c-8dc4-ac006464bbf5', 'Orange - Single Vision', 1200000.00, 98, '2026-01-09 03:39:23', '2026-03-06 14:22:01', NULL),
	('d767a352-7841-4ae0-8326-1392efedd61b', '95f03222-664a-4a4c-8dc4-ac006464bbf5', 'Brown - Single Vision', 1300000.00, 0, '2026-01-09 03:39:23', '2026-01-13 06:32:21', NULL),
	('e9f2f55c-81a2-418c-b85d-6e2e4e3c0e61', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', 'Blue Light Blocking Lens', 1200000.00, 49, '2026-03-06 08:38:46', '2026-03-06 11:10:58', NULL);

-- Dumping structure for table pos_optik.product_variant_attributes
CREATE TABLE IF NOT EXISTS `product_variant_attributes` (
  `pva_id` char(36) NOT NULL,
  `product_id` char(36) NOT NULL,
  `attribute_id` char(36) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`pva_id`),
  UNIQUE KEY `product_id_attribute_id` (`product_id`,`attribute_id`),
  KEY `product_variant_attributes_attribute_id_foreign` (`attribute_id`),
  CONSTRAINT `product_variant_attributes_attribute_id_foreign` FOREIGN KEY (`attribute_id`) REFERENCES `product_attributes` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `product_variant_attributes_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table pos_optik.product_variant_attributes: ~3 rows (approximately)
INSERT INTO `product_variant_attributes` (`pva_id`, `product_id`, `attribute_id`, `created_at`) VALUES
	('0894acb7-e0a4-4aa2-996c-b545444b4ddd', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', 'edfee81e-0a02-4e09-b3c4-a4a8cdcee514', NULL),
	('27417181-c5de-425a-8a50-70c2a2d41397', '95f03222-664a-4a4c-8dc4-ac006464bbf5', 'edfee81e-0a02-4e09-b3c4-a4a8cdcee514', NULL),
	('987c5142-2b73-4200-8fbf-a7410b678829', '95f03222-664a-4a4c-8dc4-ac006464bbf5', '331f5339-1774-4b06-9e19-bb88b603c5a2', NULL);

-- Dumping structure for table pos_optik.product_variant_images
CREATE TABLE IF NOT EXISTS `product_variant_images` (
  `pv_image_id` char(36) NOT NULL,
  `variant_id` char(36) NOT NULL,
  `product_image_id` char(36) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`pv_image_id`),
  KEY `product_variant_images_variant_id_foreign` (`variant_id`),
  KEY `product_variant_images_product_image_id_foreign` (`product_image_id`),
  CONSTRAINT `product_variant_images_product_image_id_foreign` FOREIGN KEY (`product_image_id`) REFERENCES `product_images` (`product_image_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `product_variant_images_variant_id_foreign` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`variant_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table pos_optik.product_variant_images: ~5 rows (approximately)
INSERT INTO `product_variant_images` (`pv_image_id`, `variant_id`, `product_image_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
	('71182e1d-4f47-47e3-9913-0b44ce234c14', 'e9f2f55c-81a2-418c-b85d-6e2e4e3c0e61', 'f400bd0c-dd5b-4aca-8088-26ea33e661c9', '2026-03-06 08:38:46', '2026-03-06 10:53:43', '2026-03-06 10:53:43'),
	('92f74f72-7156-4c83-8f41-6e9d0126132b', 'e9f2f55c-81a2-418c-b85d-6e2e4e3c0e61', '302d7bf8-1d6f-46c2-9307-47ac2d868e9c', '2026-03-06 10:53:44', '2026-03-06 10:53:44', NULL),
	('932137b8-b218-4a1a-a1d5-caf01a2c1ea6', '37feb422-6150-4909-9384-5c632bdd417f', '6a7f8274-afa7-4326-b5a1-fd70ed031d03', '2026-01-09 03:39:23', '2026-01-09 03:39:23', NULL),
	('a502a95d-aa15-4440-802e-63ca105c6134', 'd767a352-7841-4ae0-8326-1392efedd61b', 'fedde569-2aa8-4916-ae57-7cfa01612e92', '2026-01-09 03:39:24', '2026-01-09 03:39:24', NULL),
	('da817182-6cc2-4c7e-90d0-c98b5c10d16a', '229eff63-140c-4ceb-9d5c-76ed55953372', 'a47a1eb7-48ea-444e-a41a-75b20b1f8e27', '2026-03-06 08:38:46', '2026-03-06 08:38:46', NULL);

-- Dumping structure for table pos_optik.product_variant_values
CREATE TABLE IF NOT EXISTS `product_variant_values` (
  `pv_value_id` char(36) NOT NULL,
  `variant_id` char(36) NOT NULL,
  `pav_id` char(36) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`pv_value_id`),
  KEY `product_variant_values_variant_id_foreign` (`variant_id`),
  KEY `product_variant_values_pav_id_foreign` (`pav_id`),
  CONSTRAINT `product_variant_values_pav_id_foreign` FOREIGN KEY (`pav_id`) REFERENCES `product_attribute_values` (`pav_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `product_variant_values_variant_id_foreign` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`variant_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table pos_optik.product_variant_values: ~10 rows (approximately)
INSERT INTO `product_variant_values` (`pv_value_id`, `variant_id`, `pav_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
	('52fb06c3-941c-411f-ba32-cc338a8850cf', 'e9f2f55c-81a2-418c-b85d-6e2e4e3c0e61', '17f161f7-75e5-4bf0-baff-48fcac3618f7', '2026-03-06 10:53:44', '2026-03-06 10:53:44', NULL),
	('6eb7aa38-bf72-405e-bcdb-3da2fced4734', 'd767a352-7841-4ae0-8326-1392efedd61b', '6254658c-9211-4fad-a8aa-bc217aaa53c9', '2026-01-09 03:39:24', '2026-01-09 03:39:24', NULL),
	('7fc6511b-61ce-45a6-a48b-b30669447e78', '229eff63-140c-4ceb-9d5c-76ed55953372', '79b682d2-c2d5-45bc-825c-5bddfe2299d8', '2026-03-06 08:38:46', '2026-03-06 10:53:26', '2026-03-06 10:53:26'),
	('83b70a7c-6d4d-4b20-bf6f-aa3e9d9af5ef', '37feb422-6150-4909-9384-5c632bdd417f', 'd9f99c04-edbb-43d3-a5f9-4fbc69e5148f', '2026-01-09 03:39:23', '2026-01-09 03:39:23', NULL),
	('89bb91f7-d7ec-4057-afe9-ba642838e93c', 'e9f2f55c-81a2-418c-b85d-6e2e4e3c0e61', 'c2b87a80-3510-4e4f-bfc7-401ea1582eb9', '2026-03-06 08:38:46', '2026-03-06 10:53:26', '2026-03-06 10:53:26'),
	('9f00c852-9589-4a9c-b742-9fdab3c7a797', '37feb422-6150-4909-9384-5c632bdd417f', '6254658c-9211-4fad-a8aa-bc217aaa53c9', '2026-01-09 03:39:23', '2026-01-09 03:39:23', NULL),
	('aa8c5fa9-9435-4780-93d0-bbba1b1bb195', 'd767a352-7841-4ae0-8326-1392efedd61b', '19845266-e68c-4233-b8fc-f5fddab4673f', '2026-01-09 03:39:24', '2026-01-09 03:39:24', NULL),
	('c1a12e90-691c-4cd4-be33-b199fff4dabf', 'e9f2f55c-81a2-418c-b85d-6e2e4e3c0e61', '7c89ca0c-319c-4b79-b813-7034b0c03dba', '2026-03-06 10:53:26', '2026-03-06 10:53:44', '2026-03-06 10:53:44'),
	('e190c45f-43c8-4fd1-8b85-b06fbfd2cacb', '229eff63-140c-4ceb-9d5c-76ed55953372', 'cf5ba2a6-8254-4be8-ba3c-c7eb7b9232a3', '2026-03-06 10:53:26', '2026-03-06 10:53:43', '2026-03-06 10:53:43'),
	('e49cfe16-6023-4305-9fb7-5e3a3dd557d0', '229eff63-140c-4ceb-9d5c-76ed55953372', '9eefda96-055f-4c17-8c23-3c1375b04118', '2026-03-06 10:53:43', '2026-03-06 10:53:43', NULL);

-- Dumping structure for table pos_optik.reviews
CREATE TABLE IF NOT EXISTS `reviews` (
  `review_id` char(36) NOT NULL,
  `customer_id` char(36) DEFAULT NULL,
  `product_id` char(36) NOT NULL,
  `rating` int NOT NULL,
  `comment` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`review_id`),
  KEY `reviews_customer_id_foreign` (`customer_id`),
  KEY `reviews_product_id_foreign` (`product_id`),
  CONSTRAINT `reviews_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE ON UPDATE SET NULL,
  CONSTRAINT `reviews_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table pos_optik.reviews: ~2 rows (approximately)
INSERT INTO `reviews` (`review_id`, `customer_id`, `product_id`, `rating`, `comment`, `created_at`, `updated_at`, `deleted_at`) VALUES
	('c916584a-90f7-42fc-8958-b714e5cb4ece', 'c88af1e9-b882-4597-82ca-d414942926e0', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', 5, 'Mantab', '2026-03-06 13:20:37', '2026-03-06 13:20:37', NULL),
	('d0695491-ffa2-411d-a52c-1753f072fdfe', 'c88af1e9-b882-4597-82ca-d414942926e0', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', 5, 'Mantab', '2026-03-06 13:20:38', '2026-03-06 13:20:38', NULL),
	('eae5f308-d0c2-48b9-a709-641c3a13e415', 'c88af1e9-b882-4597-82ca-d414942926e0', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', 5, 'Very Good', '2026-03-06 13:47:02', '2026-03-06 13:47:02', NULL);

-- Dumping structure for table pos_optik.review_media
CREATE TABLE IF NOT EXISTS `review_media` (
  `review_media_id` char(36) NOT NULL,
  `review_id` char(36) NOT NULL,
  `file_url` text NOT NULL,
  `file_type` varchar(20) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`review_media_id`),
  KEY `review_media_review_id_foreign` (`review_id`),
  CONSTRAINT `review_media_review_id_foreign` FOREIGN KEY (`review_id`) REFERENCES `reviews` (`review_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table pos_optik.review_media: ~0 rows (approximately)
INSERT INTO `review_media` (`review_media_id`, `review_id`, `file_url`, `file_type`, `created_at`, `updated_at`, `deleted_at`) VALUES
	('05f18bdb-c301-4f3a-b660-be51b2f21a8e', 'eae5f308-d0c2-48b9-a709-641c3a13e415', 'https://cdn.adefoodwaste.biz.id/reviews/images/1772779622_bc7a43d3aacdd05acfdd.png', 'image', '2026-03-06 13:47:03', '2026-03-06 13:47:03', NULL),
	('46f59cb1-bfd5-4e70-a9f3-a464d2ef562c', 'eae5f308-d0c2-48b9-a709-641c3a13e415', 'https://cdn.adefoodwaste.biz.id/reviews/videos/1772779623_0f148a26c0e94323bd1b.mp4', 'video', '2026-03-06 13:47:03', '2026-03-06 13:47:03', NULL);

-- Dumping structure for table pos_optik.roles
CREATE TABLE IF NOT EXISTS `roles` (
  `role_id` char(36) NOT NULL,
  `role_name` varchar(100) NOT NULL,
  `role_description` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `role_name` (`role_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table pos_optik.roles: ~5 rows (approximately)
INSERT INTO `roles` (`role_id`, `role_name`, `role_description`, `created_at`, `updated_at`, `deleted_at`) VALUES
	('1ac84283-2aa1-45b0-b00a-522c15ea7d2c', 'cashier', 'Kasir yang menangani transaksi penjualan', '2025-12-31 03:41:32', '2025-12-31 03:41:32', NULL),
	('282e305d-dc3e-4194-85b0-612d1e1e26b2', 'optometrist', 'Dokter mata yang melakukan pemeriksaan', '2025-12-31 03:41:32', '2025-12-31 03:41:32', NULL),
	('9bd1d0cb-f353-423d-aab7-20f90f393b2e', 'admin', 'Administrator dengan akses penuh', '2025-12-31 03:41:32', '2025-12-31 03:41:32', NULL),
	('a66413db-2198-4425-99b6-f6aa4c9beb06', 'customer', 'Customer/Buyer', '2025-12-31 03:41:32', '2025-12-31 03:41:32', NULL),
	('afef13c9-a847-4400-9e9d-7d5aca151673', 'inventory', 'Staff gudang mengelola produk', '2025-12-31 03:41:32', '2025-12-31 03:41:32', NULL);

-- Dumping structure for table pos_optik.shipping_methods
CREATE TABLE IF NOT EXISTS `shipping_methods` (
  `shipping_method_id` char(36) NOT NULL,
  `name` varchar(100) NOT NULL,
  `provider` varchar(50) NOT NULL,
  `estimated_days` varchar(20) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`shipping_method_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table pos_optik.shipping_methods: ~0 rows (approximately)
INSERT INTO `shipping_methods` (`shipping_method_id`, `name`, `provider`, `estimated_days`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
	('3e08ee99-750a-4437-a3a9-922437410f6e', 'Reguler', 'Internal Courier', '3-5 hari', 1, NULL, NULL, NULL);

-- Dumping structure for table pos_optik.shipping_rates
CREATE TABLE IF NOT EXISTS `shipping_rates` (
  `rate_id` char(36) NOT NULL,
  `shipping_method_id` char(36) NOT NULL,
  `destination` varchar(200) NOT NULL,
  `cost` decimal(10,2) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`rate_id`),
  KEY `shipping_rates_shipping_method_id_foreign` (`shipping_method_id`),
  CONSTRAINT `shipping_rates_shipping_method_id_foreign` FOREIGN KEY (`shipping_method_id`) REFERENCES `shipping_methods` (`shipping_method_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table pos_optik.shipping_rates: ~9 rows (approximately)
INSERT INTO `shipping_rates` (`rate_id`, `shipping_method_id`, `destination`, `cost`, `created_at`, `updated_at`, `deleted_at`) VALUES
	('18fbe669-5ec4-4ef1-b6c3-f6df20362123', '3e08ee99-750a-4437-a3a9-922437410f6e', 'Papua', 60000.00, '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL),
	('7c9a563a-ab36-413c-a5a0-a8521c17e0e1', '3e08ee99-750a-4437-a3a9-922437410f6e', 'Jawa Barat', 20000.00, '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL),
	('83308b9e-0f76-4905-84bf-2a59b1e1bae5', '3e08ee99-750a-4437-a3a9-922437410f6e', 'Sulawesi', 45000.00, '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL),
	('8f8af33e-ecd4-4406-9763-f190b477a7a3', '3e08ee99-750a-4437-a3a9-922437410f6e', 'Kalimantan', 40000.00, '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL),
	('bbdfca15-b93a-4014-bf16-6d8790d32ed3', '3e08ee99-750a-4437-a3a9-922437410f6e', 'Jawa Timur', 15000.00, '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL),
	('cc00254f-75d7-4404-bf7f-ee00c91b9ac7', '3e08ee99-750a-4437-a3a9-922437410f6e', 'Jakarta', 20000.00, '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL),
	('e110b72c-47b3-4a5f-bc89-305c4d00c97d', '3e08ee99-750a-4437-a3a9-922437410f6e', 'Sumatra', 35000.00, '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL),
	('fcc7e8c3-75e0-43c0-8419-6e7dd7345404', '3e08ee99-750a-4437-a3a9-922437410f6e', 'Jawa Tengah', 17000.00, '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL),
	('fdf72b7b-96d2-4390-9fe6-042357a6088a', '3e08ee99-750a-4437-a3a9-922437410f6e', 'Bali', 25000.00, '2025-12-31 03:41:34', '2025-12-31 03:41:34', NULL);

-- Dumping structure for table pos_optik.users
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` char(36) NOT NULL,
  `role_id` char(36) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_email` (`user_email`),
  KEY `fk_users_roles` (`role_id`),
  CONSTRAINT `fk_users_roles` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table pos_optik.users: ~5 rows (approximately)
INSERT INTO `users` (`user_id`, `role_id`, `user_name`, `user_email`, `password`, `created_at`, `updated_at`, `deleted_at`) VALUES
	('62341e51-03d8-4e2a-856a-3f37dc48f05c', '282e305d-dc3e-4194-85b0-612d1e1e26b2', 'Dr. Mata', 'optometrist@gmail.com', '$2y$10$z3KCh8XCDQmOAaXY/ymyuOwETAJ5StMsIg1IuzeEq8wpCsa2un/B6', '2025-12-31 03:41:33', '2025-12-31 03:41:33', NULL),
	('7eb49f73-a478-433b-ae08-257faa299092', 'afef13c9-a847-4400-9e9d-7d5aca151673', 'Petugas Gudang', 'inventory@gmail.com', '$2y$10$LPAGWYTgT2nTci/D6AgpK.KpGfOcdtqM80NS4iwDp4NN1lVK2meWu', '2025-12-31 03:41:33', '2025-12-31 03:41:33', NULL),
	('9b495fef-9a19-4046-8537-beeae2ff70fd', '1ac84283-2aa1-45b0-b00a-522c15ea7d2c', 'Kasir Toko', 'cashier@gmail.com', '$2y$10$F4QTB/./eirGV3FU5oBzu.1pgt6vnJVyqhSIXmGETGQAIIcs6Uq6S', '2025-12-31 03:41:33', '2025-12-31 03:41:33', NULL),
	('bcd30656-71bc-4553-a2d5-2c72c2299ef4', '9bd1d0cb-f353-423d-aab7-20f90f393b2e', 'Admin Super', 'admin@gmail.com', '$2y$10$UlTFBxTv83IYmH5vTy9yHuKb4av/HIa/xfAvH1lVqFwuylUqbh00S', '2025-12-31 03:41:33', '2025-12-31 03:41:33', NULL),
	('bf2829a6-4bc9-470e-ad4b-6185408220f4', 'a66413db-2198-4425-99b6-f6aa4c9beb06', 'Customer', 'customer@gmail.com', '$2y$10$UAj.OY7ksp7R26ViE4E9YuoomG1PiWGcsS1LO1zg/GRuPY7GLAQm6', '2025-12-31 03:41:33', '2025-12-31 03:41:33', NULL);

-- Dumping structure for table pos_optik.user_activities
CREATE TABLE IF NOT EXISTS `user_activities` (
  `user_activity_id` char(36) NOT NULL,
  `customer_id` char(36) DEFAULT NULL,
  `product_id` char(36) DEFAULT NULL,
  `activity_type` varchar(50) NOT NULL,
  `activity_details` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`user_activity_id`),
  KEY `user_activities_customer_id_foreign` (`customer_id`),
  KEY `user_activities_product_id_foreign` (`product_id`),
  CONSTRAINT `user_activities_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE ON UPDATE SET NULL,
  CONSTRAINT `user_activities_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table pos_optik.user_activities: ~0 rows (approximately)

-- Dumping structure for table pos_optik.user_refund_accounts
CREATE TABLE IF NOT EXISTS `user_refund_accounts` (
  `user_refund_account_id` char(36) NOT NULL,
  `customer_id` char(36) NOT NULL,
  `account_name` varchar(150) NOT NULL,
  `bank_name` varchar(100) NOT NULL,
  `account_number` varchar(50) NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`user_refund_account_id`),
  KEY `customer_id` (`customer_id`),
  CONSTRAINT `user_refund_accounts_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table pos_optik.user_refund_accounts: ~4 rows (approximately)
INSERT INTO `user_refund_accounts` (`user_refund_account_id`, `customer_id`, `account_name`, `bank_name`, `account_number`, `is_default`, `created_at`, `updated_at`, `deleted_at`) VALUES
	('0d947b70-1180-4dd1-958f-0008095378f9', '025f77f1-dbb3-49d8-bb35-01e908a8e181', 'Dystian En Yusgiantoroo', 'BCAAAAAA', '081928938', 1, '2026-01-21 15:38:13', '2026-01-30 17:04:22', NULL),
	('34343708-2418-41e3-ab9a-c7ba2620d893', '04c82b5b-ea89-4a15-9fa8-8deec59610b2', 'John Doe', 'BCA', '819829289333', 0, '2026-02-03 16:32:35', '2026-02-03 16:32:35', NULL),
	('5c25c14c-bebf-4ae9-b670-726c75e0d68c', 'c88af1e9-b882-4597-82ca-d414942926e0', 'Imam', 'BCA', '093290392', 0, '2026-03-06 11:10:41', '2026-03-06 11:10:41', NULL),
	('de32d740-8243-4a54-92b4-db70bac6faaf', '17f27383-11c0-4a1d-84a6-49c1af625b2b', 'Joh Doe', 'BCA', '1829839282', 0, '2026-02-03 16:50:48', '2026-02-03 16:50:48', NULL);

-- Dumping structure for table pos_optik.wishlists
CREATE TABLE IF NOT EXISTS `wishlists` (
  `wishlist_id` char(36) NOT NULL,
  `customer_id` char(36) NOT NULL,
  `product_id` char(36) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`wishlist_id`),
  KEY `wishlists_customer_id_foreign` (`customer_id`),
  KEY `wishlists_product_id_foreign` (`product_id`),
  CONSTRAINT `wishlists_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `wishlists_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dumping data for table pos_optik.wishlists: ~31 rows (approximately)
INSERT INTO `wishlists` (`wishlist_id`, `customer_id`, `product_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
	('01670350-5861-4af9-abd4-ebcb118c3d58', '025f77f1-dbb3-49d8-bb35-01e908a8e181', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', '2026-03-06 09:28:41', '2026-03-06 09:29:10', '2026-03-06 09:29:10'),
	('03193ae7-9f54-4580-b005-1523c855ddce', '025f77f1-dbb3-49d8-bb35-01e908a8e181', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', '2026-03-06 09:12:33', '2026-03-06 09:14:00', '2026-03-06 09:14:00'),
	('03b578b5-3340-46a5-a0d5-ece3a4c55aff', '025f77f1-dbb3-49d8-bb35-01e908a8e181', '95f03222-664a-4a4c-8dc4-ac006464bbf5', '2026-01-14 06:43:39', '2026-01-14 06:43:40', '2026-01-14 06:43:40'),
	('177bc112-68c0-4e78-b37e-f6d5b6f2594c', '025f77f1-dbb3-49d8-bb35-01e908a8e181', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', '2026-03-06 09:23:27', '2026-03-06 09:23:34', '2026-03-06 09:23:34'),
	('2a73deec-da1b-46f6-868c-29e0bd99737b', '025f77f1-dbb3-49d8-bb35-01e908a8e181', '95f03222-664a-4a4c-8dc4-ac006464bbf5', '2026-01-14 06:43:42', '2026-01-14 06:45:34', '2026-01-14 06:45:34'),
	('2bbfc4fe-a0f3-47db-8f42-4fccc10b0024', '025f77f1-dbb3-49d8-bb35-01e908a8e181', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', '2026-03-06 09:15:02', '2026-03-06 09:15:07', '2026-03-06 09:15:07'),
	('320221fe-0cac-47ae-8df6-b3a8446b7bbf', '025f77f1-dbb3-49d8-bb35-01e908a8e181', '95f03222-664a-4a4c-8dc4-ac006464bbf5', '2026-01-14 06:43:36', '2026-01-14 06:43:38', '2026-01-14 06:43:38'),
	('53d8fb19-d666-4c1e-8009-cd5f43da908c', '025f77f1-dbb3-49d8-bb35-01e908a8e181', '95f03222-664a-4a4c-8dc4-ac006464bbf5', '2026-01-14 07:00:50', '2026-01-14 07:01:15', '2026-01-14 07:01:15'),
	('603551ee-eda2-4810-a376-63ae4a4aa49c', '025f77f1-dbb3-49d8-bb35-01e908a8e181', '95f03222-664a-4a4c-8dc4-ac006464bbf5', '2026-01-14 07:02:31', '2026-01-14 07:04:51', '2026-01-14 07:04:51'),
	('615bb2b9-37e7-460e-bd8e-07f1f9da01d8', '025f77f1-dbb3-49d8-bb35-01e908a8e181', '95f03222-664a-4a4c-8dc4-ac006464bbf5', '2026-01-14 07:06:06', '2026-01-14 07:06:13', '2026-01-14 07:06:13'),
	('67932782-9f51-42b4-918c-7c77d3959cb1', '025f77f1-dbb3-49d8-bb35-01e908a8e181', '95f03222-664a-4a4c-8dc4-ac006464bbf5', '2026-01-14 07:11:51', '2026-01-14 07:19:13', '2026-01-14 07:19:13'),
	('6d3e1dc3-4c50-4e4a-ae87-6f8ddac46df2', '025f77f1-dbb3-49d8-bb35-01e908a8e181', '95f03222-664a-4a4c-8dc4-ac006464bbf5', '2026-01-14 07:02:04', '2026-01-14 07:02:05', '2026-01-14 07:02:05'),
	('7120b162-d064-4cba-ac9f-4c18263753d9', '025f77f1-dbb3-49d8-bb35-01e908a8e181', '95f03222-664a-4a4c-8dc4-ac006464bbf5', '2026-01-23 14:09:04', '2026-02-02 09:47:05', '2026-02-02 09:47:05'),
	('783900e4-1a54-4dbf-8e0a-0f65f419c928', '025f77f1-dbb3-49d8-bb35-01e908a8e181', 'c0e93d11-b193-4355-bc9a-6d00d0492fb4', '2026-01-23 14:08:15', '2026-03-06 09:44:50', '2026-03-06 09:44:50'),
	('85cf2d23-a98f-4c37-b7ca-23ff622ac46a', '025f77f1-dbb3-49d8-bb35-01e908a8e181', '95f03222-664a-4a4c-8dc4-ac006464bbf5', '2026-03-06 09:11:02', '2026-03-06 09:11:12', '2026-03-06 09:11:12'),
	('8b239b0f-6726-4993-a8fc-822b68bb3cbd', '025f77f1-dbb3-49d8-bb35-01e908a8e181', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', '2026-03-06 09:16:26', '2026-03-06 09:20:33', '2026-03-06 09:20:33'),
	('8e820e61-5f1c-4ae4-98f9-9c5e5c68dd5f', '025f77f1-dbb3-49d8-bb35-01e908a8e181', '95f03222-664a-4a4c-8dc4-ac006464bbf5', '2026-01-14 06:36:23', '2026-01-14 06:36:27', '2026-01-14 06:36:27'),
	('92e55d75-c313-472e-9313-69ab0ec195bc', '025f77f1-dbb3-49d8-bb35-01e908a8e181', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', '2026-03-06 09:15:18', '2026-03-06 09:15:38', '2026-03-06 09:15:38'),
	('9eba9e6c-2db2-479c-9d98-5c1951d0fb3d', '025f77f1-dbb3-49d8-bb35-01e908a8e181', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', '2026-03-06 09:39:23', '2026-03-06 09:39:23', NULL),
	('a0f2487c-2754-4d85-8052-a1f006d87cb8', '025f77f1-dbb3-49d8-bb35-01e908a8e181', '95f03222-664a-4a4c-8dc4-ac006464bbf5', '2026-01-14 07:01:31', '2026-01-14 07:01:35', '2026-01-14 07:01:35'),
	('a7b41fb7-a6df-44f6-86ed-a4a0af81e9ee', '025f77f1-dbb3-49d8-bb35-01e908a8e181', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', '2026-03-06 09:23:36', '2026-03-06 09:28:39', '2026-03-06 09:28:39'),
	('a9df3774-24d6-4a6a-ab8c-c1eb0e9e9543', '025f77f1-dbb3-49d8-bb35-01e908a8e181', '95f03222-664a-4a4c-8dc4-ac006464bbf5', '2026-03-06 09:11:24', '2026-03-06 09:12:07', '2026-03-06 09:12:07'),
	('ab5bf1f0-4e55-41b5-b362-162dd3e51e22', '025f77f1-dbb3-49d8-bb35-01e908a8e181', '95f03222-664a-4a4c-8dc4-ac006464bbf5', '2026-01-14 07:19:47', '2026-01-14 07:19:49', '2026-01-14 07:19:49'),
	('aebc1509-4cb5-4c67-98c6-b07b9bf482fe', '025f77f1-dbb3-49d8-bb35-01e908a8e181', '95f03222-664a-4a4c-8dc4-ac006464bbf5', '2026-03-06 09:12:10', '2026-03-06 09:21:23', '2026-03-06 09:21:23'),
	('bff24a42-18dd-4a43-9d81-0674dc315ae3', '025f77f1-dbb3-49d8-bb35-01e908a8e181', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', '2026-03-06 09:15:52', '2026-03-06 09:16:22', '2026-03-06 09:16:22'),
	('c7c782d3-ebf2-4f21-b9c8-75c8b0acfda4', '025f77f1-dbb3-49d8-bb35-01e908a8e181', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', '2026-03-06 09:29:45', '2026-03-06 09:39:22', '2026-03-06 09:39:22'),
	('c8d89ea7-c583-4006-a5f2-82ee579e135b', '025f77f1-dbb3-49d8-bb35-01e908a8e181', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', '2026-03-06 09:29:22', '2026-03-06 09:29:24', '2026-03-06 09:29:24'),
	('cdb7308d-a93f-4665-8835-fbfbb73c40b8', '025f77f1-dbb3-49d8-bb35-01e908a8e181', '95f03222-664a-4a4c-8dc4-ac006464bbf5', '2026-01-14 06:48:57', '2026-01-14 07:00:48', '2026-01-14 07:00:48'),
	('d9bcfcf6-5088-4a01-a478-9e9cd51abb80', '025f77f1-dbb3-49d8-bb35-01e908a8e181', 'b2c61bfb-e9dd-4f06-b722-c1706c9e1743', '2026-03-06 09:05:05', '2026-03-06 09:12:27', '2026-03-06 09:12:27'),
	('d9e4b1c6-23ec-4e81-9d33-0259e616d23c', '025f77f1-dbb3-49d8-bb35-01e908a8e181', '95f03222-664a-4a4c-8dc4-ac006464bbf5', '2026-03-06 09:10:33', '2026-03-06 09:10:35', '2026-03-06 09:10:35'),
	('dc2aaa18-7b20-4a53-89f6-f30182c44b68', '025f77f1-dbb3-49d8-bb35-01e908a8e181', '95f03222-664a-4a4c-8dc4-ac006464bbf5', '2026-01-14 06:48:34', '2026-01-14 06:48:50', '2026-01-14 06:48:50'),
	('dc669dec-77a6-4926-8db4-cedd6f55d743', '025f77f1-dbb3-49d8-bb35-01e908a8e181', 'c0e93d11-b193-4355-bc9a-6d00d0492fb4', '2026-01-23 14:07:53', '2026-01-23 14:08:07', '2026-01-23 14:08:07'),
	('dca70d82-dd1e-4462-85f9-cde6e44324ab', '025f77f1-dbb3-49d8-bb35-01e908a8e181', '95f03222-664a-4a4c-8dc4-ac006464bbf5', '2026-01-14 06:36:33', '2026-01-14 06:38:05', '2026-01-14 06:38:05');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
