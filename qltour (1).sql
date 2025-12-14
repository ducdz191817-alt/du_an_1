-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 14, 2025 at 12:41 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `qltour`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint NOT NULL,
  `user_id` bigint DEFAULT NULL,
  `action` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'create, update, delete, login, etc.',
  `model` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Tour, Booking, User, etc.',
  `model_id` bigint DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` bigint NOT NULL,
  `booking_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Mã booking (VD: BK20251208001)',
  `tour_id` bigint DEFAULT NULL,
  `created_by` bigint DEFAULT NULL COMMENT 'Người tạo booking',
  `customer_id` bigint DEFAULT NULL COMMENT 'Khách hàng chính',
  `assigned_guide_id` bigint DEFAULT NULL COMMENT 'Hướng dẫn viên được gán',
  `status` bigint DEFAULT NULL COMMENT 'Trạng thái booking',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `departure_date` datetime DEFAULT NULL COMMENT 'Ngày giờ khởi hành',
  `return_date` datetime DEFAULT NULL COMMENT 'Ngày giờ trở về',
  `schedule_detail` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'JSON: Lịch trình chi tiết',
  `service_detail` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'JSON: Chi tiết dịch vụ, khách hàng, giá',
  `diary` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'JSON: Nhật ký tour',
  `lists_file` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'JSON: Danh sách file',
  `notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Ghi chú',
  `internal_notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Ghi chú nội bộ',
  `total_amount` decimal(15,2) DEFAULT '0.00' COMMENT 'Tổng tiền',
  `paid_amount` decimal(15,2) DEFAULT '0.00' COMMENT 'Đã thanh toán',
  `remaining_amount` decimal(15,2) DEFAULT '0.00' COMMENT 'Còn lại',
  `discount_amount` decimal(15,2) DEFAULT '0.00' COMMENT 'Giảm giá',
  `discount_type` enum('percent','fixed') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_status` enum('pending','partial','paid','refunded') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `cancellation_reason` text COLLATE utf8mb4_unicode_ci COMMENT 'Lý do hủy',
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `cancelled_by` bigint DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `booking_code`, `tour_id`, `created_by`, `customer_id`, `assigned_guide_id`, `status`, `start_date`, `end_date`, `departure_date`, `return_date`, `schedule_detail`, `service_detail`, `diary`, `lists_file`, `notes`, `internal_notes`, `total_amount`, `paid_amount`, `remaining_amount`, `discount_amount`, `discount_type`, `discount_code`, `payment_status`, `cancellation_reason`, `cancelled_at`, `cancelled_by`, `created_at`, `updated_at`) VALUES
(1, 'BK20251209001', 1, 1, 1, 2, 1, '2025-12-20', '2025-12-22', '2025-12-20 06:00:00', '2025-12-22 18:00:00', NULL, '{\"customer\":{\"name\":\"Nguyễn Văn A\",\"phone\":\"0901234567\",\"email\":\"nguyenvana@gmail.com\",\"address\":\"123 Đường ABC, Quận 1, TP.HCM\"},\"customer_id\":1,\"booking_type\":\"individual\",\"adult\":2,\"child\":0,\"total_guests\":2,\"adult_price\":3200000,\"child_price\":0,\"total_amount\":6400000}', NULL, NULL, 'Khách hàng yêu cầu phòng view biển', NULL, 6400000.00, 0.00, 6400000.00, 0.00, NULL, NULL, 'pending', NULL, NULL, NULL, '2025-12-10 09:49:03', '2025-12-10 09:49:03'),
(2, 'BK20251209002', 2, 1, 2, 3, 2, '2025-12-25', '2025-12-26', '2025-12-25 07:00:00', '2025-12-26 20:00:00', NULL, '{\"customer\":{\"name\":\"Trần Thị B\",\"phone\":\"0912345678\",\"email\":\"tranthib@gmail.com\",\"address\":\"456 Đường XYZ, Quận 2, TP.HCM\"},\"customer_id\":2,\"booking_type\":\"group\",\"adult\":4,\"child\":1,\"total_guests\":5,\"adult_price\":2500000,\"child_price\":1750000,\"total_amount\":11750000}', NULL, NULL, 'Đoàn gia đình 5 người', NULL, 11750000.00, 0.00, 11750000.00, 0.00, NULL, NULL, 'pending', NULL, NULL, NULL, '2025-12-10 09:49:03', '2025-12-10 09:49:03'),
(3, 'BK20251209003', 3, 1, 3, 2, 3, '2026-01-10', '2026-01-12', '2026-01-10 08:00:00', '2026-01-12 19:00:00', NULL, '{\"customer\":{\"name\":\"Lê Văn C\",\"phone\":\"0923456789\",\"email\":\"levanc@gmail.com\",\"address\":\"789 Đường DEF, Quận 3, Hà Nội\"},\"customer_id\":3,\"booking_type\":\"individual\",\"adult\":2,\"child\":0,\"total_guests\":2,\"adult_price\":4200000,\"child_price\":0,\"total_amount\":8400000}', NULL, NULL, 'Đã đặt cọc 3 triệu', NULL, 8400000.00, 3000000.00, 5400000.00, 0.00, NULL, NULL, 'partial', NULL, NULL, NULL, '2025-12-10 09:49:03', '2025-12-10 09:49:03'),
(4, 'BK20251209004', 4, 1, 1, 2, 4, '2025-12-08', '2025-12-12', '2025-12-08 10:00:00', '2025-12-12 22:00:00', NULL, '{\"customer\":{\"name\":\"Nguyễn Văn A\",\"phone\":\"0901234567\",\"email\":\"nguyenvana@gmail.com\",\"address\":\"123 Đường ABC, Quận 1, TP.HCM\"},\"customer_id\":1,\"booking_type\":\"group\",\"adult\":6,\"child\":2,\"total_guests\":8,\"adult_price\":11000000,\"child_price\":7700000,\"total_amount\":81400000}', NULL, NULL, 'Tour đang diễn ra, khách rất hài lòng', NULL, 81400000.00, 77400000.00, 0.00, 4000000.00, 'fixed', 'SUMMER2025', 'paid', NULL, NULL, NULL, '2025-12-09 09:49:03', '2025-12-10 09:49:03'),
(5, 'BK20251209005', 6, 1, 2, 3, 5, '2025-11-15', '2025-11-18', '2025-11-15 09:00:00', '2025-11-18 17:00:00', NULL, '{\"customer\":{\"name\":\"Trần Thị B\",\"phone\":\"0912345678\",\"email\":\"tranthib@gmail.com\",\"address\":\"456 Đường XYZ, Quận 2, TP.HCM\"},\"customer_id\":2,\"booking_type\":\"individual\",\"adult\":2,\"child\":0,\"total_guests\":2,\"adult_price\":5000000,\"child_price\":0,\"total_amount\":10000000}', NULL, NULL, 'Tour đã hoàn tất thành công', NULL, 10000000.00, 10000000.00, 0.00, 0.00, NULL, NULL, 'paid', NULL, NULL, NULL, '2025-11-20 09:49:03', '2025-12-07 09:49:03'),
(6, 'BK20251209006', 7, 1, 3, NULL, 1, '2026-02-01', '2026-02-03', '2026-02-01 07:00:00', '2026-02-03 18:00:00', NULL, '{\"customer\":{\"name\":\"Lê Văn C\",\"phone\":\"0923456789\",\"email\":\"levanc@gmail.com\",\"address\":\"789 Đường DEF, Quận 3, Hà Nội\"},\"customer_id\":3,\"booking_type\":\"individual\",\"adult\":1,\"child\":0,\"total_guests\":1,\"adult_price\":3500000,\"child_price\":0,\"total_amount\":3500000}', NULL, NULL, 'Khách hàng đặt tour đơn', NULL, 3500000.00, 0.00, 3500000.00, 0.00, NULL, NULL, 'pending', NULL, NULL, NULL, '2025-12-10 09:49:03', '2025-12-10 09:49:03'),
(7, 'BK20251209007', 8, 1, 1, 2, 2, '2026-03-15', '2026-03-18', '2026-03-15 08:00:00', '2026-03-18 23:00:00', NULL, '{\"customer\":{\"name\":\"Nguyễn Văn A\",\"phone\":\"0901234567\",\"email\":\"nguyenvana@gmail.com\",\"address\":\"123 Đường ABC, Quận 1, TP.HCM\"},\"customer_id\":1,\"booking_type\":\"group\",\"adult\":3,\"child\":1,\"total_guests\":4,\"adult_price\":14000000,\"child_price\":9800000,\"total_amount\":51800000}', NULL, NULL, 'Đã đặt cọc 10 triệu, còn lại thanh toán trước ngày đi', NULL, 51800000.00, 10000000.00, 39800000.00, 2000000.00, 'fixed', 'WELCOME10', 'partial', NULL, NULL, NULL, '2025-12-10 09:49:03', '2025-12-10 09:49:03'),
(8, 'BK20251209008', 1, 1, 2, NULL, 6, '2025-12-05', '2025-12-07', NULL, NULL, NULL, '{\"customer\":{\"name\":\"Trần Thị B\",\"phone\":\"0912345678\",\"email\":\"tranthib@gmail.com\",\"address\":\"456 Đường XYZ, Quận 2, TP.HCM\"},\"customer_id\":2,\"booking_type\":\"individual\",\"adult\":2,\"child\":0,\"total_guests\":2,\"adult_price\":3200000,\"child_price\":0,\"total_amount\":6400000}', NULL, NULL, 'Khách hàng hủy do lý do cá nhân', NULL, 6400000.00, 0.00, 6400000.00, 0.00, NULL, NULL, 'refunded', NULL, '2025-12-08 09:49:03', 1, '2025-12-05 09:49:03', '2025-12-10 09:49:03');

-- --------------------------------------------------------

--
-- Table structure for table `booking_status_logs`
--

CREATE TABLE `booking_status_logs` (
  `id` bigint NOT NULL,
  `booking_id` bigint DEFAULT NULL,
  `old_status` bigint DEFAULT NULL,
  `new_status` bigint DEFAULT NULL,
  `changed_by` bigint DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `changed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `booking_status_logs`
--

INSERT INTO `booking_status_logs` (`id`, `booking_id`, `old_status`, `new_status`, `changed_by`, `note`, `changed_at`) VALUES
(1, 1, NULL, 1, 1, 'Tạo booking mới', '2025-12-10 09:49:03'),
(2, 2, NULL, 1, 1, 'Tạo booking mới', '2025-12-10 09:49:03'),
(3, 2, 1, 2, 1, 'Xác nhận booking', '2025-12-10 08:49:03'),
(4, 3, NULL, 1, 1, 'Tạo booking mới', '2025-12-08 09:49:03'),
(5, 3, 1, 2, 1, 'Xác nhận booking', '2025-12-08 09:49:03'),
(6, 3, 2, 3, 1, 'Khách đã đặt cọc', '2025-12-09 09:49:03'),
(7, 4, NULL, 1, 1, 'Tạo booking mới', '2025-11-30 09:49:03'),
(8, 4, 1, 2, 1, 'Xác nhận booking', '2025-12-01 09:49:03'),
(9, 4, 2, 3, 1, 'Khách đã đặt cọc', '2025-12-02 09:49:03'),
(10, 4, 3, 4, 1, 'Tour bắt đầu', '2025-12-09 09:49:03'),
(11, 5, NULL, 1, 1, 'Tạo booking mới', '2025-11-15 09:49:03'),
(12, 5, 1, 2, 1, 'Xác nhận booking', '2025-11-16 09:49:03'),
(13, 5, 2, 3, 1, 'Khách đã đặt cọc', '2025-11-17 09:49:03'),
(14, 5, 3, 4, 1, 'Tour bắt đầu', '2025-11-20 09:49:03'),
(15, 5, 4, 5, 1, 'Tour hoàn tất', '2025-12-07 09:49:03'),
(16, 6, NULL, 1, 1, 'Tạo booking mới', '2025-12-10 09:49:03'),
(17, 7, NULL, 1, 1, 'Tạo booking mới', '2025-12-09 09:49:03'),
(18, 7, 1, 2, 1, 'Xác nhận booking', '2025-12-10 09:49:03'),
(19, 8, NULL, 1, 1, 'Tạo booking mới', '2025-12-05 09:49:03'),
(20, 8, 1, 6, 1, 'Khách hàng yêu cầu hủy', '2025-12-08 09:49:03');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` bigint DEFAULT NULL COMMENT 'Danh mục cha (cho danh mục con)',
  `sort_order` int DEFAULT '0',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `image`, `parent_id`, `sort_order`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Tour trong nước', 'tour-trong-nuoc', 'Tour du lịch nội địa Việt Nam', NULL, NULL, 1, 1, '2025-12-10 09:48:38', '2025-12-10 09:48:38'),
(2, 'Tour quốc tế', 'tour-quoc-te', 'Tour du lịch quốc tế', NULL, NULL, 2, 1, '2025-12-10 09:48:38', '2025-12-10 09:48:38'),
(3, 'Tour miền Bắc', 'tour-mien-bac', 'Tour du lịch miền Bắc', NULL, 1, 1, 1, '2025-12-10 09:48:38', '2025-12-10 09:48:38'),
(4, 'Tour miền Trung', 'tour-mien-trung', 'Tour du lịch miền Trung', NULL, 1, 2, 1, '2025-12-10 09:48:38', '2025-12-10 09:48:38'),
(5, 'Tour miền Nam', 'tour-mien-nam', 'Tour du lịch miền Nam', NULL, 1, 3, 1, '2025-12-10 09:48:38', '2025-12-10 09:48:38'),
(6, 'Tour Châu Á', 'tour-chau-a', 'Tour du lịch các nước Châu Á', NULL, 2, 1, 1, '2025-12-10 09:48:38', '2025-12-10 09:48:38'),
(7, 'Tour Châu Âu', 'tour-chau-au', 'Tour du lịch các nước Châu Âu', NULL, 2, 2, 1, '2025-12-10 09:48:38', '2025-12-10 09:48:38');

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` bigint NOT NULL,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `type` enum('percent','fixed') COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` decimal(10,2) NOT NULL COMMENT 'Giá trị giảm',
  `min_amount` decimal(15,2) DEFAULT NULL COMMENT 'Đơn hàng tối thiểu',
  `max_discount` decimal(15,2) DEFAULT NULL COMMENT 'Giảm tối đa',
  `usage_limit` int DEFAULT NULL COMMENT 'Số lần sử dụng tối đa',
  `used_count` int DEFAULT '0',
  `user_limit` int DEFAULT '1' COMMENT 'Số lần mỗi user được dùng',
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `applicable_tours` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'JSON: Danh sách tour áp dụng (null = tất cả)',
  `applicable_categories` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'JSON: Danh sách danh mục áp dụng',
  `status` tinyint DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `coupons`
--

INSERT INTO `coupons` (`id`, `code`, `name`, `description`, `type`, `value`, `min_amount`, `max_discount`, `usage_limit`, `used_count`, `user_limit`, `start_date`, `end_date`, `applicable_tours`, `applicable_categories`, `status`, `created_at`, `updated_at`) VALUES
(1, 'WELCOME10', 'Giảm 10% cho khách mới', 'Giảm 10% cho đơn hàng đầu tiên', 'percent', 10.00, 2000000.00, NULL, 100, 0, 1, '2025-12-10 09:48:39', '2026-12-10 09:48:39', NULL, NULL, 1, '2025-12-10 09:48:39', '2025-12-10 09:48:39'),
(2, 'SUMMER2025', 'Giảm 500.000đ mùa hè', 'Giảm 500.000đ cho tour mùa hè', 'fixed', 500000.00, 5000000.00, NULL, 50, 0, 1, '2025-12-10 09:48:39', '2025-08-31 23:59:59', NULL, NULL, 1, '2025-12-10 09:48:39', '2025-12-10 09:48:39');

-- --------------------------------------------------------

--
-- Table structure for table `coupon_usage`
--

CREATE TABLE `coupon_usage` (
  `id` bigint NOT NULL,
  `coupon_id` bigint NOT NULL,
  `booking_id` bigint NOT NULL,
  `customer_id` bigint DEFAULT NULL,
  `discount_amount` decimal(15,2) NOT NULL,
  `used_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `company` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nationality` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `passport_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `passport_expiry` date DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `phone`, `email`, `address`, `company`, `tax_code`, `date_of_birth`, `gender`, `nationality`, `passport_number`, `passport_expiry`, `notes`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Nguyễn Văn A', '0901234567', 'nguyenvana@gmail.com', '123 Đường ABC, Quận 1, TP.HCM', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-12-10 09:48:38', '2025-12-10 09:48:38'),
(2, 'Trần Thị B', '0912345678', 'tranthib@gmail.com', '456 Đường XYZ, Quận 2, TP.HCM', 'Công ty ABC', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-12-10 09:48:38', '2025-12-10 09:48:38'),
(3, 'Lê Văn C', '0923456789', 'levanc@gmail.com', '789 Đường DEF, Quận 3, Hà Nội', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-12-10 09:48:38', '2025-12-10 09:48:38');

-- --------------------------------------------------------

--
-- Table structure for table `destinations`
--

CREATE TABLE `destinations` (
  `id` bigint NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `province` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `destinations`
--

INSERT INTO `destinations` (`id`, `name`, `slug`, `country`, `province`, `city`, `description`, `image`, `latitude`, `longitude`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Hạ Long', 'ha-long', 'Việt Nam', 'Quảng Ninh', 'Hạ Long', 'Vịnh Hạ Long - Di sản thiên nhiên thế giới', NULL, NULL, NULL, 1, '2025-12-10 09:48:38', '2025-12-10 09:48:38'),
(2, 'Sapa', 'sapa', 'Việt Nam', 'Lào Cai', 'Sapa', 'Sapa - Thị trấn trong sương mù', NULL, NULL, NULL, 1, '2025-12-10 09:48:38', '2025-12-10 09:48:38'),
(3, 'Đà Nẵng', 'da-nang', 'Việt Nam', 'Đà Nẵng', 'Đà Nẵng', 'Thành phố đáng sống nhất Việt Nam', NULL, NULL, NULL, 1, '2025-12-10 09:48:38', '2025-12-10 09:48:38'),
(4, 'Hội An', 'hoi-an', 'Việt Nam', 'Quảng Nam', 'Hội An', 'Phố cổ Hội An - Di sản văn hóa thế giới', NULL, NULL, NULL, 1, '2025-12-10 09:48:38', '2025-12-10 09:48:38'),
(5, 'Nha Trang', 'nha-trang', 'Việt Nam', 'Khánh Hòa', 'Nha Trang', 'Thành phố biển xinh đẹp', NULL, NULL, NULL, 1, '2025-12-10 09:48:38', '2025-12-10 09:48:38'),
(6, 'Phú Quốc', 'phu-quoc', 'Việt Nam', 'Kiên Giang', 'Phú Quốc', 'Đảo ngọc Phú Quốc', NULL, NULL, NULL, 1, '2025-12-10 09:48:38', '2025-12-10 09:48:38'),
(7, 'Bangkok', 'bangkok', 'Thái Lan', NULL, 'Bangkok', 'Thủ đô của Thái Lan', NULL, NULL, NULL, 1, '2025-12-10 09:48:38', '2025-12-10 09:48:38'),
(8, 'Tokyo', 'tokyo', 'Nhật Bản', NULL, 'Tokyo', 'Thủ đô của Nhật Bản', NULL, NULL, NULL, 1, '2025-12-10 09:48:38', '2025-12-10 09:48:38'),
(9, 'Singapore', 'singapore', 'Singapore', NULL, 'Singapore', 'Đảo quốc sư tử', NULL, NULL, NULL, 1, '2025-12-10 09:48:38', '2025-12-10 09:48:38');

-- --------------------------------------------------------

--
-- Table structure for table `guide_profiles`
--

CREATE TABLE `guide_profiles` (
  `id` bigint NOT NULL,
  `user_id` bigint NOT NULL,
  `birthdate` date DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_card` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'CMND/CCCD',
  `address` text COLLATE utf8mb4_unicode_ci,
  `certificate` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'JSON: Chứng chỉ',
  `languages` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'JSON: Ngôn ngữ',
  `experience` text COLLATE utf8mb4_unicode_ci COMMENT 'Kinh nghiệm',
  `history` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'JSON: Lịch sử tour',
  `rating` decimal(3,2) DEFAULT '0.00',
  `review_count` int DEFAULT '0',
  `health_status` text COLLATE utf8mb4_unicode_ci,
  `group_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Loại đoàn (nội địa/quốc tế)',
  `speciality` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Chuyên môn',
  `availability_status` enum('available','busy','unavailable') COLLATE utf8mb4_unicode_ci DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `guide_profiles`
--

INSERT INTO `guide_profiles` (`id`, `user_id`, `birthdate`, `avatar`, `phone`, `id_card`, `address`, `certificate`, `languages`, `experience`, `history`, `rating`, `review_count`, `health_status`, `group_type`, `speciality`, `availability_status`, `created_at`, `updated_at`) VALUES
(1, 2, '1990-01-15', NULL, '0912345678', NULL, NULL, '[\"HDV quốc tế\", \"HDV nội địa\"]', '[\"Tiếng Anh\", \"Tiếng Việt\", \"Tiếng Trung\"]', '10 năm kinh nghiệm hướng dẫn tour trong nước và quốc tế', NULL, 4.80, 0, NULL, 'quốc tế', 'chuyên tuyến miền Bắc', 'available', '2025-12-10 09:48:38', '2025-12-10 09:48:38'),
(2, 3, '1988-05-20', NULL, '0923456789', NULL, NULL, '[\"HDV nội địa\"]', '[\"Tiếng Việt\", \"Tiếng Anh\"]', '8 năm kinh nghiệm hướng dẫn tour nội địa', NULL, 4.70, 0, NULL, 'nội địa', 'chuyên khách đoàn', 'available', '2025-12-10 09:48:38', '2025-12-10 09:48:38');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint NOT NULL,
  `user_id` bigint DEFAULT NULL COMMENT 'null = thông báo cho tất cả',
  `type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'booking, payment, review, system',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_read` tinyint DEFAULT '0',
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint NOT NULL,
  `booking_id` bigint NOT NULL,
  `payment_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Mã thanh toán',
  `payment_method_id` bigint DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT 'VND',
  `status` enum('pending','processing','completed','failed','cancelled','refunded') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `transaction_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mã giao dịch từ cổng thanh toán',
  `payment_date` datetime DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

CREATE TABLE `payment_methods` (
  `id` bigint NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint DEFAULT '1',
  `sort_order` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_methods`
--

INSERT INTO `payment_methods` (`id`, `name`, `code`, `description`, `icon`, `is_active`, `sort_order`) VALUES
(1, 'Tiền mặt', 'cash', 'Thanh toán bằng tiền mặt', NULL, 1, 1),
(2, 'Chuyển khoản', 'bank_transfer', 'Chuyển khoản ngân hàng', NULL, 1, 2),
(3, 'Thẻ tín dụng', 'credit_card', 'Thanh toán bằng thẻ tín dụng', NULL, 1, 3),
(4, 'Ví điện tử', 'e_wallet', 'Thanh toán qua ví điện tử', NULL, 1, 4);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` bigint NOT NULL,
  `tour_id` bigint NOT NULL,
  `booking_id` bigint DEFAULT NULL,
  `customer_id` bigint DEFAULT NULL,
  `rating` tinyint NOT NULL COMMENT '1-5 sao',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci,
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'JSON: Hình ảnh đánh giá',
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `helpful_count` int DEFAULT '0' COMMENT 'Số người thấy hữu ích',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `review_ratings`
--

CREATE TABLE `review_ratings` (
  `id` bigint NOT NULL,
  `review_id` bigint NOT NULL,
  `criteria` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tiêu chí (service, guide, food, accommodation, value)',
  `rating` tinyint NOT NULL COMMENT '1-5'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint NOT NULL,
  `key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'text' COMMENT 'text, number, boolean, json',
  `group` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'general',
  `description` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `type`, `group`, `description`) VALUES
(1, 'site_name', 'Website Quản Lý Tour', 'text', 'general', 'Tên website'),
(2, 'site_email', 'info@qltour.com', 'text', 'general', 'Email liên hệ'),
(3, 'site_phone', '1900123456', 'text', 'general', 'Số điện thoại'),
(4, 'currency', 'VND', 'text', 'general', 'Đơn vị tiền tệ'),
(5, 'booking_prefix', 'BK', 'text', 'booking', 'Tiền tố mã booking'),
(6, 'payment_due_days', '7', 'number', 'booking', 'Số ngày đến hạn thanh toán'),
(7, 'cancellation_policy', 'Hủy trước 7 ngày: hoàn 100%, hủy trước 3 ngày: hoàn 50%', 'text', 'booking', 'Chính sách hủy tour');

-- --------------------------------------------------------

--
-- Table structure for table `tours`
--

CREATE TABLE `tours` (
  `id` bigint NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `short_description` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_id` bigint DEFAULT NULL,
  `destination_id` bigint DEFAULT NULL,
  `schedule` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'JSON: Lịch trình chi tiết',
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'JSON: Danh sách hình ảnh',
  `prices` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'JSON: Giá theo loại',
  `policies` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'JSON: Chính sách',
  `suppliers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'JSON: Nhà cung cấp',
  `inclusions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'JSON: Bao gồm',
  `exclusions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'JSON: Không bao gồm',
  `price` decimal(15,2) DEFAULT NULL COMMENT 'Giá cơ bản',
  `sale_price` decimal(15,2) DEFAULT NULL COMMENT 'Giá khuyến mãi',
  `duration` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Thời lượng (VD: 3 ngày 2 đêm)',
  `duration_days` int DEFAULT NULL COMMENT 'Số ngày',
  `duration_nights` int DEFAULT NULL COMMENT 'Số đêm',
  `max_guests` int DEFAULT NULL COMMENT 'Số khách tối đa',
  `min_guests` int DEFAULT '1' COMMENT 'Số khách tối thiểu',
  `departure_location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Điểm khởi hành',
  `return_location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Điểm trở về',
  `rating` decimal(3,2) DEFAULT '0.00' COMMENT 'Đánh giá trung bình',
  `review_count` int DEFAULT '0' COMMENT 'Số lượng đánh giá',
  `view_count` int DEFAULT '0' COMMENT 'Số lượt xem',
  `booking_count` int DEFAULT '0' COMMENT 'Số lượt đặt',
  `is_featured` tinyint DEFAULT '0' COMMENT 'Tour nổi bật',
  `is_hot` tinyint DEFAULT '0' COMMENT 'Tour hot',
  `status` tinyint NOT NULL DEFAULT '1' COMMENT '1=Active, 0=Inactive, 2=Draft',
  `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text COLLATE utf8mb4_unicode_ci,
  `meta_keywords` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tours`
--

INSERT INTO `tours` (`id`, `name`, `slug`, `description`, `short_description`, `category_id`, `destination_id`, `schedule`, `images`, `prices`, `policies`, `suppliers`, `inclusions`, `exclusions`, `price`, `sale_price`, `duration`, `duration_days`, `duration_nights`, `max_guests`, `min_guests`, `departure_location`, `return_location`, `rating`, `review_count`, `view_count`, `booking_count`, `is_featured`, `is_hot`, `status`, `meta_title`, `meta_description`, `meta_keywords`, `created_at`, `updated_at`) VALUES
(1, 'Tour Hạ Long 3 Ngày 2 Đêm', 'tour-ha-long-3-ngay-2-dem', 'Khám phá vịnh Hạ Long - Di sản thiên nhiên thế giới với những hang động kỳ vĩ và cảnh quan tuyệt đẹp.', 'Tour Hạ Long 3N2Đ - Khám phá vịnh di sản', 3, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 3500000.00, 3200000.00, '3 ngày 2 đêm', 3, 2, 25, 2, 'Hà Nội', NULL, 4.50, 12, 0, 0, 1, 1, 1, NULL, NULL, NULL, '2025-12-10 09:48:38', '2025-12-10 09:48:38'),
(2, 'Tour Sapa 2 Ngày 1 Đêm', 'tour-sapa-2-ngay-1-dem', 'Trải nghiệm văn hóa dân tộc và cảnh quan núi rừng Tây Bắc tại Sapa.', 'Tour Sapa 2N1Đ - Trải nghiệm văn hóa Tây Bắc', 3, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2500000.00, NULL, '2 ngày 1 đêm', 2, 1, 20, 2, 'Hà Nội', NULL, 4.30, 8, 0, 0, 1, 0, 1, NULL, NULL, NULL, '2025-12-10 09:48:38', '2025-12-10 09:48:38'),
(3, 'Tour Đà Nẵng - Hội An - Bà Nà Hills', 'tour-da-nang-hoi-an-ba-na-hills', 'Khám phá con đường di sản miền Trung, chiêm ngưỡng Cầu Vàng hùng vĩ và trải nghiệm không gian cổ kính tại Hội An.', 'Tour Đà Nẵng - Hội An - Bà Nà Hills 3N2Đ', 4, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 4500000.00, 4200000.00, '3 ngày 2 đêm', 3, 2, 25, 2, 'Sân bay Đà Nẵng', NULL, 4.70, 15, 0, 0, 1, 1, 1, NULL, NULL, NULL, '2025-12-10 09:48:38', '2025-12-10 09:48:38'),
(4, 'Tour Thái Lan: Bangkok - Pattaya 5 Ngày 4 Đêm', 'tour-thai-lan-bangkok-pattaya-5-ngay-4-dem', 'Du lịch Bangkok - Pattaya với nhiều điểm tham quan hấp dẫn và ẩm thực đặc sắc.', 'Tour Thái Lan 5N4Đ - Bangkok Pattaya', 6, 7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 12000000.00, 11000000.00, '5 ngày 4 đêm', 5, 4, 30, 4, 'Sân bay Nội Bài', NULL, 4.60, 20, 0, 0, 1, 1, 1, NULL, NULL, NULL, '2025-12-10 09:48:38', '2025-12-10 09:48:38'),
(5, 'Tour Nhật Bản: Tokyo - Osaka 7 Ngày 6 Đêm', 'tour-nhat-ban-tokyo-osaka-7-ngay-6-dem', 'Trải nghiệm văn hóa xứ sở Phù Tang, ngắm hoa anh đào và tham quan núi Phú Sĩ huyền thoại.', 'Tour Nhật Bản 7N6Đ - Tokyo Osaka', 6, 8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 28990000.00, 26990000.00, '7 ngày 6 đêm', 7, 6, 20, 4, 'Sân bay Nội Bài', NULL, 4.80, 25, 0, 0, 1, 1, 1, NULL, NULL, NULL, '2025-12-10 09:48:38', '2025-12-10 09:48:38'),
(6, 'Tour Phú Quốc 4 Ngày 3 Đêm', 'tour-phu-quoc-4-ngay-3-dem', 'Nghỉ dưỡng tại đảo ngọc Phú Quốc với bãi biển đẹp và resort cao cấp.', 'Tour Phú Quốc 4N3Đ - Nghỉ dưỡng đảo ngọc', 5, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 5500000.00, 5000000.00, '4 ngày 3 đêm', 4, 3, 30, 2, 'Sân bay Phú Quốc', NULL, 4.40, 18, 0, 0, 1, 0, 1, NULL, NULL, NULL, '2025-12-10 09:48:38', '2025-12-10 09:48:38'),
(7, 'Tour Nha Trang 3 Ngày 2 Đêm', 'tour-nha-trang-3-ngay-2-dem', 'Khám phá thành phố biển Nha Trang với nhiều hoạt động thú vị.', 'Tour Nha Trang 3N2Đ - Thành phố biển', 5, 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 3800000.00, 3500000.00, '3 ngày 2 đêm', 3, 2, 25, 2, 'Sân bay Cam Ranh', NULL, 4.20, 10, 0, 0, 0, 0, 1, NULL, NULL, NULL, '2025-12-10 09:48:38', '2025-12-10 09:48:38'),
(8, 'Tour Singapore 4 Ngày 3 Đêm', 'tour-singapore-4-ngay-3-dem', 'Du lịch đảo quốc sư tử Singapore với nhiều điểm tham quan hiện đại.', 'Tour Singapore 4N3Đ - Đảo quốc sư tử', 6, 9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 15000000.00, 14000000.00, '4 ngày 3 đêm', 4, 3, 25, 4, 'Sân bay Nội Bài', NULL, 4.50, 12, 0, 0, 1, 0, 1, NULL, NULL, NULL, '2025-12-10 09:48:38', '2025-12-10 09:48:38');

-- --------------------------------------------------------

--
-- Table structure for table `tour_destinations`
--

CREATE TABLE `tour_destinations` (
  `id` bigint NOT NULL,
  `tour_id` bigint NOT NULL,
  `destination_id` bigint NOT NULL,
  `order` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tour_guests`
--

CREATE TABLE `tour_guests` (
  `id` bigint NOT NULL,
  `booking_id` bigint NOT NULL,
  `fullname` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dob` date DEFAULT NULL,
  `gender` enum('male','female','other') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `passport_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `passport_expiry` date DEFAULT NULL,
  `nationality` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `special_requirements` text COLLATE utf8mb4_unicode_ci COMMENT 'Yêu cầu đặc biệt',
  `is_primary` tinyint DEFAULT '0' COMMENT 'Khách hàng chính',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tour_statuses`
--

CREATE TABLE `tour_statuses` (
  `id` bigint NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `color` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '#6c757d',
  `order` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tour_statuses`
--

INSERT INTO `tour_statuses` (`id`, `name`, `slug`, `description`, `color`, `order`) VALUES
(1, 'Chờ xác nhận', 'cho-xac-nhan', 'Chưa xác nhận bởi admin/khách', '#ffc107', 1),
(2, 'Đã xác nhận', 'da-xac-nhan', 'Đã xác nhận booking', '#17a2b8', 2),
(3, 'Đã cọc', 'da-coc', 'Khách đã đặt cọc giữ chỗ', '#007bff', 3),
(4, 'Đang diễn ra', 'dang-dien-ra', 'Tour đang diễn ra', '#28a745', 4),
(5, 'Hoàn tất', 'hoan-tat', 'Tour đã kết thúc thành công', '#6c757d', 5),
(6, 'Hủy', 'huy', 'Tour đã bị hủy', '#dc3545', 6);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','guide','staff','customer') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'customer',
  `status` tinyint NOT NULL DEFAULT '1' COMMENT '1=Active, 0=Inactive',
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `status`, `phone`, `avatar`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@gmail.com', '$2y$10$8myHjUmVq9SaXNRUI2e1eOD8IHZTIGHd3kIb1THhW7i.NAN0otgqa', 'admin', 1, '0901234567', NULL, '2025-12-14 12:40:29', '2025-12-10 09:48:38', '2025-12-14 12:40:29'),
(2, 'Nguyễn Văn Hướng Dẫn', 'guide@gmail.com', '$2y$10$8myHjUmVq9SaXNRUI2e1eOD8IHZTIGHd3kIb1THhW7i.NAN0otgqa', 'guide', 1, '0912345678', NULL, '2025-12-14 12:21:55', '2025-12-10 09:48:38', '2025-12-14 12:21:55'),
(3, 'Trần Thị Hướng Dẫn', 'guide2@gmail.com', '$2y$10$q8y3w3YOkxorUu0iaceTL.ntvtR5UX/dxxWcHvlFaINKF.75sME8i', 'guide', 1, '0923456789', NULL, NULL, '2025-12-10 09:48:38', '2025-12-10 09:48:38'),
(4, 'Nhân viên Văn phòng', 'staff@gmail.com', '$2y$10$8myHjUmVq9SaXNRUI2e1eOD8IHZTIGHd3kIb1THhW7i.NAN0otgqa', 'staff', 1, '0934567890', NULL, NULL, '2025-12-10 09:48:38', '2025-12-10 09:48:38');

-- --------------------------------------------------------

--
-- Table structure for table `user_permissions`
--

CREATE TABLE `user_permissions` (
  `id` bigint NOT NULL,
  `user_id` bigint NOT NULL,
  `permission` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `action` (`action`),
  ADD KEY `model` (`model`),
  ADD KEY `created_at` (`created_at`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_code` (`booking_code`),
  ADD KEY `tour_id` (`tour_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `assigned_guide_id` (`assigned_guide_id`),
  ADD KEY `status` (`status`),
  ADD KEY `payment_status` (`payment_status`),
  ADD KEY `start_date` (`start_date`);

--
-- Indexes for table `booking_status_logs`
--
ALTER TABLE `booking_status_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `old_status` (`old_status`),
  ADD KEY `new_status` (`new_status`),
  ADD KEY `changed_by` (`changed_by`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `coupon_usage`
--
ALTER TABLE `coupon_usage`
  ADD PRIMARY KEY (`id`),
  ADD KEY `coupon_id` (`coupon_id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `phone` (`phone`),
  ADD KEY `email` (`email`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `destinations`
--
ALTER TABLE `destinations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `country` (`country`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `guide_profiles`
--
ALTER TABLE `guide_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `type` (`type`),
  ADD KEY `is_read` (`is_read`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payment_code` (`payment_code`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `payment_method_id` (`payment_method_id`),
  ADD KEY `status` (`status`),
  ADD KEY `payments_ibfk_3` (`created_by`);

--
-- Indexes for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tour_id` (`tour_id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `review_ratings`
--
ALTER TABLE `review_ratings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `review_id` (`review_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `key` (`key`),
  ADD KEY `group` (`group`);

--
-- Indexes for table `tours`
--
ALTER TABLE `tours`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `destination_id` (`destination_id`),
  ADD KEY `status` (`status`),
  ADD KEY `is_featured` (`is_featured`),
  ADD KEY `is_hot` (`is_hot`);

--
-- Indexes for table `tour_destinations`
--
ALTER TABLE `tour_destinations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tour_destination` (`tour_id`,`destination_id`),
  ADD KEY `destination_id` (`destination_id`);

--
-- Indexes for table `tour_guests`
--
ALTER TABLE `tour_guests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `tour_statuses`
--
ALTER TABLE `tour_statuses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role` (`role`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `user_permissions`
--
ALTER TABLE `user_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_permission` (`user_id`,`permission`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `booking_status_logs`
--
ALTER TABLE `booking_status_logs`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `coupon_usage`
--
ALTER TABLE `coupon_usage`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `destinations`
--
ALTER TABLE `destinations`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `guide_profiles`
--
ALTER TABLE `guide_profiles`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `review_ratings`
--
ALTER TABLE `review_ratings`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tours`
--
ALTER TABLE `tours`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tour_destinations`
--
ALTER TABLE `tour_destinations`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tour_guests`
--
ALTER TABLE `tour_guests`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tour_statuses`
--
ALTER TABLE `tour_statuses`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user_permissions`
--
ALTER TABLE `user_permissions`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bookings_ibfk_3` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bookings_ibfk_4` FOREIGN KEY (`assigned_guide_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bookings_ibfk_5` FOREIGN KEY (`status`) REFERENCES `tour_statuses` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `booking_status_logs`
--
ALTER TABLE `booking_status_logs`
  ADD CONSTRAINT `booking_status_logs_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_status_logs_ibfk_2` FOREIGN KEY (`old_status`) REFERENCES `tour_statuses` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `booking_status_logs_ibfk_3` FOREIGN KEY (`new_status`) REFERENCES `tour_statuses` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `booking_status_logs_ibfk_4` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `coupon_usage`
--
ALTER TABLE `coupon_usage`
  ADD CONSTRAINT `coupon_usage_ibfk_1` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `coupon_usage_ibfk_2` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `coupon_usage_ibfk_3` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `guide_profiles`
--
ALTER TABLE `guide_profiles`
  ADD CONSTRAINT `guide_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payments_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `reviews_ibfk_3` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `review_ratings`
--
ALTER TABLE `review_ratings`
  ADD CONSTRAINT `review_ratings_ibfk_1` FOREIGN KEY (`review_id`) REFERENCES `reviews` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tours`
--
ALTER TABLE `tours`
  ADD CONSTRAINT `tours_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tours_ibfk_2` FOREIGN KEY (`destination_id`) REFERENCES `destinations` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `tour_destinations`
--
ALTER TABLE `tour_destinations`
  ADD CONSTRAINT `tour_destinations_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tour_destinations_ibfk_2` FOREIGN KEY (`destination_id`) REFERENCES `destinations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tour_guests`
--
ALTER TABLE `tour_guests`
  ADD CONSTRAINT `tour_guests_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_permissions`
--
ALTER TABLE `user_permissions`
  ADD CONSTRAINT `user_permissions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
