<nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top">
    <div class="container-fluid px-4">
        <a class="navbar-brand d-flex align-items-center" href="admin_dashboard.php">
            <img src="../images/logo.png" alt="Gov D.M. Camerino" class="navbar-logo" height="40">
            <span class="logo-text ml-2">CamerinoHub</span>
        </a>
        
        <!-- Mobile menu button -->
        <button class="navbar-toggler border-0" type="button" data-toggle="collapse" data-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
     

            <!-- Right-aligned items -->
            <ul class="navbar-nav ml-auto align-items-center">
                <!-- Notifications -->
                <li class="nav-item dropdown mx-2">
                    <a class="nav-link" href="#" role="button" data-toggle="dropdown">
                        <i class="fas fa-bell"></i>
                        <?php
                        // Get unread notifications count for admin
                        $notif_query = "SELECT COUNT(*) as count 
                                       FROM notifications 
                                       WHERE user_type = 'admin' 
                                       AND user_id = ? 
                                       AND is_read = 0";
                        $stmt = $db->prepare($notif_query);
                        $stmt->bind_param("i", $_SESSION['admin_id']);
                        $stmt->execute();
                        $unread = $stmt->get_result()->fetch_assoc()['count'];
                        if ($unread > 0) {
                            echo "<span class='badge badge-danger'>$unread</span>";
                        }
                        ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right notifications-dropdown">
                        <h6 class="dropdown-header">Notifications</h6>
                        <div class="dropdown-divider"></div>
                        <?php
                        // Get recent notifications
                        $notifications_query = "SELECT * FROM notifications 
                                              WHERE user_type = 'admin' 
                                              AND user_id = ? 
                                              ORDER BY created_at DESC 
                                              LIMIT 5";
                        $stmt = $db->prepare($notifications_query);
                        $stmt->bind_param("i", $_SESSION['admin_id']);
                        $stmt->execute();
                        $notifications = $stmt->get_result();

                        if ($notifications->num_rows > 0) {
                            while ($notif = $notifications->fetch_assoc()) {
                                $icon = 'info-circle';
                                if ($notif['is_system']) $icon = 'cog';
                                
                                echo "<a class='dropdown-item " . ($notif['is_read'] ? '' : 'unread') . "' 
                                      href='#' onclick='markNotificationRead({$notif['id']})'>
                                      <i class='fas fa-{$icon} mr-2'></i>
                                      <div class='notification-content'>
                                          <div class='notification-title'>{$notif['title']}</div>
                                          <div class='notification-text'>{$notif['message']}</div>
                                          <small class='text-muted'>" . date('M j, Y g:i A', strtotime($notif['created_at'])) . "</small>
                                      </div>
                                    </a>";
                            }
                        } else {
                            echo "<div class='dropdown-item'>No notifications</div>";
                        }
                        ?>
                        
                    </div>
                </li>

                <!-- Profile -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" 
                       data-toggle="dropdown">
                        <img src="../images/admin.png" alt="Profile" class="profile-img">
                        <span class="d-none d-md-inline ml-2">
                            <?php echo htmlspecialchars($admin['firstname'] ?? 'Admin'); ?>
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="settings.php">
                            <i class="fas fa-user mr-2"></i>My Profile
                        </a>
                        <a class="dropdown-item" href="settings.php">
                            <i class="fas fa-cog mr-2"></i>Settings
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="logout.php" onclick="confirmLogout(event)">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script>
function markNotificationRead(notificationId) {
    $.ajax({
        url: 'handlers/notification_handler.php',
        type: 'POST',
        data: {
            action: 'mark_read',
            notification_id: notificationId
        },
        success: function(response) {
            if (response.status === 'success') {
                // Update UI to reflect read status
                updateNotificationCount();
            }
        }
    });
}

function updateNotificationCount() {
    $.ajax({
        url: 'handlers/notification_handler.php',
        type: 'GET',
        data: { action: 'get_unread_count' },
        success: function(response) {
            const badge = $('.nav-link .badge');
            if (response.count > 0) {
                if (badge.length) {
                    badge.text(response.count);
                } else {
                    $('.nav-link').append(`<span class="badge badge-danger">${response.count}</span>`);
                }
            } else {
                badge.remove();
            }
        }
    });
}
</script>
