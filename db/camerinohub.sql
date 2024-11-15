-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 15, 2024 at 05:01 AM
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
-- Database: `camerinohub`
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
  `year_start` int(11) NOT NULL,
  `year_end` int(11) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `academic_years`
--

INSERT INTO `academic_years` (`id`, `school_year`, `year_start`, `year_end`, `status`, `created_at`) VALUES
(1, '2024-2025', 0, 0, 'active', '2024-11-06 19:28:47');

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
  `due_date` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('active','inactive','archived') DEFAULT 'active',
  `completion_rate` decimal(5,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, 1, '::1', 'success', '2024-11-06 17:42:05'),
(2, 1, '::1', 'logout', '2024-11-06 19:31:52'),
(3, 1, '::1', 'failed', '2024-11-07 04:02:05'),
(4, 1, '::1', 'failed', '2024-11-07 04:02:10'),
(5, 1, '::1', 'failed', '2024-11-07 04:02:29'),
(6, 1, '::1', 'success', '2024-11-07 04:02:35'),
(7, 1, '::1', 'logout', '2024-11-07 04:42:43'),
(8, 1, '::1', 'success', '2024-11-07 04:50:54'),
(9, 1, '::1', 'logout', '2024-11-07 07:38:05'),
(10, 1, '::1', 'success', '2024-11-07 11:23:36'),
(11, 1, '::1', 'logout', '2024-11-07 11:23:43'),
(12, 1, '::1', 'success', '2024-11-07 11:26:49'),
(13, 1, '::1', 'failed', '2024-11-07 13:36:00'),
(14, 1, '::1', 'success', '2024-11-07 13:36:08'),
(15, 1, '::1', 'success', '2024-11-14 08:42:59'),
(16, 1, '::1', 'logout', '2024-11-14 09:19:43'),
(17, 1, '::1', 'failed', '2024-11-14 09:21:42'),
(18, 1, '::1', 'success', '2024-11-14 09:21:47'),
(19, 1, '::1', 'logout', '2024-11-14 10:03:29'),
(20, 1, '::1', 'success', '2024-11-14 10:03:47'),
(21, 1, '::1', 'success', '2024-11-14 10:04:07'),
(22, 1, '::1', 'logout', '2024-11-14 12:02:15'),
(23, 1, '::1', 'success', '2024-11-14 12:02:20'),
(24, 1, '::1', 'success', '2024-11-14 12:04:17'),
(25, 1, '::1', 'logout', '2024-11-14 12:28:58'),
(26, 1, '::1', 'success', '2024-11-14 12:29:09'),
(27, 1, '::1', 'logout', '2024-11-14 13:03:56'),
(28, 1, '::1', 'success', '2024-11-14 13:04:02'),
(29, 1, '::1', 'logout', '2024-11-14 13:04:14'),
(30, 1, '::1', 'success', '2024-11-14 13:04:20'),
(31, 1, '::1', 'logout', '2024-11-14 13:04:24'),
(32, 1, '::1', 'failed', '2024-11-14 13:04:28'),
(33, 1, '::1', 'success', '2024-11-14 13:04:32'),
(34, 1, '::1', 'logout', '2024-11-14 13:06:39'),
(35, 1, '::1', 'logout', '2024-11-14 13:27:48');

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
  `attachment` varchar(255) DEFAULT NULL,
  `status` enum('active','archived') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `type` enum('normal','quiz','activity','assignment') NOT NULL DEFAULT 'normal',
  `priority` enum('low','medium','high') DEFAULT 'medium',
  `reference_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `teacher_id`, `section_id`, `subject_id`, `content`, `attachment`, `status`, `created_at`, `type`, `priority`, `reference_id`) VALUES
(1, 1, 1, 1, '📢 Announcement: Human Society Project\r\n\r\nDear Students,\r\n\r\nWe are excited to announce your upcoming Human Society Project! This project is an opportunity for you to explore, understand, and reflect on the diverse aspects of human society, including culture, community, relationships, and how individuals and groups interact within it.\r\n\r\nProject Details:\r\n\r\nObjective: Develop a project that highlights an element of human society. This could focus on cultural diversity, societal challenges, community contributions, or any topic that helps deepen our understanding of society.\r\nFormat: You may create a presentation, poster, video, report, or a creative piece (e.g., artwork, infographic).\r\nGuidelines: Ensure your project is informative, respectful, and engaging. Feel free to incorporate real-world examples, interviews, and statistics to support your ideas.\r\nImportant Dates:\r\n\r\nProject Topic Submission: [Insert Date]\r\nFinal Project Due Date: [Insert Date]\r\nPresentation Day: [Insert Date]\r\nLet\'s work together to create meaningful projects that contribute to our understanding of the world around us. If you have any questions or need guidance, please don\'t hesitate to reach out.\r\n\r\nGood luck, and I can\'t wait to see the amazing work you\'ll create!', 'uploads/announcements/6735a779233cb.docx', 'active', '2024-11-14 07:32:09', 'normal', 'medium', NULL),
(3, 1, 1, 1, 'A new activity has been posted: \"Introduction to Programming Basics\"\r\n\r\nPlease complete this activity by 11/21/2024.\r\nTotal Points: 100\r\nSection: Pacifico - Code', NULL, 'active', '2024-11-14 15:52:23', 'normal', 'medium', NULL),
(4, 1, 1, 1, 'A new assignment has been posted: \"wads\"\r\n\r\nPlease submit your work by 11/22/2024.\r\nTotal Points: 100\r\nSection: Pacifico - Code', NULL, 'active', '2024-11-15 01:41:37', 'normal', 'medium', NULL),
(5, 1, 1, 1, 'A new activity has been posted: \"asdasdasdasd\"\r\n\r\nPlease complete this activity by 11/22/2024.\r\nTotal Points: 100\r\nSection: Pacifico - Code', NULL, 'active', '2024-11-15 02:34:08', 'normal', 'medium', NULL);

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

--
-- Dumping data for table `announcement_comments`
--

INSERT INTO `announcement_comments` (`id`, `announcement_id`, `student_id`, `comment`, `created_at`) VALUES
(1, 1, 1, 'how', '2024-11-14 07:32:48');

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
  `status` enum('present','absent','late','excused') NOT NULL,
  `time_in` time DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `student_id`, `section_subject_id`, `date`, `status`, `time_in`, `remarks`, `created_at`) VALUES
(1, 42, 1, '2024-11-14', 'absent', NULL, '', '2024-11-14 15:35:29'),
(2, 38, 1, '2024-11-14', 'late', NULL, '', '2024-11-14 15:35:29'),
(3, 2, 1, '2024-11-14', 'excused', NULL, '', '2024-11-14 15:35:29'),
(4, 39, 1, '2024-11-14', 'present', NULL, '', '2024-11-14 15:35:29'),
(5, 1, 1, '2024-11-14', 'present', NULL, '', '2024-11-14 15:35:29'),
(6, 41, 1, '2024-11-14', 'present', NULL, '', '2024-11-14 15:35:29'),
(7, 40, 1, '2024-11-14', 'present', NULL, '', '2024-11-14 15:35:29');

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
(1, 7, 1, 'save', 'Attendance saved for section_subject_id: 1, date: 2024-11-14', '2024-11-14 15:35:29');

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

--
-- Dumping data for table `contact_information`
--

INSERT INTO `contact_information` (`id`, `title`, `email`, `name`, `subject`, `content`, `type`, `status`, `response_status`, `response`, `responded_at`, `ip_address`, `created_at`) VALUES
(12, '', 'student1@camerinohub.edu.ph', 'christian pacifico', 'asdasd', 'asdasdasdad', 'inquiry', '', 'pending', NULL, NULL, '::1', '2024-11-15 01:10:47'),
(13, '', 'student1@camerinohub.edu.ph', 'christian pacifico', 'asdasd', 'asdasdasdad', 'inquiry', '', 'pending', NULL, NULL, '::1', '2024-11-15 01:11:24'),
(14, '', 'student1@camerinohub.edu.ph', 'christian pacifico', 'asd', 'asd', 'inquiry', '', 'pending', NULL, NULL, '::1', '2024-11-15 01:11:53');

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
(1, 2, 0, 0, 'student', 'announcement', 0, NULL, NULL, 'New Announcement', 'Teacher John Doe posted a new announcement', 0, 0, '2024-11-14 07:36:10'),
(2, 1, 0, 0, 'student', 'announcement', 0, NULL, NULL, 'New Announcement', 'Teacher John Doe posted a new announcement', 0, 0, '2024-11-14 07:36:10');

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
(1, 'Pacifico', '7', 1, '', 'active', '2024-11-06 17:17:35'),
(8, 'Daffodil', '7', NULL, '2023-2024', 'active', '2024-11-14 08:45:25'),
(9, 'Aster', '7', NULL, '2023-2024', 'active', '2024-11-14 08:45:25'),
(10, 'Lilac', '7', NULL, '2023-2024', 'active', '2024-11-14 08:45:25'),
(11, 'Daisy', '7', NULL, '2023-2024', 'active', '2024-11-14 08:45:25'),
(12, 'Sampaguita', '7', NULL, '2023-2024', 'active', '2024-11-14 08:45:25'),
(13, 'Sunflower', '7', NULL, '2023-2024', 'active', '2024-11-14 08:45:25'),
(14, 'Tulip', '7', NULL, '2023-2024', 'active', '2024-11-14 08:45:25'),
(15, 'Ruby', '8', NULL, '', 'active', '2024-11-14 11:09:40'),
(19, 'Rubyss', '10', NULL, '2024-2025', 'active', '2024-11-14 11:15:15');

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

--
-- Dumping data for table `section_schedules`
--

INSERT INTO `section_schedules` (`id`, `section_id`, `subject_id`, `teacher_id`, `academic_year_id`, `schedule_day`, `schedule_time`, `status`, `created_at`) VALUES
(1, 1, 10, 2, 1, 'Monday', '08:00:00', 'active', '2024-11-14 13:16:38'),
(2, 14, 10, 2, 1, 'Monday', '09:00:00', 'active', '2024-11-14 13:16:38'),
(3, 13, 10, 2, 1, 'Tuesday', '08:00:00', 'active', '2024-11-14 13:16:38'),
(4, 12, 10, 2, 1, 'Tuesday', '09:00:00', 'active', '2024-11-14 13:16:38'),
(5, 10, 10, 2, 1, 'Wednesday', '08:00:00', 'active', '2024-11-14 13:16:38'),
(6, 11, 10, 2, 1, 'Wednesday', '09:00:00', 'active', '2024-11-14 13:16:38'),
(7, 8, 10, 2, 1, 'Thursday', '08:00:00', 'active', '2024-11-14 13:16:38'),
(8, 9, 10, 2, 1, 'Thursday', '09:00:00', 'active', '2024-11-14 13:16:38');

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
(1, 1, 1, 1, 1, 'Monday', '09:00:00', 'active', '2024-11-06 19:30:48', 'CMRH2410'),
(2, 9, 10, 2, 1, 'Monday', '08:55:00', 'inactive', '2024-11-14 12:55:29', '43C48C45'),
(3, 8, 10, 2, 1, 'Monday', '08:55:00', 'inactive', '2024-11-14 12:55:29', 'FD6E2515'),
(4, 11, 10, 2, 1, 'Monday', '08:55:00', 'inactive', '2024-11-14 12:55:29', '62404C3B'),
(5, 9, 10, 2, 1, 'Monday', '08:56:00', 'inactive', '2024-11-14 12:56:48', '842F8259'),
(6, 8, 10, 2, 1, 'Monday', '08:56:00', 'inactive', '2024-11-14 12:56:48', '8ADD997F'),
(7, 11, 10, 2, 1, 'Monday', '08:56:00', 'inactive', '2024-11-14 12:56:48', 'D8EFA702'),
(8, 1, 10, 2, 1, 'Monday', '08:00:00', 'inactive', '2024-11-14 13:16:38', 'faebc51f'),
(9, 14, 10, 2, 1, 'Monday', '09:00:00', 'inactive', '2024-11-14 13:16:38', '282cff90'),
(10, 13, 10, 2, 1, 'Tuesday', '08:00:00', 'inactive', '2024-11-14 13:16:38', 'a258655c'),
(11, 12, 10, 2, 1, 'Tuesday', '09:00:00', 'inactive', '2024-11-14 13:16:38', 'e5b739ea'),
(12, 10, 10, 2, 1, 'Wednesday', '08:00:00', 'inactive', '2024-11-14 13:16:38', 'ec6c2543'),
(13, 11, 10, 2, 1, 'Wednesday', '09:00:00', 'inactive', '2024-11-14 13:16:38', 'c4d06eb2'),
(14, 8, 10, 2, 1, 'Thursday', '08:00:00', 'inactive', '2024-11-14 13:16:38', '29fb8167'),
(15, 9, 10, 2, 1, 'Thursday', '09:00:00', 'inactive', '2024-11-14 13:16:38', 'be62c27b');

-- --------------------------------------------------------

--
-- Table structure for table `security_violations`
--

CREATE TABLE `security_violations` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `violation_type` varchar(50) NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `student_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `middlename` varchar(100) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `cys` varchar(50) NOT NULL,
  `status` enum('active','inactive','archived','graduated') DEFAULT 'active',
  `login_attempts` int(11) DEFAULT 0,
  `lockout_until` datetime DEFAULT NULL,
  `user_online` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`student_id`, `username`, `password`, `email`, `firstname`, `lastname`, `middlename`, `profile_image`, `cys`, `status`, `login_attempts`, `lockout_until`, `user_online`, `created_at`, `updated_at`) VALUES
(1, 'king', 'b38ce63f61c5be0f1a1906d4d37b8725', 'student1@camerinohub.edu.ph', 'christian', 'pacifico', NULL, '../images/student1.png', 'BSIT-2A', 'active', 0, NULL, 1, '2024-11-06 15:41:01', '2024-11-15 03:58:16'),
(2, 'student2024', 'b38ce63f61c5be0f1a1906d4d37b8725', 'student2024@camerinohub.edu.ph', 'Juan', 'Dela Cruz', 'Santos', NULL, 'Grade 7', 'active', 0, NULL, 0, '2024-11-07 04:50:43', '2024-11-07 04:50:43'),
(3, 'student_aster1', 'b38ce63f61c5be0f1a1906d4d37b8725', 'aster1@camerinohub.edu.ph', 'John', 'Smith', 'A', NULL, 'Grade 7', 'active', 0, NULL, 0, '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(4, 'student_aster2', 'b38ce63f61c5be0f1a1906d4d37b8725', 'aster2@camerinohub.edu.ph', 'Mary', 'Johnson', 'B', NULL, 'Grade 7', 'active', 0, NULL, 0, '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(5, 'student_aster3', 'b38ce63f61c5be0f1a1906d4d37b8725', 'aster3@camerinohub.edu.ph', 'James', 'Williams', 'C', NULL, 'Grade 7', 'active', 0, NULL, 0, '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(6, 'student_aster4', 'b38ce63f61c5be0f1a1906d4d37b8725', 'aster4@camerinohub.edu.ph', 'Emma', 'Brown', 'D', NULL, 'Grade 7', 'active', 0, NULL, 0, '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(7, 'student_aster5', 'b38ce63f61c5be0f1a1906d4d37b8725', 'aster5@camerinohub.edu.ph', 'Oliver', 'Jones', 'E', NULL, 'Grade 7', 'active', 0, NULL, 0, '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(8, 'student_daff1', 'b38ce63f61c5be0f1a1906d4d37b8725', 'daff1@camerinohub.edu.ph', 'Sophia', 'Garcia', 'F', NULL, 'Grade 7', 'active', 0, NULL, 0, '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(9, 'student_daff2', 'b38ce63f61c5be0f1a1906d4d37b8725', 'daff2@camerinohub.edu.ph', 'Lucas', 'Miller', 'G', NULL, 'Grade 7', 'active', 0, NULL, 0, '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(10, 'student_daff3', 'b38ce63f61c5be0f1a1906d4d37b8725', 'daff3@camerinohub.edu.ph', 'Ava', 'Davis', 'H', NULL, 'Grade 7', 'active', 0, NULL, 0, '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(11, 'student_daff4', 'b38ce63f61c5be0f1a1906d4d37b8725', 'daff4@camerinohub.edu.ph', 'Ethan', 'Rodriguez', 'I', NULL, 'Grade 7', 'active', 0, NULL, 0, '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(12, 'student_daff5', 'b38ce63f61c5be0f1a1906d4d37b8725', 'daff5@camerinohub.edu.ph', 'Isabella', 'Martinez', 'J', NULL, 'Grade 7', 'active', 0, NULL, 0, '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(13, 'student_daisy1', 'b38ce63f61c5be0f1a1906d4d37b8725', 'daisy1@camerinohub.edu.ph', 'Mason', 'Anderson', 'K', NULL, 'Grade 7', 'active', 0, NULL, 0, '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(14, 'student_daisy2', 'b38ce63f61c5be0f1a1906d4d37b8725', 'daisy2@camerinohub.edu.ph', 'Charlotte', 'Taylor', 'L', NULL, 'Grade 7', 'active', 0, NULL, 0, '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(15, 'student_daisy3', 'b38ce63f61c5be0f1a1906d4d37b8725', 'daisy3@camerinohub.edu.ph', 'Alexander', 'Thomas', 'M', NULL, 'Grade 7', 'active', 0, NULL, 0, '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(16, 'student_daisy4', 'b38ce63f61c5be0f1a1906d4d37b8725', 'daisy4@camerinohub.edu.ph', 'Amelia', 'Hernandez', 'N', NULL, 'Grade 7', 'active', 0, NULL, 0, '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(17, 'student_daisy5', 'b38ce63f61c5be0f1a1906d4d37b8725', 'daisy5@camerinohub.edu.ph', 'Henry', 'Moore', 'O', NULL, 'Grade 7', 'active', 0, NULL, 0, '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(18, 'student_lilac1', 'b38ce63f61c5be0f1a1906d4d37b8725', 'lilac1@camerinohub.edu.ph', 'Mia', 'Martin', 'P', NULL, 'Grade 7', 'active', 0, NULL, 0, '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(19, 'student_lilac2', 'b38ce63f61c5be0f1a1906d4d37b8725', 'lilac2@camerinohub.edu.ph', 'Sebastian', 'Jackson', 'Q', NULL, 'Grade 7', 'active', 0, NULL, 0, '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(20, 'student_lilac3', 'b38ce63f61c5be0f1a1906d4d37b8725', 'lilac3@camerinohub.edu.ph', 'Harper', 'Thompson', 'R', NULL, 'Grade 7', 'active', 0, NULL, 0, '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(21, 'student_lilac4', 'b38ce63f61c5be0f1a1906d4d37b8725', 'lilac4@camerinohub.edu.ph', 'Jack', 'White', 'S', NULL, 'Grade 7', 'active', 0, NULL, 0, '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(22, 'student_lilac5', 'b38ce63f61c5be0f1a1906d4d37b8725', 'lilac5@camerinohub.edu.ph', 'Evelyn', 'Lopez', 'T', NULL, 'Grade 7', 'active', 0, NULL, 0, '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(23, 'student_samp1', 'b38ce63f61c5be0f1a1906d4d37b8725', 'samp1@camerinohub.edu.ph', 'William', 'Lee', 'U', NULL, 'Grade 7', 'active', 0, NULL, 0, '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(24, 'student_samp2', 'b38ce63f61c5be0f1a1906d4d37b8725', 'samp2@camerinohub.edu.ph', 'Victoria', 'Gonzalez', 'V', NULL, 'Grade 7', 'active', 0, NULL, 0, '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(25, 'student_samp3', 'b38ce63f61c5be0f1a1906d4d37b8725', 'samp3@camerinohub.edu.ph', 'Benjamin', 'Wilson', 'W', NULL, 'Grade 7', 'active', 0, NULL, 0, '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(26, 'student_samp4', 'b38ce63f61c5be0f1a1906d4d37b8725', 'samp4@camerinohub.edu.ph', 'Scarlett', 'Anderson', 'X', NULL, 'Grade 7', 'active', 0, NULL, 0, '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(27, 'student_samp5', 'b38ce63f61c5be0f1a1906d4d37b8725', 'samp5@camerinohub.edu.ph', 'Daniel', 'Taylor', 'Y', NULL, 'Grade 7', 'active', 0, NULL, 0, '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(28, 'student_sun1', 'b38ce63f61c5be0f1a1906d4d37b8725', 'sun1@camerinohub.edu.ph', 'Luna', 'Perez', 'Z', NULL, 'Grade 7', 'active', 0, NULL, 0, '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(29, 'student_sun2', 'b38ce63f61c5be0f1a1906d4d37b8725', 'sun2@camerinohub.edu.ph', 'David', 'Sanchez', 'AA', NULL, 'Grade 7', 'active', 0, NULL, 0, '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(30, 'student_sun3', 'b38ce63f61c5be0f1a1906d4d37b8725', 'sun3@camerinohub.edu.ph', 'Grace', 'Rivera', 'BB', NULL, 'Grade 7', 'active', 0, NULL, 0, '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(31, 'student_sun4', 'b38ce63f61c5be0f1a1906d4d37b8725', 'sun4@camerinohub.edu.ph', 'Joseph', 'Torres', 'CC', NULL, 'Grade 7', 'active', 0, NULL, 0, '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(32, 'student_sun5', 'b38ce63f61c5be0f1a1906d4d37b8725', 'sun5@camerinohub.edu.ph', 'Chloe', 'Flores', 'DD', NULL, 'Grade 7', 'active', 0, NULL, 0, '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(33, 'student_tulip1', 'b38ce63f61c5be0f1a1906d4d37b8725', 'tulip1@camerinohub.edu.ph', 'Andrew', 'Cruz', 'EE', NULL, 'Grade 7', 'active', 0, NULL, 0, '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(34, 'student_tulip2', 'b38ce63f61c5be0f1a1906d4d37b8725', 'tulip2@camerinohub.edu.ph', 'Zoe', 'Santos', 'FF', NULL, 'Grade 7', 'active', 0, NULL, 0, '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(35, 'student_tulip3', 'b38ce63f61c5be0f1a1906d4d37b8725', 'tulip3@camerinohub.edu.ph', 'Christopher', 'Reyes', 'GG', NULL, 'Grade 7', 'active', 0, NULL, 0, '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(36, 'student_tulip4', 'b38ce63f61c5be0f1a1906d4d37b8725', 'tulip4@camerinohub.edu.ph', 'Lily', 'Gomez', 'HH', NULL, 'Grade 7', 'active', 0, NULL, 0, '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(37, 'student_tulip5', 'b38ce63f61c5be0f1a1906d4d37b8725', 'tulip5@camerinohub.edu.ph', 'Matthew', 'Diaz', 'II', NULL, 'Grade 7', 'active', 0, NULL, 0, '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(38, 'student_bsit1', 'b38ce63f61c5be0f1a1906d4d37b8725', 'bsit1@camerinohub.edu.ph', 'Sarah', 'Castro', 'JJ', NULL, 'BSIT-4A', 'active', 0, NULL, 0, '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(39, 'student_bsit2', 'b38ce63f61c5be0f1a1906d4d37b8725', 'bsit2@camerinohub.edu.ph', 'Ryan', 'Mendoza', 'KK', NULL, 'BSIT-4A', 'active', 0, NULL, 0, '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(40, 'student_bsit3', 'b38ce63f61c5be0f1a1906d4d37b8725', 'bsit3@camerinohub.edu.ph', 'Hannah', 'Valdez', 'LL', NULL, 'BSIT-4A', 'active', 0, NULL, 0, '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(41, 'student_bsit4', 'b38ce63f61c5be0f1a1906d4d37b8725', 'bsit4@camerinohub.edu.ph', 'Nathan', 'Ramos', 'MM', NULL, 'BSIT-4A', 'active', 0, NULL, 0, '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(42, 'student_bsit5', 'b38ce63f61c5be0f1a1906d4d37b8725', 'bsit5@camerinohub.edu.ph', 'Ella', 'Aquino', 'NN', NULL, 'BSIT-4A', 'active', 0, NULL, 0, '2024-11-14 13:10:56', '2024-11-14 13:10:56');

-- --------------------------------------------------------

--
-- Table structure for table `student_activity_submissions`
--

CREATE TABLE `student_activity_submissions` (
  `submission_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `activity_id` int(11) NOT NULL,
  `points` int(11) DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'submitted'
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
(1, 1, '::1', 'failed', '2024-11-06 16:00:46'),
(2, 1, '::1', 'failed', '2024-11-06 16:00:51'),
(3, 1, '::1', 'failed', '2024-11-06 16:01:42'),
(4, 1, '::1', 'success', '2024-11-06 16:02:58'),
(5, 1, '::1', 'success', '2024-11-06 16:38:25'),
(6, 1, '::1', 'success', '2024-11-07 03:28:33'),
(7, 1, '::1', 'success', '2024-11-07 07:38:20'),
(8, 1, '::1', 'success', '2024-11-07 08:01:29'),
(9, 1, '::1', 'success', '2024-11-07 10:57:01'),
(10, 1, '::1', 'success', '2024-11-07 11:17:41'),
(11, 1, '::1', 'success', '2024-11-07 13:04:04'),
(12, 1, '::1', 'success', '2024-11-07 15:16:31'),
(13, 1, '::1', 'success', '2024-11-07 18:39:17'),
(14, 1, '::1', 'success', '2024-11-11 15:49:53'),
(15, 1, '::1', 'failed', '2024-11-14 06:48:26'),
(16, 1, '::1', 'success', '2024-11-14 06:48:32'),
(17, 1, '::1', 'success', '2024-11-14 06:58:10'),
(18, 1, '::1', 'success', '2024-11-14 07:32:33'),
(19, 1, '::1', 'success', '2024-11-14 07:42:14'),
(20, 1, '::1', 'success', '2024-11-14 08:24:57'),
(21, 1, '::1', 'success', '2024-11-14 08:41:57'),
(22, 1, '::1', 'success', '2024-11-14 11:04:02'),
(23, 1, '::1', 'failed', '2024-11-14 13:29:50'),
(24, 1, '::1', 'success', '2024-11-14 13:30:16'),
(25, 1, '::1', 'success', '2024-11-14 13:30:39'),
(26, 1, '::1', 'success', '2024-11-14 14:17:01'),
(27, 1, '::1', 'success', '2024-11-15 00:12:36'),
(28, 1, '::1', 'success', '2024-11-15 01:17:57'),
(29, 1, '::1', 'success', '2024-11-15 02:07:35'),
(30, 1, '::1', 'success', '2024-11-15 02:51:39'),
(31, 1, '::1', 'success', '2024-11-15 03:39:37'),
(32, 1, '::1', 'failed', '2024-11-15 03:58:06'),
(33, 1, '::1', 'failed', '2024-11-15 03:58:10'),
(34, 1, '::1', 'success', '2024-11-15 03:58:16');

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
  `status` enum('active','transferred','graduated') DEFAULT 'active',
  `enrolled_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_sections`
--

INSERT INTO `student_sections` (`id`, `student_id`, `section_id`, `academic_year_id`, `school_year`, `status`, `enrolled_at`, `created_at`) VALUES
(1, 2, 1, 1, '2023-2024', 'active', '2024-11-07 04:50:43', '2024-11-07 08:11:33'),
(2, 1, 1, 1, '2023-2024', 'active', '2024-11-07 08:11:42', '2024-11-07 08:11:42'),
(3, 3, 9, 1, '2024-2025', 'active', '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(4, 4, 9, 1, '2024-2025', 'active', '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(5, 5, 9, 1, '2024-2025', 'active', '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(6, 6, 9, 1, '2024-2025', 'active', '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(7, 7, 9, 1, '2024-2025', 'active', '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(8, 38, 1, 1, '2024-2025', 'active', '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(9, 39, 1, 1, '2024-2025', 'active', '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(10, 40, 1, 1, '2024-2025', 'active', '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(11, 41, 1, 1, '2024-2025', 'active', '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(12, 42, 1, 1, '2024-2025', 'active', '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(13, 8, 8, 1, '2024-2025', 'active', '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(14, 9, 8, 1, '2024-2025', 'active', '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(15, 10, 8, 1, '2024-2025', 'active', '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(16, 11, 8, 1, '2024-2025', 'active', '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(17, 12, 8, 1, '2024-2025', 'active', '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(18, 13, 11, 1, '2024-2025', 'active', '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(19, 14, 11, 1, '2024-2025', 'active', '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(20, 15, 11, 1, '2024-2025', 'active', '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(21, 16, 11, 1, '2024-2025', 'active', '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(22, 17, 11, 1, '2024-2025', 'active', '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(23, 18, 10, 1, '2024-2025', 'active', '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(24, 19, 10, 1, '2024-2025', 'active', '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(25, 20, 10, 1, '2024-2025', 'active', '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(26, 21, 10, 1, '2024-2025', 'active', '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(27, 22, 10, 1, '2024-2025', 'active', '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(28, 23, 12, 1, '2024-2025', 'active', '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(29, 24, 12, 1, '2024-2025', 'active', '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(30, 25, 12, 1, '2024-2025', 'active', '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(31, 26, 12, 1, '2024-2025', 'active', '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(32, 27, 12, 1, '2024-2025', 'active', '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(33, 28, 13, 1, '2024-2025', 'active', '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(34, 29, 13, 1, '2024-2025', 'active', '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(35, 30, 13, 1, '2024-2025', 'active', '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(36, 31, 13, 1, '2024-2025', 'active', '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(37, 32, 13, 1, '2024-2025', 'active', '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(38, 33, 14, 1, '2024-2025', 'active', '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(39, 34, 14, 1, '2024-2025', 'active', '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(40, 35, 14, 1, '2024-2025', 'active', '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(41, 36, 14, 1, '2024-2025', 'active', '2024-11-14 13:10:56', '2024-11-14 13:10:56'),
(42, 37, 14, 1, '2024-2025', 'active', '2024-11-14 13:10:56', '2024-11-14 13:10:56');

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
(1, 'ITEC', 'Code', 'Code', 'Core', 'Help codee', '2024-11-06 18:11:35', 'active', '7,8,9,10', '2024-11-14 12:39:32'),
(2, 'TEST', 'TEST', 'TEST', 'Core', '1', '2024-11-06 18:16:53', 'inactive', '7,8,9,10', '2024-11-14 10:35:46'),
(3, 'VALED', 'Values Education', 'Values Education', 'Core', 'Values Education for Grades 7-10', '2024-11-14 09:31:49', 'active', '7,8,9,10', '2024-11-14 10:32:45'),
(4, 'TLE', 'Technology and Livelihood Education', 'TLE', 'Core', 'Technology and Livelihood Education for Grades 7-10', '2024-11-14 09:31:49', 'active', '7,8,9,10', '2024-11-14 09:42:32'),
(5, 'AP', 'Araling Panlipunan', 'Araling Panlipunan', 'Major', 'Araling Panlipunan for Grades 7-10', '2024-11-14 09:31:49', 'active', '7,8,9,10', '2024-11-14 12:36:55'),
(6, 'ENG', 'English', 'English', 'Core', 'English for Grades 7-10', '2024-11-14 09:31:49', 'active', '7,8,9,10', '2024-11-14 09:42:32'),
(7, 'MATH', 'Mathematics', 'Mathematics', 'Core', 'Mathematics for Grades 7-10', '2024-11-14 09:31:49', 'active', '7,8,9,10', '2024-11-14 09:42:32'),
(8, 'FIL', 'Filipino', 'Filipino', 'Core', 'Filipino for Grades 7-10', '2024-11-14 09:31:49', 'active', '7,8,9,10', '2024-11-14 09:42:32'),
(9, 'SCI', 'Science', 'Science', 'Core', 'Science for Grades 7-10', '2024-11-14 09:31:49', 'active', '7,8,9,10', '2024-11-14 09:42:32'),
(10, 'MAPEH', 'Music, Arts, Physical Education, and Health', 'MAPEH', 'Core', 'MAPEH for Grades 7-10', '2024-11-14 09:31:49', 'active', '7,8,9,10', '2024-11-14 09:42:32'),
(20, 'TESTsdasdasd', 'asdasd', '', 'Core', 'sd', '2024-11-14 11:00:42', 'inactive', 'All', '2024-11-14 11:01:00');

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
(1, 20, '8', '2024-11-14 11:00:42');

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

-- --------------------------------------------------------

--
-- Table structure for table `teacher`
--

CREATE TABLE `teacher` (
  `teacher_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `middlename` varchar(100) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `login_attempts` int(11) DEFAULT 0,
  `lockout_time` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teacher`
--

INSERT INTO `teacher` (`teacher_id`, `username`, `password`, `email`, `firstname`, `lastname`, `middlename`, `department`, `status`, `login_attempts`, `lockout_time`, `created_at`, `updated_at`) VALUES
(1, 'chaw', '96ac0342a3ccf9553e3d4c9da9b821b0', 'teacher1@camerinohub.edu.ph', 'John', 'Doe', NULL, 'Computer Studies', 'active', 0, NULL, '2024-11-06 15:41:01', '2024-11-07 11:24:12'),
(2, 'king1', '$2y$10$5RQ4HH3GTB1vBHRyrvGVW.3737SNgWkOR/EOC8AN8XlnAfGgqYtXu', 'kingpacifico009@gmail.com', 'Christopher', 'pacifico', 'Ilagan', 'Computer', 'active', 0, NULL, '2024-11-14 12:48:50', '2024-11-14 13:29:40'),
(3, 'mapeh_teacher', '96ac0342a3ccf9553e3d4c9da9b821b0', 'mapeh@camerinohub.edu.ph', 'Maria', 'Santos', NULL, 'MAPEH', 'active', 0, NULL, '2024-11-14 13:16:38', '2024-11-14 13:16:38'),
(4, 'eng_teacher', '96ac0342a3ccf9553e3d4c9da9b821b0', 'english@camerinohub.edu.ph', 'John', 'Smith', NULL, 'English', 'active', 0, NULL, '2024-11-14 13:16:38', '2024-11-14 13:16:38'),
(5, 'math_teacher', '96ac0342a3ccf9553e3d4c9da9b821b0', 'math@camerinohub.edu.ph', 'Robert', 'Johnson', NULL, 'Mathematics', 'active', 0, NULL, '2024-11-14 13:16:38', '2024-11-14 13:16:38');

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
(1, 1, '::1', 'success', '2024-11-06 17:00:27'),
(2, 1, '::1', 'success', '2024-11-06 19:32:12'),
(3, 1, '::1', 'success', '2024-11-07 03:45:00'),
(4, 1, '::1', 'success', '2024-11-07 07:52:09'),
(5, 1, '::1', 'success', '2024-11-07 11:10:56'),
(6, 1, '::1', 'success', '2024-11-07 11:24:12'),
(7, 1, '::1', 'success', '2024-11-07 13:20:33'),
(8, 1, '::1', 'success', '2024-11-07 17:13:18'),
(9, 1, '::1', 'success', '2024-11-07 17:24:27'),
(10, 1, '::1', 'success', '2024-11-07 17:24:38'),
(11, 1, '::1', 'success', '2024-11-07 17:25:14'),
(12, 1, '::1', 'success', '2024-11-07 19:00:30'),
(13, 1, '::1', 'success', '2024-11-07 19:43:50'),
(14, 1, '::1', 'success', '2024-11-07 19:46:15'),
(15, 1, '::1', 'success', '2024-11-07 19:47:01'),
(16, 1, '::1', 'success', '2024-11-12 08:29:26'),
(17, 1, '::1', 'success', '2024-11-14 05:57:55'),
(18, 1, '::1', 'success', '2024-11-14 05:58:14'),
(19, 1, '::1', 'success', '2024-11-14 05:58:19'),
(20, 1, '::1', 'success', '2024-11-14 06:05:44'),
(21, 1, '::1', 'success', '2024-11-14 06:39:58'),
(22, 1, '::1', 'success', '2024-11-14 06:41:07'),
(23, 1, '::1', 'success', '2024-11-14 06:42:08'),
(24, 1, '::1', 'success', '2024-11-14 06:57:57'),
(25, 1, '::1', 'success', '2024-11-14 07:04:53'),
(26, 1, '::1', 'success', '2024-11-14 07:35:43'),
(27, 1, '::1', 'success', '2024-11-14 07:52:29'),
(28, 1, '::1', 'success', '2024-11-14 07:52:36'),
(29, 1, '::1', 'success', '2024-11-14 08:08:48'),
(30, 1, '::1', 'success', '2024-11-14 13:52:46'),
(31, 1, '::1', 'success', '2024-11-14 14:40:41'),
(32, 1, '::1', 'success', '2024-11-14 15:10:36'),
(33, 1, '::1', 'success', '2024-11-14 15:14:29'),
(34, 1, '::1', 'success', '2024-11-15 01:40:39'),
(35, 1, '::1', 'success', '2024-11-15 03:54:52');

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
  ADD KEY `student_id` (`student_id`),
  ADD KEY `quiz_id` (`quiz_id`);

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
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `student_activity_submissions`
--
ALTER TABLE `student_activity_submissions`
  ADD PRIMARY KEY (`submission_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `idx_activity_submitted` (`activity_id`,`submitted_at`),
  ADD KEY `idx_activity_submissions` (`activity_id`,`submitted_at`);

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
  ADD UNIQUE KEY `email` (`email`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `active_sessions`
--
ALTER TABLE `active_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `activity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `activity_files`
--
ALTER TABLE `activity_files`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admin_login_logs`
--
ALTER TABLE `admin_login_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `announcement_comments`
--
ALTER TABLE `announcement_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `assignment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `attendance_logs`
--
ALTER TABLE `attendance_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `class_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contact_information`
--
ALTER TABLE `contact_information`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `date_ranges`
--
ALTER TABLE `date_ranges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `section_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `security_violations`
--
ALTER TABLE `security_violations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `site_map_content`
--
ALTER TABLE `site_map_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `student_activity_submissions`
--
ALTER TABLE `student_activity_submissions`
  MODIFY `submission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `student_grades`
--
ALTER TABLE `student_grades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_login_logs`
--
ALTER TABLE `student_login_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `student_sections`
--
ALTER TABLE `student_sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `subject_grade_levels`
--
ALTER TABLE `subject_grade_levels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `submission_files`
--
ALTER TABLE `submission_files`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `teacher`
--
ALTER TABLE `teacher`
  MODIFY `teacher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `teacher_login_logs`
--
ALTER TABLE `teacher_login_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `active_sessions`
--
ALTER TABLE `active_sessions`
  ADD CONSTRAINT `active_sessions_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE;

--
-- Constraints for table `activities`
--
ALTER TABLE `activities`
  ADD CONSTRAINT `activities_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`teacher_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `activities_ibfk_3` FOREIGN KEY (`section_subject_id`) REFERENCES `section_subjects` (`id`);

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
  ADD CONSTRAINT `announcement_comments_ibfk_1` FOREIGN KEY (`announcement_id`) REFERENCES `announcements` (`id`),
  ADD CONSTRAINT `announcement_comments_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`);

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
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE,
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
-- Constraints for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  ADD CONSTRAINT `remember_tokens_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `security_violations_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `security_violations_ibfk_2` FOREIGN KEY (`quiz_id`) REFERENCES `activities` (`activity_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_activity_submissions`
--
ALTER TABLE `student_activity_submissions`
  ADD CONSTRAINT `student_activity_submissions_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_activity_submissions_ibfk_2` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`activity_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_login_logs`
--
ALTER TABLE `student_login_logs`
  ADD CONSTRAINT `student_login_logs_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`);

--
-- Constraints for table `student_sections`
--
ALTER TABLE `student_sections`
  ADD CONSTRAINT `student_sections_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE,
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
-- Constraints for table `teacher_login_logs`
--
ALTER TABLE `teacher_login_logs`
  ADD CONSTRAINT `teacher_login_logs_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`teacher_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
