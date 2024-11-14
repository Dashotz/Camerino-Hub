<?php
session_start();
require_once('../../db/dbConnector.php');

if (!isset($_SESSION['id'])) {
    exit('Unauthorized');
}

$db = new DbConnector();
$student_id = $_SESSION['id'];
$subject_id = isset($_POST['subject_id']) ? intval($_POST['subject_id']) : 0;

$activities_query = "
    SELECT 
        a.*,
        s.subject_name,
        s.id as subject_id,
        ss.section_id,
        sas.submission_id,
        sas.submitted_at,
        CASE 
            WHEN sas.submission_id IS NOT NULL THEN 'submitted'
            WHEN NOW() > a.due_date THEN 'late'
            ELSE 'assigned'
        END as status,
        t.firstname as teacher_fname,
        t.lastname as teacher_lname
    FROM activities a
    JOIN section_subjects ss ON a.section_subject_id = ss.id
    JOIN subjects s ON ss.subject_id = s.id
    JOIN teacher t ON ss.teacher_id = t.teacher_id
    JOIN student_sections sts ON ss.section_id = sts.section_id
    LEFT JOIN student_activity_submissions sas ON a.activity_id = sas.activity_id 
        AND sas.student_id = ?
    WHERE sts.student_id = ? 
    AND a.status = 'active'";

// Add subject filter if specified
if ($subject_id > 0) {
    $activities_query .= " AND s.id = ?";
}

$activities_query .= " ORDER BY a.created_at DESC";

$stmt = $db->prepare($activities_query);

if ($subject_id > 0) {
    $stmt->bind_param("iii", $student_id, $student_id, $subject_id);
} else {
    $stmt->bind_param("ii", $student_id, $student_id);
}

$stmt->execute();
$activities = $stmt->get_result();

if ($activities->num_rows > 0) {
    while ($activity = $activities->fetch_assoc()) {
        $status_class = match($activity['status']) {
            'submitted' => 'status-submitted',
            'late' => 'status-late',
            default => 'status-assigned'
        };
        
        $status_text = match($activity['status']) {
            'submitted' => 'Submitted',
            'late' => 'Missing',
            default => 'Assigned'
        };
        ?>
        <div class="activity-card">
            <div class="activity-header">
                <div class="activity-icon">
                    <i class="fas fa-book"></i>
                </div>
                <div class="activity-title">
                    <h3><?php echo htmlspecialchars($activity['title']); ?></h3>
                    <div class="activity-meta">
                        <?php echo htmlspecialchars($activity['subject_name']); ?> • 
                        Posted by <?php echo htmlspecialchars($activity['teacher_fname'] . ' ' . $activity['teacher_lname']); ?> • 
                        Due <?php echo date('M j, Y g:i A', strtotime($activity['due_date'])); ?>
                    </div>
                </div>
                <div class="activity-points">
                    <?php echo $activity['points']; ?> points
                </div>
            </div>
            
            <div class="activity-content">
                <?php echo nl2br(htmlspecialchars($activity['description'])); ?>
            </div>
            
            <div class="activity-actions">
                <span class="status-badge <?php echo $status_class; ?>">
                    <?php echo $status_text; ?>
                </span>
                <?php if($activity['status'] !== 'submitted'): ?>
                    <a href="view_activity.php?id=<?php echo $activity['activity_id']; ?>" 
                       class="btn btn-submit">
                        View Activity
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
} else {
    echo '<div class="no-activities">
            <i class="fas fa-tasks fa-3x mb-3"></i>
            <h3>No activities found</h3>
            <p>There are no activities for the selected filter</p>
          </div>';
}
?>
