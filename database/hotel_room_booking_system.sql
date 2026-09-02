-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 18, 2026 at 07:39 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


-- Table structure for table `billing`

CREATE TABLE `billing` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `guest_id` int(11) NOT NULL,
  `base_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `extras_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_method` enum('cash','card','bkash','nagad','bank_transfer') DEFAULT NULL,
  `payment_status` enum('pending','paid') NOT NULL DEFAULT 'pending',
  `paid_at` datetime DEFAULT NULL,
  `receipt_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `billing`
--

INSERT INTO `billing` (`id`, `booking_id`, `guest_id`, `base_amount`, `extras_amount`, `discount_amount`, `total_amount`, `payment_method`, `payment_status`, `paid_at`, `receipt_path`, `created_at`) VALUES
(1, 1, 2, 5000.00, 0.00, 0.00, 5000.00, 'nagad', 'paid', '2026-05-15 17:47:06', NULL, '2026-05-14 18:52:30'),
(2, 2, 2, 9000.00, 500.00, 0.00, 9500.00, 'bkash', 'paid', '2026-05-15 00:52:30', NULL, '2026-05-14 18:52:30'),
(5, 5, 8, 8000.00, 0.00, 0.00, 8000.00, 'bkash', 'paid', '2026-05-16 02:03:27', 'receipts/receipt_5.php', '2026-05-15 14:30:41'),
(6, 6, 9, 64000.00, 0.00, 0.00, 64000.00, 'bank_transfer', 'paid', '2026-05-16 02:05:41', 'receipts/receipt_6.php', '2026-05-15 20:05:13'),
(7, 7, 10, 4500.00, 0.00, 0.00, 4500.00, 'bank_transfer', 'paid', '2026-05-16 02:09:15', NULL, '2026-05-15 20:07:33'),
(8, 8, 11, 16000.00, 0.00, 0.00, 16000.00, 'bank_transfer', 'paid', '2026-05-16 02:10:24', NULL, '2026-05-15 20:10:01');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `guest_id` int(11) NOT NULL,
  `room_id` int(11) DEFAULT NULL,
  `room_type_id` int(11) NOT NULL,
  `checkin_date` date NOT NULL,
  `checkout_date` date NOT NULL,
  `num_guests` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','confirmed','checked_in','checked_out','cancelled') NOT NULL DEFAULT 'pending',
  `source` enum('online','walk_in') NOT NULL DEFAULT 'online',
  `special_request` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `guest_id`, `room_id`, `room_type_id`, `checkin_date`, `checkout_date`, `num_guests`, `total_price`, `status`, `source`, `special_request`, `created_at`) VALUES
(1, 2, 5, 1, '2026-05-11', '2026-05-15', 2, 5000.00, 'checked_out', 'online', 'Need quiet room', '2026-05-14 18:52:30'),
(2, 2, 6, 2, '2026-05-14', '2026-05-16', 3, 9000.00, 'checked_in', 'online', 'Extra pillow needed', '2026-05-14 18:52:30'),
(5, 8, 9, 3, '2026-05-15', '2026-05-16', 1, 8000.00, 'checked_in', 'walk_in', ' | Late checkout approved | Early check-in approved | Late checkout approved', '2026-05-15 14:30:41'),
(6, 9, NULL, 3, '2026-05-23', '2026-05-31', 1, 64000.00, 'checked_out', 'walk_in', '', '2026-05-15 20:05:13'),
(7, 10, 5, 2, '2026-05-15', '2026-05-16', 1, 4500.00, 'checked_in', 'walk_in', ' | Late checkout approved', '2026-05-15 20:07:33'),
(8, 11, NULL, 3, '2026-05-13', '2026-05-15', 3, 16000.00, 'checked_out', 'walk_in', '', '2026-05-15 20:10:01');

-- --------------------------------------------------------

--
-- Table structure for table `booking_modification_requests`
--

CREATE TABLE `booking_modification_requests` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `guest_id` int(11) NOT NULL,
  `requested_checkin_date` date NOT NULL,
  `requested_checkout_date` date NOT NULL,
  `reason` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `processed_by` int(11) DEFAULT NULL,
  `processed_at` datetime DEFAULT NULL,
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `booking_modification_requests`
--

INSERT INTO `booking_modification_requests` (`id`, `booking_id`, `guest_id`, `requested_checkin_date`, `requested_checkout_date`, `reason`, `status`, `processed_by`, `processed_at`, `requested_at`) VALUES
(1, 1, 2, '2026-05-21', '2026-05-23', 'Guest requested one day delay due to travel schedule.', 'pending', NULL, NULL, '2026-05-14 18:52:31');

-- --------------------------------------------------------

--
-- Table structure for table `housekeeping_tasks`
--

CREATE TABLE `housekeeping_tasks` (
  `id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `assigned_to` int(11) NOT NULL,
  `task_type` enum('cleaning','inspection','maintenance') NOT NULL,
  `priority` enum('normal','urgent') NOT NULL DEFAULT 'normal',
  `status` enum('pending','in_progress','done') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `scheduled_date` date NOT NULL,
  `completed_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `housekeeping_tasks`
--

INSERT INTO `housekeeping_tasks` (`id`, `room_id`, `assigned_to`, `task_type`, `priority`, `status`, `notes`, `scheduled_date`, `completed_at`, `created_at`) VALUES
(1, 3, 4, 'cleaning', 'urgent', 'pending', 'Room 103 needs cleaning after checkout.', '2026-05-15', NULL, '2026-05-14 18:52:31'),
(2, 7, 4, 'maintenance', 'urgent', 'in_progress', 'Check bathroom plumbing issue.', '2026-05-15', NULL, '2026-05-14 18:52:31'),
(3, 10, 4, 'inspection', 'normal', 'pending', 'AC inspection required.', '2026-05-15', NULL, '2026-05-14 18:52:31');

-- --------------------------------------------------------
-- Table structure for table `maintenance_reports`
--

CREATE TABLE `maintenance_reports` (
  `id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `reported_by` int(11) NOT NULL,
  `description` text NOT NULL,
  `severity` enum('low','medium','high') NOT NULL DEFAULT 'low',
  `status` enum('open','in_progress','resolved') NOT NULL DEFAULT 'open',
  `reported_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `resolved_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `maintenance_reports`
--

INSERT INTO `maintenance_reports` (`id`, `room_id`, `reported_by`, `description`, `severity`, `status`, `reported_at`, `resolved_at`) VALUES
(1, 7, 4, 'Bathroom plumbing issue reported by housekeeping.', 'high', 'in_progress', '2026-05-14 18:52:31', NULL),
(2, 10, 4, 'AC cooling performance is low.', 'medium', 'open', '2026-05-14 18:52:31', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `room_type_id` int(11) NOT NULL,
  `room_number` varchar(30) NOT NULL,
  `floor` int(11) NOT NULL,
  `status` enum('available','occupied','dirty','maintenance','blocked') NOT NULL DEFAULT 'available',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `room_type_id`, `room_number`, `floor`, `status`, `notes`, `created_at`) VALUES
(1, 1, '101', 1, 'available', 'Standard room near lobby', '2026-05-14 18:52:30'),
(2, 1, '102', 1, 'available', 'Standard room with city view', '2026-05-14 18:52:30'),
(3, 1, '103', 1, 'dirty', 'Recently checked out, needs cleaning', '2026-05-14 18:52:30'),
(4, 1, '104', 1, 'blocked', 'Temporarily blocked for internal use', '2026-05-14 18:52:30'),
(5, 2, '201', 2, 'occupied', 'Deluxe room with balcony', '2026-05-14 18:52:30'),
(6, 2, '202', 2, 'occupied', 'Currently occupied by guest', '2026-05-14 18:52:30'),
(7, 2, '203', 2, 'maintenance', 'Bathroom plumbing issue', '2026-05-14 18:52:30'),
(8, 2, '204', 2, 'available', 'Deluxe corner room', '2026-05-14 18:52:30'),
(9, 3, '301', 3, 'occupied', 'Executive suite with premium view', '2026-05-14 18:52:30'),
(10, 3, '302', 3, 'maintenance', 'AC inspection required', '2026-05-14 18:52:30'),
(11, 3, '303', 3, 'available', 'Executive suite with city view', '2026-05-14 18:52:30');

-- --------------------------------------------------------

--
-- Table structure for table `room_types`
--

CREATE TABLE `room_types` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `price_per_night` decimal(10,2) NOT NULL,
  `max_capacity` int(11) NOT NULL,
  `thumbnail_path` varchar(255) DEFAULT NULL,
  `amenities` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`amenities`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `room_types`
--

INSERT INTO `room_types` (`id`, `name`, `description`, `price_per_night`, `max_capacity`, `thumbnail_path`, `amenities`, `is_active`, `created_at`) VALUES
(1, 'Standard Room', 'A clean and comfortable budget-friendly room suitable for solo travelers and couples. Includes essential hotel facilities for a pleasant stay.', 2500.00, 2, 'standard-room.jpg', '[\"Free WiFi\", \"Air Conditioning\", \"LED TV\", \"Attached Bathroom\", \"Room Service\"]', 1, '2026-05-14 18:52:30'),
(2, 'Deluxe Room', 'A spacious room with enhanced comfort, better interior design, and additional facilities for families and business travelers.', 4500.00, 3, 'deluxe-room.jpg', '[\"Free WiFi\", \"Air Conditioning\", \"Smart TV\", \"Mini Fridge\", \"Tea Table\", \"Balcony View\"]', 1, '2026-05-14 18:52:30'),
(3, 'Executive Suite', 'A premium luxury suite designed for executive guests, families, and VIP customers with a large space and premium amenities.', 8000.00, 4, 'executive-suite.jpg', '[\"Free WiFi\", \"Air Conditioning\", \"Smart TV\", \"Mini Bar\", \"Bathtub\", \"Work Desk\", \"Premium View\"]', 1, '2026-05-14 18:52:30');

-- --------------------------------------------------------

--
-- Table structure for table `seasonal_pricing`
--

CREATE TABLE `seasonal_pricing` (
  `id` int(11) NOT NULL,
  `room_type_id` int(11) NOT NULL,
  `label` varchar(120) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `price_per_night` decimal(10,2) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `seasonal_pricing`
--

INSERT INTO `seasonal_pricing` (`id`, `room_type_id`, `label`, `start_date`, `end_date`, `price_per_night`, `is_active`) VALUES
(1, 1, 'Eid Holiday Season', '2026-06-01', '2026-06-10', 3000.00, 1),
(2, 2, 'Eid Holiday Season', '2026-06-01', '2026-06-10', 5200.00, 1),
(3, 3, 'Eid Premium Season', '2026-06-01', '2026-06-10', 9500.00, 1),
(4, 1, 'Winter Offer', '2026-12-01', '2026-12-31', 2300.00, 1),
(5, 2, 'Winter Offer', '2026-12-01', '2026-12-31', 4200.00, 1);

-- --------------------------------------------------------


--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `nationality` varchar(80) DEFAULT 'Bangladeshi',
  `id_number` varchar(80) DEFAULT NULL,
  `role` enum('guest','receptionist','housekeeping','admin') NOT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `phone`, `nationality`, `id_number`, `role`, `profile_pic`, `is_active`, `last_login_at`, `created_at`) VALUES
(1, 'Famid Rabbi', 'famid.rabbi@grandpalacehotel.com', '$2b$10$nY5/YJ7VOtSZ4V9TrqmCeOZx9MA/j8snTq7hBADCIqYMnT0wL4Wt2', '01710000001', 'Bangladeshi', 'ADM-001', 'admin', NULL, 1, NULL, '2026-05-14 18:52:30'),
(2, 'Tasmin Tashu', 'tasmin.tashu@grandpalacehotel.com', '$2b$10$nY5/YJ7VOtSZ4V9TrqmCeOZx9MA/j8snTq7hBADCIqYMnT0wL4Wt2', '01710000002', 'Bangladeshi', 'GST-001', 'guest', NULL, 1, NULL, '2026-05-14 18:52:30'),
(3, 'Rayhan Rabby', 'rayhan.rabby@grandpalacehotel.com', '$2b$10$nY5/YJ7VOtSZ4V9TrqmCeOZx9MA/j8snTq7hBADCIqYMnT0wL4Wt2', '01710000003', 'Bangladeshi', 'REC-001', 'receptionist', NULL, 1, NULL, '2026-05-14 18:52:30'),
(4, 'antu roy', 'antu.roy@grandpalacehotel.com', '$2b$10$nY5/YJ7VOtSZ4V9TrqmCeOZx9MA/j8snTq7hBADCIqYMnT0wL4Wt2', '01716667657', 'Bangladeshi', 'HK-001', 'housekeeping', NULL, 1, NULL, '2026-05-14 18:52:30'),
(8, 'Lamisa', 'lamisa1778855440@walkin.local', '$2y$10$CU8.A55WFQD1r5qD1a1vzOBYQNDOVspsJ4OCZdQFkzd/zt3z/yYYu', '01904322517', 'Bangladesh', '2323344321', 'guest', NULL, 1, NULL, '2026-05-15 14:30:41'),
(9, 'Lamisa N', 'lamisan1778875513@walkin.local', '$2y$10$2HfONk08fcqy3xA1AyM.VeoDqeMO8o7d.mnJqEiasp1I4cNqDaJuW', '01904322517', 'Bangladesh', '2323344321', 'guest', NULL, 1, NULL, '2026-05-15 20:05:13'),
(10, 'hasam', 'hasam1778875653@walkin.local', '$2y$10$eXhojY/ybW63t8mbbwSpK.lmJgi/X.5UVlLlxzIokXZXunqwo/AxK', '01904322517', 'Bangladesh', '2323344321', 'guest', NULL, 1, NULL, '2026-05-15 20:07:33'),
(11, 'Lamisa', 'lamisa1778875801@walkin.local', '$2y$10$Xhb9mXPSvL1lSWTbyQeZW.50stao3GsmXShmUhcf9dJYP65GytAUW', '01717772920', 'Bangladesh', '2323344321', 'guest', NULL, 1, NULL, '2026-05-15 20:10:01');

-- Indexes for table `billing`
--
ALTER TABLE `billing`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `guest_id` (`guest_id`),
  ADD KEY `idx_billing_status` (`payment_status`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `room_type_id` (`room_type_id`),
  ADD KEY `idx_bookings_guest` (`guest_id`),
  ADD KEY `idx_bookings_status` (`status`),
  ADD KEY `idx_bookings_dates` (`checkin_date`,`checkout_date`);

--
-- Indexes for table `booking_modification_requests`
--
ALTER TABLE `booking_modification_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `guest_id` (`guest_id`),
  ADD KEY `processed_by` (`processed_by`);

--
-- Indexes for table `housekeeping_tasks`
--
ALTER TABLE `housekeeping_tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `assigned_to` (`assigned_to`),
  ADD KEY `idx_housekeeping_status` (`status`);

--
-- Indexes for table `maintenance_reports`
--
ALTER TABLE `maintenance_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `reported_by` (`reported_by`),
  ADD KEY `idx_maintenance_status` (`status`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `room_number` (`room_number`),
  ADD KEY `idx_rooms_status` (`status`),
  ADD KEY `idx_rooms_type` (`room_type_id`);

--
-- Indexes for table `room_types`
--
ALTER TABLE `room_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `seasonal_pricing`
--
ALTER TABLE `seasonal_pricing`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_type_id` (`room_type_id`);


--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_users_role` (`role`);

-- AUTO_INCREMENT for table `billing`
--
ALTER TABLE `billing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `booking_modification_requests`
--
ALTER TABLE `booking_modification_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `housekeeping_tasks`
--
ALTER TABLE `housekeeping_tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
--
-- AUTO_INCREMENT for table `maintenance_reports`
--
ALTER TABLE `maintenance_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `room_types`
--
ALTER TABLE `room_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `seasonal_pricing`
--
ALTER TABLE `seasonal_pricing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;


-- Constraints for table `billing`
--
ALTER TABLE `billing`
  ADD CONSTRAINT `billing_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `billing_ibfk_2` FOREIGN KEY (`guest_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`guest_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_3` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `booking_modification_requests`
--
ALTER TABLE `booking_modification_requests`
  ADD CONSTRAINT `booking_modification_requests_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `booking_modification_requests_ibfk_2` FOREIGN KEY (`guest_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `booking_modification_requests_ibfk_3` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `housekeeping_tasks`
--
ALTER TABLE `housekeeping_tasks`
  ADD CONSTRAINT `housekeeping_tasks_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `housekeeping_tasks_ibfk_2` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON UPDATE CASCADE;


--
-- Constraints for table `maintenance_reports`
--
ALTER TABLE `maintenance_reports`
  ADD CONSTRAINT `maintenance_reports_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `maintenance_reports_ibfk_2` FOREIGN KEY (`reported_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_ibfk_1` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `seasonal_pricing`
--
ALTER TABLE `seasonal_pricing`
  ADD CONSTRAINT `seasonal_pricing_ibfk_1` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;



/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
