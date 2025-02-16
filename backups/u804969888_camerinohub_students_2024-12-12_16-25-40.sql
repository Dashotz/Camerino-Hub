-- Database backup created on 2024-12-12 16:25:40
SET FOREIGN_KEY_CHECKS=0;



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
) ENGINE=InnoDB AUTO_INCREMENT=224 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `student` VALUES ('38', '123456789999', '469e7e66cff79f931488a5feee1909e6', 'christian@frncszxc.helioho.st', '09701333333', 'Male', NULL, 'Edrian', 'Pacifico', 'Ilagan', NULL, '', 'active', '0', NULL, '0', '2024-11-24 20:45:19', '2024-12-11 17:28:56', NULL, NULL, NULL, 'no');
INSERT INTO `student` VALUES ('39', '100000999999', 'f8c7c5d26055adb57617e687dbf6811c', 'eleanorpacifico@gmail.com', '09701640382', 'Male', NULL, 'Christopher', 'Pacifico', 'Ilagan', NULL, '', 'active', '0', NULL, '0', '2024-11-24 22:48:34', '2024-12-11 17:18:19', NULL, NULL, NULL, 'no');
INSERT INTO `student` VALUES ('40', '123456789111', '9acb31bd27f2bb425f0b07c9b5322ed3', 'student1@gmail.com', '09504222701', 'Male', NULL, 'student1', 'student1', 'student1', NULL, '', 'active', '0', NULL, '0', '2024-12-01 13:36:59', '2024-12-11 17:28:55', NULL, NULL, NULL, 'no');
INSERT INTO `student` VALUES ('50', '999999999999', '8ccb29db1ea08e210d6d54002ada3c23', 'dashotz14@gmail.com', '09208040444', 'Male', NULL, 'Francis', 'Cruz', '', NULL, '', 'active', '0', NULL, '1', '2024-12-04 01:16:52', '2024-12-12 16:24:36', '20241212162436', 'e08jultgcuv0oqtbhign29mfae', NULL, 'no');
INSERT INTO `student` VALUES ('109', '123456789012', 'd470bfce3e6db20a2fa4d6c4b6b8bf9e', 'juan.delacruz@example.com', '9123456789', 'Male', NULL, 'Juan', 'Dela Cruz', 'Santos', NULL, '', 'active', '0', NULL, '0', '2024-12-11 17:25:55', '2024-12-11 17:29:49', NULL, NULL, NULL, 'no');
INSERT INTO `student` VALUES ('211', '123456789013', '5955c1d7fafac1a87b2e22e33503d197', 'maria.santos@example.com', '09987654321', 'Female', NULL, 'Maria', 'Santos', 'Garcia', NULL, '', 'active', '0', NULL, '0', '2024-12-11 17:48:04', '2024-12-11 17:48:04', NULL, NULL, NULL, 'no');
INSERT INTO `student` VALUES ('222', '103255555555', '1d42da412bdfd380d8b5b9725fc98f1b', '12phashajoy@gmail.com', '09167328478', 'Female', NULL, 'Phasha', 'Major', 'Joy', NULL, '', 'active', '0', NULL, '1', '2024-12-11 23:31:04', '2024-12-11 23:43:54', '20241211234354', 'lksp2hog3hgflpbi6ldhjrdrbt', NULL, 'no');
INSERT INTO `student` VALUES ('223', '000000000000', '839e25a7fbbe4764b993e10de59a4aa8', 'christianpacifico20@gmail.com', '09701640382', 'Male', NULL, 'Christian', 'Pacifico', 'Ilagan', NULL, '', 'active', '0', NULL, '0', '2024-12-12 15:46:32', '2024-12-12 16:01:50', NULL, NULL, NULL, 'no');


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
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



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
  `result_file` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`submission_id`),
  KEY `student_id` (`student_id`),
  KEY `idx_activity_submitted` (`activity_id`,`submitted_at`),
  KEY `idx_activity_submissions` (`activity_id`,`submitted_at`),
  KEY `fk_graded_by_teacher` (`graded_by`),
  CONSTRAINT `fk_graded_by_teacher` FOREIGN KEY (`graded_by`) REFERENCES `teacher` (`teacher_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `student_activity_submissions_ibfk_2` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`activity_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=196 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `student_activity_submissions` VALUES ('158', '50', '94', '99', '', NULL, '2024-12-07 00:54:34', 'submitted', '2024-12-07 00:58:34', NULL, '0', NULL, '2024-12-07 00:54:34', '2024-12-07 00:54:34', '2024-12-07 00:58:34', '0', NULL, NULL, NULL, '0.00', '0', '0', '0', NULL, NULL);
INSERT INTO `student_activity_submissions` VALUES ('179', '222', '152', NULL, NULL, NULL, '2024-12-12 00:02:38', 'submitted', NULL, NULL, '0', NULL, '2024-12-12 00:02:38', '2024-12-12 00:02:38', '2024-12-12 00:02:38', '0', NULL, NULL, NULL, '0.00', '0', '0', '0', NULL, NULL);
INSERT INTO `student_activity_submissions` VALUES ('180', '50', '152', '100', 'AWet', NULL, '2024-12-12 02:04:02', 'graded', '2024-12-12 12:23:09', '9', '0', NULL, '2024-12-12 02:04:02', '2024-12-12 02:04:02', '2024-12-12 12:23:09', '0', NULL, NULL, NULL, '0.00', '0', '0', '0', NULL, 'uploads/results/675a45dd54897_08_Handout_1(16).pdf');
INSERT INTO `student_activity_submissions` VALUES ('85', '50', '125', '0', NULL, NULL, '2024-12-06 05:24:49', 'submitted', NULL, NULL, '0', NULL, '2024-12-06 05:24:49', '2024-12-06 05:24:49', '2024-12-06 05:24:49', '0', NULL, '2', NULL, '0.00', '0', '0', '0', NULL, NULL);
INSERT INTO `student_activity_submissions` VALUES ('100', '50', '131', '2', NULL, NULL, '2024-12-06 06:12:37', 'submitted', NULL, NULL, '0', NULL, '2024-12-06 06:12:37', '2024-12-06 06:12:37', '2024-12-06 06:12:37', '0', NULL, '3', NULL, '0.00', '0', '0', '0', NULL, NULL);
INSERT INTO `student_activity_submissions` VALUES ('101', '50', '135', '3', NULL, NULL, '2024-12-06 06:12:52', 'submitted', NULL, NULL, '0', NULL, '2024-12-06 06:12:52', '2024-12-06 06:12:52', '2024-12-06 06:12:52', '0', NULL, '2', NULL, '0.00', '0', '0', '0', NULL, NULL);
INSERT INTO `student_activity_submissions` VALUES ('104', '50', '137', '1', NULL, NULL, '2024-12-06 15:57:53', 'submitted', NULL, NULL, '0', NULL, '2024-12-06 15:57:53', '2024-12-06 15:57:53', '2024-12-06 15:57:53', '0', NULL, '4', NULL, '0.00', '0', '0', '0', NULL, NULL);
INSERT INTO `student_activity_submissions` VALUES ('111', '50', '138', '3', NULL, NULL, '2024-12-06 17:26:46', 'submitted', NULL, NULL, '0', NULL, '2024-12-06 17:26:46', '2024-12-06 17:26:46', '2024-12-06 17:26:46', '0', NULL, '3', NULL, '0.00', '0', '0', '0', NULL, NULL);
INSERT INTO `student_activity_submissions` VALUES ('125', '50', '139', '3', NULL, NULL, '2024-12-06 18:34:59', 'submitted', NULL, NULL, '0', NULL, '2024-12-06 18:34:59', '2024-12-06 18:34:59', '2024-12-06 18:34:59', '0', NULL, '3', NULL, '0.00', '0', '0', '0', NULL, NULL);
INSERT INTO `student_activity_submissions` VALUES ('149', '50', '140', '6', NULL, NULL, '2024-12-07 00:06:14', 'submitted', NULL, NULL, '0', NULL, '2024-12-07 00:06:14', '2024-12-07 00:06:14', '2024-12-07 00:06:14', '0', NULL, '10', NULL, '0.00', '0', '0', '6', '6', NULL);
INSERT INTO `student_activity_submissions` VALUES ('148', '50', '141', '4', NULL, NULL, '2024-12-07 00:04:12', 'submitted', NULL, NULL, '0', NULL, '2024-12-07 00:04:12', '2024-12-07 00:04:12', '2024-12-07 00:04:12', '0', NULL, '4', NULL, '0.00', '0', '0', '4', '4', NULL);
INSERT INTO `student_activity_submissions` VALUES ('157', '50', '142', '0', NULL, NULL, '2024-12-07 00:49:06', 'missing', NULL, NULL, '0', NULL, '2024-12-07 00:49:06', '2024-12-07 00:49:06', '2024-12-07 00:49:06', '1', NULL, '13', NULL, '0.00', '0', '0', '0', '5', NULL);
INSERT INTO `student_activity_submissions` VALUES ('172', '50', '146', '3', NULL, NULL, '2024-12-09 02:12:21', 'submitted', NULL, NULL, '0', NULL, '2024-12-09 02:12:21', '2024-12-09 02:12:21', '2024-12-09 02:12:21', '0', NULL, '14', NULL, '0.00', '0', '0', '3', '7', NULL);
INSERT INTO `student_activity_submissions` VALUES ('169', '50', '147', '0', NULL, NULL, '2024-12-09 01:51:19', 'submitted', NULL, NULL, '0', NULL, '2024-12-09 01:51:19', '2024-12-09 01:51:19', '2024-12-09 01:51:19', '0', NULL, '17', NULL, '0.00', '0', '0', '0', '3', NULL);
INSERT INTO `student_activity_submissions` VALUES ('173', '50', '148', '3', NULL, NULL, '2024-12-09 02:12:44', 'submitted', NULL, NULL, '0', NULL, '2024-12-09 02:12:44', '2024-12-09 02:12:44', '2024-12-09 02:12:44', '0', NULL, '18', NULL, '0.00', '0', '0', '2', '3', NULL);
INSERT INTO `student_activity_submissions` VALUES ('174', '50', '149', '0', NULL, NULL, '2024-12-09 02:13:31', 'submitted', NULL, NULL, '0', NULL, '2024-12-09 02:13:31', '2024-12-09 02:13:31', '2024-12-09 02:13:31', '0', NULL, '13', NULL, '0.00', '0', '0', '0', '3', NULL);
INSERT INTO `student_activity_submissions` VALUES ('175', '50', '150', '3', NULL, NULL, '2024-12-09 11:15:45', 'submitted', NULL, NULL, '0', NULL, '2024-12-09 11:15:45', '2024-12-09 11:15:45', '2024-12-09 11:15:45', '0', NULL, '29', NULL, '0.00', '0', '0', '3', '3', NULL);
INSERT INTO `student_activity_submissions` VALUES ('176', '50', '151', '4', NULL, NULL, '2024-12-09 12:59:53', 'graded', '2024-12-12 02:15:05', '9', '0', NULL, '2024-12-09 12:59:53', '2024-12-09 12:59:53', '2024-12-12 02:15:05', '0', NULL, '18', NULL, '0.00', '0', '0', '3', '4', NULL);
INSERT INTO `student_activity_submissions` VALUES ('181', '50', '154', '1', NULL, NULL, '2024-12-12 11:49:40', 'graded', '2024-12-12 12:22:43', '9', '0', NULL, '2024-12-12 11:49:40', '2024-12-12 11:49:40', '2024-12-12 12:22:43', '1', NULL, '12', NULL, '0.00', '0', '0', '0', '1', NULL);
INSERT INTO `student_activity_submissions` VALUES ('187', '50', '158', '0', NULL, NULL, '2024-12-12 12:33:37', 'missing', NULL, NULL, '0', NULL, '2024-12-12 12:33:37', '2024-12-12 12:33:37', '2024-12-12 12:33:37', '1', NULL, '10', NULL, '0.00', '0', '0', '0', '3', NULL);
INSERT INTO `student_activity_submissions` VALUES ('184', '50', '159', '1', NULL, NULL, '2024-12-12 12:10:42', 'graded', '2024-12-12 12:25:26', '9', '0', NULL, '2024-12-12 12:10:42', '2024-12-12 12:10:42', '2024-12-12 12:25:26', '1', NULL, '2', NULL, '0.00', '0', '0', '0', '1', NULL);
INSERT INTO `student_activity_submissions` VALUES ('193', '50', '160', '1', NULL, NULL, '2024-12-12 12:46:33', 'submitted', NULL, NULL, '0', NULL, '2024-12-12 12:46:33', '2024-12-12 12:46:33', '2024-12-12 12:46:33', '0', NULL, '30', NULL, '0.00', '0', '0', '1', '2', NULL);
INSERT INTO `student_activity_submissions` VALUES ('195', '50', '162', '2', NULL, NULL, '2024-12-12 16:25:09', 'submitted', NULL, NULL, '0', NULL, '2024-12-12 16:25:09', '2024-12-12 16:25:09', '2024-12-12 16:25:09', '0', NULL, '29', NULL, '0.00', '0', '0', '2', '3', NULL);
INSERT INTO `student_activity_submissions` VALUES ('159', '50', '143', '88', 'tes', NULL, '2024-12-07 01:03:05', 'submitted', '2024-12-07 01:03:19', NULL, '0', NULL, '2024-12-07 01:03:05', '2024-12-07 01:03:05', '2024-12-07 01:03:19', '0', NULL, NULL, NULL, '0.00', '0', '0', '0', NULL, NULL);
INSERT INTO `student_activity_submissions` VALUES ('165', '50', '145', '89', '', NULL, '2024-12-07 01:18:38', 'submitted', '2024-12-07 01:22:54', NULL, '0', NULL, '2024-12-07 01:18:38', '2024-12-07 01:18:38', '2024-12-07 01:22:54', '0', NULL, NULL, NULL, '0.00', '0', '0', '0', NULL, NULL);
INSERT INTO `student_activity_submissions` VALUES ('194', '223', '161', '100', 'test', NULL, '2024-12-12 15:50:05', 'graded', '2024-12-12 15:50:28', '9', '0', NULL, '2024-12-12 15:50:05', '2024-12-12 15:50:05', '2024-12-12 15:50:28', '0', NULL, NULL, NULL, '0.00', '0', '0', '0', NULL, 'uploads/results/675b0644e01fc_05_Handout_1(10).pdf');


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
) ENGINE=InnoDB AUTO_INCREMENT=288 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `student_answers` VALUES ('27', '50', '138', '91', '168', '', '1', '2024-12-06 17:26:46');
INSERT INTO `student_answers` VALUES ('28', '50', '138', '92', NULL, '', '0', '2024-12-06 17:26:46');
INSERT INTO `student_answers` VALUES ('29', '50', '138', '93', NULL, '0', '1', '2024-12-06 17:26:46');
INSERT INTO `student_answers` VALUES ('30', '50', '138', '94', '172', '', '1', '2024-12-06 17:26:46');
INSERT INTO `student_answers` VALUES ('73', '50', '139', '95', '174', '', '1', '2024-12-06 18:34:59');
INSERT INTO `student_answers` VALUES ('74', '50', '139', '96', '176', '', '1', '2024-12-06 18:34:59');
INSERT INTO `student_answers` VALUES ('75', '50', '139', '97', NULL, '0', '1', '2024-12-06 18:34:59');
INSERT INTO `student_answers` VALUES ('171', '50', '141', '104', '186', '0', '1', '2024-12-07 00:04:12');
INSERT INTO `student_answers` VALUES ('172', '50', '141', '105', '188', '0', '1', '2024-12-07 00:04:12');
INSERT INTO `student_answers` VALUES ('173', '50', '141', '106', NULL, '0', '1', '2024-12-07 00:04:12');
INSERT INTO `student_answers` VALUES ('174', '50', '141', '107', '190', '0', '1', '2024-12-07 00:04:12');
INSERT INTO `student_answers` VALUES ('175', '50', '140', '98', '178', '0', '1', '2024-12-07 00:06:14');
INSERT INTO `student_answers` VALUES ('176', '50', '140', '99', '180', '0', '1', '2024-12-07 00:06:14');
INSERT INTO `student_answers` VALUES ('177', '50', '140', '100', NULL, '0', '1', '2024-12-07 00:06:14');
INSERT INTO `student_answers` VALUES ('178', '50', '140', '101', '182', '0', '1', '2024-12-07 00:06:14');
INSERT INTO `student_answers` VALUES ('179', '50', '140', '102', '184', '0', '1', '2024-12-07 00:06:14');
INSERT INTO `student_answers` VALUES ('180', '50', '140', '103', NULL, '0', '1', '2024-12-07 00:06:14');
INSERT INTO `student_answers` VALUES ('216', '50', '142', '108', NULL, '0', '0', '2024-12-07 00:49:06');
INSERT INTO `student_answers` VALUES ('217', '50', '142', '109', NULL, '0', '0', '2024-12-07 00:49:06');
INSERT INTO `student_answers` VALUES ('218', '50', '142', '110', NULL, '0', '0', '2024-12-07 00:49:06');
INSERT INTO `student_answers` VALUES ('219', '50', '142', '111', NULL, '0', '0', '2024-12-07 00:49:06');
INSERT INTO `student_answers` VALUES ('220', '50', '142', '112', NULL, '0', '0', '2024-12-07 00:49:06');
INSERT INTO `student_answers` VALUES ('231', '50', '147', '120', '211', '', '0', '2024-12-09 01:51:19');
INSERT INTO `student_answers` VALUES ('232', '50', '147', '121', '214', '', '0', '2024-12-09 01:51:19');
INSERT INTO `student_answers` VALUES ('233', '50', '147', '122', NULL, 'a', '0', '2024-12-09 01:51:19');
INSERT INTO `student_answers` VALUES ('240', '50', '146', '113', '198', '', '0', '2024-12-09 02:12:21');
INSERT INTO `student_answers` VALUES ('241', '50', '146', '114', '202', '', '1', '2024-12-09 02:12:21');
INSERT INTO `student_answers` VALUES ('242', '50', '146', '115', NULL, 'asd', '1', '2024-12-09 02:12:21');
INSERT INTO `student_answers` VALUES ('243', '50', '146', '116', NULL, 'asd', '0', '2024-12-09 02:12:21');
INSERT INTO `student_answers` VALUES ('244', '50', '146', '117', NULL, 'sad', '0', '2024-12-09 02:12:21');
INSERT INTO `student_answers` VALUES ('245', '50', '146', '118', '204', '', '1', '2024-12-09 02:12:21');
INSERT INTO `student_answers` VALUES ('246', '50', '146', '119', '208', '', '0', '2024-12-09 02:12:21');
INSERT INTO `student_answers` VALUES ('247', '50', '148', '123', '216', '', '1', '2024-12-09 02:12:44');
INSERT INTO `student_answers` VALUES ('248', '50', '148', '124', '220', '', '1', '2024-12-09 02:12:44');
INSERT INTO `student_answers` VALUES ('249', '50', '148', '125', NULL, 'asdasd', '0', '2024-12-09 02:12:44');
INSERT INTO `student_answers` VALUES ('250', '50', '149', '126', '222', '', '0', '2024-12-09 02:13:31');
INSERT INTO `student_answers` VALUES ('251', '50', '149', '127', '226', '', '0', '2024-12-09 02:13:31');
INSERT INTO `student_answers` VALUES ('252', '50', '149', '128', NULL, '111', '0', '2024-12-09 02:13:31');
INSERT INTO `student_answers` VALUES ('253', '50', '150', '129', '228', '', '1', '2024-12-09 11:15:45');
INSERT INTO `student_answers` VALUES ('254', '50', '150', '130', '231', '', '1', '2024-12-09 11:15:45');
INSERT INTO `student_answers` VALUES ('255', '50', '150', '131', NULL, 'OO', '1', '2024-12-09 11:15:45');
INSERT INTO `student_answers` VALUES ('256', '50', '151', '132', '234', '', '1', '2024-12-09 12:59:53');
INSERT INTO `student_answers` VALUES ('257', '50', '151', '133', '237', '', '1', '2024-12-09 12:59:53');
INSERT INTO `student_answers` VALUES ('258', '50', '151', '134', NULL, '4', '1', '2024-12-09 12:59:53');
INSERT INTO `student_answers` VALUES ('259', '50', '151', '135', NULL, 'testdawd', '0', '2024-12-09 12:59:53');
INSERT INTO `student_answers` VALUES ('260', '50', '154', '136', NULL, '', '0', '2024-12-12 11:49:40');
INSERT INTO `student_answers` VALUES ('267', '50', '159', '141', NULL, '', '0', '2024-12-12 12:10:42');
INSERT INTO `student_answers` VALUES ('270', '50', '158', '138', NULL, '', '0', '2024-12-12 12:33:37');
INSERT INTO `student_answers` VALUES ('271', '50', '158', '139', NULL, '', '0', '2024-12-12 12:33:37');
INSERT INTO `student_answers` VALUES ('272', '50', '158', '140', NULL, '', '0', '2024-12-12 12:33:37');
INSERT INTO `student_answers` VALUES ('283', '50', '160', '142', '252', '', '1', '2024-12-12 12:46:33');
INSERT INTO `student_answers` VALUES ('284', '50', '160', '143', '254', '', '0', '2024-12-12 12:46:33');
INSERT INTO `student_answers` VALUES ('285', '50', '162', '144', '259', '', '1', '2024-12-12 16:25:09');
INSERT INTO `student_answers` VALUES ('286', '50', '162', '145', '261', '', '1', '2024-12-12 16:25:09');
INSERT INTO `student_answers` VALUES ('287', '50', '162', '146', NULL, '', '0', '2024-12-12 16:25:09');


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


SET FOREIGN_KEY_CHECKS=1;
