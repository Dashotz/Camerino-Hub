<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['id']) || !isset($_GET['course_id'])) {
    echo '<div class="alert alert-danger">Invalid request</div>';
    exit();
}

$db = new DbConnector();
$student_id = $_SESSION['id'];
$course_id = (int)$_GET['course_id'];

// Verify student is enrolled in the course
$verify_query = "SELECT 1 FROM student_courses 
                WHERE student_id = ? AND course_id = ? AND status = 'active'";
$stmt = $db->prepare($verify_query);
$stmt->bind_param("ii", $student_id, $course_id);
$stmt->execute();
if ($stmt->get_result()->num_rows === 0) {
    echo '<div class="alert alert-danger">You are not enrolled in this course</div>';
    exit();
}

// Modified query to match your table structure
$query = "SELECT 
    a.*,
    CASE 
        WHEN ss.student_id IS NOT NULL THEN 'Submitted'
        WHEN a.due_date < NOW() THEN 'Overdue'
        ELSE 'Pending'
    END as status,
    ss.student_id as submission_status
FROM assignments a
LEFT JOIN student_submissions ss ON a.assignment_id = ss.assignment_id 
    AND ss.student_id = ?
WHERE a.course_id = ?
ORDER BY a.due_date ASC";

$stmt = $db->prepare($query);
$stmt->bind_param("ii", $student_id, $course_id);
$stmt->execute();
$result = $stmt->get_result();
$assignments = $result->fetch_all(MYSQLI_ASSOC);
?>

<div class="assignments-list">
    <?php if (empty($assignments)): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle mr-2"></i>
            No assignments available for this course yet.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($assignments as $assignment): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($assignment['title']); ?></strong>
                                <br>
                                <small class="text-muted">
                                    <?php echo nl2br(htmlspecialchars(substr($assignment['description'], 0, 100))); ?>
                                    <?php echo strlen($assignment['description']) > 100 ? '...' : ''; ?>
                                </small>
                            </td>
                            <td>
                                <?php 
                                $due_date = new DateTime($assignment['due_date']);
                                echo $due_date->format('M d, Y h:i A'); 
                                ?>
                            </td>
                            <td>
                                <?php
                                $status_class = '';
                                switch ($assignment['status']) {
                                    case 'Submitted':
                                        $status_class = 'success';
                                        break;
                                    case 'Overdue':
                                        $status_class = 'danger';
                                        break;
                                    default:
                                        $status_class = 'warning';
                                }
                                ?>
                                <span class="badge badge-<?php echo $status_class; ?>">
                                    <?php echo $assignment['status']; ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($assignment['status'] !== 'Submitted'): ?>
                                    <button type="button" 
                                            class="btn btn-sm btn-primary submit-assignment"
                                            data-assignment-id="<?php echo $assignment['assignment_id']; ?>"
                                            data-toggle="modal" 
                                            data-target="#submitAssignmentModal">
                                        <i class="fas fa-upload mr-1"></i> Submit
                                    </button>
                                <?php else: ?>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-warning submit-assignment"
                                            data-assignment-id="<?php echo $assignment['assignment_id']; ?>"
                                            data-toggle="modal" 
                                            data-target="#submitAssignmentModal">
                                        <i class="fas fa-sync-alt mr-1"></i> Resubmit
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<style>
.assignments-list .table td {
    vertical-align: middle;
}

.assignments-list .badge {
    font-size: 0.875rem;
    padding: 0.4em 0.8em;
}

.assignments-list .btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.assignments-list small {
    font-size: 0.875rem;
}
</style>

<script>
$(document).ready(function() {
    $('.submit-assignment').click(function() {
        const assignmentId = $(this).data('assignment-id');
        // Handle submission in a separate modal or redirect to submission page
        window.location.href = 'submit_assignment.php?id=' + assignmentId;
    });
});
</script>
