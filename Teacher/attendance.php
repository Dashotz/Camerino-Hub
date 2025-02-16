<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['teacher_id'])) {
    header("Location: Teacher-Login.php");
    exit();
}

$db = new DbConnector();
$teacher_id = $_SESSION['teacher_id'];

// Get teacher's sections and subjects
$query = "
    SELECT DISTINCT 
        ss.id as section_subject_id,
        s.section_name,
        sub.subject_name,
        sub.subject_code,
        ss.schedule_day,
        ss.schedule_time
    FROM section_subjects ss
    JOIN sections s ON ss.section_id = s.section_id
    JOIN subjects sub ON ss.subject_id = sub.id
    WHERE ss.teacher_id = ? 
    AND ss.status = 'active'
    ORDER BY s.section_name, sub.subject_name";

$stmt = $db->prepare($query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$classes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get the first class's section_subject_id if available
$first_class_id = !empty($classes) ? $classes[0]['section_subject_id'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance - Teacher Dashboard</title>
    
    <!-- External CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="icon" href="../images/light-logo.png">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/dashboard-shared.css">
    <link rel="stylesheet" href="css/attendance.css">
</head>
<body>
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <?php include 'includes/navigation.php'; ?>
            
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <h2 class="page-title">Attendance Records</h2>
                        
                        <!-- Attendance Records Card -->
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="row w-100">
                                        <div class="col-md-4">
                                            <select class="form-control" id="filterSection">
                                                <option value="">Select Class</option>
                                                <?php foreach ($classes as $class): ?>
                                                    <option value="<?php echo $class['section_subject_id']; ?>"
                                                            <?php echo ($class['section_subject_id'] == $first_class_id) ? 'selected' : ''; ?>>
                                                        <?php echo $class['section_name'] . ' - ' . $class['subject_name'] . 
                                                              ' (' . $class['schedule_day'] . ' ' . 
                                                              date('h:i A', strtotime($class['schedule_time'])) . ')'; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="date" class="form-control" id="filterDate" value="<?php echo date('Y-m-d'); ?>">
                                        </div>
                                        <div class="col-md-5 text-right">
                                            <button class="btn btn-primary" id="downloadDaily">
                                                <i class="fas fa-download"></i> Daily Report
                                            </button>
                                            <button class="btn btn-success" id="downloadMonthly">
                                                <i class="fas fa-download"></i> Monthly Report
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive" id="attendanceTable">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Student Name</th>
                                                <th>Status</th>
                                                <th>Time In</th>
                                            </tr>
                                        </thead>
                                        <tbody id="attendanceRecords">
                                            <!-- Records will be loaded here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/scripts.php'; ?>
    
    <script>
    $(document).ready(function() {
        function loadAttendanceRecords() {
            const sectionSubjectId = $('#filterSection').val();
            const date = $('#filterDate').val();
            
            if (!sectionSubjectId) {
                alert('Please select a class');
                return;
            }

            $.ajax({
                url: 'handlers/get_attendance_records.php',
                method: 'POST',
                data: {
                    section_subject_id: sectionSubjectId,
                    date: date
                },
                success: function(response) {
                    $('#attendanceRecords').html(response);
                },
                error: function(xhr, status, error) {
                    alert('Error loading attendance records: ' + error);
                }
            });
        }

        // Daily PDF download handler
        $('#downloadDaily').click(function() {
            const sectionSubjectId = $('#filterSection').val();
            const date = $('#filterDate').val();
            
            if (!sectionSubjectId) {
                alert('Please select a class');
                return;
            }

            window.location.href = `handlers/generate_attendance_pdf.php?section_subject_id=${sectionSubjectId}&date=${date}&type=daily`;
        });

        // Monthly PDF download handler
        $('#downloadMonthly').click(function() {
            const sectionSubjectId = $('#filterSection').val();
            const date = $('#filterDate').val();
            const month = new Date(date).getMonth() + 1;
            const year = new Date(date).getFullYear();
            
            if (!sectionSubjectId) {
                alert('Please select a class');
                return;
            }

            window.location.href = `handlers/generate_attendance_pdf.php?section_subject_id=${sectionSubjectId}&month=${month}&year=${year}&type=monthly`;
        });

        // Auto-load first class attendance when page loads
        if ($('#filterSection').val()) {
            loadAttendanceRecords();
        }

        // Handle class selection change
        $('#filterSection').change(function() {
            loadAttendanceRecords();
        });

        // Handle date change
        $('#filterDate').change(function() {
            loadAttendanceRecords();
        });
    });
    </script>
</body>
</html>
