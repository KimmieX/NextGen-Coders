-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 18, 2025 at 10:44 PM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `chantmo`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
CREATE TABLE IF NOT EXISTS `admins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `remember_token` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `email`, `password`, `created_at`, `updated_at`, `remember_token`) VALUES
(1, 'admin', 'admin@chantmo.com', '$2y$10$gRa3cTZ2S8nFUq5qzIwmYumQvRounioMreaL5NjHZw2pzfxSWydDG', '2025-08-07 19:29:49', '2025-08-13 20:02:10', NULL),
(2, 'adminpro', 'admin@gmail.com', '$2y$10$CTxaHskRC3fGGii9T7wynOMJ4KnsnvZ9QrUhsosA/nWgvApuKCABS', '2025-08-13 21:43:05', '2025-08-13 21:43:05', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `newsletter_subscribers`
--

DROP TABLE IF EXISTS `newsletter_subscribers`;
CREATE TABLE IF NOT EXISTS `newsletter_subscribers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `verification_token` varchar(255) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `newsletter_subscribers`
--

INSERT INTO `newsletter_subscribers` (`id`, `email`, `verification_token`, `is_verified`, `created_at`) VALUES
(1, 'user@gmail.com', NULL, 1, '2025-08-17 11:53:43'),
(2, 'park@gmail.com', '225371cb3337e8d22fed2f1d39d2696126c7b1663a429627aac9dfa5f4e41ffd', 0, '2025-08-17 11:59:36'),
(3, 'rick18@gmail.com', NULL, 1, '2025-08-17 12:00:59'),
(4, 'larry@gmail.com', 'e0ece3a0cacf3da809f22ef461edd0380555756da8cabea2208e1b70ab5c9e8c', 0, '2025-08-17 12:18:34'),
(5, 'test2@gmail.com', NULL, 1, '2025-08-17 13:03:17'),
(6, 'admin@gmail.com', 'aed647caa836ce26606e0160482d2d9684a83801a57b4adc6889c50a006ac4bd', 0, '2025-08-17 13:04:12'),
(7, 'leo@gmail.com', '786f8ca6c2a89c1b6a615d12f6bc77233d09c257ecc2edf9195b83f6ce1be764', 0, '2025-08-17 13:25:47'),
(8, 'spencer@mail.com', '084a60cfcc1c055c1b8237a770270c3199ff6d638d2b3070ae04fb23fb4ed201', 0, '2025-08-17 14:17:55'),
(9, 'ts2@example.com', '4b16cfaf73834a37cf4093fbccd01500bb30456577ac5b1526269a140157b11d', 0, '2025-08-17 14:18:11'),
(10, 'ts4@example.com', '6c64df79a5b34b37bf974e59e1a78ea4dbf5bad5252cd12fa52cf4f5a5f32e47', 0, '2025-08-17 14:20:04'),
(11, 'test12@gmail.com', NULL, 1, '2025-08-17 15:11:21'),
(12, 'ewoenam@gmail.com', NULL, 1, '2025-08-17 15:12:16'),
(13, 'jane@gmail.com', '9ba3088031d3066678de047cf9592168ccfbcef05e0e4f6f204bb9ed4981a87d', 0, '2025-08-17 22:55:27'),
(14, 'admi2n@gmail.com', '8edd0816e346e42dfe3a7a9466f6b642f56edbabc760004ae2f5bb76b0dab59a', 0, '2025-08-18 10:37:59');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `order_number` varchar(20) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','mobile_money') NOT NULL,
  `payment_status` enum('pending','paid') DEFAULT 'pending',
  `status` enum('pending','processing','completed','cancelled') DEFAULT 'pending',
  `address` text,
  `phone` varchar(20) DEFAULT NULL,
  `notes` text,
  `admin_notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `order_number`, `total_amount`, `payment_method`, `payment_status`, `status`, `address`, `phone`, `notes`, `admin_notes`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 4, 'ORD-68973B2C1E9F2', 44.00, 'mobile_money', 'pending', 'completed', '22 La', '23233225522', '', NULL, '2025-08-09 12:12:28', '2025-08-11 15:32:55', NULL),
(2, 4, 'ORD-6897820893292', 22.00, 'cash', 'pending', 'completed', '22 lA', '23233225523', '', 'order completed success', '2025-08-09 17:14:48', '2025-08-11 23:03:36', NULL),
(3, 4, 'ORD-68978C90C207D', 44.00, 'cash', 'pending', 'pending', 'main', '23233225523', '', '[8/11 23:22] admin:\n* Customer Called [8/11/25]\n\n', '2025-08-09 17:59:44', '2025-08-11 23:39:27', NULL),
(6, 4, 'ORD-689C4D2704311', 119.00, 'cash', 'paid', 'pending', 'Kasoa High ST', '0233223322', '', NULL, '2025-08-13 08:30:31', '2025-08-13 21:44:30', NULL),
(7, 4, 'ORD-689C801DDFE1A', 177.00, 'cash', 'paid', 'completed', 'Ghana', '0233223322', '', NULL, '2025-08-13 12:07:57', '2025-08-13 13:08:07', NULL),
(8, 1, 'ORD-68A35AF44A8B7', 270.00, 'cash', 'pending', 'pending', 'GR 22 STREET', '0245233233', '', NULL, '2025-08-18 16:55:16', '2025-08-18 16:55:16', NULL),
(9, 1, 'ORD-68A36089D1737', 89.98, 'mobile_money', 'pending', 'pending', 'Greater Accra', '054332112', '', NULL, '2025-08-18 17:19:05', '2025-08-18 17:19:05', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 2, 2, 22.00),
(2, 2, 2, 1, 22.00),
(3, 3, 2, 2, 22.00),
(6, 6, 7, 2, 10.00),
(7, 6, 5, 1, 99.00),
(8, 7, 5, 1, 99.00),
(9, 7, 6, 1, 78.00),
(10, 8, 18, 6, 45.00),
(11, 9, 13, 2, 19.99),
(12, 9, 15, 2, 25.00);

-- --------------------------------------------------------

--
-- Table structure for table `order_notes`
--

DROP TABLE IF EXISTS `order_notes`;
CREATE TABLE IF NOT EXISTS `order_notes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `admin_id` int NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `admin_id` (`admin_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_notes`
--

INSERT INTO `order_notes` (`id`, `order_id`, `admin_id`, `note`, `created_at`) VALUES
(1, 2, 1, 'order completed success', '2025-08-11 23:03:36');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `original_price` decimal(10,2) DEFAULT NULL,
  `stock_quantity` int DEFAULT '0',
  `category` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `featured` tinyint(1) DEFAULT '0',
  `badge` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `original_price`, `stock_quantity`, `category`, `image_url`, `description`, `featured`, `badge`, `expiry_date`, `created_at`, `updated_at`) VALUES
(13, 'Kinley Premium Water', 19.99, 24.99, 8, 'Beverages', 'https://images.unsplash.com/photo-1638688569176-5b6db19f9d2a?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTV8fGRyaW5raW5nJTIwd2F0ZXIlMjBib3R0bGV8ZW58MHx8MHx8fDA%3D', 'Pure, Refreshing Drinking Water', 1, 'New', '2028-12-12', '2025-08-18 15:02:33', '2025-08-18 17:19:05'),
(14, 'Jenuin Juice', 20.00, 20.00, 6, 'Beverages', 'https://images.unsplash.com/photo-1570831739435-6601aa3fa4fb?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8anVpY2UlMjBib3R0bGV8ZW58MHx8MHx8fDA%3D', '', 1, 'Limited', '2028-12-12', '2025-08-18 15:06:34', '2025-08-18 15:06:34'),
(15, 'Apple Juice', 25.00, 30.00, 6, 'Beverages', 'https://images.unsplash.com/photo-1626120032630-b51c96a544f5?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTB8fGp1aWNlJTIwYm90dGxlfGVufDB8fDB8fHww', 'Natural Drink', 1, 'New', '2028-12-12', '2025-08-18 15:08:27', '2025-08-18 17:19:05'),
(16, 'Soda', 29.99, 39.99, 10, 'Beverages', 'https://images.unsplash.com/photo-1605548230624-8d2d0419c517?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTh8fHNvZGF8ZW58MHx8MHx8fDA%3D', 'Original Soda', 1, 'Best Seller', '2028-12-12', '2025-08-18 15:11:56', '2025-08-18 15:11:56'),
(17, 'Cocktail Drink', 34.00, 34.00, 10, 'Beverages', 'https://images.unsplash.com/photo-1700328971815-854758899c06?q=80&w=735&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', 'Refreshing Drink', 1, 'New', '2028-12-12', '2025-08-18 15:18:23', '2025-08-18 15:18:23'),
(18, 'Nescafe', 45.00, 45.00, 0, 'Beverages', 'https://images.unsplash.com/photo-1643389955672-917b5d8b53ec?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NTV8fGNob2NvbGF0ZSUyMGRyaW5rJTIwYm90dGxlfGVufDB8fDB8fHww', '', 1, 'New', '2028-12-12', '2025-08-18 15:56:18', '2025-08-18 16:55:16'),
(19, 'Wine', 299.00, 325.00, 10, 'Beverages', 'https://images.unsplash.com/photo-1610631787813-9eeb1a2386cc?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8N3x8d2luZSUyMGJvdHRsZXxlbnwwfHwwfHx8MA%3D%3D', '', 1, 'New', '2028-12-12', '2025-08-18 16:03:09', '2025-08-18 16:03:09'),
(20, 'Orange Juice', 45.00, 45.00, 8, 'Beverages', 'https://media.istockphoto.com/id/1030319670/photo/3d-rendering-of-orange-juice-paper-packaging-isolated-on-white-background.webp?a=1&b=1&s=612x612&w=0&k=20&c=H5qDA-juFT_wa5SYBMKfn4CoPO2ELgypPwTiVpIGlbs=', '', 1, 'New', '2028-12-12', '2025-08-18 16:06:29', '2025-08-18 16:06:29'),
(21, 'Juice Pack', 499.00, 499.00, 12, 'Beverages', 'https://media.istockphoto.com/id/2188129302/photo/orange-juice-cartons-with-vibrant-colors-and-fruit-illustrations-on-a-gray-background-100.webp?a=1&b=1&s=612x612&w=0&k=20&c=SzzCkWeW1PG9Zg2kmis-_Hnab6ZJplt4FGfuRcVY7iA=', '', 1, 'New', NULL, '2025-08-18 16:11:45', '2025-08-18 16:11:45'),
(22, 'Potato Chip', 15.00, 15.00, 6, 'Snacks & Sweets', 'https://images.unsplash.com/photo-1694101493160-10f1257fe9fd?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NHx8cG90YXRvJTIwY2hpcHxlbnwwfHwwfHx8MA%3D%3D', '', 0, 'Sale', NULL, '2025-08-18 17:32:39', '2025-08-18 17:32:39'),
(23, 'Lays', 43.00, 58.00, 8, 'Snacks & Sweets', 'https://images.unsplash.com/photo-1741520149946-d2e652514b5a?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8M3x8bGF5cyUyMGNoaXB8ZW58MHx8MHx8fDA%3D', '', 1, 'Best Seller', NULL, '2025-08-18 17:38:53', '2025-08-18 17:38:53'),
(24, 'Popcorn', 12.00, 18.00, 12, 'Snacks & Sweets', 'https://plus.unsplash.com/premium_photo-1669341979746-5fdf76bfb0f2?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTd8fHBvcGNvcm58ZW58MHx8MHx8fDA%3D', '', 1, 'New', NULL, '2025-08-18 17:57:50', '2025-08-18 17:57:50'),
(25, 'Protein Bar', 19.00, 19.00, 10, 'Snacks & Sweets', 'https://images.unsplash.com/photo-1629214831802-bb2a07f9517e?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8N3x8cHJvdGVpbiUyMGJhcnxlbnwwfHwwfHx8MA%3D%3D', '', 0, 'Limited', NULL, '2025-08-18 18:00:46', '2025-08-18 18:00:46'),
(26, 'Kit Kat', 10.00, 10.00, 10, 'Snacks & Sweets', 'https://images.unsplash.com/photo-1604815891325-0f9c17688328?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8a2l0JTIwa2F0fGVufDB8fDB8fHww', '', 0, 'Best Seller', NULL, '2025-08-18 18:03:35', '2025-08-18 18:03:35'),
(27, 'Ice Cream', 15.00, 15.00, 6, 'Snacks & Sweets', 'https://images.unsplash.com/photo-1684672764998-1d69a67adeab?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8N3x8aWNlJTIwY3JlYW0lMjBjdXB8ZW58MHx8MHx8fDA%3D', '', 0, 'Sale', NULL, '2025-08-18 18:38:37', '2025-08-18 18:38:37'),
(28, 'Lollipop', 4.00, 4.00, 10, 'Snacks & Sweets', 'https://images.unsplash.com/photo-1582306792064-cf4184cfb6ce?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTB8fGxvbGxpcG9wfGVufDB8fDB8fHww', '', 0, 'Sale', NULL, '2025-08-18 18:40:49', '2025-08-18 18:40:49'),
(29, 'Cup Cake', 28.00, 28.00, 10, 'Snacks & Sweets', 'https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8Y2FrZXxlbnwwfHwwfHx8MA%3D%3D', '', 1, 'New', NULL, '2025-08-18 18:42:47', '2025-08-18 18:42:47'),
(30, 'Doughnut', 18.00, 18.00, 6, 'Snacks & Sweets', 'https://plus.unsplash.com/premium_photo-1679341705517-67f35920eec5?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NXx8ZG91Z2hudXR8ZW58MHx8MHx8fDA%3D', '', 1, 'Limited', NULL, '2025-08-18 18:44:49', '2025-08-18 18:44:49'),
(31, 'Glass Cleaner', 23.00, 29.00, 5, 'Cleaning Supplies', 'https://images.unsplash.com/photo-1550963295-019d8a8a61c5?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8Y2xlYW5pbmclMjBzdXBwbGllc3xlbnwwfHwwfHx8MA%3D%3D', '', 1, 'New', NULL, '2025-08-18 18:51:58', '2025-08-18 18:51:58'),
(32, 'Detergent', 145.00, 190.00, 4, 'Cleaning Supplies', 'https://images.unsplash.com/photo-1624372635282-b324bcdd4907?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NHx8ZGV0ZXJnZW50fGVufDB8fDB8fHww', '', 0, 'Limited', NULL, '2025-08-18 18:54:34', '2025-08-18 18:54:34');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email_verified` tinyint(1) DEFAULT '0',
  `verification_token` varchar(255) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expires` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `remember_token` varchar(255) DEFAULT NULL,
  `verification_token_expires` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `phone`, `address`, `password`, `email_verified`, `verification_token`, `reset_token`, `reset_token_expires`, `created_at`, `updated_at`, `remember_token`, `verification_token_expires`) VALUES
(1, 'Leo', 'user@gmail.com', '', '', '$2y$10$BMe7y01effVm29OtkxMjIuATZP2C53xRCUBZ/kohs.i67mhfrKiiy', 0, 'aa0f81577d0f09bfb95dea00cdbeceb237538a60ece647b8cf05857aef527f6e', '1c565c39ae3a418a77613df00a8baf58acc8d7c180d981d53dd39e3e9bff1d4b', '2025-08-17 16:17:18', '2025-08-07 20:47:35', '2025-08-17 15:17:18', NULL, '2025-08-08 20:47:35'),
(2, 'Paul', 'park@gmail.com', '', '', '$2y$10$6nTvQrp03mJMATKp2FSZT.SJuvuFujt4LVmYPQJONF./SXPqkXPHO', 0, 'b48a27f980df14d56bc512f26c9acae22f6f69c07f42ced4373137755b63c60c', NULL, NULL, '2025-08-07 20:48:18', '2025-08-09 17:11:53', NULL, '2025-08-08 20:48:18'),
(3, 'xeroxy24', 'test2@gmail.com', '', '', '$2y$10$9GZGtZdDFjaqdp1fZ4f4E.5OwuXhpnfsordnxfaYgnDXu5FQkUyge', 1, NULL, '9af1505377933e186cdf9292f8e25243e3c2f0bc5e8429cf3bc72cca9f77ef3d', '2025-08-17 19:12:45', '2025-08-07 21:40:55', '2025-08-17 18:12:45', NULL, NULL),
(4, 'neonspe', 'ts3@example.com', '', '', '$2y$10$wIDa1OSSQ.uRgBoXaojzgO1Ry6oGwDSDR0j0XkEomVmviX.8Nchj.', 1, NULL, 'f338eca7bd541503d41028f048e1d2b3b48e70258184a015e5c18d9e1c118ae4', '2025-08-12 23:36:48', '2025-08-07 21:56:01', '2025-08-12 22:36:48', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

DROP TABLE IF EXISTS `wishlist`;
CREATE TABLE IF NOT EXISTS `wishlist` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `product_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`product_id`),
  KEY `product_id` (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=186 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`id`, `user_id`, `product_id`, `created_at`) VALUES
(183, 1, 17, '2025-08-18 16:08:58'),
(185, 4, 29, '2025-08-18 19:13:40');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
