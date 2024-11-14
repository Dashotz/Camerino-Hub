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
                        <h2 class="page-title">Attendance Management</h2>
                        
                        <div class="card">
                            <div class="card-body">
                                <form id="attendanceForm" method="post">
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <select class="form-control" id="sectionSubject" name="section_subject_id" required>
                                                <option value="">Select Class</option>
                                                <?php foreach ($classes as $class): ?>
                                                    <option value="<?php echo $class['section_subject_id']; ?>">
                                                        <?php echo $class['section_name'] . ' - ' . $class['subject_name'] . 
                                                              ' (' . $class['schedule_day'] . ' ' . 
                                                              date('h:i A', strtotime($class['schedule_time'])) . ')'; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="date" class="form-control" id="attendanceDate" 
                                                   name="date" value="<?php echo date('Y-m-d'); ?>" required>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-primary" id="loadStudents">
                                                Load Students
                                            </button>
                                        </div>
                                    </div>

                                    <div id="studentList" class="student-list">
                                        <!-- Students will be loaded here -->
                                    </div>

                                    <div class="mt-3" id="submitButtons" style="display: none;">
                                        <button type="submit" class="btn btn-success">Save Attendance</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="card mt-4">
                            <div class="card-header">
                                <h5>Export Attendance Records</h5>
                            </div>
                            <div class="card-body">
                                <form action="export_attendance.php" method="post" class="row align-items-end">
                                    <div class="col-md-4">
                                        <label>Export Today's Attendance</label>
                                        <button type="submit" name="export_today" class="btn btn-primary btn-block">
                                            <i class="fas fa-download"></i> Export Today's Report
                                        </button>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <label>Export Monthly Attendance</label>
                                        <div class="input-group">
                                            <select name="month" class="form-control" required>
                                                <?php
                                                $months = [
                                                    1 => 'January', 2 => 'February', 3 => 'March',
                                                    4 => 'April', 5 => 'May', 6 => 'June',
                                                    7 => 'July', 8 => 'August', 9 => 'September',
                                                    10 => 'October', 11 => 'November', 12 => 'December'
                                                ];
                                                $currentMonth = date('n');
                                                foreach ($months as $num => $name) {
                                                    $selected = ($num == $currentMonth) ? 'selected' : '';
                                                    echo "<option value='$num' $selected>$name</option>";
                                                }
                                                ?>
                                            </select>
                                            <select name="year" class="form-control" required>
                                                <?php
                                                $currentYear = date('Y');
                                                for ($year = $currentYear; $year >= $currentYear - 2; $year--) {
                                                    echo "<option value='$year'>$year</option>";
                                                }
                                                ?>
                                            </select>
                                            <div class="input-group-append">
                                                <button type="submit" name="export_month" class="btn btn-success">
                                                    <i class="fas fa-file-export"></i> Export
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label>Export Date Range</label>
                                        <div class="input-group">
                                            <input type="date" name="start_date" class="form-control" required>
                                            <input type="date" name="end_date" class="form-control" required>
                                            <div class="input-group-append">
                                                <button type="submit" name="export_range" class="btn btn-info">
                                                    <i class="fas fa-calendar"></i> Export Range
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
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
        $('#loadStudents').click(function() {
            const sectionSubjectId = $('#sectionSubject').val();
            const date = $('#attendanceDate').val();
            
            if (!sectionSubjectId || !date) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Please select both class and date',
                    icon: 'error'
                });
                return;
            }

            $.ajax({
                url: 'handlers/get_students_attendance.php',
                method: 'POST',
                data: {
                    section_subject_id: sectionSubjectId,
                    date: date
                },
                success: function(response) {
                    $('#studentList').html(response);
                    $('#submitButtons').show();
                },
                error: function() {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to load students',
                        icon: 'error'
                    });
                }
            });
        });

        $('#attendanceForm').submit(function(e) {
            e.preventDefault();
            
            $.ajax({
                url: 'handlers/save_attendance.php',
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    const result = JSON.parse(response);
                    if (result.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Attendance has been saved',
                            icon: 'success'
                        }).then(() => {
                            $('#loadStudents').click();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: result.message,
                            icon: 'error'
                        });
                    }
                }
            });
        });
    });
    </script>
</body>
</html>
