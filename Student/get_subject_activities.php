<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['id']) || !isset($_GET['subject_id'])) {
    echo '<div class="alert alert-danger">Invalid request</div>';
    exit();
}

$db = new DbConnector();
$student_id = $_SESSION['id'];
$subject_id = intval($_GET['subject_id']);

// Fetch activities for the subject
$query = "
    SELECT 
        a.activity_id,
        a.title,
        a.description,
        a.type,
        a.due_date,
        a.points,
        COALESCE(sas.points, 0) as achieved_points,
        CASE 
            WHEN sas.submission_id IS NOT NULL THEN 'Submitted'
            WHEN NOW() > a.due_date THEN 'Missing'
            ELSE 'Pending'
        END as status
    FROM section_subjects ss
    JOIN activities a ON ss.id = a.section_subject_id
    LEFT JOIN student_activity_submissions sas ON a.activity_id = sas.activity_id 
        AND sas.student_id = ?
    WHERE ss.subject_id = ? AND a.status = 'active'
    ORDER BY a.due_date DESC";

try {
    $stmt = $db->prepare($query);
    $stmt->bind_param("ii", $student_id, $subject_id);
    $stmt->execute();
    $activities = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Group activities by type
    $grouped_activities = [
        'activity' => [],
        'quiz' => [],
        'assignment' => []
    ];

    foreach ($activities as $activity) {
        $grouped_activities[$activity['type']][] = $activity;
    }
?>

<div class="tab-content">
    <!-- Activities Tab -->
    <div class="tab-pane fade show active" id="activities">
        <?php echo generateActivityList($grouped_activities['activity'], 'activity'); ?>
    </div>
    
    <!-- Quizzes Tab -->
    <div class="tab-pane fade" id="quizzes">
        <?php echo generateActivityList($grouped_activities['quiz'], 'quiz'); ?>
    </div>
    
    <!-- Assignments Tab -->
    <div class="tab-pane fade" id="assignments">
        <?php echo generateActivityList($grouped_activities['assignment'], 'assignment'); ?>
    </div>
</div>

<?php
} catch (Exception $e) {
    echo '<div class="alert alert-danger">Error loading activities.</div>';
}

function generateActivityList($activities, $type) {
    if (empty($activities)) {
        return "<div class='alert alert-info'>No {$type}s available.</div>";
    }

    $output = '<div class="list-group">';
    foreach ($activities as $activity) {
        $status_class = getStatusClass($activity['status']);
        $due_date = date('M j, Y', strtotime($activity['due_date']));
        
        $output .= "
            <div class='list-group-item'>
                <div class='d-flex justify-content-between align-items-center'>
                    <div>
                        <h6 class='mb-1'>{$activity['title']}</h6>
                        <small class='text-muted'>Due: {$due_date}</small>
                    </div>
                    <div class='text-right'>
                        <span class='badge badge-{$status_class}'>{$activity['status']}</span>
                        <a href='view_activity.php?id={$activity['activity_id']}' 
                           class='btn btn-sm btn-primary ml-2'>
                            View Details
                        </a>
                    </div>
                </div>
            </div>";
    }
    $output .= '</div>';
    return $output;
}

function getStatusClass($status) {
    switch ($status) {
        case 'Submitted':
            return 'success';
        case 'Missing':
            return 'danger';
        case 'Pending':
            return 'warning';
        default:
            return 'secondary';
    }
}
?> 