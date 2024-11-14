<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['teacher_id'])) {
    exit('Not authenticated');
}

$db = new DbConnector();
$teacher_id = $_SESSION['teacher_id'];

$where_conditions = ['a.teacher_id = ?', 'a.status = "active"'];
$params = [$teacher_id];
$types = 'i';

if (!empty($_GET['section_id'])) {
    $where_conditions[] = 'a.section_id = ?';
    $params[] = $_GET['section_id'];
    $types .= 'i';
}

if (!empty($_GET['subject_id'])) {
    $where_conditions[] = 'a.subject_id = ?';
    $params[] = $_GET['subject_id'];
    $types .= 'i';
}

$query = "SELECT 
            a.*,
            s.section_name,
            sub.subject_name,
            CONCAT(t.firstname, ' ', t.lastname) as teacher_name
          FROM announcements a
          JOIN sections s ON a.section_id = s.section_id
          JOIN subjects sub ON a.subject_id = sub.id
          JOIN teacher t ON a.teacher_id = t.teacher_id
          WHERE " . implode(' AND ', $where_conditions) . "
          ORDER BY a.created_at DESC";

$stmt = $db->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()):
?>
    <div class="announcement-card" id="announcement-<?php echo $row['id']; ?>">
        <div class="announcement-header">
            <div class="announcement-meta">
                <div class="announcement-type">
                    <?php
                    switch($row['type']) {
                        case 'quiz':
                            echo '<span class="badge badge-primary">Quiz</span>';
                            break;
                        case 'activity':
                            echo '<span class="badge badge-success">Activity</span>';
                            break;
                        case 'assignment':
                            echo '<span class="badge badge-warning">Assignment</span>';
                            break;
                        default:
                            echo '<span class="badge badge-secondary">Announcement</span>';
                    }
                    ?>
                </div>
                <div class="teacher-info">
                    <strong><?php echo htmlspecialchars($row['teacher_name']); ?></strong>
                    <span class="text-muted">
                        <?php echo htmlspecialchars($row['section_name']); ?> • 
                        <?php echo htmlspecialchars($row['subject_name']); ?>
                    </span>
                </div>
                <small class="text-muted">
                    <?php echo date('M d, Y h:i A', strtotime($row['created_at'])); ?>
                </small>
            </div>
            <div class="announcement-actions">
                <button class="btn btn-link" onclick="editAnnouncement(<?php echo $row['id']; ?>)">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-link text-danger" onclick="deleteAnnouncement(<?php echo $row['id']; ?>)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        <div class="announcement-content">
            <?php echo nl2br(htmlspecialchars($row['content'])); ?>
        </div>
        <?php if ($row['attachment']): ?>
            <div class="attachment">
                <i class="fas fa-paperclip"></i>
                <a href="../<?php echo htmlspecialchars($row['attachment']); ?>" target="_blank">
                    View Attachment
                </a>
            </div>
        <?php endif; ?>
    </div>
<?php endwhile;

if ($result->num_rows === 0) {
    exit();
}
?>
