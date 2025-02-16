-- CamerinoHub Database Backup
-- Generated: 2024-12-12 14:07:29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- Table structure for table `about_us_content`
DROP TABLE IF EXISTS `about_us_content`;
CREATE TABLE `about_us_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `type` varchar(50) DEFAULT 'general',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



-- Table structure for table `academic_years`
DROP TABLE IF EXISTS `academic_years`;
CREATE TABLE `academic_years` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_year` varchar(9) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `archived_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `academic_years`
INSERT INTO `academic_years` VALUES("1","2024-2025","2024-06-01","2025-03-31","active","2024-11-07 03:28:47","0",NULL);


-- Table structure for table `active_sessions`
DROP TABLE IF EXISTS `active_sessions`;
CREATE TABLE `active_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `last_activity` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `student_id` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



-- Table structure for table `activities`
DROP TABLE IF EXISTS `activities`;
CREATE TABLE `activities` (
  `activity_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `completion_rate` decimal(5,2) DEFAULT 0.00,
  PRIMARY KEY (`activity_id`),
  KEY `idx_teacher_type` (`teacher_id`,`type`),
  KEY `idx_teacher_recent` (`teacher_id`,`created_at`),
  KEY `idx_teacher_activities` (`teacher_id`,`created_at`),
  KEY `idx_section_subject` (`section_subject_id`),
  CONSTRAINT `fk_activities_section_subject` FOREIGN KEY (`section_subject_id`) REFERENCES `section_subjects` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=158 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `activities`
INSERT INTO `activities` VALUES("92","9","46","qweqwe","asdasdasd",NULL,"assignment","100",NULL,NULL,"0","0","1","0","2024-12-02 03:39:00","2024-11-25 03:39:29","2024-12-09 11:17:41","archived","0.00");
INSERT INTO `activities` VALUES("93","123","53","asd","asd",NULL,"activity","100",NULL,NULL,"0","0","1","0","2024-12-08 05:51:00","2024-12-01 12:51:51","2024-12-01 12:51:51","active","0.00");
INSERT INTO `activities` VALUES("94","9","56","ChawCHawasdasd","asd",NULL,"activity","0",NULL,NULL,"0","0","1","0","2024-12-08 05:59:00","2024-12-01 12:59:47","2024-12-09 11:17:38","archived","0.00");
INSERT INTO `activities` VALUES("95","124","54","asd","asd",NULL,"activity","100",NULL,NULL,"0","0","1","0","2024-12-08 06:41:00","2024-12-01 13:41:19","2024-12-01 13:41:19","active","0.00");
INSERT INTO `activities` VALUES("96","124","54","asd","asd",NULL,"quiz","100","https://docs.google.com/forms/d/e/1FAIpQLSc3FbCr-qgYgxCClauhotJifydnFffW2sEu2aHrVNjHdmXzrw/viewform?usp=sf_link&embedded=true","60","1","1","1","0","2024-12-01 14:42:00","2024-12-01 13:42:29","2024-12-01 13:42:29","active","0.00");
INSERT INTO `activities` VALUES("113","9","56","sdas","dasda",NULL,"quiz","3",NULL,"60","0","0","1","0","2024-12-05 00:24:00","2024-12-04 00:24:56","2024-12-09 11:17:37","archived","0.00");
INSERT INTO `activities` VALUES("114","9","56","Q","Q",NULL,"quiz","3",NULL,"60","0","0","1","0","2024-12-05 01:37:00","2024-12-04 01:39:32","2024-12-09 11:17:35","archived","0.00");
INSERT INTO `activities` VALUES("115","9","56","q","q",NULL,"quiz","3",NULL,"60","0","0","1","0","2024-12-05 01:39:00","2024-12-04 01:39:57","2024-12-09 11:17:33","archived","0.00");
INSERT INTO `activities` VALUES("116","9","56","a","a",NULL,"quiz","3",NULL,"60","0","0","1","0","2024-12-05 01:41:00","2024-12-04 01:42:03","2024-12-09 11:17:31","archived","0.00");
INSERT INTO `activities` VALUES("124","9","56","a","a",NULL,"quiz","3",NULL,"60","0","0","1","0","2024-12-05 01:50:00","2024-12-04 01:51:08","2024-12-09 11:17:29","archived","0.00");
INSERT INTO `activities` VALUES("125","9","56","TTest","test",NULL,"quiz","3",NULL,"60","0","0","1","0","2024-12-07 03:39:00","2024-12-06 03:39:52","2024-12-09 11:17:27","archived","0.00");
INSERT INTO `activities` VALUES("128","9","56","testtt","testt",NULL,"quiz","3",NULL,"60","0","0","1","0","2024-12-07 05:06:00","2024-12-06 05:06:12","2024-12-06 06:00:35","archived","0.00");
INSERT INTO `activities` VALUES("131","9","56","test","test",NULL,"quiz","3",NULL,"60","0","0","1","0","2024-12-07 06:03:00","2024-12-06 06:03:12","2024-12-06 15:58:25","archived","0.00");
INSERT INTO `activities` VALUES("135","9","56","test","test",NULL,"quiz","3",NULL,"60","0","0","1","0","2024-12-07 06:07:00","2024-12-06 06:07:43","2024-12-06 15:58:23","archived","0.00");
INSERT INTO `activities` VALUES("136","9","56","testtasd","testasd",NULL,"quiz","11",NULL,"60","0","0","1","0","2024-12-07 07:24:00","2024-12-06 07:24:57","2024-12-06 15:58:21","archived","0.00");
INSERT INTO `activities` VALUES("137","9","56","testasdasd","asdads",NULL,"quiz","2",NULL,"60","0","0","1","0","2024-12-07 15:20:00","2024-12-06 15:21:04","2024-12-09 11:17:25","archived","0.00");
INSERT INTO `activities` VALUES("138","9","56","ASDASD","ASDASD",NULL,"quiz","3",NULL,"60","0","0","1","0","2024-12-07 15:58:00","2024-12-06 15:58:55","2024-12-06 17:26:58","archived","0.00");
INSERT INTO `activities` VALUES("139","9","56","SHORT","SHORT",NULL,"quiz","3",NULL,"60","0","0","1","0","2024-12-07 17:27:00","2024-12-06 17:27:30","2024-12-09 11:17:23","archived","0.00");
INSERT INTO `activities` VALUES("140","9","56","NEW","NEW",NULL,"quiz","3",NULL,"60","0","0","1","0","2024-12-07 17:47:00","2024-12-06 17:47:47","2024-12-09 11:17:21","archived","0.00");
INSERT INTO `activities` VALUES("141","9","56","SHOSSSS","SSADASD",NULL,"quiz","3",NULL,"60","0","0","1","0","2024-12-07 18:41:00","2024-12-06 18:41:43","2024-12-09 11:17:19","archived","0.00");
INSERT INTO `activities` VALUES("142","9","56","XZC","ZXC",NULL,"quiz","5",NULL,"60","0","0","1","0","2024-12-08 00:09:00","2024-12-07 00:09:50","2024-12-09 11:17:17","archived","0.00");
INSERT INTO `activities` VALUES("143","9","56","FRRRR","FRRR",NULL,"assignment","100",NULL,NULL,"0","0","1","0","2024-12-14 01:02:00","2024-12-07 01:02:52","2024-12-09 11:17:15","archived","0.00");
INSERT INTO `activities` VALUES("145","9","56","asdasd","asdad",NULL,"assignment","100",NULL,NULL,"0","0","1","0","2024-12-14 01:16:00","2024-12-07 01:16:06","2024-12-09 11:17:14","archived","0.00");
INSERT INTO `activities` VALUES("146","9","56","web","web",NULL,"quiz","7",NULL,"60","0","0","1","0","2024-12-10 01:37:00","2024-12-09 01:37:51","2024-12-09 11:17:11","archived","0.00");
INSERT INTO `activities` VALUES("147","9","56","Test TEST","Test",NULL,"quiz","3",NULL,"1","0","0","1","0","2024-12-10 01:44:00","2024-12-09 01:44:42","2024-12-09 11:17:10","archived","0.00");
INSERT INTO `activities` VALUES("148","9","56","points","points",NULL,"quiz","3",NULL,"60","0","0","1","0","2024-12-10 02:03:00","2024-12-09 02:03:38","2024-12-09 11:17:08","archived","0.00");
INSERT INTO `activities` VALUES("149","9","56","pacifoc","pacifoc",NULL,"quiz","3",NULL,"60","0","0","1","0","2024-12-10 02:13:00","2024-12-09 02:13:09","2024-12-09 11:17:06","archived","0.00");
INSERT INTO `activities` VALUES("150","9","56","Hahaha","Gahaga",NULL,"quiz","3",NULL,"60","0","0","1","0","2024-12-10 11:14:00","2024-12-09 11:15:10","2024-12-09 11:17:03","archived","0.00");
INSERT INTO `activities` VALUES("151","9","56","quiz 1","Description instruction \nNot case sensitive\netc \nEtc",NULL,"quiz","4",NULL,"10","0","0","1","0","2024-12-10 12:56:00","2024-12-09 12:56:56","2024-12-09 12:56:56","active","0.00");
INSERT INTO `activities` VALUES("152","9","56","Activity 2 MIDTERM LAB","Midterm Lab / ACTIVITY 2",NULL,"activity","0",NULL,NULL,"0","0","1","0","2024-12-20 23:59:00","2024-12-09 14:04:56","2024-12-09 15:08:59","active","0.00");
INSERT INTO `activities` VALUES("154","9","56","QUIZ NO 1","MULTIPLE CHOICE",NULL,"quiz","1",NULL,"20","1","0","1","0","2024-12-16 23:00:00","2024-12-09 16:13:59","2024-12-09 16:13:59","active","0.00");
INSERT INTO `activities` VALUES("155","9","56","Activity: Build a Simple Web Page","ACTIVTIY 1&2",NULL,"activity","0",NULL,NULL,"0","0","1","0","2024-12-16 23:59:00","2024-12-09 16:23:39","2024-12-09 16:26:09","active","0.00");
INSERT INTO `activities` VALUES("156","127","58","Essay","Make an essay about life",NULL,"assignment","100",NULL,NULL,"0","0","1","0","2024-12-18 23:44:00","2024-12-11 23:46:57","2024-12-11 23:46:57","active","0.00");
INSERT INTO `activities` VALUES("157","9","56","quiz 77","intruction",NULL,"quiz","3",NULL,"60","0","0","1","0","2024-12-13 14:55:00","2024-12-12 06:55:11","2024-12-12 06:55:11","active","0.00");


-- Table structure for table `activity_files`
DROP TABLE IF EXISTS `activity_files`;
CREATE TABLE `activity_files` (
  `file_id` int(11) NOT NULL AUTO_INCREMENT,
  `activity_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(100) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`file_id`),
  KEY `activity_id` (`activity_id`),
  CONSTRAINT `activity_files_ibfk_1` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`activity_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `activity_files`
INSERT INTO `activity_files` VALUES("29","92","Christian Pacificologo (1).docx","uploads/activities/1732502369_6743e361ae95a_6c17f46427645a2e.docx","application/vnd.openxmlformats-officedocument.wordprocessingml.document","139228","2024-11-25 10:39:29");
INSERT INTO `activity_files` VALUES("30","93","Survey Questionnaire.pdf","uploads/activities/1733028711_674beb6722249_b185f17fa6913ffd.pdf","application/pdf","119917","2024-12-01 12:51:51");
INSERT INTO `activity_files` VALUES("31","94","Survey Questionnaire.pdf","uploads/activities/1733029187_674bed43c2443_b202bf591659d994.pdf","application/pdf","119917","2024-12-01 12:59:47");
INSERT INTO `activity_files` VALUES("32","143","DCIT-65A-FINAL-PROJECT.pdf","uploads/activities/1733504572_67532e3c47429_75612306b6bbf9cc.pdf","application/pdf","7298057","2024-12-07 01:02:52");
INSERT INTO `activity_files` VALUES("34","145","DCIT-65A-FINAL-PROJECT.pdf","uploads/activities/1733505366_675331569c2c7_08b031c8017791b2.pdf","application/pdf","7298057","2024-12-07 01:16:06");


-- Table structure for table `admin`
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`admin_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `admin`
INSERT INTO `admin` VALUES("1","admin","21232f297a57a5a743894a0e4a801fc3","admin@camerinohub.edu.ph","System","Administrator","active","2024-11-06 23:41:01");


-- Table structure for table `admin_login_logs`
DROP TABLE IF EXISTS `admin_login_logs`;
CREATE TABLE `admin_login_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `status` enum('success','failed','logout') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`),
  CONSTRAINT `admin_login_logs_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=123 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `admin_login_logs`
INSERT INTO `admin_login_logs` VALUES("81","1","::1","success","2024-12-02 03:08:48");
INSERT INTO `admin_login_logs` VALUES("82","1","::1","success","2024-12-02 04:21:44");
INSERT INTO `admin_login_logs` VALUES("83","1","::1","success","2024-12-02 19:05:35");
INSERT INTO `admin_login_logs` VALUES("84","1","::1","logout","2024-12-02 19:18:09");
INSERT INTO `admin_login_logs` VALUES("85","1","::1","success","2024-12-02 19:18:12");
INSERT INTO `admin_login_logs` VALUES("86","1","::1","success","2024-12-04 01:16:18");
INSERT INTO `admin_login_logs` VALUES("87","1","175.176.36.21","success","2024-12-09 13:15:02");
INSERT INTO `admin_login_logs` VALUES("88","1","::1","success","2024-12-10 19:40:53");
INSERT INTO `admin_login_logs` VALUES("89","1","::1","logout","2024-12-10 19:40:57");
INSERT INTO `admin_login_logs` VALUES("90","1","::1","success","2024-12-10 20:02:57");
INSERT INTO `admin_login_logs` VALUES("91","1","::1","logout","2024-12-10 20:02:59");
INSERT INTO `admin_login_logs` VALUES("92","1","::1","success","2024-12-10 20:04:47");
INSERT INTO `admin_login_logs` VALUES("93","1","::1","logout","2024-12-10 20:04:50");
INSERT INTO `admin_login_logs` VALUES("94","1","::1","success","2024-12-10 20:18:04");
INSERT INTO `admin_login_logs` VALUES("95","1","::1","logout","2024-12-10 20:18:07");
INSERT INTO `admin_login_logs` VALUES("96","1","::1","success","2024-12-10 21:44:21");
INSERT INTO `admin_login_logs` VALUES("97","1","::1","logout","2024-12-10 21:44:26");
INSERT INTO `admin_login_logs` VALUES("98","1","::1","success","2024-12-11 01:44:43");
INSERT INTO `admin_login_logs` VALUES("99","1","180.194.233.34","success","2024-12-11 15:09:58");
INSERT INTO `admin_login_logs` VALUES("100","1","180.194.233.34","logout","2024-12-11 17:29:06");
INSERT INTO `admin_login_logs` VALUES("101","1","180.194.233.34","success","2024-12-11 17:29:13");
INSERT INTO `admin_login_logs` VALUES("102","1","180.194.233.34","logout","2024-12-11 17:29:24");
INSERT INTO `admin_login_logs` VALUES("103","1","180.194.233.34","success","2024-12-11 17:30:41");
INSERT INTO `admin_login_logs` VALUES("104","1","180.194.233.34","logout","2024-12-11 17:30:55");
INSERT INTO `admin_login_logs` VALUES("105","1","180.194.233.34","success","2024-12-11 17:47:52");
INSERT INTO `admin_login_logs` VALUES("106","1","136.158.49.29","success","2024-12-11 23:20:33");
INSERT INTO `admin_login_logs` VALUES("107","1","136.158.49.29","logout","2024-12-11 23:33:28");
INSERT INTO `admin_login_logs` VALUES("108","1","136.158.49.29","success","2024-12-11 23:33:47");
INSERT INTO `admin_login_logs` VALUES("109","1","136.158.49.29","logout","2024-12-11 23:38:13");
INSERT INTO `admin_login_logs` VALUES("110","1","136.158.49.29","success","2024-12-11 23:40:43");
INSERT INTO `admin_login_logs` VALUES("111","1","136.158.49.29","logout","2024-12-11 23:43:50");
INSERT INTO `admin_login_logs` VALUES("112","1","180.194.233.34","success","2024-12-12 01:17:06");
INSERT INTO `admin_login_logs` VALUES("113","1","180.194.233.34","logout","2024-12-12 01:48:55");
INSERT INTO `admin_login_logs` VALUES("114","1","58.69.144.207","success","2024-12-12 06:57:35");
INSERT INTO `admin_login_logs` VALUES("115","1","58.69.144.207","logout","2024-12-12 06:59:28");
INSERT INTO `admin_login_logs` VALUES("116","1","58.69.144.207","success","2024-12-12 07:00:08");
INSERT INTO `admin_login_logs` VALUES("117","1","58.69.144.207","logout","2024-12-12 07:00:55");
INSERT INTO `admin_login_logs` VALUES("118","1","49.144.12.254","success","2024-12-12 10:13:21");
INSERT INTO `admin_login_logs` VALUES("119","1","49.144.12.254","logout","2024-12-12 10:13:35");
INSERT INTO `admin_login_logs` VALUES("120","1","180.194.233.34","success","2024-12-12 14:01:22");
INSERT INTO `admin_login_logs` VALUES("121","1","180.194.233.34","logout","2024-12-12 14:01:37");
INSERT INTO `admin_login_logs` VALUES("122","1","180.194.233.34","success","2024-12-12 14:07:14");


-- Table structure for table `admin_logs`
DROP TABLE IF EXISTS `admin_logs`;
CREATE TABLE `admin_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `action` varchar(50) NOT NULL,
  `affected_table` varchar(50) NOT NULL,
  `affected_id` int(11) NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



-- Table structure for table `announcement_comments`
DROP TABLE IF EXISTS `announcement_comments`;
CREATE TABLE `announcement_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `announcement_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `announcement_id` (`announcement_id`),
  KEY `student_id` (`student_id`),
  CONSTRAINT `announcement_comments_ibfk_1` FOREIGN KEY (`announcement_id`) REFERENCES `announcements` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



-- Table structure for table `announcements`
DROP TABLE IF EXISTS `announcements`;
CREATE TABLE `announcements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `reference_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `section_id` (`section_id`),
  KEY `subject_id` (`subject_id`),
  KEY `idx_teacher_recent` (`teacher_id`,`created_at`),
  KEY `idx_teacher_announcements` (`teacher_id`,`created_at`),
  CONSTRAINT `announcements_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`teacher_id`),
  CONSTRAINT `announcements_ibfk_2` FOREIGN KEY (`section_id`) REFERENCES `sections` (`section_id`),
  CONSTRAINT `announcements_ibfk_3` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `announcements`
INSERT INTO `announcements` VALUES("50","9","20","23","A new quiz has been posted: asd\nDue date: 2024-12-05T20:13\nTotal Points: 1",NULL,"New Quiz: asd",NULL,"active","2024-12-03 20:14:48","quiz",NULL,NULL,"medium","101");
INSERT INTO `announcements` VALUES("51","9","20","23","A new quiz has been posted: test1t\nDue date: 2024-12-04T20:25\nTotal Points: 1",NULL,"New Quiz: test1t",NULL,"active","2024-12-03 20:26:13","quiz",NULL,NULL,"medium","102");
INSERT INTO `announcements` VALUES("52","9","20","23","A new quiz has been posted: Test\nDue date: 2024-12-04T20:44\nTotal Points: 1",NULL,"New Quiz: Test",NULL,"active","2024-12-03 20:44:54","quiz",NULL,NULL,"medium","103");
INSERT INTO `announcements` VALUES("53","9","20","23","A new quiz has been posted: Short answer\nDue date: 2024-12-05T20:48\nTotal Points: 1",NULL,"New Quiz: Short answer",NULL,"active","2024-12-03 20:48:39","quiz",NULL,NULL,"medium","104");
INSERT INTO `announcements` VALUES("54","9","20","23","A new quiz has been posted: Test\nDue date: 2024-12-13T20:59\nTotal Points: 3",NULL,"New Quiz: Test",NULL,"active","2024-12-03 21:00:12","quiz",NULL,NULL,"medium","105");
INSERT INTO `announcements` VALUES("55","9","20","23","A new quiz has been posted: qwe\nDue date: 2024-12-24T21:11\nTotal Points: 1",NULL,"New Quiz: qwe",NULL,"active","2024-12-03 21:11:18","quiz",NULL,NULL,"medium","106");
INSERT INTO `announcements` VALUES("56","9","20","23","A new quiz has been posted: asd\nDue date: 2024-12-04T23:14\nTotal Points: 1",NULL,"New Quiz: asd",NULL,"active","2024-12-03 23:14:53","quiz",NULL,NULL,"medium","107");
INSERT INTO `announcements` VALUES("57","9","20","23","A new quiz has been posted: TITE\nDue date: 2024-12-04T23:17\nTotal Points: 4",NULL,"New Quiz: TITE",NULL,"active","2024-12-03 23:17:40","quiz",NULL,NULL,"medium","108");
INSERT INTO `announcements` VALUES("58","9","20","23","A new quiz has been posted: wqeqw\nDue date: 2024-12-20T23:25\nTotal Points: 6",NULL,"New Quiz: wqeqw",NULL,"active","2024-12-03 23:25:23","quiz",NULL,NULL,"medium","109");
INSERT INTO `announcements` VALUES("59","9","20","23","A new quiz has been posted: test\nDue date: 2024-12-04T23:29\nTotal Points: 1",NULL,"New Quiz: test",NULL,"active","2024-12-03 23:29:09","quiz",NULL,NULL,"medium","110");
INSERT INTO `announcements` VALUES("60","9","20","23","A new quiz has been posted: Test123\nDue date: 2024-12-06T23:31\nTotal Points: 1",NULL,"New Quiz: Test123",NULL,"active","2024-12-03 23:32:01","quiz",NULL,NULL,"medium","111");
INSERT INTO `announcements` VALUES("61","9","20","23","A new quiz has been posted: test123\nDue date: 2024-12-05T00:13\nTotal Points: 3",NULL,"New Quiz: test123",NULL,"active","2024-12-04 00:13:57","quiz",NULL,NULL,"medium","112");
INSERT INTO `announcements` VALUES("62","9","20","23","A new quiz has been posted: sdas\nDue date: 2024-12-05T00:24\nTotal Points: 3",NULL,"New Quiz: sdas",NULL,"active","2024-12-04 00:24:56","quiz",NULL,NULL,"medium","113");
INSERT INTO `announcements` VALUES("63","9","20","23","A new quiz has been posted: Q\nDue date: 2024-12-05T01:37\nTotal Points: 3",NULL,"New Quiz: Q",NULL,"active","2024-12-04 01:39:32","quiz",NULL,NULL,"medium","114");
INSERT INTO `announcements` VALUES("64","9","20","23","A new quiz has been posted: q\nDue date: 2024-12-05T01:39\nTotal Points: 3",NULL,"New Quiz: q",NULL,"active","2024-12-04 01:39:58","quiz",NULL,NULL,"medium","115");
INSERT INTO `announcements` VALUES("65","9","20","23","A new quiz has been posted: a\nDue date: 2024-12-05T01:41\nTotal Points: 3",NULL,"New Quiz: a",NULL,"active","2024-12-04 01:42:03","quiz",NULL,NULL,"medium","116");
INSERT INTO `announcements` VALUES("66","9","20","23","A new quiz has been posted: testtt\nDue date: 2024-12-07T05:06\nTotal Points: 3",NULL,"New Quiz: testtt",NULL,"active","2024-12-06 05:06:12","quiz",NULL,NULL,"medium","128");
INSERT INTO `announcements` VALUES("67","9","20","23","A new quiz has been posted: test\nDue date: 2024-12-07T06:03\nTotal Points: 3",NULL,"New Quiz: test",NULL,"active","2024-12-06 06:03:12","quiz",NULL,NULL,"medium","131");
INSERT INTO `announcements` VALUES("68","9","20","23","A new quiz has been posted: test\nDue date: 2024-12-07T06:07\nTotal Points: 3",NULL,"New Quiz: test",NULL,"active","2024-12-06 06:07:43","quiz",NULL,NULL,"medium","135");
INSERT INTO `announcements` VALUES("69","9","20","23","A new quiz has been posted: testtasd\nDue date: 2024-12-07T07:24\nTotal Points: 3",NULL,"New Quiz: testtasd",NULL,"active","2024-12-06 07:24:57","quiz",NULL,NULL,"medium","136");
INSERT INTO `announcements` VALUES("70","9","20","23","A new quiz has been posted: testasdasd\nDue date: 2024-12-07T15:20\nTotal Points: 3",NULL,"New Quiz: testasdasd",NULL,"active","2024-12-06 15:21:04","quiz",NULL,NULL,"medium","137");
INSERT INTO `announcements` VALUES("71","9","20","23","A new quiz has been posted: ASDASD\nDue date: 2024-12-07T15:58\nTotal Points: 3",NULL,"New Quiz: ASDASD",NULL,"active","2024-12-06 15:58:56","quiz",NULL,NULL,"medium","138");
INSERT INTO `announcements` VALUES("72","9","20","23","A new quiz has been posted: SHORT\nDue date: 2024-12-07T17:27\nTotal Points: 3",NULL,"New Quiz: SHORT",NULL,"active","2024-12-06 17:27:30","quiz",NULL,NULL,"medium","139");
INSERT INTO `announcements` VALUES("73","9","20","23","A new quiz has been posted: NEW\nDue date: 2024-12-07T17:47\nTotal Points: 3",NULL,"New Quiz: NEW",NULL,"active","2024-12-06 17:47:47","quiz",NULL,NULL,"medium","140");
INSERT INTO `announcements` VALUES("74","9","20","23","A new quiz has been posted: SHOSSSS\nDue date: 2024-12-07T18:41\nTotal Points: 3",NULL,"New Quiz: SHOSSSS",NULL,"active","2024-12-06 18:41:43","quiz",NULL,NULL,"medium","141");
INSERT INTO `announcements` VALUES("75","9","20","23","A new quiz has been posted: XZC\nDue date: 2024-12-08T00:09\nTotal Points: 5",NULL,"New Quiz: XZC",NULL,"active","2024-12-07 00:09:51","quiz",NULL,NULL,"medium","142");
INSERT INTO `announcements` VALUES("76","9","20","23","A new quiz has been posted: web\nDue date: 2024-12-10T01:37\nTotal Points: 7",NULL,"New Quiz: web",NULL,"active","2024-12-09 01:37:51","quiz",NULL,NULL,"medium","146");
INSERT INTO `announcements` VALUES("77","9","20","23","A new quiz has been posted: Test TEST\nDue date: 2024-12-10T01:44\nTotal Points: 3",NULL,"New Quiz: Test TEST",NULL,"active","2024-12-09 01:44:42","quiz",NULL,NULL,"medium","147");
INSERT INTO `announcements` VALUES("78","9","20","23","A new quiz has been posted: points\nDue date: 2024-12-10T02:03\nTotal Points: 3",NULL,"New Quiz: points",NULL,"active","2024-12-09 02:03:38","quiz",NULL,NULL,"medium","148");
INSERT INTO `announcements` VALUES("79","9","20","23","A new quiz has been posted: pacifoc\nDue date: 2024-12-10T02:13\nTotal Points: 3",NULL,"New Quiz: pacifoc",NULL,"active","2024-12-09 02:13:09","quiz",NULL,NULL,"medium","149");
INSERT INTO `announcements` VALUES("80","9","20","23","A new quiz has been posted: Hahaha\nDue date: 2024-12-10T11:14\nTotal Points: 3",NULL,"New Quiz: Hahaha",NULL,"active","2024-12-09 11:15:10","quiz",NULL,NULL,"medium","150");
INSERT INTO `announcements` VALUES("81","9","20","23","A new quiz has been posted: quiz 1\nDue date: 2024-12-10T12:56\nTotal Points: 4",NULL,"New Quiz: quiz 1",NULL,"active","2024-12-09 12:56:56","quiz",NULL,NULL,"medium","151");
INSERT INTO `announcements` VALUES("82","9","20","23","A new quiz has been posted: QUIZ NO 1\nDue date: 2024-12-16T23:00\nTotal Points: 1",NULL,"New Quiz: QUIZ NO 1",NULL,"active","2024-12-09 16:13:59","quiz",NULL,NULL,"medium","154");
INSERT INTO `announcements` VALUES("83","9","20","23","A new quiz has been posted: quiz 77\nDue date: 2024-12-13T14:55\nTotal Points: 3",NULL,"New Quiz: quiz 77",NULL,"active","2024-12-12 06:55:11","quiz",NULL,NULL,"medium","157");


-- Table structure for table `archive_academic_years`
DROP TABLE IF EXISTS `archive_academic_years`;
CREATE TABLE `archive_academic_years` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `original_id` int(11) NOT NULL,
  `school_year` varchar(9) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('active','inactive','archived') DEFAULT 'archived',
  `archived_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `archived_by` int(11) NOT NULL,
  `restore_date` timestamp NULL DEFAULT NULL,
  `restored_by` int(11) DEFAULT NULL,
  `archive_notes` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_archived_by` (`archived_by`),
  KEY `fk_restored_by` (`restored_by`),
  CONSTRAINT `fk_archived_by` FOREIGN KEY (`archived_by`) REFERENCES `admin` (`admin_id`),
  CONSTRAINT `fk_restored_by` FOREIGN KEY (`restored_by`) REFERENCES `admin` (`admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



-- Table structure for table `assignments`
DROP TABLE IF EXISTS `assignments`;
CREATE TABLE `assignments` (
  `assignment_id` int(11) NOT NULL AUTO_INCREMENT,
  `teacher_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `due_date` datetime NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`assignment_id`),
  KEY `class_id` (`class_id`),
  KEY `idx_teacher_due_date` (`teacher_id`,`due_date`),
  CONSTRAINT `assignments_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`teacher_id`) ON DELETE CASCADE,
  CONSTRAINT `assignments_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



-- Table structure for table `attendance`
DROP TABLE IF EXISTS `attendance`;
CREATE TABLE `attendance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `section_subject_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `status` enum('present','absent','excused') NOT NULL,
  `time_in` time DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `student_id` (`student_id`),
  KEY `idx_attendance_section_subject` (`section_subject_id`),
  KEY `idx_attendance_status` (`status`),
  KEY `idx_attendance_date` (`date`),
  CONSTRAINT `attendance_ibfk_3` FOREIGN KEY (`section_subject_id`) REFERENCES `section_subjects` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=105 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



-- Table structure for table `attendance_logs`
DROP TABLE IF EXISTS `attendance_logs`;
CREATE TABLE `attendance_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `attendance_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `action` varchar(50) NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_logs_attendance` (`attendance_id`),
  KEY `fk_logs_teacher` (`teacher_id`),
  CONSTRAINT `fk_logs_attendance` FOREIGN KEY (`attendance_id`) REFERENCES `attendance` (`id`),
  CONSTRAINT `fk_logs_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`teacher_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



-- Table structure for table `classes`
DROP TABLE IF EXISTS `classes`;
CREATE TABLE `classes` (
  `class_id` int(11) NOT NULL AUTO_INCREMENT,
  `teacher_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `section_name` varchar(50) NOT NULL,
  `schedule_day` enum('Monday','Tuesday','Wednesday','Thursday','Friday') NOT NULL,
  `schedule_time` time NOT NULL,
  `status` enum('active','inactive','archived') DEFAULT 'active',
  `attendance_rate` decimal(5,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`class_id`),
  KEY `teacher_id` (`teacher_id`),
  KEY `subject_id` (`subject_id`),
  CONSTRAINT `classes_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`teacher_id`) ON DELETE CASCADE,
  CONSTRAINT `classes_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



-- Table structure for table `contact_information`
DROP TABLE IF EXISTS `contact_information`;
CREATE TABLE `contact_information` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



-- Table structure for table `date_ranges`
DROP TABLE IF EXISTS `date_ranges`;
CREATE TABLE `date_ranges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `range_type` varchar(20) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



-- Table structure for table `departments`
DROP TABLE IF EXISTS `departments`;
CREATE TABLE `departments` (
  `department_id` int(11) NOT NULL AUTO_INCREMENT,
  `department_name` varchar(100) NOT NULL,
  `department_code` varchar(20) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`department_id`),
  UNIQUE KEY `department_code` (`department_code`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `departments`
INSERT INTO `departments` VALUES("1","Mathematics Department","MATH","Focuses on developing students mathematical skills, logical reasoning, and problem-solving abilities through comprehensive math education.","active","2024-11-23 15:03:01","2024-11-23 15:03:01");
INSERT INTO `departments` VALUES("2","Science Department","SCI","Provides hands-on scientific education, covering physics, chemistry, biology, and environmental science to develop scientific inquiry and critical thinking.","active","2024-11-23 15:03:01","2024-11-23 15:03:01");
INSERT INTO `departments` VALUES("3","English Department","ENG","Develops students proficiency in English language skills including reading, writing, speaking, and listening through comprehensive language arts education.","active","2024-11-23 15:03:01","2024-11-23 15:03:01");
INSERT INTO `departments` VALUES("4","Filipino Department","FIL","Promotes Filipino language mastery and appreciation of Philippine literature and culture through comprehensive Filipino language education.","active","2024-11-23 15:03:01","2024-11-23 15:03:01");
INSERT INTO `departments` VALUES("5","Social Studies Department","SS","Teaches history, geography, and social sciences to develop students understanding of society, culture, and civic responsibility.","active","2024-11-23 15:03:01","2024-11-23 15:03:01");
INSERT INTO `departments` VALUES("6","MAPEH Department","MAPEH","Integrates Music, Arts, Physical Education, and Health education to develop students artistic, physical, and health awareness skills.","active","2024-11-23 15:03:01","2024-11-23 15:03:01");
INSERT INTO `departments` VALUES("7","Technology and Livelihood Education Department","TLE","Provides practical skills training in various technical and vocational areas to prepare students for future careers and entrepreneurship.","active","2024-11-23 15:03:01","2024-11-23 15:03:01");
INSERT INTO `departments` VALUES("8","Values Education Department","VALED","Focuses on character development, moral values, and ethical principles to shape responsible and value-oriented citizens.","active","2024-11-23 15:03:01","2024-11-23 15:03:01");
INSERT INTO `departments` VALUES("9","Guidance and Counseling","GUID","Provides student support services, career guidance, and personal counseling to promote student well-being and development.","active","2024-11-23 15:03:01","2024-11-23 15:03:01");
INSERT INTO `departments` VALUES("10","School Administration","ADMIN","Manages school operations, policies, and administrative functions to ensure effective school management and leadership.","active","2024-11-23 15:03:01","2024-11-23 15:03:01");
INSERT INTO `departments` VALUES("11","Research and Development Department","R&D","Promotes academic research, innovation, and continuous improvement in educational practices and methodologies.","active","2024-11-23 15:03:01","2024-11-23 15:03:01");
INSERT INTO `departments` VALUES("12","ICT Department","ICT","Manages information technology infrastructure and provides digital literacy education to support modern learning needs.","active","2024-11-23 15:03:01","2024-11-23 15:03:01");
INSERT INTO `departments` VALUES("13","Student Affairs Department","SAD","Oversees student activities, organizations, and welfare programs to enhance student life and development.","active","2024-11-23 15:03:01","2024-11-23 15:03:01");


-- Table structure for table `news`
DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `excerpt` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `category` enum('academic','event','announcement') NOT NULL,
  `date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('active','inactive') DEFAULT 'active',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `news`
INSERT INTO `news` VALUES("1","School Year 2023-2024 Opening","We are thrilled to announce the opening of School Year 2023-2024! As we embark on this new academic journey, we welcome both returning and new students to our campus.\n\nKey Highlights for this School Year:\n• Enhanced curriculum focusing on 21st-century skills\n• New extracurricular activities and clubs\n• Upgraded classroom facilities and learning resources\n• Implementation of blended learning approaches\n• Strengthened student support services\n\nImportant Dates:\n- First Day of Classes: June 5, 2024\n- Orientation Week: May 29-31, 2024\n- Parent-Teacher Meeting: June 15, 2024\n\nWe look forward to another year of academic excellence, personal growth, and memorable experiences. Let\'s make this school year extraordinary together!","Welcome back students! The new school year begins with excitement and new opportunities.","../images/1.jpg","academic","2024-01-15","2024-11-15 08:32:02","active");
INSERT INTO `news` VALUES("2","Annual Science Fair 2024","The Annual Science Fair 2024 is approaching! This year\'s theme is \"Innovation for Sustainable Future.\"\n\nEvent Details:\n• Date: February 20, 2024\n• Time: 8:00 AM - 4:00 PM\n• Venue: School Gymnasium\n• Categories: Environmental Science, Technology, Health Sciences, Physical Sciences\n\nCompetition Guidelines:\n1. Projects must be original and student-led\n2. Teams of 2-3 students allowed\n3. Display boards and presentations required\n4. Research documentation mandatory\n\nPrizes:\n- 1st Place: ₱5,000 and medals\n- 2nd Place: ₱3,000 and medals\n- 3rd Place: ₱2,000 and medals\n- Special Awards for Innovation\n\nRegistration deadline: February 10, 2024\nContact your science teacher for registration and more information.","Join us for an exciting showcase of student science projects and innovations.","../images/2.jpg","event","2024-02-20","2024-11-15 08:32:02","active");
INSERT INTO `news` VALUES("3","Important: Class Schedule Updates","Important Notice: Class Schedule Updates for the Current Semester\n\nThe following changes have been implemented to optimize learning experiences:\n\nMorning Sessions:\n• Grade 7: 7:00 AM - 12:00 PM\n• Grade 8: 7:30 AM - 12:30 PM\n• Grade 9: 8:00 AM - 1:00 PM\n\nAfternoon Sessions:\n• Grade 10: 12:30 PM - 5:30 PM\n• Grade 11: 1:00 PM - 6:00 PM\n• Grade 12: 1:30 PM - 6:30 PM\n\nAdditional Changes:\n1. Computer Laboratory sessions moved to mornings\n2. Physical Education classes scheduled for cooler hours\n3. Science Laboratory work in mid-morning slots\n4. Reading periods added to early morning schedules\n\nThese changes take effect from February 20, 2024. Please adjust your daily routines accordingly.","Please check the revised class schedules for the upcoming semester.","../images/3.jpg","announcement","2024-02-15","2024-11-15 08:32:02","active");
INSERT INTO `news` VALUES("4","New Learning Management System","We are excited to introduce our new Learning Management System (LMS) designed to enhance your educational experience!\n\nKey Features:\n• Interactive virtual classrooms\n• Real-time progress tracking\n• Digital assignment submission\n• Integrated video conferencing\n• Mobile-friendly interface\n• Automated attendance system\n• Parent portal access\n\nBenefits:\n1. 24/7 access to learning materials\n2. Improved student-teacher communication\n3. Paperless submission system\n4. Instant feedback on assignments\n5. Collaborative learning tools\n\nTraining Schedule:\n- Student Orientation: February 15-16, 2024\n- Parent Orientation: February 17, 2024\n- Teacher Training: February 12-14, 2024\n\nSystem Requirements:\n• Internet connection\n• Updated web browser\n• Minimum 4GB RAM device\n• Webcam and microphone\n\nThe new system will be fully implemented starting February 20, 2024.","Introducing our new digital learning platform for enhanced online education.","../images/4.jpg","academic","2024-02-10","2024-11-15 08:32:02","active");
INSERT INTO `news` VALUES("5","Sports Festival 2024","Get ready for the most exciting sports event of the year - Sports Festival 2024!\n\nEvent Schedule:\nMarch 1-5, 2024\nDay 1: Opening Ceremony and Track Events\nDay 2: Basketball and Volleyball Tournaments\nDay 3: Swimming Competition\nDay 4: Traditional Filipino Games\nDay 5: Championship Games and Closing Ceremony\n\nSports Categories:\n• Track and Field\n• Basketball (Boys/Girls)\n• Volleyball (Boys/Girls)\n• Swimming\n• Table Tennis\n• Badminton\n• Chess\n\nSpecial Events:\n- Inter-class Cheering Competition\n- Sports Exhibition Matches\n- Alumni Games\n- Teachers vs. Students Friendly Matches\n\nRegistration:\n• Sign up through your PE teachers\n• Deadline: February 25, 2024\n• Medical clearance required\n• Parent consent form mandatory\n\nPrizes for each category:\nGold Medal + Certificate\nSilver Medal + Certificate\nBronze Medal + Certificate","Get ready for our annual sports festival featuring various athletic competitions.","../images/2.jpg","event","2024-03-01","2024-11-15 08:32:02","active");
INSERT INTO `news` VALUES("6","Enrollment Period Extended","IMPORTANT ANNOUNCEMENT: Enrollment Period Extension\n\nWe are extending the enrollment period until March 15, 2024, to accommodate more students and ensure a smooth registration process.\n\nExtended Schedule:\n• Online Registration: 24/7 until March 15\n• On-site Enrollment: Monday-Friday, 8AM-5PM\n• Saturday Special Enrollment: 8AM-12PM\n\nRequired Documents:\n1. Form 137 (Report Card)\n2. Good Moral Certificate\n3. Birth Certificate\n4. 2x2 ID Pictures (4 pieces)\n5. Certificate of Completion/Graduation\n\nPayment Options:\n- Full Payment with 5% discount\n- Quarterly Payment Plan\n- Monthly Payment Plan\n\nSpecial Considerations:\n• Early bird discount until March 1\n• Sibling discount available\n• Scholar application extended\n• Financial assistance programs\n\nFor inquiries:\nEmail: enrollment@camerinohub.edu.ph\nPhone: (02) 8123-4567\nMobile: 0912-345-6789\n\nDon\'t miss this opportunity to be part of our academic community!","The enrollment period has been extended until March 15, 2024.","../images/1.jpg","announcement","2024-02-25","2024-11-15 08:32:02","active");


-- Table structure for table `notifications`
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `reference_id` (`reference_id`),
  KEY `notifications_announcement_fk` (`announcement_id`),
  KEY `fk_notifications_activity` (`activity_id`),
  CONSTRAINT `fk_notifications_activity` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`activity_id`),
  CONSTRAINT `notifications_announcement_fk` FOREIGN KEY (`announcement_id`) REFERENCES `announcements` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `notifications`
INSERT INTO `notifications` VALUES("3","37","20","23","student","quiz","114","114",NULL,"New Quiz: Q","A new quiz has been posted for \nDue date: 2024-12-05T01:37\nTotal Points: 3","0","0","2024-12-04 01:39:32");
INSERT INTO `notifications` VALUES("4","50","20","23","student","quiz","114","114",NULL,"New Quiz: Q","A new quiz has been posted for \nDue date: 2024-12-05T01:37\nTotal Points: 3","0","0","2024-12-04 01:39:32");
INSERT INTO `notifications` VALUES("6","37","20","23","student","quiz","115","115",NULL,"New Quiz: q","A new quiz has been posted for \nDue date: 2024-12-05T01:39\nTotal Points: 3","0","0","2024-12-04 01:39:58");
INSERT INTO `notifications` VALUES("7","50","20","23","student","quiz","115","115",NULL,"New Quiz: q","A new quiz has been posted for \nDue date: 2024-12-05T01:39\nTotal Points: 3","0","0","2024-12-04 01:39:58");
INSERT INTO `notifications` VALUES("9","37","20","23","student","quiz","116","116",NULL,"New Quiz: a","A new quiz has been posted for \nDue date: 2024-12-05T01:41\nTotal Points: 3","0","0","2024-12-04 01:42:03");
INSERT INTO `notifications` VALUES("10","50","20","23","student","quiz","116","116",NULL,"New Quiz: a","A new quiz has been posted for \nDue date: 2024-12-05T01:41\nTotal Points: 3","0","0","2024-12-04 01:42:03");
INSERT INTO `notifications` VALUES("11","37","20","23","student","quiz","128","128",NULL,"New Quiz: testtt","A new quiz has been posted: testtt\nDue date: 2024-12-07T05:06\nTotal Points: 3","0","0","2024-12-06 05:06:12");
INSERT INTO `notifications` VALUES("12","50","20","23","student","quiz","128","128",NULL,"New Quiz: testtt","A new quiz has been posted: testtt\nDue date: 2024-12-07T05:06\nTotal Points: 3","0","0","2024-12-06 05:06:12");
INSERT INTO `notifications` VALUES("14","37","20","23","student","quiz","131","131",NULL,"New Quiz: test","A new quiz has been posted: test\nDue date: 2024-12-07T06:03\nTotal Points: 3","0","0","2024-12-06 06:03:12");
INSERT INTO `notifications` VALUES("15","50","20","23","student","quiz","131","131",NULL,"New Quiz: test","A new quiz has been posted: test\nDue date: 2024-12-07T06:03\nTotal Points: 3","0","0","2024-12-06 06:03:12");
INSERT INTO `notifications` VALUES("17","37","20","23","student","quiz","135","135",NULL,"New Quiz: test","A new quiz has been posted: test\nDue date: 2024-12-07T06:07\nTotal Points: 3","0","0","2024-12-06 06:07:43");
INSERT INTO `notifications` VALUES("18","50","20","23","student","quiz","135","135",NULL,"New Quiz: test","A new quiz has been posted: test\nDue date: 2024-12-07T06:07\nTotal Points: 3","0","0","2024-12-06 06:07:43");
INSERT INTO `notifications` VALUES("20","37","20","23","student","quiz","136","136",NULL,"New Quiz: testtasd","A new quiz has been posted: testtasd\nDue date: 2024-12-07T07:24\nTotal Points: 3","0","0","2024-12-06 07:24:57");
INSERT INTO `notifications` VALUES("21","50","20","23","student","quiz","136","136",NULL,"New Quiz: testtasd","A new quiz has been posted: testtasd\nDue date: 2024-12-07T07:24\nTotal Points: 3","0","0","2024-12-06 07:24:57");
INSERT INTO `notifications` VALUES("22","37","20","23","student","quiz","137","137",NULL,"New Quiz: testasdasd","A new quiz has been posted: testasdasd\nDue date: 2024-12-07T15:20\nTotal Points: 3","0","0","2024-12-06 15:21:04");
INSERT INTO `notifications` VALUES("23","50","20","23","student","quiz","137","137",NULL,"New Quiz: testasdasd","A new quiz has been posted: testasdasd\nDue date: 2024-12-07T15:20\nTotal Points: 3","0","0","2024-12-06 15:21:04");
INSERT INTO `notifications` VALUES("25","37","20","23","student","quiz","138","138",NULL,"New Quiz: ASDASD","A new quiz has been posted: ASDASD\nDue date: 2024-12-07T15:58\nTotal Points: 3","0","0","2024-12-06 15:58:56");
INSERT INTO `notifications` VALUES("26","50","20","23","student","quiz","138","138",NULL,"New Quiz: ASDASD","A new quiz has been posted: ASDASD\nDue date: 2024-12-07T15:58\nTotal Points: 3","0","0","2024-12-06 15:58:56");
INSERT INTO `notifications` VALUES("28","37","20","23","student","quiz","139","139",NULL,"New Quiz: SHORT","A new quiz has been posted: SHORT\nDue date: 2024-12-07T17:27\nTotal Points: 3","0","0","2024-12-06 17:27:30");
INSERT INTO `notifications` VALUES("29","50","20","23","student","quiz","139","139",NULL,"New Quiz: SHORT","A new quiz has been posted: SHORT\nDue date: 2024-12-07T17:27\nTotal Points: 3","0","0","2024-12-06 17:27:30");
INSERT INTO `notifications` VALUES("31","37","20","23","student","quiz","140","140",NULL,"New Quiz: NEW","A new quiz has been posted: NEW\nDue date: 2024-12-07T17:47\nTotal Points: 3","0","0","2024-12-06 17:47:47");
INSERT INTO `notifications` VALUES("32","50","20","23","student","quiz","140","140",NULL,"New Quiz: NEW","A new quiz has been posted: NEW\nDue date: 2024-12-07T17:47\nTotal Points: 3","0","0","2024-12-06 17:47:47");
INSERT INTO `notifications` VALUES("34","37","20","23","student","quiz","141","141",NULL,"New Quiz: SHOSSSS","A new quiz has been posted: SHOSSSS\nDue date: 2024-12-07T18:41\nTotal Points: 3","0","0","2024-12-06 18:41:43");
INSERT INTO `notifications` VALUES("35","50","20","23","student","quiz","141","141",NULL,"New Quiz: SHOSSSS","A new quiz has been posted: SHOSSSS\nDue date: 2024-12-07T18:41\nTotal Points: 3","0","0","2024-12-06 18:41:43");
INSERT INTO `notifications` VALUES("37","37","20","23","student","quiz","142","142",NULL,"New Quiz: XZC","A new quiz has been posted: XZC\nDue date: 2024-12-08T00:09\nTotal Points: 5","0","0","2024-12-07 00:09:51");
INSERT INTO `notifications` VALUES("38","50","20","23","student","quiz","142","142",NULL,"New Quiz: XZC","A new quiz has been posted: XZC\nDue date: 2024-12-08T00:09\nTotal Points: 5","0","0","2024-12-07 00:09:51");
INSERT INTO `notifications` VALUES("40","37","20","23","student","quiz","146","146",NULL,"New Quiz: web","A new quiz has been posted: web\nDue date: 2024-12-10T01:37\nTotal Points: 7","0","0","2024-12-09 01:37:51");
INSERT INTO `notifications` VALUES("41","50","20","23","student","quiz","146","146",NULL,"New Quiz: web","A new quiz has been posted: web\nDue date: 2024-12-10T01:37\nTotal Points: 7","0","0","2024-12-09 01:37:51");
INSERT INTO `notifications` VALUES("43","37","20","23","student","quiz","147","147",NULL,"New Quiz: Test TEST","A new quiz has been posted: Test TEST\nDue date: 2024-12-10T01:44\nTotal Points: 3","0","0","2024-12-09 01:44:42");
INSERT INTO `notifications` VALUES("44","50","20","23","student","quiz","147","147",NULL,"New Quiz: Test TEST","A new quiz has been posted: Test TEST\nDue date: 2024-12-10T01:44\nTotal Points: 3","0","0","2024-12-09 01:44:42");
INSERT INTO `notifications` VALUES("46","37","20","23","student","quiz","148","148",NULL,"New Quiz: points","A new quiz has been posted: points\nDue date: 2024-12-10T02:03\nTotal Points: 3","0","0","2024-12-09 02:03:38");
INSERT INTO `notifications` VALUES("47","50","20","23","student","quiz","148","148",NULL,"New Quiz: points","A new quiz has been posted: points\nDue date: 2024-12-10T02:03\nTotal Points: 3","0","0","2024-12-09 02:03:38");
INSERT INTO `notifications` VALUES("49","37","20","23","student","quiz","149","149",NULL,"New Quiz: pacifoc","A new quiz has been posted: pacifoc\nDue date: 2024-12-10T02:13\nTotal Points: 3","0","0","2024-12-09 02:13:09");
INSERT INTO `notifications` VALUES("50","50","20","23","student","quiz","149","149",NULL,"New Quiz: pacifoc","A new quiz has been posted: pacifoc\nDue date: 2024-12-10T02:13\nTotal Points: 3","0","0","2024-12-09 02:13:09");
INSERT INTO `notifications` VALUES("52","37","20","23","student","quiz","150","150",NULL,"New Quiz: Hahaha","A new quiz has been posted: Hahaha\nDue date: 2024-12-10T11:14\nTotal Points: 3","0","0","2024-12-09 11:15:10");
INSERT INTO `notifications` VALUES("53","50","20","23","student","quiz","150","150",NULL,"New Quiz: Hahaha","A new quiz has been posted: Hahaha\nDue date: 2024-12-10T11:14\nTotal Points: 3","0","0","2024-12-09 11:15:10");
INSERT INTO `notifications` VALUES("55","37","20","23","student","quiz","151","151",NULL,"New Quiz: quiz 1","A new quiz has been posted: quiz 1\nDue date: 2024-12-10T12:56\nTotal Points: 4","0","0","2024-12-09 12:56:56");
INSERT INTO `notifications` VALUES("56","50","20","23","student","quiz","151","151",NULL,"New Quiz: quiz 1","A new quiz has been posted: quiz 1\nDue date: 2024-12-10T12:56\nTotal Points: 4","0","0","2024-12-09 12:56:56");
INSERT INTO `notifications` VALUES("58","37","20","23","student","quiz","154","154",NULL,"New Quiz: QUIZ NO 1","A new quiz has been posted: QUIZ NO 1\nDue date: 2024-12-16T23:00\nTotal Points: 1","0","0","2024-12-09 16:13:59");
INSERT INTO `notifications` VALUES("59","50","20","23","student","quiz","157","157",NULL,"New Quiz: quiz 77","A new quiz has been posted: quiz 77\nDue date: 2024-12-13T14:55\nTotal Points: 3","0","0","2024-12-12 06:55:11");


-- Table structure for table `question_choices`
DROP TABLE IF EXISTS `question_choices`;
CREATE TABLE `question_choices` (
  `choice_id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL,
  `choice_text` varchar(255) NOT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT 0,
  `choice_order` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`choice_id`),
  KEY `question_id` (`question_id`),
  CONSTRAINT `fk_question_choices_question` FOREIGN KEY (`question_id`) REFERENCES `quiz_questions` (`question_id`) ON DELETE CASCADE,
  CONSTRAINT `question_choices_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `quiz_questions` (`question_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=250 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `question_choices`
INSERT INTO `question_choices` VALUES("41","25","b","1","1","2024-12-04 00:24:56");
INSERT INTO `question_choices` VALUES("42","25","c","0","2","2024-12-04 00:24:56");
INSERT INTO `question_choices` VALUES("43","25","a","0","3","2024-12-04 00:24:56");
INSERT INTO `question_choices` VALUES("44","26","True","0","1","2024-12-04 00:24:56");
INSERT INTO `question_choices` VALUES("45","26","False","1","2","2024-12-04 00:24:56");
INSERT INTO `question_choices` VALUES("46","27","tite","1","1","2024-12-04 00:24:56");
INSERT INTO `question_choices` VALUES("47","28","A","1","1","2024-12-04 01:39:32");
INSERT INTO `question_choices` VALUES("48","28","B","0","2","2024-12-04 01:39:32");
INSERT INTO `question_choices` VALUES("49","29","True","0","1","2024-12-04 01:39:32");
INSERT INTO `question_choices` VALUES("50","29","False","1","2","2024-12-04 01:39:32");
INSERT INTO `question_choices` VALUES("51","30","ASD","1","1","2024-12-04 01:39:32");
INSERT INTO `question_choices` VALUES("52","31","a","1","1","2024-12-04 01:39:57");
INSERT INTO `question_choices` VALUES("53","31","b","0","2","2024-12-04 01:39:57");
INSERT INTO `question_choices` VALUES("54","32","True","0","1","2024-12-04 01:39:58");
INSERT INTO `question_choices` VALUES("55","32","False","1","2","2024-12-04 01:39:58");
INSERT INTO `question_choices` VALUES("56","33","asd","1","1","2024-12-04 01:39:58");
INSERT INTO `question_choices` VALUES("57","34","a","0","1","2024-12-04 01:42:03");
INSERT INTO `question_choices` VALUES("58","34","n","1","2","2024-12-04 01:42:03");
INSERT INTO `question_choices` VALUES("59","35","True","1","1","2024-12-04 01:42:03");
INSERT INTO `question_choices` VALUES("60","35","False","0","2","2024-12-04 01:42:03");
INSERT INTO `question_choices` VALUES("61","36","asd","1","1","2024-12-04 01:42:03");
INSERT INTO `question_choices` VALUES("62","37","a","0","1","2024-12-04 01:51:08");
INSERT INTO `question_choices` VALUES("63","37","b","1","2","2024-12-04 01:51:08");
INSERT INTO `question_choices` VALUES("64","38","True","0","1","2024-12-04 01:51:09");
INSERT INTO `question_choices` VALUES("65","38","False","1","2","2024-12-04 01:51:09");
INSERT INTO `question_choices` VALUES("66","40","a","1","1","2024-12-06 03:39:52");
INSERT INTO `question_choices` VALUES("67","40","b","0","2","2024-12-06 03:39:52");
INSERT INTO `question_choices` VALUES("68","41","True","0","1","2024-12-06 03:39:52");
INSERT INTO `question_choices` VALUES("69","41","False","1","2","2024-12-06 03:39:52");
INSERT INTO `question_choices` VALUES("70","45","a","1","1","2024-12-06 05:06:12");
INSERT INTO `question_choices` VALUES("71","45","b","0","2","2024-12-06 05:06:12");
INSERT INTO `question_choices` VALUES("72","46","True","1","1","2024-12-06 05:06:12");
INSERT INTO `question_choices` VALUES("73","46","False","0","2","2024-12-06 05:06:12");
INSERT INTO `question_choices` VALUES("74","50","a","1","1","2024-12-06 06:03:12");
INSERT INTO `question_choices` VALUES("75","50","b","0","2","2024-12-06 06:03:12");
INSERT INTO `question_choices` VALUES("76","51","True","0","1","2024-12-06 06:03:12");
INSERT INTO `question_choices` VALUES("77","51","False","0","2","2024-12-06 06:03:12");
INSERT INTO `question_choices` VALUES("86","60","True","1","1","2024-12-06 06:07:43");
INSERT INTO `question_choices` VALUES("87","60","False","0","2","2024-12-06 06:07:43");
INSERT INTO `question_choices` VALUES("156","63","True","1","1","2024-12-06 15:35:35");
INSERT INTO `question_choices` VALUES("157","63","False","0","2","2024-12-06 15:35:35");
INSERT INTO `question_choices` VALUES("158","65","True","1","1","2024-12-06 15:35:35");
INSERT INTO `question_choices` VALUES("159","65","False","0","2","2024-12-06 15:35:35");
INSERT INTO `question_choices` VALUES("160","73","True","1","1","2024-12-06 15:35:35");
INSERT INTO `question_choices` VALUES("161","73","False","0","2","2024-12-06 15:35:35");
INSERT INTO `question_choices` VALUES("162","75","True","0","1","2024-12-06 15:35:35");
INSERT INTO `question_choices` VALUES("163","75","False","1","2","2024-12-06 15:35:35");
INSERT INTO `question_choices` VALUES("166","90","a","1","1","2024-12-06 15:49:57");
INSERT INTO `question_choices` VALUES("167","90","b","0","2","2024-12-06 15:49:57");
INSERT INTO `question_choices` VALUES("168","91","A","1","1","2024-12-06 15:58:55");
INSERT INTO `question_choices` VALUES("169","91","B","0","2","2024-12-06 15:58:55");
INSERT INTO `question_choices` VALUES("170","92","True","1","1","2024-12-06 15:58:55");
INSERT INTO `question_choices` VALUES("171","92","False","0","2","2024-12-06 15:58:56");
INSERT INTO `question_choices` VALUES("172","94","a","1","1","2024-12-06 15:58:56");
INSERT INTO `question_choices` VALUES("173","94","b","0","2","2024-12-06 15:58:56");
INSERT INTO `question_choices` VALUES("174","95","A","1","1","2024-12-06 17:27:30");
INSERT INTO `question_choices` VALUES("175","95","B","0","2","2024-12-06 17:27:30");
INSERT INTO `question_choices` VALUES("176","96","True","1","1","2024-12-06 17:27:30");
INSERT INTO `question_choices` VALUES("177","96","False","0","2","2024-12-06 17:27:30");
INSERT INTO `question_choices` VALUES("178","98","A","1","1","2024-12-06 17:47:47");
INSERT INTO `question_choices` VALUES("179","98","B","0","2","2024-12-06 17:47:47");
INSERT INTO `question_choices` VALUES("180","99","True","1","1","2024-12-06 17:47:47");
INSERT INTO `question_choices` VALUES("181","99","False","0","2","2024-12-06 17:47:47");
INSERT INTO `question_choices` VALUES("182","101","a","1","1","2024-12-06 17:47:47");
INSERT INTO `question_choices` VALUES("183","101","b","0","2","2024-12-06 17:47:47");
INSERT INTO `question_choices` VALUES("184","102","True","1","1","2024-12-06 17:47:47");
INSERT INTO `question_choices` VALUES("185","102","False","0","2","2024-12-06 17:47:47");
INSERT INTO `question_choices` VALUES("186","104","a","1","1","2024-12-06 18:41:43");
INSERT INTO `question_choices` VALUES("187","104","b","0","2","2024-12-06 18:41:43");
INSERT INTO `question_choices` VALUES("188","105","True","1","1","2024-12-06 18:41:43");
INSERT INTO `question_choices` VALUES("189","105","False","0","2","2024-12-06 18:41:43");
INSERT INTO `question_choices` VALUES("190","107","a","1","1","2024-12-06 18:41:43");
INSERT INTO `question_choices` VALUES("191","107","b","0","2","2024-12-06 18:41:43");
INSERT INTO `question_choices` VALUES("192","108","yt","1","1","2024-12-07 00:09:51");
INSERT INTO `question_choices` VALUES("193","108","test","0","2","2024-12-07 00:09:51");
INSERT INTO `question_choices` VALUES("194","109","True","1","1","2024-12-07 00:09:51");
INSERT INTO `question_choices` VALUES("195","109","False","0","2","2024-12-07 00:09:51");
INSERT INTO `question_choices` VALUES("196","110","a","1","1","2024-12-07 00:09:51");
INSERT INTO `question_choices` VALUES("197","110","b","0","2","2024-12-07 00:09:51");
INSERT INTO `question_choices` VALUES("198","113","a","0","1","2024-12-09 01:37:51");
INSERT INTO `question_choices` VALUES("199","113","b","0","2","2024-12-09 01:37:51");
INSERT INTO `question_choices` VALUES("200","113","c","1","3","2024-12-09 01:37:51");
INSERT INTO `question_choices` VALUES("201","113","d","0","4","2024-12-09 01:37:51");
INSERT INTO `question_choices` VALUES("202","114","True","1","1","2024-12-09 01:37:51");
INSERT INTO `question_choices` VALUES("203","114","False","0","2","2024-12-09 01:37:51");
INSERT INTO `question_choices` VALUES("204","118","a","1","1","2024-12-09 01:37:51");
INSERT INTO `question_choices` VALUES("205","118","b","0","2","2024-12-09 01:37:51");
INSERT INTO `question_choices` VALUES("206","118","c","0","3","2024-12-09 01:37:51");
INSERT INTO `question_choices` VALUES("207","118","d","0","4","2024-12-09 01:37:51");
INSERT INTO `question_choices` VALUES("208","119","True","0","1","2024-12-09 01:37:51");
INSERT INTO `question_choices` VALUES("209","119","False","1","2","2024-12-09 01:37:51");
INSERT INTO `question_choices` VALUES("210","120","Oo","1","1","2024-12-09 01:44:42");
INSERT INTO `question_choices` VALUES("211","120","Hindi","0","2","2024-12-09 01:44:42");
INSERT INTO `question_choices` VALUES("212","120","Wag","0","3","2024-12-09 01:44:42");
INSERT INTO `question_choices` VALUES("213","120","check","0","4","2024-12-09 01:44:42");
INSERT INTO `question_choices` VALUES("214","121","True","0","1","2024-12-09 01:44:42");
INSERT INTO `question_choices` VALUES("215","121","False","1","2","2024-12-09 01:44:42");
INSERT INTO `question_choices` VALUES("216","123","a","1","1","2024-12-09 02:03:38");
INSERT INTO `question_choices` VALUES("217","123","b","0","2","2024-12-09 02:03:38");
INSERT INTO `question_choices` VALUES("218","123","c","0","3","2024-12-09 02:03:38");
INSERT INTO `question_choices` VALUES("219","123","d","0","4","2024-12-09 02:03:38");
INSERT INTO `question_choices` VALUES("220","124","True","1","1","2024-12-09 02:03:38");
INSERT INTO `question_choices` VALUES("221","124","False","0","2","2024-12-09 02:03:38");
INSERT INTO `question_choices` VALUES("222","126","a","0","1","2024-12-09 02:13:09");
INSERT INTO `question_choices` VALUES("223","126","b","0","2","2024-12-09 02:13:09");
INSERT INTO `question_choices` VALUES("224","126","c","1","3","2024-12-09 02:13:09");
INSERT INTO `question_choices` VALUES("225","126","d","0","4","2024-12-09 02:13:09");
INSERT INTO `question_choices` VALUES("226","127","True","0","1","2024-12-09 02:13:09");
INSERT INTO `question_choices` VALUES("227","127","False","1","2","2024-12-09 02:13:09");
INSERT INTO `question_choices` VALUES("228","129","Hindi","1","1","2024-12-09 11:15:10");
INSERT INTO `question_choices` VALUES("229","129","Oo","0","2","2024-12-09 11:15:10");
INSERT INTO `question_choices` VALUES("230","130","True","0","1","2024-12-09 11:15:10");
INSERT INTO `question_choices` VALUES("231","130","False","1","2","2024-12-09 11:15:10");
INSERT INTO `question_choices` VALUES("232","132","a","0","1","2024-12-09 12:56:56");
INSERT INTO `question_choices` VALUES("233","132","b","0","2","2024-12-09 12:56:56");
INSERT INTO `question_choices` VALUES("234","132","c","1","3","2024-12-09 12:56:56");
INSERT INTO `question_choices` VALUES("235","132","d","0","4","2024-12-09 12:56:56");
INSERT INTO `question_choices` VALUES("236","133","True","0","1","2024-12-09 12:56:56");
INSERT INTO `question_choices` VALUES("237","133","False","1","2","2024-12-09 12:56:56");
INSERT INTO `question_choices` VALUES("242","136","A) Structured Query Language","1","1","2024-12-09 16:19:05");
INSERT INTO `question_choices` VALUES("243","136","B) Simple Query Language","0","2","2024-12-09 16:19:05");
INSERT INTO `question_choices` VALUES("244","136","C) Standard Query Language","0","3","2024-12-09 16:19:05");
INSERT INTO `question_choices` VALUES("245","136","D) Sequential Query Language","0","4","2024-12-09 16:19:05");
INSERT INTO `question_choices` VALUES("246","138","hi","0","1","2024-12-12 06:55:11");
INSERT INTO `question_choices` VALUES("247","138","hello","1","2","2024-12-12 06:55:11");
INSERT INTO `question_choices` VALUES("248","139","True","0","1","2024-12-12 06:55:11");
INSERT INTO `question_choices` VALUES("249","139","False","1","2","2024-12-12 06:55:11");


-- Table structure for table `quiz_access_codes`
DROP TABLE IF EXISTS `quiz_access_codes`;
CREATE TABLE `quiz_access_codes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `quiz_id` int(11) NOT NULL,
  `access_code` varchar(10) NOT NULL,
  `valid_from` timestamp NOT NULL DEFAULT current_timestamp(),
  `valid_until` timestamp NULL DEFAULT NULL,
  `max_attempts` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_access_code` (`access_code`),
  KEY `quiz_id` (`quiz_id`),
  CONSTRAINT `quiz_access_codes_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `activities` (`activity_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



-- Table structure for table `quiz_answers`
DROP TABLE IF EXISTS `quiz_answers`;
CREATE TABLE `quiz_answers` (
  `answer_id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL,
  `answer_text` text NOT NULL,
  PRIMARY KEY (`answer_id`),
  KEY `fk_quiz_answers_question` (`question_id`),
  CONSTRAINT `fk_quiz_answers_question` FOREIGN KEY (`question_id`) REFERENCES `quiz_questions` (`question_id`) ON DELETE CASCADE,
  CONSTRAINT `quiz_answers_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `quiz_questions` (`question_id`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `quiz_answers`
INSERT INTO `quiz_answers` VALUES("1","52","test");
INSERT INTO `quiz_answers` VALUES("2","61","test");
INSERT INTO `quiz_answers` VALUES("40","86","a                            \n                                \n                                b");
INSERT INTO `quiz_answers` VALUES("41","87","a                            \n                                \n                                b");
INSERT INTO `quiz_answers` VALUES("42","89","a                            \n                                \n                                b");
INSERT INTO `quiz_answers` VALUES("43","64","asd");
INSERT INTO `quiz_answers` VALUES("44","66","asd");
INSERT INTO `quiz_answers` VALUES("45","72","a                            \n                                \n                                b");
INSERT INTO `quiz_answers` VALUES("46","74","asd");
INSERT INTO `quiz_answers` VALUES("47","88","a                            \n                                \n                                b");
INSERT INTO `quiz_answers` VALUES("48","93","asd");
INSERT INTO `quiz_answers` VALUES("49","97","asd");
INSERT INTO `quiz_answers` VALUES("50","100","asd");
INSERT INTO `quiz_answers` VALUES("51","103","asd");
INSERT INTO `quiz_answers` VALUES("52","106","asd");
INSERT INTO `quiz_answers` VALUES("53","111","asd");
INSERT INTO `quiz_answers` VALUES("54","112","asd");
INSERT INTO `quiz_answers` VALUES("55","115","asd");
INSERT INTO `quiz_answers` VALUES("56","116","2");
INSERT INTO `quiz_answers` VALUES("57","117","bdrtdfg");
INSERT INTO `quiz_answers` VALUES("58","122","malaki");
INSERT INTO `quiz_answers` VALUES("59","125","francis");
INSERT INTO `quiz_answers` VALUES("60","128","asd");
INSERT INTO `quiz_answers` VALUES("61","131","Oo");
INSERT INTO `quiz_answers` VALUES("62","134","4");
INSERT INTO `quiz_answers` VALUES("63","135","test1");
INSERT INTO `quiz_answers` VALUES("64","137","heloo");


-- Table structure for table `quiz_attempts`
DROP TABLE IF EXISTS `quiz_attempts`;
CREATE TABLE `quiz_attempts` (
  `attempt_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `start_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `end_time` timestamp NULL DEFAULT NULL,
  `score` decimal(5,2) DEFAULT 0.00,
  `status` enum('in_progress','completed','abandoned') DEFAULT 'in_progress',
  `attempt_number` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`attempt_id`),
  KEY `quiz_id` (`quiz_id`),
  CONSTRAINT `quiz_attempts_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `activities` (`activity_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `quiz_attempts`
INSERT INTO `quiz_attempts` VALUES("34","37","113","2024-12-04 00:43:07",NULL,"0.00","in_progress","1","2024-12-04 00:43:07");
INSERT INTO `quiz_attempts` VALUES("35","50","113","2024-12-04 01:32:11",NULL,"0.00","in_progress","1","2024-12-04 01:32:11");
INSERT INTO `quiz_attempts` VALUES("36","50","124","2024-12-04 01:51:17",NULL,"0.00","in_progress","1","2024-12-04 01:51:17");
INSERT INTO `quiz_attempts` VALUES("37","50","116","2024-12-04 01:53:39",NULL,"0.00","in_progress","1","2024-12-04 01:53:39");
INSERT INTO `quiz_attempts` VALUES("38","50","125","2024-12-06 03:41:34",NULL,"0.00","in_progress","1","2024-12-06 03:40:39");


-- Table structure for table `quiz_questions`
DROP TABLE IF EXISTS `quiz_questions`;
CREATE TABLE `quiz_questions` (
  `question_id` int(11) NOT NULL AUTO_INCREMENT,
  `quiz_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `question_type` enum('multiple_choice','true_false','short_answer') NOT NULL,
  `points` int(11) DEFAULT 1,
  `question_order` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`question_id`),
  KEY `quiz_id` (`quiz_id`),
  CONSTRAINT `quiz_questions_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `activities` (`activity_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=140 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `quiz_questions`
INSERT INTO `quiz_questions` VALUES("25","113","asd","multiple_choice","1","1","2024-12-04 00:24:56");
INSERT INTO `quiz_questions` VALUES("26","113","s","true_false","1","2","2024-12-04 00:24:56");
INSERT INTO `quiz_questions` VALUES("27","113","gawd","short_answer","1","3","2024-12-04 00:24:56");
INSERT INTO `quiz_questions` VALUES("28","114","WQE","multiple_choice","1","1","2024-12-04 01:39:32");
INSERT INTO `quiz_questions` VALUES("29","114","QWE","true_false","1","2","2024-12-04 01:39:32");
INSERT INTO `quiz_questions` VALUES("30","114","A","short_answer","1","3","2024-12-04 01:39:32");
INSERT INTO `quiz_questions` VALUES("31","115","q","multiple_choice","1","1","2024-12-04 01:39:57");
INSERT INTO `quiz_questions` VALUES("32","115","a","true_false","1","2","2024-12-04 01:39:57");
INSERT INTO `quiz_questions` VALUES("33","115","qw","short_answer","1","3","2024-12-04 01:39:58");
INSERT INTO `quiz_questions` VALUES("34","116","a","multiple_choice","1","1","2024-12-04 01:42:03");
INSERT INTO `quiz_questions` VALUES("35","116","a","true_false","1","2","2024-12-04 01:42:03");
INSERT INTO `quiz_questions` VALUES("36","116","a","short_answer","1","3","2024-12-04 01:42:03");
INSERT INTO `quiz_questions` VALUES("37","124","a","multiple_choice","1","1","2024-12-04 01:51:08");
INSERT INTO `quiz_questions` VALUES("38","124","a","true_false","1","2","2024-12-04 01:51:09");
INSERT INTO `quiz_questions` VALUES("39","124","asd","short_answer","1","3","2024-12-04 01:51:09");
INSERT INTO `quiz_questions` VALUES("40","125","test1","multiple_choice","1","1","2024-12-06 03:39:52");
INSERT INTO `quiz_questions` VALUES("41","125","testss","true_false","1","2","2024-12-06 03:39:52");
INSERT INTO `quiz_questions` VALUES("42","125","test","short_answer","1","3","2024-12-06 03:39:52");
INSERT INTO `quiz_questions` VALUES("45","128","test","multiple_choice","1","1","2024-12-06 05:06:12");
INSERT INTO `quiz_questions` VALUES("46","128","test","true_false","1","2","2024-12-06 05:06:12");
INSERT INTO `quiz_questions` VALUES("47","128","test","short_answer","1","3","2024-12-06 05:06:12");
INSERT INTO `quiz_questions` VALUES("50","131","test","multiple_choice","1","1","2024-12-06 06:03:12");
INSERT INTO `quiz_questions` VALUES("51","131","test","true_false","1","2","2024-12-06 06:03:12");
INSERT INTO `quiz_questions` VALUES("52","131","test","short_answer","1","3","2024-12-06 06:03:12");
INSERT INTO `quiz_questions` VALUES("60","135","test","true_false","1","2","2024-12-06 06:07:43");
INSERT INTO `quiz_questions` VALUES("61","135","test","short_answer","1","3","2024-12-06 06:07:43");
INSERT INTO `quiz_questions` VALUES("63","136","","true_false","1","2","2024-12-06 07:24:57");
INSERT INTO `quiz_questions` VALUES("64","136","","short_answer","1","3","2024-12-06 07:24:57");
INSERT INTO `quiz_questions` VALUES("65","136","","true_false","1","4","2024-12-06 07:24:57");
INSERT INTO `quiz_questions` VALUES("66","136","","short_answer","1","5","2024-12-06 07:24:57");
INSERT INTO `quiz_questions` VALUES("72","136","","short_answer","1","6","2024-12-06 07:46:35");
INSERT INTO `quiz_questions` VALUES("73","136","","true_false","1","7","2024-12-06 07:49:32");
INSERT INTO `quiz_questions` VALUES("74","136","","short_answer","1","8","2024-12-06 07:49:41");
INSERT INTO `quiz_questions` VALUES("75","136","","true_false","1","9","2024-12-06 08:30:23");
INSERT INTO `quiz_questions` VALUES("86","136","","short_answer","1","0","2024-12-06 08:45:56");
INSERT INTO `quiz_questions` VALUES("87","136","","short_answer","1","0","2024-12-06 08:46:06");
INSERT INTO `quiz_questions` VALUES("88","137","","short_answer","1","1","2024-12-06 15:21:04");
INSERT INTO `quiz_questions` VALUES("89","136","","short_answer","1","0","2024-12-06 15:35:27");
INSERT INTO `quiz_questions` VALUES("90","137","testaasdasd","multiple_choice","1","0","2024-12-06 15:38:34");
INSERT INTO `quiz_questions` VALUES("91","138","ASD","multiple_choice","1","1","2024-12-06 15:58:55");
INSERT INTO `quiz_questions` VALUES("92","138","ASD","true_false","1","2","2024-12-06 15:58:55");
INSERT INTO `quiz_questions` VALUES("93","138","asd","short_answer","1","3","2024-12-06 15:58:56");
INSERT INTO `quiz_questions` VALUES("94","138","asd","multiple_choice","1","4","2024-12-06 15:58:56");
INSERT INTO `quiz_questions` VALUES("95","139","ASD","multiple_choice","1","1","2024-12-06 17:27:30");
INSERT INTO `quiz_questions` VALUES("96","139","asd","true_false","1","2","2024-12-06 17:27:30");
INSERT INTO `quiz_questions` VALUES("97","139","asd","short_answer","1","3","2024-12-06 17:27:30");
INSERT INTO `quiz_questions` VALUES("98","140","ASD","multiple_choice","1","1","2024-12-06 17:47:47");
INSERT INTO `quiz_questions` VALUES("99","140","TEST","true_false","1","2","2024-12-06 17:47:47");
INSERT INTO `quiz_questions` VALUES("100","140","TEST","short_answer","1","3","2024-12-06 17:47:47");
INSERT INTO `quiz_questions` VALUES("101","140","asd","multiple_choice","1","4","2024-12-06 17:47:47");
INSERT INTO `quiz_questions` VALUES("102","140","asd","true_false","1","5","2024-12-06 17:47:47");
INSERT INTO `quiz_questions` VALUES("103","140","asd","short_answer","1","6","2024-12-06 17:47:47");
INSERT INTO `quiz_questions` VALUES("104","141","ASD","multiple_choice","1","1","2024-12-06 18:41:43");
INSERT INTO `quiz_questions` VALUES("105","141","asd","true_false","1","2","2024-12-06 18:41:43");
INSERT INTO `quiz_questions` VALUES("106","141","asd","short_answer","1","3","2024-12-06 18:41:43");
INSERT INTO `quiz_questions` VALUES("107","141","asd","multiple_choice","1","4","2024-12-06 18:41:43");
INSERT INTO `quiz_questions` VALUES("108","142","asd","multiple_choice","1","1","2024-12-07 00:09:50");
INSERT INTO `quiz_questions` VALUES("109","142","test","true_false","1","2","2024-12-07 00:09:51");
INSERT INTO `quiz_questions` VALUES("110","142","test","multiple_choice","1","3","2024-12-07 00:09:51");
INSERT INTO `quiz_questions` VALUES("111","142","test","short_answer","1","4","2024-12-07 00:09:51");
INSERT INTO `quiz_questions` VALUES("112","142","test","short_answer","1","5","2024-12-07 00:09:51");
INSERT INTO `quiz_questions` VALUES("113","146","test","multiple_choice","1","1","2024-12-09 01:37:51");
INSERT INTO `quiz_questions` VALUES("114","146","is that true?","true_false","1","2","2024-12-09 01:37:51");
INSERT INTO `quiz_questions` VALUES("115","146","test1","short_answer","1","3","2024-12-09 01:37:51");
INSERT INTO `quiz_questions` VALUES("116","146","test2 1+1","short_answer","1","4","2024-12-09 01:37:51");
INSERT INTO `quiz_questions` VALUES("117","146","test3","short_answer","1","5","2024-12-09 01:37:51");
INSERT INTO `quiz_questions` VALUES("118","146","tes","multiple_choice","1","6","2024-12-09 01:37:51");
INSERT INTO `quiz_questions` VALUES("119","146","hehe","true_false","1","7","2024-12-09 01:37:51");
INSERT INTO `quiz_questions` VALUES("120","147","Matutulog na","multiple_choice","2","1","2024-12-09 01:44:42");
INSERT INTO `quiz_questions` VALUES("121","147","Pogi ba ako","true_false","2","2","2024-12-09 01:44:42");
INSERT INTO `quiz_questions` VALUES("122","147","Gaano kalaki yung ulo ko","short_answer","6","3","2024-12-09 01:44:42");
INSERT INTO `quiz_questions` VALUES("123","148","test","multiple_choice","2","1","2024-12-09 02:03:38");
INSERT INTO `quiz_questions` VALUES("124","148","test","true_false","1","2","2024-12-09 02:03:38");
INSERT INTO `quiz_questions` VALUES("125","148","give my name","short_answer","7","3","2024-12-09 02:03:38");
INSERT INTO `quiz_questions` VALUES("126","149","test","multiple_choice","1","1","2024-12-09 02:13:09");
INSERT INTO `quiz_questions` VALUES("127","149","test","true_false","1","2","2024-12-09 02:13:09");
INSERT INTO `quiz_questions` VALUES("128","149","asd","short_answer","1","3","2024-12-09 02:13:09");
INSERT INTO `quiz_questions` VALUES("129","150","Papasok ka ba","multiple_choice","1","1","2024-12-09 11:15:10");
INSERT INTO `quiz_questions` VALUES("130","150","Lalaki ka ba","true_false","1","2","2024-12-09 11:15:10");
INSERT INTO `quiz_questions` VALUES("131","150","Lalaki ka ba","short_answer","1","3","2024-12-09 11:15:10");
INSERT INTO `quiz_questions` VALUES("132","151","Test 1","multiple_choice","1","1","2024-12-09 12:56:56");
INSERT INTO `quiz_questions` VALUES("133","151","Pogi si pacific","true_false","1","2","2024-12-09 12:56:56");
INSERT INTO `quiz_questions` VALUES("134","151","1+3","short_answer","1","3","2024-12-09 12:56:56");
INSERT INTO `quiz_questions` VALUES("135","151","Test 1","short_answer","1","4","2024-12-09 12:56:56");
INSERT INTO `quiz_questions` VALUES("136","154","What does SQL stand for?","multiple_choice","1","1","2024-12-09 16:13:59");
INSERT INTO `quiz_questions` VALUES("137","157","hi","short_answer","1","1","2024-12-12 06:55:11");
INSERT INTO `quiz_questions` VALUES("138","157","hello","multiple_choice","1","2","2024-12-12 06:55:11");
INSERT INTO `quiz_questions` VALUES("139","157","false","true_false","1","3","2024-12-12 06:55:11");


-- Table structure for table `remember_tokens`
DROP TABLE IF EXISTS `remember_tokens`;
CREATE TABLE `remember_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expiry` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `student_id` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



-- Table structure for table `section_advisers`
DROP TABLE IF EXISTS `section_advisers`;
CREATE TABLE `section_advisers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `academic_year_id` int(11) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `section_id` (`section_id`),
  KEY `teacher_id` (`teacher_id`),
  KEY `academic_year_id` (`academic_year_id`),
  CONSTRAINT `section_advisers_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `sections` (`section_id`),
  CONSTRAINT `section_advisers_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`teacher_id`),
  CONSTRAINT `section_advisers_ibfk_3` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



-- Table structure for table `section_schedules`
DROP TABLE IF EXISTS `section_schedules`;
CREATE TABLE `section_schedules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `academic_year_id` int(11) NOT NULL,
  `schedule_day` enum('Monday','Tuesday','Wednesday','Thursday','Friday') NOT NULL,
  `schedule_time` time NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `section_id` (`section_id`),
  KEY `subject_id` (`subject_id`),
  KEY `teacher_id` (`teacher_id`),
  KEY `academic_year_id` (`academic_year_id`),
  CONSTRAINT `section_schedules_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `sections` (`section_id`),
  CONSTRAINT `section_schedules_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
  CONSTRAINT `section_schedules_ibfk_3` FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`teacher_id`),
  CONSTRAINT `section_schedules_ibfk_4` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



-- Table structure for table `section_subjects`
DROP TABLE IF EXISTS `section_subjects`;
CREATE TABLE `section_subjects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `academic_year_id` int(11) NOT NULL,
  `schedule_day` enum('Monday','Tuesday','Wednesday','Thursday','Friday') NOT NULL,
  `schedule_time` time NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `enrollment_code` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `enrollment_code` (`enrollment_code`),
  KEY `section_id` (`section_id`),
  KEY `subject_id` (`subject_id`),
  KEY `teacher_id` (`teacher_id`),
  KEY `academic_year_id` (`academic_year_id`),
  CONSTRAINT `fk_academic_year` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`),
  CONSTRAINT `section_subjects_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `sections` (`section_id`) ON DELETE CASCADE,
  CONSTRAINT `section_subjects_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `section_subjects_ibfk_3` FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`teacher_id`) ON DELETE CASCADE,
  CONSTRAINT `section_subjects_ibfk_4` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `section_subjects`
INSERT INTO `section_subjects` VALUES("46","20","21","9","1","Tuesday","03:00:00","inactive","2024-11-23 16:34:07","CMRH1047");
INSERT INTO `section_subjects` VALUES("52","21","21","9","1","Monday","00:00:00","inactive","2024-11-24 19:43:50","CMRH1662");
INSERT INTO `section_subjects` VALUES("53","20","22","123","1","Monday","00:00:00","inactive","2024-12-01 12:29:42",NULL);
INSERT INTO `section_subjects` VALUES("54","21","23","124","1","Monday","00:00:00","active","2024-12-01 12:29:54","CMRH7392");
INSERT INTO `section_subjects` VALUES("55","21","24","125","1","Monday","00:00:00","active","2024-12-01 12:30:12",NULL);
INSERT INTO `section_subjects` VALUES("56","20","23","9","1","Monday","21:09:00","active","2024-12-03 20:09:14","CMRH7513");
INSERT INTO `section_subjects` VALUES("57","25","23","126","1","Monday","06:00:00","active","2024-12-11 15:37:21","CMRH4475");
INSERT INTO `section_subjects` VALUES("58","20","23","127","1","Tuesday","07:43:00","active","2024-12-11 23:43:15","CMRH5968");


-- Table structure for table `sections`
DROP TABLE IF EXISTS `sections`;
CREATE TABLE `sections` (
  `section_id` int(11) NOT NULL AUTO_INCREMENT,
  `section_name` varchar(50) NOT NULL,
  `grade_level` enum('7','8','9','10') NOT NULL,
  `adviser_id` int(11) DEFAULT NULL,
  `school_year` varchar(9) NOT NULL,
  `status` enum('active','inactive','archived') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`section_id`),
  UNIQUE KEY `section_name` (`section_name`),
  KEY `adviser_id` (`adviser_id`),
  CONSTRAINT `sections_ibfk_1` FOREIGN KEY (`adviser_id`) REFERENCES `teacher` (`teacher_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `sections`
INSERT INTO `sections` VALUES("20","Ruby","7",NULL,"2024-2025","active","2024-11-23 16:30:56");
INSERT INTO `sections` VALUES("21","Ice","8",NULL,"2024-2025","active","2024-11-24 19:43:42");
INSERT INTO `sections` VALUES("25","Daffodil","7",NULL,"2024-2025","active","2024-12-11 15:37:02");


-- Table structure for table `security_violations`
DROP TABLE IF EXISTS `security_violations`;
CREATE TABLE `security_violations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `violation_type` enum('tab_switch','fullscreen_exit') NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_student_id` (`student_id`),
  KEY `idx_quiz_id` (`quiz_id`),
  CONSTRAINT `fk_security_violations_quiz` FOREIGN KEY (`quiz_id`) REFERENCES `activities` (`activity_id`),
  CONSTRAINT `fk_security_violations_student` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`)
) ENGINE=InnoDB AUTO_INCREMENT=161 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `security_violations`
INSERT INTO `security_violations` VALUES("147","50","113","fullscreen_exit","Student attempted to fullscreen exit","2024-12-04 01:32:21");
INSERT INTO `security_violations` VALUES("148","50","113","fullscreen_exit","Student attempted to fullscreen exit","2024-12-04 01:32:24");
INSERT INTO `security_violations` VALUES("149","50","113","","Student attempted to page leave","2024-12-04 01:32:25");
INSERT INTO `security_violations` VALUES("150","50","113","fullscreen_exit","Student attempted to fullscreen exit","2024-12-04 01:32:26");
INSERT INTO `security_violations` VALUES("151","50","124","","Student attempted to page leave","2024-12-04 01:51:23");
INSERT INTO `security_violations` VALUES("152","50","124","fullscreen_exit","Student attempted to fullscreen exit","2024-12-04 01:51:24");
INSERT INTO `security_violations` VALUES("153","50","116","fullscreen_exit","Student attempted to fullscreen exit","2024-12-04 01:53:49");
INSERT INTO `security_violations` VALUES("154","50","116","tab_switch","Student attempted to tab switch","2024-12-04 01:53:54");
INSERT INTO `security_violations` VALUES("155","50","116","fullscreen_exit","Student attempted to fullscreen exit","2024-12-04 01:54:01");
INSERT INTO `security_violations` VALUES("156","50","116","","Student attempted to page leave","2024-12-04 01:54:01");
INSERT INTO `security_violations` VALUES("157","50","125","","Student attempted to page leave","2024-12-06 03:40:46");
INSERT INTO `security_violations` VALUES("158","50","125","fullscreen_exit","Student attempted to fullscreen exit","2024-12-06 03:40:47");
INSERT INTO `security_violations` VALUES("159","50","125","fullscreen_exit","Student attempted to fullscreen exit","2024-12-06 03:41:37");
INSERT INTO `security_violations` VALUES("160","50","125","tab_switch","Student attempted to tab switch","2024-12-06 03:41:59");


-- Table structure for table `site_map_content`
DROP TABLE IF EXISTS `site_map_content`;
CREATE TABLE `site_map_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('active','inactive') DEFAULT 'active',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



-- Table structure for table `student`
DROP TABLE IF EXISTS `student`;
CREATE TABLE `student` (
  `student_id` bigint(20) NOT NULL AUTO_INCREMENT,
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
  `password_recovery` enum('yes','no') DEFAULT 'no',
  PRIMARY KEY (`student_id`),
  UNIQUE KEY `lrn_unique` (`lrn`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_last_activity` (`last_activity`)
) ENGINE=InnoDB AUTO_INCREMENT=270 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `student`
INSERT INTO `student` VALUES("38","123456789999","469e7e66cff79f931488a5feee1909e6","christian@frncszxc.helioho.st","09701333333","Male",NULL,"Edrian","Pacifico","Ilagan",NULL,"","active","0",NULL,"0","2024-11-24 20:45:19","2024-12-11 17:28:56",NULL,NULL,NULL,"no");
INSERT INTO `student` VALUES("39","100000999999","f8c7c5d26055adb57617e687dbf6811c","eleanorpacifico@gmail.com","09701640382","Male",NULL,"Christopher","Pacifico","Ilagan",NULL,"","active","0",NULL,"0","2024-11-24 22:48:34","2024-12-11 17:18:19",NULL,NULL,NULL,"no");
INSERT INTO `student` VALUES("40","123456789111","9acb31bd27f2bb425f0b07c9b5322ed3","student1@gmail.com","09504222701","Male",NULL,"student1","student1","student1",NULL,"","active","0",NULL,"0","2024-12-01 13:36:59","2024-12-11 17:28:55",NULL,NULL,NULL,"no");
INSERT INTO `student` VALUES("50","999999999999","1f65f5499de8205ef287e1ffa5169320","hershiesoscano04@gmail.com","09208040444","Male",NULL,"Francis","Cruz","",NULL,"","active","0",NULL,"0","2024-12-04 01:16:52","2024-12-12 06:59:41",NULL,NULL,NULL,"yes");
INSERT INTO `student` VALUES("109","123456789012","d470bfce3e6db20a2fa4d6c4b6b8bf9e","juan.delacruz@example.com","9123456789","Male",NULL,"Juan","Dela Cruz","Santos",NULL,"","active","0",NULL,"0","2024-12-11 17:25:55","2024-12-11 17:29:49",NULL,NULL,NULL,"no");
INSERT INTO `student` VALUES("211","123456789013","5955c1d7fafac1a87b2e22e33503d197","maria.santos@example.com","09987654321","Female",NULL,"Maria","Santos","Garcia",NULL,"","active","0",NULL,"0","2024-12-11 17:48:04","2024-12-11 17:48:04",NULL,NULL,NULL,"no");
INSERT INTO `student` VALUES("223","402176160002","7e73346b1abddfd252afe88c046c2e98","JANGELES@gmail.com","09123456789","Male",NULL,"JENINO ABRIEL","ANGELES","CAGUIAT",NULL,"","active","0",NULL,"0","2024-12-12 01:48:22","2024-12-12 01:48:22",NULL,NULL,NULL,"no");
INSERT INTO `student` VALUES("224","107984170001","c65b12a3c86d360244ea62b61d4bd4fe","DAREVALO@gmail.com","09987654321","Male",NULL,"DJHON DANIEL","AREVALO","MALIHAN",NULL,"","active","0",NULL,"0","2024-12-12 01:48:22","2024-12-12 01:48:22",NULL,NULL,NULL,"no");
INSERT INTO `student` VALUES("225","107973170036","927cf829eb2dd7fc2e7f66280c6dad12","IBERNARDO@gmail.com","09640479631","Male",NULL,"ILDRED DOMINIC","BERNARDO","SAULOG",NULL,"","active","0",NULL,"0","2024-12-12 01:48:22","2024-12-12 01:48:22",NULL,NULL,NULL,"no");
INSERT INTO `student` VALUES("226","136524170671","e19db788c21416f452ea4978c8ba7817","JBULAWIN@gmail.com","09275742104","Male",NULL,"JAN KYLE","BULAWIN","MANANGAT",NULL,"","active","0",NULL,"0","2024-12-12 01:48:22","2024-12-12 01:48:22",NULL,NULL,NULL,"no");
INSERT INTO `student` VALUES("227","107973170001","de7bdd1f7ab593acf12ca0cdd920610a","JCANDELARIA@gmail.com","09968716364","Male",NULL,"JOHN ERNEST LOUIE","CANDELARIA","FESALBON",NULL,"","active","0",NULL,"0","2024-12-12 01:48:22","2024-12-12 02:09:11",NULL,NULL,NULL,"no");
INSERT INTO `student` VALUES("228","136766170015","be5db79548a18da5c582e5484f7ae06b","GCONCILLADO@gmail.com","09569986080","Male",NULL,"GIAN CARLO","CONCILLADO","MORTEGA",NULL,"","active","0",NULL,"0","2024-12-12 01:48:22","2024-12-12 01:48:22",NULL,NULL,NULL,"no");
INSERT INTO `student` VALUES("229","107985170027","016421190e279cebbcbe1b3972ee50a7","FCRUZ@gmail.com","09934842631","Male","2011-11-20","FRANZ MATEO","CRUZ","ALICAWAY",NULL,"","active","0",NULL,"1","2024-12-12 01:48:22","2024-12-12 10:35:45","20241212103545","u4orvrnfogmag1chctrfh58udi",NULL,"no");
INSERT INTO `student` VALUES("230","164009170016","c351ba26e18ff94be1afb717a0466f1b","VDE BELEN@gmail.com","09280500027","Male",NULL,"VON LAWRENCE","DE BELEN","CANO",NULL,"","active","0",NULL,"0","2024-12-12 01:48:22","2024-12-12 01:48:22",NULL,NULL,NULL,"no");
INSERT INTO `student` VALUES("231","107974170319","05cf50e11e8ed486b02b0e338c3cf3a8","JDEL MUNDO@gmail.com","09503134452","Male",NULL,"JOSH MATTHEW","DEL MUNDO","BOLACTIA",NULL,"","active","0",NULL,"0","2024-12-12 01:48:22","2024-12-12 01:48:22",NULL,NULL,NULL,"no");
INSERT INTO `student` VALUES("232","107973170039","97c4e885c5cf91f7057bd4c92776ca40","MENRIQUEZ@gmail.com","09320836740","Male",NULL,"MARWIN JAY","ENRIQUEZ","AGUILAR",NULL,"","active","0",NULL,"0","2024-12-12 01:48:22","2024-12-12 01:48:22",NULL,NULL,NULL,"no");
INSERT INTO `student` VALUES("233","107985170246","9770110dbd30ef42625c4e598869d834","EFEGASON@gmail.com","09273458901","Male",NULL,"EARL MATTHEW","FEGASON","PEREZ",NULL,"","active","0",NULL,"0","2024-12-12 01:48:22","2024-12-12 01:48:22",NULL,NULL,NULL,"no");
INSERT INTO `student` VALUES("234","107985170029","1977d2009700439b36fcf22bd114b53b","WLAUZON@gmail.com","09171234567","Male",NULL,"WENCY","LAUZON","AYADE",NULL,"","active","0",NULL,"0","2024-12-12 01:48:22","2024-12-12 01:48:22",NULL,NULL,NULL,"no");
INSERT INTO `student` VALUES("235","107985170031","b12ede3f70725abdaa975cb32727f2cd","JMONTABON@gmail.com","09229876543","Male",NULL,"JHOVIN IVAN","MONTABON","CLARIN",NULL,"","active","0",NULL,"0","2024-12-12 01:48:22","2024-12-12 01:48:22",NULL,NULL,NULL,"no");
INSERT INTO `student` VALUES("236","107985170158","cfc572320132d8be943f472eec6b420d","ROIDEM@gmail.com","09351112222","Male",NULL,"RICHARD","OIDEM","HABITAN",NULL,"","active","0",NULL,"0","2024-12-12 01:48:22","2024-12-12 01:48:22",NULL,NULL,NULL,"no");
INSERT INTO `student` VALUES("237","107985170014","7d3f8c2ae946606b0af4fb9e90b26161","JPARAYNO@gmail.com","09087654321","Male",NULL,"JOHN CLARK","PARAYNO","MELENDREZ",NULL,"","active","0",NULL,"0","2024-12-12 01:48:22","2024-12-12 01:48:22",NULL,NULL,NULL,"no");
INSERT INTO `student` VALUES("238","107973170008","de1e4502a0ddc4e091b214846c135e39","BRENO@gmail.com","09198765432","Male",NULL,"BIEN GABRIEL","RENO","AMORES",NULL,"","active","0",NULL,"0","2024-12-12 01:48:22","2024-12-12 01:48:22",NULL,NULL,NULL,"no");
INSERT INTO `student` VALUES("239","107979170103","0233eff5864861b54ec1f2f642aaddcc","PSALINAS@gmail.com","09212345678","Male",NULL,"PRIO LAURENCE","SALINAS","CAMPAÑA",NULL,"","active","0",NULL,"0","2024-12-12 01:48:22","2024-12-12 01:48:22",NULL,NULL,NULL,"no");
INSERT INTO `student` VALUES("240","107985170127","61ef1ae560939307ef0d79de378220c3","VSANTOS@gmail.com","09323456789","Male",NULL,"VILFRED IRVHING NATHEAL","SANTOS","BELLEZA",NULL,"","active","0",NULL,"0","2024-12-12 01:48:22","2024-12-12 01:48:22",NULL,NULL,NULL,"no");
INSERT INTO `student` VALUES("241","107985170179","85b05f16e107b7080c6939cae89d9ac6","JSECUSANA@gmail.com","09091234567","Male",NULL,"JOHN GABRIEL","SECUSANA","BESA",NULL,"","active","0",NULL,"0","2024-12-12 01:48:22","2024-12-12 01:48:22",NULL,NULL,NULL,"no");
INSERT INTO `student` VALUES("242","107973170045","a214e6c83808ee6bf4f989a1741eeba7","NTUBIO@gmail.com","09189876543","Male",NULL,"NINO JESTER","TUBIO","PAIRA",NULL,"","active","0",NULL,"0","2024-12-12 01:48:22","2024-12-12 01:48:22",NULL,NULL,NULL,"no");
INSERT INTO `student` VALUES("243","107985170137","fae8267b3adbcd5f164039f9077ab797","KVELUNTA@gmail.com","09231112222","Male",NULL,"KHYLLE KHOSHINE","VELUNTA","DICEN",NULL,"","active","0",NULL,"0","2024-12-12 01:48:22","2024-12-12 01:48:22",NULL,NULL,NULL,"no");
INSERT INTO `student` VALUES("244","424289170007","8db5bafa4d5dbc4539a3b11ee7bf85c8","JALARCON@gmail.com","09367654321","Female",NULL,"JEREMIAH EZRA","ALARCON","",NULL,"","active","0",NULL,"0","2024-12-12 01:48:22","2024-12-12 01:48:22",NULL,NULL,NULL,"no");
INSERT INTO `student` VALUES("245","107985170139","cd1b657d2fbee2bab911b506ab1c6b85","LALBERTO@gmail.com","09078765432","Female",NULL,"LORAINE","ALBERTO","CUBACUB",NULL,"","active","0",NULL,"0","2024-12-12 01:48:22","2024-12-12 01:48:22",NULL,NULL,NULL,"no");
INSERT INTO `student` VALUES("246","107974170390","e963aacd4c9824c03455378bbe6b398e","PALCANTARA@gmail.com","09152345678","Female",NULL,"PEARL KRISTINE","ALCANTARA","SOLIMAN",NULL,"","active","0",NULL,"0","2024-12-12 01:48:22","2024-12-12 01:48:22",NULL,NULL,NULL,"no");
INSERT INTO `student` VALUES("247","107985170074","786879e88bf8596614a58f05b67dd4f0","MALVAREZ@gmail.com","09243456789","Female",NULL,"MARY LORIELYN JOY","ALVAREZ","GOMEZ",NULL,"","active","0",NULL,"0","2024-12-12 01:48:22","2024-12-12 01:48:22",NULL,NULL,NULL,"no");
INSERT INTO `student` VALUES("248","107974170391","9ccb9d0f0c996dd27b8353541b3e7f1e","AARCA@gmail.com","09311234567","Female",NULL,"ALYANNA KHANE","ARCA","LAURENTE",NULL,"","active","0",NULL,"0","2024-12-12 01:48:22","2024-12-12 01:48:22",NULL,NULL,NULL,"no");
INSERT INTO `student` VALUES("249","402112150584","9ffdf5a76db50cd36881ef2a82fe406a","LBERNABAT@gmail.com","09069876543","Female",NULL,"LEXI ALEXANDRA","BERNABAT","ALBITE",NULL,"","active","0",NULL,"0","2024-12-12 01:48:22","2024-12-12 01:48:22",NULL,NULL,NULL,"no");
INSERT INTO `student` VALUES("250","107990170023","e91316fa594fee1f949be7c011c9ed6a","DBONIFACIO@gmail.com","09161112222","Female",NULL,"DHENRIE ROSE","BONIFACIO","ESPARES",NULL,"","active","0",NULL,"0","2024-12-12 01:48:22","2024-12-12 01:48:22",NULL,NULL,NULL,"no");
INSERT INTO `student` VALUES("251","424497170005","0fa0d5ca2d73f1c8bf603db98315d261","SBUSA@gmail.com","09257654321","Female",NULL,"SEF MATTHEA","BUSA","ESTRADA",NULL,"","active","0",NULL,"0","2024-12-12 01:48:22","2024-12-12 01:48:22",NULL,NULL,NULL,"no");
INSERT INTO `student` VALUES("252","107974170187","9b42bcbe7349f426a571577c2eb3629c","PCAGUIAT@gmail.com","09378765432","Female",NULL,"PRINCESS EUGENE","CAGUIAT","QUIROZ",NULL,"","active","0",NULL,"0","2024-12-12 01:48:22","2024-12-12 01:48:22",NULL,NULL,NULL,"no");
INSERT INTO `student` VALUES("253","107990170058","b665a797cb6c7d82d0ff61ce81494560","CCATALAN@gmail.com","09052345678","Female",NULL,"CYRENE MAE","CATALAN","BASO",NULL,"","active","0",NULL,"0","2024-12-12 01:48:22","2024-12-12 01:48:22",NULL,NULL,NULL,"no");
INSERT INTO `student` VALUES("254","424353170020","7fe773d6b78f6ce563147a8b564b195f","MDELOS REYES@gmail.com","09143456789","Female",NULL,"MIA ANGELA ENNA","DELOS REYES","CAMAMA",NULL,"","active","0",NULL,"0","2024-12-12 01:48:22","2024-12-12 01:48:22",NULL,NULL,NULL,"no");
INSERT INTO `student` VALUES("255","107973170018","afab64cb00d437aa6e4f23492d0abde0","ADIALA@gmail.com","09261234567","Female",NULL,"ALYANNA JENICA","DIALA","BARBA",NULL,"","active","0",NULL,"0","2024-12-12 01:48:22","2024-12-12 01:48:22",NULL,NULL,NULL,"no");
INSERT INTO `student` VALUES("256","107974170089","0ca7bfc04aca4d2d3200928b68eadb92","BFERMA@gmail.com","09339876543","Female",NULL,"BREANNE","FERMA","PIMENTEL",NULL,"","active","0",NULL,"0","2024-12-12 01:48:22","2024-12-12 01:48:22",NULL,NULL,NULL,"no");
INSERT INTO `student` VALUES("257","136711170092","9e969ca5d94ebfc9c74d89ba8b735e8b","KGALUPO@gmail.com","09041112222","Female",NULL,"KYLE CHLOE","GALUPO","SANCHEZ",NULL,"","active","0",NULL,"0","2024-12-12 01:48:22","2024-12-12 01:48:22",NULL,NULL,NULL,"no");
INSERT INTO `student` VALUES("258","107974170437","e51d459f476d84464415db320e08f7bd","AGARCIA@gmail.com","09137654321","Female",NULL,"ATHENA KEIGHT","GARCIA","LABIAN",NULL,"","active","0",NULL,"0","2024-12-12 01:48:22","2024-12-12 01:48:22",NULL,NULL,NULL,"no");
INSERT INTO `student` VALUES("259","107985170088","385adbfbfdbfe69f17e35b3efff59f4a","JLEGASPI@gmail.com","09278765432","Female",NULL,"JOSELYN","LEGASPI","LEYSON",NULL,"","active","0",NULL,"0","2024-12-12 01:48:22","2024-12-12 01:48:22",NULL,NULL,NULL,"no");
INSERT INTO `student` VALUES("260","107973170021","ffdf497b39c9cbe6b9b19721e8fbe71b","LMARTINEZ@gmail.com","09382345678","Female",NULL,"LOUISE JANA","MARTINEZ","MONZON",NULL,"","active","0",NULL,"0","2024-12-12 01:48:22","2024-12-12 01:48:22",NULL,NULL,NULL,"no");
INSERT INTO `student` VALUES("261","136791170146","993900963c860e31c644c9277f95821e","GMERENCILLO@gmail.com","09033456789","Female",NULL,"GAILE RHIAN","MERENCILLO","OLARTE",NULL,"","active","0",NULL,"0","2024-12-12 01:48:22","2024-12-12 01:48:22",NULL,NULL,NULL,"no");
INSERT INTO `student` VALUES("262","422016160014","de9fe20d9295c266994796d773082e5a","JRAYMUNDO@gmail.com","09121234567","Female",NULL,"JIANNE MEI","RAYMUNDO","",NULL,"","active","0",NULL,"0","2024-12-12 01:48:22","2024-12-12 01:48:22",NULL,NULL,NULL,"no");
INSERT INTO `student` VALUES("263","107973170070","79e92621c3ecb3a791e5fdd4ae72eba4","JREYEL@gmail.com","09289876543","Female",NULL,"JIRSTEN MARNEE","REYEL","POBLETE",NULL,"","active","0",NULL,"0","2024-12-12 01:48:22","2024-12-12 01:48:22",NULL,NULL,NULL,"no");
INSERT INTO `student` VALUES("264","107973170071","de96d397afae9bd4b438a6e35f1147e2","JROA@gmail.com","09341112222","Female",NULL,"JAMAILAH CHLOE","ROA","CAMAMA",NULL,"","active","0",NULL,"0","2024-12-12 01:48:22","2024-12-12 01:48:22",NULL,NULL,NULL,"no");
INSERT INTO `student` VALUES("265","107985170045","45c267feffca48f73fc93ad0fe91ed63","MSORIANO@gmail.com","09027654321","Female",NULL,"MARY HEAVEN","SORIANO","ESGUERRA",NULL,"","active","0",NULL,"0","2024-12-12 01:48:22","2024-12-12 01:48:22",NULL,NULL,NULL,"no");
INSERT INTO `student` VALUES("266","107985170132","d104906a2055579049528d56127f2aa3","SVILLARANDA@gmail.com","09118765432","Female",NULL,"SHIELLA MAY","VILLARANDA","LEONORA",NULL,"","active","0",NULL,"0","2024-12-12 01:48:22","2024-12-12 01:48:22",NULL,NULL,NULL,"no");
INSERT INTO `student` VALUES("267","107985170178","41833c4c1356ab6f6bac31837b3f9113","MVOCIS@gmail.com","09292345678","Female",NULL,"MERILYN","VOCIS","CASES",NULL,"","active","0",NULL,"0","2024-12-12 01:48:22","2024-12-12 01:48:22",NULL,NULL,NULL,"no");
INSERT INTO `student` VALUES("269","000000000000","fbab90bd29fa5e4c38f0ef5a3a650c0e","kingpacifico009@gmail.com","09208040444","Male",NULL,"king","king","king",NULL,"","active","0",NULL,"0","2024-12-12 07:00:47","2024-12-12 10:13:41",NULL,NULL,NULL,"yes");


-- Table structure for table `student_activity_submissions`
DROP TABLE IF EXISTS `student_activity_submissions`;
CREATE TABLE `student_activity_submissions` (
  `submission_id` int(11) NOT NULL AUTO_INCREMENT,
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
  PRIMARY KEY (`submission_id`),
  KEY `student_id` (`student_id`),
  KEY `idx_activity_submitted` (`activity_id`,`submitted_at`),
  KEY `idx_activity_submissions` (`activity_id`,`submitted_at`),
  KEY `fk_graded_by_teacher` (`graded_by`),
  CONSTRAINT `fk_graded_by_teacher` FOREIGN KEY (`graded_by`) REFERENCES `teacher` (`teacher_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `student_activity_submissions_ibfk_2` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`activity_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=181 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `student_activity_submissions`
INSERT INTO `student_activity_submissions` VALUES("85","50","125","0",NULL,NULL,"2024-12-06 05:24:49","submitted",NULL,NULL,"0",NULL,"2024-12-06 05:24:49","2024-12-06 05:24:49","2024-12-06 05:24:49","0",NULL,"2",NULL,"0.00","0","0","0",NULL);
INSERT INTO `student_activity_submissions` VALUES("100","50","131","2",NULL,NULL,"2024-12-06 06:12:37","submitted",NULL,NULL,"0",NULL,"2024-12-06 06:12:37","2024-12-06 06:12:37","2024-12-06 06:12:37","0",NULL,"3",NULL,"0.00","0","0","0",NULL);
INSERT INTO `student_activity_submissions` VALUES("101","50","135","3",NULL,NULL,"2024-12-06 06:12:52","submitted",NULL,NULL,"0",NULL,"2024-12-06 06:12:52","2024-12-06 06:12:52","2024-12-06 06:12:52","0",NULL,"2",NULL,"0.00","0","0","0",NULL);
INSERT INTO `student_activity_submissions` VALUES("104","50","137","1",NULL,NULL,"2024-12-06 15:57:53","submitted",NULL,NULL,"0",NULL,"2024-12-06 15:57:53","2024-12-06 15:57:53","2024-12-06 15:57:53","0",NULL,"4",NULL,"0.00","0","0","0",NULL);
INSERT INTO `student_activity_submissions` VALUES("111","50","138","3",NULL,NULL,"2024-12-06 17:26:46","submitted",NULL,NULL,"0",NULL,"2024-12-06 17:26:46","2024-12-06 17:26:46","2024-12-06 17:26:46","0",NULL,"3",NULL,"0.00","0","0","0",NULL);
INSERT INTO `student_activity_submissions` VALUES("125","50","139","3",NULL,NULL,"2024-12-06 18:34:59","submitted",NULL,NULL,"0",NULL,"2024-12-06 18:34:59","2024-12-06 18:34:59","2024-12-06 18:34:59","0",NULL,"3",NULL,"0.00","0","0","0",NULL);
INSERT INTO `student_activity_submissions` VALUES("148","50","141","4",NULL,NULL,"2024-12-07 00:04:12","submitted",NULL,NULL,"0",NULL,"2024-12-07 00:04:12","2024-12-07 00:04:12","2024-12-07 00:04:12","0",NULL,"4",NULL,"0.00","0","0","4","4");
INSERT INTO `student_activity_submissions` VALUES("149","50","140","6",NULL,NULL,"2024-12-07 00:06:14","submitted",NULL,NULL,"0",NULL,"2024-12-07 00:06:14","2024-12-07 00:06:14","2024-12-07 00:06:14","0",NULL,"10",NULL,"0.00","0","0","6","6");
INSERT INTO `student_activity_submissions` VALUES("157","50","142","0",NULL,NULL,"2024-12-07 00:49:06","missing",NULL,NULL,"0",NULL,"2024-12-07 00:49:06","2024-12-07 00:49:06","2024-12-07 00:49:06","1",NULL,"13",NULL,"0.00","0","0","0","5");
INSERT INTO `student_activity_submissions` VALUES("158","50","94","99","",NULL,"2024-12-07 00:54:34","submitted","2024-12-07 00:58:34",NULL,"0",NULL,"2024-12-07 00:54:34","2024-12-07 00:54:34","2024-12-07 00:58:34","0",NULL,NULL,NULL,"0.00","0","0","0",NULL);
INSERT INTO `student_activity_submissions` VALUES("159","50","143","88","tes",NULL,"2024-12-07 01:03:05","submitted","2024-12-07 01:03:19",NULL,"0",NULL,"2024-12-07 01:03:05","2024-12-07 01:03:05","2024-12-07 01:03:19","0",NULL,NULL,NULL,"0.00","0","0","0",NULL);
INSERT INTO `student_activity_submissions` VALUES("165","50","145","89","",NULL,"2024-12-07 01:18:38","submitted","2024-12-07 01:22:54",NULL,"0",NULL,"2024-12-07 01:18:38","2024-12-07 01:18:38","2024-12-07 01:22:54","0",NULL,NULL,NULL,"0.00","0","0","0",NULL);
INSERT INTO `student_activity_submissions` VALUES("166","50","93",NULL,NULL,NULL,"2024-12-07 01:25:16","submitted",NULL,NULL,"0",NULL,"2024-12-07 01:25:16","2024-12-07 01:25:16","2024-12-07 01:25:16","0",NULL,NULL,NULL,"0.00","0","0","0",NULL);
INSERT INTO `student_activity_submissions` VALUES("169","50","147","0",NULL,NULL,"2024-12-09 01:51:19","submitted",NULL,NULL,"0",NULL,"2024-12-09 01:51:19","2024-12-09 01:51:19","2024-12-09 01:51:19","0",NULL,"17",NULL,"0.00","0","0","0","3");
INSERT INTO `student_activity_submissions` VALUES("172","50","146","3",NULL,NULL,"2024-12-09 02:12:21","submitted",NULL,NULL,"0",NULL,"2024-12-09 02:12:21","2024-12-09 02:12:21","2024-12-09 02:12:21","0",NULL,"14",NULL,"0.00","0","0","3","7");
INSERT INTO `student_activity_submissions` VALUES("173","50","148","3",NULL,NULL,"2024-12-09 02:12:44","submitted",NULL,NULL,"0",NULL,"2024-12-09 02:12:44","2024-12-09 02:12:44","2024-12-09 02:12:44","0",NULL,"18",NULL,"0.00","0","0","2","3");
INSERT INTO `student_activity_submissions` VALUES("174","50","149","0",NULL,NULL,"2024-12-09 02:13:31","submitted",NULL,NULL,"0",NULL,"2024-12-09 02:13:31","2024-12-09 02:13:31","2024-12-09 02:13:31","0",NULL,"13",NULL,"0.00","0","0","0","3");
INSERT INTO `student_activity_submissions` VALUES("175","50","150","3",NULL,NULL,"2024-12-09 11:15:45","submitted",NULL,NULL,"0",NULL,"2024-12-09 11:15:45","2024-12-09 11:15:45","2024-12-09 11:15:45","0",NULL,"29",NULL,"0.00","0","0","3","3");
INSERT INTO `student_activity_submissions` VALUES("176","50","151","3",NULL,NULL,"2024-12-09 12:59:53","submitted",NULL,NULL,"0",NULL,"2024-12-09 12:59:53","2024-12-09 12:59:53","2024-12-09 12:59:53","0",NULL,"18",NULL,"0.00","0","0","3","4");
INSERT INTO `student_activity_submissions` VALUES("180","269","157","2",NULL,NULL,"2024-12-12 07:02:57","submitted",NULL,NULL,"0",NULL,"2024-12-12 07:02:57","2024-12-12 07:02:57","2024-12-12 07:02:57","0",NULL,"36",NULL,"0.00","0","0","2","3");


-- Table structure for table `student_answers`
DROP TABLE IF EXISTS `student_answers`;
CREATE TABLE `student_answers` (
  `answer_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `selected_choice_id` int(11) DEFAULT NULL,
  `text_answer` text DEFAULT NULL,
  `is_correct` tinyint(1) DEFAULT 0,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`answer_id`),
  KEY `student_id` (`student_id`),
  KEY `quiz_id` (`quiz_id`),
  KEY `question_id` (`question_id`),
  KEY `selected_choice_id` (`selected_choice_id`),
  KEY `idx_student_quiz` (`student_id`,`quiz_id`),
  KEY `idx_student_quiz_answers` (`student_id`,`quiz_id`,`question_id`),
  CONSTRAINT `fk_student_answers_choice` FOREIGN KEY (`selected_choice_id`) REFERENCES `question_choices` (`choice_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_student_answers_question` FOREIGN KEY (`question_id`) REFERENCES `quiz_questions` (`question_id`),
  CONSTRAINT `fk_student_answers_quiz` FOREIGN KEY (`quiz_id`) REFERENCES `activities` (`activity_id`),
  CONSTRAINT `fk_student_answers_student` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`)
) ENGINE=InnoDB AUTO_INCREMENT=263 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `student_answers`
INSERT INTO `student_answers` VALUES("27","50","138","91","168","","1","2024-12-06 17:26:46");
INSERT INTO `student_answers` VALUES("28","50","138","92",NULL,"","0","2024-12-06 17:26:46");
INSERT INTO `student_answers` VALUES("29","50","138","93",NULL,"0","1","2024-12-06 17:26:46");
INSERT INTO `student_answers` VALUES("30","50","138","94","172","","1","2024-12-06 17:26:46");
INSERT INTO `student_answers` VALUES("73","50","139","95","174","","1","2024-12-06 18:34:59");
INSERT INTO `student_answers` VALUES("74","50","139","96","176","","1","2024-12-06 18:34:59");
INSERT INTO `student_answers` VALUES("75","50","139","97",NULL,"0","1","2024-12-06 18:34:59");
INSERT INTO `student_answers` VALUES("171","50","141","104","186","0","1","2024-12-07 00:04:12");
INSERT INTO `student_answers` VALUES("172","50","141","105","188","0","1","2024-12-07 00:04:12");
INSERT INTO `student_answers` VALUES("173","50","141","106",NULL,"0","1","2024-12-07 00:04:12");
INSERT INTO `student_answers` VALUES("174","50","141","107","190","0","1","2024-12-07 00:04:12");
INSERT INTO `student_answers` VALUES("175","50","140","98","178","0","1","2024-12-07 00:06:14");
INSERT INTO `student_answers` VALUES("176","50","140","99","180","0","1","2024-12-07 00:06:14");
INSERT INTO `student_answers` VALUES("177","50","140","100",NULL,"0","1","2024-12-07 00:06:14");
INSERT INTO `student_answers` VALUES("178","50","140","101","182","0","1","2024-12-07 00:06:14");
INSERT INTO `student_answers` VALUES("179","50","140","102","184","0","1","2024-12-07 00:06:14");
INSERT INTO `student_answers` VALUES("180","50","140","103",NULL,"0","1","2024-12-07 00:06:14");
INSERT INTO `student_answers` VALUES("216","50","142","108",NULL,"0","0","2024-12-07 00:49:06");
INSERT INTO `student_answers` VALUES("217","50","142","109",NULL,"0","0","2024-12-07 00:49:06");
INSERT INTO `student_answers` VALUES("218","50","142","110",NULL,"0","0","2024-12-07 00:49:06");
INSERT INTO `student_answers` VALUES("219","50","142","111",NULL,"0","0","2024-12-07 00:49:06");
INSERT INTO `student_answers` VALUES("220","50","142","112",NULL,"0","0","2024-12-07 00:49:06");
INSERT INTO `student_answers` VALUES("231","50","147","120","211","","0","2024-12-09 01:51:19");
INSERT INTO `student_answers` VALUES("232","50","147","121","214","","0","2024-12-09 01:51:19");
INSERT INTO `student_answers` VALUES("233","50","147","122",NULL,"a","0","2024-12-09 01:51:19");
INSERT INTO `student_answers` VALUES("240","50","146","113","198","","0","2024-12-09 02:12:21");
INSERT INTO `student_answers` VALUES("241","50","146","114","202","","1","2024-12-09 02:12:21");
INSERT INTO `student_answers` VALUES("242","50","146","115",NULL,"asd","1","2024-12-09 02:12:21");
INSERT INTO `student_answers` VALUES("243","50","146","116",NULL,"asd","0","2024-12-09 02:12:21");
INSERT INTO `student_answers` VALUES("244","50","146","117",NULL,"sad","0","2024-12-09 02:12:21");
INSERT INTO `student_answers` VALUES("245","50","146","118","204","","1","2024-12-09 02:12:21");
INSERT INTO `student_answers` VALUES("246","50","146","119","208","","0","2024-12-09 02:12:21");
INSERT INTO `student_answers` VALUES("247","50","148","123","216","","1","2024-12-09 02:12:44");
INSERT INTO `student_answers` VALUES("248","50","148","124","220","","1","2024-12-09 02:12:44");
INSERT INTO `student_answers` VALUES("249","50","148","125",NULL,"asdasd","0","2024-12-09 02:12:44");
INSERT INTO `student_answers` VALUES("250","50","149","126","222","","0","2024-12-09 02:13:31");
INSERT INTO `student_answers` VALUES("251","50","149","127","226","","0","2024-12-09 02:13:31");
INSERT INTO `student_answers` VALUES("252","50","149","128",NULL,"111","0","2024-12-09 02:13:31");
INSERT INTO `student_answers` VALUES("253","50","150","129","228","","1","2024-12-09 11:15:45");
INSERT INTO `student_answers` VALUES("254","50","150","130","231","","1","2024-12-09 11:15:45");
INSERT INTO `student_answers` VALUES("255","50","150","131",NULL,"OO","1","2024-12-09 11:15:45");
INSERT INTO `student_answers` VALUES("256","50","151","132","234","","1","2024-12-09 12:59:53");
INSERT INTO `student_answers` VALUES("257","50","151","133","237","","1","2024-12-09 12:59:53");
INSERT INTO `student_answers` VALUES("258","50","151","134",NULL,"4","1","2024-12-09 12:59:53");
INSERT INTO `student_answers` VALUES("259","50","151","135",NULL,"testdawd","0","2024-12-09 12:59:53");
INSERT INTO `student_answers` VALUES("260","269","157","137",NULL,"hi","0","2024-12-12 07:02:57");
INSERT INTO `student_answers` VALUES("261","269","157","138","247","","1","2024-12-12 07:02:57");
INSERT INTO `student_answers` VALUES("262","269","157","139","249","","1","2024-12-12 07:02:57");


-- Table structure for table `student_answers_old`
DROP TABLE IF EXISTS `student_answers_old`;
CREATE TABLE `student_answers_old` (
  `answer_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `selected_choice_id` int(11) DEFAULT NULL,
  `text_answer` text DEFAULT NULL,
  `is_correct` tinyint(1) DEFAULT 0,
  `points_earned` decimal(5,2) DEFAULT 0.00,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`answer_id`),
  KEY `quiz_id` (`quiz_id`),
  KEY `question_id` (`question_id`),
  KEY `selected_choice_id` (`selected_choice_id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



-- Table structure for table `student_backup`
DROP TABLE IF EXISTS `student_backup`;
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



-- Table structure for table `student_grades`
DROP TABLE IF EXISTS `student_grades`;
CREATE TABLE `student_grades` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `section_subject_id` int(11) NOT NULL,
  `final_grade` decimal(5,2) NOT NULL,
  `calculated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_student_subject` (`student_id`,`section_subject_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



-- Table structure for table `student_login_logs`
DROP TABLE IF EXISTS `student_login_logs`;
CREATE TABLE `student_login_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `status` enum('success','failed','logout') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `student_id` (`student_id`)
) ENGINE=InnoDB AUTO_INCREMENT=206 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `student_login_logs`
INSERT INTO `student_login_logs` VALUES("80","2147483647","::1","success","2024-11-23 15:36:27");
INSERT INTO `student_login_logs` VALUES("158","50","::1","success","2024-12-04 01:17:24");
INSERT INTO `student_login_logs` VALUES("159","50","::1","success","2024-12-06 03:40:28");
INSERT INTO `student_login_logs` VALUES("160","50","::1","success","2024-12-06 05:15:12");
INSERT INTO `student_login_logs` VALUES("161","50","180.194.233.34","success","2024-12-09 01:38:23");
INSERT INTO `student_login_logs` VALUES("162","50","49.144.12.254","success","2024-12-09 01:42:45");
INSERT INTO `student_login_logs` VALUES("163","50","49.144.12.254","logout","2024-12-09 01:46:14");
INSERT INTO `student_login_logs` VALUES("164","50","49.144.12.254","success","2024-12-09 01:50:55");
INSERT INTO `student_login_logs` VALUES("165","50","180.194.233.34","success","2024-12-09 02:03:55");
INSERT INTO `student_login_logs` VALUES("166","50","175.176.36.21","success","2024-12-09 11:13:01");
INSERT INTO `student_login_logs` VALUES("167","50","175.176.36.21","logout","2024-12-09 11:17:29");
INSERT INTO `student_login_logs` VALUES("168","50","175.176.36.21","success","2024-12-09 11:17:42");
INSERT INTO `student_login_logs` VALUES("169","50","175.176.36.21","logout","2024-12-09 11:18:10");
INSERT INTO `student_login_logs` VALUES("170","50","136.158.49.29","success","2024-12-09 12:17:25");
INSERT INTO `student_login_logs` VALUES("171","50","175.176.36.21","success","2024-12-09 12:58:20");
INSERT INTO `student_login_logs` VALUES("172","50","136.158.49.29","success","2024-12-09 15:43:47");
INSERT INTO `student_login_logs` VALUES("173","50","136.158.49.29","success","2024-12-09 16:36:37");
INSERT INTO `student_login_logs` VALUES("174","50","::1","success","2024-12-10 19:31:47");
INSERT INTO `student_login_logs` VALUES("175","50","::1","success","2024-12-10 19:33:30");
INSERT INTO `student_login_logs` VALUES("176","50","::1","success","2024-12-10 19:34:37");
INSERT INTO `student_login_logs` VALUES("177","50","::1","success","2024-12-10 19:35:07");
INSERT INTO `student_login_logs` VALUES("178","50","::1","success","2024-12-10 19:36:43");
INSERT INTO `student_login_logs` VALUES("179","50","::1","success","2024-12-10 19:38:22");
INSERT INTO `student_login_logs` VALUES("180","50","::1","success","2024-12-10 19:40:20");
INSERT INTO `student_login_logs` VALUES("181","50","::1","success","2024-12-10 19:44:35");
INSERT INTO `student_login_logs` VALUES("182","50","::1","success","2024-12-10 19:54:25");
INSERT INTO `student_login_logs` VALUES("183","50","::1","success","2024-12-10 19:55:59");
INSERT INTO `student_login_logs` VALUES("184","50","::1","logout","2024-12-10 19:56:03");
INSERT INTO `student_login_logs` VALUES("185","50","::1","success","2024-12-10 19:59:12");
INSERT INTO `student_login_logs` VALUES("186","50","::1","success","2024-12-10 20:02:25");
INSERT INTO `student_login_logs` VALUES("187","50","::1","success","2024-12-10 20:04:21");
INSERT INTO `student_login_logs` VALUES("188","50","::1","success","2024-12-10 20:13:10");
INSERT INTO `student_login_logs` VALUES("189","50","::1","success","2024-12-10 20:18:24");
INSERT INTO `student_login_logs` VALUES("190","50","::1","success","2024-12-10 20:24:16");
INSERT INTO `student_login_logs` VALUES("192","50","::1","success","2024-12-10 21:44:10");
INSERT INTO `student_login_logs` VALUES("193","50","::1","success","2024-12-10 21:50:18");
INSERT INTO `student_login_logs` VALUES("194","50","::1","success","2024-12-11 00:05:33");
INSERT INTO `student_login_logs` VALUES("195","50","::1","success","2024-12-11 00:17:29");
INSERT INTO `student_login_logs` VALUES("196","50","::1","success","2024-12-11 01:44:32");
INSERT INTO `student_login_logs` VALUES("197","109","180.194.233.34","success","2024-12-11 17:29:44");
INSERT INTO `student_login_logs` VALUES("201","227","180.194.233.34","success","2024-12-12 01:49:43");
INSERT INTO `student_login_logs` VALUES("202","227","180.194.233.34","success","2024-12-12 01:51:52");
INSERT INTO `student_login_logs` VALUES("203","269","58.69.144.207","success","2024-12-12 07:01:56");
INSERT INTO `student_login_logs` VALUES("204","229","180.194.233.34","success","2024-12-12 10:32:26");
INSERT INTO `student_login_logs` VALUES("205","229","180.194.233.34","success","2024-12-12 10:34:13");


-- Table structure for table `student_sections`
DROP TABLE IF EXISTS `student_sections`;
CREATE TABLE `student_sections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `academic_year_id` int(11) NOT NULL,
  `school_year` varchar(9) NOT NULL,
  `status` enum('active','transferred','graduated','inactive') DEFAULT 'active',
  `enrolled_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_student_year` (`student_id`,`school_year`),
  KEY `section_id` (`section_id`),
  KEY `academic_year_id` (`academic_year_id`),
  CONSTRAINT `student_sections_ibfk_2` FOREIGN KEY (`section_id`) REFERENCES `sections` (`section_id`) ON DELETE CASCADE,
  CONSTRAINT `student_sections_ibfk_3` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `student_sections`
INSERT INTO `student_sections` VALUES("73","50","20","1","","active","2024-12-09 17:28:02","2024-12-09 17:28:02");
INSERT INTO `student_sections` VALUES("76","269","20","1","","active","2024-12-12 07:02:08","2024-12-12 07:02:08");


-- Table structure for table `subject_grade_levels`
DROP TABLE IF EXISTS `subject_grade_levels`;
CREATE TABLE `subject_grade_levels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_id` int(11) NOT NULL,
  `grade_level` enum('7','8','9','10') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `subject_id` (`subject_id`),
  CONSTRAINT `fk_subject_grade_levels_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `subject_grade_levels`
INSERT INTO `subject_grade_levels` VALUES("2","21","7","2024-11-23 16:31:27");
INSERT INTO `subject_grade_levels` VALUES("3","22","7","2024-12-01 12:28:59");
INSERT INTO `subject_grade_levels` VALUES("4","23","7","2024-12-01 12:29:10");
INSERT INTO `subject_grade_levels` VALUES("5","24","7","2024-12-01 12:29:22");


-- Table structure for table `subjects`
DROP TABLE IF EXISTS `subjects`;
CREATE TABLE `subjects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_code` varchar(20) NOT NULL,
  `subject_title` varchar(255) NOT NULL,
  `subject_name` varchar(100) NOT NULL,
  `category` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `grade_level` varchar(50) DEFAULT 'All' COMMENT 'Comma-separated list of applicable grade levels',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `subject_code` (`subject_code`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `subjects`
INSERT INTO `subjects` VALUES("21","ENG","English","","Minor","English","2024-11-23 16:31:27","inactive","All","2024-12-02 03:09:21");
INSERT INTO `subjects` VALUES("22","Fil","Fil","","Core","Fil","2024-12-01 12:28:59","inactive","All","2024-12-02 03:09:18");
INSERT INTO `subjects` VALUES("23","Math","Math","","Core","Math","2024-12-01 12:29:10","active","All","2024-12-01 12:29:10");
INSERT INTO `subjects` VALUES("24","Science","Science","","Core","Science","2024-12-01 12:29:22","active","All","2024-12-01 12:29:22");


-- Table structure for table `submission_files`
DROP TABLE IF EXISTS `submission_files`;
CREATE TABLE `submission_files` (
  `file_id` int(11) NOT NULL AUTO_INCREMENT,
  `submission_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(100) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`file_id`),
  KEY `submission_id` (`submission_id`),
  CONSTRAINT `submission_files_ibfk_1` FOREIGN KEY (`submission_id`) REFERENCES `student_activity_submissions` (`submission_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `submission_files`
INSERT INTO `submission_files` VALUES("31","158","Student Side.docx","uploads/activities/50/67532c4a3dc78_1733504074.docx","application/vnd.openxmlformats-officedocument.wordprocessingml.document","912716","2024-12-07 00:54:34","2024-12-07 00:54:34");
INSERT INTO `submission_files` VALUES("32","159","Navy and Broken White Geometric Thesis Defense Presentation.pdf","uploads/assignments/50/67532e499866a_1733504585.pdf","application/pdf","7196047","2024-12-07 01:03:05","2024-12-07 01:03:05");
INSERT INTO `submission_files` VALUES("38","165","DCIT-65A-FINAL-PROJECT.pdf","uploads/assignments/50/675331eecd0a5_1733505518.pdf","application/pdf","7298057","2024-12-07 01:18:38","2024-12-07 01:18:38");
INSERT INTO `submission_files` VALUES("39","166","DCIT-65A-FINAL-PROJECT.pdf","uploads/activities/50/6753337cd471c_1733505916.pdf","application/pdf","7298057","2024-12-07 01:25:16","2024-12-07 01:25:16");


-- Table structure for table `teacher`
DROP TABLE IF EXISTS `teacher`;
CREATE TABLE `teacher` (
  `teacher_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `password_recovery` enum('yes','no') DEFAULT 'no',
  PRIMARY KEY (`teacher_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `department_id` (`department_id`),
  CONSTRAINT `teacher_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`)
) ENGINE=InnoDB AUTO_INCREMENT=128 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `teacher`
INSERT INTO `teacher` VALUES("9","chaw","d10c353d688d407960a9728445424240","kingpacifico0021@gmail.com","../uploads/teachers/profile/teacher_9_1732418651.jpg","Christiana","Pacifico","Ilagan","3","active","0",NULL,"2024-11-23 16:30:23","2024-12-12 10:12:46",NULL,NULL,"\"{\\\"dark_mode\\\":false,\\\"compact_view\\\":false}\"","\"{\\\"auto_grading\\\":false,\\\"allow_late\\\":false,\\\"default_deadline\\\":\\\"07:00\\\"}\"","\"{\\\"email\\\":false,\\\"submissions\\\":false,\\\"deadlines\\\":false}\"","0",NULL,"yes");
INSERT INTO `teacher` VALUES("123","teacher1","41c8949aa55b8cb5dbec662f34b62df3","teacher1@gmail.com",NULL,"teacher1","teacher1","teacher1","4","active","0",NULL,"2024-12-01 12:05:44","2024-12-01 12:29:42",NULL,NULL,NULL,NULL,NULL,"0",NULL,"no");
INSERT INTO `teacher` VALUES("124","teacher2","ccffb0bb993eeb79059b31e1611ec353","teacher2@gmail.com",NULL,"teacher2","teacher2","teacher2","1","active","0",NULL,"2024-12-01 12:06:00","2024-12-01 12:29:54",NULL,NULL,NULL,NULL,NULL,"0",NULL,"no");
INSERT INTO `teacher` VALUES("125","teacher3","82470256ea4b80343b27afccbca1015b","teacher3@gmail.com",NULL,"teacher3","teacher3","teacher3","2","active","0",NULL,"2024-12-01 12:06:17","2024-12-01 12:30:12",NULL,NULL,NULL,NULL,NULL,"0",NULL,"no");
INSERT INTO `teacher` VALUES("126","Sharmaine","44b3be36ab75d17be9eb9d4acf6f9f97","sharmainep@gmail.com",NULL,"Sharmaine","Diasnes","P","1","active","0",NULL,"2024-12-11 15:36:35","2024-12-11 15:36:35",NULL,NULL,NULL,NULL,NULL,"0",NULL,"no");
INSERT INTO `teacher` VALUES("127","ahji234","ebe96d6963a04711b2b2bc8d663b5b81","Ahjilee234@gmail.com",NULL,"Ahji","Lee","Chu","6","active","0",NULL,"2024-12-11 23:37:39","2024-12-11 23:37:39",NULL,NULL,NULL,NULL,NULL,"0",NULL,"no");


-- Table structure for table `teacher_login_logs`
DROP TABLE IF EXISTS `teacher_login_logs`;
CREATE TABLE `teacher_login_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `teacher_id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `status` enum('success','failed','logout') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `teacher_id` (`teacher_id`),
  CONSTRAINT `teacher_login_logs_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`teacher_id`)
) ENGINE=InnoDB AUTO_INCREMENT=129 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `teacher_login_logs`
INSERT INTO `teacher_login_logs` VALUES("82","9","::1","success","2024-12-03 19:47:03");
INSERT INTO `teacher_login_logs` VALUES("83","9","::1","success","2024-12-04 01:17:04");
INSERT INTO `teacher_login_logs` VALUES("84","9","::1","success","2024-12-06 03:39:12");
INSERT INTO `teacher_login_logs` VALUES("85","9","::1","success","2024-12-06 04:56:11");
INSERT INTO `teacher_login_logs` VALUES("86","9","49.144.12.254","success","2024-12-09 01:40:09");
INSERT INTO `teacher_login_logs` VALUES("87","9","180.194.233.34","success","2024-12-09 01:46:20");
INSERT INTO `teacher_login_logs` VALUES("88","9","180.194.233.34","success","2024-12-09 02:12:01");
INSERT INTO `teacher_login_logs` VALUES("89","9","175.176.36.21","success","2024-12-09 11:13:58");
INSERT INTO `teacher_login_logs` VALUES("90","9","175.176.36.21","success","2024-12-09 11:16:03");
INSERT INTO `teacher_login_logs` VALUES("91","9","175.176.36.21","success","2024-12-09 11:16:57");
INSERT INTO `teacher_login_logs` VALUES("92","9","175.176.36.21","success","2024-12-09 12:44:39");
INSERT INTO `teacher_login_logs` VALUES("93","9","136.158.49.167","success","2024-12-09 13:58:46");
INSERT INTO `teacher_login_logs` VALUES("94","9","136.158.49.167","success","2024-12-09 14:02:40");
INSERT INTO `teacher_login_logs` VALUES("95","9","103.91.141.65","success","2024-12-09 15:37:36");
INSERT INTO `teacher_login_logs` VALUES("96","9","::1","success","2024-12-10 19:39:00");
INSERT INTO `teacher_login_logs` VALUES("97","9","::1","success","2024-12-10 20:02:51");
INSERT INTO `teacher_login_logs` VALUES("98","9","::1","logout","2024-12-10 20:02:53");
INSERT INTO `teacher_login_logs` VALUES("99","9","::1","success","2024-12-10 20:04:31");
INSERT INTO `teacher_login_logs` VALUES("100","9","::1","logout","2024-12-10 20:04:42");
INSERT INTO `teacher_login_logs` VALUES("101","9","::1","success","2024-12-10 20:14:07");
INSERT INTO `teacher_login_logs` VALUES("102","9","::1","logout","2024-12-10 20:17:58");
INSERT INTO `teacher_login_logs` VALUES("103","9","::1","success","2024-12-10 20:43:29");
INSERT INTO `teacher_login_logs` VALUES("104","9","::1","logout","2024-12-10 20:43:31");
INSERT INTO `teacher_login_logs` VALUES("105","9","::1","success","2024-12-10 20:59:44");
INSERT INTO `teacher_login_logs` VALUES("106","9","::1","logout","2024-12-10 20:59:47");
INSERT INTO `teacher_login_logs` VALUES("107","9","::1","success","2024-12-10 21:41:52");
INSERT INTO `teacher_login_logs` VALUES("108","9","::1","logout","2024-12-10 21:41:54");
INSERT INTO `teacher_login_logs` VALUES("109","9","::1","success","2024-12-10 21:44:33");
INSERT INTO `teacher_login_logs` VALUES("110","9","::1","logout","2024-12-10 21:44:35");
INSERT INTO `teacher_login_logs` VALUES("111","9","::1","logout","2024-12-10 22:03:08");
INSERT INTO `teacher_login_logs` VALUES("112","9","::1","logout","2024-12-10 22:09:11");
INSERT INTO `teacher_login_logs` VALUES("113","9","::1","logout","2024-12-10 22:11:09");
INSERT INTO `teacher_login_logs` VALUES("114","9","::1","success","2024-12-10 22:11:20");
INSERT INTO `teacher_login_logs` VALUES("115","9","::1","success","2024-12-10 22:13:22");
INSERT INTO `teacher_login_logs` VALUES("116","9","::1","success","2024-12-11 00:20:25");
INSERT INTO `teacher_login_logs` VALUES("117","126","180.194.233.34","success","2024-12-11 17:31:01");
INSERT INTO `teacher_login_logs` VALUES("118","127","136.158.49.29","success","2024-12-11 23:38:05");
INSERT INTO `teacher_login_logs` VALUES("119","9","180.194.233.34","success","2024-12-12 02:06:15");
INSERT INTO `teacher_login_logs` VALUES("120","9","180.194.233.34","success","2024-12-12 02:09:34");
INSERT INTO `teacher_login_logs` VALUES("121","9","180.194.233.34","success","2024-12-12 02:31:41");
INSERT INTO `teacher_login_logs` VALUES("122","9","2405:8d40:4881:7287:89a1:f04f:eb4a:5a97","success","2024-12-12 06:47:28");
INSERT INTO `teacher_login_logs` VALUES("123","9","2405:8d40:4881:7287:89a1:f04f:eb4a:5a97","success","2024-12-12 06:47:30");
INSERT INTO `teacher_login_logs` VALUES("124","9","58.69.144.207","success","2024-12-12 06:52:58");
INSERT INTO `teacher_login_logs` VALUES("125","9","58.69.144.207","success","2024-12-12 07:01:30");
INSERT INTO `teacher_login_logs` VALUES("126","9","58.69.144.207","success","2024-12-12 07:03:15");
INSERT INTO `teacher_login_logs` VALUES("127","126","180.194.233.34","success","2024-12-12 10:33:16");
INSERT INTO `teacher_login_logs` VALUES("128","126","180.194.233.34","success","2024-12-12 14:06:47");


