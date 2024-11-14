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
                        $teacher_id = $_SESSION['teacher_id'];
                        
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
                        ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right notifications-dropdown">
                        <h6 class="dropdown-header">Notifications</h6>
                        <div class="dropdown-divider"></div>
                        <?php
                        if ($notifications->num_rows > 0) {
                            while ($notif = $notifications->fetch_assoc()) {
                                $icon_class = '';
                                switch ($notif['type']) {
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
                                ?>
                                <a class="dropdown-item <?php echo $notif['is_read'] ? '' : 'unread'; ?>" 
                                   href="view_notification.php?id=<?php echo $notif['id']; ?>">
                                    <div class="notification-item">
                                        <i class="<?php echo $icon_class; ?>"></i>
                                        <div class="notification-content">
                                            <p class="mb-1"><?php echo htmlspecialchars($notif['message']); ?></p>
                                            <small class="text-muted">
                                                <?php echo timeAgo($notif['created_at']); ?>
                                            </small>
                                        </div>
                                    </div>
                                </a>
                                <?php
                            }
                        } else {
                            ?>
                            <div class="dropdown-item no-notifications">
                                <div class="text-center py-3">
                                    <i class="fas fa-bell-slash text-muted mb-2" style="font-size: 24px;"></i>
                                    <p class="mb-0">No notifications</p>
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
                        <img src="<?php echo $userData['profile_picture'] ?? '../images/teacher.png'; ?>" 
                             alt="Profile" class="profile-img">
                        <span class="d-none d-md-inline ml-2">
                            <?php echo htmlspecialchars($userData['firstname'] ?? 'Teacher'); ?>
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="student_profile.php">
                            <i class="fas fa-user mr-2"></i>My Profile
                        </a>
                        <a class="dropdown-item" href="student_settings.php">
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
