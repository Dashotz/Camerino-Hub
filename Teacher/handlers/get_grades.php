<?php
session_start();
require_once('../../db/dbConnector.php');
$db = new DbConnector();

if (!isset($_SESSION['teacher_id'])) {
    exit('Unauthorized');
}

$section_id = $_GET['section_id'] ?? 0;
$subject_id = $_GET['subject_id'] ?? 0;

// Get all students in the section
$students_query = "SELECT 
    s.student_id,
    s.firstname,
    s.lastname
FROM student s
JOIN student_sections ss ON s.student_id = ss.student_id
WHERE ss.section_id = ?
AND ss.status = 'active'
ORDER BY s.lastname, s.firstname";

$stmt = $db->prepare($students_query);
$stmt->bind_param("i", $section_id);
$stmt->execute();
$students = $stmt->get_result();

// Get all activities for the subject
$activities_query = "SELECT 
    a.activity_id,
    a.title,
    a.type,
    a.points as max_points,
    a.due_date
FROM activities a
JOIN section_subjects ss ON a.section_subject_id = ss.id
WHERE ss.section_id = ?
AND ss.subject_id = ?
AND a.status = 'active'
ORDER BY a.type, a.due_date";

$stmt = $db->prepare($activities_query);
$stmt->bind_param("ii", $section_id, $subject_id);
$stmt->execute();
$activities = $stmt->get_result();

// Store activities for later use
$activities_data = [];
while ($activity = $activities->fetch_assoc()) {
    $activities_data[] = $activity;
}
?>

<div class="table-responsive">
    <table class="table table-bordered grade-table">
        <thead>
            <tr>
                <th>Student Name</th>
                <?php foreach ($activities_data as $activity): ?>
                    <th class="text-center">
                        <?php echo htmlspecialchars($activity['title']); ?>
                        <br>
                        <span class="activity-type type-<?php echo $activity['type']; ?>">
                            <?php echo ucfirst($activity['type']); ?>
                        </span>
                        <br>
                        <small class="text-muted">
                            Max: <?php echo $activity['type'] === 'quiz' ? $activity['max_points'] : '100'; ?>
                        </small>
                        <br>
                        <small class="text-muted">
                            Due: <?php echo date('M d, Y', strtotime($activity['due_date'])); ?>
                        </small>
                    </th>
                <?php endforeach; ?>
                <th class="text-center">Average</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($student = $students->fetch_assoc()): ?>
                <tr class="student-row">
                    <td><?php echo htmlspecialchars($student['lastname'] . ', ' . $student['firstname']); ?></td>
                    <?php 
                    foreach ($activities_data as $activity): 
                        // Get student's submission for this activity
                        $submission_query = "SELECT 
                            submission_id,
                            points,
                            status,
                            IFNULL(updated_at, submitted_at) as submission_date
                        FROM student_activity_submissions
                        WHERE student_id = ? AND activity_id = ?";
                        
                        $stmt = $db->prepare($submission_query);
                        $stmt->bind_param("ii", $student['student_id'], $activity['activity_id']);
                        $stmt->execute();
                        $submission = $stmt->get_result()->fetch_assoc();
                    ?>
                        <td class="text-center">
                            <?php if ($submission): ?>
                                <input type="number" 
                                       class="form-control grade-input mx-auto"
                                       data-submission-id="<?php echo $submission['submission_id']; ?>"
                                       data-max-points="<?php echo $activity['type'] === 'quiz' ? $activity['max_points'] : 100; ?>"
                                       data-activity-type="<?php echo $activity['type']; ?>"
                                       value="<?php echo $submission['points']; ?>"
                                       min="0"
                                       max="<?php echo $activity['type'] === 'quiz' ? $activity['max_points'] : 100; ?>"
                                       step="0.01">
                                <small class="text-muted d-block mt-1">
                                    <?php echo date('M d, Y', strtotime($submission['submission_date'])); ?>
                                </small>
                                <span class="status-badge badge <?php 
                                    echo $submission['status'] === 'graded' ? 'badge-success' : 
                                        ($submission['status'] === 'submitted' ? 'badge-info' : 'badge-warning'); 
                                ?>">
                                    <?php echo ucfirst($submission['status']); ?>
                                </span>
                            <?php else: ?>
                                <div data-max-points="<?php echo $activity['type'] === 'quiz' ? $activity['max_points'] : 100; ?>"
                                     data-activity-type="<?php echo $activity['type']; ?>">
                                    <?php if (strtotime($activity['due_date']) < time()): ?>
                                        <span class="text-danger">Missing (0)</span>
                                    <?php else: ?>
                                        <span class="text-muted">Not yet submitted (0)</span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                    <td class="text-center average font-weight-bold">
                        <!-- Average will be calculated by JavaScript -->
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div> 