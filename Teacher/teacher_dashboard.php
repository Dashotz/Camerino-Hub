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

// Get recent announcements
$announcements_query = "
    SELECT 
        a.*,
        s.section_name,
        sub.subject_name
    FROM announcements a
    JOIN sections s ON a.section_id = s.section_id
    JOIN subjects sub ON a.subject_id = sub.id
    WHERE a.teacher_id = ? 
    AND a.status = 'active'
    ORDER BY a.created_at DESC 
    LIMIT 5";

$stmt = $db->prepare($announcements_query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$recent_announcements = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Modify the activities query to include due_date and status
$activities_query = "
    SELECT 
        a.activity_id,
        a.title,
        a.description,
        a.type,
        a.due_date,
        a.status,
        s.firstname as student_firstname,
        s.lastname as student_lastname,
        sas.submitted_at as activity_date,
        sec.section_name,
        sub.subject_name,
        (SELECT COUNT(*) FROM student_activity_submissions 
         WHERE activity_id = a.activity_id) as submission_count,
        (SELECT COUNT(*) FROM student_sections 
         WHERE section_id = sec.section_id 
         AND status = 'active') as total_students
    FROM activities a
    JOIN section_subjects ss ON a.section_subject_id = ss.id
    JOIN sections sec ON ss.section_id = sec.section_id
    JOIN subjects sub ON ss.subject_id = sub.id
    LEFT JOIN student_activity_submissions sas ON a.activity_id = sas.activity_id
    LEFT JOIN student s ON sas.student_id = s.student_id
    WHERE a.teacher_id = ?
        AND ss.status = 'active'
        AND a.status = 'active'
        AND ss.academic_year_id = (
            SELECT id FROM academic_years 
            WHERE status = 'active' 
            LIMIT 1
        )
    GROUP BY a.activity_id
    ORDER BY a.created_at DESC
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

function getActivityIcon($type) {
    switch($type) {
        case 'quiz': return 'fa-question-circle';
        case 'assignment': return 'fa-file-alt';
        default: return 'fa-tasks';
    }
}

function getStatusBadge($status) {
    switch($status) {
        case 'active': return 'success';
        case 'archived': return 'secondary';
        default: return 'primary';
    }
}

function truncateText($text, $length) {
    return strlen($text) > $length ? substr($text, 0, $length) . '...' : $text;
}

function getAnnouncementBadge($type) {
    $badges = [
        'quiz' => '<span class="badge badge-primary">Quiz</span>',
        'activity' => '<span class="badge badge-success">Activity</span>',
        'assignment' => '<span class="badge badge-warning">Assignment</span>',
        'normal' => '<span class="badge badge-info">Announcement</span>'
    ];
    return $badges[$type] ?? $badges['normal'];
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
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/dashboard-shared.css">
   
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
                <!-- Left Column -->
                <div class="col-md-8">
                    <!-- Quick Actions Card -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="quick-actions-grid">
                                <a href="manage_activities.php?new=activity" class="quick-action-item">
                                    <i class="fas fa-tasks"></i>
                                    <span>New Activity</span>
                                </a>
                                <a href="manage_activities.php?new=quiz" class="quick-action-item">
                                    <i class="fas fa-question-circle"></i>
                                    <span>New Quiz</span>
                                </a>
                                <a href="manage_activities.php?new=assignment" class="quick-action-item">
                                    <i class="fas fa-file-alt"></i>
                                    <span>New Assignment</span>
                                </a>
                                <a href="teacher_announcements.php" class="quick-action-item">
                                    <i class="fas fa-bullhorn"></i>
                                    <span>Announcement</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activities Card -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5>Recent Activities</h5>
                            <a href="manage_activities.php" class="btn btn-link">View All</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="activity-list">
                                <?php if (!empty($recent_activities)): ?>
                                    <?php foreach ($recent_activities as $activity): ?>
                                        <div class="activity-item p-3 border-bottom">
                                            <div class="d-flex align-items-center">
                                                <div class="activity-icon mr-3">
                                                    <i class="fas <?php echo getActivityIcon($activity['type'] ?? 'activity'); ?>"></i>
                                                </div>
                                                <div class="activity-details flex-grow-1">
                                                    <h6 class="mb-1"><?php echo htmlspecialchars($activity['title']); ?></h6>
                                                    <small class="text-muted">
                                                        Due: <?php echo isset($activity['due_date']) ? date('M d, Y', strtotime($activity['due_date'])) : 'Not set'; ?>
                                                    </small>
                                                </div>
                                                <div class="activity-status">
                                                    <span class="badge badge-<?php echo getStatusBadge($activity['status'] ?? 'active'); ?>">
                                                        <?php echo ucfirst($activity['status'] ?? 'active'); ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="p-3 text-center text-muted">
                                        No recent activities
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-md-4">
                    <!-- Recent Announcements Card -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5>Recent Announcements</h5>
                            <a href="teacher_announcements.php" class="btn btn-link">View All</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="announcement-list">
                                <?php if (!empty($recent_announcements)): ?>
                                    <?php foreach ($recent_announcements as $announcement): ?>
                                        <div class="announcement-item p-3 border-bottom">
                                            <div class="announcement-type mb-2">
                                                <?php echo getAnnouncementBadge($announcement['type'] ?? 'normal'); ?>
                                            </div>
                                            <div class="announcement-content">
                                                <?php echo nl2br(htmlspecialchars(truncateText($announcement['content'], 100))); ?>
                                            </div>
                                            <small class="text-muted">
                                                <?php echo date('M d, Y', strtotime($announcement['created_at'])); ?>
                                            </small>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="p-3 text-center text-muted">
                                        No recent announcements
                                    </div>
                                <?php endif; ?>
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
    <script>
        function createActivity(type) {
            // Store activity type in session storage
            sessionStorage.setItem('newActivityType', type);
            // Redirect to manage activities page
            window.location.href = 'manage_activities.php?new=' + type;
        }
    </script>
</body>
</html>
