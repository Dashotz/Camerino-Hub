-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 10, 2024 at 03:14 PM
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
-- Database: `frncszxc_camerinohub1`
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
(1, '2024-2025', '2024-06-01', '2025-03-31', 'active', '2024-11-06 19:28:47', 0, NULL);

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
(92, 9, 46, 'qweqwe', 'asdasdasd', NULL, 'assignment', 100, NULL, NULL, 0, 0, 1, 0, '2024-12-02 03:39:00', '2024-11-24 19:39:29', '2024-12-09 03:17:41', 'archived', 0.00),
(93, 123, 53, 'asd', 'asd', NULL, 'activity', 100, NULL, NULL, 0, 0, 1, 0, '2024-12-08 05:51:00', '2024-12-01 04:51:51', '2024-12-01 04:51:51', 'active', 0.00),
(94, 9, 56, 'ChawCHawasdasd', 'asd', NULL, 'activity', 0, NULL, NULL, 0, 0, 1, 0, '2024-12-08 05:59:00', '2024-12-01 04:59:47', '2024-12-09 03:17:38', 'archived', 0.00),
(95, 124, 54, 'asd', 'asd', NULL, 'activity', 100, NULL, NULL, 0, 0, 1, 0, '2024-12-08 06:41:00', '2024-12-01 05:41:19', '2024-12-01 05:41:19', 'active', 0.00),
(96, 124, 54, 'asd', 'asd', NULL, 'quiz', 100, 'https://docs.google.com/forms/d/e/1FAIpQLSc3FbCr-qgYgxCClauhotJifydnFffW2sEu2aHrVNjHdmXzrw/viewform?usp=sf_link&embedded=true', 60, 1, 1, 1, 0, '2024-12-01 14:42:00', '2024-12-01 05:42:29', '2024-12-01 05:42:29', 'active', 0.00),
(113, 9, 56, 'sdas', 'dasda', NULL, 'quiz', 3, NULL, 60, 0, 0, 1, 0, '2024-12-05 00:24:00', '2024-12-03 16:24:56', '2024-12-09 03:17:37', 'archived', 0.00),
(114, 9, 56, 'Q', 'Q', NULL, 'quiz', 3, NULL, 60, 0, 0, 1, 0, '2024-12-05 01:37:00', '2024-12-03 17:39:32', '2024-12-09 03:17:35', 'archived', 0.00),
(115, 9, 56, 'q', 'q', NULL, 'quiz', 3, NULL, 60, 0, 0, 1, 0, '2024-12-05 01:39:00', '2024-12-03 17:39:57', '2024-12-09 03:17:33', 'archived', 0.00),
(116, 9, 56, 'a', 'a', NULL, 'quiz', 3, NULL, 60, 0, 0, 1, 0, '2024-12-05 01:41:00', '2024-12-03 17:42:03', '2024-12-09 03:17:31', 'archived', 0.00),
(124, 9, 56, 'a', 'a', NULL, 'quiz', 3, NULL, 60, 0, 0, 1, 0, '2024-12-05 01:50:00', '2024-12-03 17:51:08', '2024-12-09 03:17:29', 'archived', 0.00),
(125, 9, 56, 'TTest', 'test', NULL, 'quiz', 3, NULL, 60, 0, 0, 1, 0, '2024-12-07 03:39:00', '2024-12-05 19:39:52', '2024-12-09 03:17:27', 'archived', 0.00),
(128, 9, 56, 'testtt', 'testt', NULL, 'quiz', 3, NULL, 60, 0, 0, 1, 0, '2024-12-07 05:06:00', '2024-12-05 21:06:12', '2024-12-05 22:00:35', 'archived', 0.00),
(131, 9, 56, 'test', 'test', NULL, 'quiz', 3, NULL, 60, 0, 0, 1, 0, '2024-12-07 06:03:00', '2024-12-05 22:03:12', '2024-12-06 07:58:25', 'archived', 0.00),
(135, 9, 56, 'test', 'test', NULL, 'quiz', 3, NULL, 60, 0, 0, 1, 0, '2024-12-07 06:07:00', '2024-12-05 22:07:43', '2024-12-06 07:58:23', 'archived', 0.00),
(136, 9, 56, 'testtasd', 'testasd', NULL, 'quiz', 11, NULL, 60, 0, 0, 1, 0, '2024-12-07 07:24:00', '2024-12-05 23:24:57', '2024-12-06 07:58:21', 'archived', 0.00),
(137, 9, 56, 'testasdasd', 'asdads', NULL, 'quiz', 2, NULL, 60, 0, 0, 1, 0, '2024-12-07 15:20:00', '2024-12-06 07:21:04', '2024-12-09 03:17:25', 'archived', 0.00),
(138, 9, 56, 'ASDASD', 'ASDASD', NULL, 'quiz', 3, NULL, 60, 0, 0, 1, 0, '2024-12-07 15:58:00', '2024-12-06 07:58:55', '2024-12-06 09:26:58', 'archived', 0.00),
(139, 9, 56, 'SHORT', 'SHORT', NULL, 'quiz', 3, NULL, 60, 0, 0, 1, 0, '2024-12-07 17:27:00', '2024-12-06 09:27:30', '2024-12-09 03:17:23', 'archived', 0.00),
(140, 9, 56, 'NEW', 'NEW', NULL, 'quiz', 3, NULL, 60, 0, 0, 1, 0, '2024-12-07 17:47:00', '2024-12-06 09:47:47', '2024-12-09 03:17:21', 'archived', 0.00),
(141, 9, 56, 'SHOSSSS', 'SSADASD', NULL, 'quiz', 3, NULL, 60, 0, 0, 1, 0, '2024-12-07 18:41:00', '2024-12-06 10:41:43', '2024-12-09 03:17:19', 'archived', 0.00),
(142, 9, 56, 'XZC', 'ZXC', NULL, 'quiz', 5, NULL, 60, 0, 0, 1, 0, '2024-12-08 00:09:00', '2024-12-06 16:09:50', '2024-12-09 03:17:17', 'archived', 0.00),
(143, 9, 56, 'FRRRR', 'FRRR', NULL, 'assignment', 100, NULL, NULL, 0, 0, 1, 0, '2024-12-14 01:02:00', '2024-12-06 17:02:52', '2024-12-09 03:17:15', 'archived', 0.00),
(145, 9, 56, 'asdasd', 'asdad', NULL, 'assignment', 100, NULL, NULL, 0, 0, 1, 0, '2024-12-14 01:16:00', '2024-12-06 17:16:06', '2024-12-09 03:17:14', 'archived', 0.00),
(146, 9, 56, 'web', 'web', NULL, 'quiz', 7, NULL, 60, 0, 0, 1, 0, '2024-12-10 01:37:00', '2024-12-08 17:37:51', '2024-12-09 03:17:11', 'archived', 0.00),
(147, 9, 56, 'Test TEST', 'Test', NULL, 'quiz', 3, NULL, 1, 0, 0, 1, 0, '2024-12-10 01:44:00', '2024-12-08 17:44:42', '2024-12-09 03:17:10', 'archived', 0.00),
(148, 9, 56, 'points', 'points', NULL, 'quiz', 3, NULL, 60, 0, 0, 1, 0, '2024-12-10 02:03:00', '2024-12-08 18:03:38', '2024-12-09 03:17:08', 'archived', 0.00),
(149, 9, 56, 'pacifoc', 'pacifoc', NULL, 'quiz', 3, NULL, 60, 0, 0, 1, 0, '2024-12-10 02:13:00', '2024-12-08 18:13:09', '2024-12-09 03:17:06', 'archived', 0.00),
(150, 9, 56, 'Hahaha', 'Gahaga', NULL, 'quiz', 3, NULL, 60, 0, 0, 1, 0, '2024-12-10 11:14:00', '2024-12-09 03:15:10', '2024-12-09 03:17:03', 'archived', 0.00),
(151, 9, 56, 'quiz 1', 'Description instruction \r\nNot case sensitive\r\netc \r\nEtc', NULL, 'quiz', 4, NULL, 10, 0, 0, 1, 0, '2024-12-10 12:56:00', '2024-12-09 04:56:56', '2024-12-09 04:56:56', 'active', 0.00),
(152, 9, 56, 'Activity 2 MIDTERM LAB', 'Midterm Lab / ACTIVITY 2', NULL, 'activity', 0, NULL, NULL, 0, 0, 1, 0, '2024-12-20 23:59:00', '2024-12-09 06:04:56', '2024-12-09 07:08:59', 'active', 0.00),
(154, 9, 56, 'QUIZ NO 1', 'MULTIPLE CHOICE', NULL, 'quiz', 1, NULL, 20, 1, 0, 1, 0, '2024-12-16 23:00:00', '2024-12-09 08:13:59', '2024-12-09 08:13:59', 'active', 0.00),
(155, 9, 56, 'Activity: Build a Simple Web Page', 'ACTIVTIY 1&2', NULL, 'activity', 0, NULL, NULL, 0, 0, 1, 0, '2024-12-16 23:59:00', '2024-12-09 08:23:39', '2024-12-09 08:26:09', 'active', 0.00);

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
(29, 92, 'Christian Pacificologo (1).docx', 'uploads/activities/1732502369_6743e361ae95a_6c17f46427645a2e.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 139228, '2024-11-25 02:39:29'),
(30, 93, 'Survey Questionnaire.pdf', 'uploads/activities/1733028711_674beb6722249_b185f17fa6913ffd.pdf', 'application/pdf', 119917, '2024-12-01 04:51:51'),
(31, 94, 'Survey Questionnaire.pdf', 'uploads/activities/1733029187_674bed43c2443_b202bf591659d994.pdf', 'application/pdf', 119917, '2024-12-01 04:59:47'),
(32, 143, 'DCIT-65A-FINAL-PROJECT.pdf', 'uploads/activities/1733504572_67532e3c47429_75612306b6bbf9cc.pdf', 'application/pdf', 7298057, '2024-12-06 17:02:52'),
(34, 145, 'DCIT-65A-FINAL-PROJECT.pdf', 'uploads/activities/1733505366_675331569c2c7_08b031c8017791b2.pdf', 'application/pdf', 7298057, '2024-12-06 17:16:06');

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
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'admin@camerinohub.edu.ph', 'System', 'Administrator', 'active', '2024-11-06 15:41:01');

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
(81, 1, '::1', 'success', '2024-12-01 19:08:48'),
(82, 1, '::1', 'success', '2024-12-01 20:21:44'),
(83, 1, '::1', 'success', '2024-12-02 11:05:35'),
(84, 1, '::1', 'logout', '2024-12-02 11:18:09'),
(85, 1, '::1', 'success', '2024-12-02 11:18:12'),
(86, 1, '::1', 'success', '2024-12-03 17:16:18'),
(87, 1, '175.176.36.21', 'success', '2024-12-09 05:15:02'),
(88, 1, '::1', 'success', '2024-12-10 11:40:53'),
(89, 1, '::1', 'logout', '2024-12-10 11:40:57'),
(90, 1, '::1', 'success', '2024-12-10 12:02:57'),
(91, 1, '::1', 'logout', '2024-12-10 12:02:59'),
(92, 1, '::1', 'success', '2024-12-10 12:04:47'),
(93, 1, '::1', 'logout', '2024-12-10 12:04:50'),
(94, 1, '::1', 'success', '2024-12-10 12:18:04'),
(95, 1, '::1', 'logout', '2024-12-10 12:18:07'),
(96, 1, '::1', 'success', '2024-12-10 13:44:21'),
(97, 1, '::1', 'logout', '2024-12-10 13:44:26');

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
(50, 9, 20, 23, 'A new quiz has been posted: asd\nDue date: 2024-12-05T20:13\nTotal Points: 1', NULL, 'New Quiz: asd', NULL, 'active', '2024-12-03 12:14:48', 'quiz', NULL, NULL, 'medium', 101),
(51, 9, 20, 23, 'A new quiz has been posted: test1t\nDue date: 2024-12-04T20:25\nTotal Points: 1', NULL, 'New Quiz: test1t', NULL, 'active', '2024-12-03 12:26:13', 'quiz', NULL, NULL, 'medium', 102),
(52, 9, 20, 23, 'A new quiz has been posted: Test\nDue date: 2024-12-04T20:44\nTotal Points: 1', NULL, 'New Quiz: Test', NULL, 'active', '2024-12-03 12:44:54', 'quiz', NULL, NULL, 'medium', 103),
(53, 9, 20, 23, 'A new quiz has been posted: Short answer\nDue date: 2024-12-05T20:48\nTotal Points: 1', NULL, 'New Quiz: Short answer', NULL, 'active', '2024-12-03 12:48:39', 'quiz', NULL, NULL, 'medium', 104),
(54, 9, 20, 23, 'A new quiz has been posted: Test\nDue date: 2024-12-13T20:59\nTotal Points: 3', NULL, 'New Quiz: Test', NULL, 'active', '2024-12-03 13:00:12', 'quiz', NULL, NULL, 'medium', 105),
(55, 9, 20, 23, 'A new quiz has been posted: qwe\nDue date: 2024-12-24T21:11\nTotal Points: 1', NULL, 'New Quiz: qwe', NULL, 'active', '2024-12-03 13:11:18', 'quiz', NULL, NULL, 'medium', 106),
(56, 9, 20, 23, 'A new quiz has been posted: asd\nDue date: 2024-12-04T23:14\nTotal Points: 1', NULL, 'New Quiz: asd', NULL, 'active', '2024-12-03 15:14:53', 'quiz', NULL, NULL, 'medium', 107),
(57, 9, 20, 23, 'A new quiz has been posted: TITE\nDue date: 2024-12-04T23:17\nTotal Points: 4', NULL, 'New Quiz: TITE', NULL, 'active', '2024-12-03 15:17:40', 'quiz', NULL, NULL, 'medium', 108),
(58, 9, 20, 23, 'A new quiz has been posted: wqeqw\nDue date: 2024-12-20T23:25\nTotal Points: 6', NULL, 'New Quiz: wqeqw', NULL, 'active', '2024-12-03 15:25:23', 'quiz', NULL, NULL, 'medium', 109),
(59, 9, 20, 23, 'A new quiz has been posted: test\nDue date: 2024-12-04T23:29\nTotal Points: 1', NULL, 'New Quiz: test', NULL, 'active', '2024-12-03 15:29:09', 'quiz', NULL, NULL, 'medium', 110),
(60, 9, 20, 23, 'A new quiz has been posted: Test123\nDue date: 2024-12-06T23:31\nTotal Points: 1', NULL, 'New Quiz: Test123', NULL, 'active', '2024-12-03 15:32:01', 'quiz', NULL, NULL, 'medium', 111),
(61, 9, 20, 23, 'A new quiz has been posted: test123\nDue date: 2024-12-05T00:13\nTotal Points: 3', NULL, 'New Quiz: test123', NULL, 'active', '2024-12-03 16:13:57', 'quiz', NULL, NULL, 'medium', 112),
(62, 9, 20, 23, 'A new quiz has been posted: sdas\nDue date: 2024-12-05T00:24\nTotal Points: 3', NULL, 'New Quiz: sdas', NULL, 'active', '2024-12-03 16:24:56', 'quiz', NULL, NULL, 'medium', 113),
(63, 9, 20, 23, 'A new quiz has been posted: Q\nDue date: 2024-12-05T01:37\nTotal Points: 3', NULL, 'New Quiz: Q', NULL, 'active', '2024-12-03 17:39:32', 'quiz', NULL, NULL, 'medium', 114),
(64, 9, 20, 23, 'A new quiz has been posted: q\nDue date: 2024-12-05T01:39\nTotal Points: 3', NULL, 'New Quiz: q', NULL, 'active', '2024-12-03 17:39:58', 'quiz', NULL, NULL, 'medium', 115),
(65, 9, 20, 23, 'A new quiz has been posted: a\nDue date: 2024-12-05T01:41\nTotal Points: 3', NULL, 'New Quiz: a', NULL, 'active', '2024-12-03 17:42:03', 'quiz', NULL, NULL, 'medium', 116),
(66, 9, 20, 23, 'A new quiz has been posted: testtt\nDue date: 2024-12-07T05:06\nTotal Points: 3', NULL, 'New Quiz: testtt', NULL, 'active', '2024-12-05 21:06:12', 'quiz', NULL, NULL, 'medium', 128),
(67, 9, 20, 23, 'A new quiz has been posted: test\nDue date: 2024-12-07T06:03\nTotal Points: 3', NULL, 'New Quiz: test', NULL, 'active', '2024-12-05 22:03:12', 'quiz', NULL, NULL, 'medium', 131),
(68, 9, 20, 23, 'A new quiz has been posted: test\nDue date: 2024-12-07T06:07\nTotal Points: 3', NULL, 'New Quiz: test', NULL, 'active', '2024-12-05 22:07:43', 'quiz', NULL, NULL, 'medium', 135),
(69, 9, 20, 23, 'A new quiz has been posted: testtasd\nDue date: 2024-12-07T07:24\nTotal Points: 3', NULL, 'New Quiz: testtasd', NULL, 'active', '2024-12-05 23:24:57', 'quiz', NULL, NULL, 'medium', 136),
(70, 9, 20, 23, 'A new quiz has been posted: testasdasd\nDue date: 2024-12-07T15:20\nTotal Points: 3', NULL, 'New Quiz: testasdasd', NULL, 'active', '2024-12-06 07:21:04', 'quiz', NULL, NULL, 'medium', 137),
(71, 9, 20, 23, 'A new quiz has been posted: ASDASD\nDue date: 2024-12-07T15:58\nTotal Points: 3', NULL, 'New Quiz: ASDASD', NULL, 'active', '2024-12-06 07:58:56', 'quiz', NULL, NULL, 'medium', 138),
(72, 9, 20, 23, 'A new quiz has been posted: SHORT\nDue date: 2024-12-07T17:27\nTotal Points: 3', NULL, 'New Quiz: SHORT', NULL, 'active', '2024-12-06 09:27:30', 'quiz', NULL, NULL, 'medium', 139),
(73, 9, 20, 23, 'A new quiz has been posted: NEW\nDue date: 2024-12-07T17:47\nTotal Points: 3', NULL, 'New Quiz: NEW', NULL, 'active', '2024-12-06 09:47:47', 'quiz', NULL, NULL, 'medium', 140),
(74, 9, 20, 23, 'A new quiz has been posted: SHOSSSS\nDue date: 2024-12-07T18:41\nTotal Points: 3', NULL, 'New Quiz: SHOSSSS', NULL, 'active', '2024-12-06 10:41:43', 'quiz', NULL, NULL, 'medium', 141),
(75, 9, 20, 23, 'A new quiz has been posted: XZC\nDue date: 2024-12-08T00:09\nTotal Points: 5', NULL, 'New Quiz: XZC', NULL, 'active', '2024-12-06 16:09:51', 'quiz', NULL, NULL, 'medium', 142),
(76, 9, 20, 23, 'A new quiz has been posted: web\nDue date: 2024-12-10T01:37\nTotal Points: 7', NULL, 'New Quiz: web', NULL, 'active', '2024-12-08 17:37:51', 'quiz', NULL, NULL, 'medium', 146),
(77, 9, 20, 23, 'A new quiz has been posted: Test TEST\nDue date: 2024-12-10T01:44\nTotal Points: 3', NULL, 'New Quiz: Test TEST', NULL, 'active', '2024-12-08 17:44:42', 'quiz', NULL, NULL, 'medium', 147),
(78, 9, 20, 23, 'A new quiz has been posted: points\nDue date: 2024-12-10T02:03\nTotal Points: 3', NULL, 'New Quiz: points', NULL, 'active', '2024-12-08 18:03:38', 'quiz', NULL, NULL, 'medium', 148),
(79, 9, 20, 23, 'A new quiz has been posted: pacifoc\nDue date: 2024-12-10T02:13\nTotal Points: 3', NULL, 'New Quiz: pacifoc', NULL, 'active', '2024-12-08 18:13:09', 'quiz', NULL, NULL, 'medium', 149),
(80, 9, 20, 23, 'A new quiz has been posted: Hahaha\nDue date: 2024-12-10T11:14\nTotal Points: 3', NULL, 'New Quiz: Hahaha', NULL, 'active', '2024-12-09 03:15:10', 'quiz', NULL, NULL, 'medium', 150),
(81, 9, 20, 23, 'A new quiz has been posted: quiz 1\nDue date: 2024-12-10T12:56\nTotal Points: 4', NULL, 'New Quiz: quiz 1', NULL, 'active', '2024-12-09 04:56:56', 'quiz', NULL, NULL, 'medium', 151),
(82, 9, 20, 23, 'A new quiz has been posted: QUIZ NO 1\nDue date: 2024-12-16T23:00\nTotal Points: 1', NULL, 'New Quiz: QUIZ NO 1', NULL, 'active', '2024-12-09 08:13:59', 'quiz', NULL, NULL, 'medium', 154);

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
(1, 'Mathematics Department', 'MATH', 'Focuses on developing students mathematical skills, logical reasoning, and problem-solving abilities through comprehensive math education.', 'active', '2024-11-23 07:03:01', '2024-11-23 07:03:01'),
(2, 'Science Department', 'SCI', 'Provides hands-on scientific education, covering physics, chemistry, biology, and environmental science to develop scientific inquiry and critical thinking.', 'active', '2024-11-23 07:03:01', '2024-11-23 07:03:01'),
(3, 'English Department', 'ENG', 'Develops students proficiency in English language skills including reading, writing, speaking, and listening through comprehensive language arts education.', 'active', '2024-11-23 07:03:01', '2024-11-23 07:03:01'),
(4, 'Filipino Department', 'FIL', 'Promotes Filipino language mastery and appreciation of Philippine literature and culture through comprehensive Filipino language education.', 'active', '2024-11-23 07:03:01', '2024-11-23 07:03:01'),
(5, 'Social Studies Department', 'SS', 'Teaches history, geography, and social sciences to develop students understanding of society, culture, and civic responsibility.', 'active', '2024-11-23 07:03:01', '2024-11-23 07:03:01'),
(6, 'MAPEH Department', 'MAPEH', 'Integrates Music, Arts, Physical Education, and Health education to develop students artistic, physical, and health awareness skills.', 'active', '2024-11-23 07:03:01', '2024-11-23 07:03:01'),
(7, 'Technology and Livelihood Education Department', 'TLE', 'Provides practical skills training in various technical and vocational areas to prepare students for future careers and entrepreneurship.', 'active', '2024-11-23 07:03:01', '2024-11-23 07:03:01'),
(8, 'Values Education Department', 'VALED', 'Focuses on character development, moral values, and ethical principles to shape responsible and value-oriented citizens.', 'active', '2024-11-23 07:03:01', '2024-11-23 07:03:01'),
(9, 'Guidance and Counseling', 'GUID', 'Provides student support services, career guidance, and personal counseling to promote student well-being and development.', 'active', '2024-11-23 07:03:01', '2024-11-23 07:03:01'),
(10, 'School Administration', 'ADMIN', 'Manages school operations, policies, and administrative functions to ensure effective school management and leadership.', 'active', '2024-11-23 07:03:01', '2024-11-23 07:03:01'),
(11, 'Research and Development Department', 'R&D', 'Promotes academic research, innovation, and continuous improvement in educational practices and methodologies.', 'active', '2024-11-23 07:03:01', '2024-11-23 07:03:01'),
(12, 'ICT Department', 'ICT', 'Manages information technology infrastructure and provides digital literacy education to support modern learning needs.', 'active', '2024-11-23 07:03:01', '2024-11-23 07:03:01'),
(13, 'Student Affairs Department', 'SAD', 'Oversees student activities, organizations, and welfare programs to enhance student life and development.', 'active', '2024-11-23 07:03:01', '2024-11-23 07:03:01');

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
(1, 'School Year 2023-2024 Opening', 'We are thrilled to announce the opening of School Year 2023-2024! As we embark on this new academic journey, we welcome both returning and new students to our campus.\r\n\r\nKey Highlights for this School Year:\r\n• Enhanced curriculum focusing on 21st-century skills\r\n• New extracurricular activities and clubs\r\n• Upgraded classroom facilities and learning resources\r\n• Implementation of blended learning approaches\r\n• Strengthened student support services\r\n\r\nImportant Dates:\r\n- First Day of Classes: June 5, 2024\r\n- Orientation Week: May 29-31, 2024\r\n- Parent-Teacher Meeting: June 15, 2024\r\n\r\nWe look forward to another year of academic excellence, personal growth, and memorable experiences. Let\'s make this school year extraordinary together!', 'Welcome back students! The new school year begins with excitement and new opportunities.', '../images/1.jpg', 'academic', '2024-01-15', '2024-11-15 00:32:02', 'active'),
(2, 'Annual Science Fair 2024', 'The Annual Science Fair 2024 is approaching! This year\'s theme is \"Innovation for Sustainable Future.\"\r\n\r\nEvent Details:\r\n• Date: February 20, 2024\r\n• Time: 8:00 AM - 4:00 PM\r\n• Venue: School Gymnasium\r\n• Categories: Environmental Science, Technology, Health Sciences, Physical Sciences\r\n\r\nCompetition Guidelines:\r\n1. Projects must be original and student-led\r\n2. Teams of 2-3 students allowed\r\n3. Display boards and presentations required\r\n4. Research documentation mandatory\r\n\r\nPrizes:\r\n- 1st Place: ₱5,000 and medals\r\n- 2nd Place: ₱3,000 and medals\r\n- 3rd Place: ₱2,000 and medals\r\n- Special Awards for Innovation\r\n\r\nRegistration deadline: February 10, 2024\r\nContact your science teacher for registration and more information.', 'Join us for an exciting showcase of student science projects and innovations.', '../images/2.jpg', 'event', '2024-02-20', '2024-11-15 00:32:02', 'active'),
(3, 'Important: Class Schedule Updates', 'Important Notice: Class Schedule Updates for the Current Semester\r\n\r\nThe following changes have been implemented to optimize learning experiences:\r\n\r\nMorning Sessions:\r\n• Grade 7: 7:00 AM - 12:00 PM\r\n• Grade 8: 7:30 AM - 12:30 PM\r\n• Grade 9: 8:00 AM - 1:00 PM\r\n\r\nAfternoon Sessions:\r\n• Grade 10: 12:30 PM - 5:30 PM\r\n• Grade 11: 1:00 PM - 6:00 PM\r\n• Grade 12: 1:30 PM - 6:30 PM\r\n\r\nAdditional Changes:\r\n1. Computer Laboratory sessions moved to mornings\r\n2. Physical Education classes scheduled for cooler hours\r\n3. Science Laboratory work in mid-morning slots\r\n4. Reading periods added to early morning schedules\r\n\r\nThese changes take effect from February 20, 2024. Please adjust your daily routines accordingly.', 'Please check the revised class schedules for the upcoming semester.', '../images/3.jpg', 'announcement', '2024-02-15', '2024-11-15 00:32:02', 'active'),
(4, 'New Learning Management System', 'We are excited to introduce our new Learning Management System (LMS) designed to enhance your educational experience!\r\n\r\nKey Features:\r\n• Interactive virtual classrooms\r\n• Real-time progress tracking\r\n• Digital assignment submission\r\n• Integrated video conferencing\r\n• Mobile-friendly interface\r\n• Automated attendance system\r\n• Parent portal access\r\n\r\nBenefits:\r\n1. 24/7 access to learning materials\r\n2. Improved student-teacher communication\r\n3. Paperless submission system\r\n4. Instant feedback on assignments\r\n5. Collaborative learning tools\r\n\r\nTraining Schedule:\r\n- Student Orientation: February 15-16, 2024\r\n- Parent Orientation: February 17, 2024\r\n- Teacher Training: February 12-14, 2024\r\n\r\nSystem Requirements:\r\n• Internet connection\r\n• Updated web browser\r\n• Minimum 4GB RAM device\r\n• Webcam and microphone\r\n\r\nThe new system will be fully implemented starting February 20, 2024.', 'Introducing our new digital learning platform for enhanced online education.', '../images/4.jpg', 'academic', '2024-02-10', '2024-11-15 00:32:02', 'active'),
(5, 'Sports Festival 2024', 'Get ready for the most exciting sports event of the year - Sports Festival 2024!\r\n\r\nEvent Schedule:\r\nMarch 1-5, 2024\r\nDay 1: Opening Ceremony and Track Events\r\nDay 2: Basketball and Volleyball Tournaments\r\nDay 3: Swimming Competition\r\nDay 4: Traditional Filipino Games\r\nDay 5: Championship Games and Closing Ceremony\r\n\r\nSports Categories:\r\n• Track and Field\r\n• Basketball (Boys/Girls)\r\n• Volleyball (Boys/Girls)\r\n• Swimming\r\n• Table Tennis\r\n• Badminton\r\n• Chess\r\n\r\nSpecial Events:\r\n- Inter-class Cheering Competition\r\n- Sports Exhibition Matches\r\n- Alumni Games\r\n- Teachers vs. Students Friendly Matches\r\n\r\nRegistration:\r\n• Sign up through your PE teachers\r\n• Deadline: February 25, 2024\r\n• Medical clearance required\r\n• Parent consent form mandatory\r\n\r\nPrizes for each category:\r\nGold Medal + Certificate\r\nSilver Medal + Certificate\r\nBronze Medal + Certificate', 'Get ready for our annual sports festival featuring various athletic competitions.', '../images/2.jpg', 'event', '2024-03-01', '2024-11-15 00:32:02', 'active'),
(6, 'Enrollment Period Extended', 'IMPORTANT ANNOUNCEMENT: Enrollment Period Extension\r\n\r\nWe are extending the enrollment period until March 15, 2024, to accommodate more students and ensure a smooth registration process.\r\n\r\nExtended Schedule:\r\n• Online Registration: 24/7 until March 15\r\n• On-site Enrollment: Monday-Friday, 8AM-5PM\r\n• Saturday Special Enrollment: 8AM-12PM\r\n\r\nRequired Documents:\r\n1. Form 137 (Report Card)\r\n2. Good Moral Certificate\r\n3. Birth Certificate\r\n4. 2x2 ID Pictures (4 pieces)\r\n5. Certificate of Completion/Graduation\r\n\r\nPayment Options:\r\n- Full Payment with 5% discount\r\n- Quarterly Payment Plan\r\n- Monthly Payment Plan\r\n\r\nSpecial Considerations:\r\n• Early bird discount until March 1\r\n• Sibling discount available\r\n• Scholar application extended\r\n• Financial assistance programs\r\n\r\nFor inquiries:\r\nEmail: enrollment@camerinohub.edu.ph\r\nPhone: (02) 8123-4567\r\nMobile: 0912-345-6789\r\n\r\nDon\'t miss this opportunity to be part of our academic community!', 'The enrollment period has been extended until March 15, 2024.', '../images/1.jpg', 'announcement', '2024-02-25', '2024-11-15 00:32:02', 'active');

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
(3, 37, 20, 23, 'student', 'quiz', 114, 114, NULL, 'New Quiz: Q', 'A new quiz has been posted for \nDue date: 2024-12-05T01:37\nTotal Points: 3', 0, 0, '2024-12-03 17:39:32'),
(4, 50, 20, 23, 'student', 'quiz', 114, 114, NULL, 'New Quiz: Q', 'A new quiz has been posted for \nDue date: 2024-12-05T01:37\nTotal Points: 3', 0, 0, '2024-12-03 17:39:32'),
(6, 37, 20, 23, 'student', 'quiz', 115, 115, NULL, 'New Quiz: q', 'A new quiz has been posted for \nDue date: 2024-12-05T01:39\nTotal Points: 3', 0, 0, '2024-12-03 17:39:58'),
(7, 50, 20, 23, 'student', 'quiz', 115, 115, NULL, 'New Quiz: q', 'A new quiz has been posted for \nDue date: 2024-12-05T01:39\nTotal Points: 3', 0, 0, '2024-12-03 17:39:58'),
(9, 37, 20, 23, 'student', 'quiz', 116, 116, NULL, 'New Quiz: a', 'A new quiz has been posted for \nDue date: 2024-12-05T01:41\nTotal Points: 3', 0, 0, '2024-12-03 17:42:03'),
(10, 50, 20, 23, 'student', 'quiz', 116, 116, NULL, 'New Quiz: a', 'A new quiz has been posted for \nDue date: 2024-12-05T01:41\nTotal Points: 3', 0, 0, '2024-12-03 17:42:03'),
(11, 37, 20, 23, 'student', 'quiz', 128, 128, NULL, 'New Quiz: testtt', 'A new quiz has been posted: testtt\nDue date: 2024-12-07T05:06\nTotal Points: 3', 0, 0, '2024-12-05 21:06:12'),
(12, 50, 20, 23, 'student', 'quiz', 128, 128, NULL, 'New Quiz: testtt', 'A new quiz has been posted: testtt\nDue date: 2024-12-07T05:06\nTotal Points: 3', 0, 0, '2024-12-05 21:06:12'),
(14, 37, 20, 23, 'student', 'quiz', 131, 131, NULL, 'New Quiz: test', 'A new quiz has been posted: test\nDue date: 2024-12-07T06:03\nTotal Points: 3', 0, 0, '2024-12-05 22:03:12'),
(15, 50, 20, 23, 'student', 'quiz', 131, 131, NULL, 'New Quiz: test', 'A new quiz has been posted: test\nDue date: 2024-12-07T06:03\nTotal Points: 3', 0, 0, '2024-12-05 22:03:12'),
(17, 37, 20, 23, 'student', 'quiz', 135, 135, NULL, 'New Quiz: test', 'A new quiz has been posted: test\nDue date: 2024-12-07T06:07\nTotal Points: 3', 0, 0, '2024-12-05 22:07:43'),
(18, 50, 20, 23, 'student', 'quiz', 135, 135, NULL, 'New Quiz: test', 'A new quiz has been posted: test\nDue date: 2024-12-07T06:07\nTotal Points: 3', 0, 0, '2024-12-05 22:07:43'),
(20, 37, 20, 23, 'student', 'quiz', 136, 136, NULL, 'New Quiz: testtasd', 'A new quiz has been posted: testtasd\nDue date: 2024-12-07T07:24\nTotal Points: 3', 0, 0, '2024-12-05 23:24:57'),
(21, 50, 20, 23, 'student', 'quiz', 136, 136, NULL, 'New Quiz: testtasd', 'A new quiz has been posted: testtasd\nDue date: 2024-12-07T07:24\nTotal Points: 3', 0, 0, '2024-12-05 23:24:57'),
(22, 37, 20, 23, 'student', 'quiz', 137, 137, NULL, 'New Quiz: testasdasd', 'A new quiz has been posted: testasdasd\nDue date: 2024-12-07T15:20\nTotal Points: 3', 0, 0, '2024-12-06 07:21:04'),
(23, 50, 20, 23, 'student', 'quiz', 137, 137, NULL, 'New Quiz: testasdasd', 'A new quiz has been posted: testasdasd\nDue date: 2024-12-07T15:20\nTotal Points: 3', 0, 0, '2024-12-06 07:21:04'),
(25, 37, 20, 23, 'student', 'quiz', 138, 138, NULL, 'New Quiz: ASDASD', 'A new quiz has been posted: ASDASD\nDue date: 2024-12-07T15:58\nTotal Points: 3', 0, 0, '2024-12-06 07:58:56'),
(26, 50, 20, 23, 'student', 'quiz', 138, 138, NULL, 'New Quiz: ASDASD', 'A new quiz has been posted: ASDASD\nDue date: 2024-12-07T15:58\nTotal Points: 3', 0, 0, '2024-12-06 07:58:56'),
(28, 37, 20, 23, 'student', 'quiz', 139, 139, NULL, 'New Quiz: SHORT', 'A new quiz has been posted: SHORT\nDue date: 2024-12-07T17:27\nTotal Points: 3', 0, 0, '2024-12-06 09:27:30'),
(29, 50, 20, 23, 'student', 'quiz', 139, 139, NULL, 'New Quiz: SHORT', 'A new quiz has been posted: SHORT\nDue date: 2024-12-07T17:27\nTotal Points: 3', 0, 0, '2024-12-06 09:27:30'),
(31, 37, 20, 23, 'student', 'quiz', 140, 140, NULL, 'New Quiz: NEW', 'A new quiz has been posted: NEW\nDue date: 2024-12-07T17:47\nTotal Points: 3', 0, 0, '2024-12-06 09:47:47'),
(32, 50, 20, 23, 'student', 'quiz', 140, 140, NULL, 'New Quiz: NEW', 'A new quiz has been posted: NEW\nDue date: 2024-12-07T17:47\nTotal Points: 3', 0, 0, '2024-12-06 09:47:47'),
(34, 37, 20, 23, 'student', 'quiz', 141, 141, NULL, 'New Quiz: SHOSSSS', 'A new quiz has been posted: SHOSSSS\nDue date: 2024-12-07T18:41\nTotal Points: 3', 0, 0, '2024-12-06 10:41:43'),
(35, 50, 20, 23, 'student', 'quiz', 141, 141, NULL, 'New Quiz: SHOSSSS', 'A new quiz has been posted: SHOSSSS\nDue date: 2024-12-07T18:41\nTotal Points: 3', 0, 0, '2024-12-06 10:41:43'),
(37, 37, 20, 23, 'student', 'quiz', 142, 142, NULL, 'New Quiz: XZC', 'A new quiz has been posted: XZC\nDue date: 2024-12-08T00:09\nTotal Points: 5', 0, 0, '2024-12-06 16:09:51'),
(38, 50, 20, 23, 'student', 'quiz', 142, 142, NULL, 'New Quiz: XZC', 'A new quiz has been posted: XZC\nDue date: 2024-12-08T00:09\nTotal Points: 5', 0, 0, '2024-12-06 16:09:51'),
(40, 37, 20, 23, 'student', 'quiz', 146, 146, NULL, 'New Quiz: web', 'A new quiz has been posted: web\nDue date: 2024-12-10T01:37\nTotal Points: 7', 0, 0, '2024-12-08 17:37:51'),
(41, 50, 20, 23, 'student', 'quiz', 146, 146, NULL, 'New Quiz: web', 'A new quiz has been posted: web\nDue date: 2024-12-10T01:37\nTotal Points: 7', 0, 0, '2024-12-08 17:37:51'),
(43, 37, 20, 23, 'student', 'quiz', 147, 147, NULL, 'New Quiz: Test TEST', 'A new quiz has been posted: Test TEST\nDue date: 2024-12-10T01:44\nTotal Points: 3', 0, 0, '2024-12-08 17:44:42'),
(44, 50, 20, 23, 'student', 'quiz', 147, 147, NULL, 'New Quiz: Test TEST', 'A new quiz has been posted: Test TEST\nDue date: 2024-12-10T01:44\nTotal Points: 3', 0, 0, '2024-12-08 17:44:42'),
(46, 37, 20, 23, 'student', 'quiz', 148, 148, NULL, 'New Quiz: points', 'A new quiz has been posted: points\nDue date: 2024-12-10T02:03\nTotal Points: 3', 0, 0, '2024-12-08 18:03:38'),
(47, 50, 20, 23, 'student', 'quiz', 148, 148, NULL, 'New Quiz: points', 'A new quiz has been posted: points\nDue date: 2024-12-10T02:03\nTotal Points: 3', 0, 0, '2024-12-08 18:03:38'),
(49, 37, 20, 23, 'student', 'quiz', 149, 149, NULL, 'New Quiz: pacifoc', 'A new quiz has been posted: pacifoc\nDue date: 2024-12-10T02:13\nTotal Points: 3', 0, 0, '2024-12-08 18:13:09'),
(50, 50, 20, 23, 'student', 'quiz', 149, 149, NULL, 'New Quiz: pacifoc', 'A new quiz has been posted: pacifoc\nDue date: 2024-12-10T02:13\nTotal Points: 3', 0, 0, '2024-12-08 18:13:09'),
(52, 37, 20, 23, 'student', 'quiz', 150, 150, NULL, 'New Quiz: Hahaha', 'A new quiz has been posted: Hahaha\nDue date: 2024-12-10T11:14\nTotal Points: 3', 0, 0, '2024-12-09 03:15:10'),
(53, 50, 20, 23, 'student', 'quiz', 150, 150, NULL, 'New Quiz: Hahaha', 'A new quiz has been posted: Hahaha\nDue date: 2024-12-10T11:14\nTotal Points: 3', 0, 0, '2024-12-09 03:15:10'),
(55, 37, 20, 23, 'student', 'quiz', 151, 151, NULL, 'New Quiz: quiz 1', 'A new quiz has been posted: quiz 1\nDue date: 2024-12-10T12:56\nTotal Points: 4', 0, 0, '2024-12-09 04:56:56'),
(56, 50, 20, 23, 'student', 'quiz', 151, 151, NULL, 'New Quiz: quiz 1', 'A new quiz has been posted: quiz 1\nDue date: 2024-12-10T12:56\nTotal Points: 4', 0, 0, '2024-12-09 04:56:56'),
(58, 37, 20, 23, 'student', 'quiz', 154, 154, NULL, 'New Quiz: QUIZ NO 1', 'A new quiz has been posted: QUIZ NO 1\nDue date: 2024-12-16T23:00\nTotal Points: 1', 0, 0, '2024-12-09 08:13:59');

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
(41, 25, 'b', 1, 1, '2024-12-03 16:24:56'),
(42, 25, 'c', 0, 2, '2024-12-03 16:24:56'),
(43, 25, 'a', 0, 3, '2024-12-03 16:24:56'),
(44, 26, 'True', 0, 1, '2024-12-03 16:24:56'),
(45, 26, 'False', 1, 2, '2024-12-03 16:24:56'),
(46, 27, 'tite', 1, 1, '2024-12-03 16:24:56'),
(47, 28, 'A', 1, 1, '2024-12-03 17:39:32'),
(48, 28, 'B', 0, 2, '2024-12-03 17:39:32'),
(49, 29, 'True', 0, 1, '2024-12-03 17:39:32'),
(50, 29, 'False', 1, 2, '2024-12-03 17:39:32'),
(51, 30, 'ASD', 1, 1, '2024-12-03 17:39:32'),
(52, 31, 'a', 1, 1, '2024-12-03 17:39:57'),
(53, 31, 'b', 0, 2, '2024-12-03 17:39:57'),
(54, 32, 'True', 0, 1, '2024-12-03 17:39:58'),
(55, 32, 'False', 1, 2, '2024-12-03 17:39:58'),
(56, 33, 'asd', 1, 1, '2024-12-03 17:39:58'),
(57, 34, 'a', 0, 1, '2024-12-03 17:42:03'),
(58, 34, 'n', 1, 2, '2024-12-03 17:42:03'),
(59, 35, 'True', 1, 1, '2024-12-03 17:42:03'),
(60, 35, 'False', 0, 2, '2024-12-03 17:42:03'),
(61, 36, 'asd', 1, 1, '2024-12-03 17:42:03'),
(62, 37, 'a', 0, 1, '2024-12-03 17:51:08'),
(63, 37, 'b', 1, 2, '2024-12-03 17:51:08'),
(64, 38, 'True', 0, 1, '2024-12-03 17:51:09'),
(65, 38, 'False', 1, 2, '2024-12-03 17:51:09'),
(66, 40, 'a', 1, 1, '2024-12-05 19:39:52'),
(67, 40, 'b', 0, 2, '2024-12-05 19:39:52'),
(68, 41, 'True', 0, 1, '2024-12-05 19:39:52'),
(69, 41, 'False', 1, 2, '2024-12-05 19:39:52'),
(70, 45, 'a', 1, 1, '2024-12-05 21:06:12'),
(71, 45, 'b', 0, 2, '2024-12-05 21:06:12'),
(72, 46, 'True', 1, 1, '2024-12-05 21:06:12'),
(73, 46, 'False', 0, 2, '2024-12-05 21:06:12'),
(74, 50, 'a', 1, 1, '2024-12-05 22:03:12'),
(75, 50, 'b', 0, 2, '2024-12-05 22:03:12'),
(76, 51, 'True', 0, 1, '2024-12-05 22:03:12'),
(77, 51, 'False', 0, 2, '2024-12-05 22:03:12'),
(86, 60, 'True', 1, 1, '2024-12-05 22:07:43'),
(87, 60, 'False', 0, 2, '2024-12-05 22:07:43'),
(156, 63, 'True', 1, 1, '2024-12-06 07:35:35'),
(157, 63, 'False', 0, 2, '2024-12-06 07:35:35'),
(158, 65, 'True', 1, 1, '2024-12-06 07:35:35'),
(159, 65, 'False', 0, 2, '2024-12-06 07:35:35'),
(160, 73, 'True', 1, 1, '2024-12-06 07:35:35'),
(161, 73, 'False', 0, 2, '2024-12-06 07:35:35'),
(162, 75, 'True', 0, 1, '2024-12-06 07:35:35'),
(163, 75, 'False', 1, 2, '2024-12-06 07:35:35'),
(166, 90, 'a', 1, 1, '2024-12-06 07:49:57'),
(167, 90, 'b', 0, 2, '2024-12-06 07:49:57'),
(168, 91, 'A', 1, 1, '2024-12-06 07:58:55'),
(169, 91, 'B', 0, 2, '2024-12-06 07:58:55'),
(170, 92, 'True', 1, 1, '2024-12-06 07:58:55'),
(171, 92, 'False', 0, 2, '2024-12-06 07:58:56'),
(172, 94, 'a', 1, 1, '2024-12-06 07:58:56'),
(173, 94, 'b', 0, 2, '2024-12-06 07:58:56'),
(174, 95, 'A', 1, 1, '2024-12-06 09:27:30'),
(175, 95, 'B', 0, 2, '2024-12-06 09:27:30'),
(176, 96, 'True', 1, 1, '2024-12-06 09:27:30'),
(177, 96, 'False', 0, 2, '2024-12-06 09:27:30'),
(178, 98, 'A', 1, 1, '2024-12-06 09:47:47'),
(179, 98, 'B', 0, 2, '2024-12-06 09:47:47'),
(180, 99, 'True', 1, 1, '2024-12-06 09:47:47'),
(181, 99, 'False', 0, 2, '2024-12-06 09:47:47'),
(182, 101, 'a', 1, 1, '2024-12-06 09:47:47'),
(183, 101, 'b', 0, 2, '2024-12-06 09:47:47'),
(184, 102, 'True', 1, 1, '2024-12-06 09:47:47'),
(185, 102, 'False', 0, 2, '2024-12-06 09:47:47'),
(186, 104, 'a', 1, 1, '2024-12-06 10:41:43'),
(187, 104, 'b', 0, 2, '2024-12-06 10:41:43'),
(188, 105, 'True', 1, 1, '2024-12-06 10:41:43'),
(189, 105, 'False', 0, 2, '2024-12-06 10:41:43'),
(190, 107, 'a', 1, 1, '2024-12-06 10:41:43'),
(191, 107, 'b', 0, 2, '2024-12-06 10:41:43'),
(192, 108, 'yt', 1, 1, '2024-12-06 16:09:51'),
(193, 108, 'test', 0, 2, '2024-12-06 16:09:51'),
(194, 109, 'True', 1, 1, '2024-12-06 16:09:51'),
(195, 109, 'False', 0, 2, '2024-12-06 16:09:51'),
(196, 110, 'a', 1, 1, '2024-12-06 16:09:51'),
(197, 110, 'b', 0, 2, '2024-12-06 16:09:51'),
(198, 113, 'a', 0, 1, '2024-12-08 17:37:51'),
(199, 113, 'b', 0, 2, '2024-12-08 17:37:51'),
(200, 113, 'c', 1, 3, '2024-12-08 17:37:51'),
(201, 113, 'd', 0, 4, '2024-12-08 17:37:51'),
(202, 114, 'True', 1, 1, '2024-12-08 17:37:51'),
(203, 114, 'False', 0, 2, '2024-12-08 17:37:51'),
(204, 118, 'a', 1, 1, '2024-12-08 17:37:51'),
(205, 118, 'b', 0, 2, '2024-12-08 17:37:51'),
(206, 118, 'c', 0, 3, '2024-12-08 17:37:51'),
(207, 118, 'd', 0, 4, '2024-12-08 17:37:51'),
(208, 119, 'True', 0, 1, '2024-12-08 17:37:51'),
(209, 119, 'False', 1, 2, '2024-12-08 17:37:51'),
(210, 120, 'Oo', 1, 1, '2024-12-08 17:44:42'),
(211, 120, 'Hindi', 0, 2, '2024-12-08 17:44:42'),
(212, 120, 'Wag', 0, 3, '2024-12-08 17:44:42'),
(213, 120, 'check', 0, 4, '2024-12-08 17:44:42'),
(214, 121, 'True', 0, 1, '2024-12-08 17:44:42'),
(215, 121, 'False', 1, 2, '2024-12-08 17:44:42'),
(216, 123, 'a', 1, 1, '2024-12-08 18:03:38'),
(217, 123, 'b', 0, 2, '2024-12-08 18:03:38'),
(218, 123, 'c', 0, 3, '2024-12-08 18:03:38'),
(219, 123, 'd', 0, 4, '2024-12-08 18:03:38'),
(220, 124, 'True', 1, 1, '2024-12-08 18:03:38'),
(221, 124, 'False', 0, 2, '2024-12-08 18:03:38'),
(222, 126, 'a', 0, 1, '2024-12-08 18:13:09'),
(223, 126, 'b', 0, 2, '2024-12-08 18:13:09'),
(224, 126, 'c', 1, 3, '2024-12-08 18:13:09'),
(225, 126, 'd', 0, 4, '2024-12-08 18:13:09'),
(226, 127, 'True', 0, 1, '2024-12-08 18:13:09'),
(227, 127, 'False', 1, 2, '2024-12-08 18:13:09'),
(228, 129, 'Hindi', 1, 1, '2024-12-09 03:15:10'),
(229, 129, 'Oo', 0, 2, '2024-12-09 03:15:10'),
(230, 130, 'True', 0, 1, '2024-12-09 03:15:10'),
(231, 130, 'False', 1, 2, '2024-12-09 03:15:10'),
(232, 132, 'a', 0, 1, '2024-12-09 04:56:56'),
(233, 132, 'b', 0, 2, '2024-12-09 04:56:56'),
(234, 132, 'c', 1, 3, '2024-12-09 04:56:56'),
(235, 132, 'd', 0, 4, '2024-12-09 04:56:56'),
(236, 133, 'True', 0, 1, '2024-12-09 04:56:56'),
(237, 133, 'False', 1, 2, '2024-12-09 04:56:56'),
(242, 136, 'A) Structured Query Language', 1, 1, '2024-12-09 08:19:05'),
(243, 136, 'B) Simple Query Language', 0, 2, '2024-12-09 08:19:05'),
(244, 136, 'C) Standard Query Language', 0, 3, '2024-12-09 08:19:05'),
(245, 136, 'D) Sequential Query Language', 0, 4, '2024-12-09 08:19:05');

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
(63, 135, 'test1');

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
(34, 37, 113, '2024-12-03 16:43:07', NULL, 0.00, 'in_progress', 1, '2024-12-03 16:43:07'),
(35, 50, 113, '2024-12-03 17:32:11', NULL, 0.00, 'in_progress', 1, '2024-12-03 17:32:11'),
(36, 50, 124, '2024-12-03 17:51:17', NULL, 0.00, 'in_progress', 1, '2024-12-03 17:51:17'),
(37, 50, 116, '2024-12-03 17:53:39', NULL, 0.00, 'in_progress', 1, '2024-12-03 17:53:39'),
(38, 50, 125, '2024-12-05 19:41:34', NULL, 0.00, 'in_progress', 1, '2024-12-05 19:40:39');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz_questions`
--

INSERT INTO `quiz_questions` (`question_id`, `quiz_id`, `question_text`, `question_type`, `points`, `question_order`, `created_at`) VALUES
(25, 113, 'asd', 'multiple_choice', 1, 1, '2024-12-03 16:24:56'),
(26, 113, 's', 'true_false', 1, 2, '2024-12-03 16:24:56'),
(27, 113, 'gawd', 'short_answer', 1, 3, '2024-12-03 16:24:56'),
(28, 114, 'WQE', 'multiple_choice', 1, 1, '2024-12-03 17:39:32'),
(29, 114, 'QWE', 'true_false', 1, 2, '2024-12-03 17:39:32'),
(30, 114, 'A', 'short_answer', 1, 3, '2024-12-03 17:39:32'),
(31, 115, 'q', 'multiple_choice', 1, 1, '2024-12-03 17:39:57'),
(32, 115, 'a', 'true_false', 1, 2, '2024-12-03 17:39:57'),
(33, 115, 'qw', 'short_answer', 1, 3, '2024-12-03 17:39:58'),
(34, 116, 'a', 'multiple_choice', 1, 1, '2024-12-03 17:42:03'),
(35, 116, 'a', 'true_false', 1, 2, '2024-12-03 17:42:03'),
(36, 116, 'a', 'short_answer', 1, 3, '2024-12-03 17:42:03'),
(37, 124, 'a', 'multiple_choice', 1, 1, '2024-12-03 17:51:08'),
(38, 124, 'a', 'true_false', 1, 2, '2024-12-03 17:51:09'),
(39, 124, 'asd', 'short_answer', 1, 3, '2024-12-03 17:51:09'),
(40, 125, 'test1', 'multiple_choice', 1, 1, '2024-12-05 19:39:52'),
(41, 125, 'testss', 'true_false', 1, 2, '2024-12-05 19:39:52'),
(42, 125, 'test', 'short_answer', 1, 3, '2024-12-05 19:39:52'),
(45, 128, 'test', 'multiple_choice', 1, 1, '2024-12-05 21:06:12'),
(46, 128, 'test', 'true_false', 1, 2, '2024-12-05 21:06:12'),
(47, 128, 'test', 'short_answer', 1, 3, '2024-12-05 21:06:12'),
(50, 131, 'test', 'multiple_choice', 1, 1, '2024-12-05 22:03:12'),
(51, 131, 'test', 'true_false', 1, 2, '2024-12-05 22:03:12'),
(52, 131, 'test', 'short_answer', 1, 3, '2024-12-05 22:03:12'),
(60, 135, 'test', 'true_false', 1, 2, '2024-12-05 22:07:43'),
(61, 135, 'test', 'short_answer', 1, 3, '2024-12-05 22:07:43'),
(63, 136, '', 'true_false', 1, 2, '2024-12-05 23:24:57'),
(64, 136, '', 'short_answer', 1, 3, '2024-12-05 23:24:57'),
(65, 136, '', 'true_false', 1, 4, '2024-12-05 23:24:57'),
(66, 136, '', 'short_answer', 1, 5, '2024-12-05 23:24:57'),
(72, 136, '', 'short_answer', 1, 6, '2024-12-05 23:46:35'),
(73, 136, '', 'true_false', 1, 7, '2024-12-05 23:49:32'),
(74, 136, '', 'short_answer', 1, 8, '2024-12-05 23:49:41'),
(75, 136, '', 'true_false', 1, 9, '2024-12-06 00:30:23'),
(86, 136, '', 'short_answer', 1, 0, '2024-12-06 00:45:56'),
(87, 136, '', 'short_answer', 1, 0, '2024-12-06 00:46:06'),
(88, 137, '', 'short_answer', 1, 1, '2024-12-06 07:21:04'),
(89, 136, '', 'short_answer', 1, 0, '2024-12-06 07:35:27'),
(90, 137, 'testaasdasd', 'multiple_choice', 1, 0, '2024-12-06 07:38:34'),
(91, 138, 'ASD', 'multiple_choice', 1, 1, '2024-12-06 07:58:55'),
(92, 138, 'ASD', 'true_false', 1, 2, '2024-12-06 07:58:55'),
(93, 138, 'asd', 'short_answer', 1, 3, '2024-12-06 07:58:56'),
(94, 138, 'asd', 'multiple_choice', 1, 4, '2024-12-06 07:58:56'),
(95, 139, 'ASD', 'multiple_choice', 1, 1, '2024-12-06 09:27:30'),
(96, 139, 'asd', 'true_false', 1, 2, '2024-12-06 09:27:30'),
(97, 139, 'asd', 'short_answer', 1, 3, '2024-12-06 09:27:30'),
(98, 140, 'ASD', 'multiple_choice', 1, 1, '2024-12-06 09:47:47'),
(99, 140, 'TEST', 'true_false', 1, 2, '2024-12-06 09:47:47'),
(100, 140, 'TEST', 'short_answer', 1, 3, '2024-12-06 09:47:47'),
(101, 140, 'asd', 'multiple_choice', 1, 4, '2024-12-06 09:47:47'),
(102, 140, 'asd', 'true_false', 1, 5, '2024-12-06 09:47:47'),
(103, 140, 'asd', 'short_answer', 1, 6, '2024-12-06 09:47:47'),
(104, 141, 'ASD', 'multiple_choice', 1, 1, '2024-12-06 10:41:43'),
(105, 141, 'asd', 'true_false', 1, 2, '2024-12-06 10:41:43'),
(106, 141, 'asd', 'short_answer', 1, 3, '2024-12-06 10:41:43'),
(107, 141, 'asd', 'multiple_choice', 1, 4, '2024-12-06 10:41:43'),
(108, 142, 'asd', 'multiple_choice', 1, 1, '2024-12-06 16:09:50'),
(109, 142, 'test', 'true_false', 1, 2, '2024-12-06 16:09:51'),
(110, 142, 'test', 'multiple_choice', 1, 3, '2024-12-06 16:09:51'),
(111, 142, 'test', 'short_answer', 1, 4, '2024-12-06 16:09:51'),
(112, 142, 'test', 'short_answer', 1, 5, '2024-12-06 16:09:51'),
(113, 146, 'test', 'multiple_choice', 1, 1, '2024-12-08 17:37:51'),
(114, 146, 'is that true?', 'true_false', 1, 2, '2024-12-08 17:37:51'),
(115, 146, 'test1', 'short_answer', 1, 3, '2024-12-08 17:37:51'),
(116, 146, 'test2 1+1', 'short_answer', 1, 4, '2024-12-08 17:37:51'),
(117, 146, 'test3', 'short_answer', 1, 5, '2024-12-08 17:37:51'),
(118, 146, 'tes', 'multiple_choice', 1, 6, '2024-12-08 17:37:51'),
(119, 146, 'hehe', 'true_false', 1, 7, '2024-12-08 17:37:51'),
(120, 147, 'Matutulog na', 'multiple_choice', 2, 1, '2024-12-08 17:44:42'),
(121, 147, 'Pogi ba ako', 'true_false', 2, 2, '2024-12-08 17:44:42'),
(122, 147, 'Gaano kalaki yung ulo ko', 'short_answer', 6, 3, '2024-12-08 17:44:42'),
(123, 148, 'test', 'multiple_choice', 2, 1, '2024-12-08 18:03:38'),
(124, 148, 'test', 'true_false', 1, 2, '2024-12-08 18:03:38'),
(125, 148, 'give my name', 'short_answer', 7, 3, '2024-12-08 18:03:38'),
(126, 149, 'test', 'multiple_choice', 1, 1, '2024-12-08 18:13:09'),
(127, 149, 'test', 'true_false', 1, 2, '2024-12-08 18:13:09'),
(128, 149, 'asd', 'short_answer', 1, 3, '2024-12-08 18:13:09'),
(129, 150, 'Papasok ka ba', 'multiple_choice', 1, 1, '2024-12-09 03:15:10'),
(130, 150, 'Lalaki ka ba', 'true_false', 1, 2, '2024-12-09 03:15:10'),
(131, 150, 'Lalaki ka ba', 'short_answer', 1, 3, '2024-12-09 03:15:10'),
(132, 151, 'Test 1', 'multiple_choice', 1, 1, '2024-12-09 04:56:56'),
(133, 151, 'Pogi si pacific', 'true_false', 1, 2, '2024-12-09 04:56:56'),
(134, 151, '1+3', 'short_answer', 1, 3, '2024-12-09 04:56:56'),
(135, 151, 'Test 1', 'short_answer', 1, 4, '2024-12-09 04:56:56'),
(136, 154, 'What does SQL stand for?', 'multiple_choice', 1, 1, '2024-12-09 08:13:59');

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
(20, 'Ruby', '7', NULL, '2024-2025', 'active', '2024-11-23 08:30:56'),
(21, 'Ice', '8', NULL, '2024-2025', 'active', '2024-11-24 11:43:42');

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
(46, 20, 21, 9, 1, 'Tuesday', '03:00:00', 'inactive', '2024-11-23 08:34:07', 'CMRH1047'),
(52, 21, 21, 9, 1, 'Monday', '00:00:00', 'inactive', '2024-11-24 11:43:50', 'CMRH1662'),
(53, 20, 22, 123, 1, 'Monday', '00:00:00', 'inactive', '2024-12-01 04:29:42', NULL),
(54, 21, 23, 124, 1, 'Monday', '00:00:00', 'active', '2024-12-01 04:29:54', 'CMRH7392'),
(55, 21, 24, 125, 1, 'Monday', '00:00:00', 'active', '2024-12-01 04:30:12', NULL),
(56, 20, 23, 9, 1, 'Monday', '21:09:00', 'active', '2024-12-03 12:09:14', 'CMRH7513');

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
(145, 37, 113, '', 'Student attempted to page leave', '2024-12-03 16:43:15'),
(146, 37, 113, 'fullscreen_exit', 'Student attempted to fullscreen exit', '2024-12-03 16:43:17'),
(147, 50, 113, 'fullscreen_exit', 'Student attempted to fullscreen exit', '2024-12-03 17:32:21'),
(148, 50, 113, 'fullscreen_exit', 'Student attempted to fullscreen exit', '2024-12-03 17:32:24'),
(149, 50, 113, '', 'Student attempted to page leave', '2024-12-03 17:32:25'),
(150, 50, 113, 'fullscreen_exit', 'Student attempted to fullscreen exit', '2024-12-03 17:32:26'),
(151, 50, 124, '', 'Student attempted to page leave', '2024-12-03 17:51:23'),
(152, 50, 124, 'fullscreen_exit', 'Student attempted to fullscreen exit', '2024-12-03 17:51:24'),
(153, 50, 116, 'fullscreen_exit', 'Student attempted to fullscreen exit', '2024-12-03 17:53:49'),
(154, 50, 116, 'tab_switch', 'Student attempted to tab switch', '2024-12-03 17:53:54'),
(155, 50, 116, 'fullscreen_exit', 'Student attempted to fullscreen exit', '2024-12-03 17:54:01'),
(156, 50, 116, '', 'Student attempted to page leave', '2024-12-03 17:54:01'),
(157, 50, 125, '', 'Student attempted to page leave', '2024-12-05 19:40:46'),
(158, 50, 125, 'fullscreen_exit', 'Student attempted to fullscreen exit', '2024-12-05 19:40:47'),
(159, 50, 125, 'fullscreen_exit', 'Student attempted to fullscreen exit', '2024-12-05 19:41:37'),
(160, 50, 125, 'tab_switch', 'Student attempted to tab switch', '2024-12-05 19:41:59');

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
(36, '123456789012', '1f2a16874f2415f72ef214a59f0a9225', 'kingpacifico0021@gmail.com', '09701640382', 'Male', '2003-03-05', 'Juan', 'Dela Cruz', 'Ilagan', '../uploads/students/profile/student_36_1732414950.jpg', '', 'active', 1, NULL, 0, '2024-11-23 14:59:04', '2024-12-10 13:52:51', NULL, NULL, NULL, 'yes'),
(37, '123456789013', 'ae906b81ef8a71e4c89193ac74057105', 'maria.santos@example.com', '09987654321', 'Female', NULL, 'Maria', 'Santos', 'Garcia', NULL, '', 'active', 0, NULL, 0, '2024-11-23 14:59:04', '2024-12-10 13:43:15', NULL, NULL, NULL, 'no'),
(38, '123456789999', '469e7e66cff79f931488a5feee1909e6', 'christian@frncszxc.helioho.st', '09701333333', 'Male', NULL, 'Edrian', 'Pacifico', 'Ilagan', NULL, '', 'active', 0, NULL, 0, '2024-11-24 12:45:19', '2024-11-24 13:08:12', NULL, NULL, NULL, 'no'),
(39, '100000999999', 'f8c7c5d26055adb57617e687dbf6811c', 'eleanorpacifico@gmail.com', '09701640382', 'Male', NULL, 'Christopher', 'Pacifico', 'Ilagan', NULL, '', 'active', 0, NULL, 0, '2024-11-24 14:48:34', '2024-11-24 14:48:34', NULL, NULL, NULL, 'no'),
(40, '123456789111', '9acb31bd27f2bb425f0b07c9b5322ed3', 'student1@gmail.com', '09504222701', 'Male', NULL, 'student1', 'student1', 'student1', NULL, '', 'active', 0, NULL, 0, '2024-12-01 05:36:59', '2024-12-01 05:37:16', NULL, NULL, NULL, 'no'),
(41, '125496378521', '1eeb5257c0d16483011332b7242c2a49', 'juan.delacruz@example.com', '09231231231', 'Male', NULL, 'Santos', 'Sifia', 'Hanna', NULL, '', 'active', 0, NULL, 0, '2024-12-02 11:16:34', '2024-12-02 11:16:34', NULL, NULL, NULL, 'no'),
(43, '145839472615', '4e868216f9197d362b85403281b602cf', 'camerlalacoster@gmail.com', '09987651345', 'Female', NULL, 'Camerla', 'Lacoste', 'a', NULL, '', 'active', 0, NULL, 0, '2024-12-02 11:16:34', '2024-12-02 11:16:34', NULL, NULL, NULL, 'no'),
(44, '156438927541', '17ce4c9b4085e98d644aaec172aa85ac', 'Dave@gmail.com', '09987654324', 'Female', NULL, 'Dave', 'dave', 'a', NULL, '', 'active', 0, NULL, 0, '2024-12-02 11:16:34', '2024-12-02 11:16:34', NULL, NULL, NULL, 'no'),
(45, '167495328716', 'c26a1a82cd5de3ac31bc3fa4138b2f38', 'juansantos@gmail.com', '09987654325', 'Female', NULL, 'Juan', 'Santos', 'a', NULL, '', 'active', 0, NULL, 0, '2024-12-02 11:16:34', '2024-12-02 11:16:34', NULL, NULL, NULL, 'no'),
(46, '178362549817', 'bb4b484a2fecf6f2f8f573c5f80961c9', 'princesscezar@gmail.com', '09987654326', 'Female', NULL, 'Princess', 'Cezar', 'a', NULL, '', 'active', 0, NULL, 0, '2024-12-02 11:16:34', '2024-12-02 11:16:34', NULL, NULL, NULL, 'no'),
(47, '189475638291', 'e4216c24f075afce33eef8ea3e9214c5', 'Laingonzales@gamil.com', '09987654327', 'Female', NULL, 'Laine', 'Gonzales', 'a', NULL, '', 'active', 0, NULL, 0, '2024-12-02 11:16:34', '2024-12-02 11:16:34', NULL, NULL, NULL, 'no'),
(48, '198546372489', '36cb8bdf319492eb0dbac3966ad1997e', 'roanaguilar@gamil.com', '099876543219', 'Female', NULL, 'Rona', 'Aguilar', 'a', NULL, '', 'active', 0, NULL, 0, '2024-12-02 11:16:34', '2024-12-02 11:16:34', NULL, NULL, NULL, 'no'),
(49, '135728496153', 'ba49e01d6b14707741e7fdf56f55046a', 'mari2a.santos@example.com', '09987654321', 'Female', NULL, 'Lovely', 'lovely', 'a', NULL, '', 'active', 0, NULL, 0, '2024-12-02 11:17:00', '2024-12-02 11:17:00', NULL, NULL, NULL, 'no'),
(50, '999999999999', '8ccb29db1ea08e210d6d54002ada3c23', 'dashotz14@gmail.com', '09208040444', 'Male', NULL, 'Francis', 'Cruz', '', NULL, '', 'active', 0, NULL, 0, '2024-12-03 17:16:52', '2024-12-10 13:50:20', NULL, NULL, NULL, 'no');

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
  `total_answers` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_activity_submissions`
--

INSERT INTO `student_activity_submissions` (`submission_id`, `student_id`, `activity_id`, `points`, `feedback`, `file_path`, `submitted_at`, `status`, `graded_at`, `graded_by`, `late_submission`, `remarks`, `submission_date`, `created_at`, `updated_at`, `security_violation`, `violation_type`, `time_spent`, `score`, `score_percentage`, `total_questions`, `answered_questions`, `correct_answers`, `total_answers`) VALUES
(53, 37, 92, 99, '', NULL, '2024-12-01 04:46:55', 'graded', '2024-12-06 16:53:49', 9, 0, NULL, '2024-12-01 04:46:55', '2024-12-01 04:46:55', '2024-12-06 16:53:49', 0, NULL, NULL, NULL, 0.00, 0, 0, 0, NULL),
(54, 37, 93, 100, NULL, NULL, '2024-12-01 04:57:23', 'graded', '2024-12-01 05:02:58', 123, 0, NULL, '2024-12-01 04:57:23', '2024-12-01 04:57:23', '2024-12-01 05:02:58', 0, NULL, NULL, NULL, 0.00, 0, 0, 0, NULL),
(55, 37, 94, 89, '', NULL, '2024-12-01 05:00:52', 'graded', '2024-12-06 16:58:29', 9, 0, NULL, '2024-12-01 05:00:52', '2024-12-01 05:00:52', '2024-12-06 16:58:29', 0, NULL, NULL, NULL, 0.00, 0, 0, 0, NULL),
(85, 50, 125, 0, NULL, NULL, '2024-12-05 21:24:49', 'submitted', NULL, NULL, 0, NULL, '2024-12-05 21:24:49', '2024-12-05 21:24:49', '2024-12-05 21:24:49', 0, NULL, 2, NULL, 0.00, 0, 0, 0, NULL),
(100, 50, 131, 2, NULL, NULL, '2024-12-05 22:12:37', 'submitted', NULL, NULL, 0, NULL, '2024-12-05 22:12:37', '2024-12-05 22:12:37', '2024-12-05 22:12:37', 0, NULL, 3, NULL, 0.00, 0, 0, 0, NULL),
(101, 50, 135, 3, NULL, NULL, '2024-12-05 22:12:52', 'submitted', NULL, NULL, 0, NULL, '2024-12-05 22:12:52', '2024-12-05 22:12:52', '2024-12-05 22:12:52', 0, NULL, 2, NULL, 0.00, 0, 0, 0, NULL),
(104, 50, 137, 1, NULL, NULL, '2024-12-06 07:57:53', 'submitted', NULL, NULL, 0, NULL, '2024-12-06 07:57:53', '2024-12-06 07:57:53', '2024-12-06 07:57:53', 0, NULL, 4, NULL, 0.00, 0, 0, 0, NULL),
(111, 50, 138, 3, NULL, NULL, '2024-12-06 09:26:46', 'submitted', NULL, NULL, 0, NULL, '2024-12-06 09:26:46', '2024-12-06 09:26:46', '2024-12-06 09:26:46', 0, NULL, 3, NULL, 0.00, 0, 0, 0, NULL),
(125, 50, 139, 3, NULL, NULL, '2024-12-06 10:34:59', 'submitted', NULL, NULL, 0, NULL, '2024-12-06 10:34:59', '2024-12-06 10:34:59', '2024-12-06 10:34:59', 0, NULL, 3, NULL, 0.00, 0, 0, 0, NULL),
(148, 50, 141, 4, NULL, NULL, '2024-12-06 16:04:12', 'submitted', NULL, NULL, 0, NULL, '2024-12-06 16:04:12', '2024-12-06 16:04:12', '2024-12-06 16:04:12', 0, NULL, 4, NULL, 0.00, 0, 0, 4, 4),
(149, 50, 140, 6, NULL, NULL, '2024-12-06 16:06:14', 'submitted', NULL, NULL, 0, NULL, '2024-12-06 16:06:14', '2024-12-06 16:06:14', '2024-12-06 16:06:14', 0, NULL, 10, NULL, 0.00, 0, 0, 6, 6),
(157, 50, 142, 0, NULL, NULL, '2024-12-06 16:49:06', 'missing', NULL, NULL, 0, NULL, '2024-12-06 16:49:06', '2024-12-06 16:49:06', '2024-12-06 16:49:06', 1, NULL, 13, NULL, 0.00, 0, 0, 0, 5),
(158, 50, 94, 99, '', NULL, '2024-12-06 16:54:34', 'submitted', '2024-12-06 16:58:34', NULL, 0, NULL, '2024-12-06 16:54:34', '2024-12-06 16:54:34', '2024-12-06 16:58:34', 0, NULL, NULL, NULL, 0.00, 0, 0, 0, NULL),
(159, 50, 143, 88, 'tes', NULL, '2024-12-06 17:03:05', 'submitted', '2024-12-06 17:03:19', NULL, 0, NULL, '2024-12-06 17:03:05', '2024-12-06 17:03:05', '2024-12-06 17:03:19', 0, NULL, NULL, NULL, 0.00, 0, 0, 0, NULL),
(165, 50, 145, 89, '', NULL, '2024-12-06 17:18:38', 'submitted', '2024-12-06 17:22:54', NULL, 0, NULL, '2024-12-06 17:18:38', '2024-12-06 17:18:38', '2024-12-06 17:22:54', 0, NULL, NULL, NULL, 0.00, 0, 0, 0, NULL),
(166, 50, 93, NULL, NULL, NULL, '2024-12-06 17:25:16', 'submitted', NULL, NULL, 0, NULL, '2024-12-06 17:25:16', '2024-12-06 17:25:16', '2024-12-06 17:25:16', 0, NULL, NULL, NULL, 0.00, 0, 0, 0, NULL),
(169, 50, 147, 0, NULL, NULL, '2024-12-08 17:51:19', 'submitted', NULL, NULL, 0, NULL, '2024-12-08 17:51:19', '2024-12-08 17:51:19', '2024-12-08 17:51:19', 0, NULL, 17, NULL, 0.00, 0, 0, 0, 3),
(172, 50, 146, 3, NULL, NULL, '2024-12-08 18:12:21', 'submitted', NULL, NULL, 0, NULL, '2024-12-08 18:12:21', '2024-12-08 18:12:21', '2024-12-08 18:12:21', 0, NULL, 14, NULL, 0.00, 0, 0, 3, 7),
(173, 50, 148, 3, NULL, NULL, '2024-12-08 18:12:44', 'submitted', NULL, NULL, 0, NULL, '2024-12-08 18:12:44', '2024-12-08 18:12:44', '2024-12-08 18:12:44', 0, NULL, 18, NULL, 0.00, 0, 0, 2, 3),
(174, 50, 149, 0, NULL, NULL, '2024-12-08 18:13:31', 'submitted', NULL, NULL, 0, NULL, '2024-12-08 18:13:31', '2024-12-08 18:13:31', '2024-12-08 18:13:31', 0, NULL, 13, NULL, 0.00, 0, 0, 0, 3),
(175, 50, 150, 3, NULL, NULL, '2024-12-09 03:15:45', 'submitted', NULL, NULL, 0, NULL, '2024-12-09 03:15:45', '2024-12-09 03:15:45', '2024-12-09 03:15:45', 0, NULL, 29, NULL, 0.00, 0, 0, 3, 3),
(176, 50, 151, 3, NULL, NULL, '2024-12-09 04:59:53', 'submitted', NULL, NULL, 0, NULL, '2024-12-09 04:59:53', '2024-12-09 04:59:53', '2024-12-09 04:59:53', 0, NULL, 18, NULL, 0.00, 0, 0, 3, 4);

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
(27, 50, 138, 91, 168, '', 1, '2024-12-06 09:26:46'),
(28, 50, 138, 92, NULL, '', 0, '2024-12-06 09:26:46'),
(29, 50, 138, 93, NULL, '0', 1, '2024-12-06 09:26:46'),
(30, 50, 138, 94, 172, '', 1, '2024-12-06 09:26:46'),
(73, 50, 139, 95, 174, '', 1, '2024-12-06 10:34:59'),
(74, 50, 139, 96, 176, '', 1, '2024-12-06 10:34:59'),
(75, 50, 139, 97, NULL, '0', 1, '2024-12-06 10:34:59'),
(171, 50, 141, 104, 186, '0', 1, '2024-12-06 16:04:12'),
(172, 50, 141, 105, 188, '0', 1, '2024-12-06 16:04:12'),
(173, 50, 141, 106, NULL, '0', 1, '2024-12-06 16:04:12'),
(174, 50, 141, 107, 190, '0', 1, '2024-12-06 16:04:12'),
(175, 50, 140, 98, 178, '0', 1, '2024-12-06 16:06:14'),
(176, 50, 140, 99, 180, '0', 1, '2024-12-06 16:06:14'),
(177, 50, 140, 100, NULL, '0', 1, '2024-12-06 16:06:14'),
(178, 50, 140, 101, 182, '0', 1, '2024-12-06 16:06:14'),
(179, 50, 140, 102, 184, '0', 1, '2024-12-06 16:06:14'),
(180, 50, 140, 103, NULL, '0', 1, '2024-12-06 16:06:14'),
(216, 50, 142, 108, NULL, '0', 0, '2024-12-06 16:49:06'),
(217, 50, 142, 109, NULL, '0', 0, '2024-12-06 16:49:06'),
(218, 50, 142, 110, NULL, '0', 0, '2024-12-06 16:49:06'),
(219, 50, 142, 111, NULL, '0', 0, '2024-12-06 16:49:06'),
(220, 50, 142, 112, NULL, '0', 0, '2024-12-06 16:49:06'),
(231, 50, 147, 120, 211, '', 0, '2024-12-08 17:51:19'),
(232, 50, 147, 121, 214, '', 0, '2024-12-08 17:51:19'),
(233, 50, 147, 122, NULL, 'a', 0, '2024-12-08 17:51:19'),
(240, 50, 146, 113, 198, '', 0, '2024-12-08 18:12:21'),
(241, 50, 146, 114, 202, '', 1, '2024-12-08 18:12:21'),
(242, 50, 146, 115, NULL, 'asd', 1, '2024-12-08 18:12:21'),
(243, 50, 146, 116, NULL, 'asd', 0, '2024-12-08 18:12:21'),
(244, 50, 146, 117, NULL, 'sad', 0, '2024-12-08 18:12:21'),
(245, 50, 146, 118, 204, '', 1, '2024-12-08 18:12:21'),
(246, 50, 146, 119, 208, '', 0, '2024-12-08 18:12:21'),
(247, 50, 148, 123, 216, '', 1, '2024-12-08 18:12:44'),
(248, 50, 148, 124, 220, '', 1, '2024-12-08 18:12:44'),
(249, 50, 148, 125, NULL, 'asdasd', 0, '2024-12-08 18:12:44'),
(250, 50, 149, 126, 222, '', 0, '2024-12-08 18:13:31'),
(251, 50, 149, 127, 226, '', 0, '2024-12-08 18:13:31'),
(252, 50, 149, 128, NULL, '111', 0, '2024-12-08 18:13:31'),
(253, 50, 150, 129, 228, '', 1, '2024-12-09 03:15:45'),
(254, 50, 150, 130, 231, '', 1, '2024-12-09 03:15:45'),
(255, 50, 150, 131, NULL, 'OO', 1, '2024-12-09 03:15:45'),
(256, 50, 151, 132, 234, '', 1, '2024-12-09 04:59:53'),
(257, 50, 151, 133, 237, '', 1, '2024-12-09 04:59:53'),
(258, 50, 151, 134, NULL, '4', 1, '2024-12-09 04:59:53'),
(259, 50, 151, 135, NULL, 'testdawd', 0, '2024-12-09 04:59:53');

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
(80, 2147483647, '::1', 'success', '2024-11-23 07:36:27'),
(84, 36, '::1', 'success', '2024-11-23 14:59:22'),
(85, 36, '::1', 'success', '2024-11-23 15:08:16'),
(86, 36, '::1', 'success', '2024-11-24 01:34:16'),
(87, 36, '::1', 'success', '2024-11-24 01:35:59'),
(88, 36, '::1', 'success', '2024-11-24 01:53:37'),
(89, 36, '::1', 'success', '2024-11-24 02:01:10'),
(90, 36, '::1', 'success', '2024-11-24 02:04:30'),
(91, 36, '::1', 'success', '2024-11-24 02:07:19'),
(92, 36, '::1', 'success', '2024-11-24 02:07:32'),
(93, 36, '::1', 'success', '2024-11-24 02:10:11'),
(94, 36, '::1', 'success', '2024-11-24 02:11:57'),
(95, 36, '::1', 'success', '2024-11-24 02:13:13'),
(96, 36, '::1', 'success', '2024-11-24 02:19:42'),
(97, 36, '::1', 'success', '2024-11-24 02:28:35'),
(98, 36, '::1', 'success', '2024-11-24 02:30:33'),
(99, 36, '::1', 'success', '2024-11-24 02:32:54'),
(100, 36, '::1', 'success', '2024-11-24 02:33:59'),
(101, 36, '::1', 'success', '2024-11-24 02:35:33'),
(102, 36, '::1', 'success', '2024-11-24 02:36:01'),
(103, 36, '::1', 'success', '2024-11-24 02:40:46'),
(104, 36, '::1', 'success', '2024-11-24 02:41:55'),
(105, 36, '::1', 'success', '2024-11-24 02:46:51'),
(106, 36, '::1', 'success', '2024-11-24 02:50:52'),
(107, 36, '::1', 'success', '2024-11-24 02:51:15'),
(108, 36, '::1', 'success', '2024-11-24 02:51:23'),
(109, 36, '::1', 'success', '2024-11-24 02:53:52'),
(110, 36, '::1', 'success', '2024-11-24 02:55:22'),
(111, 36, '::1', 'success', '2024-11-24 02:57:23'),
(112, 36, '::1', 'success', '2024-11-24 02:58:44'),
(113, 36, '::1', 'success', '2024-11-24 03:02:10'),
(114, 36, '::1', 'success', '2024-11-24 03:04:14'),
(115, 36, '::1', 'success', '2024-11-24 03:05:47'),
(116, 36, '::1', 'success', '2024-11-24 03:14:06'),
(117, 36, '::1', 'success', '2024-11-24 03:36:21'),
(118, 36, '::1', 'success', '2024-11-24 04:11:31'),
(119, 36, '::1', 'success', '2024-11-24 06:56:18'),
(120, 36, '::1', 'success', '2024-11-24 07:04:55'),
(121, 36, '::1', 'success', '2024-11-24 07:29:29'),
(122, 37, '::1', 'success', '2024-11-24 11:34:19'),
(123, 36, '::1', 'success', '2024-11-24 11:43:17'),
(124, 36, '::1', 'logout', '2024-11-24 12:29:19'),
(125, 36, '::1', 'success', '2024-11-24 12:29:34'),
(126, 36, '::1', 'logout', '2024-11-24 12:44:09'),
(127, 37, '::1', 'success', '2024-11-24 13:43:54'),
(128, 37, '::1', 'logout', '2024-11-24 13:44:04'),
(129, 36, '::1', 'success', '2024-11-24 13:49:44'),
(130, 36, '::1', 'logout', '2024-11-24 13:49:56'),
(131, 36, '::1', 'success', '2024-11-24 14:25:24'),
(132, 36, '::1', 'success', '2024-11-24 14:52:23'),
(133, 36, '::1', 'success', '2024-11-25 01:50:19'),
(134, 36, '::1', 'success', '2024-11-25 02:20:36'),
(135, 36, '::1', 'success', '2024-11-25 02:37:45'),
(136, 37, '::1', 'success', '2024-11-25 02:38:08'),
(137, 36, '::1', 'logout', '2024-11-25 02:44:45'),
(138, 36, '::1', 'success', '2024-11-29 16:17:20'),
(139, 36, '::1', 'logout', '2024-11-29 16:17:23'),
(140, 36, '::1', 'success', '2024-11-29 16:24:34'),
(141, 36, '::1', 'logout', '2024-11-29 16:45:31'),
(142, 36, '::1', '', '2024-11-29 16:49:10'),
(143, 36, '::1', 'logout', '2024-11-29 16:50:00'),
(144, 36, '::1', 'success', '2024-11-29 16:50:05'),
(145, 36, '::1', 'logout', '2024-11-29 16:50:07'),
(146, 36, '::1', 'success', '2024-11-29 16:50:22'),
(147, 37, '::1', 'success', '2024-12-01 03:55:46'),
(148, 37, '::1', 'success', '2024-12-01 04:04:08'),
(149, 37, '::1', 'success', '2024-12-01 04:43:58'),
(150, 37, '::1', 'logout', '2024-12-01 04:51:24'),
(151, 37, '::1', 'success', '2024-12-01 04:52:04'),
(152, 37, '::1', 'success', '2024-12-01 05:00:27'),
(153, 36, '::1', 'success', '2024-12-01 05:23:03'),
(154, 36, '::1', 'success', '2024-12-01 05:25:11'),
(155, 36, '::1', 'logout', '2024-12-01 05:43:17'),
(156, 36, '::1', '', '2024-12-01 12:41:52'),
(157, 37, '::1', 'success', '2024-12-03 12:04:04'),
(158, 50, '::1', 'success', '2024-12-03 17:17:24'),
(159, 50, '::1', 'success', '2024-12-05 19:40:28'),
(160, 50, '::1', 'success', '2024-12-05 21:15:12'),
(161, 50, '180.194.233.34', 'success', '2024-12-08 17:38:23'),
(162, 50, '49.144.12.254', 'success', '2024-12-08 17:42:45'),
(163, 50, '49.144.12.254', 'logout', '2024-12-08 17:46:14'),
(164, 50, '49.144.12.254', 'success', '2024-12-08 17:50:55'),
(165, 50, '180.194.233.34', 'success', '2024-12-08 18:03:55'),
(166, 50, '175.176.36.21', 'success', '2024-12-09 03:13:01'),
(167, 50, '175.176.36.21', 'logout', '2024-12-09 03:17:29'),
(168, 50, '175.176.36.21', 'success', '2024-12-09 03:17:42'),
(169, 50, '175.176.36.21', 'logout', '2024-12-09 03:18:10'),
(170, 50, '136.158.49.29', 'success', '2024-12-09 04:17:25'),
(171, 50, '175.176.36.21', 'success', '2024-12-09 04:58:20'),
(172, 50, '136.158.49.29', 'success', '2024-12-09 07:43:47'),
(173, 50, '136.158.49.29', 'success', '2024-12-09 08:36:37'),
(174, 50, '::1', 'success', '2024-12-10 11:31:47'),
(175, 50, '::1', 'success', '2024-12-10 11:33:30'),
(176, 50, '::1', 'success', '2024-12-10 11:34:37'),
(177, 50, '::1', 'success', '2024-12-10 11:35:07'),
(178, 50, '::1', 'success', '2024-12-10 11:36:43'),
(179, 50, '::1', 'success', '2024-12-10 11:38:22'),
(180, 50, '::1', 'success', '2024-12-10 11:40:20'),
(181, 50, '::1', 'success', '2024-12-10 11:44:35'),
(182, 50, '::1', 'success', '2024-12-10 11:54:25'),
(183, 50, '::1', 'success', '2024-12-10 11:55:59'),
(184, 50, '::1', 'logout', '2024-12-10 11:56:03'),
(185, 50, '::1', 'success', '2024-12-10 11:59:12'),
(186, 50, '::1', 'success', '2024-12-10 12:02:25'),
(187, 50, '::1', 'success', '2024-12-10 12:04:21'),
(188, 50, '::1', 'success', '2024-12-10 12:13:10'),
(189, 50, '::1', 'success', '2024-12-10 12:18:24'),
(190, 50, '::1', 'success', '2024-12-10 12:24:16'),
(191, 37, '::1', 'success', '2024-12-10 13:43:12'),
(192, 50, '::1', 'success', '2024-12-10 13:44:10'),
(193, 50, '::1', 'success', '2024-12-10 13:50:18');

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
(63, 37, 20, 1, '', 'active', '2024-12-01 04:30:32', '2024-12-01 04:30:32'),
(73, 50, 20, 1, '', 'active', '2024-12-09 09:28:02', '2024-12-09 09:28:02');

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
(21, 'ENG', 'English', '', 'Minor', 'English', '2024-11-23 08:31:27', 'inactive', 'All', '2024-12-01 19:09:21'),
(22, 'Fil', 'Fil', '', 'Core', 'Fil', '2024-12-01 04:28:59', 'inactive', 'All', '2024-12-01 19:09:18'),
(23, 'Math', 'Math', '', 'Core', 'Math', '2024-12-01 04:29:10', 'active', 'All', '2024-12-01 04:29:10'),
(24, 'Science', 'Science', '', 'Core', 'Science', '2024-12-01 04:29:22', 'active', 'All', '2024-12-01 04:29:22');

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
(2, 21, '7', '2024-11-23 08:31:27'),
(3, 22, '7', '2024-12-01 04:28:59'),
(4, 23, '7', '2024-12-01 04:29:10'),
(5, 24, '7', '2024-12-01 04:29:22');

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
(31, 158, 'Student Side.docx', 'uploads/activities/50/67532c4a3dc78_1733504074.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 912716, '2024-12-06 16:54:34', '2024-12-06 16:54:34'),
(32, 159, 'Navy and Broken White Geometric Thesis Defense Presentation.pdf', 'uploads/assignments/50/67532e499866a_1733504585.pdf', 'application/pdf', 7196047, '2024-12-06 17:03:05', '2024-12-06 17:03:05'),
(38, 165, 'DCIT-65A-FINAL-PROJECT.pdf', 'uploads/assignments/50/675331eecd0a5_1733505518.pdf', 'application/pdf', 7298057, '2024-12-06 17:18:38', '2024-12-06 17:18:38'),
(39, 166, 'DCIT-65A-FINAL-PROJECT.pdf', 'uploads/activities/50/6753337cd471c_1733505916.pdf', 'application/pdf', 7298057, '2024-12-06 17:25:16', '2024-12-06 17:25:16');

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
(9, 'chaw', '464b57bd033194adbd9ad3e88dd86c8a', 'kingpacifico0021@gmail.com', '../uploads/teachers/profile/teacher_9_1732418651.jpg', 'Christiana', 'Pacifico', 'Ilagan', 3, 'active', 0, NULL, '2024-11-23 08:30:23', '2024-12-10 14:13:12', NULL, NULL, '\"{\\\"dark_mode\\\":false,\\\"compact_view\\\":false}\"', '\"{\\\"auto_grading\\\":false,\\\"allow_late\\\":false,\\\"default_deadline\\\":\\\"07:00\\\"}\"', '\"{\\\"email\\\":false,\\\"submissions\\\":false,\\\"deadlines\\\":false}\"', 0, NULL, 'no'),
(123, 'teacher1', '41c8949aa55b8cb5dbec662f34b62df3', 'teacher1@gmail.com', NULL, 'teacher1', 'teacher1', 'teacher1', 4, 'active', 0, NULL, '2024-12-01 04:05:44', '2024-12-01 04:29:42', NULL, NULL, NULL, NULL, NULL, 0, NULL, 'no'),
(124, 'teacher2', 'ccffb0bb993eeb79059b31e1611ec353', 'teacher2@gmail.com', NULL, 'teacher2', 'teacher2', 'teacher2', 1, 'active', 0, NULL, '2024-12-01 04:06:00', '2024-12-01 04:29:54', NULL, NULL, NULL, NULL, NULL, 0, NULL, 'no'),
(125, 'teacher3', '82470256ea4b80343b27afccbca1015b', 'teacher3@gmail.com', NULL, 'teacher3', 'teacher3', 'teacher3', 2, 'active', 0, NULL, '2024-12-01 04:06:17', '2024-12-01 04:30:12', NULL, NULL, NULL, NULL, NULL, 0, NULL, 'no');

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
(82, 9, '::1', 'success', '2024-12-03 11:47:03'),
(83, 9, '::1', 'success', '2024-12-03 17:17:04'),
(84, 9, '::1', 'success', '2024-12-05 19:39:12'),
(85, 9, '::1', 'success', '2024-12-05 20:56:11'),
(86, 9, '49.144.12.254', 'success', '2024-12-08 17:40:09'),
(87, 9, '180.194.233.34', 'success', '2024-12-08 17:46:20'),
(88, 9, '180.194.233.34', 'success', '2024-12-08 18:12:01'),
(89, 9, '175.176.36.21', 'success', '2024-12-09 03:13:58'),
(90, 9, '175.176.36.21', 'success', '2024-12-09 03:16:03'),
(91, 9, '175.176.36.21', 'success', '2024-12-09 03:16:57'),
(92, 9, '175.176.36.21', 'success', '2024-12-09 04:44:39'),
(93, 9, '136.158.49.167', 'success', '2024-12-09 05:58:46'),
(94, 9, '136.158.49.167', 'success', '2024-12-09 06:02:40'),
(95, 9, '103.91.141.65', 'success', '2024-12-09 07:37:36'),
(96, 9, '::1', 'success', '2024-12-10 11:39:00'),
(97, 9, '::1', 'success', '2024-12-10 12:02:51'),
(98, 9, '::1', 'logout', '2024-12-10 12:02:53'),
(99, 9, '::1', 'success', '2024-12-10 12:04:31'),
(100, 9, '::1', 'logout', '2024-12-10 12:04:42'),
(101, 9, '::1', 'success', '2024-12-10 12:14:07'),
(102, 9, '::1', 'logout', '2024-12-10 12:17:58'),
(103, 9, '::1', 'success', '2024-12-10 12:43:29'),
(104, 9, '::1', 'logout', '2024-12-10 12:43:31'),
(105, 9, '::1', 'success', '2024-12-10 12:59:44'),
(106, 9, '::1', 'logout', '2024-12-10 12:59:47'),
(107, 9, '::1', 'success', '2024-12-10 13:41:52'),
(108, 9, '::1', 'logout', '2024-12-10 13:41:54'),
(109, 9, '::1', 'success', '2024-12-10 13:44:33'),
(110, 9, '::1', 'logout', '2024-12-10 13:44:35'),
(111, 9, '::1', 'logout', '2024-12-10 14:03:08'),
(112, 9, '::1', 'logout', '2024-12-10 14:09:11'),
(113, 9, '::1', 'logout', '2024-12-10 14:11:09'),
(114, 9, '::1', 'success', '2024-12-10 14:11:20'),
(115, 9, '::1', 'success', '2024-12-10 14:13:22');

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
  MODIFY `activity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=156;

--
-- AUTO_INCREMENT for table `activity_files`
--
ALTER TABLE `activity_files`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admin_login_logs`
--
ALTER TABLE `admin_login_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT for table `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT for table `attendance_logs`
--
ALTER TABLE `attendance_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `question_choices`
--
ALTER TABLE `question_choices`
  MODIFY `choice_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=246;

--
-- AUTO_INCREMENT for table `quiz_access_codes`
--
ALTER TABLE `quiz_access_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `quiz_answers`
--
ALTER TABLE `quiz_answers`
  MODIFY `answer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  MODIFY `attempt_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=137;

--
-- AUTO_INCREMENT for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `section_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

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
  MODIFY `student_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `student_activity_submissions`
--
ALTER TABLE `student_activity_submissions`
  MODIFY `submission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=177;

--
-- AUTO_INCREMENT for table `student_answers`
--
ALTER TABLE `student_answers`
  MODIFY `answer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=260;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=194;

--
-- AUTO_INCREMENT for table `student_sections`
--
ALTER TABLE `student_sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `subject_grade_levels`
--
ALTER TABLE `subject_grade_levels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `submission_files`
--
ALTER TABLE `submission_files`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `teacher`
--
ALTER TABLE `teacher`
  MODIFY `teacher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;

--
-- AUTO_INCREMENT for table `teacher_login_logs`
--
ALTER TABLE `teacher_login_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;

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
