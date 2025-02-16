<?php
// At the top of navigation.php
if (isset($_SESSION['id'])) {
    $student_id = $_SESSION['id'];
    $stmt = $db->prepare("SELECT student_id, firstname, lastname, profile_image FROM student WHERE student_id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $userData = $result->fetch_assoc();
}
?>

<nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top">
    <div class="container-fluid px-4">
        <a class="navbar-brand d-flex align-items-center" href="student_dashboard.php">
            <img src="../images/logo.png" alt="Gov D.M. Camerino" class="navbar-logo" height="40">
            <span class="logo-text ml-2">CamerinoHub</span>
        </a>

        <!-- Mobile view: Direct content -->
        <div class="mobile-nav d-lg-none">
            <ul class="navbar-nav align-items-center">
                <!-- Notifications -->
                <li class="nav-item dropdown mx-2">
                    <a class="nav-link" href="#" role="button" data-toggle="dropdown">
                        <i class="fas fa-bell"></i>
                        <?php
                        // Fetch unread notifications count and pending activities
                        $student_id = $_SESSION['id'];
                        
                        // Get student's sections
                        $sections_query = "
                            SELECT section_id 
                            FROM student_sections 
                            WHERE student_id = ? 
                            AND status = 'active'";
                        
                        $stmt = $db->prepare($sections_query);
                        $stmt->bind_param("i", $student_id);
                        $stmt->execute();
                        $sections_result = $stmt->get_result();
                        $section_ids = [];
                        
                        while($row = $sections_result->fetch_assoc()) {
                            $section_ids[] = $row['section_id'];
                        }

                        if (!empty($section_ids)) {
                            $section_ids_str = implode(',', $section_ids);
                            
                            // Fetch pending activities
                            $pending_query = "
                                SELECT a.*, ss.section_id, s.subject_code, 
                                       DATEDIFF(a.due_date, NOW()) as days_left
                                FROM activities a
                                JOIN section_subjects ss ON a.section_subject_id = ss.id
                                JOIN subjects s ON ss.subject_id = s.id
                                LEFT JOIN student_activity_submissions sas 
                                    ON sas.activity_id = a.activity_id 
                                    AND sas.student_id = ?
                                WHERE ss.section_id IN ($section_ids_str)
                                AND a.due_date > NOW()
                                AND a.status = 'active'
                                AND sas.submission_id IS NULL
                                ORDER BY a.due_date ASC
                                LIMIT 5";
                            
                            $stmt = $db->prepare($pending_query);
                            $stmt->bind_param("i", $student_id);
                            $stmt->execute();
                            $pending_activities = $stmt->get_result();
                        }
                        ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right notifications-dropdown">
                        <h6 class="dropdown-header">Pending Activities</h6>
                        <div class="dropdown-divider"></div>
                        <?php
                        if (isset($pending_activities) && $pending_activities->num_rows > 0) {
                            while ($activity = $pending_activities->fetch_assoc()) {
                                $icon_class = '';
                                switch ($activity['type']) {
                                    case 'quiz':
                                        $icon_class = 'fas fa-question-circle text-primary';
                                        break;
                                    case 'activity':
                                        $icon_class = 'fas fa-tasks text-success';
                                        break;
                                    case 'assignment':
                                        $icon_class = 'fas fa-file-alt text-warning';
                                        break;
                                }
                                
                                $due_text = '';
                                if ($activity['days_left'] == 0) {
                                    $due_text = 'Due today';
                                } elseif ($activity['days_left'] == 1) {
                                    $due_text = 'Due tomorrow';
                                } else {
                                    $due_text = "Due in {$activity['days_left']} days";
                                }
                                ?>
                                <a class="dropdown-item pending-activity" 
                                   href="view_activity.php?id=<?php echo $activity['activity_id']; ?>">
                                    <div class="notification-item">
                                        <i class="<?php echo $icon_class; ?>"></i>
                                        <div class="notification-content">
                                            <p class="mb-1">
                                                <?php echo htmlspecialchars($activity['title']); ?>
                                                <span class="badge badge-pill badge-light">
                                                    <?php echo htmlspecialchars($activity['subject_code']); ?>
                                                </span>
                                            </p>
                                            <small class="text-<?php echo $activity['days_left'] <= 1 ? 'danger' : 'muted'; ?>">
                                                <?php echo $due_text; ?>
                                            </small>
                                        </div>
                                    </div>
                                </a>
                                <?php
                            }
                        } else {
                            ?>
                            <div class="dropdown-item no-activities">
                                <div class="text-center py-3">
                                    <i class="fas fa-check-circle text-success mb-2" style="font-size: 24px;"></i>
                                    <p class="mb-0">All caught up!</p>
                                    <small class="text-muted">No pending activities</small>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                     
                    </div>
                </li>

                <!-- Profile -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" data-toggle="dropdown">
                        <img src="<?php echo $userData['profile_image'] ?? '../images/default-avatar.png'; ?>" 
                             alt="Profile" class="profile-img">
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="student_profile.php">
                            <i class="fas fa-user mr-2"></i>My Profile
                        </a>
                        <a class="dropdown-item" href="student_profile.php?tab=settings">
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

        <!-- Desktop view -->
        <div class="collapse navbar-collapse d-none d-lg-flex" id="navbarNav">
        
            <!-- Right-aligned items -->
            <ul class="navbar-nav ml-auto align-items-center">
                <!-- Notifications -->
                <li class="nav-item dropdown mx-2">
                    <a class="nav-link" href="#" role="button" data-toggle="dropdown">
                        <i class="fas fa-bell"></i>
                        <?php
                        // Fetch unread notifications count and pending activities
                        $student_id = $_SESSION['id'];
                        
                        // Get student's sections
                        $sections_query = "
                            SELECT section_id 
                            FROM student_sections 
                            WHERE student_id = ? 
                            AND status = 'active'";
                        
                        $stmt = $db->prepare($sections_query);
                        $stmt->bind_param("i", $student_id);
                        $stmt->execute();
                        $sections_result = $stmt->get_result();
                        $section_ids = [];
                        
                        while($row = $sections_result->fetch_assoc()) {
                            $section_ids[] = $row['section_id'];
                        }

                        if (!empty($section_ids)) {
                            $section_ids_str = implode(',', $section_ids);
                            
                            // Fetch pending activities
                            $pending_query = "
                                SELECT a.*, ss.section_id, s.subject_code, 
                                       DATEDIFF(a.due_date, NOW()) as days_left
                                FROM activities a
                                JOIN section_subjects ss ON a.section_subject_id = ss.id
                                JOIN subjects s ON ss.subject_id = s.id
                                LEFT JOIN student_activity_submissions sas 
                                    ON sas.activity_id = a.activity_id 
                                    AND sas.student_id = ?
                                WHERE ss.section_id IN ($section_ids_str)
                                AND a.due_date > NOW()
                                AND a.status = 'active'
                                AND sas.submission_id IS NULL
                                ORDER BY a.due_date ASC
                                LIMIT 5";
                            
                            $stmt = $db->prepare($pending_query);
                            $stmt->bind_param("i", $student_id);
                            $stmt->execute();
                            $pending_activities = $stmt->get_result();
                        }
                        ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right notifications-dropdown">
                        <h6 class="dropdown-header">Pending Activities</h6>
                        <div class="dropdown-divider"></div>
                        <?php
                        if (isset($pending_activities) && $pending_activities->num_rows > 0) {
                            while ($activity = $pending_activities->fetch_assoc()) {
                                $icon_class = '';
                                switch ($activity['type']) {
                                    case 'quiz':
                                        $icon_class = 'fas fa-question-circle text-primary';
                                        break;
                                    case 'activity':
                                        $icon_class = 'fas fa-tasks text-success';
                                        break;
                                    case 'assignment':
                                        $icon_class = 'fas fa-file-alt text-warning';
                                        break;
                                }
                                
                                $due_text = '';
                                if ($activity['days_left'] == 0) {
                                    $due_text = 'Due today';
                                } elseif ($activity['days_left'] == 1) {
                                    $due_text = 'Due tomorrow';
                                } else {
                                    $due_text = "Due in {$activity['days_left']} days";
                                }
                                ?>
                                <a class="dropdown-item pending-activity" 
                                   href="view_activity.php?id=<?php echo $activity['activity_id']; ?>">
                                    <div class="notification-item">
                                        <i class="<?php echo $icon_class; ?>"></i>
                                        <div class="notification-content">
                                            <p class="mb-1">
                                                <?php echo htmlspecialchars($activity['title']); ?>
                                                <span class="badge badge-pill badge-light">
                                                    <?php echo htmlspecialchars($activity['subject_code']); ?>
                                                </span>
                                            </p>
                                            <small class="text-<?php echo $activity['days_left'] <= 1 ? 'danger' : 'muted'; ?>">
                                                <?php echo $due_text; ?>
                                            </small>
                                        </div>
                                    </div>
                                </a>
                                <?php
                            }
                        } else {
                            ?>
                            <div class="dropdown-item no-activities">
                                <div class="text-center py-3">
                                    <i class="fas fa-check-circle text-success mb-2" style="font-size: 24px;"></i>
                                    <p class="mb-0">All caught up!</p>
                                    <small class="text-muted">No pending activities</small>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                     
                    </div>
                </li>

                <!-- Profile -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" data-toggle="dropdown">
                        <img src="<?php echo $userData['profile_image'] ?? '../images/default-avatar.png'; ?>" 
                             alt="Profile" class="profile-img">
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="student_profile.php">
                            <i class="fas fa-user mr-2"></i>My Profile
                        </a>
                        <a class="dropdown-item" href="student_profile.php?tab=settings">
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

<style>
.notifications-dropdown {
    min-width: 300px;
    max-height: 400px;
    overflow-y: auto;
}
.notification-item {
    display: flex;
    align-items: start;
    padding: 8px 0;
}
.notification-item i {
    margin-right: 12px;
    margin-top: 4px;
    font-size: 16px;
}
.notification-content {
    flex: 1;
}
.notification-content p {
    margin-bottom: 0;
    font-size: 14px;
    line-height: 1.4;
}
.badge-light {
    background-color: #f8f9fa;
    color: #6c757d;
    font-weight: normal;
    font-size: 12px;
}
.pending-activity:hover {
    background-color: #f8f9fa;
}
.no-activities {
    cursor: default;
}
.no-activities:hover {
    background: none;
}

/* Add responsive styles */
@media (max-width: 768px) {
    /* Navbar brand */
    .navbar-brand {
        font-size: 1.1rem;
    }

    .navbar-logo {
        height: 32px;
    }

    .logo-text {
        font-size: 1.1rem;
    }

    /* Search bar */
    .form-inline {
        width: 100%;
        margin: 10px 0;
    }

    .search-input {
        width: 100%;
    }

    /* Notifications dropdown */
    .notifications-dropdown {
        position: fixed;
        top: 60px;
        left: 0;
        right: 0;
        width: 100%;
        max-width: none;
        min-width: 0;
        margin: 0;
        border-radius: 0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    /* Profile section */
    .profile-img {
        width: 32px;
        height: 32px;
    }

    /* Navbar items spacing */
    .navbar-nav {
        padding: 10px 0;
    }

    .nav-item {
        padding: 5px 0;
    }

    /* Dropdown menus */
    .dropdown-menu {
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .dropdown-item {
        padding: 10px 15px;
    }

    /* Notification items */
    .notification-item {
        padding: 12px;
    }

    .notification-content p {
        font-size: 13px;
    }

    .badge-light {
        font-size: 11px;
    }
}

/* Small mobile devices */
@media (max-width: 480px) {
    /* Further compact the navbar */
    .navbar {
        padding: 8px 0;
    }

    .navbar-brand {
        font-size: 1rem;
    }

    .navbar-logo {
        height: 28px;
    }

    /* Hide logo text on very small screens */
    .logo-text {
        display: none;
    }

    /* Make notifications more compact */
    .notification-item {
        padding: 8px;
    }

    .notification-content p {
        font-size: 12px;
    }

    /* Adjust dropdown positioning */
    .dropdown-menu {
        margin-top: 5px;
    }

    /* Make profile section more compact */
    .profile-img {
        width: 28px;
        height: 28px;
    }
}

/* Fix for landscape mode */
@media (max-height: 480px) and (orientation: landscape) {
    .notifications-dropdown {
        max-height: 250px;
    }

    .navbar {
        padding: 5px 0;
    }
}

/* Add to existing styles */
.mobile-nav {
    display: flex;
    align-items: center;
}

.mobile-nav .navbar-nav {
    flex-direction: row;
    align-items: center;
    padding: 0;
}

.mobile-nav .nav-item {
    padding: 0 8px;
}

@media (max-width: 768px) {
    .mobile-nav .profile-img {
        width: 32px;
        height: 32px;
    }

    .mobile-nav .nav-link {
        padding: 0.5rem;
    }

    /* Adjust dropdown positioning for mobile */
    .mobile-nav .dropdown-menu {
        position: absolute;
        right: 0;
        left: auto;
        margin-top: 0.5rem;
    }
}

/* Small mobile devices */
@media (max-width: 480px) {
    .mobile-nav .profile-img {
        width: 28px;
        height: 28px;
    }
}
</style>
