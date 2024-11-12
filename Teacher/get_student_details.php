<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['teacher_id']) || !isset($_GET['student_id'])) {
    exit('Unauthorized access');
}

$db = new DbConnector();
$student_id = $_GET['student_id'];
$teacher_id = $_SESSION['teacher_id'];

// Fetch detailed student information
$verify_query = "SELECT 
    s.*,
    c.section_name,
    c.schedule_day,
    c.schedule_time,
    COALESCE(AVG(ss.grade), 0) as average_grade,
    COUNT(DISTINCT ss.submission_id) as total_submissions,
    COUNT(DISTINCT a.assignment_id) as total_assignments,
    (SELECT COUNT(*) FROM attendance att 
     WHERE att.student_id = s.student_id 
     AND att.status = 'present') as attendance_present,
    (SELECT COUNT(*) FROM attendance att 
     WHERE att.student_id = s.student_id) as total_attendance_days
FROM student s
JOIN class_students cs ON s.student_id = cs.student_id
JOIN classes c ON cs.class_id = c.class_id
LEFT JOIN student_submissions ss ON s.student_id = ss.student_id
LEFT JOIN assignments a ON a.class_id = c.class_id
WHERE c.teacher_id = ? AND s.student_id = ?
GROUP BY s.student_id";

$stmt = $db->prepare($verify_query);
$stmt->bind_param("ii", $teacher_id, $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    exit('Student not found');
}

$student = $result->fetch_assoc();

// Get recent submissions
$recent_submissions_query = "SELECT 
    ss.submission_id,
    ss.submitted_at,
    ss.grade,
    a.title as assignment_title
FROM student_submissions ss
JOIN assignments a ON ss.assignment_id = a.assignment_id
WHERE ss.student_id = ? AND a.teacher_id = ?
ORDER BY ss.submitted_at DESC
LIMIT 5";

$stmt = $db->prepare($recent_submissions_query);
$stmt->bind_param("ii", $student_id, $teacher_id);
$stmt->execute();
$recent_submissions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Calculate attendance rate
$attendance_rate = $student['total_attendance_days'] > 0 
    ? ($student['attendance_present'] / $student['total_attendance_days']) * 100 
    : 0;

// Calculate submission rate
$submission_rate = $student['total_assignments'] > 0 
    ? ($student['total_submissions'] / $student['total_assignments']) * 100 
    : 0;
?>

<div class="student-details">
    <div class="row">
        <div class="col-md-6">
            <h6>Personal Information</h6>
            <table class="table table-sm">
                <tr>
                    <th width="35%">Name:</th>
                    <td><?php echo htmlspecialchars($student['firstname'] . ' ' . $student['lastname']); ?></td>
                </tr>
                <tr>
                    <th>Email:</th>
                    <td><?php echo htmlspecialchars($student['email']); ?></td>
                </tr>
                <tr>
                    <th>Class:</th>
                    <td><?php echo htmlspecialchars($student['section_name']); ?></td>
                </tr>
                <tr>
                    <th>Schedule:</th>
                    <td>
                        <?php echo htmlspecialchars($student['schedule_day'] . ' at ' . 
                            date('g:i A', strtotime($student['schedule_time']))); ?>
                    </td>
                </tr>
                <tr>
                    <th>Student ID:</th>
                    <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                </tr>
            </table>

            <h6 class="mt-4">Recent Submissions</h6>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Assignment</th>
                            <th>Submitted</th>
                            <th>Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recent_submissions)): ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted">No recent submissions</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recent_submissions as $submission): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($submission['assignment_title']); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($submission['submitted_at'])); ?></td>
                                    <td>
                                        <?php if ($submission['grade'] !== null): ?>
                                            <span class="badge badge-success">
                                                <?php echo number_format($submission['grade'], 1); ?>%
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-6">
            <h6>Academic Performance</h6>
            <div class="performance-stats">
                <div class="stat-card">
                    <div class="stat-label">Overall Grade</div>
                    <div class="stat-value">
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" 
                                 style="width: <?php echo $student['average_grade']; ?>%"
                                 aria-valuenow="<?php echo $student['average_grade']; ?>" 
                                 aria-valuemin="0" aria-valuemax="100">
                                <?php echo number_format($student['average_grade'], 1); ?>%
                            </div>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Attendance Rate</div>
                    <div class="stat-value">
                        <div class="progress">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: <?php echo $attendance_rate; ?>%"
                                 aria-valuenow="<?php echo $attendance_rate; ?>" 
                                 aria-valuemin="0" aria-valuemax="100">
                                <?php echo number_format($attendance_rate, 1); ?>%
                            </div>
                        </div>
                    </div>
                    <small class="text-muted">
                        Present: <?php echo $student['attendance_present']; ?> / 
                        Total: <?php echo $student['total_attendance_days']; ?> days
                    </small>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Assignment Completion</div>
                    <div class="stat-value">
                        <div class="progress">
                            <div class="progress-bar bg-info" role="progressbar" 
                                 style="width: <?php echo $submission_rate; ?>%"
                                 aria-valuenow="<?php echo $submission_rate; ?>" 
                                 aria-valuemin="0" aria-valuemax="100">
                                <?php echo number_format($submission_rate, 1); ?>%
                            </div>
                        </div>
                    </div>
                    <small class="text-muted">
                        Submitted: <?php echo $student['total_submissions']; ?> / 
                        Total: <?php echo $student['total_assignments']; ?> assignments
                    </small>
                </div>
            </div>

            <h6 class="mt-4">Performance Summary</h6>
            <div class="performance-summary">
                <div class="alert alert-info">
                    <i class="fas fa-chart-line"></i>
                    <?php if ($student['average_grade'] >= 90): ?>
                        Excellent performance! Consistently submitting high-quality work.
                    <?php elseif ($student['average_grade'] >= 80): ?>
                        Good performance. Shows strong understanding of the material.
                    <?php elseif ($student['average_grade'] >= 70): ?>
                        Satisfactory performance. Some room for improvement.
                    <?php else: ?>
                        Needs improvement. Consider scheduling a consultation.
                    <?php endif; ?>
                </div>

                <?php if ($attendance_rate < 80): ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        Attendance needs improvement. Currently below 80% attendance rate.
                    </div>
                <?php endif; ?>

                <?php if ($submission_rate < 90): ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-tasks"></i>
                        Has <?php echo $student['total_assignments'] - $student['total_submissions']; ?> 
                        pending assignment(s) to submit.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.performance-stats {
    margin-bottom: 20px;
}

.stat-card {
    margin-bottom: 15px;
    padding: 10px;
    background-color: #f8f9fa;
    border-radius: 5px;
}

.stat-label {
    font-weight: 500;
    margin-bottom: 5px;
}

.progress {
    height: 20px;
    margin-bottom: 5px;
}

.progress-bar {
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 500;
}

.performance-summary .alert {
    margin-bottom: 10px;
    padding: 10px 15px;
}

.performance-summary .alert i {
    margin-right: 8px;
}

.badge {
    padding: 5px 10px;
}
</style>
