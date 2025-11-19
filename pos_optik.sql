-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 19, 2025 at 09:50 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pos_optik`
--

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` char(36) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(100) NOT NULL,
  `customer_password` varchar(255) NOT NULL,
  `customer_phone` varchar(20) NOT NULL,
  `customer_dob` date DEFAULT NULL,
  `customer_gender` enum('male','female','other') NOT NULL,
  `customer_occupation` varchar(50) DEFAULT NULL,
  `customer_eye_history` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`customer_eye_history`)),
  `customer_preferences` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`customer_preferences`)),
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `customer_name`, `customer_email`, `customer_password`, `customer_phone`, `customer_dob`, `customer_gender`, `customer_occupation`, `customer_eye_history`, `customer_preferences`, `created_at`, `updated_at`, `deleted_at`) VALUES
('068ec3ef-da12-4fe0-ac4b-ee58dd78076f', 'Dono Rajata', 'hesti66@yahoo.com', '$2y$10$ydFb107hgghnmedI..KFZ.ERSmkPPuS0pZc0Mk3ENytMiU6QPB0mG', '0868 1629 700', '1964-04-20', 'male', 'Tabib', '{\"left_eye\":{\"sphere\":-4.43,\"cylinder\":-2.34,\"axis\":110},\"right_eye\":{\"sphere\":-5.43,\"cylinder\":-4.12,\"axis\":149},\"last_checkup\":\"2024-12-22\",\"condition\":\"hipermetropi\"}', '{\"frame_style\":\"full-rim\",\"color\":\"Hijau Zamrud\",\"material\":\"metal\"}', '2025-11-19 04:35:20', '2025-11-19 04:35:20', NULL),
('108a6a2f-9cd1-4a5c-8903-70bfdac69f14', 'Wani Samiah Nurdiyanti', 'permadi.prakosa@gmail.co.id', '$2y$10$JatsG.1fflliQYTPHaOIWebUmaJwpFU/0GdFY1mFbXRBYGr5mvzA2', '(+62) 636 6933 7957', '1958-11-15', 'male', 'Biarawati', '{\"left_eye\":{\"sphere\":-4.97,\"cylinder\":-2.1,\"axis\":61},\"right_eye\":{\"sphere\":-7.55,\"cylinder\":-1.79,\"axis\":100},\"last_checkup\":\"2024-06-27\",\"condition\":\"normal\"}', '{\"frame_style\":\"full-rim\",\"color\":\"Biru Keunguan\",\"material\":\"titanium\"}', '2025-11-19 04:35:20', '2025-11-19 04:35:20', NULL),
('154b8160-7187-41d3-ac42-ae5f8d4dd51c', 'Tri Ozy Mustofa', 'wsudiati@gmail.com', '$2y$10$pmDfiMLqoSJ9nOGUDMtz2Op2lcuq6UN6inrrfV.Kx1oN13zUhXnre', '(+62) 984 0325 133', '1989-08-11', 'female', 'Pastor', '{\"left_eye\":{\"sphere\":-3.83,\"cylinder\":-2.47,\"axis\":71},\"right_eye\":{\"sphere\":-7.18,\"cylinder\":-1.65,\"axis\":34},\"last_checkup\":\"2024-02-11\",\"condition\":\"astigmatisme\"}', '{\"frame_style\":\"rimless\",\"color\":\"Ungu Kecokelatan\",\"material\":\"acetate\"}', '2025-11-19 04:35:20', '2025-11-19 04:35:20', NULL),
('23bb9d16-a2df-4a3d-8e71-bdcc4157492c', 'Rika Gasti Usada S.Gz', 'wkusmawati@saefullah.net', '$2y$10$3aoHboJpZCFVS34JSeKwkO3Lg/LyUEDc.bucLyr44voWXJdVeEW16', '(+62) 25 4380 1304', '1981-12-16', 'female', 'Tukang Gigi', '{\"left_eye\":{\"sphere\":-6.15,\"cylinder\":-1.8,\"axis\":2},\"right_eye\":{\"sphere\":-9.55,\"cylinder\":-2.91,\"axis\":172},\"last_checkup\":\"2025-01-02\",\"condition\":\"astigmatisme\"}', '{\"frame_style\":\"full-rim\",\"color\":\"Biru\",\"material\":\"acetate\"}', '2025-11-19 04:35:20', '2025-11-19 04:35:20', NULL),
('3a5f6f9a-5a04-46df-ab9a-c0f9f2b49006', 'Kajen Budiyanto', 'dian.firmansyah@halimah.sch.id', '$2y$10$lh8qzjy8z6ekOpSQZAfhxOHqJgSMBrThBOwzJEC0WSEpSEfLnW16i', '0654 5965 0358', '1956-08-31', 'female', 'Guru', '{\"left_eye\":{\"sphere\":-4.42,\"cylinder\":-1.74,\"axis\":111},\"right_eye\":{\"sphere\":-2.46,\"cylinder\":-0.3,\"axis\":154},\"last_checkup\":\"2024-07-31\",\"condition\":\"normal\"}', '{\"frame_style\":\"full-rim\",\"color\":\"Kuning Gelap\",\"material\":\"titanium\"}', '2025-11-19 04:35:21', '2025-11-19 04:35:21', NULL),
('46c4aa9a-0cd6-4514-9cf7-e5331d9d89ae', 'Luthfi Hutasoit', 'isusanti@kuswoyo.co', '$2y$10$wITLVpiX.Oe9YKmzq5EPBu0q2dLzayxpH64duUTwgAi0jkGg9ZBOW', '021 5639 419', '1990-01-24', 'female', 'Karyawan BUMN', '{\"left_eye\":{\"sphere\":-8.14,\"cylinder\":-4.11,\"axis\":117},\"right_eye\":{\"sphere\":-1.72,\"cylinder\":-0.78,\"axis\":142},\"last_checkup\":\"2025-08-29\",\"condition\":\"normal\"}', '{\"frame_style\":\"half-rim\",\"color\":\"Biru Malam\",\"material\":\"metal\"}', '2025-11-19 04:35:20', '2025-11-19 04:35:20', NULL),
('603e3cb0-f6f6-40c2-858b-2c813f217533', 'Labuh Hakim', 'zrahayu@rajasa.biz.id', '$2y$10$SxY7VfGXVvYciwoQ9RqefuCEUo/sQReBiZ.OUbr7ndeupSHUvgAdu', '(+62) 630 4023 4732', '1975-12-05', 'male', 'Karyawan Honorer', '{\"left_eye\":{\"sphere\":-1.69,\"cylinder\":-2.47,\"axis\":125},\"right_eye\":{\"sphere\":-9.98,\"cylinder\":-0.58,\"axis\":128},\"last_checkup\":\"2025-10-29\",\"condition\":\"hipermetropi\"}', '{\"frame_style\":\"half-rim\",\"color\":\"Tembaga\",\"material\":\"metal\"}', '2025-11-19 04:35:21', '2025-11-19 04:35:21', NULL),
('74230052-8053-4daf-894f-8d86d89b60e1', 'Ganep Yahya Saragih S.Ked', 'vega.waskita@gmail.com', '$2y$10$hV9WCw9n4Iv71Zqki0yo..WPTFLj24QhdStwrbFXYjtWJ2G3.VP7W', '0839 6909 1940', '1980-06-27', 'female', 'Transportasi', '{\"left_eye\":{\"sphere\":-1.6,\"cylinder\":-1.3,\"axis\":167},\"right_eye\":{\"sphere\":-6.31,\"cylinder\":-2.09,\"axis\":128},\"last_checkup\":\"2025-03-28\",\"condition\":\"miopi\"}', '{\"frame_style\":\"full-rim\",\"color\":\"Hijau Neon\",\"material\":\"metal\"}', '2025-11-19 04:35:21', '2025-11-19 04:35:21', NULL),
('74e0c7d4-45c3-4453-a9fe-6ff5e97b4892', 'Wage Suryono', 'marsito13@wijayanti.sch.id', '$2y$10$NlVys8RrJiq9n4aIh7PMR.FZ6C4xDd5L6J/5fltmRqgYe4nbtfQxG', '0884 1259 9123', '1957-09-10', 'female', 'Tukang Listrik', '{\"left_eye\":{\"sphere\":-9.66,\"cylinder\":-0.88,\"axis\":58},\"right_eye\":{\"sphere\":-5.58,\"cylinder\":-4.31,\"axis\":53},\"last_checkup\":\"2024-12-22\",\"condition\":\"astigmatisme\"}', '{\"frame_style\":\"half-rim\",\"color\":\"Putih Gading\",\"material\":\"titanium\"}', '2025-11-19 04:35:20', '2025-11-19 04:35:20', NULL),
('836bb2d2-cd8d-4ab8-a0f1-244a3633e2f8', 'Karna Manullang', 'pwaluyo@manullang.mil.id', '$2y$10$f5Zebp4.EERXZaWdHDjrY.4EJX85eruY7atbxQZoq0z8NDteZS146', '0855 523 363', '1961-08-22', 'female', 'Promotor Acara', '{\"left_eye\":{\"sphere\":-5.69,\"cylinder\":-3.72,\"axis\":152},\"right_eye\":{\"sphere\":-5.13,\"cylinder\":-2.02,\"axis\":22},\"last_checkup\":\"2024-02-08\",\"condition\":\"astigmatisme\"}', '{\"frame_style\":\"half-rim\",\"color\":\"Hitam Pekat\",\"material\":\"titanium\"}', '2025-11-19 04:35:20', '2025-11-19 04:35:20', NULL),
('8f2f93c4-094b-4ada-8bec-80b5b7955ca4', 'Jarwadi Nababan S.Sos', 'qhardiansyah@hutasoit.info', '$2y$10$PWm9WdsiHUcx2ygaMDRRGOPiuLUKiwRpdl7MfAWQJvliIukcC2992', '(+62) 671 2007 5062', '1998-05-22', 'female', 'Imam Masjid', '{\"left_eye\":{\"sphere\":-2.46,\"cylinder\":-2.06,\"axis\":94},\"right_eye\":{\"sphere\":-1.52,\"cylinder\":-0.46,\"axis\":129},\"last_checkup\":\"2025-08-25\",\"condition\":\"normal\"}', '{\"frame_style\":\"full-rim\",\"color\":\"Ungu Terong\",\"material\":\"metal\"}', '2025-11-19 04:35:21', '2025-11-19 04:35:21', NULL),
('90b6fc1a-5aa2-4223-acc8-de3c3528e0b3', 'Niyaga Adikara Tampubolon', 'jnuraini@nugroho.biz', '$2y$10$pJVmS6gWVRe0JCnyUFrkUuWnEfUSQZhumaW.jaSr5FwAKc7tYUATe', '0836 9074 5753', '2001-01-14', 'female', 'Jaksa', '{\"left_eye\":{\"sphere\":-1.22,\"cylinder\":-3.97,\"axis\":75},\"right_eye\":{\"sphere\":-4.75,\"cylinder\":-2.13,\"axis\":28},\"last_checkup\":\"2024-08-27\",\"condition\":\"miopi\"}', '{\"frame_style\":\"full-rim\",\"color\":\"Merah Muda Dakam\",\"material\":\"acetate\"}', '2025-11-19 04:35:21', '2025-11-19 04:35:21', NULL),
('98af831b-b384-40f3-b082-17ff559bd29b', 'Vicky Suartini', 'raisa.budiman@yahoo.co.id', '$2y$10$GWdwMJj1IDrSkyBM7B6QLO03ccFsKOsKsEYAAHXqXOYBVKgKoqkq6', '0512 1654 8948', '1982-04-23', 'female', 'Desainer', '{\"left_eye\":{\"sphere\":-3.53,\"cylinder\":-0.68,\"axis\":77},\"right_eye\":{\"sphere\":-8.46,\"cylinder\":-1.22,\"axis\":85},\"last_checkup\":\"2025-09-24\",\"condition\":\"miopi\"}', '{\"frame_style\":\"rimless\",\"color\":\"Kuning Lemon\",\"material\":\"metal\"}', '2025-11-19 04:35:21', '2025-11-19 04:35:21', NULL),
('a980d828-0443-40e3-aa98-6e89c670b230', 'Balangga Siregar', 'eko31@gmail.co.id', '$2y$10$96b1sKlsIIcoMn.ViaBxCupkssvHdbeuMhfV.u.uA1U6kW5Vg.Wb2', '0864 5483 3368', '1962-04-08', 'female', 'Karyawan Honorer', '{\"left_eye\":{\"sphere\":-6.73,\"cylinder\":-4.56,\"axis\":129},\"right_eye\":{\"sphere\":-9.44,\"cylinder\":-1.37,\"axis\":126},\"last_checkup\":\"2024-10-25\",\"condition\":\"hipermetropi\"}', '{\"frame_style\":\"half-rim\",\"color\":\"Ungu Lembayung Muda\",\"material\":\"titanium\"}', '2025-11-19 04:35:20', '2025-11-19 04:35:20', NULL),
('acf425b5-cd1e-4238-985f-4e68b5c9a1a0', 'Hilda Hartati S.E.', 'lintang52@yahoo.com', '$2y$10$b/BIteAIjjqhSh8J4.Ofq.xj/ZXL3b391.txiycWuIcz27uDRnYjG', '0899 1230 917', '2003-08-28', 'female', 'Jaksa', '{\"left_eye\":{\"sphere\":-7.22,\"cylinder\":-2.05,\"axis\":39},\"right_eye\":{\"sphere\":-0.97,\"cylinder\":-1.2,\"axis\":16},\"last_checkup\":\"2024-09-11\",\"condition\":\"hipermetropi\"}', '{\"frame_style\":\"half-rim\",\"color\":\"Tomat\",\"material\":\"metal\"}', '2025-11-19 04:35:20', '2025-11-19 04:35:20', NULL),
('b4a3d0c3-0a24-4836-86f9-2fd9d2928a85', 'Sari Purnawati S.IP', 'janet66@wijayanti.desa.id', '$2y$10$jpcCuq/4qSlp2WKM9WlQ5Oz.d1ZfsQjShjepR3mqp7A8mzGlDQle6', '0752 0368 8132', '1959-10-26', 'male', 'Seniman', '{\"left_eye\":{\"sphere\":-8.58,\"cylinder\":-3.38,\"axis\":53},\"right_eye\":{\"sphere\":-4.12,\"cylinder\":-1.46,\"axis\":38},\"last_checkup\":\"2024-01-10\",\"condition\":\"astigmatisme\"}', '{\"frame_style\":\"full-rim\",\"color\":\"Chiffon\",\"material\":\"titanium\"}', '2025-11-19 04:35:21', '2025-11-19 04:35:21', NULL),
('e586085e-5ec1-4925-ad2e-665ca693fdf1', 'Muni Asmianto Prabowo', 'waluyo.paris@gmail.com', '$2y$10$VFu38Fl5nJVOmHcujSAZA.UpQ9M4b/ZZeNL65uay/0D7Vzo6S8s8S', '(+62) 520 2799 379', '1999-07-02', 'female', 'Paraji', '{\"left_eye\":{\"sphere\":-1.93,\"cylinder\":-4.21,\"axis\":123},\"right_eye\":{\"sphere\":-1.23,\"cylinder\":-1.53,\"axis\":2},\"last_checkup\":\"2025-07-25\",\"condition\":\"miopi\"}', '{\"frame_style\":\"half-rim\",\"color\":\"Cokelat Kekuningan\",\"material\":\"metal\"}', '2025-11-19 04:35:21', '2025-11-19 04:35:21', NULL),
('edc73f08-453f-43df-b880-2f3c3b3d9e64', 'Dinda Widiastuti', 'utama.irsad@gmail.com', '$2y$10$jUydRLaZE5suspyQSsJkI.2XAIPob3Iy7cao9WDzRU7WDV5w2Oovi', '0810 0938 4673', '1977-02-21', 'male', 'Desainer', '{\"left_eye\":{\"sphere\":-4.54,\"cylinder\":-4.2,\"axis\":108},\"right_eye\":{\"sphere\":-7.81,\"cylinder\":-0.49,\"axis\":17},\"last_checkup\":\"2024-04-11\",\"condition\":\"normal\"}', '{\"frame_style\":\"rimless\",\"color\":\"Jingga Labu\",\"material\":\"acetate\"}', '2025-11-19 04:35:20', '2025-11-19 04:35:20', NULL),
('fb14e22d-dc88-4488-80b7-28b554f92043', 'Estiono Adriansyah S.Pd', 'elvina.sinaga@situmorang.mil.id', '$2y$10$qyO0KQn4ar6.G.mNJ4WPC.qNNrX5dd4gmeIg7QFNk.fcW0Ap8VVWi', '0582 8640 701', '1983-10-30', 'male', 'Promotor Acara', '{\"left_eye\":{\"sphere\":-6.11,\"cylinder\":-2.4,\"axis\":160},\"right_eye\":{\"sphere\":-0.22,\"cylinder\":-1.49,\"axis\":178},\"last_checkup\":\"2025-05-05\",\"condition\":\"miopi\"}', '{\"frame_style\":\"rimless\",\"color\":\"Ungu Lembayung Muda\",\"material\":\"acetate\"}', '2025-11-19 04:35:20', '2025-11-19 04:35:20', NULL),
('fe8c1f44-a419-424d-81d8-01df308fa107', 'Najam Prasasta S.Gz', 'mustofa.johan@gmail.com', '$2y$10$bMW84fsbDW1hj1lKHpmm..o90MICAPgytFR/.rPGDJILuMDBDfOl2', '(+62) 917 7712 0355', '1969-06-16', 'female', 'Masinis', '{\"left_eye\":{\"sphere\":-5.05,\"cylinder\":-0.24,\"axis\":116},\"right_eye\":{\"sphere\":-9.41,\"cylinder\":-3.73,\"axis\":5},\"last_checkup\":\"2024-01-04\",\"condition\":\"hipermetropi\"}', '{\"frame_style\":\"rimless\",\"color\":\"Almond\",\"material\":\"titanium\"}', '2025-11-19 04:35:21', '2025-11-19 04:35:21', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `eye_examinations`
--

CREATE TABLE `eye_examinations` (
  `eye_examination_id` char(36) NOT NULL,
  `customer_id` char(36) NOT NULL,
  `left_eye_sphere` float DEFAULT NULL,
  `left_eye_cylinder` float DEFAULT NULL,
  `left_eye_axis` float DEFAULT NULL,
  `right_eye_sphere` float DEFAULT NULL,
  `right_eye_cylinder` float DEFAULT NULL,
  `right_eye_axis` float DEFAULT NULL,
  `symptoms` text DEFAULT NULL,
  `diagnosis` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_transactions`
--

CREATE TABLE `inventory_transactions` (
  `inventory_transaction_id` char(36) NOT NULL,
  `product_id` char(36) NOT NULL,
  `transaction_type` enum('in','out') NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL,
  `transaction_date` datetime NOT NULL,
  `description` text DEFAULT NULL,
  `user_id` char(36) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(75, '2025-06-02-019900', 'App\\Database\\Migrations\\CreateRolesTable', 'default', 'App', 1763526917, 1),
(76, '2025-06-02-020018', 'App\\Database\\Migrations\\CreateCustomersTable', 'default', 'App', 1763526917, 1),
(77, '2025-06-02-020033', 'App\\Database\\Migrations\\CreateProductCategoriesTable', 'default', 'App', 1763526918, 1),
(78, '2025-06-02-020045', 'App\\Database\\Migrations\\CreateProductsTable', 'default', 'App', 1763526918, 1),
(79, '2025-06-02-020056', 'App\\Database\\Migrations\\CreateOrdersTable', 'default', 'App', 1763526918, 1),
(80, '2025-06-02-020107', 'App\\Database\\Migrations\\CreateOrderItemsTable', 'default', 'App', 1763526918, 1),
(81, '2025-06-02-020135', 'App\\Database\\Migrations\\CreateEyeExaminationsTable', 'default', 'App', 1763526918, 1),
(82, '2025-06-02-020150', 'App\\Database\\Migrations\\CreateReviewsTable', 'default', 'App', 1763526918, 1),
(83, '2025-06-02-020201', 'App\\Database\\Migrations\\CreateSalesPredictionsTable', 'default', 'App', 1763526918, 1),
(84, '2025-06-02-032255', 'App\\Database\\Migrations\\CreateUsers', 'default', 'App', 1763526918, 1),
(85, '2025-06-11-030639', 'App\\Database\\Migrations\\CreateInventoryTransactions', 'default', 'App', 1763526918, 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` char(36) NOT NULL,
  `customer_id` char(36) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `grand_total` decimal(10,2) DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `proof_of_payment` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `shipping_costs` decimal(10,2) DEFAULT NULL,
  `status` enum('cart','pending','waiting_confirmation','paid','shipped','done','cancelled') NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` char(36) NOT NULL,
  `order_id` char(36) NOT NULL,
  `product_id` char(36) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` char(36) NOT NULL,
  `category_id` char(36) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `product_stock` int(11) NOT NULL DEFAULT 0,
  `product_brand` varchar(50) DEFAULT NULL,
  `product_image_url` varchar(255) DEFAULT NULL,
  `model` varchar(50) DEFAULT NULL,
  `duration` varchar(50) DEFAULT NULL,
  `material` varchar(50) DEFAULT NULL,
  `base_curve` varchar(50) DEFAULT NULL,
  `diameter` varchar(50) DEFAULT NULL,
  `power_range` varchar(50) DEFAULT NULL,
  `water_content` varchar(50) DEFAULT NULL,
  `uv_protection` tinyint(1) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `coating` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_categories`
--

CREATE TABLE `product_categories` (
  `category_id` char(36) NOT NULL,
  `category_name` varchar(50) NOT NULL,
  `category_description` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `product_categories`
--

INSERT INTO `product_categories` (`category_id`, `category_name`, `category_description`, `created_at`, `updated_at`, `deleted_at`) VALUES
('1af0cda4-42a4-405f-a6da-9159f35d73b1', 'Softlens', 'Lensa kontak sehari-hari dan khusus', '2025-11-19 04:35:21', '2025-11-19 04:35:21', NULL),
('23e8a190-e353-480d-a1f8-b87a6e19c75f', 'Frame Kacamata', 'Berbagai macam frame kacamata pria dan wanita', '2025-11-19 04:35:21', '2025-11-19 04:35:21', NULL),
('47e18c1f-8d91-461b-8f8f-a65c5b0f52c2', 'Aksesoris', 'Tali kacamata, case, cleaner, dll', '2025-11-19 04:35:21', '2025-11-19 04:35:21', NULL),
('877aa46d-e16b-4ebd-b9dc-7dac4076c439', 'Lensa Kacamata', 'Lensa dengan berbagai jenis dan indeks', '2025-11-19 04:35:21', '2025-11-19 04:35:21', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` char(36) NOT NULL,
  `product_id` char(36) NOT NULL,
  `customer_id` char(36) NOT NULL,
  `rating` int(1) NOT NULL,
  `comment` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` char(36) NOT NULL,
  `role_name` varchar(100) NOT NULL,
  `role_description` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`, `role_description`, `created_at`, `updated_at`, `deleted_at`) VALUES
('263682ff-5604-4e4b-84a1-3735a60487f2', 'admin', 'Administrator dengan akses penuh', '2025-11-19 04:35:19', '2025-11-19 04:35:19', NULL),
('4faeda9c-17c0-4990-ac7a-7d1993a1b0e2', 'optometrist', 'Dokter mata yang melakukan pemeriksaan', '2025-11-19 04:35:20', '2025-11-19 04:35:20', NULL),
('c098ec81-b11f-4f54-99b3-c928a5b80dba', 'cashier', 'Kasir yang menangani transaksi penjualan', '2025-11-19 04:35:20', '2025-11-19 04:35:20', NULL),
('deb8f651-d9f0-49fa-ab0b-2df9476f828d', 'customer', 'Customer/Buyer', '2025-11-19 04:35:20', '2025-11-19 04:35:20', NULL),
('e5641dce-045b-43a5-8a3e-fe5a697dd318', 'sasas', 'sasas', '2025-11-19 04:37:18', '2025-11-19 04:38:54', '2025-11-19 04:38:54'),
('f1e2d5b7-4807-4060-bb29-ce5520fa3743', 'inventory', 'Staff gudang mengelola produk', '2025-11-19 04:35:20', '2025-11-19 04:35:20', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sales_predictions`
--

CREATE TABLE `sales_predictions` (
  `sales_prediction_id` char(36) NOT NULL,
  `product_id` char(36) NOT NULL,
  `prediction_date` date NOT NULL,
  `predicted_quantity` float NOT NULL,
  `confidence_score` float NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` char(36) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` char(36) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_name`, `user_email`, `password`, `role_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
('21a2b448-3f5c-4d33-bbd3-2ef891700e56', 'Customer', 'customer@gmail.com', '$2y$10$ur.87Ch62y0x178Of7k5juOSiEmyBFbBLAVgE3l35YZRw877VjXqW', 'deb8f651-d9f0-49fa-ab0b-2df9476f828d', '2025-11-19 04:35:22', '2025-11-19 04:35:22', NULL),
('56e4bac0-0acd-45cb-9086-f2a5cfecd06f', 'Dr. Mata', 'optometrist@gmail.com', '$2y$10$RT2FQIQrleMA2w8.g5Dl/uxxBJqG38lUs2aQCJoVq0tIdaQ3N/2xy', '4faeda9c-17c0-4990-ac7a-7d1993a1b0e2', '2025-11-19 04:35:22', '2025-11-19 04:35:22', NULL),
('6d9036ef-80b5-46ce-83e0-3347e611365f', 'Admin Super', 'admin@gmail.com', '$2y$10$CKb/G6sJQp9rJjpXywsqkeWXsKFiM.kSmxt1YAtOB0m/.5ZGj0Kli', '263682ff-5604-4e4b-84a1-3735a60487f2', '2025-11-19 04:35:22', '2025-11-19 04:35:22', NULL),
('aba98897-daf5-45f9-b119-76b8698249ae', 'Petugas Gudang', 'inventory@gmail.com', '$2y$10$TJLngmLr4icQJOMp6b22De9JS4wakGY/j/R5xgRhGKmf5ALv4hg22', 'f1e2d5b7-4807-4060-bb29-ce5520fa3743', '2025-11-19 04:35:22', '2025-11-19 04:35:22', NULL),
('fa364229-d7b3-4154-8ce1-bbc064ae8fe1', 'Kasir Toko', 'cashier@gmail.com', '$2y$10$T6zufRedii8tAiWfSifka.G6pQp8LgaO32xUHXxONKGYEefGn7Zr.', 'c098ec81-b11f-4f54-99b3-c928a5b80dba', '2025-11-19 04:35:22', '2025-11-19 04:35:22', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `customer_email` (`customer_email`);

--
-- Indexes for table `eye_examinations`
--
ALTER TABLE `eye_examinations`
  ADD PRIMARY KEY (`eye_examination_id`),
  ADD KEY `eye_examinations_customer_id_foreign` (`customer_id`);

--
-- Indexes for table `inventory_transactions`
--
ALTER TABLE `inventory_transactions`
  ADD PRIMARY KEY (`inventory_transaction_id`),
  ADD KEY `inventory_transactions_product_id_foreign` (`product_id`),
  ADD KEY `inventory_transactions_user_id_foreign` (`user_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `orders_customer_id_foreign` (`customer_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_items_order_id_foreign` (`order_id`),
  ADD KEY `order_items_product_id_foreign` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `products_category_id_foreign` (`category_id`);

--
-- Indexes for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `reviews_product_id_foreign` (`product_id`),
  ADD KEY `reviews_customer_id_foreign` (`customer_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `sales_predictions`
--
ALTER TABLE `sales_predictions`
  ADD PRIMARY KEY (`sales_prediction_id`),
  ADD KEY `sales_predictions_product_id_foreign` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_email` (`user_email`),
  ADD KEY `fk_users_roles` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `eye_examinations`
--
ALTER TABLE `eye_examinations`
  ADD CONSTRAINT `eye_examinations_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `inventory_transactions`
--
ALTER TABLE `inventory_transactions`
  ADD CONSTRAINT `inventory_transactions_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `inventory_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reviews_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sales_predictions`
--
ALTER TABLE `sales_predictions`
  ADD CONSTRAINT `sales_predictions_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_roles` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
