<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar">
    <div class="sidebar-content">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'admin_dashboard.php' ? 'active' : ''; ?>" 
                   href="admin_dashboard.php">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
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
                <a class="nav-link <?php echo $current_page == 'manage_teachers.php' ? 'active' : ''; ?>" 
                   href="manage_teachers.php">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span>Teachers</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'manage_subjects.php' ? 'active' : ''; ?>" 
                   href="manage_subjects.php">
                    <i class="fas fa-book"></i>
                    <span>Subjects</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'manage_sections.php' ? 'active' : ''; ?>" 
                   href="manage_sections.php">
                    <i class="fas fa-layer-group"></i>
                    <span>Sections</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'academic_year.php' ? 'active' : ''; ?>" 
                   href="academic_year.php">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Academic Year</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'reports.php' ? 'active' : ''; ?>" 
                   href="reports.php">
                    <i class="fas fa-chart-bar"></i>
                    <span>Reports</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'settings.php' ? 'active' : ''; ?>" 
                   href="settings.php">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-footer">
            <div class="help-card">
                <i class="fas fa-question-circle"></i>
                <h6>Need Help?</h6>
                <p>Access admin documentation and support</p>
                <a href="admin_support.php" class="btn btn-sm btn-outline-primary">Get Help</a>
            </div>
        </div>
    </div>
</div>
