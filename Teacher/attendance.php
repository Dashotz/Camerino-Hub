<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['teacher_id'])) {
    header("Location: Teacher-Login.php");
    exit();
}

$db = new DbConnector();
$teacher_id = $_SESSION['teacher_id'];

// Get all classes taught by this teacher
$classes_query = "SELECT 
    c.class_id,
    c.section_name,
    s.subject_code,
    s.subject_name,
    c.schedule_day,
    c.schedule_time
FROM classes c
JOIN subjects s ON c.subject_id = s.id
WHERE c.teacher_id = ?
ORDER BY c.section_name";

$stmt = $db->prepare($classes_query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$classes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get today's date
$today = date('Y-m-d');

// If a specific date is selected
$selected_date = isset($_GET['date']) ? $_GET['date'] : $today;
$selected_class = isset($_GET['class_id']) ? $_GET['class_id'] : ($classes[0]['class_id'] ?? null);

// Get students and their attendance for the selected class and date
if ($selected_class) {
    $students_query = "SELECT 
        s.student_id,
        s.firstname,
        s.lastname,
        s.middle_name,
        a.status,
        a.id as attendance_id
    FROM student s
    JOIN student_courses sc ON s.student_id = sc.student_id
    LEFT JOIN attendance a ON s.student_id = a.student_id 
        AND a.date = ? 
        AND a.course_id = ?
    WHERE sc.class_id = ?
    ORDER BY s.lastname, s.firstname";

    $stmt = $db->prepare($students_query);
    $stmt->bind_param("sii", $selected_date, $selected_class, $selected_class);
    $stmt->execute();
    $students = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance - CamerinoHub</title>
    
    <!-- External CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/dashboard-shared.css">
    <style>
        .attendance-card {
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            margin-bottom: 1rem;
        }
        .attendance-header {
            background-color: #f8f9fa;
            padding: 1rem;
            border-bottom: 1px solid #dee2e6;
        }
        .attendance-controls {
            display: flex;
            gap: 1rem;
            align-items: center;
            margin-bottom: 1rem;
        }
        .status-badge {
            width: 100px;
            text-align: center;
        }
        .student-name {
            min-width: 200px;
        }
    </style>
</head>
<body>
    <?php include 'includes/navigation.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="content-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1>Attendance</h1>
                        <p>Manage student attendance records</p>
                    </div>
                </div>
            </div>

            <div class="attendance-controls">
                <select class="form-control" id="classSelect" style="width: 200px;">
                    <?php foreach ($classes as $class): ?>
                        <option value="<?php echo $class['class_id']; ?>" 
                            <?php echo ($selected_class == $class['class_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($class['section_name'] . ' - ' . $class['subject_code']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <input type="text" id="datePicker" class="form-control" style="width: 150px;"
                       value="<?php echo $selected_date; ?>" placeholder="Select date">

                <button class="btn btn-primary" onclick="saveAllAttendance()">
                    <i class="fas fa-save"></i> Save All
                </button>
            </div>

            <?php if (isset($students) && !empty($students)): ?>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td class="student-name">
                                        <?php echo htmlspecialchars($student['lastname'] . ', ' . 
                                                                   $student['firstname'] . ' ' . 
                                                                   $student['middle_name']); ?>
                                    </td>
                                    <td>
                                        <select class="form-control status-select" 
                                                data-student-id="<?php echo $student['student_id']; ?>"
                                                data-attendance-id="<?php echo $student['attendance_id']; ?>">
                                            <option value="present" <?php echo ($student['status'] == 'present') ? 'selected' : ''; ?>>Present</option>
                                            <option value="absent" <?php echo ($student['status'] == 'absent') ? 'selected' : ''; ?>>Absent</option>
                                            <option value="late" <?php echo ($student['status'] == 'late') ? 'selected' : ''; ?>>Late</option>
                                        </select>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-success save-btn" 
                                                onclick="saveAttendance(<?php echo $student['student_id']; ?>)">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    No students found for the selected class.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    
    <script>
    $(document).ready(function() {
        // Initialize date picker
        flatpickr("#datePicker", {
            dateFormat: "Y-m-d",
            maxDate: "today",
            defaultDate: "<?php echo $selected_date; ?>"
        });

        // Handle class selection change
        $('#classSelect').change(function() {
            updatePage();
        });

        // Handle date selection change
        $('#datePicker').change(function() {
            updatePage();
        });
    });

    function updatePage() {
        const classId = $('#classSelect').val();
        const date = $('#datePicker').val();
        window.location.href = `attendance.php?class_id=${classId}&date=${date}`;
    }

    function saveAttendance(studentId) {
        const status = $(`.status-select[data-student-id="${studentId}"]`).val();
        const attendanceId = $(`.status-select[data-student-id="${studentId}"]`).data('attendance-id');
        const classId = $('#classSelect').val();
        const date = $('#datePicker').val();

        $.post('save_attendance.php', {
            student_id: studentId,
            attendance_id: attendanceId,
            class_id: classId,
            date: date,
            status: status
        }, function(response) {
            const result = JSON.parse(response);
            if (result.success) {
                showAlert('success', 'Attendance saved successfully!');
            } else {
                showAlert('danger', 'Error saving attendance: ' + result.message);
            }
        });
    }

    function saveAllAttendance() {
        $('.save-btn').each(function() {
            $(this).click();
        });
    }

    function showAlert(type, message) {
        const alert = $(`<div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>`);
        
        $('.content-header').after(alert);
        setTimeout(() => alert.alert('close'), 3000);
    }
    </script>
</body>
</html>
