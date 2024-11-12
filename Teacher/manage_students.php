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

// Fetch students with their class and performance data
$students_query = "
    SELECT DISTINCT
        s.student_id,
        s.firstname,
        s.lastname,
        s.email,
        s.profile_image,
        sec.section_name,
        subj.subject_name,
        subj.subject_code,
        ss.created_at as enrollment_date,
        COALESCE(AVG(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) * 100, 0) as attendance_rate,
        COALESCE(AVG(sas.points), 0) as performance
    FROM student s
    JOIN student_sections st_sec ON s.student_id = st_sec.student_id
    JOIN sections sec ON st_sec.section_id = sec.section_id
    JOIN section_subjects ss ON sec.section_id = ss.section_id
    JOIN subjects subj ON ss.subject_id = subj.id
    LEFT JOIN attendance a ON s.student_id = a.student_id AND a.section_subject_id = ss.id
    LEFT JOIN student_activity_submissions sas ON s.student_id = sas.student_id
    WHERE ss.teacher_id = ?
    AND ss.status = 'active'
    AND st_sec.status = 'active'
    GROUP BY 
        s.student_id, 
        s.firstname, 
        s.lastname, 
        s.email,
        sec.section_name,
        subj.subject_name,
        subj.subject_code,
        ss.created_at
    ORDER BY subj.subject_name, sec.section_name, s.lastname, s.firstname";

$stmt = $db->prepare($students_query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$students = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Debug output
echo "<!-- Number of students found: " . count($students) . " -->";

// Get classes for filter dropdown
$classes_query = "SELECT DISTINCT 
    ss.id as class_id,
    CONCAT(sec.section_name, ' - ', sub.subject_code) as section_name
FROM section_subjects ss
JOIN sections sec ON ss.section_id = sec.section_id
JOIN subjects sub ON ss.subject_id = sub.id
WHERE ss.teacher_id = ? 
AND ss.status = 'active'
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
    <title>Manage Students - Gov D.M. Camerino</title>
    
    <!-- External CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/dashboard-shared.css">
    <link rel="stylesheet" href="css/manage-students.css">
</head>
<body>
    <?php include 'includes/navigation.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="content-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1>Manage Students</h1>
                        <p>View and manage your students' information</p>
                    </div>
                    <div class="header-actions">
                        <select class="form-control mr-2" id="classFilter">
                            <option value="">All Classes</option>
                            <?php foreach ($classes as $class): ?>
                                <option value="<?php echo htmlspecialchars($class['section_name']); ?>">
                                    <?php echo htmlspecialchars($class['section_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button class="btn btn-primary" onclick="exportStudentData()">
                            <i class="fas fa-download"></i> Export Data
                        </button>
                    </div>
                </div>
            </div>

            <!-- Students Table -->
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Enrolled Students</h4>
                    <div class="header-actions">
                        <button class="btn btn-primary" onclick="exportStudentData()">
                            <i class="fas fa-download"></i> Export Data
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table id="studentsTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Subject</th>
                                <th>Section</th>
                                <th>Enrollment Date</th>
                                <th>Attendance</th>
                                <th>Performance</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo $student['profile_image'] ?? '../images/default-avatar.png'; ?>" 
                                                 class="student-avatar mr-2" 
                                                 alt="Profile Image">
                                            <div>
                                                <div class="font-weight-bold">
                                                    <?php echo htmlspecialchars($student['firstname'] . ' ' . $student['lastname']); ?>
                                                </div>
                                                <small class="text-muted"><?php echo htmlspecialchars($student['email']); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">
                                            <?php echo htmlspecialchars($student['subject_code']); ?>
                                        </span>
                                        <div class="small">
                                            <?php echo htmlspecialchars($student['subject_name']); ?>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($student['section_name']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($student['enrollment_date'])); ?></td>
                                    <td>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar bg-success" 
                                                 style="width: <?php echo $student['attendance_rate']; ?>%"
                                                 title="<?php echo number_format($student['attendance_rate'], 1); ?>%">
                                            </div>
                                        </div>
                                        <small class="text-muted">
                                            <?php echo number_format($student['attendance_rate'], 1); ?>%
                                        </small>
                                    </td>
                                    <td>
                                        <?php 
                                            $performanceClass = $student['performance'] >= 85 ? 'success' : 
                                                ($student['performance'] >= 75 ? 'warning' : 'danger');
                                        ?>
                                        <span class="badge badge-<?php echo $performanceClass; ?>">
                                            <?php echo number_format($student['performance'], 1); ?>%
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-info" 
                                                    onclick="viewStudentDetails(<?php echo $student['student_id']; ?>)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-primary" 
                                                    onclick="viewStudentProgress(<?php echo $student['student_id']; ?>)">
                                                <i class="fas fa-chart-line"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Details Modal -->
    <div class="modal fade" id="studentDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Student Details</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Content will be loaded dynamically -->
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>
    
    <script>
    $(document).ready(function() {
        // Initialize DataTable
        const table = $('#studentsTable').DataTable({
            order: [[1, 'asc'], [0, 'asc']], // Sort by class then name
            pageLength: 10,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search students..."
            }
        });

        // Class filter
        $('#classFilter').on('change', function() {
            table.column(1).search(this.value).draw();
        });
    });

    function viewStudentDetails(studentId) {
        $.get('get_student_details.php', { student_id: studentId }, function(data) {
            $('#studentDetailsModal .modal-body').html(data);
            $('#studentDetailsModal').modal('show');
        });
    }

    function viewStudentProgress(studentId) {
        window.location.href = `student_progress.php?student_id=${studentId}`;
    }

    function exportStudentData() {
        window.location.href = 'export_students.php';
    }
    </script>
</body>
</html>
