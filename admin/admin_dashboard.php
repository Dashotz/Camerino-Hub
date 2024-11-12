<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
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
    header("Location: login.php");
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
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    
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
                <h1>Welcome, <?php echo htmlspecialchars($admin['firstname'] ?? 'Admin'); ?>!</h1>
                <p>Here's your administration overview</p>
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
    </script>
</body>
</html>
