-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 10, 2025 at 08:45 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `room_utilization_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time GENERATED ALWAYS AS (addtime(`start_time`,sec_to_time(`duration` * 3600))) STORED,
  `duration` int(11) NOT NULL DEFAULT 1,
  `purpose` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `booking_modification_requests`
--

CREATE TABLE `booking_modification_requests` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `requester_id` int(11) NOT NULL,
  `new_date` date DEFAULT NULL,
  `new_start_time` time DEFAULT NULL,
  `new_end_time` time GENERATED ALWAYS AS (addtime(`new_start_time`,sec_to_time(`new_duration` * 3600))) STORED,
  `new_duration` int(11) NOT NULL DEFAULT 1,
  `new_room_id` int(11) DEFAULT NULL,
  `reason` text NOT NULL,
  `status` enum('pending','approved','denied') DEFAULT 'pending',
  `approved_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `room_name` varchar(50) NOT NULL,
  `building` enum('CICS','CIT') NOT NULL,
  `capacity` int(11) DEFAULT 0,
  `status` enum('available','maintenance','booked') DEFAULT 'available',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `room_name`, `building`, `capacity`, `status`, `created_at`, `updated_at`) VALUES
(22, '201', 'CICS', 30, 'available', '2025-12-10 14:43:55', '2025-12-10 14:43:55'),
(23, '202', 'CICS', 30, 'available', '2025-12-10 14:44:45', '2025-12-10 14:44:45'),
(24, '203', 'CICS', 30, 'available', '2025-12-10 14:45:14', '2025-12-10 14:45:14'),
(25, '204', 'CICS', 30, 'available', '2025-12-10 14:45:25', '2025-12-10 14:45:25'),
(26, '205', 'CICS', 30, 'available', '2025-12-10 14:45:34', '2025-12-10 14:45:34'),
(27, '301', 'CICS', 30, 'available', '2025-12-10 14:45:42', '2025-12-10 14:45:42'),
(28, '302', 'CICS', 30, 'available', '2025-12-10 15:03:04', '2025-12-10 15:03:04'),
(29, '303', 'CICS', 30, 'available', '2025-12-10 15:03:13', '2025-12-10 15:03:13'),
(30, '304', 'CICS', 30, 'available', '2025-12-10 15:03:21', '2025-12-10 15:03:21'),
(31, '305', 'CICS', 30, 'available', '2025-12-10 15:03:38', '2025-12-10 15:03:38'),
(32, '401', 'CICS', 30, 'available', '2025-12-10 15:09:19', '2025-12-10 15:09:19'),
(33, '402', 'CICS', 30, 'available', '2025-12-10 15:09:33', '2025-12-10 15:09:33'),
(34, '403', 'CICS', 30, 'available', '2025-12-10 15:09:43', '2025-12-10 15:09:43'),
(36, '405', 'CICS', 30, 'available', '2025-12-10 15:10:00', '2025-12-10 15:10:00'),
(37, '501', 'CICS', 30, 'available', '2025-12-10 15:10:13', '2025-12-10 15:10:13'),
(38, '502', 'CICS', 30, 'available', '2025-12-10 15:10:26', '2025-12-10 15:10:26'),
(39, '503', 'CICS', 30, 'available', '2025-12-10 15:10:37', '2025-12-10 15:10:37'),
(40, '504', 'CICS', 30, 'available', '2025-12-10 15:10:50', '2025-12-10 15:10:50'),
(41, '505', 'CICS', 30, 'available', '2025-12-10 15:10:57', '2025-12-10 15:10:57'),
(42, '101', 'CIT', 30, 'available', '2025-12-10 15:11:30', '2025-12-10 15:11:30'),
(45, '103', 'CIT', 30, 'available', '2025-12-10 15:12:08', '2025-12-10 15:12:08'),
(46, '104', 'CIT', 30, 'available', '2025-12-10 15:12:41', '2025-12-10 15:12:41'),
(47, '105', 'CIT', 30, 'available', '2025-12-10 15:12:53', '2025-12-10 15:12:53'),
(48, '203', 'CIT', 30, 'available', '2025-12-10 15:13:28', '2025-12-10 15:13:28'),
(49, '102', 'CIT', 30, 'available', '2025-12-10 15:14:30', '2025-12-10 15:14:30'),
(50, '204', 'CIT', 30, 'available', '2025-12-10 15:14:46', '2025-12-10 15:14:46'),
(51, '205', 'CIT', 30, 'available', '2025-12-10 15:15:00', '2025-12-10 15:15:00'),
(52, '301', 'CIT', 30, 'available', '2025-12-10 15:15:15', '2025-12-10 15:15:15'),
(53, '303', 'CIT', 30, 'available', '2025-12-10 15:15:24', '2025-12-10 15:15:24'),
(54, '302', 'CIT', 30, 'available', '2025-12-10 15:16:53', '2025-12-10 15:16:53'),
(55, '304', 'CIT', 30, 'available', '2025-12-10 15:17:28', '2025-12-10 15:17:28'),
(56, '305', 'CIT', 30, 'available', '2025-12-10 15:17:37', '2025-12-10 15:17:37'),
(57, '401', 'CIT', 30, 'available', '2025-12-10 15:17:52', '2025-12-10 15:17:52'),
(58, '402', 'CIT', 30, 'available', '2025-12-10 15:18:06', '2025-12-10 15:18:06'),
(59, '403', 'CIT', 30, 'available', '2025-12-10 15:18:16', '2025-12-10 15:18:16'),
(60, '404', 'CIT', 30, 'available', '2025-12-10 15:18:42', '2025-12-10 15:18:42'),
(61, '405', 'CIT', 30, 'available', '2025-12-10 15:18:51', '2025-12-10 15:18:51'),
(62, '503', 'CIT', 30, 'available', '2025-12-10 15:19:24', '2025-12-10 15:19:24');

-- --------------------------------------------------------

--
-- Table structure for table `student_booking_requests`
--

CREATE TABLE `student_booking_requests` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time GENERATED ALWAYS AS (addtime(`start_time`,sec_to_time(`duration` * 3600))) STORED,
  `duration` int(11) NOT NULL DEFAULT 1,
  `purpose` text DEFAULT NULL,
  `status` enum('pending','approved','denied','cancelled') NOT NULL DEFAULT 'pending',
  `faculty_id` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','faculty','student','super') NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `role`, `status`, `created_at`, `updated_at`) VALUES
(1, 'chiharu', 'quijanoemman99@gmail.com', '$2y$12$MzXDBPqSnJpTFZp8mu0EFeiGgrnuew9Cu4jKejifm3wGhe5o6.9Hm', 'admin', 'active', '2025-11-30 06:54:09', '2025-12-10 15:36:31'),
(2, 'tungtung sahur', 'emmanuelequijanoboss02@gmail.com', '$2y$12$F1DyeRppJ6B12y5JB.iKbep2zwLPK7NMRE4YvlwQP.7q9Ewng.4XW', 'faculty', 'inactive', '2025-12-01 08:37:50', '2025-12-09 10:25:18'),
(3, 'Princess Shrek', 'blairrhoades9@gmail.com', '$2y$12$F1DyeRppJ6B12y5JB.iKbep2zwLPK7NMRE4YvlwQP.7q9Ewng.4XW', 'student', 'inactive', '2025-12-01 08:44:00', '2025-12-10 13:34:53'),
(4, 'Karl Pitarde', 'karlkarlkenken@gmail.com', 'chi123', 'faculty', 'active', '2025-12-08 14:40:07', '2025-12-09 17:48:12'),
(5, 'Super Karl', 'haha@gmail.com', '$2y$12$F1DyeRppJ6B12y5JB.iKbep2zwLPK7NMRE4YvlwQP.7q9Ewng.4XW', 'super', 'active', '2025-12-01 08:44:00', '2025-12-01 08:44:00'),
(6, 'emmantot', 'hahahahahaha@gmail.com', '$2y$10$aFrjmO6RQrFz3AbpR9KvlOdozLHKoS8nhKBFGMrx5i1DtvvaLP5te', 'student', 'active', '2025-12-10 13:32:57', '2025-12-10 15:38:47'),
(7, 'jordi el Sins', 'jordielsins@gmail.com', '$2y$10$Bu/Y1VHVlROVSZ8r1s6k8.4TlzjHhBoIpxrJSTsrnGoZpbowO.HQy', 'faculty', 'inactive', '2025-12-10 13:45:48', '2025-12-10 14:16:02'),
(8, 'Princess Tagpeo', 'tagpetagpe@gmail.com', '$2y$10$eVC40hVopELUjMZNW1fu7uTl6fbBWNVHzUAv.QI1c7pPfLKorrfba', 'student', 'active', '2025-12-10 13:52:16', '2025-12-10 13:52:16'),
(9, 'gawk gawk', 'juantot@gmail.com', '$2y$10$uJxGh8MX/wcVvjRB93fGYO5PDN9VOVNvfX3hVtCWSffTssXuytirq', 'student', 'active', '2025-12-10 15:10:43', '2025-12-10 15:10:43');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `booking_modification_requests`
--
ALTER TABLE `booking_modification_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `requester_id` (`requester_id`),
  ADD KEY `new_room_id` (`new_room_id`),
  ADD KEY `approved_by` (`approved_by`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_booking_requests`
--
ALTER TABLE `student_booking_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `student_booking_requests_ibfk_3` (`faculty_id`);

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
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `booking_modification_requests`
--
ALTER TABLE `booking_modification_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `student_booking_requests`
--
ALTER TABLE `student_booking_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `booking_modification_requests`
--
ALTER TABLE `booking_modification_requests`
  ADD CONSTRAINT `booking_modification_requests_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_modification_requests_ibfk_2` FOREIGN KEY (`requester_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_modification_requests_ibfk_3` FOREIGN KEY (`new_room_id`) REFERENCES `rooms` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `booking_modification_requests_ibfk_4` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `student_booking_requests`
--
ALTER TABLE `student_booking_requests`
  ADD CONSTRAINT `student_booking_requests_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_booking_requests_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_booking_requests_ibfk_3` FOREIGN KEY (`faculty_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
