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
$class_id = $_GET['id'] ?? 0;

// Fetch class details with related information
$query = "
    SELECT 
        ss.id as class_id,
        sec.section_name,
        sec.grade_level,
        s.subject_code,
        s.subject_name,
        ss.schedule_day,
        ss.schedule_time,
        ss.enrollment_code,
        COUNT(DISTINCT st_sec.student_id) as student_count,
        COALESCE(AVG(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) * 100, 0) as attendance_rate,
        COALESCE(AVG(sas.points), 0) as average_performance
    FROM section_subjects ss
    JOIN sections sec ON ss.section_id = sec.section_id
    JOIN subjects s ON ss.subject_id = s.id
    LEFT JOIN student_sections st_sec ON sec.section_id = st_sec.section_id 
        AND st_sec.academic_year_id = ss.academic_year_id
    LEFT JOIN attendance a ON ss.id = a.section_subject_id
    LEFT JOIN student_activity_submissions sas ON st_sec.student_id = sas.student_id
    WHERE ss.id = ? AND ss.teacher_id = ? AND ss.status = 'active'
    GROUP BY ss.id";

$stmt = $db->prepare($query);
$stmt->bind_param("ii", $class_id, $teacher_id);
$stmt->execute();
$class = $stmt->get_result()->fetch_assoc();

if (!$class) {
    header("Location: manage_classes.php");
    exit();
}

// Add this after line 13 (after getting $class_id)
function generateEnrollmentCode($db, $class_id) {
    // Generate a unique code
    do {
        $code = 'CMRH' . str_pad(mt_rand(1000, 9999), 4, '0', STR_PAD_LEFT);
        $check = $db->prepare("SELECT id FROM section_subjects WHERE enrollment_code = ?");
        $check->bind_param("s", $code);
        $check->execute();
    } while ($check->get_result()->num_rows > 0);
    
    // Update the section_subject with the new code
    $update = $db->prepare("UPDATE section_subjects SET enrollment_code = ? WHERE id = ?");
    $update->bind_param("si", $code, $class_id);
    $update->execute();
    
    return $code;
}

// Check if we need to generate a new enrollment code
if (!isset($class['enrollment_code'])) {
    $class['enrollment_code'] = generateEnrollmentCode($db, $class_id);
}

// Fetch students in this class
$students_query = "
    SELECT 
        s.student_id,
        s.firstname,
        s.lastname,
        COALESCE(AVG(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) * 100, 0) as attendance_rate,
        COALESCE(AVG(sas.points), 0) as performance
    FROM student s
    JOIN student_sections ss ON s.student_id = ss.student_id
    JOIN section_subjects secsubj ON ss.section_id = secsubj.section_id 
        AND ss.academic_year_id = secsubj.academic_year_id
    LEFT JOIN attendance a ON s.student_id = a.student_id 
        AND a.section_subject_id = ?
    LEFT JOIN activities act ON act.section_subject_id = secsubj.id
    LEFT JOIN student_activity_submissions sas ON s.student_id = sas.student_id 
        AND sas.activity_id = act.activity_id
    WHERE secsubj.id = ?
    GROUP BY s.student_id
    ORDER BY s.lastname, s.firstname";

$stmt = $db->prepare($students_query);
$stmt->bind_param("ii", $class_id, $class_id);
$stmt->execute();
$students = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Details - <?php echo htmlspecialchars($class['section_name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="css/dashboard-shared.css" rel="stylesheet">
	<link rel="icon" href="../images/light-logo.png">
    <style>
        .container {
            padding: 20px;
            margin-left: 250px;
            width: calc(100% - 250px);
            transition: all 0.3s;
        }

        .class-header {
            background: #fff;
            border-radius: 10px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .schedule-badge {
            background: #f8f9fa;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            margin-right: 1rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .stat-card {
            background: #fff;
            padding: 1.5rem;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 600;
            color: #2c3e50;
        }

        .student-card {
            background: #fff;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .student-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }

        .student-info {
            flex: 1;
        }

        .student-stats {
            display: flex;
            gap: 2rem;
        }

        .performance-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.875rem;
        }

        .performance-good { background: #e8f8f3; color: #2ecc71; }
        .performance-average { background: #fdf3e8; color: #f39c12; }
        .performance-poor { background: #fde8e8; color: #e74c3c; }

        .enrollment-code {
            text-align: left;
            padding: 1rem;
        }

        .code-display {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0.5rem 0;
        }

        .code-display span {
            font-size: 1.2rem;
            font-weight: 600;
            font-family: monospace;
            background: #f8f9fa;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            flex: 1;
        }

        .code-display button {
            padding: 0.25rem 0.5rem;
        }

        .code-display button:hover {
            background: #e9ecef;
        }

        .navbar {
            padding: 0;
            min-height: 56px;
            width: 100%;
            display: flex;
            align-items: center;
        }

        .container-fluid {
            width: 100%;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            padding-left: 0;
            margin-left: 0;
        }

        .navbar-logo {
            height: 36px;
            width: auto;
            margin-left: 0;
        }

        .nav-items-container {
            margin-left: auto;
            padding-right: 1rem;
        }

        /* Profile section adjustments */
        #profileDropdown {
            padding: 0.25rem;
            gap: 0.5rem;
        }

        /* Responsive adjustments */
        @media (max-width: 1024px) {
            .container-fluid {
                width: calc(100% - 200px);
                margin-left: 200px;
                padding: 0 0.75rem;
            }
        }

        @media (max-width: 768px) {
            .container-fluid {
                width: calc(100% - 40px);
                margin-left: 40px;
                padding: 0 0.5rem;
            }

            .navbar {
                min-height: 48px;
                padding: 0.25rem;
            }

            .navbar-logo {
                height: 32px;
            }

            .nav-items-container {
                gap: 0.5rem;
            }

            #profileDropdown {
                padding: 0.125rem;
                gap: 0.25rem;
            }

            .profile-img {
                width: 28px;
                height: 28px;
            }
        }

        @media (max-width: 480px) {
            .container-fluid {
                padding: 0 0.25rem;
            }

            .navbar-logo {
                height: 28px;
            }

            .nav-items-container {
                gap: 0.25rem;
            }

            #profileDropdown {
                padding: 0.125rem;
            }

            .profile-img {
                width: 24px;
                height: 24px;
            }
        }

        /* Ensure dropdowns stay within viewport */
        .dropdown-menu {
            position: absolute;
            right: 0;
            top: 100%;
            margin-top: 0.25rem;
        }

        @media (max-width: 360px) {
            .notifications-dropdown {
                width: 280px;
                right: -70px;
            }

            .dropdown-menu-right {
                right: 0;
            }
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            background: white;
            border: 1px solid rgba(0,0,0,.15);
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,.175);
            z-index: 1000;
            min-width: 10rem;
            margin-top: 0.5rem;
        }

        .dropdown-menu.show {
            display: block;
        }

        .notifications-dropdown {
            width: 300px;
            padding: 0;
            max-height: 400px;
            overflow-y: auto;
        }

        .notification-item {
            display: flex;
            align-items: start;
            padding: 8px;
            gap: 12px;
        }

        .notification-item i {
            font-size: 1.2rem;
            padding-top: 4px;
        }

        .notification-content {
            flex: 1;
        }

        .notification-content p {
            margin: 0;
            font-size: 0.875rem;
        }

        .dropdown-item {
            padding: 0.5rem 1rem;
            color: var(--text-primary);
        }

        .dropdown-item:hover {
            background: var(--hover-bg);
        }

        .dropdown-item i {
            width: 20px;
        }

        .unread {
            background-color: #f8f9fa;
        }

        .dropdown-divider {
            margin: 0.5rem 0;
            border-top: 1px solid var(--border-color);
        }

        .dropdown-header {
            padding: 0.5rem 1rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .ml-1 {
            margin-left: 0.25rem;
        }

        /* Ensure the caret rotates when dropdown is open */
        .dropdown.show .fa-caret-down {
            transform: rotate(180deg);
        }

        /* Add smooth transition for caret rotation */
        .fa-caret-down {
            transition: transform 0.2s ease;
        }

        /* Update profile dropdown styles */
        #profileDropdown {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .fa-caret-down {
            transition: transform 0.2s ease;
        }

        /* Show caret in both mobile and desktop */
        #profileDropdown .fa-caret-down {
            display: inline-block;
        }

        /* Rotate caret when dropdown is open */
        .dropdown.show .fa-caret-down {
            transform: rotate(180deg);
        }

        @media (max-width: 768px) {
            #profileDropdown {
                padding: 0.5rem;
                gap: 4px;
            }

            .profile-img {
                width: 28px;
                height: 28px;
            }

            /* Ensure caret is visible on mobile */
            #profileDropdown .fa-caret-down {
                font-size: 0.875rem;
            }
        }

        /* Profile dropdown specific styles */
        #profileDropdown {
            padding: 0.5rem;
            display: flex;
            align-items: center;
        }

        #profileDropdown .fa-caret-down {
            margin-left: 4px;
            font-size: 14px;
        }

        .ms-1 {
            margin-left: 0.25rem !important;
        }

        /* Ensure caret is visible in both mobile and desktop */
        @media (max-width: 768px) {
            #profileDropdown {
                padding: 0.25rem;
            }
            
            #profileDropdown .fa-caret-down {
                margin-left: 2px;
            }
        }

        /* Ensure dropdowns position correctly */
        .dropdown-menu {
            position: absolute;
            right: 0;
            top: 100%;
            margin-top: 0.25rem;
        }

        @media (max-width: 360px) {
            .notifications-dropdown {
                width: 280px;
                right: -70px;
            }

            .dropdown-menu-right {
                right: 0;
            }
        }

        /* Update the container-fluid class in the navbar */
        .container-fluid {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
            width: 100%;
        }

        /* Adjust nav items spacing */
        .nav-items-container {
            margin-left: auto;
            gap: 0.5rem;
        }

        /* Update profile dropdown spacing */
        #profileDropdown {
            padding: 0.25rem;
            margin-right: 0.25rem;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .container-fluid {
                padding-left: 0.25rem !important;
                padding-right: 0.25rem !important;
            }

            .nav-items-container {
                gap: 0.25rem;
            }

            #profileDropdown {
                padding: 0.125rem;
                margin-right: 0.125rem;
            }

            .navbar-logo {
                height: 32px;
            }
        }

        /* Extra small devices */
        @media (max-width: 360px) {
            .container-fluid {
                padding-left: 0.125rem !important;
                padding-right: 0.125rem !important;
            }

            .nav-items-container {
                gap: 0.125rem;
            }

            .navbar-logo {
                height: 28px;
            }
        }

        /* Update main content spacing */
        .main-content {
            margin-left: 250px;
            padding: 0;
            width: calc(100% - 250px);
        }

        .welcome-section {
            padding: 1rem;
            margin: 0;
        }

        .content-wrapper {
            width: 100%;
            padding: 1rem;
            margin: 0;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .container-fluid {
                width: calc(100% - 40px);
                margin-left: 40px;
                padding: 0 0.25rem;
            }

            .main-content {
                margin-left: 40px;
                width: calc(100% - 40px);
            }

            .welcome-section,
            .content-wrapper {
                padding: 0.75rem;
            }
        }

        @media (max-width: 480px) {
            .welcome-section,
            .content-wrapper {
                padding: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand navbar-light bg-white fixed-top">
        <div class="container-fluid px-2">
            <!-- Logo -->
            <a class="navbar-brand d-flex align-items-center" href="teacher_dashboard.php">
                <img src="../images/logo.png" alt="Gov D.M. Camerino" class="navbar-logo" height="40">
                <span class="logo-text ml-2 d-none d-md-inline">CamerinoHub</span>
            </a>

            <!-- Right-aligned items -->
            <div class="nav-items-container">
                <ul class="navbar-nav align-items-center">
                    <!-- Notifications -->
                    <li class="nav-item dropdown mx-2">
                        <a class="nav-link" href="#" id="notificationDropdown" role="button">
                            <i class="fas fa-bell"></i>
                            <?php
                            // Get unread notifications count
                            $count_query = "SELECT COUNT(*) as unread 
                                           FROM notifications 
                                           WHERE user_id = ? 
                                           AND user_type = 'teacher' 
                                           AND is_read = 0";
                            $stmt = $db->prepare($count_query);
                            $stmt->bind_param("i", $teacher_id);
                            $stmt->execute();
                            $unread_count = $stmt->get_result()->fetch_assoc()['unread'];
                            
                            if($unread_count > 0) {
                                echo "<span class='badge badge-danger'>$unread_count</span>";
                            }
                            ?>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right notifications-dropdown">
                            <h6 class="dropdown-header">Notifications</h6>
                            <div class="dropdown-divider"></div>
                            <?php
                            // Get recent notifications
                            $notifications_query = "
                                SELECT n.*, 
                                       CASE 
                                           WHEN n.type = 'quiz' THEN a.title
                                           WHEN n.type = 'activity' THEN a.title
                                           WHEN n.type = 'assignment' THEN a.title
                                       END as reference_title
                                FROM notifications n
                                LEFT JOIN activities a ON n.reference_id = a.activity_id
                                WHERE n.user_id = ? 
                                AND n.user_type = 'teacher'
                                ORDER BY n.created_at DESC 
                                LIMIT 5";
                            
                            $stmt = $db->prepare($notifications_query);
                            $stmt->bind_param("i", $teacher_id);
                            $stmt->execute();
                            $notifications = $stmt->get_result();

                            if ($notifications->num_rows > 0) {
                                while ($notif = $notifications->fetch_assoc()) {
                                    $icon_class = '';
                                    switch ($notif['type']) {
                                        case 'quiz': $icon_class = 'fas fa-question-circle text-primary'; break;
                                        case 'activity': $icon_class = 'fas fa-tasks text-success'; break;
                                        case 'assignment': $icon_class = 'fas fa-file-alt text-warning'; break;
                                    }
                                    ?>
                                    <a class="dropdown-item <?php echo $notif['is_read'] ? '' : 'unread'; ?>" 
                                       href="view_notification.php?id=<?php echo $notif['id']; ?>">
                                        <div class="notification-item">
                                            <i class="<?php echo $icon_class; ?>"></i>
                                            <div class="notification-content">
                                                <p class="mb-1"><?php echo htmlspecialchars($notif['message']); ?></p>
                                                <small class="text-muted">
                                                    <?php echo date('M d, Y h:i A', strtotime($notif['created_at'])); ?>
                                                </small>
                                            </div>
                                        </div>
                                    </a>
                                    <?php
                                }
                            } else {
                                ?>
                                <div class="dropdown-item no-notifications">
                                    <p class="text-muted text-center mb-0">No notifications</p>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </li>

                    <!-- Profile -->
                    <li class="nav-item dropdown">
                        <a class="nav-link d-flex align-items-center" href="#" id="profileDropdown">
                            <div class="d-flex align-items-center">
                                <img src="<?php echo $userData['profile_picture'] ?? '../images/teacher.png'; ?>" 
                                     alt="Profile" class="profile-img">
                                <span class="d-none d-md-inline ml-2">
                                    <?php echo htmlspecialchars($userData['firstname'] ?? 'Teacher'); ?>
                                </span>
                                <i class="fas fa-caret-down ms-1"></i>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="teacher_profile.php">
                                <i class="fas fa-user mr-2"></i>My Profile
                            </a>
                            <a class="dropdown-item" href="teacher_profile.php?tab=settings">
                                <i class="fas fa-cog mr-2"></i>Settings
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="logout.php">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="welcome-section">
                <h1>Class Details</h1>
                <p>View and manage your class information</p>
            </div>

            <div class="content-wrapper">
                <div class="class-header">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h1><?php echo htmlspecialchars($class['section_name']); ?></h1>
                            <h4 class="text-muted">
                                <?php echo htmlspecialchars($class['subject_code'] . ' - ' . $class['subject_name']); ?>
                            </h4>
                            <div class="mt-3">
                                <span class="schedule-badge">
                                    <i class="fas fa-calendar-alt"></i>
                                    <?php echo htmlspecialchars($class['schedule_day']); ?>
                                </span>
                                <span class="schedule-badge">
                                    <i class="fas fa-clock"></i>
                                    <?php 
                                        $time = DateTime::createFromFormat('H:i:s', $class['schedule_time']);
                                        echo $time ? $time->format('h:i A') : $class['schedule_time']; 
                                    ?>
                                </span>
                            </div>
                        </div>
                        <a href="manage_classes.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Classes
                        </a>
                    </div>

                    <div class="stats-container">
                        <div class="stat-card">
                            <div class="enrollment-code">
                                <h6>Section Code</h6>
                                <div class="code-display">
                                    <span id="enrollmentCode"><?php echo htmlspecialchars($class['enrollment_code']); ?></span>
                                    <button class="btn btn-sm btn-outline-primary" onclick="copyEnrollmentCode()" data-bs-toggle="tooltip" title="Copy to clipboard">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Share this code with students to join the section</small>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value"><?php echo $class['student_count']; ?></div>
                            <div class="stat-label">Students</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value"><?php echo number_format($class['attendance_rate'], 1); ?>%</div>
                            <div class="stat-label">Average Attendance</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value"><?php echo number_format($class['average_performance'], 1); ?>%</div>
                            <div class="stat-label">Class Performance</div>
                        </div>
                    </div>
                </div>

                <div class="students-container">
                    <h2 class="mb-4">Students</h2>
                    <?php foreach ($students as $student): ?>
                        <div class="student-card">
                            <img src="<?php echo $student['profile_image'] ?? '../images/default-avatar.png'; ?>" 
                                 alt="Student Avatar" 
                                 class="student-avatar">
                            <div class="student-info">
                                <h5 class="mb-1">
                                    <?php echo htmlspecialchars($student['firstname'] . ' ' . $student['lastname']); ?>
                                </h5>
                                <div class="student-stats">
                                    <span>Attendance: <?php echo number_format($student['attendance_rate'], 1); ?>%</span>
                                    <span>Performance: 
                                        <?php 
                                        $performance = $student['performance'];
                                        $performanceClass = $performance >= 85 ? 'good' : ($performance >= 75 ? 'average' : 'poor');
                                        ?>
                                        <span class="performance-badge performance-<?php echo $performanceClass; ?>">
                                            <?php echo number_format($performance, 1); ?>%
                                        </span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function copyEnrollmentCode() {
        const codeElement = document.getElementById('enrollmentCode');
        const code = codeElement.textContent;
        
        navigator.clipboard.writeText(code).then(() => {
            // Show success feedback
            const btn = codeElement.nextElementSibling;
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check"></i>';
            btn.classList.add('btn-success');
            btn.classList.remove('btn-outline-primary');
            
            setTimeout(() => {
                btn.innerHTML = originalHtml;
                btn.classList.remove('btn-success');
                btn.classList.add('btn-outline-primary');
            }, 2000);
            
            // Optional: Show tooltip or toast
            if (typeof bootstrap !== 'undefined') {
                const toast = new bootstrap.Toast(Object.assign(document.createElement('div'), {
                    className: 'toast position-fixed bottom-0 end-0 m-3',
                    innerHTML: `
                        <div class="toast-body bg-success text-white">
                            Enrollment code copied to clipboard!
                        </div>
                    `
                }));
                document.body.appendChild(toast._element);
                toast.show();
                setTimeout(() => toast._element.remove(), 3000);
            }
        }).catch(err => {
            console.error('Failed to copy code:', err);
            alert('Failed to copy code. Please try again.');
        });
    }

    // Initialize tooltips if using Bootstrap 5
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof bootstrap !== 'undefined') {
            const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            tooltips.forEach(tooltip => new bootstrap.Tooltip(tooltip));
        }
    });

    function regenerateCode() {
        if (!confirm('Are you sure you want to generate a new enrollment code? The old code will no longer work.')) {
            return;
        }
        
        $.ajax({
            url: 'handlers/class_handler.php',
            method: 'POST',
            data: {
                action: 'regenerate_code',
                class_id: <?php echo $class_id; ?>
            },
            success: function(response) {
                try {
                    const result = JSON.parse(response);
                    if (result.status === 'success') {
                        document.getElementById('enrollmentCode').textContent = result.code;
                        
                        const toast = new bootstrap.Toast(Object.assign(document.createElement('div'), {
                            className: 'toast position-fixed bottom-0 end-0 m-3',
                            innerHTML: `
                                <div class="toast-body bg-success text-white">
                                    New enrollment code generated successfully!
                                </div>
                            `
                        }));
                        document.body.appendChild(toast._element);
                        toast.show();
                        setTimeout(() => toast._element.remove(), 3000);
                    } else {
                        alert('Failed to generate new code: ' + result.message);
                    }
                } catch (e) {
                    console.error('JSON parse error:', e);
                    alert('Failed to generate new code. Please try again.');
                }
            },
            error: function() {
                alert('Failed to connect to server. Please try again.');
            }
        });
    }

    // Update the dropdown toggle handlers
    document.getElementById('notificationDropdown').addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const dropdown = this.nextElementSibling;
        const profileDropdown = document.querySelector('#profileDropdown').nextElementSibling;
        
        // Close profile dropdown if open
        profileDropdown.classList.remove('show');
        
        // Toggle notifications dropdown
        dropdown.classList.toggle('show');
    });

    document.getElementById('profileDropdown').addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const dropdown = this.nextElementSibling;
        const notifDropdown = document.querySelector('#notificationDropdown').nextElementSibling;
        
        // Close notifications dropdown if open
        notifDropdown.classList.remove('show');
        
        // Toggle profile dropdown
        dropdown.classList.toggle('show');
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        const dropdowns = document.querySelectorAll('.dropdown-menu');
        dropdowns.forEach(dropdown => {
            if (!dropdown.contains(e.target) && !e.target.matches('.nav-link')) {
                dropdown.classList.remove('show');
            }
        });
    });

    // Add these styles to ensure proper navigation behavior
    document.addEventListener('DOMContentLoaded', function() {
        const navbar = document.querySelector('.navbar');
        const mainContent = document.querySelector('.main-content');
        const sidebar = document.querySelector('.sidebar');

        // Adjust main content padding based on navbar height
        const navbarHeight = navbar.offsetHeight;
        mainContent.style.paddingTop = navbarHeight + 'px';
        sidebar.style.top = navbarHeight + 'px';
        sidebar.style.height = `calc(100vh - ${navbarHeight}px)`;
    });
    </script>
</body>
</html>
