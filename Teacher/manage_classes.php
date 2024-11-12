<?php
session_start();
require_once('../db/dbConnector.php');

// Check if teacher is logged in
if (!isset($_SESSION['teacher_id'])) {
    header("Location: Teacher-Login.php");
    exit();
}

$db = new DbConnector();
$teacher_id = $_SESSION['teacher_id'];

// Add new function to fetch subjects
function getSubjects($db) {
    $query = "SELECT id, subject_code, subject_name FROM subjects ORDER BY subject_code";
    $result = $db->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Handle POST requests for adding/editing/deleting classes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch($action) {
        case 'add':
            $section_name = $_POST['section_name'];
            $subject_id = $_POST['subject_id'];
            $schedule_day = $_POST['schedule_day'];
            $schedule_time = $_POST['schedule_time'];
            
            // First create or get section
            $section_query = "
                INSERT INTO sections (section_name) 
                VALUES (?) 
                ON DUPLICATE KEY UPDATE section_id = LAST_INSERT_ID(section_id)";
            $stmt = $db->prepare($section_query);
            $stmt->bind_param("s", $section_name);
            $stmt->execute();
            $section_id = $stmt->insert_id;
            
            // Then create section_subject relationship
            $query = "
                INSERT INTO section_subjects 
                (teacher_id, section_id, subject_id, schedule_day, schedule_time, academic_year_id) 
                VALUES (?, ?, ?, ?, ?, 
                    (SELECT id FROM academic_years WHERE status = 'active' LIMIT 1)
                )";
            $stmt = $db->prepare($query);
            $stmt->bind_param("iiiss", $teacher_id, $section_id, $subject_id, $schedule_day, $schedule_time);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Class added successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error adding class']);
            }
            exit;
            
        case 'edit':
            $class_id = $_POST['class_id'];
            $section_name = $_POST['section_name'];
            $subject_id = $_POST['subject_id'];
            $schedule_day = $_POST['schedule_day'];
            $schedule_time = $_POST['schedule_time'];
            
            $query = "
                UPDATE section_subjects ss
                JOIN sections sec ON ss.section_id = sec.section_id
                SET 
                    sec.section_name = ?,
                    ss.subject_id = ?,
                    ss.schedule_day = ?,
                    ss.schedule_time = ?
                WHERE ss.id = ? 
                AND ss.teacher_id = ?";
            $stmt = $db->prepare($query);
            $stmt->bind_param("sissii", $section_name, $subject_id, $schedule_day, $schedule_time, $class_id, $teacher_id);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Class updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error updating class']);
            }
            exit;
            
        case 'delete':
            $class_id = $_POST['class_id'];
            
            $query = "
                UPDATE section_subjects 
                SET status = 'inactive' 
                WHERE id = ? AND teacher_id = ?";
            $stmt = $db->prepare($query);
            $stmt->bind_param("ii", $class_id, $teacher_id);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Class deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error deleting class']);
            }
            exit;
    }
}

// Fetch subjects for the dropdown
$subjects = getSubjects($db);

// Fetch teacher's classes with related information
$classes_query = "
    SELECT 
        ss.id as class_id,
        sec.section_name,
        s.subject_code,
        s.subject_name,
        ss.schedule_day,
        ss.schedule_time,
        COUNT(DISTINCT sts.student_id) as student_count,
        COALESCE(
            (SELECT 
                SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) * 100.0 / 
                COUNT(*)
            FROM attendance a 
            WHERE a.section_subject_id = ss.id
            AND a.date >= CURDATE() - INTERVAL 30 DAY
            GROUP BY a.section_subject_id), 
            0
        ) as attendance_rate,
        COALESCE(
            (SELECT AVG(sas.points)
            FROM student_activity_submissions sas
            JOIN activities act ON sas.activity_id = act.activity_id
            WHERE act.section_subject_id = ss.id), 
            0
        ) as average_performance
    FROM section_subjects ss
    JOIN sections sec ON ss.section_id = sec.section_id
    JOIN subjects s ON ss.subject_id = s.id
    LEFT JOIN student_sections sts ON sec.section_id = sts.section_id
    WHERE ss.teacher_id = ?
        AND ss.status = 'active'
        AND ss.academic_year_id = (
            SELECT id FROM academic_years 
            WHERE status = 'active' 
            LIMIT 1
        )
    GROUP BY 
        ss.id, 
        sec.section_name, 
        s.subject_code, 
        s.subject_name, 
        ss.schedule_day, 
        ss.schedule_time
    ORDER BY sec.section_name";

$stmt = $db->prepare($classes_query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$classes_result = $stmt->get_result();
$classes = $classes_result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Classes - CamerinoHub</title>
    
    <!-- External CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/dashboard-shared.css">
    <link rel="stylesheet" href="css/manage-classes.css">
<body>
    <?php include 'includes/navigation.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="content-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1>My Classes</h1>
                        <p>Manage your classes and student assignments</p>
                    </div>
                </div>
            </div>

            <!-- Classes Grid -->
            <div class="row">
                <?php foreach ($classes as $class): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card class-card">
                            <div class="class-header">
                                <h5 class="mb-0"><?php echo htmlspecialchars($class['section_name']); ?></h5>
                                <small><?php echo htmlspecialchars($class['subject_code'] . ' - ' . $class['subject_name']); ?></small>
                            </div>
                            <div class="class-body">
                                <div class="schedule-info">
                                    <span class="schedule-badge">
                                        <i class="fas fa-calendar-alt"></i>
                                        <?php echo htmlspecialchars($class['schedule_day']); ?>
                                    </span>
                                    <span class="schedule-badge">
                                        <i class="fas fa-clock"></i>
                                        <?php echo htmlspecialchars($class['schedule_time']); ?>
                                    </span>
                                </div>
                                
                                <div class="class-stats">
                                    <div class="stat-item">
                                        <div class="stat-value"><?php echo $class['student_count']; ?></div>
                                        <div class="stat-label">Students</div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="stat-value">
                                            <?php echo number_format($class['attendance_rate'] ?? 0, 1); ?>%
                                        </div>
                                        <div class="stat-label">Attendance</div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="stat-value">
                                            <?php echo number_format($class['average_performance'], 1); ?>%
                                        </div>
                                        <div class="stat-label">Performance</div>
                                    </div>
                                </div>

                                <div class="class-actions">
                                    <a href="view_class.php?id=<?php echo $class['class_id']; ?>" 
                                       class="btn btn-outline-primary btn-sm w-100">
                                        <i class="fas fa-eye"></i> View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html> 