<?php
session_start();

// Check if user is logged in and is a student
if (!isset($_SESSION['id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'student') {
    error_log("Session check failed: " . json_encode($_SESSION));
    header("Location: ../login.php");
    exit();
}

// Check for active session and device status
require_once('../db/dbConnector.php');
$db = new DbConnector();
$student_id = $_SESSION['id'];

// Generate or get session ID
if (!isset($_SESSION['session_id'])) {
    $_SESSION['session_id'] = session_id();
}

// Check if this is a different session
$check_session = "SELECT session_id FROM student WHERE student_id = ?";
$stmt = $db->prepare($check_session);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$current_session = $result->fetch_assoc();

if ($current_session && $current_session['session_id'] !== $_SESSION['session_id']) {
    error_log("Session mismatch: Current=" . $current_session['session_id'] . " vs Session=" . $_SESSION['session_id']);
    // Force logout
    session_destroy();
    header("Location: ../login.php?error=multiple_login");
    exit();
}

// Verify session data is complete
if (!isset($_SESSION['firstname']) || !isset($_SESSION['lastname'])) {
    session_destroy();
    header("Location: ../login.php?error=incomplete_session");
    exit();
}

// Update user status and session
$update_status = "UPDATE student SET 
    user_online = 1, 
    session_id = ? 
    WHERE student_id = ?";
$stmt = $db->prepare($update_status);
$stmt->bind_param("si", $_SESSION['session_id'], $student_id);
$stmt->execute();

// Get user data
require_once('../db/dbConnector.php');
$db = new DbConnector();
$student_id = $_SESSION['id'];

// Fetch student's basic information
$query = "SELECT s.*, sec.section_name, sec.grade_level 
          FROM student s
          LEFT JOIN student_sections ss ON s.student_id = ss.student_id
          LEFT JOIN sections sec ON ss.section_id = sec.section_id
          WHERE s.student_id = ? AND ss.status = 'active'";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student_info = $stmt->get_result()->fetch_assoc();

// Fetch recent activities
$activities_query = "SELECT 
    a.title,
    a.type,
    a.created_at as activity_date,
    sec.section_name,
    sub.subject_name,
    sub.subject_code,
    COALESCE(sas.points, 'Not submitted') as score,
    CASE 
        WHEN sas.submission_id IS NOT NULL THEN 'Submitted'
        WHEN a.due_date < NOW() THEN 'Overdue'
        ELSE 'Pending'
    END as status
FROM student_sections ss
JOIN sections sec ON ss.section_id = sec.section_id
JOIN section_subjects ssub ON sec.section_id = ssub.section_id
JOIN subjects sub ON ssub.subject_id = sub.id
JOIN activities a ON ssub.id = a.section_subject_id
LEFT JOIN student_activity_submissions sas ON a.activity_id = sas.activity_id 
    AND sas.student_id = ?
WHERE ss.student_id = ? 
    AND ss.status = 'active'
    AND ssub.status = 'active'
    AND ss.academic_year_id = (SELECT id FROM academic_years WHERE status = 'active' LIMIT 1)
ORDER BY a.created_at DESC
LIMIT 5";

$stmt = $db->prepare($activities_query);
$stmt->bind_param("ii", $student_id, $student_id);
$stmt->execute();
$recent_activities = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch upcoming assignments
$upcoming_tasks_query = "
    SELECT 
        a.activity_id,
        a.title,
        a.due_date,
        s.subject_name as course_name
    FROM activities a
    JOIN section_subjects ss ON a.section_subject_id = ss.id
    JOIN subjects s ON ss.subject_id = s.id
    JOIN student_sections sts ON ss.section_id = sts.section_id
    WHERE sts.student_id = ?
    AND a.status = 'active'
    AND sts.status = 'active'
    AND a.due_date > NOW()
    ORDER BY a.due_date ASC
    LIMIT 5";

try {
    $stmt = $db->prepare($upcoming_tasks_query);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $upcoming_tasks = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    error_log("Error fetching upcoming tasks: " . $e->getMessage());
    $upcoming_tasks = [];
}

// Calculate attendance rate
$attendance_query = "SELECT 
    COALESCE(
        COUNT(CASE WHEN a.status = 'present' THEN 1 END) * 100.0 / NULLIF(COUNT(*), 0),
        0
    ) as attendance_rate
FROM student_sections ss
JOIN section_subjects ssub ON ss.section_id = ssub.section_id
JOIN attendance a ON ssub.id = a.section_subject_id AND ss.student_id = a.student_id
WHERE ss.student_id = ?
    AND ss.status = 'active'
    AND ssub.status = 'active'
    AND ss.academic_year_id = (SELECT id FROM academic_years WHERE status = 'active' LIMIT 1)
    AND a.date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";

$stmt = $db->prepare($attendance_query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$attendance_result = $stmt->get_result()->fetch_assoc();
$attendance_rate = number_format($attendance_result['attendance_rate'] ?? 0, 1);

// Get course count and pending tasks in one query for stats cards
$stats_query = "SELECT 
    (
        SELECT COUNT(DISTINCT ssub.subject_id) 
        FROM student_sections ss
        JOIN section_subjects ssub ON ss.section_id = ssub.section_id
        WHERE ss.student_id = ? 
        AND ss.status = 'active'
        AND ssub.status = 'active'
        AND ss.academic_year_id = (SELECT id FROM academic_years WHERE status = 'active' LIMIT 1)
    ) as course_count,
    (
        SELECT COUNT(*) 
        FROM activities a
        JOIN section_subjects ssub ON a.section_subject_id = ssub.id
        JOIN student_sections ss ON ssub.section_id = ss.section_id
        WHERE ss.student_id = ?
        AND ss.status = 'active'
        AND ssub.status = 'active'
        AND ss.academic_year_id = (SELECT id FROM academic_years WHERE status = 'active' LIMIT 1)
        AND a.type = 'assignment'
        AND a.due_date > NOW()
        AND a.activity_id NOT IN (
            SELECT activity_id 
            FROM student_activity_submissions 
            WHERE student_id = ?
        )
    ) as pending_count";

// Execute stats query
$stmt = $db->prepare($stats_query);
$stmt->bind_param("iii", $student_id, $student_id, $student_id);
$stmt->execute();
$stats_result = $stmt->get_result()->fetch_assoc();

// Update student info with stats
$student_info['course_count'] = $stats_result['course_count'] ?? 0;
$student_info['pending_count'] = $stats_result['pending_count'] ?? 0;

// Calculate average grade
$grades_query = "
    SELECT 
        ROUND(
            AVG(
                CASE 
                    -- Weight quiz scores (30%)
                    WHEN a.type = 'quiz' THEN (sas.points / a.points * 100) * 0.30
                    -- Weight activities (30%)
                    WHEN a.type = 'activity' THEN (sas.points / a.points * 100) * 0.30
                    -- Weight assignments (40%)
                    WHEN a.type = 'assignment' THEN (sas.points / a.points * 100) * 0.40
                END
            ), 1
        ) as average_grade,
        COUNT(DISTINCT CASE WHEN a.type = 'quiz' THEN sas.submission_id END) as quiz_count,
        COUNT(DISTINCT CASE WHEN a.type = 'activity' THEN sas.submission_id END) as activity_count,
        COUNT(DISTINCT CASE WHEN a.type = 'assignment' THEN sas.submission_id END) as assignment_count
    FROM student_sections ss
    JOIN section_subjects ssub ON ss.section_id = ssub.section_id
    JOIN activities a ON ssub.id = a.section_subject_id
    JOIN student_activity_submissions sas ON a.activity_id = sas.activity_id 
        AND sas.student_id = ss.student_id
    WHERE ss.student_id = ?
        AND ss.status = 'active'
        AND ssub.status = 'active'
        AND ss.academic_year_id = (
            SELECT id FROM academic_years 
            WHERE status = 'active' 
            LIMIT 1
        )
        AND sas.points IS NOT NULL
    GROUP BY ss.student_id";

$stmt = $db->prepare($grades_query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$grades_result = $stmt->get_result()->fetch_assoc();

// Calculate the weighted average only if there are submissions
if ($grades_result && 
    ($grades_result['quiz_count'] > 0 || 
     $grades_result['activity_count'] > 0 || 
     $grades_result['assignment_count'] > 0)) {
    $average_grade = $grades_result['average_grade'];
} else {
    $average_grade = 0;
}

// Format the average grade
$average_grade = number_format($average_grade, 1);

function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    
    if ($diff < 60) {
        return "Just now";
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . " minute" . ($mins > 1 ? "s" : "") . " ago";
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . " hour" . ($hours > 1 ? "s" : "") . " ago";
    } else {
        $days = floor($diff / 86400);
        return $days . " day" . ($days > 1 ? "s" : "") . " ago";
    }
}

function formatDueDate($date) {
    $dueDate = strtotime($date);
    $now = time();
    $diff = $dueDate - $now;
    
    if ($diff < 0) {
        return "Overdue";
    } elseif ($diff < 86400) {
        return "Today";
    } elseif ($diff < 172800) {
        return "Tomorrow";
    } else {
        return date('M j', $dueDate);
    }
}

function getActivityTypeClass($type) {
    $classes = [
        'assignment' => 'bg-primary',
        'quiz' => 'bg-warning',
        'activity' => 'bg-success'
    ];
    return $classes[$type] ?? 'bg-secondary';
}

function getActivityTypeIcon($type) {
    $icons = [
        'assignment' => 'fa-file-alt',
        'quiz' => 'fa-question-circle',
        'activity' => 'fa-tasks'
    ];
    return $icons[$type] ?? 'fa-circle';
}

function getStatusBadgeClass($status) {
    $classes = [
        'Submitted' => 'badge-success',
        'Pending' => 'badge-warning',
        'Overdue' => 'badge-danger'
    ];
    return $classes[$status] ?? 'badge-secondary';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Gov D.M. Camerino</title>
    
    <!-- External CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Product+Sans:wght@400;700&display=swap" rel="stylesheet">
    <link rel="icon" href="../images/light-logo.png">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/dashboard-shared.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
    <?php include 'includes/navigation.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Welcome Section -->
            <div class="welcome-section">
                <h1>Welcome, <?php echo htmlspecialchars($student_info['firstname'] ?? 'Student'); ?>!</h1>
                <p>Here's your learning overview</p>
            </div>

            <!-- Attendance Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-clock mr-2"></i>Mark Attendance</h5>
                        </div>
                        <div class="card-body">
                            <?php
                            // Get all active subjects for the student
                            $subjects_query = "SELECT 
                                ss.id as section_subject_id,
                                s.subject_name
                            FROM student_sections sts
                            JOIN section_subjects ss ON sts.section_id = ss.section_id
                            JOIN subjects s ON ss.subject_id = s.id
                            WHERE sts.student_id = ?
                            AND ss.status = 'active'";
                            
                            $stmt = $db->prepare($subjects_query);
                            $stmt->bind_param("i", $student_id);
                            $stmt->execute();
                            $subjects = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                            
                            if (!empty($subjects)) {
                                foreach ($subjects as $subject) {
                                    // Check if attendance already marked
                                    $check_attendance = "SELECT id FROM attendance 
                                        WHERE student_id = ? 
                                        AND section_subject_id = ? 
                                        AND date = CURRENT_DATE()";
                                    $stmt = $db->prepare($check_attendance);
                                    $stmt->bind_param("ii", $student_id, $subject['section_subject_id']);
                                    $stmt->execute();
                                    $existing_attendance = $stmt->get_result()->fetch_assoc();
                                    
                                    if (!$existing_attendance) {
                                        echo '<div class="attendance-card mb-3">';
                                        echo '<h6>' . htmlspecialchars($subject['subject_name']) . '</h6>';
                                        echo '<button class="btn btn-primary mark-attendance" 
                                                data-section-subject="' . $subject['section_subject_id'] . '">
                                                <i class="fas fa-check-circle mr-2"></i>Mark Attendance
                                              </button>';
                                        echo '</div>';
                                    } else {
                                        echo '<div class="attendance-card mb-3">';
                                        echo '<h6>' . htmlspecialchars($subject['subject_name']) . '</h6>';
                                        echo '<p class="text-success"><i class="fas fa-check-circle mr-2"></i>Attendance already marked for today</p>';
                                        echo '</div>';
                                    }
                                }
                            } else {
                                echo '<p class="text-muted text-center"><i class="fas fa-info-circle mr-2"></i>No subjects available.</p>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row stats-cards">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <i class="fas fa-book"></i>
                            <h5>Current Subjects</h5>
                            <h3><?php echo $student_info['course_count']; ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <i class="fas fa-tasks"></i>
                            <h5>Pending Tasks</h5>
                            <h3><?php echo $student_info['pending_count']; ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <i class="fas fa-chart-line"></i>
                            <h5>Average Grade</h5>
                            <h3><?php echo $average_grade; ?>%</h3>
                            <small class="text-muted">
                                (<?php echo $grades_result['quiz_count'] ?? 0; ?> Quizzes, 
                                 <?php echo $grades_result['activity_count'] ?? 0; ?> Activities, 
                                 <?php echo $grades_result['assignment_count'] ?? 0; ?> Assignments)
                            </small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <i class="fas fa-calendar-check"></i>
                            <h5>Attendance</h5>
                            <h3><?php echo $attendance_rate; ?>%</h3>
                        </div>
                    </div>
                </div>
            </div>
         

            <!-- Recent Activities and Upcoming Tasks -->
            <div class="row mt-4">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5>Recent Activities</h5>
                        </div>
                        <div class="card-body">
                            <?php foreach ($recent_activities as $activity): ?>
                                <div class="activity-item">
                                    <div class="activity-icon <?php echo getActivityTypeClass($activity['type']); ?>">
                                        <i class="fas <?php echo getActivityTypeIcon($activity['type']); ?>"></i>
                                    </div>
                                    <div class="activity-content">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1"><?php echo htmlspecialchars($activity['title']); ?></h6>
                                                <p class="mb-1 text-muted">
                                                    <?php echo htmlspecialchars($activity['subject_code']); ?> - 
                                                    <?php echo htmlspecialchars($activity['subject_name']); ?>
                                                </p>
                                            </div>
                                            <span class="badge <?php echo getStatusBadgeClass($activity['status']); ?>">
                                                <?php echo $activity['status']; ?>
                                            </span>
                                        </div>
                                        <div class="activity-meta">
                                            <small class="text-muted">
                                                <i class="far fa-clock mr-1"></i>
                                                <?php echo timeAgo($activity['activity_date']); ?>
                                            </small>
                                            <?php if ($activity['score'] !== 'Not submitted'): ?>
                                                <small class="text-muted ml-3">
                                                    <i class="fas fa-star mr-1"></i>
                                                    Score: <?php echo $activity['score']; ?>
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <?php if (empty($recent_activities)): ?>
                                <p class="text-muted text-center">No recent activities</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5>Upcoming Tasks</h5>
                        </div>
                        <div class="card-body">
                            <ul class="task-list">
                                <?php foreach ($upcoming_tasks as $task): ?>
                                    <li>
                                        <span class="task-title">
                                            <?php echo htmlspecialchars($task['title']); ?>
                                            <small class="d-block text-muted">
                                                <?php echo htmlspecialchars($task['course_name'] ?? 'No subject name'); ?>
                                            </small>
                                        </span>
                                        <span class="task-date">
                                            Due: <?php echo formatDueDate($task['due_date']); ?>
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                                <?php if (empty($upcoming_tasks)): ?>
                                    <li class="text-muted text-center">No upcoming tasks</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
    function checkSession() {
        fetch('check_session.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                'student_id': <?php echo $student_id; ?>,
                'session_id': '<?php echo $_SESSION['session_id']; ?>'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                window.location.href = 'Student-Login.php?error=session_expired';
            }
        })
        .catch(error => {
            console.error('Error checking session:', error);
            alert('An error occurred while checking session.');
            window.location.href = 'Student-Login.php?error=session_expired';
        });
    }
    checkSession();
    </script>

    <!-- Add this before the closing </body> tag -->
    <script>
    $(document).ready(function() {
        $('.mark-attendance').click(function() {
            const sectionSubjectId = $(this).data('section-subject');
            const button = $(this);
            const card = button.closest('.attendance-card');
            
            $.ajax({
                url: 'handlers/mark_attendance.php',
                method: 'POST',
                data: {
                    section_subject_id: sectionSubjectId
                },
                success: function(response) {
                    try {
                        const data = JSON.parse(response);
                        if (data.success) {
                            card.html(`
                                <h6>${card.find('h6').text()}</h6>
                                <p class="text-success">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    Attendance marked successfully at ${data.time}
                                </p>
                            `);
                        } else {
                            alert(data.message || 'Error marking attendance');
                        }
                    } catch (e) {
                        alert('Error processing response');
                    }
                },
                error: function() {
                    alert('Error connecting to server');
                }
            });
        });
    });
    </script>
</body>
</html> 