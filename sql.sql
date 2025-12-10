-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 08, 2025 at 09:25 AM
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
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` bigint NOT NULL,
  `tour_id` bigint DEFAULT NULL,
  `created_by` bigint DEFAULT NULL,
  `assigned_guide_id` bigint DEFAULT NULL,
  `status` bigint DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `schedule_detail` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `service_detail` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `diary` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `lists_file` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `tour_id`, `created_by`, `assigned_guide_id`, `status`, `start_date`, `end_date`, `schedule_detail`, `service_detail`, `diary`, `lists_file`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 3, 2, '2024-07-01', '2024-07-03', '{\"guide\":\"2\"}', '{\"customer\":{\"name\":\"Thịt heo\",\"phone\":\"0243543534\",\"email\":\"guide2@gmail.com\",\"address\":\"Ha Noi\"},\"customer_id\":1,\"adult\":1,\"child\":0,\"total_guests\":1,\"adult_price\":1500000,\"child_price\":800000,\"total_amount\":1500000}', '{\"entries\":[\"Ngày 1: OK\"]}', '{\"guest_list\":\"guest1.xlsx\"}', 'Booking đoàn A', '2025-11-21 10:34:56', '2025-12-03 18:36:52'),
(5, 2, 1, 2, 3, '2025-10-10', '2025-10-13', NULL, '{\"customer\":{\"name\":\"Thịt heo\",\"phone\":\"0243543534\",\"email\":\"guide2@gmail.com\",\"address\":\"Ha Noi\"},\"customer_id\":1,\"booking_type\":\"group\",\"adult\":6,\"child\":1,\"total_guests\":7,\"adult_price\":8000000,\"child_price\":6000000,\"total_amount\":54000000}', NULL, NULL, '12', '2025-12-03 17:41:13', '2025-12-03 18:36:24'),
(6, 1, 1, 2, 2, '2025-12-11', '2025-12-13', NULL, '{\"customer\":{\"name\":\"Thịt heo\",\"phone\":\"0243543534\",\"email\":\"guide2@gmail.com\",\"address\":\"Ha Noi\"},\"customer_id\":1,\"booking_type\":\"group\",\"adult\":7,\"child\":0,\"total_guests\":7,\"adult_price\":1500000,\"child_price\":800000,\"total_amount\":10500000}', NULL, NULL, '', '2025-12-03 17:49:06', '2025-12-03 17:49:51'),
(7, 2, 1, 2, 1, '2025-09-04', '2025-09-06', NULL, '{\"customer\":{\"name\":\"Thịt heo\",\"phone\":\"0243543534\",\"email\":\"guide2@gmail.com\",\"address\":\"Ha Noi\"},\"customer_id\":1,\"booking_type\":\"individual\",\"special_requirements\":\"bệnh lý\",\"adult\":2,\"child\":1,\"total_guests\":3,\"adult_price\":8000000,\"child_price\":6000000,\"total_amount\":22000000}', '{\"entries\":[{\"id\":1764784672,\"title\":\"alo\",\"content\":\"a\",\"cost\":11000,\"images\":[],\"created_at\":\"2025-12-03 17:57:52\"}]}', NULL, '2', '2025-12-03 17:50:32', '2025-12-03 18:36:14');

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
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `changed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking_status_logs`
--

INSERT INTO `booking_status_logs` (`id`, `booking_id`, `old_status`, `new_status`, `changed_by`, `note`, `changed_at`) VALUES
(1, 1, 1, 2, 1, 'Chuyển sang đã xác nhận', '2025-11-21 10:35:15'),
(3, 1, 1, 2, 1, '', '2025-12-03 15:09:02'),
(4, 1, 2, 1, 1, '', '2025-12-03 15:09:17'),
(12, 5, NULL, 1, 1, 'Tạo booking mới', '2025-12-03 17:41:13'),
(13, 5, 1, 2, 1, '', '2025-12-03 17:47:25'),
(14, 6, NULL, 1, 1, 'Tạo booking mới', '2025-12-03 17:49:06'),
(15, 6, 1, 2, 1, '', '2025-12-03 17:49:12'),
(16, 7, NULL, 1, 1, 'Tạo booking mới', '2025-12-03 17:50:32'),
(17, 7, 1, 2, 1, '', '2025-12-03 17:50:40'),
(18, 7, 2, 3, 1, '', '2025-12-03 17:51:00'),
(19, 7, 3, 1, 1, '', '2025-12-03 17:51:11'),
(20, 7, 1, 2, 1, '', '2025-12-03 17:54:13'),
(21, 1, 1, 2, 1, '', '2025-12-03 17:54:17'),
(22, 5, 2, 1, 1, '', '2025-12-03 17:55:17'),
(23, 5, 1, 2, 1, '', '2025-12-03 17:55:18'),
(26, 7, 2, 1, 1, '', '2025-12-03 18:36:14'),
(27, 5, 2, 3, 1, '', '2025-12-03 18:36:24'),
(28, 1, 2, 4, 1, '', '2025-12-03 18:36:30'),
(29, 1, 4, 2, 1, '', '2025-12-03 18:36:52');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `status` tinyint NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Tour trong nước', 'Tour du lịch nội địa', 1, '2025-11-21 10:33:01', '2025-11-21 10:33:01'),
(2, 'Tour quốc tế', 'Tour du lịch quốc tế', 1, '2025-11-21 10:33:01', '2025-11-21 10:33:01');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `company` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tax_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `phone`, `email`, `address`, `company`, `tax_code`, `notes`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Thịt heo', '0243543534', 'guide2@gmail.com', 'Ha Noi', 'Chc', 'uaw', 'ư', 1, '2025-12-03 17:04:06', '2025-12-03 17:04:06');

-- --------------------------------------------------------

--
-- Table structure for table `guide_profiles`
--

CREATE TABLE `guide_profiles` (
  `id` bigint NOT NULL,
  `user_id` bigint DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `certificate` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `languages` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `experience` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `history` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `rating` decimal(3,2) DEFAULT NULL,
  `health_status` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `group_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `speciality` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `guide_profiles`
--

INSERT INTO `guide_profiles` (`id`, `user_id`, `birthdate`, `avatar`, `phone`, `certificate`, `languages`, `experience`, `history`, `rating`, `health_status`, `group_type`, `speciality`, `created_at`, `updated_at`) VALUES
(1, 2, '1990-01-01', 'guide_69306a3dd6dae5.69104325.jpg', '0912345678', '[\"HDV quốc tế\"]', '[\"Tiếng Anh\",\"Tiếng Việt\"]', '5 năm kinh nghiệm', '{\"tours\":[1]}', 4.80, 'Tốt', 'quốc tế', 'chuyên tuyến miền Bắc', '2025-11-21 10:35:29', '2025-12-03 16:50:05'),
(2, 1, '1985-02-02', 'guide_69305561736329.46517827.jpg', '0987654321', '[\"HDV nội địa\"]', '[\"Tiếng Việt\"]', '10 năm kinh nghiệm', '{\"tours\":[2]}', 4.90, 'Khá', 'nội địa', 'chuyên khách đoàn', '2025-11-21 10:35:29', '2025-12-03 15:21:05');

-- --------------------------------------------------------

--
-- Table structure for table `tours`
--

CREATE TABLE `tours` (
  `id` bigint NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `category_id` bigint DEFAULT NULL,
  `schedule` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `prices` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `policies` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `suppliers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `price` decimal(15,2) DEFAULT NULL,
  `status` tinyint NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `duration` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Thời lượng tour (vd: 3 ngày 2 đêm)',
  `max_guests` int DEFAULT NULL COMMENT 'Số lượng khách tối đa'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tours`
--

INSERT INTO `tours` (`id`, `name`, `description`, `category_id`, `schedule`, `images`, `prices`, `policies`, `suppliers`, `price`, `status`, `created_at`, `updated_at`, `duration`, `max_guests`) VALUES
(1, 'Tour Hạ Long', 'Khám phá vịnh Hạ Long', 1, '{\"text\":\"{\\\"days\\\":[{\\\"date\\\":\\\"2024-07-01\\\",\\\"activities\\\":[\\\"Thăm vịnh Hạ Long\\\",\\\"Ăn trưa trên tàu\\\"]}]}\"}', '[\"halong1.jpg\",\"halong2.jpg\",\"tour_69307f2652df87.70021953.jpg\",\"tour_69307f2f56e547.29454666.jpg\"]', '{\"adult\":1500000,\"child\":800000}', '{\"text\":\"{\\\"booking\\\":\\\"Không hoàn tiền khi hủy trong vòng 48h\\\"}\"}', '{\"text\":\"[\\\"Vinpearl Hotel\\\",\\\"Xe Hùng Mạnh\\\"]\"}', 1500000.00, 1, '2025-11-21 10:33:16', '2025-12-03 18:19:27', '', NULL),
(2, 'Tour Thái Lan', 'Du lịch Bangkok - Pattaya', 2, '{\"days\":[{\"date\":\"2024-08-10\",\"activities\":[\"Tham quan chùa vàng\",\"Ăn buffet\"]}]}', '[\"thailand1.jpg\",\"thailand2.jpg\"]', '{\"adult\":8000000,\"child\":6000000}', '{\"booking\":\"Hoàn 50% trước 7 ngày\"}', '[\"Bangkok Hotel\",\"Bus Express\"]', 8000000.00, 1, '2025-11-21 10:33:16', '2025-11-21 10:33:16', NULL, NULL),
(4, 'Tour Đà Nẵng - Hội An - Bà Nà Hills', 'Khám phá con đường di sản miền Trung, chiêm ngưỡng Cầu Vàng hùng vĩ và trải nghiệm không gian cổ kính tại Hội An.', 1, '{\"text\":\"{\\\"days\\\":[{\\\"date\\\":\\\"2024-06-15\\\",\\\"activities\\\":[\\\"Đón sân bay Đà Nẵng\\\",\\\"Tham quan Ngũ Hành Sơn\\\"]},{\\\"date\\\":\\\"2024-06-16\\\",\\\"activities\\\":[\\\"Đi cáp treo Bà Nà Hills\\\",\\\"Dạo phố cổ Hội An\\\"]}]}\"}', '[\"tour_693080425798f6.60706721.jpg\"]', '{\"adult\":4500000,\"child\":3150000}', '{\"text\":\"{\\\"booking\\\": \\\"Hủy trước 5 ngày hoàn 100%, hủy sau đó phạt 50%\\\"}\"}', '{\"text\":\"[\\\"Vietnam Airlines\\\", \\\"Sun World Ba Na Hills\\\"]\"}', 4500000.00, 1, '2025-12-03 18:23:45', '2025-12-03 18:24:02', '3N2Đ', 25),
(5, 'Tour Nhật Bản: Cung Đường Vàng Tokyo - Osaka', 'Trải nghiệm văn hóa xứ sở Phù Tang, ngắm hoa anh đào và tham quan núi Phú Sĩ huyền thoại.', 2, '{\"text\":\"{\\\"days\\\":[{\\\"date\\\":\\\"2024-11-20\\\",\\\"activities\\\":[\\\"Bay đến sân bay Narita\\\",\\\"Nhận phòng khách sạn Shinjuku\\\"]},{\\\"date\\\":\\\"2024-11-21\\\",\\\"activities\\\":[\\\"Tham quan chùa Asakusa Kannon\\\",\\\"Ngắm tháp Tokyo Skytree\\\"]}]}\"}', '[\"tour_693080566f44a4.26714689.jpg\"]', '{\"adult\":28990000,\"child\":24500000}', '{\"text\":\"{\\\"booking\\\": \\\"Đặt cọc 50% khi đăng ký. Hoàn tất thanh toán trước 10 ngày khởi hành.\\\"}\"}', '{\"text\":\"[\\\"Japan Airlines\\\", \\\"Hilton Tokyo Hotel\\\"]\"}', 28990000.00, 1, '2025-12-03 18:23:45', '2025-12-03 18:24:22', '5N4Đ', 20);

-- --------------------------------------------------------

--
-- Table structure for table `tour_guests`
--

CREATE TABLE `tour_guests` (
  `id` bigint NOT NULL,
  `booking_id` bigint NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `dob` date DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `passport_number` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tour_guests`
--

INSERT INTO `tour_guests` (`id`, `booking_id`, `fullname`, `dob`, `gender`, `passport_number`) VALUES
(8, 6, 'user', '2025-12-05', 'Nữ', '1'),
(9, 6, 'user', '2025-12-05', 'Nữ', NULL),
(10, 6, 'user', '2025-12-11', NULL, '1'),
(11, 6, 'user', NULL, NULL, NULL),
(12, 6, 'user', NULL, NULL, NULL),
(13, 6, 'user', NULL, NULL, NULL),
(14, 6, 'user', NULL, NULL, NULL),
(15, 1, 'user', NULL, NULL, NULL),
(16, 5, 'user', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tour_statuses`
--

CREATE TABLE `tour_statuses` (
  `id` bigint NOT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tour_statuses`
--

INSERT INTO `tour_statuses` (`id`, `name`, `description`) VALUES
(1, 'Chờ xác nhận', 'Chưa xác nhận bởi admin/khách'),
(2, 'Đã cọc', 'Khách đã đặt cọc giữ chỗ'),
(3, 'Hoàn tất', 'Tour đã kết thúc thành công'),
(4, 'Hủy', 'Tour đã bị hủy');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `role` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` tinyint NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@gmail.com', '$2y$10$8myHjUmVq9SaXNRUI2e1eOD8IHZTIGHd3kIb1THhW7i.NAN0otgqa', 'admin', 1, '2025-11-21 10:32:39', '2025-12-03 14:34:25'),
(2, 'Guide', 'guide@gmail.com', '$2y$10$8myHjUmVq9SaXNRUI2e1eOD8IHZTIGHd3kIb1THhW7i.NAN0otgqa', 'guide', 1, '2025-11-21 10:32:39', '2025-12-03 14:34:39'),
(3, 'Guide2', 'guide2@gmail.com', '$2y$10$q8y3w3YOkxorUu0iaceTL.ntvtR5UX/dxxWcHvlFaINKF.75sME8i', 'guide', 1, '2025-12-03 16:53:02', '2025-12-03 16:53:02');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tour_id` (`tour_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `assigned_guide_id` (`assigned_guide_id`),
  ADD KEY `status` (`status`);

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
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `phone` (`phone`),
  ADD KEY `email` (`email`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `guide_profiles`
--
ALTER TABLE `guide_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `tours`
--
ALTER TABLE `tours`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

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
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `booking_status_logs`
--
ALTER TABLE `booking_status_logs`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `guide_profiles`
--
ALTER TABLE `guide_profiles`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tours`
--
ALTER TABLE `tours`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tour_guests`
--
ALTER TABLE `tour_guests`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `tour_statuses`
--
ALTER TABLE `tour_statuses`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bookings_ibfk_3` FOREIGN KEY (`assigned_guide_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bookings_ibfk_4` FOREIGN KEY (`status`) REFERENCES `tour_statuses` (`id`);

--
-- Constraints for table `booking_status_logs`
--
ALTER TABLE `booking_status_logs`
  ADD CONSTRAINT `booking_status_logs_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`),
  ADD CONSTRAINT `booking_status_logs_ibfk_2` FOREIGN KEY (`old_status`) REFERENCES `tour_statuses` (`id`),
  ADD CONSTRAINT `booking_status_logs_ibfk_3` FOREIGN KEY (`new_status`) REFERENCES `tour_statuses` (`id`),
  ADD CONSTRAINT `booking_status_logs_ibfk_4` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `guide_profiles`
--
ALTER TABLE `guide_profiles`
  ADD CONSTRAINT `guide_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `tours`
--
ALTER TABLE `tours`
  ADD CONSTRAINT `tours_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `tour_guests`
--
ALTER TABLE `tour_guests`
  ADD CONSTRAINT `tour_guests_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

