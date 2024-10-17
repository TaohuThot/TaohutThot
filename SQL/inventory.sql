-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 17, 2024 at 10:04 AM
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
-- Database: `inventory`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`, `created_at`) VALUES
(1, 'สายไฟ', '2024-07-30 12:33:10'),
(2, 'รีโมท', '2024-07-30 12:45:47'),
(3, 'หม้อหุงข้าว', '2024-08-01 14:12:33'),
(4, 'พัดลม', '2024-08-01 14:31:13'),
(5, 'สายแลน', '2024-08-01 15:16:53'),
(6, 'สายปลั๊กไฟ', '2024-08-14 13:59:24'),
(7, 'MIXER', '2024-08-29 15:36:50'),
(8, 'ตู้ลำโพง', '2024-08-29 15:45:43'),
(10, 'ไมค์ลอย', '2024-08-29 15:49:08'),
(14, 'dasd', '2024-09-21 10:53:09'),
(15, 'adas', '2024-09-21 10:53:57');

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `company_id` int(11) NOT NULL,
  `company_name` varchar(100) NOT NULL,
  `company_address` varchar(255) DEFAULT NULL,
  `company_phone` varchar(10) DEFAULT NULL,
  `company_email` varchar(100) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`company_id`, `company_name`, `company_address`, `company_phone`, `company_email`, `profile_image`) VALUES
(1, 'บริษัท เอ็ม เค อิเล็กทรอนิกส์ จำกัด', '111/74 ม.3 ต.พิมลราช อ.บางบัวทอง จ.นนทบุรี 11110', '0955514657', 'third.nk20@gmail.com', 'ed095430-d7fe-11ed-b2bb-d336b48a2b9a_webp_original.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `phonenumber` varchar(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `customer_name`, `address`, `district`, `city`, `province`, `phonenumber`, `created_at`) VALUES
(1, 'ร้าน จงเจริญฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟ', '303/46 ม.5ฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟฟ', 'ต.บ้านแดง', 'อ.เมือง', 'จ.เลย', '0953145788', '2024-07-30 13:13:14'),
(12, 'ร้านจรัญ', '77/123 ม.2', 'ต.ฝ้ายแดง', 'อ.เมือง', 'จ.ยะลา', '0874475632', '2024-08-05 11:24:51'),
(19, 'ร้าน รวย รวย รวย จำกัด', 'กฟหกฟหก', 'เกดเเ', 'เมือง', '48', '0844615645', '2024-08-15 13:26:59'),
(20, 'สาธิต', '131/7', 'บ้านดอน', 'เมือง', 'สุราษฎร์ธานี', '0985894984', '2024-08-29 15:31:48'),
(21, 'ก อิเล็คทรอนิคส์', '323/7', 'เมือง', 'เมือง', 'พัทลุง', '3232685749', '2024-08-29 15:33:04'),
(22, 'จ วิทยุ', '959/3\r\n', 'ป่าหวาย', 'สวนผึง', 'ราชบุรี', '0896969966', '2024-08-29 15:34:54'),
(23, 'ก อะไหล่', '98\r\n', 'ลำนาราย', 'ลำนานวย', 'ลพบุรี', '0896566332', '2024-08-29 15:54:00'),
(24, 'นครหลวง', '123/89\r\n', 'ท่าลาด', 'เมือง', 'ระนอง', '0987854963', '2024-08-29 15:54:52'),
(25, 'นครหลวง2', '5212/8', 'ท่าวัง', 'กะบุรี', 'ระนอง', '0236955158', '2024-08-29 15:55:26'),
(26, 'นครพัฒนาวิทยุ', '155/84', 'ท่าลาด', 'เมือง', 'ระนอง', '0956554555', '2024-08-29 15:56:15'),
(27, 'ฉิ่มการไฟฟ้า', '89/8\r\n', 'เวียงป่าเป้า', 'เวียงป่าเป้า', 'เชียงราย', '034366455', '2024-08-29 15:57:57'),
(28, 'จิ่มเครื่องเสียง', '895/4\r\n', 'ท่าแล', 'เมือง', 'เชียงใหม่', '078369563', '2024-08-29 15:58:51'),
(29, '99การไฟฟ้า', '235/8', 'ละมาย', 'เมือง', 'สตูล', '0986664567', '2024-09-11 12:01:59'),
(31, 'ร้านไม่มี/ไม่จ่าย', '888', 'ฟ้าใหม่', 'ชาติก่อน', 'ชาติหน้า', '0899986999', '2024-09-21 13:32:05');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `employee_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `phone` varchar(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`employee_id`, `name`, `address`, `district`, `city`, `province`, `phone`, `created_at`) VALUES
(4, 'สมพร แซ่ม้า', '123/89', 'น้ำกุ่ม', 'นครไทย', 'พิษณุโลก', '0895656542', '2024-08-29 16:00:16'),
(5, 'จิ่มแจ้ม ยิ้มงาม', '89/56', 'ปาดี', 'เมือง', 'นาราธิวาส', '0895655645', '2024-08-29 16:01:45'),
(9, 'ณัฐันนทน์ กลางเคื่อม', '111/74 ม.3', 'ปากน้ำ', 'อ.บางบัวทอง', 'นนทบุรี', '0955514657', '2024-09-23 08:48:00');

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE `images` (
  `id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_size` int(11) NOT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `company_id` int(11) DEFAULT NULL,
  `discount` decimal(10,2) DEFAULT NULL,
  `tax_rate` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `customer_id`, `total_price`, `order_date`, `company_id`, `discount`, `tax_rate`) VALUES
(9, 1, 2118.60, '2024-08-14 17:00:00', 1, 10.00, 7.00),
(10, 1, 2118.60, '2024-08-14 17:00:00', 1, 10.00, 7.00),
(11, 1, 0.00, '2024-08-18 08:30:06', 1, 0.00, 7.00),
(12, 1, 0.00, '2024-08-18 08:30:40', 1, 0.00, 7.00),
(13, 12, 0.00, '2024-08-18 10:11:10', 1, 0.00, 7.00),
(14, 1, 0.00, '2024-08-18 10:18:13', 1, 0.00, 7.00),
(15, 1, 0.00, '2024-08-18 10:18:31', 1, 0.00, 7.00),
(16, 1, 0.00, '2024-08-18 10:19:00', 1, 0.00, 7.00),
(17, 1, 0.00, '2024-08-18 10:20:33', 1, 0.00, 7.00),
(18, 1, 0.00, '2024-08-18 10:25:03', 1, 0.00, 7.00),
(19, 1, 0.00, '2024-08-18 14:21:06', 1, 0.00, 7.00),
(20, 12, 0.00, '2024-08-18 14:53:53', 1, 0.00, 7.00),
(21, 19, 0.00, '2024-08-19 13:44:19', 1, 10.00, 7.00),
(22, 1, 0.00, '2024-08-19 13:53:35', 1, 10.00, 7.00),
(23, 19, 0.00, '2024-08-19 14:04:23', 1, 0.00, 7.00),
(24, 19, 0.00, '2024-08-19 14:32:35', 1, 0.00, 7.00),
(25, 1, 5103.90, '2024-08-19 14:40:55', 1, 10.00, 7.00),
(26, 1, 481.50, '2024-08-26 07:50:01', 1, 0.00, 7.00),
(27, 1, 74.90, '2024-08-26 07:50:29', 1, 0.00, 7.00),
(28, 1, 920.20, '2024-08-29 09:39:03', 1, 0.00, 7.00),
(29, 19, 107.00, '2024-08-29 10:05:10', 1, 0.00, 7.00),
(30, 1, 460.10, '2024-08-29 10:08:10', 1, 0.00, 7.00),
(31, 1, 0.00, '2024-08-29 16:05:37', 1, 0.00, 7.00),
(32, 1, 11235.00, '2024-08-29 16:06:10', 1, 0.00, 7.00),
(33, 25, 9974.22, '2024-08-29 16:07:30', 1, 3.00, 7.00),
(34, 25, 37727.67, '2024-08-29 16:09:42', 1, 3.00, 7.00),
(35, 1, 374.50, '2024-08-29 16:12:30', 1, 0.00, 7.00),
(36, 25, 3103.00, '2024-08-29 16:12:48', 1, 0.00, 7.00),
(37, 1, 12485.94, '2024-08-29 16:25:28', 1, 3.00, 7.00),
(38, 24, 11363.40, '2024-08-30 14:33:38', 1, 0.00, 7.00),
(39, 1, 3745.00, '2024-09-01 08:52:24', 1, 0.00, 7.00),
(40, 1, 3103.00, '2024-09-11 07:07:56', 1, 0.00, 7.00),
(41, 1, 107.00, '2024-09-11 11:49:05', 1, 0.00, 7.00),
(42, 29, 12412.00, '2024-09-11 12:02:39', 1, 0.00, 7.00),
(43, 29, 24888.20, '2024-09-11 12:04:31', 1, 0.00, 7.00),
(44, 1, 3884.10, '2024-09-11 12:09:21', 1, 0.00, 7.00),
(45, 1, 9095.00, '2024-09-11 12:46:05', 1, 0.00, 7.00),
(46, 1, 11716.50, '2024-09-15 09:18:45', 1, 0.00, 7.00),
(47, 1, 9469.50, '2024-09-15 10:21:17', 1, 0.00, 7.00),
(48, 1, 9095.00, '2024-09-15 10:22:21', 1, 0.00, 7.00),
(49, 1, 14605.50, '2024-09-16 08:16:57', 1, 0.00, 7.00),
(50, 1, 3648.70, '2024-09-21 11:14:43', 1, 0.00, 7.00),
(51, 1, 1326.80, '2024-09-21 11:21:33', 1, 0.00, 7.00),
(52, 1, 1326.80, '2024-09-21 11:21:41', 1, 0.00, 7.00),
(53, 1, 1326.80, '2024-09-21 11:22:21', 1, 0.00, 7.00),
(54, 1, 331.70, '2024-09-21 11:22:36', 1, 0.00, 7.00),
(55, 1, 4643.80, '2024-09-21 11:24:48', 1, 0.00, 7.00),
(56, 1, 4643.80, '2024-09-21 11:25:14', 1, 0.00, 7.00),
(57, 1, 4793.60, '2024-09-21 12:38:22', 1, 0.00, 7.00),
(58, 20, 11556.00, '2024-09-21 13:15:13', 1, 0.00, 7.00),
(59, 23, 49113.00, '2024-09-21 13:17:43', 1, 0.00, 7.00),
(60, 12, 72225.00, '2024-09-21 13:19:00', 1, 0.00, 7.00),
(61, 26, 0.00, '2024-09-21 13:21:16', 1, 0.00, 7.00),
(62, 12, 8185.50, '2024-09-21 13:23:29', 1, 0.00, 7.00),
(63, 29, 6420.00, '2024-09-21 13:28:01', 1, 0.00, 7.00),
(64, 31, 111922.00, '2024-09-21 13:33:23', 1, 0.00, 7.00),
(65, 26, 1936.70, '2024-09-21 13:46:21', 1, 0.00, 7.00),
(66, 1, 22470.00, '2024-09-23 05:38:09', 1, 0.00, 7.00),
(67, 1, 1.07, '2024-09-23 08:46:51', 1, 0.00, 7.00),
(68, 1, 2.14, '2024-09-23 08:54:50', 1, 0.00, 7.00),
(69, 1, 214.00, '2024-09-23 11:55:35', 1, 0.00, 7.00),
(70, 1, 1177.00, '2024-09-23 11:59:52', 1, 0.00, 7.00),
(71, 1, 1177.00, '2024-09-23 12:00:16', 1, 0.00, 7.00),
(72, 1, 1177.00, '2024-09-23 12:00:52', 1, 0.00, 7.00),
(73, 1, 1177.00, '2024-09-23 12:01:19', 1, 0.00, 7.00),
(74, 1, 1177.00, '2024-09-23 12:02:31', 1, 0.00, 7.00),
(75, 1, 2140.00, '2024-09-25 10:42:13', 1, 0.00, 7.00),
(76, 1, 5200.20, '2024-09-29 09:56:28', 1, 10.00, 7.00),
(77, 1, 321.00, '2024-09-29 12:14:01', 1, 0.00, 7.00),
(78, 1, 1326.80, '2024-10-06 10:19:28', 1, 0.00, 7.00),
(79, 1, 192.60, '2024-10-07 10:30:49', 1, 10.00, 7.00),
(80, 1, 2140.00, '2024-10-07 10:35:40', 1, 0.00, 7.00),
(81, 1, 963.00, '2024-10-07 10:43:44', 1, 10.00, 7.00),
(82, 1, 2140.00, '2024-10-17 06:53:50', 1, 0.00, 7.00),
(83, 1, 200.00, '2024-10-17 06:59:22', 1, 0.00, NULL),
(84, 1, 90.00, '2024-10-17 07:00:18', 1, 10.00, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `is_cancelled` tinyint(1) DEFAULT 0,
  `cancelled_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `quantity`, `total_price`, `is_cancelled`, `cancelled_at`) VALUES
(1, 9, 1, 10, 1000.00, 1, NULL),
(2, 9, 2, 10, 1200.00, 1, NULL),
(3, 10, 2, 10, 1200.00, 1, NULL),
(4, 10, 1, 10, 1000.00, 1, NULL),
(5, 11, 8, 10, 700.00, 1, '2024-08-20 21:30:58'),
(6, 12, 4, 10, 4300.00, 1, NULL),
(7, 13, 1, 10, 1000.00, 0, NULL),
(8, 14, 1, 10, 1000.00, 0, NULL),
(9, 16, 3, 10, 1000.00, 0, NULL),
(10, 17, 4, 10, 4300.00, 1, NULL),
(11, 17, 3, 10, 1000.00, 1, NULL),
(12, 18, 4, 10, 4300.00, 1, NULL),
(13, 18, 3, 10, 1000.00, 1, NULL),
(14, 19, 4, 10, 4300.00, 1, NULL),
(15, 20, 2, 10, 0.00, 1, NULL),
(16, 21, 1, 10, 1000.00, 1, NULL),
(17, 22, 1, 10, 1000.00, 1, NULL),
(18, 23, 3, 10, 1000.00, 0, NULL),
(19, 23, 1, 10, 1000.00, 0, NULL),
(20, 23, 6, 10, 1700.00, 0, NULL),
(21, 24, 7, 10, 2500.00, 0, NULL),
(22, 24, 6, 10, 1700.00, 0, NULL),
(23, 25, 4, 10, 4300.00, 1, '2024-08-21 18:11:51'),
(24, 25, 5, 10, 1000.00, 1, '2024-08-21 18:11:51'),
(25, 26, 17, 1, 450.00, 0, NULL),
(26, 27, 8, 1, 70.00, 0, NULL),
(27, 28, 4, 2, 860.00, 0, NULL),
(28, 29, 1, 1, 100.00, 0, NULL),
(29, 30, 4, 1, 430.00, 1, '2024-08-29 17:08:44'),
(30, 31, 1, 3, 0.00, 0, NULL),
(31, 32, 19, 3, 10500.00, 0, NULL),
(32, 33, 16, 3, 930.00, 1, '2024-08-29 23:08:49'),
(33, 33, 17, 5, 2250.00, 1, '2024-08-29 23:08:49'),
(34, 33, 8, 9, 630.00, 1, '2024-08-29 23:08:49'),
(35, 33, 20, 2, 5800.00, 1, '2024-08-29 23:08:49'),
(36, 34, 19, 3, 10500.00, 0, NULL),
(37, 34, 6, 5, 850.00, 0, NULL),
(38, 34, 20, 5, 14500.00, 0, NULL),
(39, 34, 19, 3, 10500.00, 0, NULL),
(40, 35, 21, 1, 350.00, 0, NULL),
(41, 36, 20, 1, 2900.00, 0, NULL),
(42, 37, 20, 1, 2900.00, 0, NULL),
(43, 37, 19, 1, 3500.00, 0, NULL),
(44, 37, 19, 1, 3500.00, 0, NULL),
(45, 37, 17, 1, 450.00, 0, NULL),
(46, 37, 21, 1, 350.00, 0, NULL),
(47, 37, 16, 1, 310.00, 0, NULL),
(48, 37, 21, 1, 350.00, 0, NULL),
(49, 37, 17, 1, 450.00, 0, NULL),
(50, 37, 2, 1, 120.00, 0, NULL),
(51, 37, 5, 1, 100.00, 0, NULL),
(52, 38, 21, 1, 350.00, 0, NULL),
(53, 38, 21, 1, 350.00, 0, NULL),
(54, 38, 17, 1, 450.00, 0, NULL),
(55, 38, 4, 1, 430.00, 0, NULL),
(56, 38, 2, 1, 120.00, 0, NULL),
(57, 38, 2, 1, 120.00, 0, NULL),
(58, 38, 3, 1, 100.00, 0, NULL),
(59, 38, 20, 1, 2900.00, 0, NULL),
(60, 38, 20, 1, 2900.00, 0, NULL),
(61, 38, 20, 1, 2900.00, 0, NULL),
(62, 39, 21, 10, 3500.00, 0, NULL),
(63, 40, 20, 1, 2900.00, 0, NULL),
(64, 41, 5, 1, 100.00, 0, NULL),
(65, 42, 20, 4, 11600.00, 0, NULL),
(66, 43, 1, 4, 400.00, 0, NULL),
(67, 43, 1, 1, 100.00, 0, NULL),
(68, 43, 2, 2, 240.00, 0, NULL),
(69, 43, 3, 2, 200.00, 0, NULL),
(70, 43, 4, 2, 860.00, 0, NULL),
(71, 43, 5, 4, 400.00, 0, NULL),
(72, 43, 7, 2, 500.00, 0, NULL),
(73, 43, 17, 10, 4500.00, 0, NULL),
(74, 43, 19, 3, 10500.00, 0, NULL),
(75, 43, 22, 2, 4200.00, 0, NULL),
(76, 43, 6, 8, 1360.00, 0, NULL),
(77, 44, 6, 9, 1530.00, 0, NULL),
(78, 44, 8, 30, 2100.00, 0, NULL),
(79, 45, 1, 1, 100.00, 0, NULL),
(80, 45, 2, 1, 120.00, 0, NULL),
(81, 45, 3, 1, 100.00, 0, NULL),
(82, 45, 4, 1, 430.00, 0, NULL),
(83, 45, 5, 1, 100.00, 0, NULL),
(84, 45, 6, 1, 170.00, 0, NULL),
(85, 45, 7, 1, 250.00, 0, NULL),
(86, 45, 8, 1, 70.00, 0, NULL),
(87, 45, 16, 1, 310.00, 0, NULL),
(88, 45, 17, 1, 450.00, 0, NULL),
(89, 45, 19, 1, 3500.00, 0, NULL),
(90, 45, 20, 1, 2900.00, 0, NULL),
(91, 46, 1, 1, 100.00, 0, NULL),
(92, 46, 2, 1, 120.00, 0, NULL),
(93, 46, 3, 1, 100.00, 0, NULL),
(94, 46, 4, 1, 430.00, 0, NULL),
(95, 46, 5, 1, 100.00, 0, NULL),
(96, 46, 6, 1, 170.00, 0, NULL),
(97, 46, 7, 1, 250.00, 0, NULL),
(98, 46, 8, 1, 70.00, 0, NULL),
(99, 46, 16, 1, 310.00, 0, NULL),
(100, 46, 17, 1, 450.00, 0, NULL),
(101, 46, 19, 1, 3500.00, 0, NULL),
(102, 46, 20, 1, 2900.00, 0, NULL),
(103, 46, 21, 1, 350.00, 0, NULL),
(104, 46, 22, 1, 2100.00, 0, NULL),
(105, 47, 1, 1, 100.00, 0, NULL),
(106, 47, 2, 1, 120.00, 0, NULL),
(107, 47, 3, 1, 100.00, 0, NULL),
(108, 47, 4, 1, 430.00, 0, NULL),
(109, 47, 5, 1, 100.00, 0, NULL),
(110, 47, 6, 1, 170.00, 0, NULL),
(111, 47, 7, 1, 250.00, 0, NULL),
(112, 47, 8, 1, 70.00, 0, NULL),
(113, 47, 16, 1, 310.00, 0, NULL),
(114, 47, 17, 1, 450.00, 0, NULL),
(115, 47, 19, 1, 3500.00, 0, NULL),
(116, 47, 20, 1, 2900.00, 0, NULL),
(117, 47, 21, 1, 350.00, 0, NULL),
(118, 48, 1, 1, 100.00, 0, NULL),
(119, 48, 2, 1, 120.00, 0, NULL),
(120, 48, 3, 1, 100.00, 0, NULL),
(121, 48, 4, 1, 430.00, 0, NULL),
(122, 48, 5, 1, 100.00, 0, NULL),
(123, 48, 6, 1, 170.00, 0, NULL),
(124, 48, 7, 1, 250.00, 0, NULL),
(125, 48, 8, 1, 70.00, 0, NULL),
(126, 48, 16, 1, 310.00, 0, NULL),
(127, 48, 17, 1, 450.00, 0, NULL),
(128, 48, 19, 1, 3500.00, 0, NULL),
(129, 48, 20, 1, 2900.00, 0, NULL),
(130, 49, 1, 1, 100.00, 0, NULL),
(131, 49, 2, 1, 120.00, 0, NULL),
(132, 49, 3, 1, 100.00, 0, NULL),
(133, 49, 4, 1, 430.00, 0, NULL),
(134, 49, 5, 1, 100.00, 0, NULL),
(135, 49, 6, 1, 170.00, 0, NULL),
(136, 49, 7, 1, 250.00, 0, NULL),
(137, 49, 8, 1, 70.00, 0, NULL),
(138, 49, 16, 1, 310.00, 0, NULL),
(139, 49, 17, 1, 450.00, 0, NULL),
(140, 49, 19, 1, 3500.00, 0, NULL),
(141, 49, 20, 1, 2900.00, 0, NULL),
(142, 49, 21, 1, 350.00, 0, NULL),
(143, 49, 22, 1, 2100.00, 0, NULL),
(144, 49, 23, 1, 950.00, 0, NULL),
(145, 49, 21, 1, 350.00, 0, NULL),
(146, 49, 17, 1, 450.00, 0, NULL),
(147, 49, 23, 1, 950.00, 0, NULL),
(148, 50, 16, 11, 3410.00, 1, '2024-09-21 18:15:05'),
(149, 51, 16, 4, 1240.00, 1, '2024-09-21 18:24:39'),
(150, 52, 16, 4, 1240.00, 1, '2024-09-21 18:24:32'),
(151, 53, 16, 4, 1240.00, 1, '2024-09-21 18:22:57'),
(152, 54, 16, 1, 310.00, 1, '2024-09-21 18:22:54'),
(153, 55, 16, 14, 4340.00, 1, '2024-09-21 18:25:09'),
(154, 56, 16, 14, 4340.00, 0, NULL),
(155, 57, 1, 1, 100.00, 1, '2024-09-21 19:38:54'),
(156, 57, 2, 1, 120.00, 1, '2024-09-21 19:38:54'),
(157, 57, 3, 1, 100.00, 1, '2024-09-21 19:38:54'),
(158, 57, 4, 1, 430.00, 1, '2024-09-21 19:38:54'),
(159, 57, 5, 1, 100.00, 1, '2024-09-21 19:38:54'),
(160, 57, 6, 1, 170.00, 1, '2024-09-21 19:38:54'),
(161, 57, 7, 1, 250.00, 1, '2024-09-21 19:38:54'),
(162, 57, 16, 1, 310.00, 1, '2024-09-21 19:38:54'),
(163, 57, 20, 1, 2900.00, 1, '2024-09-21 19:38:54'),
(164, 58, 26, 3, 8100.00, 0, NULL),
(165, 58, 25, 1, 2700.00, 0, NULL),
(166, 59, 25, 10, 27000.00, 0, NULL),
(167, 59, 26, 7, 18900.00, 0, NULL),
(168, 60, 26, 25, 67500.00, 0, NULL),
(169, 61, 1, 50, 0.00, 1, '2024-09-21 20:21:33'),
(170, 62, 27, 45, 7650.00, 0, NULL),
(171, 63, 28, 30, 6000.00, 0, NULL),
(172, 64, 28, 80, 16000.00, 1, '2024-09-21 20:33:48'),
(173, 64, 16, 100, 31000.00, 1, '2024-09-21 20:33:48'),
(174, 64, 3, 90, 9000.00, 1, '2024-09-21 20:33:48'),
(175, 64, 2, 50, 6000.00, 1, '2024-09-21 20:33:48'),
(176, 64, 8, 10, 700.00, 1, '2024-09-21 20:33:48'),
(177, 64, 25, 15, 40500.00, 1, '2024-09-21 20:33:48'),
(178, 64, 8, 20, 1400.00, 1, '2024-09-21 20:33:48'),
(179, 65, 30, 20, 1810.00, 0, NULL),
(180, 66, 22, 10, 21000.00, 1, '2024-09-23 18:57:18'),
(181, 67, 31, 1, 1.00, 1, '2024-09-23 15:48:30'),
(182, 68, 26, 1, 2.00, 1, '2024-09-23 15:55:21'),
(183, 69, 28, 1, 200.00, 1, '2024-09-23 18:56:04'),
(184, 70, 1, 11, 1100.00, 1, '2024-09-23 19:00:03'),
(185, 71, 1, 11, 1100.00, 1, '2024-09-23 19:00:22'),
(186, 72, 1, 11, 1100.00, 1, '2024-09-23 19:00:56'),
(187, 73, 1, 11, 1100.00, 1, '2024-09-23 19:01:24'),
(188, 74, 1, 11, 1100.00, 1, '2024-09-23 19:02:35'),
(189, 75, 28, 10, 2000.00, 0, NULL),
(190, 76, 28, 10, 2000.00, 0, NULL),
(191, 76, 16, 10, 3100.00, 0, NULL),
(192, 76, 3, 3, 300.00, 0, NULL),
(193, 77, 28, 1, 200.00, 1, '2024-09-29 19:14:13'),
(194, 77, 3, 1, 100.00, 1, '2024-09-29 19:14:13'),
(195, 78, 28, 1, 200.00, 0, NULL),
(196, 78, 16, 1, 310.00, 0, NULL),
(197, 78, 28, 1, 200.00, 0, NULL),
(198, 78, 4, 1, 430.00, 0, NULL),
(199, 78, 3, 1, 100.00, 0, NULL),
(200, 79, 28, 1, 200.00, 0, NULL),
(201, 80, 28, 10, 2000.00, 0, NULL),
(202, 81, 3, 10, 1000.00, 0, NULL),
(203, 82, 28, 10, 2000.00, 0, NULL),
(204, 83, 28, 1, 200.00, 0, NULL),
(205, 84, 1, 1, 100.00, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `description`, `price`, `quantity`, `category_id`, `created_at`, `image`) VALUES
(1, 'สายไฟ 5 เมตรกกกกกกกกกกกกกกกกกกกกกกกกกกกกกกกกกกกกกกกกกกกกกกกกกกกกกกกกกกกกก', 'สายไฟคุณภาพสูง', 100.00, 20, 1, '2024-07-30 12:33:10', '515996.jpg'),
(2, 'รีโมทแอร์รุ่นv.1', 'ใช้สำหรับเครื่องปรับอากาศรุ่น momo fotu', 120.00, 81, 2, '2024-07-30 12:47:29', '641849.jpg'),
(3, 'รีโมทแอร์รุ่นv.2', 'ใช้กับเครื่องปรับอากาสทุกยี่ห้อ', 100.00, 79, 2, '2024-07-30 12:52:25', '641850.jpg'),
(4, 'หม้อหุงข้าว 120w', 'หม้อหุงข้าว\r\n', 430.00, 90, 3, '2024-08-01 14:13:26', '567315.jpg'),
(5, 'พัดลม v.1', 'พัดลมมมมม\r\n\r\n\r\n', 100.00, 71, 4, '2024-08-01 14:32:26', '653379.jpg'),
(6, 'สายไฟ 8 เมตร', 'สายไฟยาว 8 เมตรเหมาะกับการใช้งาน\r\n', 170.00, 20, 1, '2024-08-08 13:14:10', '515996.jpg'),
(7, 'สายไฟ 10 เมตร', 'สายไฟฟฟฟฟฟฟ', 250.00, 63, 1, '2024-08-08 13:15:41', '515996.jpg'),
(8, 'รีโมทv.3', '-', 70.00, 30, 2, '2024-08-08 13:38:52', 'รีโมท LED รวมรุ่น.jpg'),
(16, 'หม้อข้าวขนาดเล็ก', '-', 310.00, 119, 3, '2024-08-09 16:45:21', '567314.jpg'),
(17, 'สายแลน 15 เมตร', '-', 450.00, 47, 5, '2024-08-12 14:56:51', '515997.jpg'),
(19, 'AX-4BT', 'เครื่องปรับแต่งเสียงง', 3500.00, 20, 7, '2024-08-29 15:38:28', '179906.jpg'),
(20, 'ลำโพงไฟเบอร์12\"', 'ลำโพงขยายเสียง', 2900.00, 20, 8, '2024-08-29 15:46:36', 'LINE_ALBUM_ตู้ลำโพงบ้าน_๒๔๐๒๑๑_1.jpg'),
(21, 'ไมค์ลอยถือคู่', 'ใช้ในการขยายเสียง', 350.00, 51, 10, '2024-08-29 15:50:51', 'LINE_ALBUM_ไมค์ลอย แบบถือ_๒๔๐๒๑๑_17.jpg'),
(22, 'ลำโพงคางหมู10\"', 'ลำโพง', 2100.00, 21, 8, '2024-09-11 11:59:14', '32.jpg'),
(23, 'ตู้ลำโพง8\"', 'ลำโพงอเนกประสงค์พร้อมไมค์\r\n', 950.00, 21, 8, '2024-09-11 12:11:53', '1.jpg'),
(25, 'ตู็แล็ค', '-', 1500.00, 15, 8, '2024-09-21 13:11:45', 'LINE_ALBUM_ขาตั้งลำโพง+ขาแขวนTV+ตู้ Rack_240802_3.jpg'),
(26, 'ตู้ลำโพงอเนกประสงค์', '--', 200.00, 5, 8, '2024-09-21 13:14:07', '19.jpg'),
(27, 'เสาอากาศ', '-', 170.00, 5, 14, '2024-09-21 13:22:46', '656415.jpg'),
(28, 'เครื่องช่างดิจิตอล', '-', 200.00, 66, 15, '2024-09-21 13:26:23', '4D5D7E37-B1AE-4EA5-9F51-0BAD0FDA238E.jpg'),
(29, 'วิทยุ', '-', 350.00, 11, 14, '2024-09-21 13:41:11', '590951.jpg'),
(30, 'หลอดไฟ', '-', 90.50, 11, 14, '2024-09-21 13:41:59', 'S__967025.jpg'),
(31, 'หฟกฟหก', 'กหฟฟหกก', 150.00, 33, 3, '2024-09-23 05:53:21', '590954.jpg'),
(42, 'ไดร์เป๋าผม', '', 67.00, 50, 1, '2024-09-28 16:30:32', 'S__1736854_0.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `product_costs`
--

CREATE TABLE `product_costs` (
  `cost_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `stock_in_id` int(11) NOT NULL,
  `cost_per_unit` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_costs`
--

INSERT INTO `product_costs` (`cost_id`, `product_id`, `stock_in_id`, `cost_per_unit`, `quantity`) VALUES
(1, 1, 32, 10.00, 0),
(2, 1, 33, 20.00, 0),
(3, 22, 36, 200.00, 10),
(4, 2, 37, 100.00, 10),
(5, 2, 38, 200.00, 10),
(6, 2, 39, 300.00, 20),
(7, 1, 40, 100.00, 10),
(8, 1, 41, 100.00, 10),
(9, 1, 42, 150.00, 10),
(10, 2, 43, 100.00, 10),
(11, 3, 43, 300.00, 10),
(12, 3, 44, 100.00, 10),
(13, 25, 45, 1900.00, 30),
(14, 26, 46, 2400.00, 10),
(15, 26, 47, 2150.00, 30),
(16, 27, 48, 150.00, 50),
(17, 28, 49, 150.00, 30),
(18, 28, 50, 120.00, 100),
(19, 29, 51, 200.00, 50),
(20, 30, 52, 70.25, 50),
(21, 1, 53, 200.00, 10),
(22, 1, 54, 250.00, 30),
(23, 1, 55, 150.00, 30),
(24, 1, 56, 150.00, 10),
(25, 17, 57, 100.00, 1),
(26, 17, 58, 200.00, 10),
(27, 31, 59, 100.00, 10),
(28, 31, 60, 200.00, 10),
(29, 8, 61, 200.00, 10),
(30, 7, 62, 100.00, 10),
(31, 6, 63, 140.00, 2),
(32, 1, 64, 100.00, 10),
(33, 30, 65, 200.00, 10),
(34, 25, 66, 1800.00, 12),
(35, 17, 67, 150.00, 25),
(36, 42, 68, 35.00, 50),
(37, 28, 69, 150.00, 10),
(38, 1, 70, 100.00, 10);

-- --------------------------------------------------------

--
-- Table structure for table `stock_in`
--

CREATE TABLE `stock_in` (
  `stock_in_id` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_in`
--

INSERT INTO `stock_in` (`stock_in_id`, `date`) VALUES
(1, '2024-07-30 12:33:10'),
(2, '2024-07-30 12:48:46'),
(3, '2024-07-30 13:19:43'),
(4, '2024-07-30 13:19:48'),
(5, '2024-08-01 14:15:22'),
(6, '2024-08-01 14:18:23'),
(7, '2024-08-01 14:32:45'),
(8, '2024-08-01 14:32:54'),
(9, '2024-08-01 15:19:41'),
(10, '2024-08-08 15:47:31'),
(11, '2024-08-08 16:02:45'),
(12, '2024-08-08 17:02:11'),
(13, '2024-08-09 16:46:19'),
(14, '2024-08-12 14:47:02'),
(16, '2024-08-13 11:54:11'),
(17, '2024-08-22 10:18:58'),
(18, '2024-08-22 10:19:15'),
(19, '2024-08-29 15:47:42'),
(20, '2024-08-29 15:47:49'),
(21, '2024-08-29 15:47:57'),
(22, '2024-08-29 15:51:56'),
(23, '2024-08-29 15:52:06'),
(24, '2024-08-29 15:52:41'),
(25, '2024-09-02 11:50:04'),
(26, '2024-09-02 11:51:28'),
(27, '2024-09-11 07:11:31'),
(28, '2024-09-11 11:59:40'),
(29, '2024-09-11 12:12:35'),
(30, '2024-09-11 12:14:29'),
(31, '2024-09-11 12:14:37'),
(32, '2024-09-14 07:27:21'),
(33, '2024-09-14 07:38:36'),
(36, '2024-09-14 09:43:28'),
(37, '2024-09-14 09:44:28'),
(38, '2024-09-14 09:47:20'),
(39, '2024-09-14 10:01:18'),
(40, '2024-09-14 10:05:37'),
(41, '2024-09-14 10:07:44'),
(42, '2024-09-14 10:10:24'),
(43, '2024-09-14 12:30:18'),
(44, '2024-09-21 10:56:08'),
(45, '2024-09-21 13:13:21'),
(46, '2024-09-21 13:14:22'),
(47, '2024-09-21 13:18:14'),
(48, '2024-09-21 13:23:00'),
(49, '2024-09-21 13:26:55'),
(50, '2024-09-21 13:29:01'),
(51, '2024-09-21 13:41:32'),
(52, '2024-09-21 13:42:14'),
(53, '2024-09-22 11:54:06'),
(54, '2024-09-22 12:54:06'),
(55, '2024-09-22 13:19:10'),
(56, '2024-09-23 05:41:29'),
(57, '2024-09-23 05:42:24'),
(58, '2024-09-23 05:45:47'),
(59, '2024-09-23 05:53:41'),
(60, '2024-09-23 05:56:23'),
(61, '2024-09-23 06:09:37'),
(62, '2024-09-23 06:24:16'),
(63, '2024-09-23 06:24:40'),
(64, '2024-09-23 11:59:14'),
(65, '2024-09-25 08:45:57'),
(66, '2024-09-28 16:23:15'),
(67, '2024-09-28 16:28:00'),
(68, '2024-09-28 16:32:18'),
(69, '2024-09-29 09:52:50'),
(70, '2024-09-29 11:37:49');

-- --------------------------------------------------------

--
-- Table structure for table `stock_in_items`
--

CREATE TABLE `stock_in_items` (
  `item_id` int(11) NOT NULL,
  `stock_in_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_in_items`
--

INSERT INTO `stock_in_items` (`item_id`, `stock_in_id`, `product_id`, `quantity`) VALUES
(1, NULL, 17, 10),
(2, NULL, 19, 10),
(3, 25, 17, 10),
(4, 25, 19, 10),
(5, 26, 8, 21),
(6, 26, 20, 10),
(7, 27, 2, 1),
(8, 27, 4, 1),
(9, 28, 22, 15),
(10, 29, 23, 23),
(11, 30, 8, 25),
(12, 31, 6, 23),
(13, 32, 1, 10),
(14, 33, 1, 10),
(17, 36, 22, 10),
(18, 37, 2, 10),
(19, 38, 2, 10),
(20, 39, 2, 20),
(21, 40, 1, 10),
(22, 41, 1, 10),
(23, 42, 1, 10),
(24, 43, 2, 10),
(25, 43, 3, 10),
(26, 44, 3, 10),
(27, 45, 25, 300),
(28, 46, 26, 10),
(29, 47, 26, 30),
(30, 48, 27, 50),
(31, 49, 28, 30),
(32, 50, 28, 100),
(33, 51, 29, 10),
(34, 52, 30, 50),
(35, 53, 1, 10),
(36, 54, 1, 40),
(37, 55, 1, 20),
(38, 56, 1, 10),
(39, 57, 17, 1),
(40, 58, 17, 20),
(41, 59, 31, 12),
(42, 60, 31, 20),
(43, 61, 8, 10),
(44, 62, 7, 10),
(45, 63, 6, 2),
(46, 64, 1, 10),
(47, 65, 30, 10),
(48, 66, 25, 12),
(49, 67, 17, 25),
(50, 68, 42, 50),
(51, 69, 28, 10),
(52, 70, 1, 10);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL,
  `employee_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `employee_id`) VALUES
(1, 'Admin', '$2y$10$4C9IgjEHS2HWusJuJANAYuFGnsgEOA2jf2pTnN5XeKz9ae4vu3Fs2', 'admin', NULL),
(6, 'User', '$2y$10$ozPpIMeO92d/rT.zJFpOn.nz741AfTpqnHU/w4puGjmRJp6u6BS/S', 'user', 9);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`company_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`employee_id`);

--
-- Indexes for table `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `product_costs`
--
ALTER TABLE `product_costs`
  ADD PRIMARY KEY (`cost_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `stock_in_id` (`stock_in_id`);

--
-- Indexes for table `stock_in`
--
ALTER TABLE `stock_in`
  ADD PRIMARY KEY (`stock_in_id`);

--
-- Indexes for table `stock_in_items`
--
ALTER TABLE `stock_in_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `stock_in_id` (`stock_in_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_employee` (`employee_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `company_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `images`
--
ALTER TABLE `images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=206;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `product_costs`
--
ALTER TABLE `product_costs`
  MODIFY `cost_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `stock_in`
--
ALTER TABLE `stock_in`
  MODIFY `stock_in_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `stock_in_items`
--
ALTER TABLE `stock_in_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`),
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`),
  ADD CONSTRAINT `products_ibfk_3` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);

--
-- Constraints for table `product_costs`
--
ALTER TABLE `product_costs`
  ADD CONSTRAINT `product_costs_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `product_costs_ibfk_2` FOREIGN KEY (`stock_in_id`) REFERENCES `stock_in` (`stock_in_id`);

--
-- Constraints for table `stock_in_items`
--
ALTER TABLE `stock_in_items`
  ADD CONSTRAINT `stock_in_items_ibfk_1` FOREIGN KEY (`stock_in_id`) REFERENCES `stock_in` (`stock_in_id`),
  ADD CONSTRAINT `stock_in_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
