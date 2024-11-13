-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 06, 2024 at 04:05 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `elearning`
--

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `class_id` int(11) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `section_name` varchar(50) DEFAULT NULL,
  `schedule_day` varchar(100) DEFAULT NULL,
  `schedule_time` varchar(50) DEFAULT NULL,
  `max_students` int(11) DEFAULT 40,
  `status` enum('active','inactive') DEFAULT 'active',
  `student_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `attendance_rate` decimal(5,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`class_id`, `course_id`, `subject_id`, `teacher_id`, `section_name`, `schedule_day`, `schedule_time`, `max_students`, `status`, `student_id`, `created_at`, `updated_at`, `attendance_rate`) VALUES
(1, 1, 1, 1, 'BSIT-1A', 'Monday,Wednesday', '08:00:00-09:30:00', 40, 'active', 1, '2024-11-05 15:18:15', '2024-11-05 15:23:18', 0.00),
(2, 1, 2, 1, 'BSIT-1A', 'Tuesday,Thursday', '09:30:00-11:00:00', 40, 'active', 2, '2024-11-05 15:18:15', '2024-11-05 15:23:26', 0.00),
(3, 1, 3, 1, 'BSIT-1B', 'Monday,Wednesday', '13:00:00-14:30:00', 40, 'active', 3, '2024-11-05 15:18:15', '2024-11-05 15:23:32', 0.00),
(4, 1, 4, 4, 'BSIT-1B', 'Tuesday,Thursday', '14:30:00-16:00:00', 40, 'active', 4, '2024-11-05 15:18:15', '2024-11-05 15:23:37', 0.00),
(5, 2, 5, 1, 'BSIT-2A', 'Monday,Wednesday', '09:30:00-11:00:00', 40, 'active', 5, '2024-11-05 15:18:15', '2024-11-05 15:23:42', 0.00),
(6, 2, 6, 2, 'BSIT-2A', 'Tuesday,Thursday', '13:00:00-14:30:00', 40, 'active', 6, '2024-11-05 15:18:15', '2024-11-05 15:23:49', 0.00),
(7, 2, 7, 3, 'BSIT-3A', 'Monday,Wednesday', '14:30:00-16:00:00', 40, 'active', 7, '2024-11-05 15:18:15', '2024-11-05 15:23:55', 0.00),
(8, 2, 8, 4, 'BSIT-3A', 'Tuesday,Thursday', '08:00:00-09:30:00', 40, 'active', 8, '2024-11-05 15:18:15', '2024-11-05 15:24:00', 0.00),
(9, 3, 9, 1, 'BSIT-3B', 'Monday,Wednesday', '11:00:00-12:30:00', 40, 'active', 9, '2024-11-05 15:18:15', '2024-11-05 15:24:06', 0.00),
(10, 3, 10, 2, 'BSIT-3B', 'Tuesday,Thursday', '16:00:00-17:30:00', 40, 'active', 10, '2024-11-05 15:18:15', '2024-11-05 15:24:14', 0.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`class_id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `student_id` (`student_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
