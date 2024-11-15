<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['id']) || !isset($_GET['subject_id'])) {
    echo '<div class="alert alert-danger">Invalid request</div>';
    exit();
}

$db = new DbConnector();
$student_id = $_SESSION['id'];
$section_subject_id = intval($_GET['subject_id']);

// Fetch detailed activity grades
$query = "
    SELECT 
        a.activity_id,
        a.title,
        a.type,
        a.points as max_points,
        a.due_date,
        COALESCE(sas.points, 0) as achieved_points,
        sas.submitted_at,
        CASE 
            WHEN sas.submission_id IS NOT NULL THEN 'Submitted'
            WHEN NOW() > a.due_date THEN 'Missing'
            ELSE 'Pending'
        END as status
    FROM activities a
    LEFT JOIN student_activity_submissions sas ON a.activity_id = sas.activity_id 
        AND sas.student_id = ?
    WHERE a.section_subject_id = ?
    AND a.status = 'active'
    ORDER BY a.due_date DESC";

try {
    $stmt = $db->prepare($query);
    $stmt->bind_param("ii", $student_id, $section_subject_id);
    $stmt->execute();
    $activities = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Calculate statistics
    $total_points = 0;
    $achieved_points = 0;
    $total_activities = count($activities);
    $submitted_activities = 0;

    foreach ($activities as $activity) {
        $total_points += $activity['max_points'];
        $achieved_points += $activity['achieved_points'];
        if ($activity['status'] === 'Submitted') {
            $submitted_activities++;
        }
    }

    $average_grade = $total_points > 0 ? round(($achieved_points / $total_points) * 100, 2) : 0;
    $completion_rate = $total_activities > 0 ? round(($submitted_activities / $total_activities) * 100, 2) : 0;
?>

<div class="grade-details-container">
    <!-- Summary Statistics -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="summary-card">
                <h6>Average Grade</h6>
                <div class="summary-value"><?php echo $average_grade; ?>%</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="summary-card">
                <h6>Completion Rate</h6>
                <div class="summary-value"><?php echo $completion_rate; ?>%</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="summary-card">
                <h6>Total Activities</h6>
                <div class="summary-value"><?php echo $total_activities; ?></div>
            </div>
        </div>
    </div>

    <!-- Activities Table -->
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Activity</th>
                    <th>Type</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Score</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($activities as $activity): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($activity['title']); ?></td>
                        <td>
                            <span class="badge badge-<?php echo getTypeBadgeClass($activity['type']); ?>">
                                <?php echo ucfirst($activity['type']); ?>
                            </span>
                        </td>
                        <td><?php echo date('M j, Y', strtotime($activity['due_date'])); ?></td>
                        <td>
                            <span class="badge badge-<?php echo getStatusBadgeClass($activity['status']); ?>">
                                <?php echo $activity['status']; ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($activity['status'] === 'Submitted'): ?>
                                <?php echo $activity['achieved_points']; ?>/<?php echo $activity['max_points']; ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.summary-card {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    text-align: center;
}

.summary-value {
    font-size: 24px;
    font-weight: bold;
    color: #1967d2;
}

.badge {
    padding: 5px 10px;
}
</style>

<?php
} catch (Exception $e) {
    echo '<div class="alert alert-danger">Error loading grade details.</div>';
}

function getTypeBadgeClass($type) {
    switch ($type) {
        case 'quiz':
            return 'info';
        case 'assignment':
            return 'primary';
        case 'activity':
            return 'success';
        default:
            return 'secondary';
    }
}

function getStatusBadgeClass($status) {
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