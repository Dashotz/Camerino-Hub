<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar">
    <a href="student_dashboard.php" class="sidebar-logo-link">
        <img src="../images/logo.png" alt="CamerinoHub" class="sidebar-mobile-logo">
    </a>
    
    <div class="sidebar-content">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'student_dashboard.php' ? 'active' : ''; ?>" 
                   href="student_dashboard.php"
                   data-title="Dashboard">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'student_section.php' ? 'active' : ''; ?>" 
                   href="student_section.php"
                   data-title="My Section">
                    <i class="fas fa-users"></i>
                    <span>My Section</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'student_subjects.php' ? 'active' : ''; ?>" 
                   href="student_subjects.php"
                   data-title="My Subjects">
                    <i class="fas fa-book"></i>
                    <span>My Subjects</span>
                </a>
            </li>           
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'student_grades.php' ? 'active' : ''; ?>" 
                   href="student_grades.php"
                   data-title="Grades">
                    <i class="fas fa-chart-line"></i>
                    <span>Grades</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'student_calendar.php' ? 'active' : ''; ?>" 
                   href="student_calendar.php"
                   data-title="Calendar">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Calendar</span>
                </a>
            </li>
			 <li class="nav-item">
                <a href="student_assignments.php" class="nav-link <?php echo $current_page == 'student_assignments.php' ? 'active' : ''; ?>"
                   data-title="Assignments">
                    <i class="fas fa-tasks"></i>
                    <span>Assignments</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="student_activities.php" class="nav-link <?php echo $current_page == 'student_activities.php' ? 'active' : ''; ?>"
                   data-title="Activities">
                    <i class="fas fa-tasks"></i>
                    <span>Activities</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'student_quizzes.php' ? 'active' : ''; ?>" 
                   href="student_quizzes.php"
                   data-title="Quizzes">
                    <i class="fas fa-question-circle"></i>
                    <span>Quizzes</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'student_announcements.php' ? 'active' : ''; ?>" 
                   href="student_announcements.php"
                   data-title="Announcements">
                    <i class="fas fa-bullhorn"></i>
                    <span>Announcements</span>
                </a>
            </li>
        </ul>
    </div>
</div> 