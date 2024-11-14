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
                    <?php if (isset($assignment_count) && $assignment_count > 0): ?>
                        <span class="badge badge-primary"><?php echo $assignment_count; ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'manage_students.php' ? 'active' : ''; ?>" 
                   href="manage_students.php">
                    <i class="fas fa-users"></i>
                    <span>Students</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'grade_submission.php' ? 'active' : ''; ?>" 
                   href="grade_submissions.php">
                    <i class="fas fa-star"></i>
                    <span>Grade Submissions</span>
                </a>
            </li>
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

        <div class="sidebar-footer">
            <div class="help-card">
                <i class="fas fa-question-circle"></i>
                <h6>Need Support?</h6>
                <p>Access teaching resources and technical support</p>
                <a href="teacher_support.php" class="btn btn-sm btn-outline-primary">Get Support</a>
            </div>
        </div>
    </div>
</div>
