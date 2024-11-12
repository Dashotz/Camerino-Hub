<?php
session_start();
require_once('../db/dbConnector.php');

// Check if teacher is logged in
if (!isset($_SESSION['teacher_id'])) {
    header("Location: Teacher-Login.php");
    exit();
}

$db = new DbConnector();
$teacher_id = $_SESSION['teacher_id'];

// Fetch teacher information
$query = "SELECT * FROM teacher WHERE teacher_id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$teacher = $result->fetch_assoc();

// Get class/section count
$class_query = "
    SELECT COUNT(DISTINCT ss.section_id) as class_count 
    FROM section_subjects ss
    WHERE ss.teacher_id = ?
        AND ss.status = 'active'
        AND ss.academic_year_id = (
            SELECT id FROM academic_years 
            WHERE status = 'active' 
            LIMIT 1
        )";
$stmt = $db->prepare($class_query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$class_result = $stmt->get_result();
$class_count = $class_result->fetch_assoc()['class_count'];

// Get detailed student count per section
$students_query = "
    SELECT 
        sec.section_id,
        sec.section_name,
        COUNT(DISTINCT sts.student_id) as student_count
    FROM section_subjects ss
    JOIN sections sec ON ss.section_id = sec.section_id
    JOIN student_sections sts ON sec.section_id = sts.section_id
    WHERE ss.teacher_id = ?
        AND ss.status = 'active'
        AND sts.status = 'active'
        AND ss.academic_year_id = (
            SELECT id FROM academic_years 
            WHERE status = 'active' 
            LIMIT 1
        )
    GROUP BY sec.section_id, sec.section_name";
$stmt = $db->prepare($students_query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$students_result = $stmt->get_result();
$class_students = $students_result->fetch_all(MYSQLI_ASSOC);

// Get total student count
$total_students_query = "
    SELECT COUNT(DISTINCT sts.student_id) as total_students
    FROM section_subjects ss
    JOIN student_sections sts ON ss.section_id = sts.section_id
    WHERE ss.teacher_id = ?
        AND ss.status = 'active'
        AND sts.status = 'active'
        AND ss.academic_year_id = (
            SELECT id FROM academic_years 
            WHERE status = 'active' 
            LIMIT 1
        )";
$stmt = $db->prepare($total_students_query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$total_result = $stmt->get_result();
$student_count = $total_result->fetch_assoc()['total_students'];

// Get pending activities count
$assignments_query = "
    SELECT COUNT(*) as assignment_count 
    FROM activities a
    WHERE a.teacher_id = ?
        AND a.status = 'active'
        AND a.due_date > NOW()";
$stmt = $db->prepare($assignments_query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$assignments_result = $stmt->get_result();
$assignment_count = $assignments_result->fetch_assoc()['assignment_count'];

// Get average class performance
$performance_query = "
    SELECT AVG(sas.points) as average_grade 
    FROM student_activity_submissions sas
    JOIN activities a ON sas.activity_id = a.activity_id
    WHERE a.teacher_id = ? 
        AND sas.points IS NOT NULL
        AND a.status = 'active'";
$stmt = $db->prepare($performance_query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$performance_result = $stmt->get_result();
$average_performance = round($performance_result->fetch_assoc()['average_grade'] ?? 0);

// Get recent activities/submissions
$activities_query = "
    SELECT 
        a.activity_id,
        a.title,
        a.description,
        a.type,
        s.firstname as student_firstname,
        s.lastname as student_lastname,
        sas.submitted_at as activity_date,
        sec.section_name,
        sub.subject_name
    FROM activities a
    JOIN section_subjects ss ON a.teacher_id = ss.teacher_id
    JOIN sections sec ON ss.section_id = sec.section_id
    JOIN subjects sub ON ss.subject_id = sub.id
    JOIN student_activity_submissions sas ON a.activity_id = sas.activity_id
    JOIN student s ON sas.student_id = s.student_id
    WHERE a.teacher_id = ?
        AND ss.status = 'active'
        AND a.status = 'active'
        AND ss.academic_year_id = (
            SELECT id FROM academic_years 
            WHERE status = 'active' 
            LIMIT 1
        )
    ORDER BY sas.submitted_at DESC
    LIMIT 5";

$stmt = $db->prepare($activities_query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$activities_result = $stmt->get_result();
$recent_activities = $activities_result->fetch_all(MYSQLI_ASSOC);

// Include the timeAgo function from student dashboard
function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    
    if ($diff < 60) {
        return "Just now";
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . " minute" . ($mins > 1 ? "s" : "") . " ago";
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . " hour" . ($hours > 1 ? "s" : "") . " ago";
    } else {
        $days = floor($diff / 86400);
        return $days . " day" . ($days > 1 ? "s" : "") . " ago";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard - Gov D.M. Camerino</title>
    
    <!-- External CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Product+Sans:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/dashboard-shared.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
    <?php include 'includes/navigation.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Welcome Section -->
            <div class="welcome-section">
                <h1>Welcome, <?php echo htmlspecialchars($teacher['firstname'] ?? 'Teacher'); ?>!</h1>
                <p>Here's your teaching overview</p>
            </div>

            <!-- Stats Cards -->
            <div class="row stats-cards">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <i class="fas fa-chalkboard"></i>
                            <h5>Active Classes</h5>
                            <h3><?php echo $class_count; ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <i class="fas fa-users"></i>
                            <h5>Total Students</h5>
                            <h3><?php echo $student_count; ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <i class="fas fa-tasks"></i>
                            <h5>Active Assignments</h5>
                            <h3><?php echo $assignment_count; ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <i class="fas fa-chart-line"></i>
                            <h5>Class Performance</h5>
                            <h3><?php echo $average_performance; ?>%</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities and Quick Actions -->
            <div class="row mt-4">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5>Recent Activities</h5>
                        </div>
                        <div class="card-body">
                            <?php foreach ($recent_activities as $activity): ?>
                                <div class="activity-item">
                                    <i class="fas <?php echo $activity['type'] === 'assignment' ? 'fa-file-alt' : 'fa-check-circle'; ?>"></i>
                                    <div class="activity-details">
                                        <h6><?php echo htmlspecialchars($activity['title']); ?></h6>
                                        <p><?php echo htmlspecialchars($activity['description']); ?></p>
                                        <small><?php echo timeAgo($activity['activity_date']); ?></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <?php if (empty($recent_activities)): ?>
                                <p class="text-muted text-center">No recent activities</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5>Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="quick-actions-list">
                                <a href="create_assignment.php" class="quick-action-item">
                                    <i class="fas fa-plus-circle"></i>
                                    <span>Create Assignment</span>
                                </a>
                                <a href="take_attendance.php" class="quick-action-item">
                                    <i class="fas fa-clipboard-check"></i>
                                    <span>Take Attendance</span>
                                </a>
                                <a href="grade_submissions.php" class="quick-action-item">
                                    <i class="fas fa-check-square"></i>
                                    <span>Grade Submissions</span>
                                </a>
                                <a href="class_reports.php" class="quick-action-item">
                                    <i class="fas fa-chart-bar"></i>
                                    <span>View Reports</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
