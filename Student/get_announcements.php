<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['id'])) {
    exit('Not authenticated');
}

$db = new DbConnector();
$student_id = $_SESSION['id'];

// Get student's section first
$section_query = "SELECT section_id FROM student_sections 
                 WHERE student_id = ? AND status = 'active'";
$stmt = $db->prepare($section_query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$section_result = $stmt->get_result();
$section = $section_result->fetch_assoc();

if (!$section) {
    exit('No section assigned');
}

// Get announcements for student's section
$query = "SELECT 
            a.*,
            CONCAT(t.firstname, ' ', t.lastname) as teacher_name,
            s.section_name,
            sub.subject_name,
            CASE 
                WHEN a.type = 'quiz' THEN q.title
                WHEN a.type = 'activity' THEN act.title
                WHEN a.type = 'assignment' THEN asg.title
                ELSE NULL 
            END as reference_title,
            CASE 
                WHEN a.type = 'quiz' THEN q.due_date
                WHEN a.type = 'activity' THEN act.due_date
                WHEN a.type = 'assignment' THEN asg.due_date
                ELSE NULL 
            END as due_date
          FROM announcements a
          JOIN teacher t ON a.teacher_id = t.teacher_id
          JOIN sections s ON a.section_id = s.section_id
          JOIN subjects sub ON a.subject_id = sub.id
          LEFT JOIN activities q ON a.reference_id = q.activity_id AND a.type = 'quiz'
          LEFT JOIN activities act ON a.reference_id = act.activity_id AND a.type = 'activity'
          LEFT JOIN assignments asg ON a.reference_id = asg.assignment_id AND a.type = 'assignment'
          WHERE a.section_id = ? 
          AND a.status = 'active'
          ORDER BY a.created_at DESC";

$stmt = $db->prepare($query);
$stmt->bind_param("i", $section['section_id']);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()):
    $type_class = '';
    $type_icon = '';
    $type_text = '';
    
    switch($row['type']) {
        case 'quiz':
            $type_class = 'quiz-announcement';
            $type_icon = 'fa-question-circle';
            $type_text = 'Quiz';
            break;
        case 'activity':
            $type_class = 'activity-announcement';
            $type_icon = 'fa-tasks';
            $type_text = 'Activity';
            break;
        case 'assignment':
            $type_class = 'assignment-announcement';
            $type_icon = 'fa-book';
            $type_text = 'Assignment';
            break;
        default:
            $type_class = 'normal-announcement';
            $type_icon = 'fa-bullhorn';
            $type_text = 'Announcement';
    }
?>
    <div class="announcement-card <?php echo $type_class; ?>" id="announcement-<?php echo $row['id']; ?>">
        <div class="announcement-header">
            <div class="announcement-meta">
                <div class="announcement-type">
                    <span class="badge badge-<?php echo $type_class; ?>">
                        <i class="fas <?php echo $type_icon; ?>"></i> <?php echo $type_text; ?>
                    </span>
                    <?php if ($row['reference_title']): ?>
                        <span class="reference-title">
                            <?php echo htmlspecialchars($row['reference_title']); ?>
                        </span>
                    <?php endif; ?>
                </div>
                <div class="teacher-info">
                    <strong><?php echo htmlspecialchars($row['teacher_name']); ?></strong>
                    <span class="text-muted">
                        <?php echo htmlspecialchars($row['section_name']); ?> • 
                        <?php echo htmlspecialchars($row['subject_name']); ?>
                    </span>
                </div>
                <div class="announcement-time">
                    <small class="text-muted">
                        <i class="far fa-clock"></i> 
                        <?php echo date('M d, Y h:i A', strtotime($row['created_at'])); ?>
                    </small>
                    <?php if ($row['due_date']): ?>
                        <small class="text-danger">
                            <i class="fas fa-hourglass-end"></i> 
                            Due: <?php echo date('M d, Y h:i A', strtotime($row['due_date'])); ?>
                        </small>
                    <?php endif; ?>
                </div>
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
        
        <?php if ($row['type'] !== 'normal'): ?>
            <div class="announcement-actions">
                <a href="view_<?php echo $row['type']; ?>.php?id=<?php echo $row['reference_id']; ?>" 
                   class="btn btn-primary btn-sm">
                    <i class="fas fa-external-link-alt"></i>
                    View <?php echo ucfirst($row['type']); ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
<?php endwhile;

if ($result->num_rows === 0) {
    echo '<div class="no-announcements">No announcements available.</div>';
}
?>
