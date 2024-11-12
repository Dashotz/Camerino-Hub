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
$class_id = $_GET['id'] ?? 0;

// Fetch class details with related information
$query = "
    SELECT 
        ss.id as class_id,
        sec.section_name,
        sec.grade_level,
        s.subject_code,
        s.subject_name,
        ss.schedule_day,
        ss.schedule_time,
        ss.enrollment_code,
        COUNT(DISTINCT st_sec.student_id) as student_count,
        COALESCE(AVG(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) * 100, 0) as attendance_rate,
        COALESCE(AVG(sas.points), 0) as average_performance
    FROM section_subjects ss
    JOIN sections sec ON ss.section_id = sec.section_id
    JOIN subjects s ON ss.subject_id = s.id
    LEFT JOIN student_sections st_sec ON sec.section_id = st_sec.section_id 
        AND st_sec.academic_year_id = ss.academic_year_id
    LEFT JOIN attendance a ON ss.id = a.section_subject_id
    LEFT JOIN student_activity_submissions sas ON st_sec.student_id = sas.student_id
    WHERE ss.id = ? AND ss.teacher_id = ? AND ss.status = 'active'
    GROUP BY ss.id";

$stmt = $db->prepare($query);
$stmt->bind_param("ii", $class_id, $teacher_id);
$stmt->execute();
$class = $stmt->get_result()->fetch_assoc();

if (!$class) {
    header("Location: manage_classes.php");
    exit();
}

// Add this after line 13 (after getting $class_id)
function generateEnrollmentCode($db, $class_id) {
    // Generate a unique code
    do {
        $code = 'CMRH' . str_pad(mt_rand(1000, 9999), 4, '0', STR_PAD_LEFT);
        $check = $db->prepare("SELECT id FROM section_subjects WHERE enrollment_code = ?");
        $check->bind_param("s", $code);
        $check->execute();
    } while ($check->get_result()->num_rows > 0);
    
    // Update the section_subject with the new code
    $update = $db->prepare("UPDATE section_subjects SET enrollment_code = ? WHERE id = ?");
    $update->bind_param("si", $code, $class_id);
    $update->execute();
    
    return $code;
}

// Check if we need to generate a new enrollment code
if (!isset($class['enrollment_code'])) {
    $class['enrollment_code'] = generateEnrollmentCode($db, $class_id);
}

// Fetch students in this class
$students_query = "
    SELECT 
        s.student_id,
        s.firstname,
        s.lastname,
        COALESCE(AVG(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) * 100, 0) as attendance_rate,
        COALESCE(AVG(sas.points), 0) as performance
    FROM student s
    JOIN student_sections ss ON s.student_id = ss.student_id
    JOIN section_subjects secsubj ON ss.section_id = secsubj.section_id 
        AND ss.academic_year_id = secsubj.academic_year_id
    LEFT JOIN attendance a ON s.student_id = a.student_id 
        AND a.section_subject_id = ?
    LEFT JOIN activities act ON act.section_subject_id = secsubj.id
    LEFT JOIN student_activity_submissions sas ON s.student_id = sas.student_id 
        AND sas.activity_id = act.activity_id
    WHERE secsubj.id = ?
    GROUP BY s.student_id
    ORDER BY s.lastname, s.firstname";

$stmt = $db->prepare($students_query);
$stmt->bind_param("ii", $class_id, $class_id);
$stmt->execute();
$students = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Details - <?php echo htmlspecialchars($class['section_name']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .class-header {
            background: #fff;
            border-radius: 10px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .schedule-badge {
            background: #f8f9fa;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            margin-right: 1rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .stat-card {
            background: #fff;
            padding: 1.5rem;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 600;
            color: #2c3e50;
        }

        .student-card {
            background: #fff;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .student-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }

        .student-info {
            flex: 1;
        }

        .student-stats {
            display: flex;
            gap: 2rem;
        }

        .performance-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.875rem;
        }

        .performance-good { background: #e8f8f3; color: #2ecc71; }
        .performance-average { background: #fdf3e8; color: #f39c12; }
        .performance-poor { background: #fde8e8; color: #e74c3c; }

        .enrollment-code {
            text-align: left;
            padding: 1rem;
        }

        .code-display {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0.5rem 0;
        }

        .code-display span {
            font-size: 1.2rem;
            font-weight: 600;
            font-family: monospace;
            background: #f8f9fa;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            flex: 1;
        }

        .code-display button {
            padding: 0.25rem 0.5rem;
        }

        .code-display button:hover {
            background: #e9ecef;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="class-header">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h1><?php echo htmlspecialchars($class['section_name']); ?></h1>
                    <h4 class="text-muted">
                        <?php echo htmlspecialchars($class['subject_code'] . ' - ' . $class['subject_name']); ?>
                    </h4>
                    <div class="mt-3">
                        <span class="schedule-badge">
                            <i class="fas fa-calendar-alt"></i>
                            <?php echo htmlspecialchars($class['schedule_day']); ?>
                        </span>
                        <span class="schedule-badge">
                            <i class="fas fa-clock"></i>
                            <?php 
                                $time = DateTime::createFromFormat('H:i:s', $class['schedule_time']);
                                echo $time ? $time->format('h:i A') : $class['schedule_time']; 
                            ?>
                        </span>
                    </div>
                </div>
                <a href="manage_classes.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Classes
                </a>
            </div>

            <div class="stats-container">
                <div class="stat-card">
                    <div class="enrollment-code">
                        <h6>Class Enrollment Code</h6>
                        <div class="code-display">
                            <span id="enrollmentCode"><?php echo htmlspecialchars($class['enrollment_code']); ?></span>
                            <button class="btn btn-sm btn-outline-primary" onclick="copyEnrollmentCode()" data-bs-toggle="tooltip" title="Copy to clipboard">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                        <small class="text-muted">Share this code with students to join the class</small>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo $class['student_count']; ?></div>
                    <div class="stat-label">Students</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo number_format($class['attendance_rate'], 1); ?>%</div>
                    <div class="stat-label">Average Attendance</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo number_format($class['average_performance'], 1); ?>%</div>
                    <div class="stat-label">Class Performance</div>
                </div>
            </div>
        </div>

        <div class="students-container">
            <h2 class="mb-4">Students</h2>
            <?php foreach ($students as $student): ?>
                <div class="student-card">
                    <img src="<?php echo $student['profile_image'] ?? '../images/default-avatar.png'; ?>" 
                         alt="Student Avatar" 
                         class="student-avatar">
                    <div class="student-info">
                        <h5 class="mb-1">
                            <?php echo htmlspecialchars($student['firstname'] . ' ' . $student['lastname']); ?>
                        </h5>
                        <div class="student-stats">
                            <span>Attendance: <?php echo number_format($student['attendance_rate'], 1); ?>%</span>
                            <span>Performance: 
                                <?php 
                                $performance = $student['performance'];
                                $performanceClass = $performance >= 85 ? 'good' : ($performance >= 75 ? 'average' : 'poor');
                                ?>
                                <span class="performance-badge performance-<?php echo $performanceClass; ?>">
                                    <?php echo number_format($performance, 1); ?>%
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function copyEnrollmentCode() {
        const codeElement = document.getElementById('enrollmentCode');
        const code = codeElement.textContent;
        
        navigator.clipboard.writeText(code).then(() => {
            // Show success feedback
            const btn = codeElement.nextElementSibling;
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check"></i>';
            btn.classList.add('btn-success');
            btn.classList.remove('btn-outline-primary');
            
            setTimeout(() => {
                btn.innerHTML = originalHtml;
                btn.classList.remove('btn-success');
                btn.classList.add('btn-outline-primary');
            }, 2000);
            
            // Optional: Show tooltip or toast
            if (typeof bootstrap !== 'undefined') {
                const toast = new bootstrap.Toast(Object.assign(document.createElement('div'), {
                    className: 'toast position-fixed bottom-0 end-0 m-3',
                    innerHTML: `
                        <div class="toast-body bg-success text-white">
                            Enrollment code copied to clipboard!
                        </div>
                    `
                }));
                document.body.appendChild(toast._element);
                toast.show();
                setTimeout(() => toast._element.remove(), 3000);
            }
        }).catch(err => {
            console.error('Failed to copy code:', err);
            alert('Failed to copy code. Please try again.');
        });
    }

    // Initialize tooltips if using Bootstrap 5
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof bootstrap !== 'undefined') {
            const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            tooltips.forEach(tooltip => new bootstrap.Tooltip(tooltip));
        }
    });

    function regenerateCode() {
        if (!confirm('Are you sure you want to generate a new enrollment code? The old code will no longer work.')) {
            return;
        }
        
        $.ajax({
            url: 'handlers/class_handler.php',
            method: 'POST',
            data: {
                action: 'regenerate_code',
                class_id: <?php echo $class_id; ?>
            },
            success: function(response) {
                try {
                    const result = JSON.parse(response);
                    if (result.status === 'success') {
                        document.getElementById('enrollmentCode').textContent = result.code;
                        
                        const toast = new bootstrap.Toast(Object.assign(document.createElement('div'), {
                            className: 'toast position-fixed bottom-0 end-0 m-3',
                            innerHTML: `
                                <div class="toast-body bg-success text-white">
                                    New enrollment code generated successfully!
                                </div>
                            `
                        }));
                        document.body.appendChild(toast._element);
                        toast.show();
                        setTimeout(() => toast._element.remove(), 3000);
                    } else {
                        alert('Failed to generate new code: ' + result.message);
                    }
                } catch (e) {
                    console.error('JSON parse error:', e);
                    alert('Failed to generate new code. Please try again.');
                }
            },
            error: function() {
                alert('Failed to connect to server. Please try again.');
            }
        });
    }
    </script>
</body>
</html>
