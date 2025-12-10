-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 10, 2025 at 12:21 PM
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
  `updated_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `room_id`, `user_id`, `date`, `start_time`, `duration`, `purpose`, `created_at`, `updated_at`) VALUES
(1, 9, 4, '2025-12-04', '10:00:00', 5, 'tangina', '2025-12-03 10:10:21', '2025-12-07 08:08:36'),
(4, 14, 2, '2025-12-12', '10:00:00', 5, 'hhahahahahaha', '2025-12-05 19:32:09', '2025-12-07 08:08:43'),
(5, 15, 2, '2025-12-09', '10:28:00', 1, 'afwefewf', '2025-12-09 19:28:56', '2025-12-09 19:28:56'),
(7, 24, 2, '2025-12-10', '07:00:00', 5, NULL, '2025-12-10 15:57:25', '2025-12-10 15:57:25'),
(8, 9, 3, '2025-12-10', '07:00:00', 12, 'vfdvfdvd', '2025-12-10 17:14:44', '2025-12-10 17:14:44'),
(9, 42, 3, '2025-12-10', '08:00:00', 5, 'ertyjmbvfrtyuj', '2025-12-10 18:10:26', '2025-12-10 18:10:26'),
(10, 45, 3, '2025-12-10', '09:00:00', 5, 'qwikmnbvcs', '2025-12-10 18:56:53', '2025-12-10 18:56:53'),
(11, 42, 3, '2025-12-10', '07:00:00', 1, 'qwertyuiop', '2025-12-10 18:57:07', '2025-12-10 18:57:07'),
(12, 42, 3, '2025-12-10', '09:00:00', 1, '345678765eyuyfd', '2025-12-10 18:58:10', '2025-12-10 18:58:10');

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
  `updated_at` datetime DEFAULT current_timestamp()
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
  `updated_at` datetime DEFAULT current_timestamp()
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
(62, '503', 'CIT', 30, 'available', '2025-12-10 15:19:24', '2025-12-10 15:19:24'),
(63, 'TestRoom_7645', 'CICS', 50, 'available', '2025-12-10 15:55:07', '2025-12-10 15:55:07');

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
  `updated_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_booking_requests`
--

INSERT INTO `student_booking_requests` (`id`, `student_id`, `room_id`, `date`, `start_time`, `duration`, `purpose`, `status`, `faculty_id`, `notes`, `created_at`, `updated_at`) VALUES
(1, 3, 14, '2025-12-13', '15:38:00', 4, 'gfrefdsgrgfse', 'cancelled', 2, NULL, '2025-12-08 15:38:12', '2025-12-08 15:38:12'),
(2, 3, 14, '2025-12-11', '16:39:00', 2, 'asfasdfdas', 'pending', 2, NULL, '2025-12-08 15:39:15', '2025-12-08 15:39:15'),
(3, 3, 15, '2025-12-11', '17:17:00', 3, 'dscdsfdsgsfgdfsa', 'pending', 4, NULL, '2025-12-08 16:17:20', '2025-12-08 16:17:20'),
(4, 3, 9, '2025-12-11', '17:17:00', 3, 'dscdsfdsgsfgdfsa', 'cancelled', 4, NULL, '2025-12-08 16:19:24', '2025-12-08 16:19:24'),
(5, 3, 9, '2025-12-11', '08:17:00', 12, 'dscdsfdsgsfgdfsa', 'pending', 4, NULL, '2025-12-08 16:21:07', '2025-12-08 16:21:07'),
(6, 3, 15, '2025-12-17', '07:21:00', 9, 'asdfsadfsadfsda', 'cancelled', 2, NULL, '2025-12-08 16:21:32', '2025-12-08 16:21:32'),
(7, 3, 14, '2025-12-19', '22:30:00', 1, 'Matutulog lang po hihi', 'cancelled', 2, NULL, '2025-12-09 18:21:52', '2025-12-09 18:21:52'),
(8, 3, 9, '2025-12-10', '07:00:00', 12, 'vfdvfdvd', 'approved', 2, '', '2025-12-10 12:16:59', '2025-12-10 12:16:59'),
(9, 3, 9, '2025-12-10', '07:00:00', 12, 'dsdfgyhh', 'denied', 2, 'asdfg', '2025-12-10 13:48:56', '2025-12-10 13:48:56'),
(10, 3, 42, '2025-12-10', '07:00:00', 12, 'juytrew', 'denied', 2, '', '2025-12-10 15:47:45', '2025-12-10 15:47:45'),
(11, 3, 63, '2025-12-10', '10:00:00', 1, 'Testing Fix', 'denied', 0, 'iuytrew', '2025-12-10 15:55:07', '2025-12-10 15:55:07'),
(13, 3, 42, '2025-12-10', '07:00:00', 1, 'qwertyuiop', 'approved', 2, '', '2025-12-10 17:25:46', '2025-12-10 17:25:46'),
(14, 3, 42, '2025-12-10', '09:00:00', 1, '345678765eyuyfd', 'approved', 2, '', '2025-12-10 17:28:45', '2025-12-10 17:28:45'),
(15, 3, 51, '2026-01-07', '07:00:00', 5, 'qwertyuilmnbvcxz', 'pending', 2, NULL, '2025-12-10 17:52:11', '2025-12-10 17:52:11'),
(16, 3, 42, '2025-12-10', '08:00:00', 5, 'ertyjmbvfrtyuj', 'approved', 2, '', '2025-12-10 17:56:36', '2025-12-10 17:56:36'),
(17, 3, 45, '2025-12-10', '09:00:00', 5, 'qwikmnbvcs', 'approved', 2, '', '2025-12-10 18:12:19', '2025-12-10 18:12:19'),
(18, 3, 49, '2025-12-10', '07:00:00', 1, 'sresthgrafe', 'pending', 5, NULL, '2025-12-10 18:54:03', '2025-12-10 18:54:03');

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
  `updated_at` datetime DEFAULT current_timestamp(),
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `role`, `status`, `created_at`, `updated_at`, `reset_token`, `reset_expires`) VALUES
(1, 'chiharu', 'admin@venb.top', '$2y$12$MzXDBPqSnJpTFZp8mu0EFeiGgrnuew9Cu4jKejifm3wGhe5o6.9Hm', 'admin', 'active', '2025-11-30 06:54:09', '2025-11-30 11:16:22', NULL, NULL),
(2, 'tungtung sahur', 'faculty@venb.top', '$2y$12$F1DyeRppJ6B12y5JB.iKbep2zwLPK7NMRE4YvlwQP.7q9Ewng.4XW', 'faculty', 'active', '2025-12-01 08:37:50', '2025-12-01 08:37:50', NULL, NULL),
(3, 'Princess Shrek', 'student@venb.top', '$2y$12$F1DyeRppJ6B12y5JB.iKbep2zwLPK7NMRE4YvlwQP.7q9Ewng.4XW', 'student', 'active', '2025-12-01 08:44:00', '2025-12-01 08:44:00', '072ccc00bf008012c80fb0190436cd72528383d6f4500b9fdc2620e5a9f112bc', '2025-12-10 13:20:07'),
(4, 'ven', 'super@gmail.com', '$2y$12$F1DyeRppJ6B12y5JB.iKbep2zwLPK7NMRE4YvlwQP.7q9Ewng.4XW', 'super', 'active', '2025-12-01 08:37:50', '2025-12-01 08:37:50', NULL, NULL),
(5, 'quijano', 'emmanuelequijanoboss02@gmail.com', '$2y$10$xKJ3ZL3G/D8FVNQbAaYekO/5yUP3SywuSNSwHCHDBwkOnYVLpyDci', 'faculty', 'active', '2025-12-10 18:53:26', '2025-12-10 18:53:26', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `booking_modification_requests`
--
ALTER TABLE `booking_modification_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_booking_requests`
--
ALTER TABLE `student_booking_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
