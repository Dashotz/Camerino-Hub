<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar">
    <div class="sidebar-content">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'student_dashboard.php' ? 'active' : ''; ?>" 
                   href="student_dashboard.php">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'student_section.php' ? 'active' : ''; ?>" 
                   href="student_section.php">
                    <i class="fas fa-users"></i>
                    <span>My Section</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'student_subjects.php' ? 'active' : ''; ?>" 
                   href="student_subjects.php">
                    <i class="fas fa-book"></i>
                    <span>My Subjects</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="student_assignments.php" class="nav-link <?php echo $current_page == 'student_assignments.php' ? 'active' : ''; ?>">
                    <i class="fas fa-tasks"></i>
                    <span>Assignments</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'student_grades.php' ? 'active' : ''; ?>" 
                   href="student_grades.php">
                    <i class="fas fa-chart-line"></i>
                    <span>Grades</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'student_calendar.php' ? 'active' : ''; ?>" 
                   href="student_calendar.php">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Calendar</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="student_activities.php" class="nav-link <?php echo $current_page == 'student_activities.php' ? 'active' : ''; ?>">
                    <i class="fas fa-tasks"></i>
                    <span>Activities</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'student_quizzes.php' ? 'active' : ''; ?>" 
                   href="student_quizzes.php">
                    <i class="fas fa-question-circle"></i>
                    <span>Quizzes</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'student_announcements.php' ? 'active' : ''; ?>" 
                   href="student_announcements.php">
                    <i class="fas fa-bullhorn"></i>
                    <span>Announcements</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-footer">
            <div class="help-card">
                <i class="fas fa-question-circle"></i>
                <h6>Need Help?</h6>
                <p>Contact your adviser or visit our help center</p>
                <a href="help.php" class="btn btn-sm btn-outline-primary">Get Help</a>
            </div>
        </div>
    </div>
</div> 