<?php
if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

require_once('../db/dbConnector.php');
$db = new DbConnector();
$admin_id = $_SESSION['admin_id'];

// Get admin info
$query = "SELECT * FROM admin WHERE admin_id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

if (!$admin) {
    session_destroy();
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Admin Dashboard - Gov D.M. Camerino</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/admin-styles.css">
    <?php if (isset($additional_css)): ?>
    <style><?php echo $additional_css; ?></style>
    <?php endif; ?>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="../images/Logo.png" alt="Logo">
            <h3>Admin Panel</h3>
        </div>
        <ul class="menu-items">
            <li><a href="admin_dashboard.php" <?php echo $current_page === 'dashboard' ? 'class="active"' : ''; ?>><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
            <li><a href="manage_students.php" <?php echo $current_page === 'students' ? 'class="active"' : ''; ?>><i class="fas fa-users"></i> <span>Students</span></a></li>
            <li><a href="manage_teachers.php" <?php echo $current_page === 'teachers' ? 'class="active"' : ''; ?>><i class="fas fa-chalkboard-teacher"></i> <span>Teachers</span></a></li>
            <li><a href="manage_subjects.php" <?php echo $current_page === 'subjects' ? 'class="active"' : ''; ?>><i class="fas fa-book"></i> <span>Subjects</span></a></li>
            <li><a href="settings.php" <?php echo $current_page === 'settings' ? 'class="active"' : ''; ?>><i class="fas fa-cog"></i> <span>Settings</span></a></li>
            <li><a href="#" onclick="confirmLogout(event)"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="top-bar">
            <h2><?php echo isset($page_title) ? $page_title : 'Dashboard'; ?></h2>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($admin['firstname']); ?></span>
            </div>
        </div>
    </div>
</body>
</html>
