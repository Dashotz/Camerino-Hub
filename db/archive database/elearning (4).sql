-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 06, 2024 at 02:44 PM
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
-- Table structure for table `active_sessions`
--

CREATE TABLE `active_sessions` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `last_activity` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
  `activity_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `type` enum('activity','quiz') NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `quiz_link` varchar(255) DEFAULT NULL,
  `due_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `points` int(11) DEFAULT 100
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activities`
--

INSERT INTO `activities` (`activity_id`, `teacher_id`, `class_id`, `title`, `description`, `type`, `file_path`, `quiz_link`, `due_date`, `created_at`, `updated_at`, `points`) VALUES
(1, 2, 6, 'TITE', 'Test', 'activity', 'uploads/activities/activity_672b6fb958a75.docx', NULL, '2024-11-06', '2024-11-06 13:31:37', '2024-11-06 13:31:37', 100);

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `username`, `password`, `email`, `firstname`, `lastname`, `created_at`) VALUES
(1, 'admin', 'admin123', 'admin@gmail.com', 'admin', 'admins', '2024-10-01 14:55:15');

-- --------------------------------------------------------

--
-- Table structure for table `admin_login_logs`
--

CREATE TABLE `admin_login_logs` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `status` varchar(20) NOT NULL CHECK (`status` in ('success','failed','logout')),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_login_logs`
--

INSERT INTO `admin_login_logs` (`id`, `admin_id`, `ip_address`, `status`, `created_at`) VALUES
(1, 1, '::1', 'success', '2024-10-31 14:55:52'),
(2, 1, '::1', 'logout', '2024-10-31 15:03:50'),
(3, 1, '::1', 'success', '2024-10-31 15:03:56'),
(4, 1, '::1', 'logout', '2024-10-31 15:29:53'),
(5, 1, '::1', 'success', '2024-11-01 03:38:05'),
(6, 1, '::1', 'logout', '2024-11-01 04:34:14'),
(7, 1, '::1', 'success', '2024-11-01 04:35:55'),
(8, 1, '::1', 'logout', '2024-11-01 04:36:03'),
(9, 1, '::1', 'success', '2024-11-06 12:54:05'),
(10, 1, '::1', 'logout', '2024-11-06 12:54:17'),
(11, 1, '::1', 'success', '2024-11-06 12:54:59'),
(12, 1, '::1', 'logout', '2024-11-06 12:55:55');

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `assignment_id` int(11) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `due_date` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assignments`
--

INSERT INTO `assignments` (`assignment_id`, `course_id`, `teacher_id`, `title`, `description`, `due_date`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Algebra Basics', 'Complete exercises 1-10 from Chapter 1', '2024-03-25 23:59:59', '2024-11-05 15:03:41', '2024-11-05 15:03:41'),
(2, 1, 1, 'Linear Equations', 'Solve word problems using linear equations', '2024-03-28 23:59:59', '2024-11-05 15:03:41', '2024-11-05 15:03:41'),
(3, 1, 1, 'Quadratic Functions', 'Graph quadratic equations and find roots', '2024-04-01 23:59:59', '2024-11-05 15:03:41', '2024-11-05 15:03:41'),
(4, 1, 1, 'Essay Writing', 'Write a 1000-word essay on modern literature', '2023-12-15 23:59:59', '2024-11-05 15:03:41', '2024-11-05 15:03:41'),
(5, 1, 1, 'Python Basics', 'Complete basic Python programming exercises', '2024-03-30 23:59:59', '2024-11-05 15:03:41', '2024-11-05 15:03:41'),
(6, 1, 1, 'Data Structures', 'Implement a linked list and binary tree', '2024-04-05 23:59:59', '2024-11-05 15:03:41', '2024-11-05 15:03:41'),
(7, 1, 1, 'Algorithms', 'Analyze and implement sorting algorithms', '2024-04-12 23:59:59', '2024-11-05 15:03:41', '2024-11-05 15:03:41');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `status` enum('present','absent','late') DEFAULT 'present'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `student_id`, `course_id`, `date`, `status`) VALUES
(1, 1, 2, '2024-03-18', 'present'),
(2, 1, 2, '2024-03-20', 'present'),
(3, 1, 2, '2024-03-22', 'late'),
(4, 2, 2, '2024-03-18', 'present'),
(5, 2, 2, '2024-03-20', 'absent'),
(6, 2, 2, '2024-03-22', 'present');

-- --------------------------------------------------------

--
-- Table structure for table `class`
--

CREATE TABLE `class` (
  `class_id` int(11) NOT NULL,
  `course_id` varchar(100) NOT NULL,
  `subject_id` varchar(100) NOT NULL,
  `teacher_id` varchar(100) NOT NULL,
  `student_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `class`
--

INSERT INTO `class` (`class_id`, `course_id`, `subject_id`, `teacher_id`, `student_id`) VALUES
(5, 'BSIS 3B', 'Fundamentals of Business Management', '1', 0),
(4, 'BSIS -3A', 'Fundamentals of Business Management', '1', 1),
(7, 'BSIS -3A', 'DBMS', '2', 13),
(9, 'BSIS 3B', 'Fundamentals of Business Management', '2', 0);

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

-- --------------------------------------------------------

--
-- Table structure for table `class_students`
--

CREATE TABLE `class_students` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

CREATE TABLE `course` (
  `course_id` int(11) NOT NULL,
  `cys` varchar(100) NOT NULL,
  `department` varchar(100) NOT NULL,
  `major` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `course`
--

INSERT INTO `course` (`course_id`, `cys`, `department`, `major`) VALUES
(5, 'BSIS -3A', 'CIT', ''),
(6, 'BSIS 3B', 'College of Industrial Technology', 'None'),
(7, 'BSIS 3C', 'College of Industrial Technology', 'None');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `course_id` int(11) NOT NULL,
  `course_name` varchar(255) DEFAULT NULL,
  `teacher_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`course_id`, `course_name`, `teacher_id`) VALUES
(1, 'Mathematics 101', 1),
(2, 'Biology 101', 2),
(3, 'English 101', 3),
(4, 'Computer Science 101', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `dep_id` int(11) NOT NULL,
  `incharge` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  `department` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`dep_id`, `incharge`, `title`, `department`) VALUES
(4, 'Dr. Antonio Derajah', 'Dean', 'College of Industrial Technology'),
(5, 'Prof. Luisa Tejada', 'Dean', 'School of Arts And Sciences'),
(6, 'hmmm ', 'Dean', 'College of Education');

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
  `file_id` int(11) NOT NULL,
  `floc` varchar(500) NOT NULL,
  `fdatein` varchar(200) NOT NULL,
  `fdesc` varchar(100) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `fname` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `files`
--

INSERT INTO `files` (`file_id`, `floc`, `fdatein`, `fdesc`, `teacher_id`, `class_id`, `fname`) VALUES
(18, 'uploads/2640_File_CHAPTER 2.docx', '2013-03-24 14:38:20', 'i dont know', 1, 4, 'chapter 2'),
(17, 'uploads/2682_File_Chapter1.docx', '2013-03-24 13:40:58', 'i dont know', 1, 4, 'chapter 1'),
(26, 'uploads/3079_File_chapter_2_sausa.docx', '2013-03-25 08:47:16', 'Searching the Web', 2, 7, 'Chapter 2 - Searching the Web'),
(29, 'uploads/7258_File_INPUTTING DATA IN OTHER WAYS.docx', '2013-03-25 08:50:19', 'Inputting Data in Other Ways', 2, 7, 'Chapter 3B - Inputting Data in Other Ways'),
(25, 'uploads/8728_File_lesson1a.docx', '2013-03-25 08:46:46', 'Computer in our World', 2, 7, 'Chapter 1 - Computer in our World'),
(28, 'uploads/4322_File_fnal doc.chap.3.docx', '2013-03-25 08:49:42', 'The Keyboard and Mouse', 2, 7, 'Chapter 3A - The Keyboard and Mouse'),
(30, 'uploads/9448_File_Chapter 4B.docx', '2013-03-25 08:55:30', 'Printing', 2, 7, 'Chapter 4B-printing');

-- --------------------------------------------------------

--
-- Table structure for table `login_logs`
--

CREATE TABLE `login_logs` (
  `id` int(11) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `status` enum('success','failed') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_logs`
--

INSERT INTO `login_logs` (`id`, `student_id`, `ip_address`, `status`, `created_at`) VALUES
(59, '1', '::1', 'success', '2024-11-04 15:41:59'),
(60, '1', '::1', 'success', '2024-11-04 15:42:23'),
(61, '1', '::1', 'success', '2024-11-04 15:42:33'),
(62, '1', '::1', 'success', '2024-11-04 15:46:02'),
(63, '1', '::1', 'success', '2024-11-04 15:46:18'),
(64, '1', '::1', 'success', '2024-11-04 16:07:28'),
(65, '1', '::1', 'success', '2024-11-05 05:14:00'),
(66, '1', '::1', 'success', '2024-11-05 05:46:57'),
(67, '1', '::1', 'success', '2024-11-05 12:02:58'),
(68, '1', '::1', 'success', '2024-11-05 13:15:06'),
(69, '1', '::1', 'success', '2024-11-05 13:16:40'),
(70, '1', '::1', 'success', '2024-11-05 13:16:58'),
(71, '1', '::1', 'success', '2024-11-05 13:17:13'),
(72, '1', '::1', 'success', '2024-11-05 13:19:03'),
(73, '1', '::1', 'success', '2024-11-05 13:20:26'),
(74, '1', '::1', 'success', '2024-11-05 13:20:55'),
(75, '1', '::1', 'success', '2024-11-05 13:26:26'),
(76, '1', '::1', 'success', '2024-11-05 13:41:18'),
(77, '1', '::1', 'success', '2024-11-05 14:04:18'),
(78, '1', '::1', 'success', '2024-11-05 14:23:22'),
(79, '1', '::1', 'success', '2024-11-05 14:31:26'),
(80, '1', '::1', 'success', '2024-11-05 17:15:42'),
(81, '1', '::1', 'success', '2024-11-05 17:39:49'),
(82, '1', '::1', 'success', '2024-11-05 17:48:20'),
(83, '1', '::1', 'success', '2024-11-06 09:07:08'),
(84, '1', '::1', 'success', '2024-11-06 09:30:47'),
(85, '1', '::1', 'success', '2024-11-06 11:54:56');

-- --------------------------------------------------------

--
-- Table structure for table `remember_tokens`
--

CREATE TABLE `remember_tokens` (
  `id` int(11) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expiry` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `student_id` int(11) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `middle_name` varchar(100) NOT NULL,
  `cys` varchar(100) NOT NULL,
  `subject_id` varchar(100) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(100) NOT NULL,
  `location` varchar(100) NOT NULL,
  `login_attempts` int(11) DEFAULT 0,
  `lockout_until` datetime DEFAULT NULL,
  `status` enum('active','archived','graduated','suspended') NOT NULL DEFAULT 'active',
  `user_online` tinyint(1) DEFAULT 0,
  `last_activity` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`student_id`, `firstname`, `lastname`, `middle_name`, `cys`, `subject_id`, `teacher_id`, `username`, `email`, `password`, `location`, `login_attempts`, `lockout_until`, `status`, `user_online`, `last_activity`) VALUES
(1, 'christian', 'pacifico', 'ilagan', 'BSIT-4A', '2', 1, 'king', 'king@student.camerino.edu.ph', 'king123', '../images/student1.png', 0, NULL, 'active', 0, '2024-11-04 15:46:02'),
(2, 'Christopher', 'Pacifico', 'Ilagan', 'BSIT-4A', '', 1, 'tupey', 'tupey@student.camerino.edu.ph', '$2y$10$gnMGZCxQUXH7GsjIcpwnC.5ow3Bzr89W5TUKlOszylFRINkUgv7PC', 'Bacoor', 0, NULL, 'active', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `student_activity_submissions`
--

CREATE TABLE `student_activity_submissions` (
  `submission_id` int(11) NOT NULL,
  `activity_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `points` int(11) DEFAULT NULL,
  `feedback` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_courses`
--

CREATE TABLE `student_courses` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `class_id` int(11) DEFAULT NULL,
  `status` enum('active','completed','dropped') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_courses`
--

INSERT INTO `student_courses` (`id`, `student_id`, `course_id`, `class_id`, `status`) VALUES
(1, 1, 1, 2, 'active'),
(2, 2, 1, 1, 'active'),
(3, 3, 1, 1, 'active'),
(4, 1, 2, 1, 'active'),
(5, 2, 2, 1, 'active'),
(6, 4, 2, 1, 'active'),
(7, 1, 3, 1, 'completed'),
(8, 2, 3, 1, 'completed'),
(9, 3, 3, 1, 'dropped'),
(10, 1, 4, 1, 'active'),
(11, 3, 4, 1, 'active'),
(12, 4, 4, 1, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `student_submissions`
--

CREATE TABLE `student_submissions` (
  `submission_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `assignment_id` int(11) DEFAULT NULL,
  `submitted_at` datetime DEFAULT NULL,
  `grade` decimal(5,2) DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_submissions`
--

INSERT INTO `student_submissions` (`submission_id`, `student_id`, `assignment_id`, `submitted_at`, `grade`, `feedback`, `file_path`) VALUES
(1, 1, 1, '2024-03-24 14:30:00', 95.50, NULL, NULL),
(2, 1, 2, '2024-03-27 16:45:00', 88.75, NULL, NULL),
(3, 2, 1, '2024-03-25 09:15:00', 78.25, NULL, NULL),
(4, 3, 1, '2024-03-24 23:50:00', 92.00, NULL, NULL),
(5, 1, 4, '2023-12-14 18:20:00', 94.00, NULL, NULL),
(6, 2, 4, '2023-12-15 22:30:00', 88.50, NULL, NULL),
(7, 1, 5, '2024-03-29 15:45:00', 97.00, NULL, NULL),
(8, 3, 5, '2024-03-30 23:45:00', 85.50, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `subject`
--

CREATE TABLE `subject` (
  `subject_id` int(11) NOT NULL,
  `subject_code` varchar(100) NOT NULL,
  `subject_title` varchar(100) NOT NULL,
  `category` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `subject`
--

INSERT INTO `subject` (`subject_id`, `subject_code`, `subject_title`, `category`) VALUES
(5, 'IS 123', 'Fundamentals of Business Management', 'Technology and Livelihood Education'),
(7, 'IS 301', 'DBMS', 'Major'),
(8, 'GNED-09', 'MATH', 'test');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `subject_code` varchar(20) NOT NULL,
  `subject_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `subject_code`, `subject_name`, `created_at`, `updated_at`) VALUES
(1, 'GDM-200', 'Mathematics', '2024-10-30 13:09:22', '2024-10-30 13:09:22'),
(2, 'GDM-201', 'Filipino', '2024-10-30 13:09:22', '2024-10-30 13:09:22'),
(3, 'GDM-202', 'Aralin Panlipunan', '2024-10-30 13:09:22', '2024-10-30 13:09:22'),
(4, 'GDM-203', 'English', '2024-10-30 13:09:22', '2024-10-30 13:09:22'),
(5, 'GDM-204', 'Science', '2024-10-30 13:09:22', '2024-10-30 13:09:22'),
(6, 'GDM-205', 'Edukasyon sa Pagpapakatao', '2024-10-30 13:09:22', '2024-10-30 13:09:22'),
(7, 'GDM-206', 'Technology and Livelihood Education (TLE)', '2024-10-30 13:09:22', '2024-10-30 13:09:22'),
(8, 'GDM-207', 'Music, Arts, PE, and Health (MAPEH)', '2024-10-30 13:09:22', '2024-10-30 13:09:22');

-- --------------------------------------------------------

--
-- Table structure for table `sws`
--

CREATE TABLE `sws` (
  `sws_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `cys` varchar(100) NOT NULL,
  `subject_id` varchar(100) NOT NULL,
  `class_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `sws`
--

INSERT INTO `sws` (`sws_id`, `student_id`, `teacher_id`, `cys`, `subject_id`, `class_id`) VALUES
(9, 17, 2, 'BSIS -3A', 'DBMS', 7),
(8, 16, 2, 'BSIS -3A', 'DBMS', 7),
(7, 13, 2, 'BSIS -3A', 'DBMS', 7),
(10, 17, 2, 'BSIS -3A', 'DBMS', 9),
(11, 16, 2, 'BSIS -3A', 'DBMS', 9);

-- --------------------------------------------------------

--
-- Table structure for table `sy`
--

CREATE TABLE `sy` (
  `sy_id` int(11) NOT NULL,
  `sy` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `sy`
--

INSERT INTO `sy` (`sy_id`, `sy`) VALUES
(3, '2012-2013'),
(5, 't');

-- --------------------------------------------------------

--
-- Table structure for table `teacher`
--

CREATE TABLE `teacher` (
  `teacher_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(200) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `middlename` varchar(100) NOT NULL,
  `department` varchar(100) NOT NULL,
  `student_id` int(11) NOT NULL,
  `location` varchar(200) NOT NULL,
  `login_attempts` int(11) DEFAULT 0,
  `lockout_time` datetime DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `teacher`
--

INSERT INTO `teacher` (`teacher_id`, `username`, `password`, `firstname`, `lastname`, `middlename`, `department`, `student_id`, `location`, `login_attempts`, `lockout_time`, `subject_id`, `course_id`, `status`) VALUES
(1, 'jk', 'jk', 'Jomar', 'Pabuaya', 'smith', 'College of Industrial Technology', 0, 'uploads/images (3).jpg', 0, NULL, 1, 1, 'active'),
(2, 'chaw', 'pan', 'Charito', 'Puray', 'dela tore', 'College of Industrial Technology', 0, 'uploads/images (3).jpg', 0, NULL, 1, 1, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `id` int(11) NOT NULL,
  `username` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `password` varchar(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `firstname` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `lastname` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `middlename` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `department` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `student_id` int(11) NOT NULL,
  `location` varchar(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `login_attempts` int(11) DEFAULT 0,
  `lockout_time` datetime DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`id`, `username`, `password`, `firstname`, `lastname`, `middlename`, `department`, `student_id`, `location`, `login_attempts`, `lockout_time`, `profile_picture`) VALUES
(1, 'john.smith', 'password123', 'John', 'Smith', 'Michael', 'Mathematics', 1, 'Room 101', 0, NULL, NULL),
(2, 'maria.garcia', 'password123', 'Maria', 'Garcia', 'Elena', 'Science', 2, 'Room 102', 0, NULL, NULL),
(3, 'david.johnson', 'password123', 'David', 'Johnson', 'Robert', 'English', 3, 'Room 103', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `teacher_student`
--

CREATE TABLE `teacher_student` (
  `teacher_student_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teacher_student`
--

INSERT INTO `teacher_student` (`teacher_student_id`, `teacher_id`, `student_id`, `created_at`) VALUES
(1, 1, 1, '2024-11-01 04:18:42');

-- --------------------------------------------------------

--
-- Table structure for table `teacher_subject`
--

CREATE TABLE `teacher_subject` (
  `teacher_subject_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teacher_subject`
--

INSERT INTO `teacher_subject` (`teacher_subject_id`, `teacher_id`, `subject_id`, `created_at`) VALUES
(1, 1, 2, '2024-11-01 04:19:24');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `username` varchar(150) NOT NULL,
  `password` varchar(150) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `username`, `password`, `firstname`, `lastname`) VALUES
(4, 'john', 'smith', 'john ', 'smith'),
(5, 'sv', 'sv', 'stephanie', 'villanueva'),
(6, 'jkev', 'jkev', 'john kevin', 'lorayna'),
(7, 'jk', 'jkdjakjk', 'jkdkajkj', 'jkjak');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `active_sessions`
--
ALTER TABLE `active_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`activity_id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `admin_login_logs`
--
ALTER TABLE `admin_login_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`assignment_id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `class`
--
ALTER TABLE `class`
  ADD PRIMARY KEY (`class_id`);

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
-- Indexes for table `class_students`
--
ALTER TABLE `class_students`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`course_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`course_id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`dep_id`);

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`file_id`);

--
-- Indexes for table `login_logs`
--
ALTER TABLE `login_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`student_id`);

--
-- Indexes for table `student_activity_submissions`
--
ALTER TABLE `student_activity_submissions`
  ADD PRIMARY KEY (`submission_id`),
  ADD KEY `activity_id` (`activity_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `student_courses`
--
ALTER TABLE `student_courses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `student_submissions`
--
ALTER TABLE `student_submissions`
  ADD PRIMARY KEY (`submission_id`);

--
-- Indexes for table `subject`
--
ALTER TABLE `subject`
  ADD PRIMARY KEY (`subject_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `subject_code` (`subject_code`);

--
-- Indexes for table `sws`
--
ALTER TABLE `sws`
  ADD PRIMARY KEY (`sws_id`);

--
-- Indexes for table `sy`
--
ALTER TABLE `sy`
  ADD PRIMARY KEY (`sy_id`);

--
-- Indexes for table `teacher`
--
ALTER TABLE `teacher`
  ADD PRIMARY KEY (`teacher_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `teacher_student`
--
ALTER TABLE `teacher_student`
  ADD PRIMARY KEY (`teacher_student_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `teacher_subject`
--
ALTER TABLE `teacher_subject`
  ADD PRIMARY KEY (`teacher_subject_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `active_sessions`
--
ALTER TABLE `active_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `activity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admin_login_logs`
--
ALTER TABLE `admin_login_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `assignment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `class`
--
ALTER TABLE `class`
  MODIFY `class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `class_students`
--
ALTER TABLE `class_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `course`
--
ALTER TABLE `course`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `dep_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `login_logs`
--
ALTER TABLE `login_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `student_activity_submissions`
--
ALTER TABLE `student_activity_submissions`
  MODIFY `submission_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_courses`
--
ALTER TABLE `student_courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `student_submissions`
--
ALTER TABLE `student_submissions`
  MODIFY `submission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `subject`
--
ALTER TABLE `subject`
  MODIFY `subject_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `sws`
--
ALTER TABLE `sws`
  MODIFY `sws_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `sy`
--
ALTER TABLE `sy`
  MODIFY `sy_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `teacher`
--
ALTER TABLE `teacher`
  MODIFY `teacher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `teacher_student`
--
ALTER TABLE `teacher_student`
  MODIFY `teacher_student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `teacher_subject`
--
ALTER TABLE `teacher_subject`
  MODIFY `teacher_subject_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `active_sessions`
--
ALTER TABLE `active_sessions`
  ADD CONSTRAINT `active_sessions_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `admin_login_logs`
--
ALTER TABLE `admin_login_logs`
  ADD CONSTRAINT `admin_login_logs_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`admin_id`);

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`);

--
-- Constraints for table `student_courses`
--
ALTER TABLE `student_courses`
  ADD CONSTRAINT `student_courses_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`);

--
-- Constraints for table `teacher_student`
--
ALTER TABLE `teacher_student`
  ADD CONSTRAINT `teacher_student_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `teacher_subject`
--
ALTER TABLE `teacher_subject`
  ADD CONSTRAINT `teacher_subject_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
