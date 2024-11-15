<nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top">
    <div class="container-fluid px-4">
        <a class="navbar-brand d-flex align-items-center" href="home.php">
            <img src="../images/logo.png" alt="Gov D.M. Camerino" class="navbar-logo" height="40">
            <span class="logo-text ml-2">CamerinoHub</span>
        </a>
        
        <!-- Mobile menu button -->
        <button class="navbar-toggler border-0" type="button" data-toggle="collapse" data-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Search bar -->
            <form class="form-inline mx-auto d-none d-md-flex position-relative">
                <input class="form-control search-input" type="search" placeholder="Search...">
                <i class="fas fa-search search-icon"></i>
            </form>

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
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-center" href="all_notifications.php">
                            <small>View All Notifications</small>
                        </a>
                    </div>
                </li>

                <!-- Profile -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" 
                       data-toggle="dropdown">
                        <img src="<?php echo $userData['profile_picture'] ?? '../images/default-avatar.png'; ?>" 
                             alt="Profile" class="profile-img">
                        <span class="d-none d-md-inline ml-2">
                            <?php echo htmlspecialchars($userData['firstname'] ?? 'Student'); ?>
                        </span>
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
</style>
