-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 09, 2025 at 08:46 AM
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
(1, 9, 4, '2025-12-04', '10:00:00', 5, NULL, '2025-12-03 10:10:21', '2025-12-07 08:08:36'),
(4, 14, 2, '2025-12-12', '10:00:00', 5, NULL, '2025-12-05 19:32:09', '2025-12-07 08:08:43');

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
(9, '102', 'CICS', 30, 'available', '2025-12-01 19:09:40', '2025-12-02 15:30:34'),
(14, '503', 'CICS', 50, 'available', '2025-12-04 14:17:14', '2025-12-04 14:17:14'),
(15, '201', 'CICS', 20, 'available', '2025-12-04 14:17:23', '2025-12-04 14:17:23');

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
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_booking_requests`
--

INSERT INTO `student_booking_requests` (`id`, `student_id`, `room_id`, `date`, `start_time`, `duration`, `purpose`, `status`, `faculty_id`, `created_at`, `updated_at`) VALUES
(1, 3, 14, '2025-12-13', '15:38:00', 4, 'gfrefdsgrgfse', 'pending', 2, '2025-12-08 15:38:12', '2025-12-08 15:38:12'),
(2, 3, 14, '2025-12-11', '16:39:00', 2, 'asfasdfdas', 'pending', 2, '2025-12-08 15:39:15', '2025-12-08 15:39:15'),
(3, 3, 15, '2025-12-11', '17:17:00', 3, 'dscdsfdsgsfgdfsa', 'pending', 4, '2025-12-08 16:17:20', '2025-12-08 16:17:20'),
(4, 3, 9, '2025-12-11', '17:17:00', 3, 'dscdsfdsgsfgdfsa', 'pending', 4, '2025-12-08 16:19:24', '2025-12-08 16:19:24'),
(5, 3, 9, '2025-12-11', '08:17:00', 12, 'dscdsfdsgsfgdfsa', 'pending', 4, '2025-12-08 16:21:07', '2025-12-08 16:21:07'),
(6, 3, 15, '2025-12-17', '07:21:00', 9, 'asdfsadfsadfsda', 'pending', 2, '2025-12-08 16:21:32', '2025-12-08 16:21:32');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','faculty','student') NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `role`, `status`, `created_at`, `updated_at`) VALUES
(1, 'chiharu', 'quijanoemman99@gmail.com', '$2y$12$MzXDBPqSnJpTFZp8mu0EFeiGgrnuew9Cu4jKejifm3wGhe5o6.9Hm', 'admin', 'active', '2025-11-30 06:54:09', '2025-11-30 11:16:22'),
(2, 'tungtung sahur', 'emmanuelequijanoboss02@gmail.com', '$2y$12$F1DyeRppJ6B12y5JB.iKbep2zwLPK7NMRE4YvlwQP.7q9Ewng.4XW', 'faculty', 'active', '2025-12-01 08:37:50', '2025-12-01 08:37:50'),
(3, 'Princess Shrek', 'blairrhoades9@gmail.com', '$2y$12$F1DyeRppJ6B12y5JB.iKbep2zwLPK7NMRE4YvlwQP.7q9Ewng.4XW', 'student', 'active', '2025-12-01 08:44:00', '2025-12-01 08:44:00'),
(4, 'ven', 'ventralberry@gmail.com', '$2y$12$F1DyeRppJ6B12y5JB.iKbep2zwLPK7NMRE4YvlwQP.7q9Ewng.4XW', 'faculty', 'active', '2025-12-01 08:37:50', '2025-12-01 08:37:50');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `booking_modification_requests`
--
ALTER TABLE `booking_modification_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `student_booking_requests`
--
ALTER TABLE `student_booking_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
