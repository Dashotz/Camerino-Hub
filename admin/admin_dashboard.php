<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Get admin data
require_once('../db/dbConnector.php');
$db = new DbConnector();
$admin_id = $_SESSION['admin_id'];

// Get counts for dashboard cards
$stats = array();

// Get total students count
$query = "SELECT COUNT(*) as total FROM student";
$result = $db->query($query);
$stats['students'] = $result->fetch_assoc()['total'];

// Get total teachers count
$query = "SELECT COUNT(*) as total FROM teacher";
$result = $db->query($query);
$stats['teachers'] = $result->fetch_assoc()['total'];

// Get total subjects count
$query = "SELECT COUNT(*) as total FROM subjects";
$result = $db->query($query);
$stats['subjects'] = $result->fetch_assoc()['total'];

// Get active users count
$query = "SELECT COUNT(*) as total FROM active_sessions";
$result = $db->query($query);
$stats['active_users'] = $result->fetch_assoc()['total'];

// Get recent activities (modified query)
$query = "SELECT 
    'Student Login' as activity_type,
    CONCAT(s.firstname, ' ', s.lastname) as name,
    l.created_at as activity_time
FROM login_logs l
JOIN student s ON s.student_id = l.student_id
WHERE l.status = 'success'
UNION ALL
SELECT 
    'Admin Login' as activity_type,
    CONCAT(a.firstname, ' ', a.lastname) as name,
    al.created_at as activity_time
FROM admin_login_logs al
JOIN admin a ON a.admin_id = al.admin_id
WHERE al.status = 'success'
ORDER BY activity_time DESC
LIMIT 5";

$recent_activities = $db->query($query);

// Get admin info
$query = "SELECT * FROM admin WHERE admin_id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Gov D.M. Camerino</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #f4f6f9;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100%;
            width: 250px;
            background: #2c3e50;
            padding: 20px;
            transition: all 0.3s ease;
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            gap: 15px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }

        .sidebar-header img {
            width: 40px;
            height: 40px;
        }

        .sidebar-header h3 {
            color: white;
            font-size: 1.2rem;
        }

        .menu-items {
            list-style: none;
        }

        .menu-items li {
            margin-bottom: 5px;
        }

        .menu-items a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 15px;
            color: #ecf0f1;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .menu-items a:hover, .menu-items a.active {
            background: rgba(255,255,255,0.1);
            transform: translateX(5px);
        }

        /* Main Content Styles */
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }

        .top-bar {
            background: white;
            padding: 15px 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        /* Dashboard Cards */
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .card-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .card-title {
            font-size: 0.9rem;
            color: #7f8c8d;
        }

        .card-value {
            font-size: 1.8rem;
            font-weight: 600;
            color: #2c3e50;
        }

        /* Recent Activity Section */
        .recent-activity {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .activity-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .activity-list {
            list-style: none;
        }

        .activity-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid #ecf0f1;
        }

        .activity-icon {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .activity-details h4 {
            font-size: 0.9rem;
            color: #2c3e50;
        }

        .activity-details p {
            font-size: 0.8rem;
            color: #7f8c8d;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
                padding: 10px;
            }

            .sidebar-header h3, .menu-items span {
                display: none;
            }

            .main-content {
                margin-left: 70px;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="../images/Logo.png" alt="Logo">
            <h3>Admin Panel</h3>
        </div>
        <ul class="menu-items">
            <li><a href="admin_dashboard.php" class="active"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
            <li><a href="manage_students.php"><i class="fas fa-users"></i> <span>Students</span></a></li>
            <li><a href="manage_teachers.php"><i class="fas fa-chalkboard-teacher"></i> <span>Teachers</span></a></li>
            <li><a href="manage_subjects.php"><i class="fas fa-book"></i> <span>Subjects</span></a></li>
            <li><a href="settings.php"><i class="fas fa-cog"></i> <span>Settings</span></a></li>
            <li>
                <a href="#" onclick="confirmLogout(event)">
                    <i class="fas fa-sign-out-alt"></i> 
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="top-bar">
            <h2>Dashboard</h2>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($admin['firstname']); ?></span>
                <img src="../images/admin.png" alt="Admin">
            </div>
        </div>

        <!-- Dashboard Cards -->
        <div class="dashboard-cards">
            <div class="card">
                <div class="card-header">
                    <div class="card-icon" style="background: #3498db;">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <div class="card-title">Total Students</div>
                <div class="card-value"><?php echo $stats['students']; ?></div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-icon" style="background: #2ecc71;">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                </div>
                <div class="card-title">Total Teachers</div>
                <div class="card-value"><?php echo $stats['teachers']; ?></div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-icon" style="background: #e74c3c;">
                        <i class="fas fa-book"></i>
                    </div>
                </div>
                <div class="card-title">Total Subjects</div>
                <div class="card-value"><?php echo $stats['subjects']; ?></div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-icon" style="background: #f1c40f;">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <div class="card-title">Active Users</div>
                <div class="card-value"><?php echo $stats['active_users']; ?></div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="recent-activity">
            <div class="activity-header">
                <h3>Recent Activity</h3>
                <a href="#" style="color: #3498db;">View All</a>
            </div>
            <ul class="activity-list">
                <?php if ($recent_activities && $recent_activities->num_rows > 0): ?>
                    <?php while($activity = $recent_activities->fetch_assoc()): ?>
                        <li class="activity-item">
                            <div class="activity-icon" style="background: #3498db;">
                                <i class="fas <?php echo ($activity['activity_type'] == 'Student Login') ? 'fa-user-graduate' : 'fa-user-shield'; ?>"></i>
                            </div>
                            <div class="activity-details">
                                <h4><?php echo htmlspecialchars($activity['activity_type']); ?></h4>
                                <p><?php echo htmlspecialchars($activity['name']); ?></p>
                                <small><?php 
                                    $activity_time = new DateTime($activity['activity_time']);
                                    $now = new DateTime();
                                    $interval = $activity_time->diff($now);
                                    
                                    if ($interval->d > 0) {
                                        echo $interval->d . ' days ago';
                                    } elseif ($interval->h > 0) {
                                        echo $interval->h . ' hours ago';
                                    } elseif ($interval->i > 0) {
                                        echo $interval->i . ' minutes ago';
                                    } else {
                                        echo 'Just now';
                                    }
                                ?></small>
                            </div>
                        </li>
                    <?php endwhile; ?>
                <?php else: ?>
                    <li class="activity-item">
                        <div class="activity-details">
                            <h4>No recent activities</h4>
                        </div>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    // Welcome Alert
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: 'Welcome Back, <?php echo htmlspecialchars($admin['firstname']); ?>!',
            text: 'You are now logged in to the admin dashboard.',
            icon: 'success',
            confirmButtonColor: '#3498db',
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false,
            customClass: {
                popup: 'animated fadeInDown'
            }
        });
    });

    // Add this if you want to show alerts for notifications
    function showNotification(title, message, icon = 'info') {
        Swal.fire({
            title: title,
            text: message,
            icon: icon,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    }

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
            cancelButtonText: 'Cancel',
            customClass: {
                popup: 'animated fadeInDown'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                Swal.fire({
                    title: 'Logging out...',
                    text: 'Please wait',
                    icon: 'info',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Redirect to logout script
                window.location.href = 'logout.php';
            }
        });
    }
    </script>
</body>
</html>
