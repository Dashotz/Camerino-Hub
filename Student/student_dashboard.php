<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: Student-Login.php");
    exit();
}

// Get user data
require_once('../db/dbConnector.php');
$db = new DbConnector();
$student_id = $_SESSION['id'];

// Fetch student's basic information
$query = "SELECT s.*, sec.section_name, sec.grade_level 
          FROM student s
          LEFT JOIN student_sections ss ON s.student_id = ss.student_id
          LEFT JOIN sections sec ON ss.section_id = sec.section_id
          WHERE s.student_id = ? AND ss.status = 'active'";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student_info = $stmt->get_result()->fetch_assoc();

// Fetch recent activities
$activities_query = "SELECT 
    a.title,
    a.type,
    a.created_at as activity_date,
    sec.section_name,
    sub.subject_name,
    sub.subject_code,
    COALESCE(sas.points, 'Not submitted') as score,
    CASE 
        WHEN sas.submission_id IS NOT NULL THEN 'Submitted'
        WHEN a.due_date < NOW() THEN 'Overdue'
        ELSE 'Pending'
    END as status
FROM student_sections ss
JOIN sections sec ON ss.section_id = sec.section_id
JOIN section_subjects ssub ON sec.section_id = ssub.section_id
JOIN subjects sub ON ssub.subject_id = sub.id
JOIN activities a ON ssub.id = a.section_subject_id
LEFT JOIN student_activity_submissions sas ON a.activity_id = sas.activity_id 
    AND sas.student_id = ?
WHERE ss.student_id = ? 
    AND ss.status = 'active'
    AND ssub.status = 'active'
    AND ss.academic_year_id = (SELECT id FROM academic_years WHERE status = 'active' LIMIT 1)
ORDER BY a.created_at DESC
LIMIT 5";

$stmt = $db->prepare($activities_query);
$stmt->bind_param("ii", $student_id, $student_id);
$stmt->execute();
$recent_activities = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch upcoming assignments
$tasks_query = "SELECT 
    a.activity_id,
    a.title,
    a.due_date,
    sub.subject_name,
    sub.subject_code,
    ssub.schedule_day,
    ssub.schedule_time
FROM student_sections ss
JOIN sections sec ON ss.section_id = sec.section_id
JOIN section_subjects ssub ON sec.section_id = ssub.section_id
JOIN subjects sub ON ssub.subject_id = sub.id
JOIN activities a ON ssub.id = a.section_subject_id
WHERE ss.student_id = ?
    AND ss.status = 'active'
    AND ssub.status = 'active'
    AND ss.academic_year_id = (SELECT id FROM academic_years WHERE status = 'active' LIMIT 1)
    AND a.type = 'assignment'
    AND a.due_date > NOW()
    AND a.activity_id NOT IN (
        SELECT activity_id 
        FROM student_activity_submissions 
        WHERE student_id = ?
    )
ORDER BY a.due_date ASC
LIMIT 5";

$stmt = $db->prepare($tasks_query);
$stmt->bind_param("ii", $student_id, $student_id);
$stmt->execute();
$upcoming_tasks = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Calculate attendance rate
$attendance_query = "SELECT 
    COALESCE(
        COUNT(CASE WHEN a.status = 'present' THEN 1 END) * 100.0 / NULLIF(COUNT(*), 0),
        0
    ) as attendance_rate
FROM student_sections ss
JOIN section_subjects ssub ON ss.section_id = ssub.section_id
JOIN attendance a ON ssub.id = a.section_subject_id AND ss.student_id = a.student_id
WHERE ss.student_id = ?
    AND ss.status = 'active'
    AND ssub.status = 'active'
    AND ss.academic_year_id = (SELECT id FROM academic_years WHERE status = 'active' LIMIT 1)
    AND a.date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";

$stmt = $db->prepare($attendance_query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$attendance_result = $stmt->get_result()->fetch_assoc();
$attendance_rate = number_format($attendance_result['attendance_rate'] ?? 0, 1);

// Get course count and pending tasks in one query for stats cards
$stats_query = "SELECT 
    (
        SELECT COUNT(DISTINCT ssub.subject_id) 
        FROM student_sections ss
        JOIN section_subjects ssub ON ss.section_id = ssub.section_id
        WHERE ss.student_id = ? 
        AND ss.status = 'active'
        AND ssub.status = 'active'
        AND ss.academic_year_id = (SELECT id FROM academic_years WHERE status = 'active' LIMIT 1)
    ) as course_count,
    (
        SELECT COUNT(*) 
        FROM activities a
        JOIN section_subjects ssub ON a.section_subject_id = ssub.id
        JOIN student_sections ss ON ssub.section_id = ss.section_id
        WHERE ss.student_id = ?
        AND ss.status = 'active'
        AND ssub.status = 'active'
        AND ss.academic_year_id = (SELECT id FROM academic_years WHERE status = 'active' LIMIT 1)
        AND a.type = 'assignment'
        AND a.due_date > NOW()
        AND a.activity_id NOT IN (
            SELECT activity_id 
            FROM student_activity_submissions 
            WHERE student_id = ?
        )
    ) as pending_count";

// Execute stats query
$stmt = $db->prepare($stats_query);
$stmt->bind_param("iii", $student_id, $student_id, $student_id);
$stmt->execute();
$stats_result = $stmt->get_result()->fetch_assoc();

// Update student info with stats
$student_info['course_count'] = $stats_result['course_count'] ?? 0;
$student_info['pending_count'] = $stats_result['pending_count'] ?? 0;

// Calculate average grade
$grades_query = "SELECT 
    AVG(sas.points) as average_grade
FROM student_sections ss
JOIN sections sec ON ss.section_id = sec.section_id
JOIN section_schedules sched ON sec.section_id = sched.section_id
JOIN activities a ON sched.teacher_id = a.teacher_id
JOIN student_activity_submissions sas ON a.activity_id = sas.activity_id
WHERE ss.student_id = ?
    AND ss.status = 'active'
    AND sched.status = 'active'";

$stmt = $db->prepare($grades_query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$grades_result = $stmt->get_result()->fetch_assoc();
$average_grade = number_format($grades_result['average_grade'] ?? 0, 1);

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

function formatDueDate($date) {
    $dueDate = strtotime($date);
    $now = time();
    $diff = $dueDate - $now;
    
    if ($diff < 0) {
        return "Overdue";
    } elseif ($diff < 86400) {
        return "Today";
    } elseif ($diff < 172800) {
        return "Tomorrow";
    } else {
        return date('M j', $dueDate);
    }
}

function getActivityTypeClass($type) {
    $classes = [
        'assignment' => 'bg-primary',
        'quiz' => 'bg-warning',
        'activity' => 'bg-success'
    ];
    return $classes[$type] ?? 'bg-secondary';
}

function getActivityTypeIcon($type) {
    $icons = [
        'assignment' => 'fa-file-alt',
        'quiz' => 'fa-question-circle',
        'activity' => 'fa-tasks'
    ];
    return $icons[$type] ?? 'fa-circle';
}

function getStatusBadgeClass($status) {
    $classes = [
        'Submitted' => 'badge-success',
        'Pending' => 'badge-warning',
        'Overdue' => 'badge-danger'
    ];
    return $classes[$status] ?? 'badge-secondary';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Gov D.M. Camerino</title>
    
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
                <h1>Welcome, <?php echo htmlspecialchars($student_info['firstname'] ?? 'Student'); ?>!</h1>
                <p>Here's your learning overview</p>
            </div>

            <!-- Stats Cards -->
            <div class="row stats-cards">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <i class="fas fa-book-open"></i>
                            <h5>Current Courses</h5>
                            <h3><?php echo $student_info['course_count'] ?? 0; ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <i class="fas fa-tasks"></i>
                            <h5>Pending Tasks</h5>
                            <h3><?php echo $student_info['pending_count'] ?? 0; ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <i class="fas fa-chart-line"></i>
                            <h5>Average Grade</h5>
                            <h3><?php echo $average_grade; ?>%</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <i class="fas fa-calendar-check"></i>
                            <h5>Attendance</h5>
                            <h3><?php echo $attendance_rate; ?>%</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities and Upcoming Tasks -->
            <div class="row mt-4">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5>Recent Activities</h5>
                        </div>
                        <div class="card-body">
                            <?php foreach ($recent_activities as $activity): ?>
                                <div class="activity-item">
                                    <div class="activity-icon <?php echo getActivityTypeClass($activity['type']); ?>">
                                        <i class="fas <?php echo getActivityTypeIcon($activity['type']); ?>"></i>
                                    </div>
                                    <div class="activity-content">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1"><?php echo htmlspecialchars($activity['title']); ?></h6>
                                                <p class="mb-1 text-muted">
                                                    <?php echo htmlspecialchars($activity['subject_code']); ?> - 
                                                    <?php echo htmlspecialchars($activity['subject_name']); ?>
                                                </p>
                                            </div>
                                            <span class="badge <?php echo getStatusBadgeClass($activity['status']); ?>">
                                                <?php echo $activity['status']; ?>
                                            </span>
                                        </div>
                                        <div class="activity-meta">
                                            <small class="text-muted">
                                                <i class="far fa-clock mr-1"></i>
                                                <?php echo timeAgo($activity['activity_date']); ?>
                                            </small>
                                            <?php if ($activity['score'] !== 'Not submitted'): ?>
                                                <small class="text-muted ml-3">
                                                    <i class="fas fa-star mr-1"></i>
                                                    Score: <?php echo $activity['score']; ?>
                                                </small>
                                            <?php endif; ?>
                                        </div>
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
                            <h5>Upcoming Tasks</h5>
                        </div>
                        <div class="card-body">
                            <ul class="task-list">
                                <?php foreach ($upcoming_tasks as $task): ?>
                                    <li>
                                        <span class="task-title">
                                            <?php echo htmlspecialchars($task['title']); ?>
                                            <small class="d-block text-muted"><?php echo htmlspecialchars($task['course_name']); ?></small>
                                        </span>
                                        <span class="task-date">Due: <?php echo formatDueDate($task['due_date']); ?></span>
                                    </li>
                                <?php endforeach; ?>
                                <?php if (empty($upcoming_tasks)): ?>
                                    <li class="text-muted text-center">No upcoming tasks</li>
                                <?php endif; ?>
                            </ul>
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