<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: Student-Login.php");
    exit();
}

// Get user data
require_once('../db/dbConnector.php');
$db = new DbConnector();

$student_id = $_SESSION['id'];

// Fetch student data
$query = "SELECT * FROM student WHERE student_id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();

// Get student's subjects and grades
$grades_query = "
    SELECT 
        s.subject_name,
        s.subject_code,
        ss.id as section_subject_id,
        COUNT(DISTINCT a.activity_id) as total_activities,
        COUNT(DISTINCT sas.submission_id) as submitted_activities,
        COALESCE(AVG(sas.points), 0) as average_grade,
        t.firstname as teacher_fname,
        t.lastname as teacher_lname
    FROM student_sections sts
    JOIN section_subjects ss ON sts.section_id = ss.section_id
    JOIN subjects s ON ss.subject_id = s.id
    JOIN teacher t ON ss.teacher_id = t.teacher_id
    LEFT JOIN activities a ON ss.id = a.section_subject_id AND a.status = 'active'
    LEFT JOIN student_activity_submissions sas ON a.activity_id = sas.activity_id 
        AND sas.student_id = ?
    WHERE sts.student_id = ? 
        AND sts.status = 'active'
        AND ss.status = 'active'
        AND sts.academic_year_id = (SELECT id FROM academic_years WHERE status = 'active' LIMIT 1)
    GROUP BY s.id, ss.id";

$stmt = $db->prepare($grades_query);
$stmt->bind_param("ii", $student_id, $student_id);
$stmt->execute();
$subjects_grades = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Calculate overall statistics
$total_subjects = count($subjects_grades);
$overall_grade = 0;
$total_activities = 0;
$completed_activities = 0;

foreach ($subjects_grades as $subject) {
    $overall_grade += $subject['average_grade'];
    $total_activities += $subject['total_activities'];
    $completed_activities += $subject['submitted_activities'];
}

$average_grade = $total_subjects ? round($overall_grade / $total_subjects, 2) : 0;
$completion_rate = $total_activities ? round(($completed_activities / $total_activities) * 100, 2) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Grades - Gov D.M. Camerino</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/dashboard-shared.css">
    <link rel="stylesheet" href="css/grades.css">
</head>
<body>
    <?php include 'includes/navigation.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="content-header">
                <h1>My Grades</h1>
                <p>View your academic performance across all subjects</p>
            </div>

            <!-- Overall Statistics -->
            <div class="stats-overview">
                <div class="row">
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-value"><?php echo $total_subjects; ?></div>
                            <div class="stat-label">Total Subjects</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-value"><?php echo $average_grade; ?>%</div>
                            <div class="stat-label">Overall Average</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-value"><?php echo $completion_rate; ?>%</div>
                            <div class="stat-label">Completion Rate</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Subject Grades -->
            <div class="subjects-grades">
                <div class="card">
                    <div class="card-header">
                        <h5>Subject Grades</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Subject</th>
                                        <th>Teacher</th>
                                        <th>Progress</th>
                                        <th>Grade</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($subjects_grades as $subject): 
                                        $completion_rate = $subject['total_activities'] ? 
                                            ($subject['submitted_activities'] / $subject['total_activities']) * 100 : 0;
                                    ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($subject['subject_code']); ?></strong><br>
                                                <small><?php echo htmlspecialchars($subject['subject_name']); ?></small>
                                            </td>
                                            <td><?php echo htmlspecialchars($subject['teacher_fname'] . ' ' . $subject['teacher_lname']); ?></td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar" style="width: <?php echo $completion_rate; ?>%">
                                                        <?php echo $subject['submitted_activities']; ?>/<?php echo $subject['total_activities']; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?php echo number_format($subject['average_grade'], 2); ?>%</td>
                                            <td>
                                                <button class="btn btn-sm btn-primary" 
                                                        onclick="viewDetails(<?php echo $subject['section_subject_id']; ?>)">
                                                    View Details
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grade Details Modal -->
    <div class="modal fade" id="gradeDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Grade Details</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="gradeDetailsContent">
                    Loading...
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <script>
        function viewDetails(subjectId) {
            $('#gradeDetailsModal').modal('show');
            
            // Fetch grade details using AJAX
            $.ajax({
                url: 'get_grade_details.php',
                type: 'GET',
                data: { subject_id: subjectId },
                success: function(response) {
                    $('#gradeDetailsContent').html(response);
                },
                error: function() {
                    $('#gradeDetailsContent').html(
                        '<div class="alert alert-danger">Error loading grade details.</div>'
                    );
                }
            });
        }
    </script>
</body>
</html>
