-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 17, 2024 at 02:00 AM
-- Server version: 10.11.10-MariaDB
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u804969888_camerinohub`
--

-- --------------------------------------------------------

--
-- Table structure for table `about_us_content`
--

CREATE TABLE `about_us_content` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `type` varchar(50) DEFAULT 'general',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `academic_years`
--

CREATE TABLE `academic_years` (
  `id` int(11) NOT NULL,
  `school_year` varchar(9) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `archived_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `academic_years`
--

INSERT INTO `academic_years` (`id`, `school_year`, `start_date`, `end_date`, `status`, `created_at`, `is_archived`, `archived_at`) VALUES
(1, '2024-2025', '2024-06-01', '2025-03-31', 'active', '2024-11-07 03:28:47', 0, NULL);

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
  `section_subject_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `type` enum('activity','quiz','assignment') DEFAULT 'activity',
  `points` int(11) NOT NULL DEFAULT 100,
  `quiz_link` varchar(255) DEFAULT NULL,
  `quiz_duration` int(11) DEFAULT NULL COMMENT 'Duration in minutes',
  `prevent_tab_switch` tinyint(1) DEFAULT 0,
  `fullscreen_required` tinyint(1) DEFAULT 0,
  `quiz_attempts` int(11) DEFAULT 1,
  `shuffle_questions` tinyint(1) DEFAULT 0,
  `due_date` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('active','inactive','archived') DEFAULT 'active',
  `completion_rate` decimal(5,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activities`
--

INSERT INTO `activities` (`activity_id`, `teacher_id`, `section_subject_id`, `title`, `description`, `file_path`, `type`, `points`, `quiz_link`, `quiz_duration`, `prevent_tab_switch`, `fullscreen_required`, `quiz_attempts`, `shuffle_questions`, `due_date`, `created_at`, `updated_at`, `status`, `completion_rate`) VALUES
(92, 9, 46, 'qweqwe', 'asdasdasd', NULL, 'assignment', 100, NULL, NULL, 0, 0, 1, 0, '2024-12-02 03:39:00', '2024-11-25 03:39:29', '2024-12-09 11:17:41', 'archived', 0.00),
(93, 123, 53, 'asd', 'asd', NULL, 'activity', 100, NULL, NULL, 0, 0, 1, 0, '2024-12-08 05:51:00', '2024-12-01 12:51:51', '2024-12-01 12:51:51', 'active', 0.00),
(94, 9, 56, 'ChawCHawasdasd', 'asd', NULL, 'activity', 0, NULL, NULL, 0, 0, 1, 0, '2024-12-08 05:59:00', '2024-12-01 12:59:47', '2024-12-09 11:17:38', 'archived', 0.00),
(95, 124, 54, 'asd', 'asd', NULL, 'activity', 100, NULL, NULL, 0, 0, 1, 0, '2024-12-08 06:41:00', '2024-12-01 13:41:19', '2024-12-01 13:41:19', 'active', 0.00),
(96, 124, 54, 'asd', 'asd', NULL, 'quiz', 100, 'https://docs.google.com/forms/d/e/1FAIpQLSc3FbCr-qgYgxCClauhotJifydnFffW2sEu2aHrVNjHdmXzrw/viewform?usp=sf_link&embedded=true', 60, 1, 1, 1, 0, '2024-12-01 14:42:00', '2024-12-01 13:42:29', '2024-12-01 13:42:29', 'active', 0.00),
(113, 9, 56, 'sdas', 'dasda', NULL, 'quiz', 3, NULL, 60, 0, 0, 1, 0, '2024-12-05 00:24:00', '2024-12-04 00:24:56', '2024-12-09 11:17:37', 'archived', 0.00),
(114, 9, 56, 'Q', 'Q', NULL, 'quiz', 3, NULL, 60, 0, 0, 1, 0, '2024-12-05 01:37:00', '2024-12-04 01:39:32', '2024-12-09 11:17:35', 'archived', 0.00),
(115, 9, 56, 'q', 'q', NULL, 'quiz', 3, NULL, 60, 0, 0, 1, 0, '2024-12-05 01:39:00', '2024-12-04 01:39:57', '2024-12-09 11:17:33', 'archived', 0.00),
(116, 9, 56, 'a', 'a', NULL, 'quiz', 3, NULL, 60, 0, 0, 1, 0, '2024-12-05 01:41:00', '2024-12-04 01:42:03', '2024-12-09 11:17:31', 'archived', 0.00),
(124, 9, 56, 'a', 'a', NULL, 'quiz', 3, NULL, 60, 0, 0, 1, 0, '2024-12-05 01:50:00', '2024-12-04 01:51:08', '2024-12-09 11:17:29', 'archived', 0.00),
(125, 9, 56, 'TTest', 'test', NULL, 'quiz', 3, NULL, 60, 0, 0, 1, 0, '2024-12-07 03:39:00', '2024-12-06 03:39:52', '2024-12-09 11:17:27', 'archived', 0.00),
(128, 9, 56, 'testtt', 'testt', NULL, 'quiz', 3, NULL, 60, 0, 0, 1, 0, '2024-12-07 05:06:00', '2024-12-06 05:06:12', '2024-12-06 06:00:35', 'archived', 0.00),
(131, 9, 56, 'test', 'test', NULL, 'quiz', 3, NULL, 60, 0, 0, 1, 0, '2024-12-07 06:03:00', '2024-12-06 06:03:12', '2024-12-06 15:58:25', 'archived', 0.00),
(135, 9, 56, 'test', 'test', NULL, 'quiz', 3, NULL, 60, 0, 0, 1, 0, '2024-12-07 06:07:00', '2024-12-06 06:07:43', '2024-12-06 15:58:23', 'archived', 0.00),
(136, 9, 56, 'testtasd', 'testasd', NULL, 'quiz', 11, NULL, 60, 0, 0, 1, 0, '2024-12-07 07:24:00', '2024-12-06 07:24:57', '2024-12-06 15:58:21', 'archived', 0.00),
(137, 9, 56, 'testasdasd', 'asdads', NULL, 'quiz', 2, NULL, 60, 0, 0, 1, 0, '2024-12-07 15:20:00', '2024-12-06 15:21:04', '2024-12-09 11:17:25', 'archived', 0.00),
(138, 9, 56, 'ASDASD', 'ASDASD', NULL, 'quiz', 3, NULL, 60, 0, 0, 1, 0, '2024-12-07 15:58:00', '2024-12-06 15:58:55', '2024-12-06 17:26:58', 'archived', 0.00),
(139, 9, 56, 'SHORT', 'SHORT', NULL, 'quiz', 3, NULL, 60, 0, 0, 1, 0, '2024-12-07 17:27:00', '2024-12-06 17:27:30', '2024-12-09 11:17:23', 'archived', 0.00),
(140, 9, 56, 'NEW', 'NEW', NULL, 'quiz', 3, NULL, 60, 0, 0, 1, 0, '2024-12-07 17:47:00', '2024-12-06 17:47:47', '2024-12-09 11:17:21', 'archived', 0.00),
(141, 9, 56, 'SHOSSSS', 'SSADASD', NULL, 'quiz', 3, NULL, 60, 0, 0, 1, 0, '2024-12-07 18:41:00', '2024-12-06 18:41:43', '2024-12-09 11:17:19', 'archived', 0.00),
(142, 9, 56, 'XZC', 'ZXC', NULL, 'quiz', 5, NULL, 60, 0, 0, 1, 0, '2024-12-08 00:09:00', '2024-12-07 00:09:50', '2024-12-09 11:17:17', 'archived', 0.00),
(143, 9, 56, 'FRRRR', 'FRRR', NULL, 'assignment', 100, NULL, NULL, 0, 0, 1, 0, '2024-12-14 01:02:00', '2024-12-07 01:02:52', '2024-12-09 11:17:15', 'archived', 0.00),
(145, 9, 56, 'asdasd', 'asdad', NULL, 'assignment', 100, NULL, NULL, 0, 0, 1, 0, '2024-12-14 01:16:00', '2024-12-07 01:16:06', '2024-12-09 11:17:14', 'archived', 0.00),
(146, 9, 56, 'web', 'web', NULL, 'quiz', 7, NULL, 60, 0, 0, 1, 0, '2024-12-10 01:37:00', '2024-12-09 01:37:51', '2024-12-09 11:17:11', 'archived', 0.00),
(147, 9, 56, 'Test TEST', 'Test', NULL, 'quiz', 3, NULL, 1, 0, 0, 1, 0, '2024-12-10 01:44:00', '2024-12-09 01:44:42', '2024-12-09 11:17:10', 'archived', 0.00),
(148, 9, 56, 'points', 'points', NULL, 'quiz', 3, NULL, 60, 0, 0, 1, 0, '2024-12-10 02:03:00', '2024-12-09 02:03:38', '2024-12-09 11:17:08', 'archived', 0.00),
(149, 9, 56, 'pacifoc', 'pacifoc', NULL, 'quiz', 3, NULL, 60, 0, 0, 1, 0, '2024-12-10 02:13:00', '2024-12-09 02:13:09', '2024-12-09 11:17:06', 'archived', 0.00),
(150, 9, 56, 'Hahaha', 'Gahaga', NULL, 'quiz', 3, NULL, 60, 0, 0, 1, 0, '2024-12-10 11:14:00', '2024-12-09 11:15:10', '2024-12-09 11:17:03', 'archived', 0.00),
(151, 9, 64, 'quiz 1', 'Description instruction \nNot case sensitive\netc \nEtc', NULL, 'quiz', 4, NULL, 10, 0, 0, 1, 0, '2024-12-10 12:56:00', '2024-12-09 12:56:56', '2024-12-15 04:52:40', 'active', 0.00),
(152, 9, 56, 'Activity 2 MIDTERM LAB', 'Midterm Lab / ACTIVITY 2', NULL, 'activity', 0, NULL, NULL, 0, 0, 1, 0, '2024-12-20 23:59:00', '2024-12-09 14:04:56', '2024-12-09 15:08:59', 'active', 0.00),
(154, 9, 56, 'QUIZ NO 1', 'MULTIPLE CHOICE', NULL, 'quiz', 1, NULL, 20, 1, 0, 1, 0, '2024-12-16 23:00:00', '2024-12-09 16:13:59', '2024-12-09 16:13:59', 'active', 0.00),
(155, 9, 56, 'Activity: Build a Simple Web Page', 'ACTIVTIY 1&2', NULL, 'activity', 0, NULL, NULL, 0, 0, 1, 0, '2024-12-16 23:59:00', '2024-12-09 16:23:39', '2024-12-09 16:26:09', 'active', 0.00),
(156, 127, 58, 'Essay', 'Make an essay about life', NULL, 'assignment', 100, NULL, NULL, 0, 0, 1, 0, '2024-12-18 23:44:00', '2024-12-11 23:46:57', '2024-12-11 23:46:57', 'active', 0.00),
(158, 9, 56, 'qweqwe', 'weqweqwe', NULL, 'quiz', 3, NULL, 60, 0, 0, 1, 0, '2024-12-12 21:04:00', '2024-12-12 12:04:45', '2024-12-12 12:04:45', 'active', 0.00),
(159, 9, 56, 'ee', 'eeeee', NULL, 'quiz', 1, NULL, 60, 0, 0, 1, 0, '2024-12-12 21:10:00', '2024-12-12 12:10:28', '2024-12-12 12:10:28', 'active', 0.00),
(160, 9, 56, 'test', 'qweqweqe', NULL, 'quiz', 1, NULL, 60, 0, 0, 1, 0, '2024-12-12 21:13:00', '2024-12-12 12:13:04', '2024-12-12 12:13:04', 'active', 0.00),
(161, 9, 56, 'e', 'e', NULL, 'assignment', 100, NULL, NULL, 0, 0, 1, 0, '2024-12-19 16:48:00', '2024-12-12 08:48:36', '2024-12-12 15:48:36', 'active', 0.00),
(162, 9, 56, 'test', 'test', NULL, 'quiz', 3, NULL, 60, 0, 0, 1, 0, '2024-12-14 00:22:00', '2024-12-12 16:23:02', '2024-12-12 16:23:02', 'active', 0.00),
(163, 128, 62, 'Activity1', ' Imagine a future technology and create a model of it.', NULL, 'activity', 0, NULL, NULL, 0, 0, 1, 0, '2024-12-24 07:30:00', '2024-12-15 04:34:47', '2024-12-16 14:47:10', 'active', 0.00),
(164, 128, 62, 'Assignment:Essay', 'Make an essay about  \"The Future of Science: A Glimpse into Tomorrow\"', NULL, 'assignment', 0, NULL, NULL, 0, 0, 1, 0, '2024-12-17 18:00:00', '2024-12-15 04:48:38', '2024-12-16 05:20:09', 'active', 0.00),
(165, 129, 66, 'Arts:Drawing', 'Draw your favorite animals', NULL, 'activity', 0, NULL, NULL, 0, 0, 1, 0, '2024-12-15 18:00:00', '2024-12-15 05:02:58', '2024-12-16 14:09:06', 'active', 0.00),
(166, 129, 66, 'Health Assessment', 'Complete a personal health assessment, including information about your current diet, exercise habits, sleep patterns, and stress levels.', NULL, 'assignment', 100, NULL, NULL, 0, 0, 1, 0, '2024-12-15 06:00:00', '2024-12-15 05:07:29', '2024-12-15 05:07:29', 'active', 0.00),
(167, 129, 66, 'Quiz ', 'https://docs.google.com/forms/d/e/1FAIpQLSe-koE6JddABInD7j-aTBaBCtaiWGd6XvZBDaUW6Vo2KEN72A/viewform?usp=header', NULL, 'quiz', 2, NULL, 60, 0, 0, 1, 0, '2024-12-15 13:19:00', '2024-12-15 05:20:44', '2024-12-15 05:36:23', 'archived', 0.00),
(168, 129, 66, 'SHORT QUIZ', 'Test your science knowledge', NULL, 'quiz', 3, NULL, 60, 0, 0, 1, 0, '2024-12-15 10:30:00', '2024-12-15 05:27:22', '2024-12-15 05:27:22', 'active', 0.00),
(170, 130, 68, 'Qzui', 'No Erresure', NULL, 'quiz', 3, NULL, 60, 1, 1, 1, 0, '2024-12-18 07:29:00', '2024-12-16 23:30:14', '2024-12-17 00:12:07', 'archived', 0.00),
(171, 130, 68, 'test', 'test', NULL, 'activity', 100, NULL, NULL, 0, 0, 1, 0, '2024-12-23 23:33:00', '2024-12-16 23:34:08', '2024-12-17 00:12:11', 'archived', 0.00),
(172, 130, 68, 'Quiz No.1', 'Answers only.', NULL, 'quiz', 5, NULL, 30, 1, 1, 1, 0, '2024-12-18 07:20:00', '2024-12-16 23:47:42', '2024-12-16 23:47:42', 'active', 0.00),
(173, 130, 68, 'Quiz #2', 'Test', NULL, 'quiz', 2, NULL, 60, 0, 0, 1, 0, '2024-12-18 08:19:00', '2024-12-17 00:19:22', '2024-12-17 00:19:22', 'active', 0.00),
(174, 126, 57, 'test1', 'test1', NULL, 'quiz', 4, NULL, 5, 1, 1, 1, 0, '2024-12-17 09:58:00', '2024-12-17 01:58:25', '2024-12-17 01:59:26', 'archived', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `activity_files`
--

CREATE TABLE `activity_files` (
  `file_id` int(11) NOT NULL,
  `activity_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(100) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_files`
--

INSERT INTO `activity_files` (`file_id`, `activity_id`, `file_name`, `file_path`, `file_type`, `file_size`, `created_at`) VALUES
(29, 92, 'Christian Pacificologo (1).docx', 'uploads/activities/1732502369_6743e361ae95a_6c17f46427645a2e.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 139228, '2024-11-25 10:39:29'),
(30, 93, 'Survey Questionnaire.pdf', 'uploads/activities/1733028711_674beb6722249_b185f17fa6913ffd.pdf', 'application/pdf', 119917, '2024-12-01 12:51:51'),
(31, 94, 'Survey Questionnaire.pdf', 'uploads/activities/1733029187_674bed43c2443_b202bf591659d994.pdf', 'application/pdf', 119917, '2024-12-01 12:59:47'),
(32, 143, 'DCIT-65A-FINAL-PROJECT.pdf', 'uploads/activities/1733504572_67532e3c47429_75612306b6bbf9cc.pdf', 'application/pdf', 7298057, '2024-12-07 01:02:52'),
(34, 145, 'DCIT-65A-FINAL-PROJECT.pdf', 'uploads/activities/1733505366_675331569c2c7_08b031c8017791b2.pdf', 'application/pdf', 7298057, '2024-12-07 01:16:06'),
(35, 161, '08_Handout_2(4).pdf', 'uploads/activities/1734018516_675b05d4ecf34_70de6f6ca48c065d.pdf', 'application/pdf', 170135, '2024-12-12 15:48:36'),
(36, 171, 'Activity_SoscanoHershie.docx', 'uploads/activities/1734392048_6760b8f0ba2a8_1107fac3fcf8282e.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 1219160, '2024-12-16 23:34:08');

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `username`, `password`, `email`, `firstname`, `lastname`, `status`, `created_at`) VALUES
(1, 'admin', '2637a5c30af69a7bad877fdb65fbd78b', 'admin@camerinohub.edu.ph', 'System', 'Administrator', 'active', '2024-11-06 23:41:01');

-- --------------------------------------------------------

--
-- Table structure for table `admin_login_logs`
--

CREATE TABLE `admin_login_logs` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `status` enum('success','failed','logout') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_login_logs`
--

INSERT INTO `admin_login_logs` (`id`, `admin_id`, `ip_address`, `status`, `created_at`) VALUES
(81, 1, '::1', 'success', '2024-12-02 03:08:48'),
(82, 1, '::1', 'success', '2024-12-02 04:21:44'),
(83, 1, '::1', 'success', '2024-12-02 19:05:35'),
(84, 1, '::1', 'logout', '2024-12-02 19:18:09'),
(85, 1, '::1', 'success', '2024-12-02 19:18:12'),
(86, 1, '::1', 'success', '2024-12-04 01:16:18'),
(87, 1, '175.176.36.21', 'success', '2024-12-09 13:15:02'),
(88, 1, '::1', 'success', '2024-12-10 19:40:53'),
(89, 1, '::1', 'logout', '2024-12-10 19:40:57'),
(90, 1, '::1', 'success', '2024-12-10 20:02:57'),
(91, 1, '::1', 'logout', '2024-12-10 20:02:59'),
(92, 1, '::1', 'success', '2024-12-10 20:04:47'),
(93, 1, '::1', 'logout', '2024-12-10 20:04:50'),
(94, 1, '::1', 'success', '2024-12-10 20:18:04'),
(95, 1, '::1', 'logout', '2024-12-10 20:18:07'),
(96, 1, '::1', 'success', '2024-12-10 21:44:21'),
(97, 1, '::1', 'logout', '2024-12-10 21:44:26'),
(98, 1, '::1', 'success', '2024-12-11 01:44:43'),
(99, 1, '180.194.233.34', 'success', '2024-12-11 15:09:58'),
(100, 1, '180.194.233.34', 'logout', '2024-12-11 17:29:06'),
(101, 1, '180.194.233.34', 'success', '2024-12-11 17:29:13'),
(102, 1, '180.194.233.34', 'logout', '2024-12-11 17:29:24'),
(103, 1, '180.194.233.34', 'success', '2024-12-11 17:30:41'),
(104, 1, '180.194.233.34', 'logout', '2024-12-11 17:30:55'),
(105, 1, '180.194.233.34', 'success', '2024-12-11 17:47:52'),
(106, 1, '136.158.49.29', 'success', '2024-12-11 23:20:33'),
(107, 1, '136.158.49.29', 'logout', '2024-12-11 23:33:28'),
(108, 1, '136.158.49.29', 'success', '2024-12-11 23:33:47'),
(109, 1, '136.158.49.29', 'logout', '2024-12-11 23:38:13'),
(110, 1, '136.158.49.29', 'success', '2024-12-11 23:40:43'),
(111, 1, '136.158.49.29', 'logout', '2024-12-11 23:43:50'),
(112, 1, '::1', 'success', '2024-12-12 01:30:10'),
(113, 1, '::1', 'logout', '2024-12-12 01:41:24'),
(114, 1, '::1', 'success', '2024-12-12 15:45:50'),
(115, 1, '::1', 'logout', '2024-12-12 15:46:42'),
(116, 1, '180.194.233.34', 'success', '2024-12-12 16:28:58'),
(117, 1, '180.194.233.34', 'logout', '2024-12-12 16:31:35'),
(118, 1, '180.194.233.34', 'success', '2024-12-12 16:41:05'),
(119, 1, '180.194.233.34', 'logout', '2024-12-12 16:42:15'),
(120, 1, '49.144.12.254', 'success', '2024-12-12 16:47:25'),
(121, 1, '49.144.12.254', 'logout', '2024-12-12 16:47:42'),
(122, 1, '112.208.177.94', 'failed', '2024-12-13 07:44:14'),
(123, 1, '112.208.177.94', 'failed', '2024-12-13 07:44:19'),
(124, 1, '112.208.177.94', 'failed', '2024-12-13 07:45:03'),
(125, 1, '112.208.177.94', 'failed', '2024-12-13 07:55:08'),
(126, 1, '180.194.233.34', 'success', '2024-12-13 11:41:04'),
(127, 1, '136.158.49.29', 'success', '2024-12-15 03:41:15'),
(128, 1, '136.158.49.29', 'logout', '2024-12-15 03:47:38'),
(129, 1, '136.158.49.29', 'success', '2024-12-15 03:52:30'),
(130, 1, '136.158.49.29', 'logout', '2024-12-15 03:52:52'),
(131, 1, '136.158.49.29', 'success', '2024-12-15 03:57:13'),
(132, 1, '136.158.49.29', 'success', '2024-12-15 04:01:50'),
(133, 1, '136.158.49.29', 'logout', '2024-12-15 04:01:56'),
(134, 1, '136.158.49.29', 'logout', '2024-12-15 04:23:58'),
(135, 1, '136.158.49.29', 'success', '2024-12-15 04:26:44'),
(136, 1, '136.158.49.29', 'logout', '2024-12-15 04:36:25'),
(137, 1, '136.158.49.29', 'success', '2024-12-15 04:38:06'),
(138, 1, '136.158.49.29', 'logout', '2024-12-15 04:40:57'),
(139, 1, '136.158.49.29', 'success', '2024-12-15 04:42:44'),
(140, 1, '136.158.49.29', 'success', '2024-12-15 04:43:11'),
(141, 1, '136.158.49.29', 'logout', '2024-12-15 04:50:09'),
(142, 1, '136.158.49.29', 'success', '2024-12-15 04:54:30'),
(143, 1, '136.158.49.29', 'logout', '2024-12-15 05:05:52'),
(144, 1, '103.91.141.65', 'success', '2024-12-15 05:11:14'),
(145, 1, '112.208.177.94', 'failed', '2024-12-15 07:55:03'),
(146, 1, '112.208.177.94', 'failed', '2024-12-15 07:55:11'),
(147, 1, '112.208.177.94', 'failed', '2024-12-15 07:55:17'),
(148, 1, '112.208.177.94', 'failed', '2024-12-15 07:55:23'),
(149, 1, '180.194.233.34', 'success', '2024-12-16 14:51:40'),
(150, 1, '180.194.233.34', 'logout', '2024-12-16 14:55:13'),
(151, 1, '175.176.32.48', 'success', '2024-12-16 23:17:07'),
(152, 1, '175.176.32.48', 'logout', '2024-12-16 23:19:11'),
(153, 1, '175.176.35.25', 'success', '2024-12-16 23:21:01'),
(154, 1, '175.176.35.25', 'logout', '2024-12-16 23:30:23'),
(155, 1, '175.176.35.25', 'success', '2024-12-16 23:53:15'),
(156, 1, '175.176.35.25', 'logout', '2024-12-16 23:54:59'),
(157, 1, '175.176.35.25', 'success', '2024-12-16 23:56:59'),
(158, 1, '175.176.35.25', 'logout', '2024-12-16 23:58:44'),
(159, 1, '175.176.35.25', 'success', '2024-12-16 23:59:35'),
(160, 1, '180.194.233.34', 'success', '2024-12-17 01:54:31'),
(161, 1, '180.194.233.34', 'logout', '2024-12-17 01:56:49');

-- --------------------------------------------------------

--
-- Table structure for table `admin_logs`
--

CREATE TABLE `admin_logs` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `action` varchar(50) NOT NULL,
  `affected_table` varchar(50) NOT NULL,
  `affected_id` int(11) NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `instructions` text DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `status` enum('active','archived') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `type` enum('normal','quiz','activity','assignment') NOT NULL DEFAULT 'normal',
  `due_date` datetime DEFAULT NULL,
  `points` int(11) DEFAULT NULL,
  `priority` enum('low','medium','high') DEFAULT 'medium',
  `reference_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `teacher_id`, `section_id`, `subject_id`, `content`, `instructions`, `title`, `attachment`, `status`, `created_at`, `type`, `due_date`, `points`, `priority`, `reference_id`) VALUES
(50, 9, 20, 23, 'A new quiz has been posted: asd\nDue date: 2024-12-05T20:13\nTotal Points: 1', NULL, 'New Quiz: asd', NULL, 'active', '2024-12-03 20:14:48', 'quiz', NULL, NULL, 'medium', 101),
(51, 9, 20, 23, 'A new quiz has been posted: test1t\nDue date: 2024-12-04T20:25\nTotal Points: 1', NULL, 'New Quiz: test1t', NULL, 'active', '2024-12-03 20:26:13', 'quiz', NULL, NULL, 'medium', 102),
(52, 9, 20, 23, 'A new quiz has been posted: Test\nDue date: 2024-12-04T20:44\nTotal Points: 1', NULL, 'New Quiz: Test', NULL, 'active', '2024-12-03 20:44:54', 'quiz', NULL, NULL, 'medium', 103),
(53, 9, 20, 23, 'A new quiz has been posted: Short answer\nDue date: 2024-12-05T20:48\nTotal Points: 1', NULL, 'New Quiz: Short answer', NULL, 'active', '2024-12-03 20:48:39', 'quiz', NULL, NULL, 'medium', 104),
(54, 9, 20, 23, 'A new quiz has been posted: Test\nDue date: 2024-12-13T20:59\nTotal Points: 3', NULL, 'New Quiz: Test', NULL, 'active', '2024-12-03 21:00:12', 'quiz', NULL, NULL, 'medium', 105),
(55, 9, 20, 23, 'A new quiz has been posted: qwe\nDue date: 2024-12-24T21:11\nTotal Points: 1', NULL, 'New Quiz: qwe', NULL, 'active', '2024-12-03 21:11:18', 'quiz', NULL, NULL, 'medium', 106),
(56, 9, 20, 23, 'A new quiz has been posted: asd\nDue date: 2024-12-04T23:14\nTotal Points: 1', NULL, 'New Quiz: asd', NULL, 'active', '2024-12-03 23:14:53', 'quiz', NULL, NULL, 'medium', 107),
(57, 9, 20, 23, 'A new quiz has been posted: TITE\nDue date: 2024-12-04T23:17\nTotal Points: 4', NULL, 'New Quiz: TITE', NULL, 'active', '2024-12-03 23:17:40', 'quiz', NULL, NULL, 'medium', 108),
(58, 9, 20, 23, 'A new quiz has been posted: wqeqw\nDue date: 2024-12-20T23:25\nTotal Points: 6', NULL, 'New Quiz: wqeqw', NULL, 'active', '2024-12-03 23:25:23', 'quiz', NULL, NULL, 'medium', 109),
(59, 9, 20, 23, 'A new quiz has been posted: test\nDue date: 2024-12-04T23:29\nTotal Points: 1', NULL, 'New Quiz: test', NULL, 'active', '2024-12-03 23:29:09', 'quiz', NULL, NULL, 'medium', 110),
(60, 9, 20, 23, 'A new quiz has been posted: Test123\nDue date: 2024-12-06T23:31\nTotal Points: 1', NULL, 'New Quiz: Test123', NULL, 'active', '2024-12-03 23:32:01', 'quiz', NULL, NULL, 'medium', 111),
(61, 9, 20, 23, 'A new quiz has been posted: test123\nDue date: 2024-12-05T00:13\nTotal Points: 3', NULL, 'New Quiz: test123', NULL, 'active', '2024-12-04 00:13:57', 'quiz', NULL, NULL, 'medium', 112),
(62, 9, 20, 23, 'A new quiz has been posted: sdas\nDue date: 2024-12-05T00:24\nTotal Points: 3', NULL, 'New Quiz: sdas', NULL, 'active', '2024-12-04 00:24:56', 'quiz', NULL, NULL, 'medium', 113),
(63, 9, 20, 23, 'A new quiz has been posted: Q\nDue date: 2024-12-05T01:37\nTotal Points: 3', NULL, 'New Quiz: Q', NULL, 'active', '2024-12-04 01:39:32', 'quiz', NULL, NULL, 'medium', 114),
(64, 9, 20, 23, 'A new quiz has been posted: q\nDue date: 2024-12-05T01:39\nTotal Points: 3', NULL, 'New Quiz: q', NULL, 'active', '2024-12-04 01:39:58', 'quiz', NULL, NULL, 'medium', 115),
(65, 9, 20, 23, 'A new quiz has been posted: a\nDue date: 2024-12-05T01:41\nTotal Points: 3', NULL, 'New Quiz: a', NULL, 'active', '2024-12-04 01:42:03', 'quiz', NULL, NULL, 'medium', 116),
(66, 9, 20, 23, 'A new quiz has been posted: testtt\nDue date: 2024-12-07T05:06\nTotal Points: 3', NULL, 'New Quiz: testtt', NULL, 'active', '2024-12-06 05:06:12', 'quiz', NULL, NULL, 'medium', 128),
(67, 9, 20, 23, 'A new quiz has been posted: test\nDue date: 2024-12-07T06:03\nTotal Points: 3', NULL, 'New Quiz: test', NULL, 'active', '2024-12-06 06:03:12', 'quiz', NULL, NULL, 'medium', 131),
(68, 9, 20, 23, 'A new quiz has been posted: test\nDue date: 2024-12-07T06:07\nTotal Points: 3', NULL, 'New Quiz: test', NULL, 'active', '2024-12-06 06:07:43', 'quiz', NULL, NULL, 'medium', 135),
(69, 9, 20, 23, 'A new quiz has been posted: testtasd\nDue date: 2024-12-07T07:24\nTotal Points: 3', NULL, 'New Quiz: testtasd', NULL, 'active', '2024-12-06 07:24:57', 'quiz', NULL, NULL, 'medium', 136),
(70, 9, 20, 23, 'A new quiz has been posted: testasdasd\nDue date: 2024-12-07T15:20\nTotal Points: 3', NULL, 'New Quiz: testasdasd', NULL, 'active', '2024-12-06 15:21:04', 'quiz', NULL, NULL, 'medium', 137),
(71, 9, 20, 23, 'A new quiz has been posted: ASDASD\nDue date: 2024-12-07T15:58\nTotal Points: 3', NULL, 'New Quiz: ASDASD', NULL, 'active', '2024-12-06 15:58:56', 'quiz', NULL, NULL, 'medium', 138),
(72, 9, 20, 23, 'A new quiz has been posted: SHORT\nDue date: 2024-12-07T17:27\nTotal Points: 3', NULL, 'New Quiz: SHORT', NULL, 'active', '2024-12-06 17:27:30', 'quiz', NULL, NULL, 'medium', 139),
(73, 9, 20, 23, 'A new quiz has been posted: NEW\nDue date: 2024-12-07T17:47\nTotal Points: 3', NULL, 'New Quiz: NEW', NULL, 'active', '2024-12-06 17:47:47', 'quiz', NULL, NULL, 'medium', 140),
(74, 9, 20, 23, 'A new quiz has been posted: SHOSSSS\nDue date: 2024-12-07T18:41\nTotal Points: 3', NULL, 'New Quiz: SHOSSSS', NULL, 'active', '2024-12-06 18:41:43', 'quiz', NULL, NULL, 'medium', 141),
(75, 9, 20, 23, 'A new quiz has been posted: XZC\nDue date: 2024-12-08T00:09\nTotal Points: 5', NULL, 'New Quiz: XZC', NULL, 'active', '2024-12-07 00:09:51', 'quiz', NULL, NULL, 'medium', 142),
(76, 9, 20, 23, 'A new quiz has been posted: web\nDue date: 2024-12-10T01:37\nTotal Points: 7', NULL, 'New Quiz: web', NULL, 'active', '2024-12-09 01:37:51', 'quiz', NULL, NULL, 'medium', 146),
(77, 9, 20, 23, 'A new quiz has been posted: Test TEST\nDue date: 2024-12-10T01:44\nTotal Points: 3', NULL, 'New Quiz: Test TEST', NULL, 'active', '2024-12-09 01:44:42', 'quiz', NULL, NULL, 'medium', 147),
(78, 9, 20, 23, 'A new quiz has been posted: points\nDue date: 2024-12-10T02:03\nTotal Points: 3', NULL, 'New Quiz: points', NULL, 'active', '2024-12-09 02:03:38', 'quiz', NULL, NULL, 'medium', 148),
(79, 9, 20, 23, 'A new quiz has been posted: pacifoc\nDue date: 2024-12-10T02:13\nTotal Points: 3', NULL, 'New Quiz: pacifoc', NULL, 'active', '2024-12-09 02:13:09', 'quiz', NULL, NULL, 'medium', 149),
(80, 9, 20, 23, 'A new quiz has been posted: Hahaha\nDue date: 2024-12-10T11:14\nTotal Points: 3', NULL, 'New Quiz: Hahaha', NULL, 'active', '2024-12-09 11:15:10', 'quiz', NULL, NULL, 'medium', 150),
(81, 9, 20, 23, 'A new quiz has been posted: quiz 1\nDue date: 2024-12-10T12:56\nTotal Points: 4', NULL, 'New Quiz: quiz 1', NULL, 'active', '2024-12-09 12:56:56', 'quiz', NULL, NULL, 'medium', 151),
(82, 9, 20, 23, 'A new quiz has been posted: QUIZ NO 1\nDue date: 2024-12-16T23:00\nTotal Points: 1', NULL, 'New Quiz: QUIZ NO 1', NULL, 'active', '2024-12-09 16:13:59', 'quiz', NULL, NULL, 'medium', 154),
(83, 9, 20, 23, 'A new quiz has been posted: qweqwe\nDue date: 2024-12-12T21:04\nTotal Points: 3', NULL, 'New Quiz: qweqwe', NULL, 'active', '2024-12-12 12:04:45', 'quiz', NULL, NULL, 'medium', 158),
(84, 9, 20, 23, 'A new quiz has been posted: ee\nDue date: 2024-12-12T21:10\nTotal Points: 1', NULL, 'New Quiz: ee', NULL, 'active', '2024-12-12 12:10:28', 'quiz', NULL, NULL, 'medium', 159),
(85, 9, 20, 23, 'A new quiz has been posted: test\nDue date: 2024-12-12T21:13\nTotal Points: 1', NULL, 'New Quiz: test', NULL, 'active', '2024-12-12 12:13:04', 'quiz', NULL, NULL, 'medium', 160),
(86, 9, 20, 23, 'A new quiz has been posted: test\nDue date: 2024-12-14T00:22\nTotal Points: 3', NULL, 'New Quiz: test', NULL, 'active', '2024-12-12 16:23:02', 'quiz', NULL, NULL, 'medium', 162),
(87, 129, 21, 25, 'A new quiz has been posted: Quiz \nDue date: 2024-12-15T13:19\nTotal Points: 1', NULL, 'New Quiz: Quiz ', NULL, 'active', '2024-12-15 05:20:44', 'quiz', NULL, NULL, 'medium', 167),
(88, 129, 21, 25, 'A new quiz has been posted: SHORT QUIZ\nDue date: 2024-12-15T10:30\nTotal Points: 3', NULL, 'New Quiz: SHORT QUIZ', NULL, 'active', '2024-12-15 05:27:22', 'quiz', NULL, NULL, 'medium', 168),
(89, 130, 25, 25, 'A new quiz has been posted: Qzui\nDue date: 2024-12-18T07:29\nTotal Points: 3', NULL, 'New Quiz: Qzui', NULL, 'active', '2024-12-16 23:30:14', 'quiz', NULL, NULL, 'medium', 170),
(90, 130, 25, 25, 'A new quiz has been posted: Quiz No.1\nDue date: 2024-12-18T07:20\nTotal Points: 5', NULL, 'New Quiz: Quiz No.1', NULL, 'active', '2024-12-16 23:47:42', 'quiz', NULL, NULL, 'medium', 172),
(91, 130, 25, 25, 'A new quiz has been posted: Quiz #2\nDue date: 2024-12-18T08:19\nTotal Points: 2', NULL, 'New Quiz: Quiz #2', NULL, 'active', '2024-12-17 00:19:22', 'quiz', NULL, NULL, 'medium', 173),
(92, 126, 25, 23, 'A new quiz has been posted: test1\nDue date: 2024-12-17T09:58\nTotal Points: 4', NULL, 'New Quiz: test1', NULL, 'active', '2024-12-17 01:58:25', 'quiz', NULL, NULL, 'medium', 174);

-- --------------------------------------------------------

--
-- Table structure for table `announcement_comments`
--

CREATE TABLE `announcement_comments` (
  `id` int(11) NOT NULL,
  `announcement_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `archive_academic_years`
--

CREATE TABLE `archive_academic_years` (
  `id` int(11) NOT NULL,
  `original_id` int(11) NOT NULL,
  `school_year` varchar(9) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('active','inactive','archived') DEFAULT 'archived',
  `archived_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `archived_by` int(11) NOT NULL,
  `restore_date` timestamp NULL DEFAULT NULL,
  `restored_by` int(11) DEFAULT NULL,
  `archive_notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `assignment_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `due_date` datetime NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `section_subject_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `status` enum('present','absent','excused') NOT NULL,
  `time_in` time DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `time_status` enum('on_time','late','absent') NOT NULL DEFAULT 'absent',
  `attendance_time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `student_id`, `section_subject_id`, `date`, `status`, `time_in`, `remarks`, `created_at`) VALUES
(107, 50, 68, '2024-12-16', 'present', '23:38:00', '', '2024-12-16 23:39:09');

-- --------------------------------------------------------

--
-- Table structure for table `attendance_logs`
--

CREATE TABLE `attendance_logs` (
  `id` int(11) NOT NULL,
  `attendance_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `action` varchar(50) NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance_logs`
--

INSERT INTO `attendance_logs` (`id`, `attendance_id`, `teacher_id`, `action`, `details`, `created_at`) VALUES
(27, 107, 130, 'save', 'Attendance saved for section_subject_id: 68, date: 2024-12-16', '2024-12-16 23:39:09');

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `class_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `section_name` varchar(50) NOT NULL,
  `schedule_day` enum('Monday','Tuesday','Wednesday','Thursday','Friday') NOT NULL,
  `schedule_time` time NOT NULL,
  `status` enum('active','inactive','archived') DEFAULT 'active',
  `attendance_rate` decimal(5,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_information`
--

CREATE TABLE `contact_information` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `type` varchar(50) DEFAULT 'general',
  `status` enum('active','inactive') DEFAULT 'active',
  `response_status` enum('pending','responded','archived') DEFAULT 'pending',
  `response` text DEFAULT NULL,
  `responded_at` timestamp NULL DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `date_ranges`
--

CREATE TABLE `date_ranges` (
  `id` int(11) NOT NULL,
  `range_type` varchar(20) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `department_id` int(11) NOT NULL,
  `department_name` varchar(100) NOT NULL,
  `department_code` varchar(20) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`department_id`, `department_name`, `department_code`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Mathematics Department', 'MATH', 'Focuses on developing students mathematical skills, logical reasoning, and problem-solving abilities through comprehensive math education.', 'active', '2024-11-23 15:03:01', '2024-11-23 15:03:01'),
(2, 'Science Department', 'SCI', 'Provides hands-on scientific education, covering physics, chemistry, biology, and environmental science to develop scientific inquiry and critical thinking.', 'active', '2024-11-23 15:03:01', '2024-11-23 15:03:01'),
(3, 'English Department', 'ENG', 'Develops students proficiency in English language skills including reading, writing, speaking, and listening through comprehensive language arts education.', 'active', '2024-11-23 15:03:01', '2024-11-23 15:03:01'),
(4, 'Filipino Department', 'FIL', 'Promotes Filipino language mastery and appreciation of Philippine literature and culture through comprehensive Filipino language education.', 'active', '2024-11-23 15:03:01', '2024-11-23 15:03:01'),
(5, 'Social Studies Department', 'SS', 'Teaches history, geography, and social sciences to develop students understanding of society, culture, and civic responsibility.', 'active', '2024-11-23 15:03:01', '2024-11-23 15:03:01'),
(6, 'MAPEH Department', 'MAPEH', 'Integrates Music, Arts, Physical Education, and Health education to develop students artistic, physical, and health awareness skills.', 'active', '2024-11-23 15:03:01', '2024-11-23 15:03:01'),
(7, 'Technology and Livelihood Education Department', 'TLE', 'Provides practical skills training in various technical and vocational areas to prepare students for future careers and entrepreneurship.', 'active', '2024-11-23 15:03:01', '2024-11-23 15:03:01'),
(8, 'Values Education Department', 'VALED', 'Focuses on character development, moral values, and ethical principles to shape responsible and value-oriented citizens.', 'active', '2024-11-23 15:03:01', '2024-11-23 15:03:01'),
(9, 'Guidance and Counseling', 'GUID', 'Provides student support services, career guidance, and personal counseling to promote student well-being and development.', 'active', '2024-11-23 15:03:01', '2024-11-23 15:03:01'),
(10, 'School Administration', 'ADMIN', 'Manages school operations, policies, and administrative functions to ensure effective school management and leadership.', 'active', '2024-11-23 15:03:01', '2024-11-23 15:03:01'),
(11, 'Research and Development Department', 'R&D', 'Promotes academic research, innovation, and continuous improvement in educational practices and methodologies.', 'active', '2024-11-23 15:03:01', '2024-11-23 15:03:01'),
(12, 'ICT Department', 'ICT', 'Manages information technology infrastructure and provides digital literacy education to support modern learning needs.', 'active', '2024-11-23 15:03:01', '2024-11-23 15:03:01'),
(13, 'Student Affairs Department', 'SAD', 'Oversees student activities, organizations, and welfare programs to enhance student life and development.', 'active', '2024-11-23 15:03:01', '2024-11-23 15:03:01');

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `excerpt` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `category` enum('academic','event','announcement') NOT NULL,
  `date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `title`, `content`, `excerpt`, `image`, `category`, `date`, `created_at`, `status`) VALUES
(1, 'School Year 2023-2024 Opening', 'We are thrilled to announce the opening of School Year 2023-2024! As we embark on this new academic journey, we welcome both returning and new students to our campus.\r\n\r\nKey Highlights for this School Year:\r\n• Enhanced curriculum focusing on 21st-century skills\r\n• New extracurricular activities and clubs\r\n• Upgraded classroom facilities and learning resources\r\n• Implementation of blended learning approaches\r\n• Strengthened student support services\r\n\r\nImportant Dates:\r\n- First Day of Classes: June 5, 2024\r\n- Orientation Week: May 29-31, 2024\r\n- Parent-Teacher Meeting: June 15, 2024\r\n\r\nWe look forward to another year of academic excellence, personal growth, and memorable experiences. Let\'s make this school year extraordinary together!', 'Welcome back students! The new school year begins with excitement and new opportunities.', '../images/1.jpg', 'academic', '2024-01-15', '2024-11-15 08:32:02', 'active'),
(2, 'Annual Science Fair 2024', 'The Annual Science Fair 2024 is approaching! This year\'s theme is \"Innovation for Sustainable Future.\"\r\n\r\nEvent Details:\r\n• Date: February 20, 2024\r\n• Time: 8:00 AM - 4:00 PM\r\n• Venue: School Gymnasium\r\n• Categories: Environmental Science, Technology, Health Sciences, Physical Sciences\r\n\r\nCompetition Guidelines:\r\n1. Projects must be original and student-led\r\n2. Teams of 2-3 students allowed\r\n3. Display boards and presentations required\r\n4. Research documentation mandatory\r\n\r\nPrizes:\r\n- 1st Place: ₱5,000 and medals\r\n- 2nd Place: ₱3,000 and medals\r\n- 3rd Place: ₱2,000 and medals\r\n- Special Awards for Innovation\r\n\r\nRegistration deadline: February 10, 2024\r\nContact your science teacher for registration and more information.', 'Join us for an exciting showcase of student science projects and innovations.', '../images/2.jpg', 'event', '2024-02-20', '2024-11-15 08:32:02', 'active'),
(3, 'Important: Class Schedule Updates', 'Important Notice: Class Schedule Updates for the Current Semester\r\n\r\nThe following changes have been implemented to optimize learning experiences:\r\n\r\nMorning Sessions:\r\n• Grade 7: 7:00 AM - 12:00 PM\r\n• Grade 8: 7:30 AM - 12:30 PM\r\n• Grade 9: 8:00 AM - 1:00 PM\r\n\r\nAfternoon Sessions:\r\n• Grade 10: 12:30 PM - 5:30 PM\r\n• Grade 11: 1:00 PM - 6:00 PM\r\n• Grade 12: 1:30 PM - 6:30 PM\r\n\r\nAdditional Changes:\r\n1. Computer Laboratory sessions moved to mornings\r\n2. Physical Education classes scheduled for cooler hours\r\n3. Science Laboratory work in mid-morning slots\r\n4. Reading periods added to early morning schedules\r\n\r\nThese changes take effect from February 20, 2024. Please adjust your daily routines accordingly.', 'Please check the revised class schedules for the upcoming semester.', '../images/3.jpg', 'announcement', '2024-02-15', '2024-11-15 08:32:02', 'active'),
(4, 'New Learning Management System', 'We are excited to introduce our new Learning Management System (LMS) designed to enhance your educational experience!\r\n\r\nKey Features:\r\n• Interactive virtual classrooms\r\n• Real-time progress tracking\r\n• Digital assignment submission\r\n• Integrated video conferencing\r\n• Mobile-friendly interface\r\n• Automated attendance system\r\n• Parent portal access\r\n\r\nBenefits:\r\n1. 24/7 access to learning materials\r\n2. Improved student-teacher communication\r\n3. Paperless submission system\r\n4. Instant feedback on assignments\r\n5. Collaborative learning tools\r\n\r\nTraining Schedule:\r\n- Student Orientation: February 15-16, 2024\r\n- Parent Orientation: February 17, 2024\r\n- Teacher Training: February 12-14, 2024\r\n\r\nSystem Requirements:\r\n• Internet connection\r\n• Updated web browser\r\n• Minimum 4GB RAM device\r\n• Webcam and microphone\r\n\r\nThe new system will be fully implemented starting February 20, 2024.', 'Introducing our new digital learning platform for enhanced online education.', '../images/4.jpg', 'academic', '2024-02-10', '2024-11-15 08:32:02', 'active'),
(5, 'Sports Festival 2024', 'Get ready for the most exciting sports event of the year - Sports Festival 2024!\r\n\r\nEvent Schedule:\r\nMarch 1-5, 2024\r\nDay 1: Opening Ceremony and Track Events\r\nDay 2: Basketball and Volleyball Tournaments\r\nDay 3: Swimming Competition\r\nDay 4: Traditional Filipino Games\r\nDay 5: Championship Games and Closing Ceremony\r\n\r\nSports Categories:\r\n• Track and Field\r\n• Basketball (Boys/Girls)\r\n• Volleyball (Boys/Girls)\r\n• Swimming\r\n• Table Tennis\r\n• Badminton\r\n• Chess\r\n\r\nSpecial Events:\r\n- Inter-class Cheering Competition\r\n- Sports Exhibition Matches\r\n- Alumni Games\r\n- Teachers vs. Students Friendly Matches\r\n\r\nRegistration:\r\n• Sign up through your PE teachers\r\n• Deadline: February 25, 2024\r\n• Medical clearance required\r\n• Parent consent form mandatory\r\n\r\nPrizes for each category:\r\nGold Medal + Certificate\r\nSilver Medal + Certificate\r\nBronze Medal + Certificate', 'Get ready for our annual sports festival featuring various athletic competitions.', '../images/2.jpg', 'event', '2024-03-01', '2024-11-15 08:32:02', 'active'),
(6, 'Enrollment Period Extended', 'IMPORTANT ANNOUNCEMENT: Enrollment Period Extension\r\n\r\nWe are extending the enrollment period until March 15, 2024, to accommodate more students and ensure a smooth registration process.\r\n\r\nExtended Schedule:\r\n• Online Registration: 24/7 until March 15\r\n• On-site Enrollment: Monday-Friday, 8AM-5PM\r\n• Saturday Special Enrollment: 8AM-12PM\r\n\r\nRequired Documents:\r\n1. Form 137 (Report Card)\r\n2. Good Moral Certificate\r\n3. Birth Certificate\r\n4. 2x2 ID Pictures (4 pieces)\r\n5. Certificate of Completion/Graduation\r\n\r\nPayment Options:\r\n- Full Payment with 5% discount\r\n- Quarterly Payment Plan\r\n- Monthly Payment Plan\r\n\r\nSpecial Considerations:\r\n• Early bird discount until March 1\r\n• Sibling discount available\r\n• Scholar application extended\r\n• Financial assistance programs\r\n\r\nFor inquiries:\r\nEmail: enrollment@camerinohub.edu.ph\r\nPhone: (02) 8123-4567\r\nMobile: 0912-345-6789\r\n\r\nDon\'t miss this opportunity to be part of our academic community!', 'The enrollment period has been extended until March 15, 2024.', '../images/1.jpg', 'announcement', '2024-02-25', '2024-11-15 08:32:02', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `user_type` enum('student','teacher','admin') NOT NULL,
  `type` enum('quiz','activity','assignment','announcement') NOT NULL,
  `reference_id` int(11) NOT NULL,
  `activity_id` int(11) DEFAULT NULL,
  `announcement_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `is_system` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `section_id`, `subject_id`, `user_type`, `type`, `reference_id`, `activity_id`, `announcement_id`, `title`, `message`, `is_read`, `is_system`, `created_at`) VALUES
(3, 37, 20, 23, 'student', 'quiz', 114, 114, NULL, 'New Quiz: Q', 'A new quiz has been posted for \nDue date: 2024-12-05T01:37\nTotal Points: 3', 0, 0, '2024-12-04 01:39:32'),
(4, 50, 20, 23, 'student', 'quiz', 114, 114, NULL, 'New Quiz: Q', 'A new quiz has been posted for \nDue date: 2024-12-05T01:37\nTotal Points: 3', 0, 0, '2024-12-04 01:39:32'),
(6, 37, 20, 23, 'student', 'quiz', 115, 115, NULL, 'New Quiz: q', 'A new quiz has been posted for \nDue date: 2024-12-05T01:39\nTotal Points: 3', 0, 0, '2024-12-04 01:39:58'),
(7, 50, 20, 23, 'student', 'quiz', 115, 115, NULL, 'New Quiz: q', 'A new quiz has been posted for \nDue date: 2024-12-05T01:39\nTotal Points: 3', 0, 0, '2024-12-04 01:39:58'),
(9, 37, 20, 23, 'student', 'quiz', 116, 116, NULL, 'New Quiz: a', 'A new quiz has been posted for \nDue date: 2024-12-05T01:41\nTotal Points: 3', 0, 0, '2024-12-04 01:42:03'),
(10, 50, 20, 23, 'student', 'quiz', 116, 116, NULL, 'New Quiz: a', 'A new quiz has been posted for \nDue date: 2024-12-05T01:41\nTotal Points: 3', 0, 0, '2024-12-04 01:42:03'),
(11, 37, 20, 23, 'student', 'quiz', 128, 128, NULL, 'New Quiz: testtt', 'A new quiz has been posted: testtt\nDue date: 2024-12-07T05:06\nTotal Points: 3', 0, 0, '2024-12-06 05:06:12'),
(12, 50, 20, 23, 'student', 'quiz', 128, 128, NULL, 'New Quiz: testtt', 'A new quiz has been posted: testtt\nDue date: 2024-12-07T05:06\nTotal Points: 3', 0, 0, '2024-12-06 05:06:12'),
(14, 37, 20, 23, 'student', 'quiz', 131, 131, NULL, 'New Quiz: test', 'A new quiz has been posted: test\nDue date: 2024-12-07T06:03\nTotal Points: 3', 0, 0, '2024-12-06 06:03:12'),
(15, 50, 20, 23, 'student', 'quiz', 131, 131, NULL, 'New Quiz: test', 'A new quiz has been posted: test\nDue date: 2024-12-07T06:03\nTotal Points: 3', 0, 0, '2024-12-06 06:03:12'),
(17, 37, 20, 23, 'student', 'quiz', 135, 135, NULL, 'New Quiz: test', 'A new quiz has been posted: test\nDue date: 2024-12-07T06:07\nTotal Points: 3', 0, 0, '2024-12-06 06:07:43'),
(18, 50, 20, 23, 'student', 'quiz', 135, 135, NULL, 'New Quiz: test', 'A new quiz has been posted: test\nDue date: 2024-12-07T06:07\nTotal Points: 3', 0, 0, '2024-12-06 06:07:43'),
(20, 37, 20, 23, 'student', 'quiz', 136, 136, NULL, 'New Quiz: testtasd', 'A new quiz has been posted: testtasd\nDue date: 2024-12-07T07:24\nTotal Points: 3', 0, 0, '2024-12-06 07:24:57'),
(21, 50, 20, 23, 'student', 'quiz', 136, 136, NULL, 'New Quiz: testtasd', 'A new quiz has been posted: testtasd\nDue date: 2024-12-07T07:24\nTotal Points: 3', 0, 0, '2024-12-06 07:24:57'),
(22, 37, 20, 23, 'student', 'quiz', 137, 137, NULL, 'New Quiz: testasdasd', 'A new quiz has been posted: testasdasd\nDue date: 2024-12-07T15:20\nTotal Points: 3', 0, 0, '2024-12-06 15:21:04'),
(23, 50, 20, 23, 'student', 'quiz', 137, 137, NULL, 'New Quiz: testasdasd', 'A new quiz has been posted: testasdasd\nDue date: 2024-12-07T15:20\nTotal Points: 3', 0, 0, '2024-12-06 15:21:04'),
(25, 37, 20, 23, 'student', 'quiz', 138, 138, NULL, 'New Quiz: ASDASD', 'A new quiz has been posted: ASDASD\nDue date: 2024-12-07T15:58\nTotal Points: 3', 0, 0, '2024-12-06 15:58:56'),
(26, 50, 20, 23, 'student', 'quiz', 138, 138, NULL, 'New Quiz: ASDASD', 'A new quiz has been posted: ASDASD\nDue date: 2024-12-07T15:58\nTotal Points: 3', 0, 0, '2024-12-06 15:58:56'),
(28, 37, 20, 23, 'student', 'quiz', 139, 139, NULL, 'New Quiz: SHORT', 'A new quiz has been posted: SHORT\nDue date: 2024-12-07T17:27\nTotal Points: 3', 0, 0, '2024-12-06 17:27:30'),
(29, 50, 20, 23, 'student', 'quiz', 139, 139, NULL, 'New Quiz: SHORT', 'A new quiz has been posted: SHORT\nDue date: 2024-12-07T17:27\nTotal Points: 3', 0, 0, '2024-12-06 17:27:30'),
(31, 37, 20, 23, 'student', 'quiz', 140, 140, NULL, 'New Quiz: NEW', 'A new quiz has been posted: NEW\nDue date: 2024-12-07T17:47\nTotal Points: 3', 0, 0, '2024-12-06 17:47:47'),
(32, 50, 20, 23, 'student', 'quiz', 140, 140, NULL, 'New Quiz: NEW', 'A new quiz has been posted: NEW\nDue date: 2024-12-07T17:47\nTotal Points: 3', 0, 0, '2024-12-06 17:47:47'),
(34, 37, 20, 23, 'student', 'quiz', 141, 141, NULL, 'New Quiz: SHOSSSS', 'A new quiz has been posted: SHOSSSS\nDue date: 2024-12-07T18:41\nTotal Points: 3', 0, 0, '2024-12-06 18:41:43'),
(35, 50, 20, 23, 'student', 'quiz', 141, 141, NULL, 'New Quiz: SHOSSSS', 'A new quiz has been posted: SHOSSSS\nDue date: 2024-12-07T18:41\nTotal Points: 3', 0, 0, '2024-12-06 18:41:43'),
(37, 37, 20, 23, 'student', 'quiz', 142, 142, NULL, 'New Quiz: XZC', 'A new quiz has been posted: XZC\nDue date: 2024-12-08T00:09\nTotal Points: 5', 0, 0, '2024-12-07 00:09:51'),
(38, 50, 20, 23, 'student', 'quiz', 142, 142, NULL, 'New Quiz: XZC', 'A new quiz has been posted: XZC\nDue date: 2024-12-08T00:09\nTotal Points: 5', 0, 0, '2024-12-07 00:09:51'),
(40, 37, 20, 23, 'student', 'quiz', 146, 146, NULL, 'New Quiz: web', 'A new quiz has been posted: web\nDue date: 2024-12-10T01:37\nTotal Points: 7', 0, 0, '2024-12-09 01:37:51'),
(41, 50, 20, 23, 'student', 'quiz', 146, 146, NULL, 'New Quiz: web', 'A new quiz has been posted: web\nDue date: 2024-12-10T01:37\nTotal Points: 7', 0, 0, '2024-12-09 01:37:51'),
(43, 37, 20, 23, 'student', 'quiz', 147, 147, NULL, 'New Quiz: Test TEST', 'A new quiz has been posted: Test TEST\nDue date: 2024-12-10T01:44\nTotal Points: 3', 0, 0, '2024-12-09 01:44:42'),
(44, 50, 20, 23, 'student', 'quiz', 147, 147, NULL, 'New Quiz: Test TEST', 'A new quiz has been posted: Test TEST\nDue date: 2024-12-10T01:44\nTotal Points: 3', 0, 0, '2024-12-09 01:44:42'),
(46, 37, 20, 23, 'student', 'quiz', 148, 148, NULL, 'New Quiz: points', 'A new quiz has been posted: points\nDue date: 2024-12-10T02:03\nTotal Points: 3', 0, 0, '2024-12-09 02:03:38'),
(47, 50, 20, 23, 'student', 'quiz', 148, 148, NULL, 'New Quiz: points', 'A new quiz has been posted: points\nDue date: 2024-12-10T02:03\nTotal Points: 3', 0, 0, '2024-12-09 02:03:38'),
(49, 37, 20, 23, 'student', 'quiz', 149, 149, NULL, 'New Quiz: pacifoc', 'A new quiz has been posted: pacifoc\nDue date: 2024-12-10T02:13\nTotal Points: 3', 0, 0, '2024-12-09 02:13:09'),
(50, 50, 20, 23, 'student', 'quiz', 149, 149, NULL, 'New Quiz: pacifoc', 'A new quiz has been posted: pacifoc\nDue date: 2024-12-10T02:13\nTotal Points: 3', 0, 0, '2024-12-09 02:13:09'),
(52, 37, 20, 23, 'student', 'quiz', 150, 150, NULL, 'New Quiz: Hahaha', 'A new quiz has been posted: Hahaha\nDue date: 2024-12-10T11:14\nTotal Points: 3', 0, 0, '2024-12-09 11:15:10'),
(53, 50, 20, 23, 'student', 'quiz', 150, 150, NULL, 'New Quiz: Hahaha', 'A new quiz has been posted: Hahaha\nDue date: 2024-12-10T11:14\nTotal Points: 3', 0, 0, '2024-12-09 11:15:10'),
(55, 37, 20, 23, 'student', 'quiz', 151, 151, NULL, 'New Quiz: quiz 1', 'A new quiz has been posted: quiz 1\nDue date: 2024-12-10T12:56\nTotal Points: 4', 0, 0, '2024-12-09 12:56:56'),
(56, 50, 20, 23, 'student', 'quiz', 151, 151, NULL, 'New Quiz: quiz 1', 'A new quiz has been posted: quiz 1\nDue date: 2024-12-10T12:56\nTotal Points: 4', 0, 0, '2024-12-09 12:56:56'),
(58, 37, 20, 23, 'student', 'quiz', 154, 154, NULL, 'New Quiz: QUIZ NO 1', 'A new quiz has been posted: QUIZ NO 1\nDue date: 2024-12-16T23:00\nTotal Points: 1', 0, 0, '2024-12-09 16:13:59'),
(59, 50, 20, 23, 'student', 'activity', 152, NULL, NULL, 'Activity Graded: Activity 2 MIDTERM LAB', 'Your submission for Activity 2 MIDTERM LAB has been graded. Score: 100', 0, 0, '2024-12-12 02:09:33'),
(60, 50, 20, 23, 'student', 'quiz', 158, 158, NULL, 'New Quiz: qweqwe', 'A new quiz has been posted: qweqwe\nDue date: 2024-12-12T21:04\nTotal Points: 3', 0, 0, '2024-12-12 12:04:45'),
(61, 222, 20, 23, 'student', 'quiz', 158, 158, NULL, 'New Quiz: qweqwe', 'A new quiz has been posted: qweqwe\nDue date: 2024-12-12T21:04\nTotal Points: 3', 0, 0, '2024-12-12 12:04:45'),
(63, 50, 20, 23, 'student', 'quiz', 159, 159, NULL, 'New Quiz: ee', 'A new quiz has been posted: ee\nDue date: 2024-12-12T21:10\nTotal Points: 1', 0, 0, '2024-12-12 12:10:28'),
(64, 222, 20, 23, 'student', 'quiz', 159, 159, NULL, 'New Quiz: ee', 'A new quiz has been posted: ee\nDue date: 2024-12-12T21:10\nTotal Points: 1', 0, 0, '2024-12-12 12:10:28'),
(66, 50, 20, 23, 'student', 'quiz', 160, 160, NULL, 'New Quiz: test', 'A new quiz has been posted: test\nDue date: 2024-12-12T21:13\nTotal Points: 1', 0, 0, '2024-12-12 12:13:04'),
(67, 222, 20, 23, 'student', 'quiz', 160, 160, NULL, 'New Quiz: test', 'A new quiz has been posted: test\nDue date: 2024-12-12T21:13\nTotal Points: 1', 0, 0, '2024-12-12 12:13:04'),
(68, 223, 20, 23, 'student', 'activity', 161, NULL, NULL, 'Activity Graded: e', 'Your submission for e has been graded. Score: 100', 0, 0, '2024-12-12 15:50:28'),
(69, 50, 20, 23, 'student', 'quiz', 162, 162, NULL, 'New Quiz: test', 'A new quiz has been posted: test\nDue date: 2024-12-14T00:22\nTotal Points: 3', 0, 0, '2024-12-12 16:23:02'),
(70, 222, 20, 23, 'student', 'quiz', 162, 162, NULL, 'New Quiz: test', 'A new quiz has been posted: test\nDue date: 2024-12-14T00:22\nTotal Points: 3', 0, 0, '2024-12-12 16:23:02'),
(71, 223, 20, 23, 'student', 'quiz', 162, 162, NULL, 'New Quiz: test', 'A new quiz has been posted: test\nDue date: 2024-12-14T00:22\nTotal Points: 3', 0, 0, '2024-12-12 16:23:02'),
(72, 50, 20, 23, 'student', 'activity', 155, NULL, NULL, 'Activity Graded: Activity: Build a Simple Web Page', 'Your submission for Activity: Build a Simple Web Page has been graded. Score: 80', 0, 0, '2024-12-12 16:27:35'),
(73, 50, 20, 23, 'student', 'activity', 161, NULL, NULL, 'Activity Graded: e', 'Your submission for e has been graded. Score: 89', 0, 0, '2024-12-12 16:28:23'),
(74, 222, 21, 25, 'student', 'quiz', 167, 167, NULL, 'New Quiz: Quiz ', 'A new quiz has been posted: Quiz \nDue date: 2024-12-15T13:19\nTotal Points: 1', 0, 0, '2024-12-15 05:20:44'),
(75, 222, 21, 25, 'student', 'quiz', 168, 168, NULL, 'New Quiz: SHORT QUIZ', 'A new quiz has been posted: SHORT QUIZ\nDue date: 2024-12-15T10:30\nTotal Points: 3', 0, 0, '2024-12-15 05:27:22'),
(76, 222, 21, 25, 'student', 'activity', 165, NULL, NULL, 'Activity Graded: Arts:Drawing', 'Your submission for Arts:Drawing has been graded. Score: 50', 0, 0, '2024-12-16 14:43:10'),
(77, 222, 21, 24, 'student', 'activity', 163, NULL, NULL, 'Activity Graded: Activity1', 'Your submission for Activity1 has been graded. Score: 100', 0, 0, '2024-12-16 14:44:03'),
(78, 222, 21, 24, 'student', 'activity', 163, NULL, NULL, 'Activity Graded: Activity1', 'Your submission for Activity1 has been graded. Score: 0', 0, 0, '2024-12-16 14:44:36'),
(79, 222, 21, 24, 'student', 'activity', 163, NULL, NULL, 'Activity Graded: Activity1', 'Your submission for Activity1 has been graded. Score: 95', 0, 0, '2024-12-16 14:47:57'),
(80, 50, 25, 25, 'student', 'quiz', 170, 170, NULL, 'New Quiz: Qzui', 'A new quiz has been posted: Qzui\nDue date: 2024-12-18T07:29\nTotal Points: 3', 0, 0, '2024-12-16 23:30:14'),
(81, 50, 25, 25, 'student', 'activity', 171, NULL, NULL, 'Activity Graded: test', 'Your submission for test has been graded. Score: 100', 0, 0, '2024-12-16 23:35:33'),
(82, 50, 25, 25, 'student', 'quiz', 172, 172, NULL, 'New Quiz: Quiz No.1', 'A new quiz has been posted: Quiz No.1\nDue date: 2024-12-18T07:20\nTotal Points: 5', 0, 0, '2024-12-16 23:47:42'),
(83, 50, 25, 25, 'student', 'quiz', 173, 173, NULL, 'New Quiz: Quiz #2', 'A new quiz has been posted: Quiz #2\nDue date: 2024-12-18T08:19\nTotal Points: 2', 0, 0, '2024-12-17 00:19:22'),
(84, 234, 25, 25, 'student', 'quiz', 173, 173, NULL, 'New Quiz: Quiz #2', 'A new quiz has been posted: Quiz #2\nDue date: 2024-12-18T08:19\nTotal Points: 2', 0, 0, '2024-12-17 00:19:22'),
(85, 263, 25, 25, 'student', 'quiz', 173, 173, NULL, 'New Quiz: Quiz #2', 'A new quiz has been posted: Quiz #2\nDue date: 2024-12-18T08:19\nTotal Points: 2', 0, 0, '2024-12-17 00:19:22'),
(86, 238, 25, 25, 'student', 'quiz', 173, 173, NULL, 'New Quiz: Quiz #2', 'A new quiz has been posted: Quiz #2\nDue date: 2024-12-18T08:19\nTotal Points: 2', 0, 0, '2024-12-17 00:19:22'),
(87, 256, 25, 25, 'student', 'quiz', 173, 173, NULL, 'New Quiz: Quiz #2', 'A new quiz has been posted: Quiz #2\nDue date: 2024-12-18T08:19\nTotal Points: 2', 0, 0, '2024-12-17 00:19:22'),
(88, 267, 25, 25, 'student', 'quiz', 173, 173, NULL, 'New Quiz: Quiz #2', 'A new quiz has been posted: Quiz #2\nDue date: 2024-12-18T08:19\nTotal Points: 2', 0, 0, '2024-12-17 00:19:22'),
(89, 228, 25, 25, 'student', 'quiz', 173, 173, NULL, 'New Quiz: Quiz #2', 'A new quiz has been posted: Quiz #2\nDue date: 2024-12-18T08:19\nTotal Points: 2', 0, 0, '2024-12-17 00:19:22'),
(90, 230, 25, 25, 'student', 'quiz', 173, 173, NULL, 'New Quiz: Quiz #2', 'A new quiz has been posted: Quiz #2\nDue date: 2024-12-18T08:19\nTotal Points: 2', 0, 0, '2024-12-17 00:19:22'),
(91, 257, 25, 25, 'student', 'quiz', 173, 173, NULL, 'New Quiz: Quiz #2', 'A new quiz has been posted: Quiz #2\nDue date: 2024-12-18T08:19\nTotal Points: 2', 0, 0, '2024-12-17 00:19:22'),
(92, 247, 25, 25, 'student', 'quiz', 173, 173, NULL, 'New Quiz: Quiz #2', 'A new quiz has been posted: Quiz #2\nDue date: 2024-12-18T08:19\nTotal Points: 2', 0, 0, '2024-12-17 00:19:22'),
(93, 226, 25, 25, 'student', 'quiz', 173, 173, NULL, 'New Quiz: Quiz #2', 'A new quiz has been posted: Quiz #2\nDue date: 2024-12-18T08:19\nTotal Points: 2', 0, 0, '2024-12-17 00:19:22'),
(94, 255, 25, 25, 'student', 'quiz', 173, 173, NULL, 'New Quiz: Quiz #2', 'A new quiz has been posted: Quiz #2\nDue date: 2024-12-18T08:19\nTotal Points: 2', 0, 0, '2024-12-17 00:19:22'),
(95, 266, 25, 25, 'student', 'quiz', 173, 173, NULL, 'New Quiz: Quiz #2', 'A new quiz has been posted: Quiz #2\nDue date: 2024-12-18T08:19\nTotal Points: 2', 0, 0, '2024-12-17 00:19:22'),
(96, 248, 25, 25, 'student', 'quiz', 173, 173, NULL, 'New Quiz: Quiz #2', 'A new quiz has been posted: Quiz #2\nDue date: 2024-12-18T08:19\nTotal Points: 2', 0, 0, '2024-12-17 00:19:22'),
(97, 258, 25, 25, 'student', 'quiz', 173, 173, NULL, 'New Quiz: Quiz #2', 'A new quiz has been posted: Quiz #2\nDue date: 2024-12-18T08:19\nTotal Points: 2', 0, 0, '2024-12-17 00:19:22'),
(98, 254, 25, 25, 'student', 'quiz', 173, 173, NULL, 'New Quiz: Quiz #2', 'A new quiz has been posted: Quiz #2\nDue date: 2024-12-18T08:19\nTotal Points: 2', 0, 0, '2024-12-17 00:19:22'),
(99, 232, 25, 25, 'student', 'quiz', 173, 173, NULL, 'New Quiz: Quiz #2', 'A new quiz has been posted: Quiz #2\nDue date: 2024-12-18T08:19\nTotal Points: 2', 0, 0, '2024-12-17 00:19:22'),
(100, 239, 25, 25, 'student', 'quiz', 173, 173, NULL, 'New Quiz: Quiz #2', 'A new quiz has been posted: Quiz #2\nDue date: 2024-12-18T08:19\nTotal Points: 2', 0, 0, '2024-12-17 00:19:22'),
(101, 225, 25, 25, 'student', 'quiz', 173, 173, NULL, 'New Quiz: Quiz #2', 'A new quiz has been posted: Quiz #2\nDue date: 2024-12-18T08:19\nTotal Points: 2', 0, 0, '2024-12-17 00:19:22'),
(102, 264, 25, 25, 'student', 'quiz', 173, 173, NULL, 'New Quiz: Quiz #2', 'A new quiz has been posted: Quiz #2\nDue date: 2024-12-18T08:19\nTotal Points: 2', 0, 0, '2024-12-17 00:19:22'),
(103, 245, 25, 25, 'student', 'quiz', 173, 173, NULL, 'New Quiz: Quiz #2', 'A new quiz has been posted: Quiz #2\nDue date: 2024-12-18T08:19\nTotal Points: 2', 0, 0, '2024-12-17 00:19:22'),
(104, 252, 25, 25, 'student', 'quiz', 173, 173, NULL, 'New Quiz: Quiz #2', 'A new quiz has been posted: Quiz #2\nDue date: 2024-12-18T08:19\nTotal Points: 2', 0, 0, '2024-12-17 00:19:22'),
(105, 261, 25, 25, 'student', 'quiz', 173, 173, NULL, 'New Quiz: Quiz #2', 'A new quiz has been posted: Quiz #2\nDue date: 2024-12-18T08:19\nTotal Points: 2', 0, 0, '2024-12-17 00:19:22'),
(106, 259, 25, 25, 'student', 'quiz', 173, 173, NULL, 'New Quiz: Quiz #2', 'A new quiz has been posted: Quiz #2\nDue date: 2024-12-18T08:19\nTotal Points: 2', 0, 0, '2024-12-17 00:19:22'),
(107, 260, 25, 25, 'student', 'quiz', 173, 173, NULL, 'New Quiz: Quiz #2', 'A new quiz has been posted: Quiz #2\nDue date: 2024-12-18T08:19\nTotal Points: 2', 0, 0, '2024-12-17 00:19:22'),
(108, 224, 25, 25, 'student', 'quiz', 173, 173, NULL, 'New Quiz: Quiz #2', 'A new quiz has been posted: Quiz #2\nDue date: 2024-12-18T08:19\nTotal Points: 2', 0, 0, '2024-12-17 00:19:22'),
(109, 240, 25, 25, 'student', 'quiz', 173, 173, NULL, 'New Quiz: Quiz #2', 'A new quiz has been posted: Quiz #2\nDue date: 2024-12-18T08:19\nTotal Points: 2', 0, 0, '2024-12-17 00:19:22'),
(110, 269, 25, 25, 'student', 'quiz', 173, 173, NULL, 'New Quiz: Quiz #2', 'A new quiz has been posted: Quiz #2\nDue date: 2024-12-18T08:19\nTotal Points: 2', 0, 0, '2024-12-17 00:19:22'),
(111, 253, 25, 25, 'student', 'quiz', 173, 173, NULL, 'New Quiz: Quiz #2', 'A new quiz has been posted: Quiz #2\nDue date: 2024-12-18T08:19\nTotal Points: 2', 0, 0, '2024-12-17 00:19:22'),
(112, 241, 25, 25, 'student', 'quiz', 173, 173, NULL, 'New Quiz: Quiz #2', 'A new quiz has been posted: Quiz #2\nDue date: 2024-12-18T08:19\nTotal Points: 2', 0, 0, '2024-12-17 00:19:22'),
(113, 265, 25, 25, 'student', 'quiz', 173, 173, NULL, 'New Quiz: Quiz #2', 'A new quiz has been posted: Quiz #2\nDue date: 2024-12-18T08:19\nTotal Points: 2', 0, 0, '2024-12-17 00:19:22'),
(114, 250, 25, 25, 'student', 'quiz', 173, 173, NULL, 'New Quiz: Quiz #2', 'A new quiz has been posted: Quiz #2\nDue date: 2024-12-18T08:19\nTotal Points: 2', 0, 0, '2024-12-17 00:19:22'),
(115, 251, 25, 25, 'student', 'quiz', 173, 173, NULL, 'New Quiz: Quiz #2', 'A new quiz has been posted: Quiz #2\nDue date: 2024-12-18T08:19\nTotal Points: 2', 0, 0, '2024-12-17 00:19:22'),
(116, 231, 25, 25, 'student', 'quiz', 173, 173, NULL, 'New Quiz: Quiz #2', 'A new quiz has been posted: Quiz #2\nDue date: 2024-12-18T08:19\nTotal Points: 2', 0, 0, '2024-12-17 00:19:22'),
(146, 50, 25, 23, 'student', 'quiz', 174, 174, NULL, 'New Quiz: test1', 'A new quiz has been posted: test1\nDue date: 2024-12-17T09:58\nTotal Points: 4', 0, 0, '2024-12-17 01:58:25'),
(147, 234, 25, 23, 'student', 'quiz', 174, 174, NULL, 'New Quiz: test1', 'A new quiz has been posted: test1\nDue date: 2024-12-17T09:58\nTotal Points: 4', 0, 0, '2024-12-17 01:58:25'),
(148, 263, 25, 23, 'student', 'quiz', 174, 174, NULL, 'New Quiz: test1', 'A new quiz has been posted: test1\nDue date: 2024-12-17T09:58\nTotal Points: 4', 0, 0, '2024-12-17 01:58:25'),
(149, 238, 25, 23, 'student', 'quiz', 174, 174, NULL, 'New Quiz: test1', 'A new quiz has been posted: test1\nDue date: 2024-12-17T09:58\nTotal Points: 4', 0, 0, '2024-12-17 01:58:25'),
(150, 256, 25, 23, 'student', 'quiz', 174, 174, NULL, 'New Quiz: test1', 'A new quiz has been posted: test1\nDue date: 2024-12-17T09:58\nTotal Points: 4', 0, 0, '2024-12-17 01:58:25'),
(151, 267, 25, 23, 'student', 'quiz', 174, 174, NULL, 'New Quiz: test1', 'A new quiz has been posted: test1\nDue date: 2024-12-17T09:58\nTotal Points: 4', 0, 0, '2024-12-17 01:58:25'),
(152, 228, 25, 23, 'student', 'quiz', 174, 174, NULL, 'New Quiz: test1', 'A new quiz has been posted: test1\nDue date: 2024-12-17T09:58\nTotal Points: 4', 0, 0, '2024-12-17 01:58:25'),
(153, 230, 25, 23, 'student', 'quiz', 174, 174, NULL, 'New Quiz: test1', 'A new quiz has been posted: test1\nDue date: 2024-12-17T09:58\nTotal Points: 4', 0, 0, '2024-12-17 01:58:25'),
(154, 257, 25, 23, 'student', 'quiz', 174, 174, NULL, 'New Quiz: test1', 'A new quiz has been posted: test1\nDue date: 2024-12-17T09:58\nTotal Points: 4', 0, 0, '2024-12-17 01:58:25'),
(155, 247, 25, 23, 'student', 'quiz', 174, 174, NULL, 'New Quiz: test1', 'A new quiz has been posted: test1\nDue date: 2024-12-17T09:58\nTotal Points: 4', 0, 0, '2024-12-17 01:58:25'),
(156, 226, 25, 23, 'student', 'quiz', 174, 174, NULL, 'New Quiz: test1', 'A new quiz has been posted: test1\nDue date: 2024-12-17T09:58\nTotal Points: 4', 0, 0, '2024-12-17 01:58:25'),
(157, 255, 25, 23, 'student', 'quiz', 174, 174, NULL, 'New Quiz: test1', 'A new quiz has been posted: test1\nDue date: 2024-12-17T09:58\nTotal Points: 4', 0, 0, '2024-12-17 01:58:25'),
(158, 266, 25, 23, 'student', 'quiz', 174, 174, NULL, 'New Quiz: test1', 'A new quiz has been posted: test1\nDue date: 2024-12-17T09:58\nTotal Points: 4', 0, 0, '2024-12-17 01:58:25'),
(159, 248, 25, 23, 'student', 'quiz', 174, 174, NULL, 'New Quiz: test1', 'A new quiz has been posted: test1\nDue date: 2024-12-17T09:58\nTotal Points: 4', 0, 0, '2024-12-17 01:58:25'),
(160, 258, 25, 23, 'student', 'quiz', 174, 174, NULL, 'New Quiz: test1', 'A new quiz has been posted: test1\nDue date: 2024-12-17T09:58\nTotal Points: 4', 0, 0, '2024-12-17 01:58:25'),
(161, 254, 25, 23, 'student', 'quiz', 174, 174, NULL, 'New Quiz: test1', 'A new quiz has been posted: test1\nDue date: 2024-12-17T09:58\nTotal Points: 4', 0, 0, '2024-12-17 01:58:25'),
(162, 232, 25, 23, 'student', 'quiz', 174, 174, NULL, 'New Quiz: test1', 'A new quiz has been posted: test1\nDue date: 2024-12-17T09:58\nTotal Points: 4', 0, 0, '2024-12-17 01:58:25'),
(163, 239, 25, 23, 'student', 'quiz', 174, 174, NULL, 'New Quiz: test1', 'A new quiz has been posted: test1\nDue date: 2024-12-17T09:58\nTotal Points: 4', 0, 0, '2024-12-17 01:58:25'),
(164, 225, 25, 23, 'student', 'quiz', 174, 174, NULL, 'New Quiz: test1', 'A new quiz has been posted: test1\nDue date: 2024-12-17T09:58\nTotal Points: 4', 0, 0, '2024-12-17 01:58:25'),
(165, 264, 25, 23, 'student', 'quiz', 174, 174, NULL, 'New Quiz: test1', 'A new quiz has been posted: test1\nDue date: 2024-12-17T09:58\nTotal Points: 4', 0, 0, '2024-12-17 01:58:25'),
(166, 245, 25, 23, 'student', 'quiz', 174, 174, NULL, 'New Quiz: test1', 'A new quiz has been posted: test1\nDue date: 2024-12-17T09:58\nTotal Points: 4', 0, 0, '2024-12-17 01:58:25'),
(167, 252, 25, 23, 'student', 'quiz', 174, 174, NULL, 'New Quiz: test1', 'A new quiz has been posted: test1\nDue date: 2024-12-17T09:58\nTotal Points: 4', 0, 0, '2024-12-17 01:58:25'),
(168, 261, 25, 23, 'student', 'quiz', 174, 174, NULL, 'New Quiz: test1', 'A new quiz has been posted: test1\nDue date: 2024-12-17T09:58\nTotal Points: 4', 0, 0, '2024-12-17 01:58:25'),
(169, 259, 25, 23, 'student', 'quiz', 174, 174, NULL, 'New Quiz: test1', 'A new quiz has been posted: test1\nDue date: 2024-12-17T09:58\nTotal Points: 4', 0, 0, '2024-12-17 01:58:25'),
(170, 260, 25, 23, 'student', 'quiz', 174, 174, NULL, 'New Quiz: test1', 'A new quiz has been posted: test1\nDue date: 2024-12-17T09:58\nTotal Points: 4', 0, 0, '2024-12-17 01:58:25'),
(171, 224, 25, 23, 'student', 'quiz', 174, 174, NULL, 'New Quiz: test1', 'A new quiz has been posted: test1\nDue date: 2024-12-17T09:58\nTotal Points: 4', 0, 0, '2024-12-17 01:58:25'),
(172, 240, 25, 23, 'student', 'quiz', 174, 174, NULL, 'New Quiz: test1', 'A new quiz has been posted: test1\nDue date: 2024-12-17T09:58\nTotal Points: 4', 0, 0, '2024-12-17 01:58:25'),
(173, 269, 25, 23, 'student', 'quiz', 174, 174, NULL, 'New Quiz: test1', 'A new quiz has been posted: test1\nDue date: 2024-12-17T09:58\nTotal Points: 4', 0, 0, '2024-12-17 01:58:25'),
(174, 253, 25, 23, 'student', 'quiz', 174, 174, NULL, 'New Quiz: test1', 'A new quiz has been posted: test1\nDue date: 2024-12-17T09:58\nTotal Points: 4', 0, 0, '2024-12-17 01:58:25'),
(175, 241, 25, 23, 'student', 'quiz', 174, 174, NULL, 'New Quiz: test1', 'A new quiz has been posted: test1\nDue date: 2024-12-17T09:58\nTotal Points: 4', 0, 0, '2024-12-17 01:58:25'),
(176, 265, 25, 23, 'student', 'quiz', 174, 174, NULL, 'New Quiz: test1', 'A new quiz has been posted: test1\nDue date: 2024-12-17T09:58\nTotal Points: 4', 0, 0, '2024-12-17 01:58:25'),
(177, 250, 25, 23, 'student', 'quiz', 174, 174, NULL, 'New Quiz: test1', 'A new quiz has been posted: test1\nDue date: 2024-12-17T09:58\nTotal Points: 4', 0, 0, '2024-12-17 01:58:25'),
(178, 251, 25, 23, 'student', 'quiz', 174, 174, NULL, 'New Quiz: test1', 'A new quiz has been posted: test1\nDue date: 2024-12-17T09:58\nTotal Points: 4', 0, 0, '2024-12-17 01:58:25'),
(179, 231, 25, 23, 'student', 'quiz', 174, 174, NULL, 'New Quiz: test1', 'A new quiz has been posted: test1\nDue date: 2024-12-17T09:58\nTotal Points: 4', 0, 0, '2024-12-17 01:58:25');

-- --------------------------------------------------------

--
-- Table structure for table `question_choices`
--

CREATE TABLE `question_choices` (
  `choice_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `choice_text` varchar(255) NOT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT 0,
  `choice_order` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `question_choices`
--

INSERT INTO `question_choices` (`choice_id`, `question_id`, `choice_text`, `is_correct`, `choice_order`, `created_at`) VALUES
(41, 25, 'b', 1, 1, '2024-12-04 00:24:56'),
(42, 25, 'c', 0, 2, '2024-12-04 00:24:56'),
(43, 25, 'a', 0, 3, '2024-12-04 00:24:56'),
(44, 26, 'True', 0, 1, '2024-12-04 00:24:56'),
(45, 26, 'False', 1, 2, '2024-12-04 00:24:56'),
(46, 27, 'tite', 1, 1, '2024-12-04 00:24:56'),
(47, 28, 'A', 1, 1, '2024-12-04 01:39:32'),
(48, 28, 'B', 0, 2, '2024-12-04 01:39:32'),
(49, 29, 'True', 0, 1, '2024-12-04 01:39:32'),
(50, 29, 'False', 1, 2, '2024-12-04 01:39:32'),
(51, 30, 'ASD', 1, 1, '2024-12-04 01:39:32'),
(52, 31, 'a', 1, 1, '2024-12-04 01:39:57'),
(53, 31, 'b', 0, 2, '2024-12-04 01:39:57'),
(54, 32, 'True', 0, 1, '2024-12-04 01:39:58'),
(55, 32, 'False', 1, 2, '2024-12-04 01:39:58'),
(56, 33, 'asd', 1, 1, '2024-12-04 01:39:58'),
(57, 34, 'a', 0, 1, '2024-12-04 01:42:03'),
(58, 34, 'n', 1, 2, '2024-12-04 01:42:03'),
(59, 35, 'True', 1, 1, '2024-12-04 01:42:03'),
(60, 35, 'False', 0, 2, '2024-12-04 01:42:03'),
(61, 36, 'asd', 1, 1, '2024-12-04 01:42:03'),
(62, 37, 'a', 0, 1, '2024-12-04 01:51:08'),
(63, 37, 'b', 1, 2, '2024-12-04 01:51:08'),
(64, 38, 'True', 0, 1, '2024-12-04 01:51:09'),
(65, 38, 'False', 1, 2, '2024-12-04 01:51:09'),
(66, 40, 'a', 1, 1, '2024-12-06 03:39:52'),
(67, 40, 'b', 0, 2, '2024-12-06 03:39:52'),
(68, 41, 'True', 0, 1, '2024-12-06 03:39:52'),
(69, 41, 'False', 1, 2, '2024-12-06 03:39:52'),
(70, 45, 'a', 1, 1, '2024-12-06 05:06:12'),
(71, 45, 'b', 0, 2, '2024-12-06 05:06:12'),
(72, 46, 'True', 1, 1, '2024-12-06 05:06:12'),
(73, 46, 'False', 0, 2, '2024-12-06 05:06:12'),
(74, 50, 'a', 1, 1, '2024-12-06 06:03:12'),
(75, 50, 'b', 0, 2, '2024-12-06 06:03:12'),
(76, 51, 'True', 0, 1, '2024-12-06 06:03:12'),
(77, 51, 'False', 0, 2, '2024-12-06 06:03:12'),
(86, 60, 'True', 1, 1, '2024-12-06 06:07:43'),
(87, 60, 'False', 0, 2, '2024-12-06 06:07:43'),
(156, 63, 'True', 1, 1, '2024-12-06 15:35:35'),
(157, 63, 'False', 0, 2, '2024-12-06 15:35:35'),
(158, 65, 'True', 1, 1, '2024-12-06 15:35:35'),
(159, 65, 'False', 0, 2, '2024-12-06 15:35:35'),
(160, 73, 'True', 1, 1, '2024-12-06 15:35:35'),
(161, 73, 'False', 0, 2, '2024-12-06 15:35:35'),
(162, 75, 'True', 0, 1, '2024-12-06 15:35:35'),
(163, 75, 'False', 1, 2, '2024-12-06 15:35:35'),
(166, 90, 'a', 1, 1, '2024-12-06 15:49:57'),
(167, 90, 'b', 0, 2, '2024-12-06 15:49:57'),
(168, 91, 'A', 1, 1, '2024-12-06 15:58:55'),
(169, 91, 'B', 0, 2, '2024-12-06 15:58:55'),
(170, 92, 'True', 1, 1, '2024-12-06 15:58:55'),
(171, 92, 'False', 0, 2, '2024-12-06 15:58:56'),
(172, 94, 'a', 1, 1, '2024-12-06 15:58:56'),
(173, 94, 'b', 0, 2, '2024-12-06 15:58:56'),
(174, 95, 'A', 1, 1, '2024-12-06 17:27:30'),
(175, 95, 'B', 0, 2, '2024-12-06 17:27:30'),
(176, 96, 'True', 1, 1, '2024-12-06 17:27:30'),
(177, 96, 'False', 0, 2, '2024-12-06 17:27:30'),
(178, 98, 'A', 1, 1, '2024-12-06 17:47:47'),
(179, 98, 'B', 0, 2, '2024-12-06 17:47:47'),
(180, 99, 'True', 1, 1, '2024-12-06 17:47:47'),
(181, 99, 'False', 0, 2, '2024-12-06 17:47:47'),
(182, 101, 'a', 1, 1, '2024-12-06 17:47:47'),
(183, 101, 'b', 0, 2, '2024-12-06 17:47:47'),
(184, 102, 'True', 1, 1, '2024-12-06 17:47:47'),
(185, 102, 'False', 0, 2, '2024-12-06 17:47:47'),
(186, 104, 'a', 1, 1, '2024-12-06 18:41:43'),
(187, 104, 'b', 0, 2, '2024-12-06 18:41:43'),
(188, 105, 'True', 1, 1, '2024-12-06 18:41:43'),
(189, 105, 'False', 0, 2, '2024-12-06 18:41:43'),
(190, 107, 'a', 1, 1, '2024-12-06 18:41:43'),
(191, 107, 'b', 0, 2, '2024-12-06 18:41:43'),
(192, 108, 'yt', 1, 1, '2024-12-07 00:09:51'),
(193, 108, 'test', 0, 2, '2024-12-07 00:09:51'),
(194, 109, 'True', 1, 1, '2024-12-07 00:09:51'),
(195, 109, 'False', 0, 2, '2024-12-07 00:09:51'),
(196, 110, 'a', 1, 1, '2024-12-07 00:09:51'),
(197, 110, 'b', 0, 2, '2024-12-07 00:09:51'),
(198, 113, 'a', 0, 1, '2024-12-09 01:37:51'),
(199, 113, 'b', 0, 2, '2024-12-09 01:37:51'),
(200, 113, 'c', 1, 3, '2024-12-09 01:37:51'),
(201, 113, 'd', 0, 4, '2024-12-09 01:37:51'),
(202, 114, 'True', 1, 1, '2024-12-09 01:37:51'),
(203, 114, 'False', 0, 2, '2024-12-09 01:37:51'),
(204, 118, 'a', 1, 1, '2024-12-09 01:37:51'),
(205, 118, 'b', 0, 2, '2024-12-09 01:37:51'),
(206, 118, 'c', 0, 3, '2024-12-09 01:37:51'),
(207, 118, 'd', 0, 4, '2024-12-09 01:37:51'),
(208, 119, 'True', 0, 1, '2024-12-09 01:37:51'),
(209, 119, 'False', 1, 2, '2024-12-09 01:37:51'),
(210, 120, 'Oo', 1, 1, '2024-12-09 01:44:42'),
(211, 120, 'Hindi', 0, 2, '2024-12-09 01:44:42'),
(212, 120, 'Wag', 0, 3, '2024-12-09 01:44:42'),
(213, 120, 'check', 0, 4, '2024-12-09 01:44:42'),
(214, 121, 'True', 0, 1, '2024-12-09 01:44:42'),
(215, 121, 'False', 1, 2, '2024-12-09 01:44:42'),
(216, 123, 'a', 1, 1, '2024-12-09 02:03:38'),
(217, 123, 'b', 0, 2, '2024-12-09 02:03:38'),
(218, 123, 'c', 0, 3, '2024-12-09 02:03:38'),
(219, 123, 'd', 0, 4, '2024-12-09 02:03:38'),
(220, 124, 'True', 1, 1, '2024-12-09 02:03:38'),
(221, 124, 'False', 0, 2, '2024-12-09 02:03:38'),
(222, 126, 'a', 0, 1, '2024-12-09 02:13:09'),
(223, 126, 'b', 0, 2, '2024-12-09 02:13:09'),
(224, 126, 'c', 1, 3, '2024-12-09 02:13:09'),
(225, 126, 'd', 0, 4, '2024-12-09 02:13:09'),
(226, 127, 'True', 0, 1, '2024-12-09 02:13:09'),
(227, 127, 'False', 1, 2, '2024-12-09 02:13:09'),
(228, 129, 'Hindi', 1, 1, '2024-12-09 11:15:10'),
(229, 129, 'Oo', 0, 2, '2024-12-09 11:15:10'),
(230, 130, 'True', 0, 1, '2024-12-09 11:15:10'),
(231, 130, 'False', 1, 2, '2024-12-09 11:15:10'),
(242, 136, 'A) Structured Query Language', 1, 1, '2024-12-09 16:19:05'),
(243, 136, 'B) Simple Query Language', 0, 2, '2024-12-09 16:19:05'),
(244, 136, 'C) Standard Query Language', 0, 3, '2024-12-09 16:19:05'),
(245, 136, 'D) Sequential Query Language', 0, 4, '2024-12-09 16:19:05'),
(246, 138, 'qweqwe', 0, 1, '2024-12-12 12:04:45'),
(247, 138, 'qewe', 1, 2, '2024-12-12 12:04:45'),
(248, 139, 'True', 1, 1, '2024-12-12 12:04:45'),
(249, 139, 'False', 0, 2, '2024-12-12 12:04:45'),
(250, 141, 'eew', 1, 1, '2024-12-12 12:10:28'),
(251, 141, 'eeew', 0, 2, '2024-12-12 12:10:28'),
(252, 142, 'qwes', 1, 1, '2024-12-12 12:13:04'),
(253, 142, 'sd1', 0, 2, '2024-12-12 12:13:04'),
(254, 143, 'asd', 0, 1, '2024-12-12 12:17:31'),
(255, 143, 'sd', 1, 2, '2024-12-12 12:17:31'),
(256, 144, 'a', 0, 1, '2024-12-12 16:23:02'),
(257, 144, 'b', 0, 2, '2024-12-12 16:23:02'),
(258, 144, 'c', 0, 3, '2024-12-12 16:23:02'),
(259, 144, 'sd', 1, 4, '2024-12-12 16:23:02'),
(260, 145, 'True', 0, 1, '2024-12-12 16:23:02'),
(261, 145, 'False', 1, 2, '2024-12-12 16:23:02'),
(288, 157, 'yes', 1, 1, '2024-12-16 23:30:14'),
(289, 157, 'no', 0, 2, '2024-12-16 23:30:14'),
(290, 157, 'test', 0, 3, '2024-12-16 23:30:14'),
(291, 158, 'True', 1, 1, '2024-12-16 23:30:14'),
(292, 158, 'False', 0, 2, '2024-12-16 23:30:14'),
(293, 168, 'True', 1, 1, '2024-12-17 01:58:25'),
(294, 168, 'False', 0, 2, '2024-12-17 01:58:25'),
(295, 169, 'a', 0, 1, '2024-12-17 01:58:25'),
(296, 169, 'b', 0, 2, '2024-12-17 01:58:25'),
(297, 169, 'c', 1, 3, '2024-12-17 01:58:25');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_access_codes`
--

CREATE TABLE `quiz_access_codes` (
  `id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `access_code` varchar(10) NOT NULL,
  `valid_from` timestamp NOT NULL DEFAULT current_timestamp(),
  `valid_until` timestamp NULL DEFAULT NULL,
  `max_attempts` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quiz_answers`
--

CREATE TABLE `quiz_answers` (
  `answer_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer_text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz_answers`
--

INSERT INTO `quiz_answers` (`answer_id`, `question_id`, `answer_text`) VALUES
(1, 52, 'test'),
(2, 61, 'test'),
(40, 86, 'a                            \n                                \n                                b'),
(41, 87, 'a                            \n                                \n                                b'),
(42, 89, 'a                            \n                                \n                                b'),
(43, 64, 'asd'),
(44, 66, 'asd'),
(45, 72, 'a                            \n                                \n                                b'),
(46, 74, 'asd'),
(47, 88, 'a                            \n                                \n                                b'),
(48, 93, 'asd'),
(49, 97, 'asd'),
(50, 100, 'asd'),
(51, 103, 'asd'),
(52, 106, 'asd'),
(53, 111, 'asd'),
(54, 112, 'asd'),
(55, 115, 'asd'),
(56, 116, '2'),
(57, 117, 'bdrtdfg'),
(58, 122, 'malaki'),
(59, 125, 'francis'),
(60, 128, 'asd'),
(61, 131, 'Oo'),
(62, 134, '4'),
(63, 135, 'test1'),
(64, 140, 'weeee'),
(65, 146, 'what'),
(66, 147, 'cvfd'),
(67, 159, 'yes'),
(68, 160, 'Gamelan'),
(69, 161, 'Eagle'),
(70, 162, 'Indonesia'),
(71, 163, 'Binanog'),
(72, 164, 'Chordophones'),
(73, 165, 'yes'),
(74, 166, 'no'),
(75, 167, 'yes'),
(76, 170, 'test1');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_attempts`
--

CREATE TABLE `quiz_attempts` (
  `attempt_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `start_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `end_time` timestamp NULL DEFAULT NULL,
  `score` decimal(5,2) DEFAULT 0.00,
  `status` enum('in_progress','completed','abandoned') DEFAULT 'in_progress',
  `attempt_number` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz_attempts`
--

INSERT INTO `quiz_attempts` (`attempt_id`, `student_id`, `quiz_id`, `start_time`, `end_time`, `score`, `status`, `attempt_number`, `created_at`) VALUES
(34, 37, 113, '2024-12-04 00:43:07', NULL, 0.00, 'in_progress', 1, '2024-12-04 00:43:07'),
(35, 50, 113, '2024-12-04 01:32:11', NULL, 0.00, 'in_progress', 1, '2024-12-04 01:32:11'),
(36, 50, 124, '2024-12-04 01:51:17', NULL, 0.00, 'in_progress', 1, '2024-12-04 01:51:17'),
(37, 50, 116, '2024-12-04 01:53:39', NULL, 0.00, 'in_progress', 1, '2024-12-04 01:53:39'),
(38, 50, 125, '2024-12-06 03:41:34', NULL, 0.00, 'in_progress', 1, '2024-12-06 03:40:39');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_questions`
--

CREATE TABLE `quiz_questions` (
  `question_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `question_type` enum('multiple_choice','true_false','short_answer') NOT NULL,
  `points` int(11) DEFAULT 1,
  `question_order` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz_questions`
--

INSERT INTO `quiz_questions` (`question_id`, `quiz_id`, `question_text`, `question_type`, `points`, `question_order`, `created_at`, `image_path`) VALUES
(25, 113, 'asd', 'multiple_choice', 1, 1, '2024-12-04 00:24:56', NULL),
(26, 113, 's', 'true_false', 1, 2, '2024-12-04 00:24:56', NULL),
(27, 113, 'gawd', 'short_answer', 1, 3, '2024-12-04 00:24:56', NULL),
(28, 114, 'WQE', 'multiple_choice', 1, 1, '2024-12-04 01:39:32', NULL),
(29, 114, 'QWE', 'true_false', 1, 2, '2024-12-04 01:39:32', NULL),
(30, 114, 'A', 'short_answer', 1, 3, '2024-12-04 01:39:32', NULL),
(31, 115, 'q', 'multiple_choice', 1, 1, '2024-12-04 01:39:57', NULL),
(32, 115, 'a', 'true_false', 1, 2, '2024-12-04 01:39:57', NULL),
(33, 115, 'qw', 'short_answer', 1, 3, '2024-12-04 01:39:58', NULL),
(34, 116, 'a', 'multiple_choice', 1, 1, '2024-12-04 01:42:03', NULL),
(35, 116, 'a', 'true_false', 1, 2, '2024-12-04 01:42:03', NULL),
(36, 116, 'a', 'short_answer', 1, 3, '2024-12-04 01:42:03', NULL),
(37, 124, 'a', 'multiple_choice', 1, 1, '2024-12-04 01:51:08', NULL),
(38, 124, 'a', 'true_false', 1, 2, '2024-12-04 01:51:09', NULL),
(39, 124, 'asd', 'short_answer', 1, 3, '2024-12-04 01:51:09', NULL),
(40, 125, 'test1', 'multiple_choice', 1, 1, '2024-12-06 03:39:52', NULL),
(41, 125, 'testss', 'true_false', 1, 2, '2024-12-06 03:39:52', NULL),
(42, 125, 'test', 'short_answer', 1, 3, '2024-12-06 03:39:52', NULL),
(45, 128, 'test', 'multiple_choice', 1, 1, '2024-12-06 05:06:12', NULL),
(46, 128, 'test', 'true_false', 1, 2, '2024-12-06 05:06:12', NULL),
(47, 128, 'test', 'short_answer', 1, 3, '2024-12-06 05:06:12', NULL),
(50, 131, 'test', 'multiple_choice', 1, 1, '2024-12-06 06:03:12', NULL),
(51, 131, 'test', 'true_false', 1, 2, '2024-12-06 06:03:12', NULL),
(52, 131, 'test', 'short_answer', 1, 3, '2024-12-06 06:03:12', NULL),
(60, 135, 'test', 'true_false', 1, 2, '2024-12-06 06:07:43', NULL),
(61, 135, 'test', 'short_answer', 1, 3, '2024-12-06 06:07:43', NULL),
(63, 136, '', 'true_false', 1, 2, '2024-12-06 07:24:57', NULL),
(64, 136, '', 'short_answer', 1, 3, '2024-12-06 07:24:57', NULL),
(65, 136, '', 'true_false', 1, 4, '2024-12-06 07:24:57', NULL),
(66, 136, '', 'short_answer', 1, 5, '2024-12-06 07:24:57', NULL),
(72, 136, '', 'short_answer', 1, 6, '2024-12-06 07:46:35', NULL),
(73, 136, '', 'true_false', 1, 7, '2024-12-06 07:49:32', NULL),
(74, 136, '', 'short_answer', 1, 8, '2024-12-06 07:49:41', NULL),
(75, 136, '', 'true_false', 1, 9, '2024-12-06 08:30:23', NULL),
(86, 136, '', 'short_answer', 1, 0, '2024-12-06 08:45:56', NULL),
(87, 136, '', 'short_answer', 1, 0, '2024-12-06 08:46:06', NULL),
(88, 137, '', 'short_answer', 1, 1, '2024-12-06 15:21:04', NULL),
(89, 136, '', 'short_answer', 1, 0, '2024-12-06 15:35:27', NULL),
(90, 137, 'testaasdasd', 'multiple_choice', 1, 0, '2024-12-06 15:38:34', NULL),
(91, 138, 'ASD', 'multiple_choice', 1, 1, '2024-12-06 15:58:55', NULL),
(92, 138, 'ASD', 'true_false', 1, 2, '2024-12-06 15:58:55', NULL),
(93, 138, 'asd', 'short_answer', 1, 3, '2024-12-06 15:58:56', NULL),
(94, 138, 'asd', 'multiple_choice', 1, 4, '2024-12-06 15:58:56', NULL),
(95, 139, 'ASD', 'multiple_choice', 1, 1, '2024-12-06 17:27:30', NULL),
(96, 139, 'asd', 'true_false', 1, 2, '2024-12-06 17:27:30', NULL),
(97, 139, 'asd', 'short_answer', 1, 3, '2024-12-06 17:27:30', NULL),
(98, 140, 'ASD', 'multiple_choice', 1, 1, '2024-12-06 17:47:47', NULL),
(99, 140, 'TEST', 'true_false', 1, 2, '2024-12-06 17:47:47', NULL),
(100, 140, 'TEST', 'short_answer', 1, 3, '2024-12-06 17:47:47', NULL),
(101, 140, 'asd', 'multiple_choice', 1, 4, '2024-12-06 17:47:47', NULL),
(102, 140, 'asd', 'true_false', 1, 5, '2024-12-06 17:47:47', NULL),
(103, 140, 'asd', 'short_answer', 1, 6, '2024-12-06 17:47:47', NULL),
(104, 141, 'ASD', 'multiple_choice', 1, 1, '2024-12-06 18:41:43', NULL),
(105, 141, 'asd', 'true_false', 1, 2, '2024-12-06 18:41:43', NULL),
(106, 141, 'asd', 'short_answer', 1, 3, '2024-12-06 18:41:43', NULL),
(107, 141, 'asd', 'multiple_choice', 1, 4, '2024-12-06 18:41:43', NULL),
(108, 142, 'asd', 'multiple_choice', 1, 1, '2024-12-07 00:09:50', NULL),
(109, 142, 'test', 'true_false', 1, 2, '2024-12-07 00:09:51', NULL),
(110, 142, 'test', 'multiple_choice', 1, 3, '2024-12-07 00:09:51', NULL),
(111, 142, 'test', 'short_answer', 1, 4, '2024-12-07 00:09:51', NULL),
(112, 142, 'test', 'short_answer', 1, 5, '2024-12-07 00:09:51', NULL),
(113, 146, 'test', 'multiple_choice', 1, 1, '2024-12-09 01:37:51', NULL),
(114, 146, 'is that true?', 'true_false', 1, 2, '2024-12-09 01:37:51', NULL),
(115, 146, 'test1', 'short_answer', 1, 3, '2024-12-09 01:37:51', NULL),
(116, 146, 'test2 1+1', 'short_answer', 1, 4, '2024-12-09 01:37:51', NULL),
(117, 146, 'test3', 'short_answer', 1, 5, '2024-12-09 01:37:51', NULL),
(118, 146, 'tes', 'multiple_choice', 1, 6, '2024-12-09 01:37:51', NULL),
(119, 146, 'hehe', 'true_false', 1, 7, '2024-12-09 01:37:51', NULL),
(120, 147, 'Matutulog na', 'multiple_choice', 2, 1, '2024-12-09 01:44:42', NULL),
(121, 147, 'Pogi ba ako', 'true_false', 2, 2, '2024-12-09 01:44:42', NULL),
(122, 147, 'Gaano kalaki yung ulo ko', 'short_answer', 6, 3, '2024-12-09 01:44:42', NULL),
(123, 148, 'test', 'multiple_choice', 2, 1, '2024-12-09 02:03:38', NULL),
(124, 148, 'test', 'true_false', 1, 2, '2024-12-09 02:03:38', NULL),
(125, 148, 'give my name', 'short_answer', 7, 3, '2024-12-09 02:03:38', NULL),
(126, 149, 'test', 'multiple_choice', 1, 1, '2024-12-09 02:13:09', NULL),
(127, 149, 'test', 'true_false', 1, 2, '2024-12-09 02:13:09', NULL),
(128, 149, 'asd', 'short_answer', 1, 3, '2024-12-09 02:13:09', NULL),
(129, 150, 'Papasok ka ba', 'multiple_choice', 1, 1, '2024-12-09 11:15:10', NULL),
(130, 150, 'Lalaki ka ba', 'true_false', 1, 2, '2024-12-09 11:15:10', NULL),
(131, 150, 'Lalaki ka ba', 'short_answer', 1, 3, '2024-12-09 11:15:10', NULL),
(132, 151, '', '', NULL, 1, '2024-12-09 12:56:56', NULL),
(133, 151, '', '', NULL, 2, '2024-12-09 12:56:56', NULL),
(134, 151, '', '', NULL, 3, '2024-12-09 12:56:56', NULL),
(135, 151, '', '', NULL, 4, '2024-12-09 12:56:56', NULL),
(136, 154, 'What does SQL stand for?', 'multiple_choice', 1, 1, '2024-12-09 16:13:59', NULL),
(138, 158, 'qweqwe', 'multiple_choice', 1, 1, '2024-12-12 12:04:45', NULL),
(139, 158, 'test', 'true_false', 1, 2, '2024-12-12 12:04:45', NULL),
(140, 158, 'ewewe', 'short_answer', 1, 3, '2024-12-12 12:04:45', NULL),
(141, 159, 'ee', 'multiple_choice', 1, 1, '2024-12-12 12:10:28', NULL),
(142, 160, 'qweqwe', 'multiple_choice', 1, 0, '2024-12-12 12:13:04', 'uploads/quiz_images/675ad34b6a4b5_qweqweqwe.png'),
(143, 160, 'asea', 'multiple_choice', 1, 0, '2024-12-12 12:17:31', NULL),
(144, 162, 'tes', 'multiple_choice', 1, 0, '2024-12-12 16:23:02', 'uploads/quiz_images/675b0dc98520a_ERD (1).jpg'),
(145, 162, 'tes', 'true_false', 1, 1, '2024-12-12 16:23:02', 'uploads/quiz_images/675b0dd88a566_LMS_ERD.jpg'),
(146, 162, 'what', 'short_answer', 1, 2, '2024-12-12 16:23:02', 'uploads/quiz_images/675b0de3db3b7_462563642_933458851632368_1518574745069213287_n (1).png'),
(147, 167, '', '', NULL, 0, '2024-12-15 05:20:44', NULL),
(151, 168, '', '', NULL, 0, '2024-12-15 05:29:50', NULL),
(154, 168, '', '', NULL, 0, '2024-12-15 05:33:09', NULL),
(155, 168, '', '', NULL, 0, '2024-12-15 05:33:49', NULL),
(156, 167, '', '', NULL, 0, '2024-12-15 05:35:55', NULL),
(157, 170, 'example', 'multiple_choice', 1, 0, '2024-12-16 23:30:14', 'uploads/quiz_images/6760b7a126299_RobloxScreenShot20241031_203246240.png'),
(158, 170, 'test', 'true_false', 1, 1, '2024-12-16 23:30:14', NULL),
(159, 170, 'tes', 'short_answer', 1, 2, '2024-12-16 23:30:14', NULL),
(160, 172, 'An orchestra made up of a set of instruments from Java/Bali Indonesia.', 'short_answer', 1, 0, '2024-12-16 23:47:42', NULL),
(161, 172, 'What movement of an animal was featured in the dance binanog?', 'short_answer', 1, 1, '2024-12-16 23:47:42', NULL),
(162, 172, 'It is where gamelan is originated.', 'short_answer', 1, 2, '2024-12-16 23:47:42', NULL),
(163, 172, 'An indigenous dance that comes from Panay Bukidnon indigenous community.', 'short_answer', 1, 3, '2024-12-16 23:47:42', NULL),
(164, 172, 'It is the classification of instruments that is composed of string instruments.', 'short_answer', 1, 4, '2024-12-16 23:47:42', NULL),
(165, 173, 'Test', 'short_answer', 1, 0, '2024-12-17 00:19:22', NULL),
(166, 173, 'test', 'short_answer', 1, 1, '2024-12-17 00:19:22', NULL),
(167, 174, 'yes', 'short_answer', 1, 0, '2024-12-17 01:58:25', 'uploads/quiz_images/6760da9fb9e13_a4bbdec9-7cd3-4258-880f-15a0301172f4.jpg'),
(168, 174, 'tes', 'true_false', 1, 1, '2024-12-17 01:58:25', 'uploads/quiz_images/6760daa73526e_a4bbdec9-7cd3-4258-880f-15a0301172f4.jpg'),
(169, 174, 'test', 'multiple_choice', 1, 2, '2024-12-17 01:58:25', 'uploads/quiz_images/6760dab35f363_3385c3ad-8f04-4c52-bead-da8d6f1dcd7c.jpg'),
(170, 174, 'test1', 'short_answer', 1, 3, '2024-12-17 01:58:25', 'uploads/quiz_images/6760dab9413a5_a4bbdec9-7cd3-4258-880f-15a0301172f4.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `remember_tokens`
--

CREATE TABLE `remember_tokens` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expiry` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `report_history`
--

CREATE TABLE `report_history` (
  `report_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `report_type` varchar(50) NOT NULL,
  `format` varchar(10) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `generated_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `section_id` int(11) NOT NULL,
  `section_name` varchar(50) NOT NULL,
  `grade_level` enum('7','8','9','10') NOT NULL,
  `adviser_id` int(11) DEFAULT NULL,
  `school_year` varchar(9) NOT NULL,
  `status` enum('active','inactive','archived') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`section_id`, `section_name`, `grade_level`, `adviser_id`, `school_year`, `status`, `created_at`) VALUES
(20, 'Ruby', '7', 9, '2024-2025', 'active', '2024-11-23 16:30:56'),
(21, 'Ice', '8', 127, '2024-2025', 'active', '2024-11-24 19:43:42'),
(25, 'Daffodil', '7', 126, '2024-2025', 'active', '2024-12-11 15:37:02');

-- --------------------------------------------------------

--
-- Table structure for table `section_advisers`
--

CREATE TABLE `section_advisers` (
  `id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `academic_year_id` int(11) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `section_schedules`
--

CREATE TABLE `section_schedules` (
  `id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `academic_year_id` int(11) NOT NULL,
  `schedule_day` enum('Monday','Tuesday','Wednesday','Thursday','Friday') NOT NULL,
  `schedule_time` time NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `section_subjects`
--

CREATE TABLE `section_subjects` (
  `id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `academic_year_id` int(11) NOT NULL,
  `schedule_day` enum('Monday','Tuesday','Wednesday','Thursday','Friday') NOT NULL,
  `schedule_time` time NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `enrollment_code` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `section_subjects`
--

INSERT INTO `section_subjects` (`id`, `section_id`, `subject_id`, `teacher_id`, `academic_year_id`, `schedule_day`, `schedule_time`, `status`, `created_at`, `enrollment_code`) VALUES
(46, 20, 21, 9, 1, 'Tuesday', '03:00:00', 'inactive', '2024-11-23 16:34:07', 'CMRH1047'),
(52, 21, 21, 9, 1, 'Monday', '00:00:00', 'inactive', '2024-11-24 19:43:50', 'CMRH1662'),
(53, 20, 22, 123, 1, 'Monday', '00:00:00', 'inactive', '2024-12-01 12:29:42', NULL),
(54, 21, 23, 124, 1, 'Monday', '00:00:00', 'inactive', '2024-12-01 12:29:54', 'CMRH7392'),
(55, 21, 24, 125, 1, 'Monday', '00:00:00', 'inactive', '2024-12-01 12:30:12', NULL),
(56, 20, 23, 9, 1, 'Monday', '21:00:00', 'inactive', '2024-12-03 20:09:14', 'CMRH7513'),
(57, 25, 23, 126, 1, 'Monday', '06:00:00', 'active', '2024-12-11 15:37:21', 'CMRH2351'),
(58, 20, 23, 127, 1, 'Tuesday', '07:43:00', 'inactive', '2024-12-11 23:43:15', 'CMRH5968'),
(59, 25, 24, 123, 1, 'Monday', '12:09:00', '', '2024-12-15 04:06:08', NULL),
(60, 20, 24, 127, 1, 'Wednesday', '13:10:00', 'inactive', '2024-12-15 04:09:14', NULL),
(61, 21, 24, 127, 1, 'Wednesday', '13:10:00', '', '2024-12-15 04:17:32', NULL),
(62, 21, 24, 128, 1, 'Wednesday', '14:30:00', 'active', '2024-12-15 04:22:55', 'CMRH1641'),
(63, 25, 23, 124, 1, 'Monday', '00:00:00', 'active', '2024-12-15 04:39:38', NULL),
(64, 21, 23, 9, 1, 'Monday', '21:00:00', 'inactive', '2024-12-15 04:40:13', NULL),
(65, 25, 24, 125, 1, 'Monday', '00:00:00', 'active', '2024-12-15 04:40:29', NULL),
(66, 21, 25, 129, 1, 'Thursday', '07:30:00', 'active', '2024-12-15 04:59:30', 'CMRH4261'),
(67, 25, 23, 9, 1, 'Monday', '21:00:00', 'active', '2024-12-15 04:59:56', NULL),
(68, 25, 25, 130, 1, 'Monday', '06:40:00', 'active', '2024-12-16 23:25:20', 'CMRH2241');

-- --------------------------------------------------------

--
-- Table structure for table `security_violations`
--

CREATE TABLE `security_violations` (
  `id` int(11) NOT NULL,
  `student_id` bigint(20) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `violation_type` enum('tab_switch','fullscreen_exit') NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `security_violations`
--

INSERT INTO `security_violations` (`id`, `student_id`, `quiz_id`, `violation_type`, `details`, `created_at`) VALUES
(147, 50, 113, 'fullscreen_exit', 'Student attempted to fullscreen exit', '2024-12-04 01:32:21'),
(148, 50, 113, 'fullscreen_exit', 'Student attempted to fullscreen exit', '2024-12-04 01:32:24'),
(149, 50, 113, '', 'Student attempted to page leave', '2024-12-04 01:32:25'),
(150, 50, 113, 'fullscreen_exit', 'Student attempted to fullscreen exit', '2024-12-04 01:32:26'),
(151, 50, 124, '', 'Student attempted to page leave', '2024-12-04 01:51:23'),
(152, 50, 124, 'fullscreen_exit', 'Student attempted to fullscreen exit', '2024-12-04 01:51:24'),
(153, 50, 116, 'fullscreen_exit', 'Student attempted to fullscreen exit', '2024-12-04 01:53:49'),
(154, 50, 116, 'tab_switch', 'Student attempted to tab switch', '2024-12-04 01:53:54'),
(155, 50, 116, 'fullscreen_exit', 'Student attempted to fullscreen exit', '2024-12-04 01:54:01'),
(156, 50, 116, '', 'Student attempted to page leave', '2024-12-04 01:54:01'),
(157, 50, 125, '', 'Student attempted to page leave', '2024-12-06 03:40:46'),
(158, 50, 125, 'fullscreen_exit', 'Student attempted to fullscreen exit', '2024-12-06 03:40:47'),
(159, 50, 125, 'fullscreen_exit', 'Student attempted to fullscreen exit', '2024-12-06 03:41:37'),
(160, 50, 125, 'tab_switch', 'Student attempted to tab switch', '2024-12-06 03:41:59');

-- --------------------------------------------------------

--
-- Table structure for table `site_map_content`
--

CREATE TABLE `site_map_content` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `student_id` bigint(20) NOT NULL,
  `lrn` varchar(12) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `middlename` varchar(100) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `cys` varchar(50) NOT NULL,
  `status` enum('active','archived') DEFAULT 'active',
  `login_attempts` int(11) DEFAULT 0,
  `lockout_until` datetime DEFAULT NULL,
  `user_online` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_activity` bigint(20) DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `last_login_attempt` timestamp NULL DEFAULT NULL,
  `password_recovery` enum('yes','no') DEFAULT 'no'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`student_id`, `lrn`, `password`, `email`, `contact_number`, `gender`, `birthdate`, `firstname`, `lastname`, `middlename`, `profile_image`, `cys`, `status`, `login_attempts`, `lockout_until`, `user_online`, `created_at`, `updated_at`, `last_activity`, `session_id`, `last_login_attempt`, `password_recovery`) VALUES
(38, '123456789999', '469e7e66cff79f931488a5feee1909e6', 'christian@frncszxc.helioho.st', '09701333333', 'Male', NULL, 'Edrian', 'Pacifico', 'Ilagan', NULL, '', 'active', 0, NULL, 0, '2024-11-24 20:45:19', '2024-12-11 17:28:56', NULL, NULL, NULL, 'no'),
(39, '100000999999', 'f8c7c5d26055adb57617e687dbf6811c', 'eleanorpacifico@gmail.com', '09701640382', 'Male', NULL, 'Christopher', 'Pacifico', 'Ilagan', NULL, '', 'active', 0, NULL, 0, '2024-11-24 22:48:34', '2024-12-11 17:18:19', NULL, NULL, NULL, 'no'),
(40, '123456789111', '9acb31bd27f2bb425f0b07c9b5322ed3', 'student1@gmail.com', '09504222701', 'Male', NULL, 'student1', 'student1', 'student1', NULL, '', 'active', 0, NULL, 0, '2024-12-01 13:36:59', '2024-12-11 17:28:55', NULL, NULL, NULL, 'no'),
(50, '999999999999', '8ccb29db1ea08e210d6d54002ada3c23', 'dashotz14@gmail.com', '09208040444', 'Male', '1940-12-13', 'Francis', 'Cruz', '', '../uploads/students/profile/student_50_1734021981.jpg', '', 'active', 0, NULL, 1, '2024-12-04 01:16:52', '2024-12-17 00:19:27', 20241217001927, 'uk8gi79a335afoh4aqf8tb0gir', NULL, 'no'),
(222, '103255555555', '1d42da412bdfd380d8b5b9725fc98f1b', 'thelming@gmail.com', '09167328478', 'Female', NULL, 'Thel', 'Badion', 'Joy', NULL, '', 'active', 0, NULL, 0, '2024-12-11 23:31:04', '2024-12-16 23:52:46', NULL, NULL, NULL, 'no'),
(223, '000000000000', '839e25a7fbbe4764b993e10de59a4aa8', 'christianpacifico20@gmail.com', '09701640382', 'Male', '2002-06-04', 'Christian', 'Pacifico', 'Ilagan', '../uploads/students/profile/student_223_1734022150.jpg', '', 'active', 0, NULL, 0, '2024-12-12 15:46:32', '2024-12-12 16:49:29', NULL, NULL, NULL, 'no'),
(224, '402176160002', '7e73346b1abddfd252afe88c046c2e98', 'JANGELES@gmail.com', '09123456789', 'Male', NULL, 'JENINO ABRIEL', 'ANGELES', 'CAGUIAT', NULL, '', 'active', 0, NULL, 1, '2024-12-12 16:30:01', '2024-12-17 00:20:30', 20241217002030, 'ou9ip5to4rb2a91196nj3c0rbe', NULL, 'no'),
(225, '107984170001', 'c65b12a3c86d360244ea62b61d4bd4fe', 'DAREVALO@gmail.com', '09987654321', 'Male', '2012-02-12', 'DJHON DANIEL', 'AREVALO', 'MALIHAN', '../uploads/students/profile/student_225_1734393800.jpg', '', 'active', 0, NULL, 0, '2024-12-12 16:30:01', '2024-12-17 00:19:53', NULL, NULL, NULL, 'no'),
(226, '107973170036', '927cf829eb2dd7fc2e7f66280c6dad12', 'IBERNARDO@gmail.com', '09640479631', 'Male', '2012-07-16', 'ILDRED DOMINIC', 'BERNARDO', 'SAULOG', '../uploads/students/profile/student_226_1734394157.jpeg', '', 'active', 0, NULL, 1, '2024-12-12 16:30:01', '2024-12-17 00:15:41', 20241217001541, 'ra1fqvuu00ih3p8fgqrg12ip8v', NULL, 'no'),
(227, '136524170671', 'e19db788c21416f452ea4978c8ba7817', 'JBULAWIN@gmail.com', '09275742104', 'Male', NULL, 'JAN KYLE', 'BULAWIN', 'MANANGAT', NULL, '', 'active', 0, NULL, 0, '2024-12-12 16:30:01', '2024-12-12 16:30:01', NULL, NULL, NULL, 'no'),
(228, '107973170001', 'de7bdd1f7ab593acf12ca0cdd920610a', 'JCANDELARIA@gmail.com', '09564581319', 'Male', '2012-03-01', 'JOHN ERNEST LOUIE', 'CANDELARIA', 'FESALBON', '../uploads/students/profile/student_228_1734393639.jpg', '', 'active', 0, NULL, 1, '2024-12-12 16:30:01', '2024-12-17 00:18:06', 20241217001806, '7vehvetdrel64k9d55ct7uued8', NULL, 'no'),
(229, '136766170015', 'be5db79548a18da5c582e5484f7ae06b', 'GCONCILLADO@gmail.com', '09569986080', 'Male', NULL, 'GIAN CARLO', 'CONCILLADO', 'MORTEGA', NULL, '', 'active', 0, NULL, 0, '2024-12-12 16:30:01', '2024-12-12 16:30:01', NULL, NULL, NULL, 'no'),
(230, '107985170027', '016421190e279cebbcbe1b3972ee50a7', 'FCRUZ@gmail.com', '09934842631', 'Male', '2011-11-20', 'FRANZ MATEO', 'CRUZ', 'ALICAWAY', '../uploads/students/profile/student_230_1734394059.jpg', '', 'active', 0, NULL, 1, '2024-12-12 16:30:01', '2024-12-17 00:20:49', 20241217002049, 'g5oocgkqulcp19pbcq8ldvlm38', NULL, 'no'),
(231, '164009170016', 'c351ba26e18ff94be1afb717a0466f1b', 'VDE BELEN@gmail.com', '09280500027', 'Male', '2011-09-06', 'VON LAWRENCE', 'DE BELEN', 'CANO', '../uploads/students/profile/student_231_1734393719.jpeg', '', 'active', 0, NULL, 1, '2024-12-12 16:30:01', '2024-12-17 00:13:29', 20241217001329, '06f3lj6829qrfvs2lplb6ttamm', NULL, 'no'),
(232, '107974170319', '05cf50e11e8ed486b02b0e338c3cf3a8', 'JDEL MUNDO@gmail.com', '09503134452', 'Male', '2012-08-07', 'JOSH MATTHEW', 'DEL MUNDO', 'BOLACTIA', '../uploads/students/profile/student_232_1734393521.jpg', '', 'active', 0, NULL, 1, '2024-12-12 16:30:01', '2024-12-17 00:18:45', 20241217001845, 'flhri4uimlq2rnt6s6kkh4hasj', NULL, 'no'),
(233, '107973170039', '97c4e885c5cf91f7057bd4c92776ca40', 'MENRIQUEZ@gmail.com', '09320836740', 'Male', NULL, 'MARWIN JAY', 'ENRIQUEZ', 'AGUILAR', NULL, '', 'active', 0, NULL, 0, '2024-12-12 16:30:01', '2024-12-12 16:30:01', NULL, NULL, NULL, 'no'),
(234, '107985170246', '9770110dbd30ef42625c4e598869d834', 'EFEGASON@gmail.com', '09273458901', 'Male', '2012-01-18', 'EARL MATTHEW', 'FEGASON', 'PEREZ', '../uploads/students/profile/student_234_1734393429.jpg', '', 'active', 0, NULL, 0, '2024-12-12 16:30:01', '2024-12-17 00:04:51', NULL, NULL, NULL, 'no'),
(235, '107985170029', '1977d2009700439b36fcf22bd114b53b', 'WLAUZON@gmail.com', '09171234567', 'Male', NULL, 'WENCY', 'LAUZON', 'AYADE', NULL, '', 'active', 0, NULL, 0, '2024-12-12 16:30:01', '2024-12-12 16:30:01', NULL, NULL, NULL, 'no'),
(236, '107985170031', 'b12ede3f70725abdaa975cb32727f2cd', 'JMONTABON@gmail.com', '09229876543', 'Male', NULL, 'JHOVIN IVAN', 'MONTABON', 'CLARIN', NULL, '', 'active', 0, NULL, 0, '2024-12-12 16:30:01', '2024-12-12 16:30:01', NULL, NULL, NULL, 'no'),
(237, '107985170158', 'cfc572320132d8be943f472eec6b420d', 'ROIDEM@gmail.com', '09351112222', 'Male', NULL, 'RICHARD', 'OIDEM', 'HABITAN', NULL, '', 'active', 0, NULL, 0, '2024-12-12 16:30:01', '2024-12-12 16:30:01', NULL, NULL, NULL, 'no'),
(238, '107985170014', '7d3f8c2ae946606b0af4fb9e90b26161', 'JPARAYNO@gmail.com', '09087654321', 'Male', '2012-01-16', 'JOHN CLARK', 'PARAYNO', 'MELENDREZ', '../uploads/students/profile/student_238_1734394762.jpg', '', 'active', 0, NULL, 1, '2024-12-12 16:30:01', '2024-12-17 00:19:22', 20241217001725, 'da8vtvl2th2nllt9tu5d3cok84', NULL, 'no'),
(239, '107973170008', 'de1e4502a0ddc4e091b214846c135e39', 'BRENO@gmail.com', '09198765432', 'Male', NULL, 'BIEN GABRIEL', 'RENO', 'AMORES', NULL, '', 'active', 0, NULL, 0, '2024-12-12 16:30:01', '2024-12-17 00:19:17', NULL, NULL, NULL, 'no'),
(240, '107979170103', '0233eff5864861b54ec1f2f642aaddcc', 'PSALINAS@gmail.com', '09212345678', 'Male', NULL, 'PRIO LAURENCE', 'SALINAS', 'CAMPAÑA', NULL, '', 'active', 0, NULL, 0, '2024-12-12 16:30:01', '2024-12-17 00:17:20', NULL, NULL, NULL, 'no'),
(241, '107985170127', '61ef1ae560939307ef0d79de378220c3', 'VSANTOS@gmail.com', '09323456789', 'Male', NULL, 'VILFRED IRVHING NATHEAL', 'SANTOS', 'BELLEZA', NULL, '', 'active', 0, NULL, 1, '2024-12-12 16:30:01', '2024-12-17 00:15:17', 20241217001517, 'g5rurbadvu58dfl7hldsg91l9r', NULL, 'no'),
(242, '107985170179', '85b05f16e107b7080c6939cae89d9ac6', 'JSECUSANA@gmail.com', '09091234567', 'Male', NULL, 'JOHN GABRIEL', 'SECUSANA', 'BESA', NULL, '', 'active', 0, NULL, 0, '2024-12-12 16:30:01', '2024-12-12 16:30:01', NULL, NULL, NULL, 'no'),
(243, '107973170045', 'a214e6c83808ee6bf4f989a1741eeba7', 'NTUBIO@gmail.com', '09189876543', 'Male', NULL, 'NINO JESTER', 'TUBIO', 'PAIRA', NULL, '', 'active', 0, NULL, 0, '2024-12-12 16:30:01', '2024-12-12 16:30:01', NULL, NULL, NULL, 'no'),
(244, '107985170137', 'fae8267b3adbcd5f164039f9077ab797', 'KVELUNTA@gmail.com', '09231112222', 'Male', NULL, 'KHYLLE KHOSHINE', 'VELUNTA', 'DICEN', NULL, '', 'active', 0, NULL, 0, '2024-12-12 16:30:01', '2024-12-12 16:30:01', NULL, NULL, NULL, 'no'),
(245, '424289170007', '8db5bafa4d5dbc4539a3b11ee7bf85c8', 'JALARCON@gmail.com', '09367654321', 'Female', '2011-10-31', 'JEREMIAH EZRA', 'ALARCON', '', '../uploads/students/profile/student_245_1734393433.jpg', '', 'active', 0, NULL, 0, '2024-12-12 16:30:01', '2024-12-17 00:17:20', NULL, NULL, NULL, 'no'),
(246, '107985170139', 'cd1b657d2fbee2bab911b506ab1c6b85', 'LALBERTO@gmail.com', '09078765432', 'Female', NULL, 'LORAINE', 'ALBERTO', 'CUBACUB', NULL, '', 'active', 0, NULL, 0, '2024-12-12 16:30:01', '2024-12-12 16:30:01', NULL, NULL, NULL, 'no'),
(247, '107974170390', 'e963aacd4c9824c03455378bbe6b398e', 'PALCANTARA@gmail.com', '', '', NULL, '', '', '', '../uploads/students/profile/student_247_1734393777.jpg', '', 'active', 0, NULL, 1, '2024-12-12 16:30:01', '2024-12-17 00:13:18', 20241217001318, 'ifnfqefkkjm2flp9637rb07048', NULL, 'no'),
(248, '107985170074', '786879e88bf8596614a58f05b67dd4f0', 'MALVAREZ@gmail.com', '09243456789', 'Female', NULL, 'MARY LORIELYN JOY', 'ALVAREZ', 'GOMEZ', NULL, '', 'active', 0, NULL, 0, '2024-12-12 16:30:01', '2024-12-17 00:13:19', NULL, NULL, NULL, 'no'),
(249, '107974170391', '9ccb9d0f0c996dd27b8353541b3e7f1e', 'AARCA@gmail.com', '09311234567', 'Female', NULL, 'ALYANNA KHANE', 'ARCA', 'LAURENTE', NULL, '', 'active', 0, NULL, 0, '2024-12-12 16:30:01', '2024-12-12 16:30:01', NULL, NULL, NULL, 'no'),
(250, '402112150584', '9ffdf5a76db50cd36881ef2a82fe406a', 'LBERNABAT@gmail.com', '09069876543', 'Female', '2012-05-19', 'LEXI ALEXANDRA', 'BERNABAT', 'ALBITE', '../uploads/students/profile/student_250_1734393756.jpeg', '', 'active', 0, NULL, 1, '2024-12-12 16:30:01', '2024-12-17 00:10:55', 20241217001055, 'ufd9v6k6r2mb520f01h1ru6sb9', NULL, 'no'),
(251, '107990170023', 'e91316fa594fee1f949be7c011c9ed6a', 'DBONIFACIO@gmail.com', '09161112222', 'Female', NULL, 'DHENRIE ROSE', 'BONIFACIO', 'ESPARES', NULL, '', 'active', 0, NULL, 1, '2024-12-12 16:30:01', '2024-12-17 00:11:45', 20241217001145, '7clu7rl7rm4ebap148n4igvjvl', NULL, 'no'),
(252, '424497170005', '237afe15acdccca53e6f037c762d84e7', 'SBUSA@gmail.com', '09257654321', 'Female', '2012-03-28', 'SEF MATTHEA', 'BUSA', 'ESTRADA', '../uploads/students/profile/student_252_1734394121.jpg', '', 'active', 0, NULL, 1, '2024-12-12 16:30:01', '2024-12-17 00:22:34', 20241217002234, 'dka9cvms1e37a3mkcp14ani1du', NULL, 'no'),
(253, '107974170187', '9b42bcbe7349f426a571577c2eb3629c', 'PCAGUIAT@gmail.com', '09378765432', 'Female', '2011-08-30', 'EUGENE', 'CAGUIAT', 'QUIROZ', '../uploads/students/profile/student_253_1734394818.jpg', '', 'active', 0, NULL, 1, '2024-12-12 16:30:01', '2024-12-17 00:20:32', 20241217002032, 'npf3vsgnm87qgdb11kcvv661o2', NULL, 'no'),
(254, '107990170058', 'b665a797cb6c7d82d0ff61ce81494560', 'CCATALAN@gmail.com', '09052345678', 'Female', NULL, 'CYRENE MAE', 'CATALAN', 'BASO', NULL, '', 'active', 3, NULL, 0, '2024-12-12 16:30:01', '2024-12-17 00:19:53', NULL, NULL, NULL, 'no'),
(255, '424353170020', '7fe773d6b78f6ce563147a8b564b195f', 'MDELOS REYES@gmail.com', '09143456789', 'Female', '2012-03-28', 'MIA ANGELA ENNA', 'DELOS REYES', 'CAMAMA', '../uploads/students/profile/student_255_1734395327.jpg', '', 'active', 0, NULL, 1, '2024-12-12 16:30:01', '2024-12-17 00:28:47', 20241217002802, 'hnje4nl7ur935v6taf3s918a0c', NULL, 'no'),
(256, '107973170018', 'afab64cb00d437aa6e4f23492d0abde0', 'ADIALA@gmail.com', '09261234567', 'Female', '2012-04-30', 'ALYANNA JENICA', 'DIALA', 'BARBA', '../uploads/students/profile/student_256_1734393808.jpg', '', 'active', 0, NULL, 1, '2024-12-12 16:30:01', '2024-12-17 00:11:23', 20241217001123, 'kpnkmmca6dvgh97bjqlbtnlhuv', NULL, 'no'),
(257, '107974170089', '0ca7bfc04aca4d2d3200928b68eadb92', 'BFERMA@gmail.com', '09339876543', 'Female', NULL, 'BREANNE', 'FERMA', 'PIMENTEL', NULL, '', 'active', 0, NULL, 1, '2024-12-12 16:30:01', '2024-12-17 00:11:58', 20241217001158, 'osof3hm35ik12uv28vb2vle4bj', NULL, 'no'),
(258, '136711170092', '9e969ca5d94ebfc9c74d89ba8b735e8b', 'KGALUPO@gmail.com', '09041112222', 'Female', '2024-12-17', 'KYLE CHLOE', 'GALUPO', 'SANCHEZ', '../uploads/students/profile/student_258_1734393896.jpg', '', 'active', 0, NULL, 1, '2024-12-12 16:30:01', '2024-12-17 00:20:12', 20241217002012, 'gj8dgp7ciaa58c692ag2kqdn4c', NULL, 'no'),
(259, '107974170437', 'e51d459f476d84464415db320e08f7bd', 'AGARCIA@gmail.com', '09137654321', 'Female', NULL, 'ATHENA KEIGHT', 'GARCIA', 'LABIAN', NULL, '', 'active', 0, NULL, 0, '2024-12-12 16:30:01', '2024-12-17 00:17:20', NULL, NULL, NULL, 'no'),
(260, '107985170088', '385adbfbfdbfe69f17e35b3efff59f4a', 'JLEGASPI@gmail.com', '09278765432', 'Female', NULL, 'JOSELYN', 'LEGASPI', 'LEYSON', NULL, '', 'active', 0, NULL, 0, '2024-12-12 16:30:01', '2024-12-17 00:17:20', NULL, NULL, NULL, 'no'),
(261, '107973170021', 'ffdf497b39c9cbe6b9b19721e8fbe71b', 'LMARTINEZ@gmail.com', '09382345678', 'Female', '2012-08-05', 'LOUISE JANA', 'MARTINEZ', 'MONZON', '../uploads/students/profile/student_261_1734393971.jpg', '', 'active', 0, NULL, 0, '2024-12-12 16:30:01', '2024-12-17 00:17:20', NULL, NULL, NULL, 'no'),
(262, '136791170146', '993900963c860e31c644c9277f95821e', 'GMERENCILLO@gmail.com', '09033456789', 'Female', NULL, 'GAILE RHIAN', 'MERENCILLO', 'OLARTE', NULL, '', 'active', 0, NULL, 0, '2024-12-12 16:30:01', '2024-12-12 16:30:01', NULL, NULL, NULL, 'no'),
(263, '422016160014', 'de9fe20d9295c266994796d773082e5a', 'JRAYMUNDO@gmail.com', '09543888944', 'Female', '2011-02-19', 'Jianne Mei', 'Raymundo', '', '../uploads/students/profile/student_263_1734394742.jpg', '', 'active', 0, NULL, 1, '2024-12-12 16:30:01', '2024-12-17 00:19:25', 20241217001925, '5rtibnl4d91hpp4nk4jr6kmlro', NULL, 'no'),
(264, '107973170070', '79e92621c3ecb3a791e5fdd4ae72eba4', 'JREYEL@gmail.com', '09279293076', 'Female', '2011-10-12', 'JIRSTEN MARNEE', 'REYEL', 'POBLETE', NULL, '', 'active', 0, NULL, 1, '2024-12-12 16:30:01', '2024-12-17 00:10:15', 20241217001015, 'tom3v8hcvss0qp81dgr06o4p8s', NULL, 'no'),
(265, '107973170071', 'de96d397afae9bd4b438a6e35f1147e2', 'JROA@gmail.com', '09341112222', 'Female', NULL, 'JAMAILAH CHLOE', 'ROA', 'CAMAMA', NULL, '', 'active', 0, NULL, 0, '2024-12-12 16:30:01', '2024-12-17 00:19:53', NULL, NULL, NULL, 'no'),
(266, '107985170045', '45c267feffca48f73fc93ad0fe91ed63', 'MSORIANO@gmail.com', '09027654321', 'Female', '2012-04-12', 'HEAVENLY', 'SORIANO', 'ESGUERRA', '../uploads/students/profile/student_266_1734395267.jpeg', '', 'active', 0, NULL, 1, '2024-12-12 16:30:01', '2024-12-17 00:27:47', 20241217001249, '7p5r5jqlkb9bj2b1kb34eaua6j', NULL, 'no'),
(267, '107985170132', 'd104906a2055579049528d56127f2aa3', 'SVILLARANDA@gmail.com', '09118765432', 'Female', '2012-05-21', 'Shiella May', 'Villaranda', 'Leonora', '../uploads/students/profile/student_267_1734394053.jpg', '', 'active', 0, NULL, 1, '2024-12-12 16:30:01', '2024-12-17 00:22:13', 20241217002213, 'f5ho74c9oq7cchdmrucmvnpe3g', NULL, 'no'),
(268, '107985170178', '41833c4c1356ab6f6bac31837b3f9113', 'MVOCIS@gmail.com', '09292345678', 'Female', NULL, 'MERILYN', 'VOCIS', 'CASES', NULL, '', 'active', 0, NULL, 0, '2024-12-12 16:30:01', '2024-12-12 16:30:01', NULL, NULL, NULL, 'no'),
(269, '666666666666', 'aa40a95184dc0f6be62bcd75927bbee1', 'jesicacananayrason@gmail.com', '09304154642', 'Male', NULL, 'HARYETTE', 'RAZON', 'CANAYNAY', NULL, '', 'active', 0, NULL, 0, '2024-12-17 00:05:52', '2024-12-17 00:19:53', NULL, NULL, NULL, 'no');

-- --------------------------------------------------------

--
-- Table structure for table `student_activity_submissions`
--

CREATE TABLE `student_activity_submissions` (
  `submission_id` int(11) NOT NULL,
  `student_id` bigint(20) NOT NULL,
  `activity_id` int(11) NOT NULL,
  `points` int(11) DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','submitted','auto_submitted','graded','missing') NOT NULL DEFAULT 'pending',
  `graded_at` timestamp NULL DEFAULT NULL,
  `graded_by` int(11) DEFAULT NULL,
  `late_submission` tinyint(1) DEFAULT 0,
  `remarks` text DEFAULT NULL,
  `submission_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `security_violation` tinyint(1) DEFAULT 0,
  `violation_type` varchar(255) DEFAULT NULL,
  `time_spent` int(11) DEFAULT NULL COMMENT 'Time spent in seconds',
  `score` int(11) DEFAULT NULL,
  `score_percentage` decimal(5,2) DEFAULT 0.00,
  `total_questions` int(11) DEFAULT 0,
  `answered_questions` int(11) DEFAULT 0,
  `correct_answers` int(11) DEFAULT 0,
  `total_answers` int(11) DEFAULT NULL,
  `result_file` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_activity_submissions`
--

INSERT INTO `student_activity_submissions` (`submission_id`, `student_id`, `activity_id`, `points`, `feedback`, `file_path`, `submitted_at`, `status`, `graded_at`, `graded_by`, `late_submission`, `remarks`, `submission_date`, `created_at`, `updated_at`, `security_violation`, `violation_type`, `time_spent`, `score`, `score_percentage`, `total_questions`, `answered_questions`, `correct_answers`, `total_answers`, `result_file`) VALUES
(85, 50, 125, 0, NULL, NULL, '2024-12-06 05:24:49', 'submitted', NULL, NULL, 0, NULL, '2024-12-06 05:24:49', '2024-12-06 05:24:49', '2024-12-06 05:24:49', 0, NULL, 2, NULL, 0.00, 0, 0, 0, NULL, NULL),
(100, 50, 131, 2, NULL, NULL, '2024-12-06 06:12:37', 'submitted', NULL, NULL, 0, NULL, '2024-12-06 06:12:37', '2024-12-06 06:12:37', '2024-12-06 06:12:37', 0, NULL, 3, NULL, 0.00, 0, 0, 0, NULL, NULL),
(101, 50, 135, 3, NULL, NULL, '2024-12-06 06:12:52', 'submitted', NULL, NULL, 0, NULL, '2024-12-06 06:12:52', '2024-12-06 06:12:52', '2024-12-06 06:12:52', 0, NULL, 2, NULL, 0.00, 0, 0, 0, NULL, NULL),
(104, 50, 137, 1, NULL, NULL, '2024-12-06 15:57:53', 'submitted', NULL, NULL, 0, NULL, '2024-12-06 15:57:53', '2024-12-06 15:57:53', '2024-12-06 15:57:53', 0, NULL, 4, NULL, 0.00, 0, 0, 0, NULL, NULL),
(111, 50, 138, 3, NULL, NULL, '2024-12-06 17:26:46', 'submitted', NULL, NULL, 0, NULL, '2024-12-06 17:26:46', '2024-12-06 17:26:46', '2024-12-06 17:26:46', 0, NULL, 3, NULL, 0.00, 0, 0, 0, NULL, NULL),
(125, 50, 139, 3, NULL, NULL, '2024-12-06 18:34:59', 'submitted', NULL, NULL, 0, NULL, '2024-12-06 18:34:59', '2024-12-06 18:34:59', '2024-12-06 18:34:59', 0, NULL, 3, NULL, 0.00, 0, 0, 0, NULL, NULL),
(148, 50, 141, 4, NULL, NULL, '2024-12-07 00:04:12', 'submitted', NULL, NULL, 0, NULL, '2024-12-07 00:04:12', '2024-12-07 00:04:12', '2024-12-07 00:04:12', 0, NULL, 4, NULL, 0.00, 0, 0, 4, 4, NULL),
(149, 50, 140, 6, NULL, NULL, '2024-12-07 00:06:14', 'submitted', NULL, NULL, 0, NULL, '2024-12-07 00:06:14', '2024-12-07 00:06:14', '2024-12-07 00:06:14', 0, NULL, 10, NULL, 0.00, 0, 0, 6, 6, NULL),
(157, 50, 142, 0, NULL, NULL, '2024-12-07 00:49:06', 'missing', NULL, NULL, 0, NULL, '2024-12-07 00:49:06', '2024-12-07 00:49:06', '2024-12-07 00:49:06', 1, NULL, 13, NULL, 0.00, 0, 0, 0, 5, NULL),
(158, 50, 94, 99, '', NULL, '2024-12-07 00:54:34', 'submitted', '2024-12-07 00:58:34', NULL, 0, NULL, '2024-12-07 00:54:34', '2024-12-07 00:54:34', '2024-12-07 00:58:34', 0, NULL, NULL, NULL, 0.00, 0, 0, 0, NULL, NULL),
(159, 50, 143, 88, 'tes', NULL, '2024-12-07 01:03:05', 'submitted', '2024-12-07 01:03:19', NULL, 0, NULL, '2024-12-07 01:03:05', '2024-12-07 01:03:05', '2024-12-07 01:03:19', 0, NULL, NULL, NULL, 0.00, 0, 0, 0, NULL, NULL),
(165, 50, 145, 89, '', NULL, '2024-12-07 01:18:38', 'submitted', '2024-12-07 01:22:54', NULL, 0, NULL, '2024-12-07 01:18:38', '2024-12-07 01:18:38', '2024-12-07 01:22:54', 0, NULL, NULL, NULL, 0.00, 0, 0, 0, NULL, NULL),
(166, 50, 93, NULL, NULL, NULL, '2024-12-07 01:25:16', 'submitted', NULL, NULL, 0, NULL, '2024-12-07 01:25:16', '2024-12-07 01:25:16', '2024-12-07 01:25:16', 0, NULL, NULL, NULL, 0.00, 0, 0, 0, NULL, NULL),
(169, 50, 147, 0, NULL, NULL, '2024-12-09 01:51:19', 'submitted', NULL, NULL, 0, NULL, '2024-12-09 01:51:19', '2024-12-09 01:51:19', '2024-12-09 01:51:19', 0, NULL, 17, NULL, 0.00, 0, 0, 0, 3, NULL),
(172, 50, 146, 3, NULL, NULL, '2024-12-09 02:12:21', 'submitted', NULL, NULL, 0, NULL, '2024-12-09 02:12:21', '2024-12-09 02:12:21', '2024-12-09 02:12:21', 0, NULL, 14, NULL, 0.00, 0, 0, 3, 7, NULL),
(173, 50, 148, 3, NULL, NULL, '2024-12-09 02:12:44', 'submitted', NULL, NULL, 0, NULL, '2024-12-09 02:12:44', '2024-12-09 02:12:44', '2024-12-09 02:12:44', 0, NULL, 18, NULL, 0.00, 0, 0, 2, 3, NULL),
(174, 50, 149, 0, NULL, NULL, '2024-12-09 02:13:31', 'submitted', NULL, NULL, 0, NULL, '2024-12-09 02:13:31', '2024-12-09 02:13:31', '2024-12-09 02:13:31', 0, NULL, 13, NULL, 0.00, 0, 0, 0, 3, NULL),
(175, 50, 150, 3, NULL, NULL, '2024-12-09 11:15:45', 'submitted', NULL, NULL, 0, NULL, '2024-12-09 11:15:45', '2024-12-09 11:15:45', '2024-12-09 11:15:45', 0, NULL, 29, NULL, 0.00, 0, 0, 3, 3, NULL),
(176, 50, 151, 4, NULL, NULL, '2024-12-09 12:59:53', 'graded', '2024-12-12 02:15:05', 9, 0, NULL, '2024-12-09 12:59:53', '2024-12-09 12:59:53', '2024-12-12 02:15:05', 0, NULL, 18, NULL, 0.00, 0, 0, 3, 4, NULL),
(179, 222, 152, NULL, NULL, NULL, '2024-12-12 00:02:38', 'submitted', NULL, NULL, 0, NULL, '2024-12-12 00:02:38', '2024-12-12 00:02:38', '2024-12-12 00:02:38', 0, NULL, NULL, NULL, 0.00, 0, 0, 0, NULL, NULL),
(180, 50, 152, 100, 'AWet', NULL, '2024-12-12 02:04:02', 'graded', '2024-12-12 12:23:09', 9, 0, NULL, '2024-12-12 02:04:02', '2024-12-12 02:04:02', '2024-12-12 12:23:09', 0, NULL, NULL, NULL, 0.00, 0, 0, 0, NULL, 'uploads/results/675a45dd54897_08_Handout_1(16).pdf'),
(181, 50, 154, 1, NULL, NULL, '2024-12-12 11:49:40', 'graded', '2024-12-12 12:22:43', 9, 0, NULL, '2024-12-12 11:49:40', '2024-12-12 11:49:40', '2024-12-12 12:22:43', 1, NULL, 12, NULL, 0.00, 0, 0, 0, 1, NULL),
(184, 50, 159, 1, NULL, NULL, '2024-12-12 12:10:42', 'graded', '2024-12-15 03:34:50', 127, 0, NULL, '2024-12-12 12:10:42', '2024-12-12 12:10:42', '2024-12-15 03:34:50', 1, NULL, 2, NULL, 0.00, 0, 0, 0, 1, NULL),
(187, 50, 158, 0, NULL, NULL, '2024-12-12 12:33:37', 'missing', NULL, NULL, 0, NULL, '2024-12-12 12:33:37', '2024-12-12 12:33:37', '2024-12-12 12:33:37', 1, NULL, 10, NULL, 0.00, 0, 0, 0, 3, NULL),
(193, 50, 160, 1, NULL, NULL, '2024-12-12 12:46:33', 'submitted', NULL, NULL, 0, NULL, '2024-12-12 12:46:33', '2024-12-12 12:46:33', '2024-12-12 12:46:33', 0, NULL, 30, NULL, 0.00, 0, 0, 1, 2, NULL),
(194, 223, 161, 100, 'test', NULL, '2024-12-12 15:50:05', 'graded', '2024-12-12 15:50:28', 9, 0, NULL, '2024-12-12 15:50:05', '2024-12-12 15:50:05', '2024-12-12 15:50:28', 0, NULL, NULL, NULL, 0.00, 0, 0, 0, NULL, 'uploads/results/675b0644e01fc_05_Handout_1(10).pdf'),
(195, 50, 162, 2, NULL, NULL, '2024-12-12 16:25:09', 'submitted', NULL, NULL, 0, NULL, '2024-12-12 16:25:09', '2024-12-12 16:25:09', '2024-12-12 16:25:09', 0, NULL, 29, NULL, 0.00, 0, 0, 2, 3, NULL),
(196, 50, 155, 80, 'wow', NULL, '2024-12-12 16:27:15', 'graded', '2024-12-12 16:27:35', 9, 0, NULL, '2024-12-12 16:27:15', '2024-12-12 16:27:15', '2024-12-12 16:27:35', 0, NULL, NULL, NULL, 0.00, 0, 0, 0, NULL, 'uploads/results/675b0ef73f23e_Excuse-Letter-for-Christmas-basket-distribution.pdf'),
(197, 50, 161, 89, 'wow', NULL, '2024-12-12 16:28:04', 'graded', '2024-12-12 16:28:23', 9, 0, NULL, '2024-12-12 16:28:04', '2024-12-12 16:28:04', '2024-12-12 16:28:23', 0, NULL, NULL, NULL, 0.00, 0, 0, 0, NULL, 'uploads/results/675b0f275d1d6_Navy and Broken White Geometric Thesis Defense Presentation.pdf'),
(201, 222, 156, NULL, NULL, NULL, '2024-12-15 03:19:25', 'submitted', NULL, NULL, 0, NULL, '2024-12-15 03:19:25', '2024-12-15 03:19:25', '2024-12-15 03:19:25', 0, NULL, NULL, NULL, 0.00, 0, 0, 0, NULL, NULL),
(202, 222, 93, NULL, NULL, NULL, '2024-12-15 03:29:29', 'submitted', NULL, NULL, 0, NULL, '2024-12-15 03:29:29', '2024-12-15 03:29:29', '2024-12-15 03:29:29', 0, NULL, NULL, NULL, 0.00, 0, 0, 0, NULL, NULL),
(205, 222, 164, NULL, NULL, NULL, '2024-12-16 09:49:15', 'submitted', NULL, NULL, 0, NULL, '2024-12-16 09:49:15', '2024-12-16 09:49:15', '2024-12-16 09:49:15', 0, NULL, NULL, NULL, 0.00, 0, 0, 0, NULL, NULL),
(211, 222, 163, 95, '', NULL, '2024-12-16 14:40:39', 'graded', '2024-12-16 14:47:57', 128, 0, NULL, '2024-12-16 14:40:39', '2024-12-16 14:40:39', '2024-12-16 14:47:57', 0, NULL, NULL, NULL, 0.00, 0, 0, 0, NULL, NULL),
(212, 222, 165, 50, '', NULL, '2024-12-16 14:41:25', 'graded', '2024-12-16 14:43:10', 129, 0, NULL, '2024-12-16 14:41:25', '2024-12-16 14:41:25', '2024-12-16 14:43:10', 0, NULL, NULL, NULL, 0.00, 0, 0, 0, NULL, NULL),
(215, 50, 170, 2, NULL, NULL, '2024-12-16 23:32:44', 'submitted', NULL, NULL, 0, NULL, '2024-12-16 23:32:44', '2024-12-16 23:32:44', '2024-12-16 23:32:44', 0, NULL, 14, NULL, 0.00, 0, 0, 2, 3, NULL),
(216, 50, 171, 90, 'yes', NULL, '2024-12-16 23:34:45', 'graded', '2024-12-16 23:38:21', 130, 0, NULL, '2024-12-16 23:34:45', '2024-12-16 23:34:45', '2024-12-16 23:38:21', 0, NULL, NULL, NULL, 0.00, 0, 0, 0, NULL, 'uploads/results/6760b94519333_Activity_SoscanoHershie.docx'),
(220, 267, 170, 2, NULL, NULL, '2024-12-17 00:00:55', 'submitted', NULL, NULL, 0, NULL, '2024-12-17 00:00:55', '2024-12-17 00:00:55', '2024-12-17 00:00:55', 0, NULL, 63, NULL, 0.00, 0, 0, 2, 3, NULL),
(221, 251, 170, 0, NULL, NULL, '2024-12-17 00:01:04', 'missing', NULL, NULL, 0, NULL, '2024-12-17 00:01:04', '2024-12-17 00:01:04', '2024-12-17 00:01:04', 1, NULL, 17, NULL, 0.00, 0, 0, 0, 3, NULL),
(222, 228, 170, 2, NULL, NULL, '2024-12-17 00:03:04', 'submitted', NULL, NULL, 0, NULL, '2024-12-17 00:03:04', '2024-12-17 00:03:04', '2024-12-17 00:03:04', 0, NULL, 29, NULL, 0.00, 0, 0, 2, 3, NULL),
(226, 245, 170, 2, NULL, NULL, '2024-12-17 00:04:52', 'submitted', NULL, NULL, 0, NULL, '2024-12-17 00:04:52', '2024-12-17 00:04:52', '2024-12-17 00:04:52', 0, NULL, 24, NULL, 0.00, 0, 0, 2, 3, NULL),
(230, 225, 170, 2, NULL, NULL, '2024-12-17 00:05:39', 'submitted', NULL, NULL, 0, NULL, '2024-12-17 00:05:39', '2024-12-17 00:05:39', '2024-12-17 00:05:39', 0, NULL, 17, NULL, 0.00, 0, 0, 2, 3, NULL),
(231, 239, 170, 2, NULL, NULL, '2024-12-17 00:05:51', 'submitted', NULL, NULL, 0, NULL, '2024-12-17 00:05:51', '2024-12-17 00:05:51', '2024-12-17 00:05:51', 0, NULL, 21, NULL, 0.00, 0, 0, 2, 3, NULL),
(234, 234, 170, 2, NULL, NULL, '2024-12-17 00:06:15', 'submitted', NULL, NULL, 0, NULL, '2024-12-17 00:06:15', '2024-12-17 00:06:15', '2024-12-17 00:06:15', 0, NULL, 18, NULL, 0.00, 0, 0, 2, 3, NULL),
(235, 232, 170, 2, NULL, NULL, '2024-12-17 00:07:46', 'submitted', NULL, NULL, 0, NULL, '2024-12-17 00:07:46', '2024-12-17 00:07:46', '2024-12-17 00:07:46', 0, NULL, 81, NULL, 0.00, 0, 0, 2, 3, NULL),
(236, 238, 170, 0, NULL, NULL, '2024-12-17 00:07:46', 'missing', NULL, NULL, 0, NULL, '2024-12-17 00:07:46', '2024-12-17 00:07:46', '2024-12-17 00:07:46', 1, NULL, 20, NULL, 0.00, 0, 0, 1, 3, NULL),
(238, 239, 171, NULL, NULL, NULL, '2024-12-17 00:08:37', 'submitted', NULL, NULL, 0, NULL, '2024-12-17 00:08:37', '2024-12-17 00:08:37', '2024-12-17 00:08:37', 0, NULL, NULL, NULL, 0.00, 0, 0, 0, NULL, NULL),
(243, 239, 172, 0, NULL, NULL, '2024-12-17 00:12:53', 'submitted', NULL, NULL, 0, NULL, '2024-12-17 00:12:53', '2024-12-17 00:12:53', '2024-12-17 00:12:53', 0, NULL, 83, NULL, 0.00, 0, 0, 0, 5, NULL),
(244, 225, 172, 0, NULL, NULL, '2024-12-17 00:12:57', 'submitted', NULL, NULL, 0, NULL, '2024-12-17 00:12:57', '2024-12-17 00:12:57', '2024-12-17 00:12:57', 0, NULL, 60, NULL, 0.00, 0, 0, 0, 5, NULL),
(245, 241, 172, 0, NULL, NULL, '2024-12-17 00:14:17', 'missing', NULL, NULL, 0, NULL, '2024-12-17 00:14:17', '2024-12-17 00:14:17', '2024-12-17 00:14:17', 1, NULL, 43, NULL, 0.00, 0, 0, 0, 5, NULL),
(246, 228, 172, 0, NULL, NULL, '2024-12-17 00:14:23', 'submitted', NULL, NULL, 0, NULL, '2024-12-17 00:14:23', '2024-12-17 00:14:23', '2024-12-17 00:14:23', 0, NULL, 46, NULL, 0.00, 0, 0, 0, 5, NULL),
(247, 230, 172, 0, NULL, NULL, '2024-12-17 00:14:31', 'submitted', NULL, NULL, 0, NULL, '2024-12-17 00:14:31', '2024-12-17 00:14:31', '2024-12-17 00:14:31', 0, NULL, 81, NULL, 0.00, 0, 0, 0, 5, NULL),
(248, 234, 172, 0, NULL, NULL, '2024-12-17 00:15:17', 'submitted', NULL, NULL, 0, NULL, '2024-12-17 00:15:17', '2024-12-17 00:15:17', '2024-12-17 00:15:17', 0, NULL, 130, NULL, 0.00, 0, 0, 0, 5, NULL),
(249, 265, 172, 0, NULL, NULL, '2024-12-17 00:15:17', 'missing', NULL, NULL, 0, NULL, '2024-12-17 00:15:17', '2024-12-17 00:15:17', '2024-12-17 00:15:17', 1, NULL, 80, NULL, 0.00, 0, 0, 0, 5, NULL),
(250, 267, 172, 0, NULL, NULL, '2024-12-17 00:15:28', 'submitted', NULL, NULL, 0, NULL, '2024-12-17 00:15:28', '2024-12-17 00:15:28', '2024-12-17 00:15:28', 0, NULL, 53, NULL, 0.00, 0, 0, 0, 5, NULL),
(251, 245, 172, 0, NULL, NULL, '2024-12-17 00:15:33', 'submitted', NULL, NULL, 0, NULL, '2024-12-17 00:15:33', '2024-12-17 00:15:33', '2024-12-17 00:15:33', 0, NULL, 147, NULL, 0.00, 0, 0, 0, 5, NULL),
(252, 238, 172, 0, NULL, NULL, '2024-12-17 00:15:35', 'submitted', NULL, NULL, 0, NULL, '2024-12-17 00:15:35', '2024-12-17 00:15:35', '2024-12-17 00:15:35', 0, NULL, 82, NULL, 0.00, 0, 0, 0, 5, NULL),
(253, 232, 172, 0, NULL, NULL, '2024-12-17 00:15:35', 'submitted', NULL, NULL, 0, NULL, '2024-12-17 00:15:35', '2024-12-17 00:15:35', '2024-12-17 00:15:35', 0, NULL, 60, NULL, 0.00, 0, 0, 0, 5, NULL),
(254, 231, 172, 0, NULL, NULL, '2024-12-17 00:15:37', 'submitted', NULL, NULL, 0, NULL, '2024-12-17 00:15:37', '2024-12-17 00:15:37', '2024-12-17 00:15:37', 0, NULL, 104, NULL, 0.00, 0, 0, 0, 5, NULL),
(255, 247, 172, 0, NULL, NULL, '2024-12-17 00:15:41', 'submitted', NULL, NULL, 0, NULL, '2024-12-17 00:15:41', '2024-12-17 00:15:41', '2024-12-17 00:15:41', 0, NULL, 100, NULL, 0.00, 0, 0, 0, 5, NULL),
(256, 255, 172, 0, NULL, NULL, '2024-12-17 00:15:44', 'submitted', NULL, NULL, 0, NULL, '2024-12-17 00:15:44', '2024-12-17 00:15:44', '2024-12-17 00:15:44', 0, NULL, 138, NULL, 0.00, 0, 0, 0, 5, NULL),
(257, 251, 172, 0, NULL, NULL, '2024-12-17 00:15:51', 'submitted', NULL, NULL, 0, NULL, '2024-12-17 00:15:51', '2024-12-17 00:15:51', '2024-12-17 00:15:51', 0, NULL, 145, NULL, 0.00, 0, 0, 0, 5, NULL),
(258, 250, 172, 0, NULL, NULL, '2024-12-17 00:16:05', 'submitted', NULL, NULL, 0, NULL, '2024-12-17 00:16:05', '2024-12-17 00:16:05', '2024-12-17 00:16:05', 0, NULL, 164, NULL, 0.00, 0, 0, 0, 5, NULL),
(259, 259, 172, 0, NULL, NULL, '2024-12-17 00:16:14', 'submitted', NULL, NULL, 0, NULL, '2024-12-17 00:16:14', '2024-12-17 00:16:14', '2024-12-17 00:16:14', 0, NULL, 70, NULL, 0.00, 0, 0, 0, 5, NULL),
(260, 224, 172, 0, NULL, NULL, '2024-12-17 00:16:18', 'submitted', NULL, NULL, 0, NULL, '2024-12-17 00:16:18', '2024-12-17 00:16:18', '2024-12-17 00:16:18', 0, NULL, 178, NULL, 0.00, 0, 0, 0, 5, NULL),
(261, 256, 172, 0, NULL, NULL, '2024-12-17 00:16:20', 'missing', NULL, NULL, 0, NULL, '2024-12-17 00:16:20', '2024-12-17 00:16:20', '2024-12-17 00:16:20', 1, NULL, 147, NULL, 0.00, 0, 0, 0, 5, NULL),
(262, 253, 172, 0, NULL, NULL, '2024-12-17 00:16:30', 'submitted', NULL, NULL, 0, NULL, '2024-12-17 00:16:30', '2024-12-17 00:16:30', '2024-12-17 00:16:30', 0, NULL, 165, NULL, 0.00, 0, 0, 0, 5, NULL),
(263, 254, 172, 0, NULL, NULL, '2024-12-17 00:16:38', 'submitted', NULL, NULL, 0, NULL, '2024-12-17 00:16:38', '2024-12-17 00:16:38', '2024-12-17 00:16:38', 0, NULL, 188, NULL, 0.00, 0, 0, 0, 5, NULL),
(264, 264, 172, 0, NULL, NULL, '2024-12-17 00:16:39', 'submitted', NULL, NULL, 0, NULL, '2024-12-17 00:16:39', '2024-12-17 00:16:39', '2024-12-17 00:16:39', 0, NULL, 175, NULL, 0.00, 0, 0, 0, 5, NULL),
(265, 269, 172, 0, NULL, NULL, '2024-12-17 00:16:58', 'submitted', NULL, NULL, 0, NULL, '2024-12-17 00:16:58', '2024-12-17 00:16:58', '2024-12-17 00:16:58', 0, NULL, 218, NULL, 0.00, 0, 0, 0, 5, NULL),
(266, 261, 172, 0, NULL, NULL, '2024-12-17 00:17:05', 'submitted', NULL, NULL, 0, NULL, '2024-12-17 00:17:05', '2024-12-17 00:17:05', '2024-12-17 00:17:05', 0, NULL, 149, NULL, 0.00, 0, 0, 0, 5, NULL),
(267, 252, 172, 0, NULL, NULL, '2024-12-17 00:17:15', 'submitted', NULL, NULL, 0, NULL, '2024-12-17 00:17:15', '2024-12-17 00:17:15', '2024-12-17 00:17:15', 0, NULL, 239, NULL, 0.00, 0, 0, 0, 5, NULL),
(268, 226, 172, 0, NULL, NULL, '2024-12-17 00:17:52', 'submitted', NULL, NULL, 0, NULL, '2024-12-17 00:17:52', '2024-12-17 00:17:52', '2024-12-17 00:17:52', 0, NULL, 108, NULL, 0.00, 0, 0, 0, 5, NULL),
(269, 248, 172, 0, NULL, NULL, '2024-12-17 00:18:02', 'submitted', NULL, NULL, 0, NULL, '2024-12-17 00:18:02', '2024-12-17 00:18:02', '2024-12-17 00:18:02', 0, NULL, 363, NULL, 0.00, 0, 0, 0, 5, NULL),
(270, 257, 172, 0, NULL, NULL, '2024-12-17 00:18:12', 'submitted', NULL, NULL, 0, NULL, '2024-12-17 00:18:12', '2024-12-17 00:18:12', '2024-12-17 00:18:12', 0, NULL, 279, NULL, 0.00, 0, 0, 0, 5, NULL),
(271, 266, 172, 0, NULL, NULL, '2024-12-17 00:18:19', 'submitted', NULL, NULL, 0, NULL, '2024-12-17 00:18:19', '2024-12-17 00:18:19', '2024-12-17 00:18:19', 0, NULL, 236, NULL, 0.00, 0, 0, 0, 5, NULL),
(272, 240, 172, 0, NULL, NULL, '2024-12-17 00:18:58', 'submitted', NULL, NULL, 0, NULL, '2024-12-17 00:18:58', '2024-12-17 00:18:58', '2024-12-17 00:18:58', 0, NULL, 350, NULL, 0.00, 0, 0, 0, 5, NULL),
(273, 50, 173, 0, NULL, NULL, '2024-12-17 00:19:46', 'submitted', NULL, NULL, 0, NULL, '2024-12-17 00:19:46', '2024-12-17 00:19:46', '2024-12-17 00:19:46', 0, NULL, 13, NULL, 0.00, 0, 0, 0, 2, NULL),
(274, 263, 172, 0, NULL, NULL, '2024-12-17 00:21:58', 'submitted', NULL, NULL, 0, NULL, '2024-12-17 00:21:58', '2024-12-17 00:21:58', '2024-12-17 00:21:58', 0, NULL, 126, NULL, 0.00, 0, 0, 0, 5, NULL),
(275, 263, 173, 0, NULL, NULL, '2024-12-17 00:22:29', 'submitted', NULL, NULL, 0, NULL, '2024-12-17 00:22:29', '2024-12-17 00:22:29', '2024-12-17 00:22:29', 0, NULL, 18, NULL, 0.00, 0, 0, 0, 2, NULL),
(276, 267, 173, 0, NULL, NULL, '2024-12-17 00:26:49', 'submitted', NULL, NULL, 0, NULL, '2024-12-17 00:26:49', '2024-12-17 00:26:49', '2024-12-17 00:26:49', 0, NULL, 17, NULL, 0.00, 0, 0, 0, 2, NULL),
(277, 258, 172, 0, NULL, NULL, '2024-12-17 00:29:32', 'submitted', NULL, NULL, 0, NULL, '2024-12-17 00:29:32', '2024-12-17 00:29:32', '2024-12-17 00:29:32', 0, NULL, 165, NULL, 0.00, 0, 0, 0, 5, NULL),
(278, 50, 172, 0, NULL, NULL, '2024-12-17 01:54:16', 'submitted', NULL, NULL, 0, NULL, '2024-12-17 01:54:16', '2024-12-17 01:54:16', '2024-12-17 01:54:16', 0, NULL, 26, NULL, 0.00, 0, 0, 0, 5, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `student_answers`
--

CREATE TABLE `student_answers` (
  `answer_id` int(11) NOT NULL,
  `student_id` bigint(20) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `selected_choice_id` int(11) DEFAULT NULL,
  `text_answer` text DEFAULT NULL,
  `is_correct` tinyint(1) DEFAULT 0,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_answers`
--

INSERT INTO `student_answers` (`answer_id`, `student_id`, `quiz_id`, `question_id`, `selected_choice_id`, `text_answer`, `is_correct`, `submitted_at`) VALUES
(27, 50, 138, 91, 168, '', 1, '2024-12-06 17:26:46'),
(28, 50, 138, 92, NULL, '', 0, '2024-12-06 17:26:46'),
(29, 50, 138, 93, NULL, '0', 1, '2024-12-06 17:26:46'),
(30, 50, 138, 94, 172, '', 1, '2024-12-06 17:26:46'),
(73, 50, 139, 95, 174, '', 1, '2024-12-06 18:34:59'),
(74, 50, 139, 96, 176, '', 1, '2024-12-06 18:34:59'),
(75, 50, 139, 97, NULL, '0', 1, '2024-12-06 18:34:59'),
(171, 50, 141, 104, 186, '0', 1, '2024-12-07 00:04:12'),
(172, 50, 141, 105, 188, '0', 1, '2024-12-07 00:04:12'),
(173, 50, 141, 106, NULL, '0', 1, '2024-12-07 00:04:12'),
(174, 50, 141, 107, 190, '0', 1, '2024-12-07 00:04:12'),
(175, 50, 140, 98, 178, '0', 1, '2024-12-07 00:06:14'),
(176, 50, 140, 99, 180, '0', 1, '2024-12-07 00:06:14'),
(177, 50, 140, 100, NULL, '0', 1, '2024-12-07 00:06:14'),
(178, 50, 140, 101, 182, '0', 1, '2024-12-07 00:06:14'),
(179, 50, 140, 102, 184, '0', 1, '2024-12-07 00:06:14'),
(180, 50, 140, 103, NULL, '0', 1, '2024-12-07 00:06:14'),
(216, 50, 142, 108, NULL, '0', 0, '2024-12-07 00:49:06'),
(217, 50, 142, 109, NULL, '0', 0, '2024-12-07 00:49:06'),
(218, 50, 142, 110, NULL, '0', 0, '2024-12-07 00:49:06'),
(219, 50, 142, 111, NULL, '0', 0, '2024-12-07 00:49:06'),
(220, 50, 142, 112, NULL, '0', 0, '2024-12-07 00:49:06'),
(231, 50, 147, 120, 211, '', 0, '2024-12-09 01:51:19'),
(232, 50, 147, 121, 214, '', 0, '2024-12-09 01:51:19'),
(233, 50, 147, 122, NULL, 'a', 0, '2024-12-09 01:51:19'),
(240, 50, 146, 113, 198, '', 0, '2024-12-09 02:12:21'),
(241, 50, 146, 114, 202, '', 1, '2024-12-09 02:12:21'),
(242, 50, 146, 115, NULL, 'asd', 1, '2024-12-09 02:12:21'),
(243, 50, 146, 116, NULL, 'asd', 0, '2024-12-09 02:12:21'),
(244, 50, 146, 117, NULL, 'sad', 0, '2024-12-09 02:12:21'),
(245, 50, 146, 118, 204, '', 1, '2024-12-09 02:12:21'),
(246, 50, 146, 119, 208, '', 0, '2024-12-09 02:12:21'),
(247, 50, 148, 123, 216, '', 1, '2024-12-09 02:12:44'),
(248, 50, 148, 124, 220, '', 1, '2024-12-09 02:12:44'),
(249, 50, 148, 125, NULL, 'asdasd', 0, '2024-12-09 02:12:44'),
(250, 50, 149, 126, 222, '', 0, '2024-12-09 02:13:31'),
(251, 50, 149, 127, 226, '', 0, '2024-12-09 02:13:31'),
(252, 50, 149, 128, NULL, '111', 0, '2024-12-09 02:13:31'),
(253, 50, 150, 129, 228, '', 1, '2024-12-09 11:15:45'),
(254, 50, 150, 130, 231, '', 1, '2024-12-09 11:15:45'),
(255, 50, 150, 131, NULL, 'OO', 1, '2024-12-09 11:15:45'),
(256, 50, 151, 132, NULL, '', 1, '2024-12-09 12:59:53'),
(257, 50, 151, 133, NULL, '', 1, '2024-12-09 12:59:53'),
(258, 50, 151, 134, NULL, '4', 1, '2024-12-09 12:59:53'),
(259, 50, 151, 135, NULL, 'testdawd', 0, '2024-12-09 12:59:53'),
(260, 50, 154, 136, NULL, '', 0, '2024-12-12 11:49:40'),
(267, 50, 159, 141, NULL, '', 0, '2024-12-12 12:10:42'),
(270, 50, 158, 138, NULL, '', 0, '2024-12-12 12:33:37'),
(271, 50, 158, 139, NULL, '', 0, '2024-12-12 12:33:37'),
(272, 50, 158, 140, NULL, '', 0, '2024-12-12 12:33:37'),
(283, 50, 160, 142, 252, '', 1, '2024-12-12 12:46:33'),
(284, 50, 160, 143, 254, '', 0, '2024-12-12 12:46:33'),
(285, 50, 162, 144, 259, '', 1, '2024-12-12 16:25:09'),
(286, 50, 162, 145, 261, '', 1, '2024-12-12 16:25:09'),
(287, 50, 162, 146, NULL, '', 0, '2024-12-12 16:25:09'),
(295, 50, 170, 157, 288, '', 1, '2024-12-16 23:32:44'),
(296, 50, 170, 158, 291, '', 1, '2024-12-16 23:32:44'),
(297, 50, 170, 159, NULL, '', 0, '2024-12-16 23:32:44'),
(313, 267, 170, 157, 288, '', 1, '2024-12-17 00:00:55'),
(314, 267, 170, 158, 291, '', 1, '2024-12-17 00:00:55'),
(315, 267, 170, 159, NULL, '', 0, '2024-12-17 00:00:55'),
(316, 251, 170, 157, NULL, '', 0, '2024-12-17 00:01:04'),
(317, 251, 170, 158, NULL, '', 0, '2024-12-17 00:01:04'),
(318, 251, 170, 159, NULL, '', 0, '2024-12-17 00:01:04'),
(319, 228, 170, 157, 288, '', 1, '2024-12-17 00:03:04'),
(320, 228, 170, 158, 291, '', 1, '2024-12-17 00:03:04'),
(321, 228, 170, 159, NULL, '', 0, '2024-12-17 00:03:04'),
(337, 245, 170, 157, 288, '', 1, '2024-12-17 00:04:52'),
(338, 245, 170, 158, 291, '', 1, '2024-12-17 00:04:52'),
(339, 245, 170, 159, NULL, '', 0, '2024-12-17 00:04:52'),
(355, 225, 170, 157, 288, '', 1, '2024-12-17 00:05:39'),
(356, 225, 170, 158, 291, '', 1, '2024-12-17 00:05:39'),
(357, 225, 170, 159, NULL, '', 0, '2024-12-17 00:05:39'),
(358, 239, 170, 157, 288, '', 1, '2024-12-17 00:05:51'),
(359, 239, 170, 158, 291, '', 1, '2024-12-17 00:05:51'),
(360, 239, 170, 159, NULL, '', 0, '2024-12-17 00:05:51'),
(371, 234, 170, 157, 288, '', 1, '2024-12-17 00:06:15'),
(372, 234, 170, 158, 291, '', 1, '2024-12-17 00:06:15'),
(373, 234, 170, 159, NULL, '', 0, '2024-12-17 00:06:15'),
(374, 232, 170, 157, 288, '', 1, '2024-12-17 00:07:46'),
(375, 232, 170, 158, 291, '', 1, '2024-12-17 00:07:46'),
(376, 232, 170, 159, NULL, '', 0, '2024-12-17 00:07:46'),
(377, 238, 170, 157, 288, '', 1, '2024-12-17 00:07:46'),
(378, 238, 170, 158, NULL, '', 0, '2024-12-17 00:07:46'),
(379, 238, 170, 159, NULL, '', 0, '2024-12-17 00:07:46'),
(405, 239, 172, 160, NULL, '', 0, '2024-12-17 00:12:53'),
(406, 239, 172, 161, NULL, '', 0, '2024-12-17 00:12:53'),
(407, 239, 172, 162, NULL, '', 0, '2024-12-17 00:12:53'),
(408, 239, 172, 163, NULL, '', 0, '2024-12-17 00:12:53'),
(409, 239, 172, 164, NULL, '', 0, '2024-12-17 00:12:53'),
(410, 225, 172, 160, NULL, '', 0, '2024-12-17 00:12:57'),
(411, 225, 172, 161, NULL, '', 0, '2024-12-17 00:12:57'),
(412, 225, 172, 162, NULL, '', 0, '2024-12-17 00:12:57'),
(413, 225, 172, 163, NULL, '', 0, '2024-12-17 00:12:57'),
(414, 225, 172, 164, NULL, '', 0, '2024-12-17 00:12:57'),
(415, 241, 172, 160, NULL, '', 0, '2024-12-17 00:14:17'),
(416, 241, 172, 161, NULL, '', 0, '2024-12-17 00:14:17'),
(417, 241, 172, 162, NULL, '', 0, '2024-12-17 00:14:17'),
(418, 241, 172, 163, NULL, '', 0, '2024-12-17 00:14:17'),
(419, 241, 172, 164, NULL, '', 0, '2024-12-17 00:14:17'),
(420, 228, 172, 160, NULL, '', 0, '2024-12-17 00:14:23'),
(421, 228, 172, 161, NULL, '', 0, '2024-12-17 00:14:23'),
(422, 228, 172, 162, NULL, '', 0, '2024-12-17 00:14:23'),
(423, 228, 172, 163, NULL, '', 0, '2024-12-17 00:14:23'),
(424, 228, 172, 164, NULL, '', 0, '2024-12-17 00:14:23'),
(425, 230, 172, 160, NULL, '', 0, '2024-12-17 00:14:31'),
(426, 230, 172, 161, NULL, '', 0, '2024-12-17 00:14:31'),
(427, 230, 172, 162, NULL, '', 0, '2024-12-17 00:14:31'),
(428, 230, 172, 163, NULL, '', 0, '2024-12-17 00:14:31'),
(429, 230, 172, 164, NULL, '', 0, '2024-12-17 00:14:31'),
(430, 234, 172, 160, NULL, '', 0, '2024-12-17 00:15:17'),
(431, 234, 172, 161, NULL, '', 0, '2024-12-17 00:15:17'),
(432, 234, 172, 162, NULL, '', 0, '2024-12-17 00:15:17'),
(433, 234, 172, 163, NULL, '', 0, '2024-12-17 00:15:17'),
(434, 234, 172, 164, NULL, '', 0, '2024-12-17 00:15:17'),
(435, 265, 172, 160, NULL, '', 0, '2024-12-17 00:15:17'),
(436, 265, 172, 161, NULL, '', 0, '2024-12-17 00:15:17'),
(437, 265, 172, 162, NULL, '', 0, '2024-12-17 00:15:17'),
(438, 265, 172, 163, NULL, '', 0, '2024-12-17 00:15:17'),
(439, 265, 172, 164, NULL, '', 0, '2024-12-17 00:15:17'),
(440, 267, 172, 160, NULL, '', 0, '2024-12-17 00:15:28'),
(441, 267, 172, 161, NULL, '', 0, '2024-12-17 00:15:28'),
(442, 267, 172, 162, NULL, '', 0, '2024-12-17 00:15:28'),
(443, 267, 172, 163, NULL, '', 0, '2024-12-17 00:15:28'),
(444, 267, 172, 164, NULL, '', 0, '2024-12-17 00:15:28'),
(445, 245, 172, 160, NULL, '', 0, '2024-12-17 00:15:33'),
(446, 245, 172, 161, NULL, '', 0, '2024-12-17 00:15:33'),
(447, 245, 172, 162, NULL, '', 0, '2024-12-17 00:15:33'),
(448, 245, 172, 163, NULL, '', 0, '2024-12-17 00:15:33'),
(449, 245, 172, 164, NULL, '', 0, '2024-12-17 00:15:33'),
(450, 238, 172, 160, NULL, '', 0, '2024-12-17 00:15:35'),
(451, 238, 172, 161, NULL, '', 0, '2024-12-17 00:15:35'),
(452, 238, 172, 162, NULL, '', 0, '2024-12-17 00:15:35'),
(453, 238, 172, 163, NULL, '', 0, '2024-12-17 00:15:35'),
(454, 238, 172, 164, NULL, '', 0, '2024-12-17 00:15:35'),
(455, 232, 172, 160, NULL, '', 0, '2024-12-17 00:15:35'),
(456, 232, 172, 161, NULL, '', 0, '2024-12-17 00:15:35'),
(457, 232, 172, 162, NULL, '', 0, '2024-12-17 00:15:35'),
(458, 232, 172, 163, NULL, '', 0, '2024-12-17 00:15:35'),
(459, 232, 172, 164, NULL, '', 0, '2024-12-17 00:15:35'),
(460, 231, 172, 160, NULL, '', 0, '2024-12-17 00:15:37'),
(461, 231, 172, 161, NULL, '', 0, '2024-12-17 00:15:37'),
(462, 231, 172, 162, NULL, '', 0, '2024-12-17 00:15:37'),
(463, 231, 172, 163, NULL, '', 0, '2024-12-17 00:15:37'),
(464, 231, 172, 164, NULL, '', 0, '2024-12-17 00:15:37'),
(465, 247, 172, 160, NULL, '', 0, '2024-12-17 00:15:41'),
(466, 247, 172, 161, NULL, '', 0, '2024-12-17 00:15:41'),
(467, 247, 172, 162, NULL, '', 0, '2024-12-17 00:15:41'),
(468, 247, 172, 163, NULL, '', 0, '2024-12-17 00:15:41'),
(469, 247, 172, 164, NULL, '', 0, '2024-12-17 00:15:41'),
(470, 255, 172, 160, NULL, '', 0, '2024-12-17 00:15:44'),
(471, 255, 172, 161, NULL, '', 0, '2024-12-17 00:15:44'),
(472, 255, 172, 162, NULL, '', 0, '2024-12-17 00:15:44'),
(473, 255, 172, 163, NULL, '', 0, '2024-12-17 00:15:44'),
(474, 255, 172, 164, NULL, '', 0, '2024-12-17 00:15:44'),
(475, 251, 172, 160, NULL, '', 0, '2024-12-17 00:15:51'),
(476, 251, 172, 161, NULL, '', 0, '2024-12-17 00:15:51'),
(477, 251, 172, 162, NULL, '', 0, '2024-12-17 00:15:51'),
(478, 251, 172, 163, NULL, '', 0, '2024-12-17 00:15:51'),
(479, 251, 172, 164, NULL, '', 0, '2024-12-17 00:15:51'),
(480, 250, 172, 160, NULL, '', 0, '2024-12-17 00:16:05'),
(481, 250, 172, 161, NULL, '', 0, '2024-12-17 00:16:05'),
(482, 250, 172, 162, NULL, '', 0, '2024-12-17 00:16:05'),
(483, 250, 172, 163, NULL, '', 0, '2024-12-17 00:16:05'),
(484, 250, 172, 164, NULL, '', 0, '2024-12-17 00:16:05'),
(485, 259, 172, 160, NULL, '', 0, '2024-12-17 00:16:14'),
(486, 259, 172, 161, NULL, '', 0, '2024-12-17 00:16:14'),
(487, 259, 172, 162, NULL, '', 0, '2024-12-17 00:16:14'),
(488, 259, 172, 163, NULL, '', 0, '2024-12-17 00:16:14'),
(489, 259, 172, 164, NULL, '', 0, '2024-12-17 00:16:14'),
(490, 224, 172, 160, NULL, '', 0, '2024-12-17 00:16:18'),
(491, 224, 172, 161, NULL, '', 0, '2024-12-17 00:16:18'),
(492, 224, 172, 162, NULL, '', 0, '2024-12-17 00:16:18'),
(493, 224, 172, 163, NULL, '', 0, '2024-12-17 00:16:18'),
(494, 224, 172, 164, NULL, '', 0, '2024-12-17 00:16:18'),
(495, 256, 172, 160, NULL, '', 0, '2024-12-17 00:16:20'),
(496, 256, 172, 161, NULL, '', 0, '2024-12-17 00:16:20'),
(497, 256, 172, 162, NULL, '', 0, '2024-12-17 00:16:20'),
(498, 256, 172, 163, NULL, '', 0, '2024-12-17 00:16:20'),
(499, 256, 172, 164, NULL, '', 0, '2024-12-17 00:16:20'),
(500, 253, 172, 160, NULL, '', 0, '2024-12-17 00:16:30'),
(501, 253, 172, 161, NULL, '', 0, '2024-12-17 00:16:30'),
(502, 253, 172, 162, NULL, '', 0, '2024-12-17 00:16:30'),
(503, 253, 172, 163, NULL, '', 0, '2024-12-17 00:16:30'),
(504, 253, 172, 164, NULL, '', 0, '2024-12-17 00:16:30'),
(505, 254, 172, 160, NULL, '', 0, '2024-12-17 00:16:38'),
(506, 254, 172, 161, NULL, '', 0, '2024-12-17 00:16:38'),
(507, 254, 172, 162, NULL, '', 0, '2024-12-17 00:16:38'),
(508, 254, 172, 163, NULL, '', 0, '2024-12-17 00:16:38'),
(509, 254, 172, 164, NULL, '', 0, '2024-12-17 00:16:38'),
(510, 264, 172, 160, NULL, '', 0, '2024-12-17 00:16:39'),
(511, 264, 172, 161, NULL, '', 0, '2024-12-17 00:16:39'),
(512, 264, 172, 162, NULL, '', 0, '2024-12-17 00:16:39'),
(513, 264, 172, 163, NULL, '', 0, '2024-12-17 00:16:39'),
(514, 264, 172, 164, NULL, '', 0, '2024-12-17 00:16:39'),
(515, 269, 172, 160, NULL, '', 0, '2024-12-17 00:16:58'),
(516, 269, 172, 161, NULL, '', 0, '2024-12-17 00:16:58'),
(517, 269, 172, 162, NULL, '', 0, '2024-12-17 00:16:58'),
(518, 269, 172, 163, NULL, '', 0, '2024-12-17 00:16:58'),
(519, 269, 172, 164, NULL, '', 0, '2024-12-17 00:16:58'),
(520, 261, 172, 160, NULL, '', 0, '2024-12-17 00:17:05'),
(521, 261, 172, 161, NULL, '', 0, '2024-12-17 00:17:05'),
(522, 261, 172, 162, NULL, '', 0, '2024-12-17 00:17:05'),
(523, 261, 172, 163, NULL, '', 0, '2024-12-17 00:17:05'),
(524, 261, 172, 164, NULL, '', 0, '2024-12-17 00:17:05'),
(525, 252, 172, 160, NULL, '', 0, '2024-12-17 00:17:15'),
(526, 252, 172, 161, NULL, '', 0, '2024-12-17 00:17:15'),
(527, 252, 172, 162, NULL, '', 0, '2024-12-17 00:17:15'),
(528, 252, 172, 163, NULL, '', 0, '2024-12-17 00:17:15'),
(529, 252, 172, 164, NULL, '', 0, '2024-12-17 00:17:15'),
(530, 226, 172, 160, NULL, '', 0, '2024-12-17 00:17:52'),
(531, 226, 172, 161, NULL, '', 0, '2024-12-17 00:17:52'),
(532, 226, 172, 162, NULL, '', 0, '2024-12-17 00:17:52'),
(533, 226, 172, 163, NULL, '', 0, '2024-12-17 00:17:52'),
(534, 226, 172, 164, NULL, '', 0, '2024-12-17 00:17:52'),
(535, 248, 172, 160, NULL, '', 0, '2024-12-17 00:18:02'),
(536, 248, 172, 161, NULL, '', 0, '2024-12-17 00:18:02'),
(537, 248, 172, 162, NULL, '', 0, '2024-12-17 00:18:02'),
(538, 248, 172, 163, NULL, '', 0, '2024-12-17 00:18:02'),
(539, 248, 172, 164, NULL, '', 0, '2024-12-17 00:18:02'),
(540, 257, 172, 160, NULL, '', 0, '2024-12-17 00:18:12'),
(541, 257, 172, 161, NULL, '', 0, '2024-12-17 00:18:12'),
(542, 257, 172, 162, NULL, '', 0, '2024-12-17 00:18:12'),
(543, 257, 172, 163, NULL, '', 0, '2024-12-17 00:18:12'),
(544, 257, 172, 164, NULL, '', 0, '2024-12-17 00:18:12'),
(545, 266, 172, 160, NULL, '', 0, '2024-12-17 00:18:19'),
(546, 266, 172, 161, NULL, '', 0, '2024-12-17 00:18:19'),
(547, 266, 172, 162, NULL, '', 0, '2024-12-17 00:18:19'),
(548, 266, 172, 163, NULL, '', 0, '2024-12-17 00:18:19'),
(549, 266, 172, 164, NULL, '', 0, '2024-12-17 00:18:19'),
(550, 240, 172, 160, NULL, '', 0, '2024-12-17 00:18:58'),
(551, 240, 172, 161, NULL, '', 0, '2024-12-17 00:18:58'),
(552, 240, 172, 162, NULL, '', 0, '2024-12-17 00:18:58'),
(553, 240, 172, 163, NULL, '', 0, '2024-12-17 00:18:58'),
(554, 240, 172, 164, NULL, '', 0, '2024-12-17 00:18:58'),
(555, 50, 173, 165, NULL, '', 0, '2024-12-17 00:19:46'),
(556, 50, 173, 166, NULL, '', 0, '2024-12-17 00:19:46'),
(557, 263, 172, 160, NULL, '', 0, '2024-12-17 00:21:58'),
(558, 263, 172, 161, NULL, '', 0, '2024-12-17 00:21:58'),
(559, 263, 172, 162, NULL, '', 0, '2024-12-17 00:21:58'),
(560, 263, 172, 163, NULL, '', 0, '2024-12-17 00:21:58'),
(561, 263, 172, 164, NULL, '', 0, '2024-12-17 00:21:58'),
(562, 263, 173, 165, NULL, '', 0, '2024-12-17 00:22:29'),
(563, 263, 173, 166, NULL, '', 0, '2024-12-17 00:22:29'),
(564, 267, 173, 165, NULL, '', 0, '2024-12-17 00:26:49'),
(565, 267, 173, 166, NULL, '', 0, '2024-12-17 00:26:49'),
(566, 258, 172, 160, NULL, '', 0, '2024-12-17 00:29:32'),
(567, 258, 172, 161, NULL, '', 0, '2024-12-17 00:29:32'),
(568, 258, 172, 162, NULL, '', 0, '2024-12-17 00:29:32'),
(569, 258, 172, 163, NULL, '', 0, '2024-12-17 00:29:32'),
(570, 258, 172, 164, NULL, '', 0, '2024-12-17 00:29:32'),
(571, 50, 172, 160, NULL, '', 0, '2024-12-17 01:54:16'),
(572, 50, 172, 161, NULL, '', 0, '2024-12-17 01:54:16'),
(573, 50, 172, 162, NULL, '', 0, '2024-12-17 01:54:16'),
(574, 50, 172, 163, NULL, '', 0, '2024-12-17 01:54:16'),
(575, 50, 172, 164, NULL, '', 0, '2024-12-17 01:54:16');

-- --------------------------------------------------------

--
-- Table structure for table `student_answers_old`
--

CREATE TABLE `student_answers_old` (
  `answer_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `selected_choice_id` int(11) DEFAULT NULL,
  `text_answer` text DEFAULT NULL,
  `is_correct` tinyint(1) DEFAULT 0,
  `points_earned` decimal(5,2) DEFAULT 0.00,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_backup`
--

CREATE TABLE `student_backup` (
  `student_id` int(11) NOT NULL DEFAULT 0,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `middlename` varchar(100) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `cys` varchar(50) NOT NULL,
  `status` enum('active','archived') DEFAULT 'active',
  `login_attempts` int(11) DEFAULT 0,
  `lockout_until` datetime DEFAULT NULL,
  `user_online` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_activity` bigint(20) DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_grades`
--

CREATE TABLE `student_grades` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `section_subject_id` int(11) NOT NULL,
  `final_grade` decimal(5,2) NOT NULL,
  `calculated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_login_logs`
--

CREATE TABLE `student_login_logs` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `status` enum('success','failed','logout') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_login_logs`
--

INSERT INTO `student_login_logs` (`id`, `student_id`, `ip_address`, `status`, `created_at`) VALUES
(80, 2147483647, '::1', 'success', '2024-11-23 15:36:27'),
(158, 50, '::1', 'success', '2024-12-04 01:17:24'),
(159, 50, '::1', 'success', '2024-12-06 03:40:28'),
(160, 50, '::1', 'success', '2024-12-06 05:15:12'),
(161, 50, '180.194.233.34', 'success', '2024-12-09 01:38:23'),
(162, 50, '49.144.12.254', 'success', '2024-12-09 01:42:45'),
(163, 50, '49.144.12.254', 'logout', '2024-12-09 01:46:14'),
(164, 50, '49.144.12.254', 'success', '2024-12-09 01:50:55'),
(165, 50, '180.194.233.34', 'success', '2024-12-09 02:03:55'),
(166, 50, '175.176.36.21', 'success', '2024-12-09 11:13:01'),
(167, 50, '175.176.36.21', 'logout', '2024-12-09 11:17:29'),
(168, 50, '175.176.36.21', 'success', '2024-12-09 11:17:42'),
(169, 50, '175.176.36.21', 'logout', '2024-12-09 11:18:10'),
(170, 50, '136.158.49.29', 'success', '2024-12-09 12:17:25'),
(171, 50, '175.176.36.21', 'success', '2024-12-09 12:58:20'),
(172, 50, '136.158.49.29', 'success', '2024-12-09 15:43:47'),
(173, 50, '136.158.49.29', 'success', '2024-12-09 16:36:37'),
(174, 50, '::1', 'success', '2024-12-10 19:31:47'),
(175, 50, '::1', 'success', '2024-12-10 19:33:30'),
(176, 50, '::1', 'success', '2024-12-10 19:34:37'),
(177, 50, '::1', 'success', '2024-12-10 19:35:07'),
(178, 50, '::1', 'success', '2024-12-10 19:36:43'),
(179, 50, '::1', 'success', '2024-12-10 19:38:22'),
(180, 50, '::1', 'success', '2024-12-10 19:40:20'),
(181, 50, '::1', 'success', '2024-12-10 19:44:35'),
(182, 50, '::1', 'success', '2024-12-10 19:54:25'),
(183, 50, '::1', 'success', '2024-12-10 19:55:59'),
(184, 50, '::1', 'logout', '2024-12-10 19:56:03'),
(185, 50, '::1', 'success', '2024-12-10 19:59:12'),
(186, 50, '::1', 'success', '2024-12-10 20:02:25'),
(187, 50, '::1', 'success', '2024-12-10 20:04:21'),
(188, 50, '::1', 'success', '2024-12-10 20:13:10'),
(189, 50, '::1', 'success', '2024-12-10 20:18:24'),
(190, 50, '::1', 'success', '2024-12-10 20:24:16'),
(192, 50, '::1', 'success', '2024-12-10 21:44:10'),
(193, 50, '::1', 'success', '2024-12-10 21:50:18'),
(194, 50, '::1', 'success', '2024-12-11 00:05:33'),
(195, 50, '::1', 'success', '2024-12-11 00:17:29'),
(196, 50, '::1', 'success', '2024-12-11 01:44:32'),
(198, 222, '136.158.49.29', 'success', '2024-12-11 23:32:44'),
(199, 222, '136.158.49.29', 'success', '2024-12-11 23:39:06'),
(200, 222, '136.158.49.29', 'success', '2024-12-11 23:43:54'),
(201, 50, '::1', 'success', '2024-12-12 01:46:37'),
(202, 50, '::1', 'success', '2024-12-12 11:49:13'),
(203, 50, '::1', 'success', '2024-12-12 12:52:00'),
(204, 223, '::1', 'success', '2024-12-12 15:46:46'),
(205, 223, '::1', 'success', '2024-12-12 15:49:06'),
(206, 50, '180.194.233.34', 'success', '2024-12-12 16:24:35'),
(207, 228, '180.194.233.34', 'success', '2024-12-12 16:30:41'),
(208, 50, '180.194.233.34', 'success', '2024-12-12 16:45:03'),
(209, 50, '180.194.233.34', 'success', '2024-12-12 16:46:45'),
(210, 50, '180.194.233.34', 'success', '2024-12-12 16:47:23'),
(211, 223, '49.144.12.254', 'success', '2024-12-12 16:47:54'),
(212, 50, '180.194.233.34', 'success', '2024-12-12 16:49:29'),
(213, 222, '136.158.49.29', 'success', '2024-12-14 06:21:43'),
(214, 222, '136.158.49.29', 'success', '2024-12-14 06:53:06'),
(215, 222, '136.158.49.29', 'success', '2024-12-14 09:01:53'),
(216, 222, '136.158.49.29', 'success', '2024-12-14 14:18:22'),
(217, 222, '136.158.49.29', 'success', '2024-12-15 03:53:22'),
(218, 222, '136.158.49.29', 'success', '2024-12-15 03:56:28'),
(219, 222, '136.158.49.29', 'success', '2024-12-15 04:01:59'),
(220, 222, '136.158.49.29', 'success', '2024-12-15 04:08:02'),
(221, 222, '136.158.49.29', 'success', '2024-12-15 04:11:30'),
(222, 222, '136.158.49.29', 'success', '2024-12-15 04:17:51'),
(223, 222, '136.158.49.29', 'success', '2024-12-15 04:20:02'),
(224, 222, '136.158.49.29', 'success', '2024-12-15 04:24:57'),
(225, 222, '136.158.49.29', 'success', '2024-12-15 04:37:18'),
(226, 222, '136.158.49.29', 'success', '2024-12-15 04:51:27'),
(227, 222, '136.158.49.29', 'success', '2024-12-15 05:38:42'),
(228, 222, '136.158.49.29', 'success', '2024-12-15 08:01:55'),
(229, 222, '136.158.49.29', 'success', '2024-12-15 08:10:39'),
(230, 263, '110.54.143.166', 'success', '2024-12-15 08:45:54'),
(231, 222, '136.158.49.29', 'success', '2024-12-15 17:13:15'),
(232, 263, '110.54.143.166', 'success', '2024-12-16 02:07:37'),
(233, 50, '2405:8d40:448c:10fe:1811:51e8:be0:47e8', 'success', '2024-12-16 04:40:45'),
(234, 230, '180.194.233.34', 'success', '2024-12-16 06:02:07'),
(235, 50, '180.194.233.34', 'success', '2024-12-16 14:44:25'),
(236, 50, '175.176.35.25', 'success', '2024-12-16 23:30:33'),
(237, 263, '110.54.142.50', 'success', '2024-12-16 23:53:56'),
(238, 257, '175.176.35.25', 'success', '2024-12-16 23:54:01'),
(239, 234, '216.247.80.1', 'success', '2024-12-16 23:54:19'),
(240, 251, '175.176.35.25', 'success', '2024-12-16 23:54:29'),
(241, 50, '175.176.35.25', 'success', '2024-12-16 23:54:31'),
(242, 256, '2001:fd8:1f04:c3d7:1811:c4bc:6f10:c87', 'success', '2024-12-16 23:55:04'),
(243, 226, '175.176.32.48', 'success', '2024-12-16 23:55:38'),
(244, 228, '216.247.84.164', 'success', '2024-12-16 23:56:01'),
(245, 238, '175.176.35.25', 'success', '2024-12-16 23:56:03'),
(246, 232, '175.176.35.25', 'success', '2024-12-16 23:56:17'),
(247, 245, '175.176.35.25', 'success', '2024-12-16 23:56:36'),
(248, 250, '2405:8d40:4c8d:ee6d:2ca3:8feb:a3ae:9d7f', 'success', '2024-12-16 23:56:48'),
(249, 230, '110.54.142.50', 'success', '2024-12-16 23:56:50'),
(250, 266, '110.54.142.50', 'success', '2024-12-16 23:56:57'),
(251, 266, '110.54.142.50', 'success', '2024-12-16 23:56:58'),
(252, 266, '110.54.142.50', 'success', '2024-12-16 23:57:00'),
(253, 247, '2405:8d40:4c8d:ee6d:9556:28ef:369:2ca4', 'success', '2024-12-16 23:57:21'),
(254, 247, '2405:8d40:4c8d:ee6d:9556:28ef:369:2ca4', 'success', '2024-12-16 23:57:22'),
(255, 247, '2405:8d40:4c8d:ee6d:9556:28ef:369:2ca4', 'success', '2024-12-16 23:57:25'),
(256, 247, '2405:8d40:4c8d:ee6d:9556:28ef:369:2ca4', 'success', '2024-12-16 23:57:25'),
(257, 267, '175.176.35.25', 'success', '2024-12-16 23:57:34'),
(258, 254, '175.176.35.25', 'success', '2024-12-16 23:57:58'),
(259, 252, '2405:8d40:4c8d:ee6d:1811:8b62:d5d8:4e9f', 'success', '2024-12-16 23:58:18'),
(260, 252, '2405:8d40:4c8d:ee6d:1811:8b62:d5d8:4e9f', 'success', '2024-12-16 23:58:18'),
(261, 238, '175.176.35.25', 'success', '2024-12-16 23:58:26'),
(262, 247, '2405:8d40:4c8d:ee6d:9556:28ef:369:2ca4', 'success', '2024-12-16 23:58:53'),
(263, 247, '2405:8d40:4c8d:ee6d:9556:28ef:369:2ca4', 'success', '2024-12-16 23:58:54'),
(264, 247, '2405:8d40:4c8d:ee6d:9556:28ef:369:2ca4', 'success', '2024-12-16 23:58:54'),
(265, 247, '2405:8d40:4c8d:ee6d:9556:28ef:369:2ca4', 'success', '2024-12-16 23:58:56'),
(266, 231, '175.176.32.48', 'success', '2024-12-16 23:59:20'),
(267, 264, '175.176.32.48', 'success', '2024-12-16 23:59:58'),
(268, 248, '175.176.35.25', 'success', '2024-12-17 00:00:28'),
(269, 255, '175.176.35.25', 'success', '2024-12-17 00:00:53'),
(270, 260, '175.176.35.25', 'success', '2024-12-17 00:01:33'),
(271, 239, '175.176.32.48', 'success', '2024-12-17 00:01:36'),
(272, 225, '175.176.35.25', 'success', '2024-12-17 00:01:45'),
(273, 258, '131.226.105.108', 'success', '2024-12-17 00:01:49'),
(274, 265, '2405:8d40:4c8d:ee6d:c880:40fa:8702:7667', 'success', '2024-12-17 00:02:43'),
(275, 259, '131.226.106.73', 'success', '2024-12-17 00:02:49'),
(276, 261, '110.54.142.50', 'success', '2024-12-17 00:03:54'),
(277, 240, '2405:8d40:4881:3b4d:1811:4581:82d0:8e22', 'success', '2024-12-17 00:04:13'),
(278, 224, '2405:8d40:408c:b2f1:ef5a:1a26:88b:b62a', 'success', '2024-12-17 00:05:37'),
(279, 226, '2405:8d40:408c:b2f1:ad22:bdaa:804c:4316', 'success', '2024-12-17 00:07:40'),
(280, 269, '175.176.35.25', 'success', '2024-12-17 00:07:42'),
(281, 265, '175.176.32.48', 'success', '2024-12-17 00:08:13'),
(282, 254, '175.176.35.25', 'success', '2024-12-17 00:08:25'),
(283, 225, '175.176.35.25', 'success', '2024-12-17 00:08:53'),
(284, 253, '175.176.32.48', 'success', '2024-12-17 00:09:57'),
(285, 241, '175.176.32.48', 'success', '2024-12-17 00:09:58'),
(286, 247, '110.54.142.50', 'success', '2024-12-17 00:10:50'),
(287, 251, '175.176.32.48', 'success', '2024-12-17 00:11:44'),
(288, 247, '110.54.142.50', 'success', '2024-12-17 00:13:17'),
(289, 263, '110.54.142.50', 'success', '2024-12-17 00:15:14'),
(290, 228, '216.247.84.164', 'success', '2024-12-17 00:17:13'),
(291, 232, '175.176.35.25', 'success', '2024-12-17 00:17:15'),
(292, 228, '216.247.84.164', 'success', '2024-12-17 00:18:06'),
(293, 50, '175.176.32.48', 'success', '2024-12-17 00:19:27'),
(294, 258, '131.226.105.108', 'success', '2024-12-17 00:20:12'),
(295, 252, '2405:8d40:4c8d:ee6d:1811:8b62:d5d8:4e9f', 'success', '2024-12-17 00:22:33'),
(296, 255, '175.176.35.25', 'success', '2024-12-17 00:27:55');

-- --------------------------------------------------------

--
-- Table structure for table `student_sections`
--

CREATE TABLE `student_sections` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `academic_year_id` int(11) NOT NULL,
  `school_year` varchar(9) NOT NULL,
  `status` enum('active','transferred','graduated','inactive') DEFAULT 'active',
  `enrolled_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_sections`
--

INSERT INTO `student_sections` (`id`, `student_id`, `section_id`, `academic_year_id`, `school_year`, `status`, `enrolled_at`, `created_at`) VALUES
(76, 223, 20, 1, '', 'active', '2024-12-12 15:49:16', '2024-12-12 15:49:16'),
(91, 222, 21, 1, '', 'active', '2024-12-15 07:36:29', '2024-12-15 07:36:29'),
(92, 50, 25, 1, '', 'active', '2024-12-16 14:50:00', '2024-12-16 14:50:00'),
(93, 234, 25, 1, '', 'active', '2024-12-16 23:54:44', '2024-12-16 23:54:44'),
(94, 263, 25, 1, '', 'active', '2024-12-16 23:55:02', '2024-12-16 23:55:02'),
(96, 238, 25, 1, '', 'active', '2024-12-16 23:56:37', '2024-12-16 23:56:37'),
(97, 256, 25, 1, '', 'active', '2024-12-16 23:56:41', '2024-12-16 23:56:41'),
(98, 267, 25, 1, '', 'active', '2024-12-16 23:58:05', '2024-12-16 23:58:05'),
(99, 228, 25, 1, '', 'active', '2024-12-16 23:58:12', '2024-12-16 23:58:12'),
(100, 230, 25, 1, '', 'active', '2024-12-16 23:58:53', '2024-12-16 23:58:53'),
(101, 257, 25, 1, '', 'active', '2024-12-16 23:59:31', '2024-12-16 23:59:31'),
(102, 247, 25, 1, '', 'active', '2024-12-16 23:59:41', '2024-12-16 23:59:41'),
(104, 226, 25, 1, '', 'active', '2024-12-17 00:00:44', '2024-12-17 00:00:44'),
(107, 255, 25, 1, '', 'active', '2024-12-17 00:01:24', '2024-12-17 00:01:24'),
(108, 266, 25, 1, '', 'active', '2024-12-17 00:01:43', '2024-12-17 00:01:43'),
(109, 248, 25, 1, '', 'active', '2024-12-17 00:02:11', '2024-12-17 00:02:11'),
(110, 258, 25, 1, '', 'active', '2024-12-17 00:02:14', '2024-12-17 00:02:14'),
(111, 254, 25, 1, '', 'active', '2024-12-17 00:02:28', '2024-12-17 00:02:28'),
(112, 232, 25, 1, '', 'active', '2024-12-17 00:02:32', '2024-12-17 00:02:32'),
(113, 239, 25, 1, '', 'active', '2024-12-17 00:03:10', '2024-12-17 00:03:10'),
(114, 225, 25, 1, '', 'active', '2024-12-17 00:03:44', '2024-12-17 00:03:44'),
(115, 264, 25, 1, '', 'active', '2024-12-17 00:03:54', '2024-12-17 00:03:54'),
(116, 245, 25, 1, '', 'active', '2024-12-17 00:04:09', '2024-12-17 00:04:09'),
(120, 252, 25, 1, '', 'active', '2024-12-17 00:04:17', '2024-12-17 00:04:17'),
(121, 261, 25, 1, '', 'active', '2024-12-17 00:04:19', '2024-12-17 00:04:19'),
(122, 259, 25, 1, '', 'active', '2024-12-17 00:04:23', '2024-12-17 00:04:23'),
(123, 260, 25, 1, '', 'active', '2024-12-17 00:05:08', '2024-12-17 00:05:08'),
(124, 224, 25, 1, '', 'active', '2024-12-17 00:06:12', '2024-12-17 00:06:12'),
(125, 240, 25, 1, '', 'active', '2024-12-17 00:07:17', '2024-12-17 00:07:17'),
(126, 269, 25, 1, '', 'active', '2024-12-17 00:08:26', '2024-12-17 00:08:26'),
(127, 253, 25, 1, '', 'active', '2024-12-17 00:11:06', '2024-12-17 00:11:06'),
(128, 241, 25, 1, '', 'active', '2024-12-17 00:11:14', '2024-12-17 00:11:14'),
(129, 265, 25, 1, '', 'active', '2024-12-17 00:12:08', '2024-12-17 00:12:08'),
(130, 250, 25, 1, '', 'active', '2024-12-17 00:12:38', '2024-12-17 00:12:38'),
(131, 251, 25, 1, '', 'active', '2024-12-17 00:12:41', '2024-12-17 00:12:41'),
(132, 231, 25, 1, '', 'active', '2024-12-17 00:13:26', '2024-12-17 00:13:26');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `subject_code` varchar(20) NOT NULL,
  `subject_title` varchar(255) NOT NULL,
  `subject_name` varchar(100) NOT NULL,
  `category` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `grade_level` varchar(50) DEFAULT 'All' COMMENT 'Comma-separated list of applicable grade levels',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `subject_code`, `subject_title`, `subject_name`, `category`, `description`, `created_at`, `status`, `grade_level`, `updated_at`) VALUES
(21, 'ENG', 'English', '', 'Minor', 'English', '2024-11-23 16:31:27', 'inactive', 'All', '2024-12-02 03:09:21'),
(22, 'Fil', 'Fil', '', 'Core', 'Fil', '2024-12-01 12:28:59', 'inactive', 'All', '2024-12-02 03:09:18'),
(23, 'Math', 'Math', '', 'Core', 'Math', '2024-12-01 12:29:10', 'active', 'All', '2024-12-01 12:29:10'),
(24, 'Science', 'Science', 'Science', 'Core', 'Science', '2024-12-01 12:29:22', 'active', 'All', '2024-12-15 03:44:15'),
(25, 'Mapeh', 'MAPEH', 'MAPEH', 'Core', '', '2024-12-15 04:58:13', 'active', 'All', '2024-12-15 04:58:39');

-- --------------------------------------------------------

--
-- Table structure for table `subject_grade_levels`
--

CREATE TABLE `subject_grade_levels` (
  `id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `grade_level` enum('7','8','9','10') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subject_grade_levels`
--

INSERT INTO `subject_grade_levels` (`id`, `subject_id`, `grade_level`, `created_at`) VALUES
(2, 21, '7', '2024-11-23 16:31:27'),
(3, 22, '7', '2024-12-01 12:28:59'),
(4, 23, '7', '2024-12-01 12:29:10'),
(5, 24, '7', '2024-12-01 12:29:22'),
(6, 25, '8', '2024-12-15 04:58:13');

-- --------------------------------------------------------

--
-- Table structure for table `submission_files`
--

CREATE TABLE `submission_files` (
  `file_id` int(11) NOT NULL,
  `submission_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(100) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `submission_files`
--

INSERT INTO `submission_files` (`file_id`, `submission_id`, `file_name`, `file_path`, `file_type`, `file_size`, `uploaded_at`, `created_at`) VALUES
(31, 158, 'Student Side.docx', 'uploads/activities/50/67532c4a3dc78_1733504074.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 912716, '2024-12-07 00:54:34', '2024-12-07 00:54:34'),
(32, 159, 'Navy and Broken White Geometric Thesis Defense Presentation.pdf', 'uploads/assignments/50/67532e499866a_1733504585.pdf', 'application/pdf', 7196047, '2024-12-07 01:03:05', '2024-12-07 01:03:05'),
(38, 165, 'DCIT-65A-FINAL-PROJECT.pdf', 'uploads/assignments/50/675331eecd0a5_1733505518.pdf', 'application/pdf', 7298057, '2024-12-07 01:18:38', '2024-12-07 01:18:38'),
(39, 166, 'DCIT-65A-FINAL-PROJECT.pdf', 'uploads/activities/50/6753337cd471c_1733505916.pdf', 'application/pdf', 7298057, '2024-12-07 01:25:16', '2024-12-07 01:25:16'),
(42, 179, 'Doc1.docx', 'uploads/activities/222/675a281e563c3_1733961758.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 13119, '2024-12-12 00:02:38', '2024-12-12 00:02:38'),
(43, 180, '08_Handout_1(16).pdf', 'uploads/activities/50/675a44927f0c8_1733969042.pdf', 'application/pdf', 163941, '2024-12-12 02:04:02', '2024-12-12 02:04:02'),
(44, 194, '06_Handout_1(22).pdf', 'uploads/assignments/223/675b062d255fa_1734018605.pdf', 'application/pdf', 91308, '2024-12-12 15:50:05', '2024-12-12 15:50:05'),
(45, 196, 'Admin Side.docx', 'uploads/activities/50/675b0ee37e7c5_1734020835.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 547784, '2024-12-12 16:27:15', '2024-12-12 16:27:15'),
(46, 197, '675b0ef73f23e_Excuse-Letter-for-Christmas-basket-distribution.pdf', 'uploads/assignments/50/675b0f143d240_1734020884.pdf', 'application/pdf', 242879, '2024-12-12 16:28:04', '2024-12-12 16:28:04'),
(50, 201, 'Doc1.docx', 'uploads/assignments/222/675e4abddb95d_1734232765.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 13119, '2024-12-15 03:19:25', '2024-12-15 03:19:25'),
(51, 202, 'Doc1.docx', 'uploads/activities/222/675e4d1939109_1734233369.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 13119, '2024-12-15 03:29:29', '2024-12-15 03:29:29'),
(53, 205, 'Doc1.docx', 'uploads/assignments/222/675ff79bcca21_1734342555.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 13119, '2024-12-16 09:49:15', '2024-12-16 09:49:15'),
(59, 211, 'Doc1.docx', 'uploads/activities/222/67603be7196c0_1734360039.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 13119, '2024-12-16 14:40:39', '2024-12-16 14:40:39'),
(60, 212, 'Doc1.docx', 'uploads/activities/222/67603c1538ac7_1734360085.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 13119, '2024-12-16 14:41:25', '2024-12-16 14:41:25'),
(61, 216, 'My-Egg-Baby-Performance-Task.pdf', 'uploads/activities/50/6760b915d46b7_1734392085.pdf', 'application/pdf', 506176, '2024-12-16 23:34:45', '2024-12-16 23:34:45'),
(62, 238, 'Research-qusetions-1.docx', 'uploads/activities/239/6760c1056cf17_1734394117.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 29010, '2024-12-17 00:08:37', '2024-12-17 00:08:37');

-- --------------------------------------------------------

--
-- Table structure for table `teacher`
--

CREATE TABLE `teacher` (
  `teacher_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `middlename` varchar(100) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `status` enum('active','inactive','archived') DEFAULT 'active',
  `login_attempts` int(11) DEFAULT 0,
  `lockout_time` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `archived_at` timestamp NULL DEFAULT NULL,
  `archived_by` int(11) DEFAULT NULL,
  `interface_settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`interface_settings`)),
  `teaching_settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`teaching_settings`)),
  `notification_settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`notification_settings`)),
  `is_temporary_password` tinyint(1) DEFAULT 0,
  `department` varchar(100) DEFAULT NULL,
  `password_recovery` enum('yes','no') DEFAULT 'no'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teacher`
--

INSERT INTO `teacher` (`teacher_id`, `username`, `password`, `email`, `profile_image`, `firstname`, `lastname`, `middlename`, `department_id`, `status`, `login_attempts`, `lockout_time`, `created_at`, `updated_at`, `archived_at`, `archived_by`, `interface_settings`, `teaching_settings`, `notification_settings`, `is_temporary_password`, `department`, `password_recovery`) VALUES
(9, 'chaw', '464b57bd033194adbd9ad3e88dd86c8a', 'kingpacifico009@gmail.com', '../uploads/teachers/profile/teacher_9_1732418651.jpg', 'Christiana', 'Pacifico', 'Ilagan', 3, 'active', 0, NULL, '2024-11-23 16:30:23', '2024-12-12 15:48:10', NULL, NULL, '\"{\\\"dark_mode\\\":false,\\\"compact_view\\\":false}\"', '\"{\\\"auto_grading\\\":false,\\\"allow_late\\\":false,\\\"default_deadline\\\":\\\"07:00\\\"}\"', '\"{\\\"email\\\":false,\\\"submissions\\\":false,\\\"deadlines\\\":false}\"', 1, NULL, 'no'),
(123, 'teacher1', '41c8949aa55b8cb5dbec662f34b62df3', 'teacher1@gmail.com', NULL, 'teacher1', 'teacher1', 'teacher1', 4, 'archived', 0, NULL, '2024-12-01 12:05:44', '2024-12-15 04:11:19', '2024-12-15 04:11:19', 1, NULL, NULL, NULL, 0, NULL, 'no'),
(124, 'teacher2', 'ccffb0bb993eeb79059b31e1611ec353', 'teacher2@gmail.com', NULL, 'teacher2', 'teacher2', 'teacher2', 1, 'active', 0, NULL, '2024-12-01 12:06:00', '2024-12-01 12:29:54', NULL, NULL, NULL, NULL, NULL, 0, NULL, 'no'),
(125, 'teacher3', '82470256ea4b80343b27afccbca1015b', 'teacher3@gmail.com', NULL, 'teacher3', 'teacher3', 'teacher3', 2, 'active', 0, NULL, '2024-12-01 12:06:17', '2024-12-01 12:30:12', NULL, NULL, NULL, NULL, NULL, 0, NULL, 'no'),
(126, 'Sharmaine', '44b3be36ab75d17be9eb9d4acf6f9f97', 'sharmainep@gmail.com', NULL, 'Sharmaine', 'Diasnes', 'P', 1, 'active', 0, NULL, '2024-12-11 15:36:35', '2024-12-11 15:36:35', NULL, NULL, NULL, NULL, NULL, 0, NULL, 'no'),
(127, 'ahji234', 'ebe96d6963a04711b2b2bc8d663b5b81', 'Ahjilee234@gmail.com', NULL, 'Ahji', 'Lee', 'Chu', 6, 'archived', 0, NULL, '2024-12-11 23:37:39', '2024-12-15 04:20:41', '2024-12-15 04:20:41', 1, NULL, NULL, NULL, 0, NULL, 'no'),
(128, 'Ahji', '65d314fc27340fc55b42273b982c1caa', 'lheno@gmail.com', NULL, 'Ahji', 'Lheno', 'Ji', 6, 'active', 0, NULL, '2024-12-15 04:22:23', '2024-12-15 04:22:23', NULL, NULL, NULL, NULL, NULL, 0, NULL, 'no'),
(129, 'mike', '441d3d1c7ee0c689e64a88d325c8605b', 'mikelee12@gmail.com', NULL, 'Mike', 'Lee', '', 6, 'active', 0, NULL, '2024-12-15 04:56:25', '2024-12-15 04:56:25', NULL, NULL, NULL, NULL, NULL, 0, NULL, 'no'),
(130, 'Mirah', 'ae1f3e65dd4bed40980845f882df3228', 'mirah.diones@deped.gov.ph', NULL, 'mirah doloara', 'Diones', '', 6, 'active', 0, NULL, '2024-12-16 23:23:39', '2024-12-16 23:23:39', NULL, NULL, NULL, NULL, NULL, 0, NULL, 'no');

-- --------------------------------------------------------

--
-- Table structure for table `teacher_login_logs`
--

CREATE TABLE `teacher_login_logs` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `status` enum('success','failed','logout') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teacher_login_logs`
--

INSERT INTO `teacher_login_logs` (`id`, `teacher_id`, `ip_address`, `status`, `created_at`) VALUES
(82, 9, '::1', 'success', '2024-12-03 19:47:03'),
(83, 9, '::1', 'success', '2024-12-04 01:17:04'),
(84, 9, '::1', 'success', '2024-12-06 03:39:12'),
(85, 9, '::1', 'success', '2024-12-06 04:56:11'),
(86, 9, '49.144.12.254', 'success', '2024-12-09 01:40:09'),
(87, 9, '180.194.233.34', 'success', '2024-12-09 01:46:20'),
(88, 9, '180.194.233.34', 'success', '2024-12-09 02:12:01'),
(89, 9, '175.176.36.21', 'success', '2024-12-09 11:13:58'),
(90, 9, '175.176.36.21', 'success', '2024-12-09 11:16:03'),
(91, 9, '175.176.36.21', 'success', '2024-12-09 11:16:57'),
(92, 9, '175.176.36.21', 'success', '2024-12-09 12:44:39'),
(93, 9, '136.158.49.167', 'success', '2024-12-09 13:58:46'),
(94, 9, '136.158.49.167', 'success', '2024-12-09 14:02:40'),
(95, 9, '103.91.141.65', 'success', '2024-12-09 15:37:36'),
(96, 9, '::1', 'success', '2024-12-10 19:39:00'),
(97, 9, '::1', 'success', '2024-12-10 20:02:51'),
(98, 9, '::1', 'logout', '2024-12-10 20:02:53'),
(99, 9, '::1', 'success', '2024-12-10 20:04:31'),
(100, 9, '::1', 'logout', '2024-12-10 20:04:42'),
(101, 9, '::1', 'success', '2024-12-10 20:14:07'),
(102, 9, '::1', 'logout', '2024-12-10 20:17:58'),
(103, 9, '::1', 'success', '2024-12-10 20:43:29'),
(104, 9, '::1', 'logout', '2024-12-10 20:43:31'),
(105, 9, '::1', 'success', '2024-12-10 20:59:44'),
(106, 9, '::1', 'logout', '2024-12-10 20:59:47'),
(107, 9, '::1', 'success', '2024-12-10 21:41:52'),
(108, 9, '::1', 'logout', '2024-12-10 21:41:54'),
(109, 9, '::1', 'success', '2024-12-10 21:44:33'),
(110, 9, '::1', 'logout', '2024-12-10 21:44:35'),
(111, 9, '::1', 'logout', '2024-12-10 22:03:08'),
(112, 9, '::1', 'logout', '2024-12-10 22:09:11'),
(113, 9, '::1', 'logout', '2024-12-10 22:11:09'),
(114, 9, '::1', 'success', '2024-12-10 22:11:20'),
(115, 9, '::1', 'success', '2024-12-10 22:13:22'),
(116, 9, '::1', 'success', '2024-12-11 00:20:25'),
(117, 126, '180.194.233.34', 'success', '2024-12-11 17:31:01'),
(118, 127, '136.158.49.29', 'success', '2024-12-11 23:38:05'),
(119, 9, '::1', 'success', '2024-12-12 01:17:07'),
(120, 9, '::1', 'success', '2024-12-12 01:26:26'),
(121, 9, '::1', 'success', '2024-12-12 01:41:32'),
(122, 9, '180.194.233.34', 'success', '2024-12-12 16:22:13'),
(123, 126, '180.194.233.34', 'success', '2024-12-12 16:31:42'),
(124, 127, '136.158.49.29', 'success', '2024-12-15 03:33:43'),
(125, 9, '136.158.49.29', 'success', '2024-12-15 03:50:40'),
(126, 128, '136.158.49.29', 'success', '2024-12-15 04:23:26'),
(127, 128, '136.158.49.29', 'success', '2024-12-15 04:41:02'),
(128, 9, '136.158.49.29', 'success', '2024-12-15 04:51:09'),
(129, 9, '136.158.49.29', 'success', '2024-12-15 04:52:16'),
(130, 129, '136.158.49.29', 'success', '2024-12-15 05:01:49'),
(131, 129, '136.158.49.29', 'success', '2024-12-15 05:06:07'),
(132, 128, '136.158.49.29', 'success', '2024-12-16 03:18:40'),
(133, 129, '136.158.49.29', 'success', '2024-12-16 03:21:58'),
(134, 9, '58.69.144.207', 'success', '2024-12-16 04:39:29'),
(135, 128, '136.158.49.29', 'success', '2024-12-16 05:19:51'),
(136, 128, '136.158.49.29', 'success', '2024-12-16 14:08:07'),
(137, 129, '136.158.49.29', 'success', '2024-12-16 14:08:31'),
(138, 128, '136.158.49.29', 'success', '2024-12-16 14:43:45'),
(139, 126, '180.194.233.34', 'success', '2024-12-16 14:49:48'),
(140, 126, '175.176.32.48', 'success', '2024-12-16 23:16:24'),
(141, 126, '175.176.32.48', 'success', '2024-12-16 23:19:25'),
(142, 130, '175.176.35.25', 'success', '2024-12-16 23:26:02'),
(143, 130, '175.176.35.25', 'success', '2024-12-16 23:55:33'),
(144, 130, '175.176.35.25', 'success', '2024-12-16 23:58:56'),
(145, 126, '180.194.233.34', 'success', '2024-12-17 01:57:29');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `about_us_content`
--
ALTER TABLE `about_us_content`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `academic_years`
--
ALTER TABLE `academic_years`
  ADD PRIMARY KEY (`id`);

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
  ADD KEY `idx_teacher_type` (`teacher_id`,`type`),
  ADD KEY `idx_teacher_recent` (`teacher_id`,`created_at`),
  ADD KEY `idx_teacher_activities` (`teacher_id`,`created_at`),
  ADD KEY `idx_section_subject` (`section_subject_id`);

--
-- Indexes for table `activity_files`
--
ALTER TABLE `activity_files`
  ADD PRIMARY KEY (`file_id`),
  ADD KEY `activity_id` (`activity_id`);

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
-- Indexes for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `section_id` (`section_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `idx_teacher_recent` (`teacher_id`,`created_at`),
  ADD KEY `idx_teacher_announcements` (`teacher_id`,`created_at`);

--
-- Indexes for table `announcement_comments`
--
ALTER TABLE `announcement_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `announcement_id` (`announcement_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `archive_academic_years`
--
ALTER TABLE `archive_academic_years`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_archived_by` (`archived_by`),
  ADD KEY `fk_restored_by` (`restored_by`);

--
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`assignment_id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `idx_teacher_due_date` (`teacher_id`,`due_date`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_attendance` (`student_id`,`section_subject_id`,`date`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `idx_attendance_section_subject` (`section_subject_id`),
  ADD KEY `idx_attendance_status` (`status`),
  ADD KEY `idx_attendance_date` (`date`);

--
-- Indexes for table `attendance_logs`
--
ALTER TABLE `attendance_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_logs_attendance` (`attendance_id`),
  ADD KEY `fk_logs_teacher` (`teacher_id`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`class_id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `contact_information`
--
ALTER TABLE `contact_information`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `date_ranges`
--
ALTER TABLE `date_ranges`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`department_id`),
  ADD UNIQUE KEY `department_code` (`department_code`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reference_id` (`reference_id`),
  ADD KEY `notifications_announcement_fk` (`announcement_id`),
  ADD KEY `fk_notifications_activity` (`activity_id`);

--
-- Indexes for table `question_choices`
--
ALTER TABLE `question_choices`
  ADD PRIMARY KEY (`choice_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `quiz_access_codes`
--
ALTER TABLE `quiz_access_codes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_access_code` (`access_code`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indexes for table `quiz_answers`
--
ALTER TABLE `quiz_answers`
  ADD PRIMARY KEY (`answer_id`),
  ADD KEY `fk_quiz_answers_question` (`question_id`);

--
-- Indexes for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD PRIMARY KEY (`attempt_id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indexes for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  ADD PRIMARY KEY (`question_id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indexes for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `report_history`
--
ALTER TABLE `report_history`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`section_id`),
  ADD UNIQUE KEY `section_name` (`section_name`),
  ADD KEY `adviser_id` (`adviser_id`);

--
-- Indexes for table `section_advisers`
--
ALTER TABLE `section_advisers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `section_id` (`section_id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `academic_year_id` (`academic_year_id`);

--
-- Indexes for table `section_schedules`
--
ALTER TABLE `section_schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `section_id` (`section_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `academic_year_id` (`academic_year_id`);

--
-- Indexes for table `section_subjects`
--
ALTER TABLE `section_subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `enrollment_code` (`enrollment_code`),
  ADD KEY `section_id` (`section_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `academic_year_id` (`academic_year_id`);

--
-- Indexes for table `security_violations`
--
ALTER TABLE `security_violations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_student_id` (`student_id`),
  ADD KEY `idx_quiz_id` (`quiz_id`);

--
-- Indexes for table `site_map_content`
--
ALTER TABLE `site_map_content`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `lrn_unique` (`lrn`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_last_activity` (`last_activity`);

--
-- Indexes for table `student_activity_submissions`
--
ALTER TABLE `student_activity_submissions`
  ADD PRIMARY KEY (`submission_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `idx_activity_submitted` (`activity_id`,`submitted_at`),
  ADD KEY `idx_activity_submissions` (`activity_id`,`submitted_at`),
  ADD KEY `fk_graded_by_teacher` (`graded_by`);

--
-- Indexes for table `student_answers`
--
ALTER TABLE `student_answers`
  ADD PRIMARY KEY (`answer_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `quiz_id` (`quiz_id`),
  ADD KEY `question_id` (`question_id`),
  ADD KEY `selected_choice_id` (`selected_choice_id`),
  ADD KEY `idx_student_quiz` (`student_id`,`quiz_id`),
  ADD KEY `idx_student_quiz_answers` (`student_id`,`quiz_id`,`question_id`);

--
-- Indexes for table `student_answers_old`
--
ALTER TABLE `student_answers_old`
  ADD PRIMARY KEY (`answer_id`),
  ADD KEY `quiz_id` (`quiz_id`),
  ADD KEY `question_id` (`question_id`),
  ADD KEY `selected_choice_id` (`selected_choice_id`);

--
-- Indexes for table `student_grades`
--
ALTER TABLE `student_grades`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_student_subject` (`student_id`,`section_subject_id`);

--
-- Indexes for table `student_login_logs`
--
ALTER TABLE `student_login_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `student_sections`
--
ALTER TABLE `student_sections`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_student_year` (`student_id`,`school_year`),
  ADD KEY `section_id` (`section_id`),
  ADD KEY `academic_year_id` (`academic_year_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `subject_code` (`subject_code`);

--
-- Indexes for table `subject_grade_levels`
--
ALTER TABLE `subject_grade_levels`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `submission_files`
--
ALTER TABLE `submission_files`
  ADD PRIMARY KEY (`file_id`),
  ADD KEY `submission_id` (`submission_id`);

--
-- Indexes for table `teacher`
--
ALTER TABLE `teacher`
  ADD PRIMARY KEY (`teacher_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `teacher_login_logs`
--
ALTER TABLE `teacher_login_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `about_us_content`
--
ALTER TABLE `about_us_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `academic_years`
--
ALTER TABLE `academic_years`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `active_sessions`
--
ALTER TABLE `active_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `activity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=175;

--
-- AUTO_INCREMENT for table `activity_files`
--
ALTER TABLE `activity_files`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admin_login_logs`
--
ALTER TABLE `admin_login_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=162;

--
-- AUTO_INCREMENT for table `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT for table `announcement_comments`
--
ALTER TABLE `announcement_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `archive_academic_years`
--
ALTER TABLE `archive_academic_years`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `assignment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT for table `attendance_logs`
--
ALTER TABLE `attendance_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `class_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contact_information`
--
ALTER TABLE `contact_information`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `date_ranges`
--
ALTER TABLE `date_ranges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `department_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=209;

--
-- AUTO_INCREMENT for table `question_choices`
--
ALTER TABLE `question_choices`
  MODIFY `choice_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=298;

--
-- AUTO_INCREMENT for table `quiz_access_codes`
--
ALTER TABLE `quiz_access_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `quiz_answers`
--
ALTER TABLE `quiz_answers`
  MODIFY `answer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  MODIFY `attempt_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=171;

--
-- AUTO_INCREMENT for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `report_history`
--
ALTER TABLE `report_history`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `section_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `section_advisers`
--
ALTER TABLE `section_advisers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `section_schedules`
--
ALTER TABLE `section_schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `section_subjects`
--
ALTER TABLE `section_subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `security_violations`
--
ALTER TABLE `security_violations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=161;

--
-- AUTO_INCREMENT for table `site_map_content`
--
ALTER TABLE `site_map_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `student_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=270;

--
-- AUTO_INCREMENT for table `student_activity_submissions`
--
ALTER TABLE `student_activity_submissions`
  MODIFY `submission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=280;

--
-- AUTO_INCREMENT for table `student_answers`
--
ALTER TABLE `student_answers`
  MODIFY `answer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=580;

--
-- AUTO_INCREMENT for table `student_answers_old`
--
ALTER TABLE `student_answers_old`
  MODIFY `answer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `student_grades`
--
ALTER TABLE `student_grades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_login_logs`
--
ALTER TABLE `student_login_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=297;

--
-- AUTO_INCREMENT for table `student_sections`
--
ALTER TABLE `student_sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=133;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `subject_grade_levels`
--
ALTER TABLE `subject_grade_levels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `submission_files`
--
ALTER TABLE `submission_files`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `teacher`
--
ALTER TABLE `teacher`
  MODIFY `teacher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=131;

--
-- AUTO_INCREMENT for table `teacher_login_logs`
--
ALTER TABLE `teacher_login_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=146;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activities`
--
ALTER TABLE `activities`
  ADD CONSTRAINT `fk_activities_section_subject` FOREIGN KEY (`section_subject_id`) REFERENCES `section_subjects` (`id`);

--
-- Constraints for table `activity_files`
--
ALTER TABLE `activity_files`
  ADD CONSTRAINT `activity_files_ibfk_1` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`activity_id`) ON DELETE CASCADE;

--
-- Constraints for table `admin_login_logs`
--
ALTER TABLE `admin_login_logs`
  ADD CONSTRAINT `admin_login_logs_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`admin_id`);

--
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `announcements_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`teacher_id`),
  ADD CONSTRAINT `announcements_ibfk_2` FOREIGN KEY (`section_id`) REFERENCES `sections` (`section_id`),
  ADD CONSTRAINT `announcements_ibfk_3` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`);

--
-- Constraints for table `announcement_comments`
--
ALTER TABLE `announcement_comments`
  ADD CONSTRAINT `announcement_comments_ibfk_1` FOREIGN KEY (`announcement_id`) REFERENCES `announcements` (`id`);

--
-- Constraints for table `archive_academic_years`
--
ALTER TABLE `archive_academic_years`
  ADD CONSTRAINT `fk_archived_by` FOREIGN KEY (`archived_by`) REFERENCES `admin` (`admin_id`),
  ADD CONSTRAINT `fk_restored_by` FOREIGN KEY (`restored_by`) REFERENCES `admin` (`admin_id`);

--
-- Constraints for table `assignments`
--
ALTER TABLE `assignments`
  ADD CONSTRAINT `assignments_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`teacher_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assignments_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`) ON DELETE CASCADE;

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_3` FOREIGN KEY (`section_subject_id`) REFERENCES `section_subjects` (`id`);

--
-- Constraints for table `attendance_logs`
--
ALTER TABLE `attendance_logs`
  ADD CONSTRAINT `fk_logs_attendance` FOREIGN KEY (`attendance_id`) REFERENCES `attendance` (`id`),
  ADD CONSTRAINT `fk_logs_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`teacher_id`);

--
-- Constraints for table `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `classes_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`teacher_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `classes_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notifications_activity` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`activity_id`),
  ADD CONSTRAINT `notifications_announcement_fk` FOREIGN KEY (`announcement_id`) REFERENCES `announcements` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `question_choices`
--
ALTER TABLE `question_choices`
  ADD CONSTRAINT `fk_question_choices_question` FOREIGN KEY (`question_id`) REFERENCES `quiz_questions` (`question_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `question_choices_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `quiz_questions` (`question_id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_access_codes`
--
ALTER TABLE `quiz_access_codes`
  ADD CONSTRAINT `quiz_access_codes_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `activities` (`activity_id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_answers`
--
ALTER TABLE `quiz_answers`
  ADD CONSTRAINT `fk_quiz_answers_question` FOREIGN KEY (`question_id`) REFERENCES `quiz_questions` (`question_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quiz_answers_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `quiz_questions` (`question_id`);

--
-- Constraints for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD CONSTRAINT `quiz_attempts_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `activities` (`activity_id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  ADD CONSTRAINT `quiz_questions_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `activities` (`activity_id`) ON DELETE CASCADE;

--
-- Constraints for table `report_history`
--
ALTER TABLE `report_history`
  ADD CONSTRAINT `report_history_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`teacher_id`);

--
-- Constraints for table `sections`
--
ALTER TABLE `sections`
  ADD CONSTRAINT `sections_ibfk_1` FOREIGN KEY (`adviser_id`) REFERENCES `teacher` (`teacher_id`) ON DELETE SET NULL;

--
-- Constraints for table `section_advisers`
--
ALTER TABLE `section_advisers`
  ADD CONSTRAINT `section_advisers_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `sections` (`section_id`),
  ADD CONSTRAINT `section_advisers_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`teacher_id`),
  ADD CONSTRAINT `section_advisers_ibfk_3` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`);

--
-- Constraints for table `section_schedules`
--
ALTER TABLE `section_schedules`
  ADD CONSTRAINT `section_schedules_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `sections` (`section_id`),
  ADD CONSTRAINT `section_schedules_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
  ADD CONSTRAINT `section_schedules_ibfk_3` FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`teacher_id`),
  ADD CONSTRAINT `section_schedules_ibfk_4` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`);

--
-- Constraints for table `section_subjects`
--
ALTER TABLE `section_subjects`
  ADD CONSTRAINT `fk_academic_year` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`),
  ADD CONSTRAINT `section_subjects_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `sections` (`section_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `section_subjects_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `section_subjects_ibfk_3` FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`teacher_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `section_subjects_ibfk_4` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `security_violations`
--
ALTER TABLE `security_violations`
  ADD CONSTRAINT `fk_security_violations_quiz` FOREIGN KEY (`quiz_id`) REFERENCES `activities` (`activity_id`),
  ADD CONSTRAINT `fk_security_violations_student` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`);

--
-- Constraints for table `student_activity_submissions`
--
ALTER TABLE `student_activity_submissions`
  ADD CONSTRAINT `fk_graded_by_teacher` FOREIGN KEY (`graded_by`) REFERENCES `teacher` (`teacher_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `student_activity_submissions_ibfk_2` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`activity_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_answers`
--
ALTER TABLE `student_answers`
  ADD CONSTRAINT `fk_student_answers_choice` FOREIGN KEY (`selected_choice_id`) REFERENCES `question_choices` (`choice_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_student_answers_question` FOREIGN KEY (`question_id`) REFERENCES `quiz_questions` (`question_id`),
  ADD CONSTRAINT `fk_student_answers_quiz` FOREIGN KEY (`quiz_id`) REFERENCES `activities` (`activity_id`),
  ADD CONSTRAINT `fk_student_answers_student` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`);

--
-- Constraints for table `student_sections`
--
ALTER TABLE `student_sections`
  ADD CONSTRAINT `student_sections_ibfk_2` FOREIGN KEY (`section_id`) REFERENCES `sections` (`section_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_sections_ibfk_3` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`);

--
-- Constraints for table `subject_grade_levels`
--
ALTER TABLE `subject_grade_levels`
  ADD CONSTRAINT `fk_subject_grade_levels_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `submission_files`
--
ALTER TABLE `submission_files`
  ADD CONSTRAINT `submission_files_ibfk_1` FOREIGN KEY (`submission_id`) REFERENCES `student_activity_submissions` (`submission_id`) ON DELETE CASCADE;

--
-- Constraints for table `teacher`
--
ALTER TABLE `teacher`
  ADD CONSTRAINT `teacher_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`);

--
-- Constraints for table `teacher_login_logs`
--
ALTER TABLE `teacher_login_logs`
  ADD CONSTRAINT `teacher_login_logs_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`teacher_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
