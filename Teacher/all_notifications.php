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

// Fetch teacher's sections and subjects
$sections_query = "
    SELECT DISTINCT 
        s.section_id,
        s.section_name,
        s.grade_level
    FROM section_subjects ss
    JOIN sections s ON ss.section_id = s.section_id
    WHERE ss.teacher_id = ? AND ss.status = 'active'";

$stmt = $db->prepare($sections_query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$sections = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch all notifications for the teacher
$notifications_query = "
    SELECT 
        n.*,
        CASE 
            WHEN n.type = 'quiz' THEN 'Quiz'
            WHEN n.type = 'activity' THEN 'Activity'
            WHEN n.type = 'assignment' THEN 'Assignment'
            ELSE 'Announcement'
        END as type_label,
        s.section_name,
        sub.subject_name,
        CASE 
            WHEN n.type IN ('quiz', 'activity', 'assignment') THEN a.title
            ELSE ann.content
        END as content_title,
        n.created_at as notification_date
    FROM notifications n
    LEFT JOIN sections s ON n.section_id = s.section_id
    LEFT JOIN subjects sub ON n.subject_id = sub.id
    LEFT JOIN activities a ON n.activity_id = a.activity_id
    LEFT JOIN announcements ann ON n.announcement_id = ann.id
    WHERE n.user_id = ? 
    AND n.user_type = 'teacher'
    ORDER BY n.created_at DESC";

$stmt = $db->prepare($notifications_query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$notifications = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Notifications - CamerinoHub</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/dashboard-shared.css">
	<link rel="icon" href="../images/light-logo.png">
</head>
<body>

<?php include 'includes/navigation.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="container mt-5 pt-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">All Notifications</h5>
                    <div>
                        <button class="btn btn-sm btn-outline-primary" id="markAllRead">
                            <i class="fas fa-check-double"></i> Mark All as Read
                        </button>
                        <button class="btn btn-sm btn-outline-danger" id="clearAll">
                            <i class="fas fa-trash"></i> Clear All
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <?php if ($notifications->num_rows > 0): ?>
                            <?php while ($notif = $notifications->fetch_assoc()): ?>
                                <?php
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
                                    default:
                                        $icon_class = 'fas fa-bell text-info';
                                }
                                ?>
                                <div class="list-group-item <?php echo !$notif['is_read'] ? 'unread' : ''; ?>">
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <div>
                                            <i class="<?php echo $icon_class; ?> mr-2"></i>
                                            <strong><?php echo htmlspecialchars($notif['type_label']); ?></strong>
                                            <span class="badge badge-secondary ml-2">
                                                <?php echo htmlspecialchars($notif['section_name']); ?> - 
                                                <?php echo htmlspecialchars($notif['subject_name']); ?>
                                            </span>
                                        </div>
                                        <small class="text-muted">
                                            <?php echo date('M j, Y g:i A', strtotime($notif['notification_date'])); ?>
                                        </small>
                                    </div>
                                    <p class="mb-1 mt-2"><?php echo htmlspecialchars($notif['message']); ?></p>
                                    <?php if ($notif['content_title']): ?>
                                        <small class="text-muted">
                                            <?php echo htmlspecialchars($notif['content_title']); ?>
                                        </small>
                                    <?php endif; ?>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No notifications found</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
$(document).ready(function() {
    // Mark all notifications as read
    $('#markAllRead').click(function() {
        $.ajax({
            url: 'mark_all_notifications_read.php',
            type: 'POST',
            success: function(response) {
                $('.list-group-item').removeClass('unread');
                updateNotificationCount();
            }
        });
    });

    // Clear all notifications
    $('#clearAll').click(function() {
        if (confirm('Are you sure you want to clear all notifications?')) {
            $.ajax({
                url: 'clear_all_notifications.php',
                type: 'POST',
                success: function(response) {
                    $('.list-group').html('<div class="text-center py-4"><i class="fas fa-bell-slash fa-3x text-muted mb-3"></i><p class="text-muted">No notifications found</p></div>');
                    updateNotificationCount();
                }
            });
        }
    });
});
</script>

<style>
.unread {
    background-color: #f8f9fa;
    border-left: 4px solid #007bff;
}

.list-group-item {
    transition: all 0.3s ease;
}

.list-group-item:hover {
    background-color: #f8f9fa;
}

.notification-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}
</style>

</body>
</html>