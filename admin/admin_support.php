<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$db = new DbConnector();
$admin_id = $_SESSION['admin_id'];

// Get admin info
$query = "SELECT * FROM admin WHERE admin_id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Support - CamerinoHub</title>
    
    <!-- External CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href="css/dashboard-shared.css" rel="stylesheet">
	<link rel="icon" href="../images/light-logo.png">
    <style>
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

        .faq-item {
            margin-bottom: 1rem;
        }

        .faq-question {
            cursor: pointer;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 0.5rem;
        }

        .faq-answer {
            padding: 1rem;
            background: #fff;
            border-left: 3px solid #3498db;
            margin-left: 1rem;
            display: none;
        }

        .contact-info {
            background: #e8f4f8;
            padding: 1rem;
            border-radius: 8px;
        }

        /* Guide Cards */
        .guide-card {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            height: 100%;
        }

        .guide-card h4 {
            color: #2c3e50;
            font-size: 1.1rem;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .guide-card ul {
            list-style: none;
            padding-left: 0;
            margin-bottom: 20px;
        }

        .guide-card ul li {
            padding: 5px 0;
            color: #555;
            position: relative;
            padding-left: 20px;
        }

        .guide-card ul li:before {
            content: "→";
            position: absolute;
            left: 0;
            color: #3498db;
        }

        /* Task Grid */
        .task-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .task-item {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            border: 1px solid #dee2e6;
        }

        .task-item h5 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .task-item ol {
            padding-left: 20px;
            margin-bottom: 0;
        }

        .task-item ol li {
            color: #555;
            padding: 5px 0;
        }

        /* Enhanced FAQ Styling */
        .faq-item {
            margin-bottom: 15px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            overflow: hidden;
        }

        .faq-question {
            padding: 15px;
            background: #f8f9fa;
            cursor: pointer;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .faq-answer {
            padding: 15px;
            background: #fff;
            border-top: 1px solid #e9ecef;
        }

        .faq-answer ol {
            padding-left: 20px;
            margin-bottom: 0;
        }

        .faq-answer ol li {
            padding: 5px 0;
            color: #555;
        }
    </style>
</head>
<body>
    <?php include 'includes/navigation.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="container-fluid">
                <h2 class="mb-4">Admin Support Center</h2>

                <!-- Quick Links -->
                <div class="support-card feature-section">
                    <h3 class="feature-title">
                        <i class="fas fa-link support-icon"></i>
                        Quick Access
                    </h3>
                    <div class="row">
                        <div class="col-md-4">
                            <a href="manage_teachers.php" class="quick-link">
                                <i class="fas fa-chalkboard-teacher"></i> Teacher Management
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="manage_students.php" class="quick-link">
                                <i class="fas fa-user-graduate"></i> Student Management
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="manage_subjects.php" class="quick-link">
                                <i class="fas fa-book"></i> Subject Management
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Admin Features -->
                <div class="support-card feature-section">
                    <h3 class="feature-title">
                        <i class="fas fa-tools support-icon"></i>
                        Administrative Features
                    </h3>
                    <div class="row">
                        <div class="col-md-6">
                            <h4>User Management</h4>
                            <ul>
                                <li>Add, edit, or remove teachers</li>
                                <li>Manage student accounts</li>
                                <li>Reset user passwords</li>
                                <li>Update user information</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h4>Academic Management</h4>
                            <ul>
                                <li>Create and manage subjects</li>
                                <li>Assign teachers to sections</li>
                                <li>Monitor class schedules</li>
                                <li>Track academic progress</li>
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
                            How do I manage student accounts?
                        </div>
                        <div class="faq-answer">
                            <ol>
                                <li>Go to "Student Management" in the sidebar</li>
                                <li>Add new students using the "Add New Student" button</li>
                                <li>Import multiple students using the "Import Students" feature</li>
                                <li>Edit student information by clicking the edit icon</li>
                                <li>Archive inactive students instead of deleting them</li>
                                <li>Reset passwords if students are having login issues</li>
                            </ol>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <i class="fas fa-chevron-right"></i>
                            How do I manage academic years?
                        </div>
                        <div class="faq-answer">
                            <ol>
                                <li>Navigate to "Academic Year Management"</li>
                                <li>Create new academic year with start and end dates</li>
                                <li>Set the active academic year for current operations</li>
                                <li>Archive previous academic years when completed</li>
                                <li>View enrollment statistics for each academic year</li>
                                <li>Manage section assignments for each year</li>
                            </ol>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <i class="fas fa-chevron-right"></i>
                            How do I handle teacher assignments?
                        </div>
                        <div class="faq-answer">
                            <ol>
                                <li>Go to "Teacher Management"</li>
                                <li>Add new teachers with their credentials</li>
                                <li>Assign teachers to specific departments</li>
                                <li>Assign subjects to teachers</li>
                                <li>Manage teacher-section assignments</li>
                                <li>Monitor teacher workload and schedules</li>
                            </ol>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <i class="fas fa-chevron-right"></i>
                            How do I manage sections and subjects?
                        </div>
                        <div class="faq-answer">
                            <ol>
                                <li>Use "Section Management" for creating/editing sections</li>
                                <li>Create subjects in "Subject Management"</li>
                                <li>Assign subjects to specific grade levels</li>
                                <li>Set section capacities and advisers</li>
                                <li>Monitor section enrollments</li>
                                <li>Track subject assignments and schedules</li>
                            </ol>
                        </div>
                    </div>

                    <div class="faq-item">
                        <div class="faq-question">
                            <i class="fas fa-chevron-right"></i>
                            How do I handle system backups and maintenance?
                        </div>
                        <div class="faq-answer">
                            <ol>
                                <li>Regular backups are automated daily</li>
                                <li>Manual backups can be initiated from "System Settings"</li>
                                <li>Archive old data at the end of each academic year</li>
                                <li>Monitor system performance in "System Status"</li>
                                <li>Check error logs for any issues</li>
                                <li>Contact technical support for major concerns</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- Contact Support -->
                <div class="support-card feature-section">
                    <h3 class="feature-title">
                        <i class="fas fa-headset support-icon"></i>
                        Technical Support
                    </h3>
                    <div class="contact-info">
                        <p><i class="fas fa-envelope"></i> Email: admin.support@camerinohub.edu</p>
                        <p><i class="fas fa-phone"></i> Support Hotline: (123) 456-7890</p>
                        <p><i class="fas fa-clock"></i> Available: Monday to Friday, 8:00 AM - 5:00 PM</p>
                        <p><i class="fas fa-exclamation-circle"></i> For urgent matters, please contact the IT department directly</p>
                    </div>
                </div>

                <!-- Add Quick Access Guides section -->
                <div class="support-card feature-section">
                    <h3 class="feature-title">
                        <i class="fas fa-book support-icon"></i>
                        Quick Access Guides
                    </h3>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="guide-card">
                                <h4><i class="fas fa-users"></i> Student Management</h4>
                                <ul>
                                    <li>Add/Edit Student Records</li>
                                    <li>Bulk Import Students</li>
                                    <li>Manage Enrollments</li>
                                    <li>Reset Student Passwords</li>
                                    <li>View Student History</li>
                                </ul>
                                <a href="manage_students.php" class="btn btn-sm btn-primary">Go to Students</a>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="guide-card">
                                <h4><i class="fas fa-chalkboard-teacher"></i> Teacher Management</h4>
                                <ul>
                                    <li>Add/Edit Teachers</li>
                                    <li>Assign Subjects</li>
                                    <li>Set Teaching Load</li>
                                    <li>Monitor Performance</li>
                                    <li>Manage Schedules</li>
                                </ul>
                                <a href="manage_teachers.php" class="btn btn-sm btn-primary">Go to Teachers</a>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="guide-card">
                                <h4><i class="fas fa-calendar-alt"></i> Academic Management</h4>
                                <ul>
                                    <li>Set Academic Year</li>
                                    <li>Manage Sections</li>
                                    <li>Configure Subjects</li>
                                    <li>Set Schedules</li>
                                    <li>View Reports</li>
                                </ul>
                                <a href="academic_year.php" class="btn btn-sm btn-primary">Go to Academic</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add Common Tasks section -->
                <div class="support-card feature-section">
                    <h3 class="feature-title">
                        <i class="fas fa-tasks support-icon"></i>
                        Common Administrative Tasks
                    </h3>
                    <div class="task-grid">
                        <div class="task-item">
                            <h5>Start of School Year</h5>
                            <ol>
                                <li>Create new academic year</li>
                                <li>Set up sections</li>
                                <li>Assign teachers</li>
                                <li>Process enrollments</li>
                                <li>Configure schedules</li>
                            </ol>
                        </div>
                        <div class="task-item">
                            <h5>Regular Maintenance</h5>
                            <ol>
                                <li>Monitor system status</li>
                                <li>Update user accounts</li>
                                <li>Check error logs</li>
                                <li>Backup data</li>
                                <li>Review security</li>
                            </ol>
                        </div>
                        <div class="task-item">
                            <h5>End of School Year</h5>
                            <ol>
                                <li>Archive student records</li>
                                <li>Generate final reports</li>
                                <li>Backup all data</li>
                                <li>Close academic year</li>
                                <li>Prepare next year setup</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // FAQ Toggle
            $('.faq-question').click(function() {
                $(this).next('.faq-answer').slideToggle();
                $(this).find('i').toggleClass('fa-chevron-right fa-chevron-down');
            });
        });
    </script>
</body>
</html>
