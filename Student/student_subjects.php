<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: Student-Login.php");
    exit();
}

require_once('../db/dbConnector.php');
$db = new DbConnector();
$student_id = $_SESSION['id'];

// Fetch student's subjects
$subjects_query = "SELECT 
    s.id as subject_id,
    s.subject_name,
    s.subject_code,
    CONCAT(t.firstname, ' ', t.lastname) as teacher_name,
    ss.schedule_day,
    ss.schedule_time,
    sec.section_name,
    sec.grade_level,
    (SELECT COUNT(*) 
     FROM activities a 
     WHERE a.section_subject_id = ss.id 
     AND a.status = 'active') as total_activities,
    (SELECT COUNT(*) 
     FROM student_activity_submissions sas
     JOIN activities a ON sas.activity_id = a.activity_id
     WHERE a.section_subject_id = ss.id 
     AND sas.student_id = ?) as completed_activities
FROM student_sections st_sec
JOIN sections sec ON st_sec.section_id = sec.section_id
JOIN section_subjects ss ON sec.section_id = ss.section_id
JOIN subjects s ON ss.subject_id = s.id
JOIN teacher t ON ss.teacher_id = t.teacher_id
WHERE st_sec.student_id = ?
AND st_sec.status = 'active'
AND ss.status = 'active'
ORDER BY ss.schedule_day, ss.schedule_time";

$stmt = $db->prepare($subjects_query);
$stmt->bind_param("ii", $student_id, $student_id);
$stmt->execute();
$subjects = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Group subjects by day for the schedule view
$schedule = [];
$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
foreach ($days as $day) {
    $schedule[$day] = array_filter($subjects, function($subject) use ($day) {
        return $subject['schedule_day'] === $day;
    });
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Subjects - Student Dashboard</title>
    
    <!-- External CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="../images/light-logo.png">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/dashboard-shared.css">
    <style>
        .subject-card {
            border: none;
            border-radius: 12px;
            transition: transform 0.3s ease;
            margin-bottom: 20px;
        }

        .subject-card:hover {
            transform: translateY(-5px);
        }

        .subject-card .card-header {
            background: var(--primary-color);
            color: white;
            border-radius: 12px 12px 0 0;
            padding: 15px 20px;
        }

        .schedule-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
        }

        .schedule-table td {
            padding: 15px;
            background: white;
            border-radius: 8px;
        }

        .schedule-table .time-slot {
            font-weight: 500;
            color: var(--primary-color);
            width: 120px;
        }

        .progress {
            height: 8px;
            border-radius: 4px;
        }

        .teacher-info {
            display: flex;
            align-items: center;
            margin-top: 10px;
        }

        .teacher-info img {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            margin-right: 10px;
        }

        /* Search Bar Styles */
.search-container {
    position: relative;
    max-width: 300px;
}

.search-input {
    width: 100%;
    padding: 0.5rem 1rem 0.5rem 2.5rem;
    border: 1px solid #e0e0e0;
    border-radius: 20px;
    background: #f8f9fa;
    transition: all 0.3s ease;
}

.search-input:focus {
    outline: none;
    border-color: #3498db;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

.search-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
}

/* Header Layout */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.page-title {
    margin: 0;
    color: #2c3e50;
    font-size: 1.75rem;
    font-weight: 500;
}
    </style>
</head>
<body>
    <?php include 'includes/navigation.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="content-header d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1>My Subjects</h1>
                    <p class="text-muted">View your enrolled subjects and schedule</p>
                </div>
            </div>

            <!-- Updated Subjects Grid View -->
            <div class="row mb-4">
                <?php foreach ($subjects as $subject): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card subject-card h-100 shadow-sm">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><?php echo htmlspecialchars($subject['subject_name']); ?></h5>
                                <span class="badge badge-light"><?php echo htmlspecialchars($subject['subject_code']); ?></span>
                            </div>
                            <div class="card-body">
                                <div class="teacher-info">
                                    <div class="teacher-avatar">
                                        <i class="fas fa-user-tie"></i>
                                    </div>
                                    <div class="teacher-details">
                                        <span class="teacher-name"><?php echo htmlspecialchars($subject['teacher_name']); ?></span>
                                        <small class="text-muted d-block">Instructor</small>
                                    </div>
                                </div>
                                <div class="schedule-info mt-4">
                                    <div class="schedule-item">
                                        <i class="fas fa-clock text-primary"></i>
                                        <span><?php echo $subject['schedule_day'] . ' ' . date('g:i A', strtotime($subject['schedule_time'])); ?></span>
                                    </div>
                                    <div class="schedule-item mt-2">
                                        <i class="fas fa-users text-primary"></i>
                                        <span><?php echo htmlspecialchars($subject['section_name']); ?></span>
                                    </div>
                                </div>
                                <div class="progress-info mt-4">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Course Progress</span>
                                        <span class="font-weight-bold">
                                            <?php 
                                                $progress = $subject['total_activities'] > 0 
                                                    ? round(($subject['completed_activities'] / $subject['total_activities']) * 100) 
                                                    : 0;
                                                echo $progress . '%';
                                            ?>
                                        </span>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-success" 
                                             style="width: <?php echo $progress; ?>%"
                                             role="progressbar"
                                             aria-valuenow="<?php echo $progress; ?>"
                                             aria-valuemin="0"
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Schedule View -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Weekly Schedule</h5>
                </div>
                <div class="card-body">
                    <table class="schedule-table">
                        <?php foreach ($schedule as $day => $day_subjects): ?>
                            <?php if (!empty($day_subjects)): ?>
                                <tr>
                                    <td class="font-weight-bold text-muted" colspan="2"><?php echo $day; ?></td>
                                </tr>
                                <?php foreach ($day_subjects as $subject): ?>
                                    <tr>
                                        <td class="time-slot">
                                            <?php echo date('g:i A', strtotime($subject['schedule_time'])); ?>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-0"><?php echo htmlspecialchars($subject['subject_name']); ?></h6>
                                                    <small class="text-muted">
                                                        <?php echo htmlspecialchars($subject['teacher_name']); ?> • 
                                                        <?php echo htmlspecialchars($subject['section_name']); ?>
                                                    </small>
                                                </div>
                                                <span class="badge badge-primary"><?php echo htmlspecialchars($subject['subject_code']); ?></span>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>