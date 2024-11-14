<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['id'])) {
    exit('Not authenticated');
}

$db = new DbConnector();
$student_id = $_SESSION['id'];

// Build the query to get announcements from teachers teaching the student's sections
$query = "
    SELECT DISTINCT 
        a.*,
        t.firstname as teacher_firstname,
        t.lastname as teacher_lastname,
        s.section_name,
        sub.subject_name
    FROM announcements a
    JOIN teacher t ON a.teacher_id = t.teacher_id
    JOIN sections s ON a.section_id = s.section_id
    JOIN subjects sub ON a.subject_id = sub.id
    JOIN student_sections ss ON s.section_id = ss.section_id
    JOIN section_subjects subs ON (s.section_id = subs.section_id AND subs.subject_id = a.subject_id)
    WHERE ss.student_id = ? 
    AND ss.status = 'active'
    AND a.status = 'active'
    ORDER BY a.created_at DESC";

$stmt = $db->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo '<div class="no-announcements">
            <div class="empty-state">
                <i class="fas fa-bullhorn"></i>
                <h4>No Announcements</h4>
                <p>There are no announcements to display at this time.</p>
            </div>
          </div>';
    exit();
}

while ($row = $result->fetch_assoc()):
?>
    <div class="announcement-card" id="announcement-<?php echo $row['id']; ?>">
        <div class="teacher-header">
            <div class="teacher-profile">
                <i class="fas fa-user-circle profile-icon"></i>
                <div class="teacher-details">
                    <div class="teacher-name">
                        <?php echo htmlspecialchars($row['teacher_firstname'] . ' ' . $row['teacher_lastname']); ?>
                    </div>
                    <div class="teacher-meta">
                        <span class="section-badge">
                            <i class="fas fa-users"></i> 
                            <?php echo htmlspecialchars($row['section_name']); ?>
                        </span>
                        <span class="subject-badge">
                            <i class="fas fa-book"></i> 
                            <?php echo htmlspecialchars($row['subject_name']); ?>
                        </span>
                        <span class="timestamp">
                            <i class="far fa-clock"></i> 
                            <?php echo date('M d, Y h:i A', strtotime($row['created_at'])); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="announcement-content">
            <?php echo nl2br(htmlspecialchars($row['content'])); ?>
        </div>

        <?php if ($row['attachment']): ?>
            <a href="../<?php echo htmlspecialchars($row['attachment']); ?>" class="attachment" target="_blank">
                <i class="fas fa-paperclip"></i>
                View Attachment
            </a>
        <?php endif; ?>
    </div>
<?php endwhile; ?>
