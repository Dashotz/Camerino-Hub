<?php
session_start();
require_once('../db/dbConnector.php');
$db = new DbConnector();

// Check if teacher is logged in
if (!isset($_SESSION['teacher_id'])) {
    header("Location: Teacher-Login.php");
    exit();
}

// Get teacher information
$teacher_id = $_SESSION['teacher_id'];
$teacher_query = "SELECT firstname, lastname FROM teacher WHERE teacher_id = ?";
$stmt = $db->prepare($teacher_query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$teacher_result = $stmt->get_result();
$teacher_info = $teacher_result->fetch_assoc();

// Add default profile image path
$teacher_info['profile_image'] = '../assets/images/default-profile.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Support - CamerinoHub</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href="css/dashboard-shared.css" rel="stylesheet">
	<link rel="icon" href="../images/light-logo.png">
    <style>
        body {
            padding-top: 60px;
            margin: 0;
            min-height: 100vh;
            background: #f8f9fa;
        }

        .main-content {
            margin-left: 250px;
            padding: 1.5rem;
            min-height: calc(100vh - 60px);
            background: #f8f9fa;
            width: calc(100% - 250px);
        }

        .support-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
            padding: 1.5rem;
        }

        .feature-section {
            margin-bottom: 2rem;
        }

        .feature-title {
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
        }

        .support-icon {
            font-size: 2rem;
            color: #3498db;
            margin-bottom: 1rem;
        }

        .quick-link {
            display: block;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            color: #2c3e50;
            transition: all 0.3s ease;
        }

        .quick-link:hover {
            background: #e9ecef;
            text-decoration: none;
            transform: translateY(-2px);
        }

        .contact-info {
            background: #e8f4f8;
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
        }

        .faq-item {
            margin-bottom: 1rem;
        }

        .faq-question {
            font-weight: 600;
            color: #2c3e50;
            cursor: pointer;
        }

        .faq-answer {
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <?php include 'includes/navigation.php'; ?>
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="container-fluid">
            <h2 class="mb-4">Teacher Support Center</h2>

            <!-- Quick Start Guide -->
            <div class="support-card feature-section">
                <h3 class="feature-title">
                    <i class="fas fa-rocket support-icon"></i>
                    Quick Start Guide
                </h3>
                <div class="row">
                    <div class="col-md-4">
                        <a href="#classes" class="quick-link">
                            <i class="fas fa-chalkboard"></i> Managing Classes
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="#activities" class="quick-link">
                            <i class="fas fa-tasks"></i> Creating Activities
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="#grading" class="quick-link">
                            <i class="fas fa-graduation-cap"></i> Grading System
                        </a>
                    </div>
                </div>
            </div>

            <!-- System Features -->
            <div class="support-card feature-section">
                <h3 class="feature-title">
                    <i class="fas fa-star support-icon"></i>
                    System Features
                </h3>
                <div class="row">
                    <div class="col-md-6">
                        <h4 id="classes">Managing Classes</h4>
                        <ul>
                            <li>View and manage your assigned classes</li>
                            <li>Access student lists and section information</li>
                            <li>Track attendance and participation</li>
                            <li>Communicate with students</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h4 id="activities">Activities and Quizzes</h4>
                        <ul>
                            <li>Create and manage assignments</li>
                            <li>Set up online quizzes</li>
                            <li>Schedule due dates</li>
                            <li>Track submission status</li>
                        </ul>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-md-6">
                        <h4 id="grading">Grading System</h4>
                        <ul>
                            <li>Grade submissions efficiently</li>
                            <li>View student performance analytics</li>
                            <li>Generate grade reports</li>
                            <li>Track academic progress</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h4>Additional Features</h4>
                        <ul>
                            <li>Calendar management</li>
                            <li>Announcement system</li>
                            <li>Resource sharing</li>
                            <li>Performance reports</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- FAQ Section -->
            <div class="support-card feature-section">
                <h3 class="feature-title">
                    <i class="fas fa-question-circle support-icon"></i>
                    Frequently Asked Questions
                </h3>
                <div class="faq-item">
                    <div class="faq-question">
                        <i class="fas fa-chevron-right"></i>
                        How do I create a new activity?
                    </div>
                    <div class="faq-answer">
                        Navigate to "Activities and Quizzes", click "Create New Activity", fill in the required details including title, instructions, due date, and points. You can attach files if needed.
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <i class="fas fa-chevron-right"></i>
                        How do I grade student submissions?
                    </div>
                    <div class="faq-answer">
                        Access "Grade Management", select the class and activity, view submissions, and input grades. The system automatically calculates averages and updates student records.
                    </div>
                </div>
                <!-- Add more FAQ items as needed -->
            </div>

            <!-- Contact Support -->
            <div class="support-card feature-section">
                <h3 class="feature-title">
                    <i class="fas fa-headset support-icon"></i>
                    Contact Support
                </h3>
                <div class="contact-info">
                    <p><i class="fas fa-envelope"></i> Email: support@camerinohub.edu</p>
                    <p><i class="fas fa-phone"></i> Hotline: (123) 456-7890</p>
                    <p><i class="fas fa-clock"></i> Available: Monday to Friday, 8:00 AM - 5:00 PM</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // FAQ Toggle
            $('.faq-question').click(function() {
                $(this).next('.faq-answer').slideToggle();
                $(this).find('i').toggleClass('fa-chevron-right fa-chevron-down');
            });

            // Hide FAQ answers initially
            $('.faq-answer').hide();

            // Smooth scroll for anchor links
            $('a[href^="#"]').on('click', function(e) {
                e.preventDefault();
                var target = $(this.hash);
                $('html, body').animate({
                    scrollTop: target.offset().top - 100
                }, 500);
            });
        });
    </script>
</body>
</html>
