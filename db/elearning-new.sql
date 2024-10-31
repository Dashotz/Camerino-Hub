-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 31, 2024 at 01:28 AM
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
(4, 'BSIS -3A', 'Fundamentals of Business Management', '1', 13),
(7, 'BSIS -3A', 'DBMS', '2', 13),
(9, 'BSIS 3B', 'Fundamentals of Business Management', '2', 0);

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
(1, '17', '::1', 'failed', '2024-10-30 23:58:56'),
(2, '17', '::1', 'failed', '2024-10-30 23:59:08'),
(3, '17', '::1', 'success', '2024-10-30 23:59:17'),
(4, '17', '::1', 'success', '2024-10-31 00:08:07'),
(5, '17', '::1', 'failed', '2024-10-31 00:08:29'),
(6, '17', '::1', 'failed', '2024-10-31 00:08:37'),
(7, '17', '::1', 'success', '2024-10-31 00:08:45'),
(8, '17', '::1', 'failed', '2024-10-31 00:18:04');

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

--
-- Dumping data for table `remember_tokens`
--

INSERT INTO `remember_tokens` (`id`, `student_id`, `token`, `expiry`, `created_at`) VALUES
(1, '17', '4fa2224b38047ef0a8e46ce4ad0fab7bff3a19c612f477b37033bf74d8ee84bd', '2024-11-30 01:08:07', '2024-10-31 00:08:07');

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
  `password` varchar(100) NOT NULL,
  `location` varchar(100) NOT NULL,
  `login_attempts` int(11) DEFAULT 0,
  `lockout_until` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`student_id`, `firstname`, `lastname`, `middle_name`, `cys`, `subject_id`, `teacher_id`, `username`, `password`, `location`, `login_attempts`, `lockout_until`) VALUES
(17, 'Christian', 'Sausa', 'Ambot', '', '', 0, 'Christian', 'Sausa', 'uploads/letra-n-md.png', 1, NULL),
(15, 'Brian Paul', 'Sablan', 'jkl', '', '', 0, 'xiaboh', 'xiaboh', 'uploads/images (3).jpg', 0, NULL),
(16, 'Sherwin', 'Laylon', 'b', '', '', 0, 'Sherwin', 'agi', 'uploads/443px-Rasmus_Lerdorf_cropped.jpg', 0, NULL),
(13, 'john kevin', 'lorayna', 'amos', 'BSIS -3A', 'DBMS', 2, '100175', 'teph', 'uploads/735067_452259954828103_1940229060_n.jpg', 0, NULL);

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
(5, 'IS 123', 'Fundamentals of Business Management', 'Major'),
(7, 'IS 301', 'DBMS', 'Major');

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
  `lockout_time` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `teacher`
--

INSERT INTO `teacher` (`teacher_id`, `username`, `password`, `firstname`, `lastname`, `middlename`, `department`, `student_id`, `location`, `login_attempts`, `lockout_time`) VALUES
(1, 'jk', 'jk', 'Jomar', 'Pabuaya', 'smith', 'College of Industrial Technology', 0, 'uploads/images (3).jpg', 0, NULL),
(2, 'chaw', 'pan', 'Charito', 'Puray', 'dela tore', 'College of Industrial Technology', 0, 'uploads/images (3).jpg', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `teacher_student`
--

CREATE TABLE `teacher_student` (
  `teacher_student_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `teacher_student`
--

INSERT INTO `teacher_student` (`teacher_student_id`, `teacher_id`, `student_id`) VALUES
(8, 2, 17),
(7, 2, 16);

-- --------------------------------------------------------

--
-- Table structure for table `teacher_suject`
--

CREATE TABLE `teacher_suject` (
  `teacher_subject_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `teacher_suject`
--

INSERT INTO `teacher_suject` (`teacher_subject_id`, `subject_id`, `teacher_id`) VALUES
(12, 5, 2),
(11, 7, 2),
(6, 6, 1);

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
-- Indexes for table `class`
--
ALTER TABLE `class`
  ADD PRIMARY KEY (`class_id`);

--
-- Indexes for table `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`course_id`);

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
  ADD PRIMARY KEY (`teacher_id`);

--
-- Indexes for table `teacher_student`
--
ALTER TABLE `teacher_student`
  ADD PRIMARY KEY (`teacher_student_id`);

--
-- Indexes for table `teacher_suject`
--
ALTER TABLE `teacher_suject`
  ADD PRIMARY KEY (`teacher_subject_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `class`
--
ALTER TABLE `class`
  MODIFY `class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `course`
--
ALTER TABLE `course`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `subject`
--
ALTER TABLE `subject`
  MODIFY `subject_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
-- AUTO_INCREMENT for table `teacher_student`
--
ALTER TABLE `teacher_student`
  MODIFY `teacher_student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `teacher_suject`
--
ALTER TABLE `teacher_suject`
  MODIFY `teacher_subject_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
