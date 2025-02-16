<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar">
    <div class="sidebar-content">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'teacher_dashboard.php' ? 'active' : ''; ?>" 
                   href="teacher_dashboard.php">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'manage_classes.php' ? 'active' : ''; ?>" 
                   href="manage_classes.php">
                    <i class="fas fa-chalkboard"></i>
                    <span>My Classes</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'manage_activities.php' ? 'active' : ''; ?>" 
                   href="manage_activities.php">
                    <i class="fas fa-tasks"></i>
                    <span>Activities and Quizzes</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'manage_students.php' ? 'active' : ''; ?>" 
                   href="manage_students.php">
                    <i class="fas fa-users"></i>
                    <span>Students</span>
                </a>
            </li>
                        <!-- Add Grade Management Link -->
                        <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'grade_management.php' ? 'active' : ''; ?>" 
                   href="grade_management.php">
                    <i class="fas fa-graduation-cap"></i>
                    <span>Grade Management</span>
                </a>
            </li>

           <!-- Hide Grade Submissions link for now
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'grade_submission.php' ? 'active' : ''; ?>" 
                   href="grade_submissions.php">
                    <i class="fas fa-star"></i>
                    <span>Grade Submissions</span>
                </a>
            </li> 
            -->
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'attendance.php' ? 'active' : ''; ?>" 
                   href="attendance.php">
                    <i class="fas fa-clipboard-check"></i>
                    <span>Attendance</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'teacher_calendar.php' ? 'active' : ''; ?>" 
                   href="teacher_calendar.php">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Calendar</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'teacher_announcements.php' ? 'active' : ''; ?>" 
                   href="teacher_announcements.php">
                    <i class="fas fa-bullhorn"></i>
                    <span>Announcements</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'reports.php' ? 'active' : ''; ?>" 
                   href="reports.php">
                    <i class="fas fa-chart-bar"></i>
                    <span>Reports</span>
                </a>
            </li>
        </ul>
    </div>
</div>
