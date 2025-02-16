<?php
session_start();
require_once('../db/dbConnector.php');

// Check if teacher is logged in
if (!isset($_SESSION['teacher_id'])) {
    header("Location: ../login.php");
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

// Add these functions after the existing PHP functions

function getWeeklyPerformanceData($db, $teacher_id) {
    $query = "
        SELECT 
            WEEK(sas.submitted_at) as week_number,
            AVG(sas.points) as average_score
        FROM student_activity_submissions sas
        JOIN activities a ON sas.activity_id = a.activity_id
        WHERE a.teacher_id = ?
            AND sas.submitted_at >= DATE_SUB(NOW(), INTERVAL 7 WEEK)
            AND sas.status = 'graded'
        GROUP BY WEEK(sas.submitted_at)
        ORDER BY week_number ASC
        LIMIT 7";
    
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $teacher_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $scores = [];
    $weeks = [];
    while ($row = $result->fetch_assoc()) {
        $scores[] = round($row['average_score'], 2);
        $weeks[] = 'Week ' . $row['week_number'];
    }
    
    return ['scores' => $scores, 'weeks' => $weeks];
}

function getAssignmentDistribution($db, $teacher_id) {
    $query = "
        SELECT 
            COUNT(CASE WHEN sas.status = 'submitted' OR sas.status = 'graded' THEN 1 END) as submitted,
            COUNT(CASE WHEN sas.status = 'pending' THEN 1 END) as in_progress,
            (
                SELECT COUNT(DISTINCT ss.student_id) * COUNT(DISTINCT a.activity_id) 
                FROM section_subjects sec 
                JOIN student_sections ss ON sec.section_id = ss.section_id
                CROSS JOIN activities a 
                WHERE sec.teacher_id = ? AND a.teacher_id = ?
            ) - COUNT(*) as not_started
        FROM student_activity_submissions sas
        JOIN activities a ON sas.activity_id = a.activity_id
        WHERE a.teacher_id = ?";
    
    $stmt = $db->prepare($query);
    $stmt->bind_param("iii", $teacher_id, $teacher_id, $teacher_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    
    return [
        intval($data['submitted']),
        intval($data['in_progress']),
        intval($data['not_started'])
    ];
}

function getClassDistribution($db, $teacher_id) {
    $query = "
        SELECT 
            s.section_name,
            COUNT(DISTINCT ss.student_id) as student_count
        FROM section_subjects sec
        JOIN sections s ON sec.section_id = s.section_id
        JOIN student_sections ss ON s.section_id = ss.section_id
        WHERE sec.teacher_id = ?
        AND sec.status = 'active'
        GROUP BY s.section_id, s.section_name
        ORDER BY s.section_name";
    
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $teacher_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $sections = [];
    $counts = [];
    while ($row = $result->fetch_assoc()) {
        $sections[] = $row['section_name'];
        $counts[] = intval($row['student_count']);
    }
    
    return ['sections' => $sections, 'counts' => $counts];
}

// Fetch data for charts
$performanceData = getWeeklyPerformanceData($db, $teacher_id);
$assignmentDistribution = getAssignmentDistribution($db, $teacher_id);
$classDistribution = getClassDistribution($db, $teacher_id);
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- Add ApexCharts CSS -->
    <link href="https://cdn.jsdelivr.net/npm/apexcharts@3.41.0/dist/apexcharts.min.css" rel="stylesheet">
    <link rel="icon" href="../images/light-logo.png">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/dashboard-shared.css">
    
    <style>
    /* Base styles */
    :root {
        --primary-color: #2196F3;
        --primary-dark: #1976D2;
        --secondary-color: #64B5F6;
        --accent-color: #0D47A1;
        --card-spacing: 1rem;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background: #f8f9fa;
        overflow-x: hidden;
        font-size: 14px;
    }

    @media (min-width: 768px) {
        body {
            font-size: 16px;
        }
    }

    /* Sidebar & Navigation */
    .dashboard-container {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    @media (min-width: 992px) {
        .dashboard-container {
            flex-direction: row;
        }
    }

    .main-content {
        padding: 0.75rem;
        width: 100%;
    }

    @media (min-width: 768px) {
        .main-content {
            padding: 1.5rem;
        }
    }

    /* Welcome Section */
    .welcome-section {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        color: white;
        padding: 1rem;
        border-radius: 12px;
        margin-bottom: 1rem;
    }

    .welcome-section h1 {
        font-size: 1.25rem;
        margin-bottom: 0.25rem;
    }

    @media (min-width: 768px) {
        .welcome-section {
            padding: 2rem;
            margin-bottom: 1.5rem;
        }
        .welcome-section h1 {
            font-size: 2rem;
        }
    }

    /* Metric Cards Grid */
    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    @media (min-width: 768px) {
        .metrics-grid {
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
    }

    .metric-card {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        color: white;
        border-radius: 12px;
        padding: 1rem;
        position: relative;
        overflow: hidden;
        min-height: 120px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .metric-value {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
        z-index: 1;
    }

    .metric-label {
        font-size: 0.875rem;
        opacity: 0.9;
        z-index: 1;
    }

    .metric-card .icon {
        position: absolute;
        right: -10px;
        bottom: -10px;
        font-size: 3.5rem;
        opacity: 0.2;
    }

    /* Charts Section */
    .chart-container {
        background: white;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        overflow: hidden;
    }

    .chart-title {
        font-size: 1rem;
        margin-bottom: 1rem;
        color: #333;
    }

    /* Activity Cards */
    .activity-card {
        background: white;
        border-radius: 12px;
        padding: 0.75rem;
        margin-bottom: 0.75rem;
    }

    .activity-header {
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .activity-icon {
        width: 32px;
        height: 32px;
        min-width: 32px;
        border-radius: 50%;
        background: #f5f5f5;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 0.75rem;
    }

    /* Quick Actions */
    .quick-actions-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
        padding: 0.75rem;
    }

    @media (min-width: 768px) {
        .quick-actions-grid {
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            padding: 1rem;
        }
    }

    .quick-action-item {
        background: white;
        padding: 1rem;
        border-radius: 12px;
        text-align: center;
        transition: all 0.3s ease;
        border: 1px solid #eee;
    }

    .quick-action-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    /* Progress Bars */
    .progress {
        height: 6px;
        border-radius: 3px;
    }

    /* Responsive Charts */
    #performanceChart, #assignmentPieChart, #classDistributionChart {
        width: 100%;
        min-height: 250px;
    }

    @media (min-width: 768px) {
        #performanceChart, #assignmentPieChart, #classDistributionChart {
            min-height: 300px;
        }
    }

    /* Card Shadows and Hover Effects */
    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    /* Loading States */
    .loading {
        opacity: 0.7;
        pointer-events: none;
    }

    /* Scrollable Areas */
    .scrollable-content {
        max-height: 400px;
        overflow-y: auto;
        scrollbar-width: thin;
    }

    .scrollable-content::-webkit-scrollbar {
        width: 6px;
    }

    .scrollable-content::-webkit-scrollbar-thumb {
        background: #ddd;
        border-radius: 3px;
    }

    /* Update gradient backgrounds */
    .welcome-section {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    }

    /* Update metric cards colors */
    .metric-card {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    }

    .metric-card:nth-child(1) {
        background: linear-gradient(135deg, #2196F3, #1976D2);
    }

    .metric-card:nth-child(2) {
        background: linear-gradient(135deg, #1E88E5, #1565C0);
    }

    .metric-card:nth-child(3) {
        background: linear-gradient(135deg, #42A5F5, #1976D2);
    }

    .metric-card:nth-child(4) {
        background: linear-gradient(135deg, #64B5F6, #1E88E5);
    }

    /* Update progress bar color */
    .progress-bar {
        background-color: var(--primary-color) !important;
    }

    /* Update quick action hover states */
    .quick-action-item:hover {
        background: var(--primary-color);
        color: white;
    }

    /* Update chart colors */
    .apexcharts-series path {
        stroke: var(--primary-color);
    }

    /* Update card hover effects */
    .card:hover {
        border-color: var(--primary-color);
    }

    /* Update badges */
    .badge-primary {
        background-color: var(--primary-color);
    }

    .badge-success {
        background-color: var(--secondary-color);
    }
    </style>
</head>
<body>
    <?php include 'includes/navigation.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Welcome Section -->
            <div class="welcome-section mb-4">
                <h1>Welcome back, <?php echo htmlspecialchars($teacher['firstname'] ?? 'Teacher'); ?>!</h1>
                <p>Here's your teaching overview for today</p>
            </div>

            <!-- Live Metrics Row -->
            <div class="metrics-grid">
                <div class="metric-card">
                    <i class="fas fa-chalkboard icon"></i>
                    <div class="metric-value"><?php echo $class_count; ?></div>
                    <div class="metric-label">Active Classes</div>
                </div>
                <div class="metric-card" style="background: linear-gradient(135deg, #2196F3, #1976D2);">
                    <i class="fas fa-users icon"></i>
                    <div class="metric-value"><?php echo $student_count; ?></div>
                    <div class="metric-label">Total Students</div>
                </div>
                <div class="metric-card" style="background: linear-gradient(135deg, #FF9800, #F57C00);">
                    <i class="fas fa-tasks icon"></i>
                    <div class="metric-value"><?php echo $assignment_count; ?></div>
                    <div class="metric-label">Active Assignments</div>
                </div>
                <div class="metric-card" style="background: linear-gradient(135deg, #9C27B0, #7B1FA2);">
                    <i class="fas fa-chart-line icon"></i>
                    <div class="metric-value"><?php echo $average_performance; ?>%</div>
                    <div class="metric-label">Class Performance</div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="charts-row">
                <div class="row g-0">
                    <div class="col-12 col-lg-8 chart-wrapper">
                        <div class="chart-container">
                            <h5 class="chart-title">Student Performance Trends</h5>
                            <div id="performanceChart"></div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4 chart-wrapper">
                        <div class="chart-container">
                            <h5 class="chart-title">All Activities Distribution</h5>
                            <div id="assignmentPieChart"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity Progress -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="progress-card">
                        <h5>Recent Activities Completion Rate</h5>
                        <?php foreach($recent_activities as $index => $activity): 
                            if($index < 3): // Show only 3 most recent
                                // Calculate completion percentage
                                $completion = isset($activity['submission_count']) && isset($activity['total_students']) 
                                    ? round(($activity['submission_count'] / $activity['total_students']) * 100) 
                                    : 0;
                        ?>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <small><?php echo htmlspecialchars(truncateText($activity['title'], 30)); ?></small>
                                <small><?php echo $completion; ?>%</small>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-primary" role="progressbar" 
                                     style="width: <?php echo $completion; ?>%" 
                                     aria-valuenow="<?php echo $completion; ?>" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100"></div>
                            </div>
                        </div>
                        <?php 
                            endif;
                        endforeach; ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="progress-card">
                        <h5>Class Distribution</h5>
                        <div id="classDistributionChart"></div>
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
    <!-- Add ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    
    <script>
    // Performance Chart
    var performanceOptions = {
        series: [{
            name: 'Average Score',
            data: <?php echo json_encode($performanceData['scores']); ?>
        }],
        chart: {
            height: 350,
            type: 'line',
            toolbar: {
                show: false
            },
            zoom: {
                enabled: false
            },
            animations: {
                enabled: true
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    legend: {
                        position: 'bottom',
                        offsetX: -10,
                        offsetY: 0
                    }
                }
            }]
        },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        colors: ['#2196F3'],
        xaxis: {
            categories: <?php echo json_encode($performanceData['weeks']); ?>
        },
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'light',
                type: 'horizontal',
                shadeIntensity: 0.5,
                gradientToColors: ['#1976D2'],
                opacityFrom: 1,
                opacityTo: 0.7,
                stops: [0, 100]
            }
        }
    };

    var performanceChart = new ApexCharts(document.querySelector("#performanceChart"), performanceOptions);
    performanceChart.render();

    // Assignment Distribution Pie Chart
    var pieOptions = {
        series: <?php echo json_encode($assignmentDistribution); ?>,
        chart: {
            type: 'donut',
            height: 350,
            animations: {
                enabled: true
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        height: 300
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        },
        labels: ['Submitted', 'In Progress', 'Not Started'],
        colors: ['#2196F3', '#64B5F6', '#1976D2'],
        legend: {
            position: 'bottom'
        }
    };

    var assignmentPieChart = new ApexCharts(document.querySelector("#assignmentPieChart"), pieOptions);
    assignmentPieChart.render();

    // Class Distribution Chart
    var classDistOptions = {
        series: [{
            data: <?php echo json_encode($classDistribution['counts']); ?>
        }],
        chart: {
            type: 'bar',
            height: 250,
            toolbar: {
                show: false
            },
            animations: {
                enabled: true
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        height: 200
                    }
                }
            }]
        },
        colors: ['#2196F3'],
        plotOptions: {
            bar: {
                borderRadius: 4,
                horizontal: true,
            }
        },
        dataLabels: {
            enabled: false
        },
        xaxis: {
            categories: <?php echo json_encode($classDistribution['sections']); ?>,
        }
    };

    var classDistChart = new ApexCharts(document.querySelector("#classDistributionChart"), classDistOptions);
    classDistChart.render();
    </script>
</body>
</html>
