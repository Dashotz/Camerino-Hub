<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['id']) || !isset($_GET['course_id'])) {
    echo '<div class="alert alert-danger">Invalid request</div>';
    exit();
}

$db = new DbConnector();
$course_id = (int)$_GET['course_id'];
$student_id = $_SESSION['id'];

// Fetch course details with proper table structure
$query = "SELECT 
    c.*,
    t.firstname, t.middlename, t.lastname, t.department,
    (SELECT COUNT(*) FROM student_courses WHERE course_id = c.course_id) as enrolled_students,
    (SELECT COUNT(*) FROM assignments WHERE course_id = c.course_id) as total_assignments,
    (SELECT COUNT(*) FROM student_submissions ss 
     JOIN assignments a ON ss.assignment_id = a.assignment_id 
     WHERE a.course_id = c.course_id AND ss.student_id = ?) as completed_assignments
FROM courses c
LEFT JOIN teachers t ON c.teacher_id = t.teacher_id
WHERE c.course_id = ? AND EXISTS (
    SELECT 1 FROM student_courses 
    WHERE student_id = ? AND course_id = c.course_id AND status = 'active'
)";

$stmt = $db->prepare($query);
$stmt->bind_param("iii", $student_id, $course_id, $student_id);
$stmt->execute();
$result = $stmt->get_result();
$course = $result->fetch_assoc();

if (!$course) {
    echo '<div class="alert alert-danger">Course not found</div>';
    exit();
}
?>

<div class="course-details">
    <div class="row">
        <div class="col-md-6">
            <h6 class="font-weight-bold">Course Information</h6>
            <p><strong>Course Name:</strong> <?php echo htmlspecialchars($course['course_name'] ?? 'Not specified'); ?></p>
            <p><strong>Course Description:</strong> <?php echo htmlspecialchars($course['description'] ?? 'No description available'); ?></p>
            <p><strong>Schedule:</strong> <?php echo htmlspecialchars($course['schedule'] ?? 'Not specified'); ?></p>
        </div>
        <div class="col-md-6">
            <h6 class="font-weight-bold">Instructor</h6>
            <p><strong>Name:</strong> <?php 
                $teacher_name = trim(
                    ($course['firstname'] ?? '') . ' ' . 
                    ($course['middlename'] ?? '') . ' ' . 
                    ($course['lastname'] ?? '')
                );
                echo $teacher_name ? htmlspecialchars($teacher_name) : 'Not assigned';
            ?></p>
            <p><strong>Department:</strong> <?php echo htmlspecialchars($course['department'] ?? 'Not specified'); ?></p>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <h6 class="font-weight-bold">Course Progress</h6>
            <div class="progress-stats">
                <p><strong>Students Enrolled:</strong> <?php echo (int)$course['enrolled_students']; ?></p>
                <p><strong>Total Assignments:</strong> <?php echo (int)$course['total_assignments']; ?></p>
                <p><strong>Completed Assignments:</strong> <?php echo (int)$course['completed_assignments']; ?></p>
                
                <?php 
                $completion_percentage = $course['total_assignments'] > 0 
                    ? round(($course['completed_assignments'] / $course['total_assignments']) * 100) 
                    : 0;
                ?>
                <div class="progress">
                    <div class="progress-bar bg-primary" 
                         role="progressbar" 
                         style="width: <?php echo $completion_percentage; ?>%" 
                         aria-valuenow="<?php echo $completion_percentage; ?>" 
                         aria-valuemin="0" 
                         aria-valuemax="100">
                        <?php echo $completion_percentage; ?>%
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.course-details {
    padding: 20px;
}

.course-details h6 {
    color: var(--primary-color);
    margin-bottom: 1rem;
}

.course-details p {
    margin-bottom: 0.5rem;
}

.course-details strong {
    color: var(--text-primary);
}

.progress {
    height: 20px;
    margin-top: 10px;
}

.progress-bar {
    font-size: 12px;
    line-height: 20px;
}

.progress-stats {
    background-color: var(--hover-bg);
    padding: 15px;
    border-radius: 8px;
}
</style>
