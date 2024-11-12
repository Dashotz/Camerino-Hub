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

// Fetch grades for all subjects
$grades_query = "
    SELECT 
        s.subject_name,
        s.id as subject_id,
        s.subject_code,
        COUNT(DISTINCT a.activity_id) as total_activities,
        COUNT(DISTINCT sas.submission_id) as submitted_activities,
        ROUND(AVG(sas.points), 1) as average_grade,
        MAX(sas.submitted_at) as last_submission
    FROM student_sections ss
    JOIN sections sec ON ss.section_id = sec.section_id
    JOIN section_subjects ssub ON sec.section_id = ssub.section_id
    JOIN subjects s ON ssub.subject_id = s.id
    LEFT JOIN activities a ON ssub.teacher_id = a.teacher_id 
        AND a.type = 'assignment'
    LEFT JOIN student_activity_submissions sas ON a.activity_id = sas.activity_id 
        AND sas.student_id = ?
    WHERE ss.student_id = ? 
        AND ss.status = 'active'
        AND ssub.status = 'active'
    GROUP BY s.id, s.subject_name, s.subject_code
    ORDER BY s.subject_name";

$stmt = $db->prepare($grades_query);
$stmt->bind_param("ii", $student_id, $student_id);
$stmt->execute();
$grades_result = $stmt->get_result();
$subjects_grades = $grades_result->fetch_all(MYSQLI_ASSOC);

// Calculate overall statistics
$overall_stats = [
    'total_subjects' => count($subjects_grades),
    'average_grade' => 0,
    'total_activities' => 0,
    'completed_activities' => 0
];

foreach ($subjects_grades as $subject) {
    $overall_stats['average_grade'] += $subject['average_grade'] ?? 0;
    $overall_stats['total_activities'] += $subject['total_activities'];
    $overall_stats['completed_activities'] += $subject['submitted_activities'];
}

if ($overall_stats['total_subjects'] > 0) {
    $overall_stats['average_grade'] = round($overall_stats['average_grade'] / $overall_stats['total_subjects'], 1);
}
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
                            <div class="stat-icon">
                                <i class="fas fa-book"></i>
                            </div>
                            <div class="stat-info">
                                <h3><?php echo $overall_stats['total_subjects']; ?></h3>
                                <p>Total Subjects</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="stat-info">
                                <h3><?php echo $overall_stats['average_grade']; ?>%</h3>
                                <p>Overall Average</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-tasks"></i>
                            </div>
                            <div class="stat-info">
                                <h3><?php echo $overall_stats['total_activities']; ?></h3>
                                <p>Total Activities</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="stat-info">
                                <h3><?php echo $overall_stats['completed_activities']; ?></h3>
                                <p>Completed</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Subject Grades -->
            <div class="subjects-grades">
                <div class="card">
                    <div class="card-header">
                        <h5>Subject Performance</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Subject Code</th>
                                        <th>Subject Name</th>
                                        <th>Activities</th>
                                        <th>Completed</th>
                                        <th>Average Grade</th>
                                        <th>Last Submission</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($subjects_grades as $subject): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($subject['subject_code']); ?></td>
                                            <td><?php echo htmlspecialchars($subject['subject_name']); ?></td>
                                            <td><?php echo $subject['total_activities']; ?></td>
                                            <td>
                                                <div class="progress">
                                                    <?php 
                                                    $completion_rate = $subject['total_activities'] > 0 
                                                        ? ($subject['submitted_activities'] / $subject['total_activities']) * 100 
                                                        : 0;
                                                    ?>
                                                    <div class="progress-bar" style="width: <?php echo $completion_rate; ?>%">
                                                        <?php echo $subject['submitted_activities']; ?>/<?php echo $subject['total_activities']; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="grade-badge">
                                                    <?php echo $subject['average_grade'] ?? 'N/A'; ?>%
                                                </span>
                                            </td>
                                            <td>
                                                <?php echo $subject['last_submission'] 
                                                    ? date('M j, Y', strtotime($subject['last_submission'])) 
                                                    : 'No submissions'; ?>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary" 
                                                        onclick="viewDetails(<?php echo $subject['subject_id']; ?>)">
                                                    View Details
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($subjects_grades)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No subjects found</td>
                                        </tr>
                                    <?php endif; ?>
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
