<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

$db = new DbConnector();

// Get admin info
$admin_id = $_SESSION['admin_id'];
$admin_query = "SELECT * FROM admin WHERE admin_id = ?";
$stmt = $db->prepare($admin_query);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();

if (!$admin) {
    // Handle case where admin data couldn't be found
    session_destroy();
    header("Location: ../login.php");
    exit();
}

// Get total counts
$counts_query = "SELECT
    (SELECT COUNT(*) FROM student WHERE status = 'active') as student_count,
    (SELECT COUNT(*) FROM teacher WHERE status = 'active') as teacher_count,
    (SELECT COUNT(*) FROM sections WHERE status = 'active') as section_count,
    (SELECT COUNT(*) FROM subjects) as subject_count";

$counts = $db->query($counts_query)->fetch_assoc();

// Get recent activities (combining student and teacher logins)
$recent_activities_query = "SELECT 
    'Student' as user_type,
    CONCAT(s.firstname, ' ', s.lastname) as name,
    l.status,
    l.created_at as activity_time
FROM student_login_logs l
JOIN student s ON l.student_id = s.student_id
WHERE l.created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)

UNION ALL

SELECT 
    'Teacher' as user_type,
    CONCAT(t.firstname, ' ', t.lastname) as name,
    l.status,
    l.created_at as activity_time
FROM teacher_login_logs l
JOIN teacher t ON l.teacher_id = t.teacher_id
WHERE l.created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)

UNION ALL

SELECT 
    'Admin' as user_type,
    CONCAT(a.firstname, ' ', a.lastname) as name,
    l.status,
    l.created_at as activity_time
FROM admin_login_logs l
JOIN admin a ON l.admin_id = a.admin_id
WHERE l.created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)

ORDER BY activity_time DESC
LIMIT 10";

$recent_activities = $db->query($recent_activities_query);

// Get active users count
$active_users_query = "SELECT 
    (SELECT COUNT(*) FROM student WHERE user_online = 1) +
    (SELECT COUNT(*) FROM teacher WHERE status = 'active') as active_users";

$active_users = $db->query($active_users_query)->fetch_assoc()['active_users'];

// Add after existing queries

// Get user activity trends
function getUserActivityTrends($db) {
    $query = "SELECT 
        DATE(created_at) as date,
        COUNT(CASE WHEN status = 'success' THEN 1 END) as logins,
        COUNT(CASE WHEN status = 'failed' THEN 1 END) as failed_attempts
    FROM (
        SELECT created_at, status FROM student_login_logs
        UNION ALL
        SELECT created_at, status FROM teacher_login_logs
        UNION ALL
        SELECT created_at, status FROM admin_login_logs
    ) as combined_logs
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    GROUP BY DATE(created_at)
    ORDER BY date ASC";
    
    return $db->query($query)->fetch_all(MYSQLI_ASSOC);
}

// Get student enrollment trends
function getEnrollmentTrends($db) {
    $query = "SELECT 
        DATE(created_at) as date,
        COUNT(*) as enrollments
    FROM student_sections
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    GROUP BY DATE(created_at)
    ORDER BY date ASC";
    
    return $db->query($query)->fetch_all(MYSQLI_ASSOC);
}

// Get subject distribution
function getSubjectDistribution($db) {
    $query = "SELECT 
        s.subject_name,
        COUNT(DISTINCT ss.section_id) as section_count
    FROM subjects s
    LEFT JOIN section_subjects ss ON s.id = ss.subject_id
    GROUP BY s.id, s.subject_name
    ORDER BY section_count DESC
    LIMIT 10";
    
    return $db->query($query)->fetch_all(MYSQLI_ASSOC);
}

// Get system performance metrics
function getSystemMetrics($db) {
    $query = "SELECT
        (SELECT COUNT(*) FROM student_activity_submissions WHERE status = 'graded') as graded_submissions,
        (SELECT COUNT(*) FROM activities WHERE status = 'active') as active_activities,
        (SELECT COUNT(*) FROM announcements WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)) as recent_announcements,
        (SELECT COUNT(*) FROM security_violations WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)) as security_violations";
    
    return $db->query($query)->fetch_assoc();
}

// Fetch data for charts
$activityTrends = getUserActivityTrends($db);
$enrollmentTrends = getEnrollmentTrends($db);
$subjectDistribution = getSubjectDistribution($db);
$systemMetrics = getSystemMetrics($db);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Gov D.M. Camerino</title>
    
    <!-- External CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/apexcharts@3.41.0/dist/apexcharts.min.css" rel="stylesheet">
    <link rel="icon" href="../images/light-logo.png">
    
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
                <h1>Welcome, <?php echo htmlspecialchars($admin['firstname'] ?? 'Admin'); ?>!</h1>
                <p>Here's your administration overview</p>
            </div>

            <!-- Charts Section -->
            <div class="row mb-4">
                <div class="col-12 col-lg-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5>User Activity Trends</h5>
                            <div class="btn-group">
                                <button class="btn btn-sm btn-outline-primary active" onclick="updateTimeRange('week')">Week</button>
                                <button class="btn btn-sm btn-outline-primary" onclick="updateTimeRange('month')">Month</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="activityChart"></div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5>System Metrics</h5>
                        </div>
                        <div class="card-body">
                            <div id="metricsChart"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Enrollment Trends</h5>
                        </div>
                        <div class="card-body">
                            <div id="enrollmentChart"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Subject Distribution</h5>
                        </div>
                        <div class="card-body">
                            <div id="subjectChart"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row stats-cards">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <i class="fas fa-users"></i>
                            <h5>Total Students</h5>
                            <h3><?php echo number_format($counts['student_count']); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <i class="fas fa-chalkboard-teacher"></i>
                            <h5>Total Teachers</h5>
                            <h3><?php echo number_format($counts['teacher_count']); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <i class="fas fa-book"></i>
                            <h5>Total Subjects</h5>
                            <h3><?php echo number_format($counts['subject_count']); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <i class="fas fa-users-cog"></i>
                            <h5>Active Users</h5>
                            <h3><?php echo number_format($active_users); ?></h3>
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
                            <?php if ($recent_activities && $recent_activities->num_rows > 0): ?>
                                <?php while($activity = $recent_activities->fetch_assoc()): ?>
                                    <div class="activity-item">
                                        <i class="fas <?php 
                                            echo $activity['user_type'] === 'Student' ? 'fa-user-graduate' : 
                                                ($activity['user_type'] === 'Teacher' ? 'fa-chalkboard-teacher' : 'fa-user-shield'); 
                                        ?>"></i>
                                        <div class="activity-details">
                                            <h6><?php echo htmlspecialchars($activity['name']); ?></h6>
                                            <p><?php echo $activity['user_type']; ?> <?php echo $activity['status']; ?></p>
                                            <small><?php echo date('M d, Y h:i A', strtotime($activity['activity_time'])); ?></small>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
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
                                <a href="manage_students.php" class="quick-action-item">
                                    <i class="fas fa-user-plus"></i>
                                    <span>Add New Student</span>
                                </a>
                                <a href="manage_teachers.php" class="quick-action-item">
                                    <i class="fas fa-user-tie"></i>
                                    <span>Add New Teacher</span>
                                </a>
                                <a href="manage_subjects.php" class="quick-action-item">
                                    <i class="fas fa-book-medical"></i>
                                    <span>Add New Subject</span>
                                </a>
                                <a href="reports.php" class="quick-action-item">
                                    <i class="fas fa-chart-line"></i>
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
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    
    <script>
    function confirmLogout(event) {
        event.preventDefault();
        
        Swal.fire({
            title: 'Ready to Leave?',
            text: "Are you sure you want to logout?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, logout',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'logout.php';
            }
        });
    }

    // Activity Trends Chart
    var activityOptions = {
        series: [{
            name: 'Successful Logins',
            data: <?php echo json_encode(array_column($activityTrends, 'logins')); ?>
        }, {
            name: 'Failed Attempts',
            data: <?php echo json_encode(array_column($activityTrends, 'failed_attempts')); ?>
        }],
        chart: {
            type: 'area',
            height: 350,
            toolbar: {
                show: false
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth'
        },
        xaxis: {
            categories: <?php echo json_encode(array_column($activityTrends, 'date')); ?>,
            type: 'datetime'
        },
        colors: ['#2196F3', '#FF5252'],
        tooltip: {
            x: {
                format: 'dd MMM yyyy'
            }
        }
    };

    // System Metrics Donut Chart
    var metricsOptions = {
        series: [
            <?php echo $systemMetrics['graded_submissions']; ?>,
            <?php echo $systemMetrics['active_activities']; ?>,
            <?php echo $systemMetrics['recent_announcements']; ?>,
            <?php echo $systemMetrics['security_violations']; ?>
        ],
        chart: {
            type: 'donut',
            height: 320
        },
        labels: ['Graded Submissions', 'Active Activities', 'Recent Announcements', 'Security Alerts'],
        colors: ['#2196F3', '#4CAF50', '#FFC107', '#FF5252'],
        legend: {
            position: 'bottom'
        }
    };

    // Enrollment Trends Chart
    var enrollmentOptions = {
        series: [{
            name: 'New Enrollments',
            data: <?php echo json_encode(array_column($enrollmentTrends, 'enrollments')); ?>
        }],
        chart: {
            type: 'bar',
            height: 350
        },
        plotOptions: {
            bar: {
                borderRadius: 4
            }
        },
        xaxis: {
            categories: <?php echo json_encode(array_column($enrollmentTrends, 'date')); ?>,
            type: 'datetime'
        },
        colors: ['#2196F3']
    };

    // Subject Distribution Chart
    var subjectOptions = {
        series: [{
            name: 'Sections',
            data: <?php echo json_encode(array_column($subjectDistribution, 'section_count')); ?>
        }],
        chart: {
            type: 'bar',
            height: 350
        },
        plotOptions: {
            bar: {
                horizontal: true,
                borderRadius: 4
            }
        },
        xaxis: {
            categories: <?php echo json_encode(array_column($subjectDistribution, 'subject_name')); ?>
        },
        colors: ['#2196F3']
    };

    // Initialize Charts
    var activityChart = new ApexCharts(document.querySelector("#activityChart"), activityOptions);
    var metricsChart = new ApexCharts(document.querySelector("#metricsChart"), metricsOptions);
    var enrollmentChart = new ApexCharts(document.querySelector("#enrollmentChart"), enrollmentOptions);
    var subjectChart = new ApexCharts(document.querySelector("#subjectChart"), subjectOptions);

    activityChart.render();
    metricsChart.render();
    enrollmentChart.render();
    subjectChart.render();

    // Time range update function
    function updateTimeRange(range) {
        // Add AJAX call to fetch new data based on range
        // Update charts accordingly
    }
    </script>
</body>
</html>
