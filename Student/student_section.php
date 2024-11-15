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

// Get current academic year and student's section info
$section_query = "
    SELECT 
        COALESCE(sec.section_name, 'Not Assigned') as section_name,
        COALESCE(sec.grade_level, 'Not Assigned') as grade_level,
        COALESCE(CONCAT(t.firstname, ' ', 
            CASE WHEN t.middlename IS NOT NULL THEN CONCAT(t.middlename, ' ') ELSE '' END,
            t.lastname), 'Not Assigned') as adviser_name,
        COALESCE(ay.year_start, YEAR(CURRENT_DATE)) as year_start,
        COALESCE(ay.year_end, YEAR(CURRENT_DATE) + 1) as year_end,
        COALESCE(ss.status, 'inactive') as enrollment_status
    FROM student_sections ss
    JOIN sections sec ON ss.section_id = sec.section_id
    LEFT JOIN academic_years ay ON ay.status = 'active'
    LEFT JOIN section_advisers sa ON sec.section_id = sa.section_id 
        AND sa.academic_year_id = ay.id 
        AND sa.status = 'active'
    LEFT JOIN teacher t ON sa.teacher_id = t.teacher_id
    WHERE ss.student_id = ? 
    AND ss.status = 'active'
    LIMIT 1";

$stmt = $db->prepare($section_query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$section_info = $stmt->get_result()->fetch_assoc();

// Set default values if no section info found
if (!$section_info) {
    $section_info = [
        'section_name' => 'Not Assigned',
        'grade_level' => 'Not Assigned',
        'adviser_name' => 'Not Assigned',
        'year_start' => date('Y'),
        'year_end' => date('Y') + 1,
        'enrollment_status' => 'inactive'
    ];
}

// Fetch student's subjects
$subjects_query = "
    SELECT 
        s.id as subject_id,
        s.subject_code,
        s.subject_name,
        t.firstname as teacher_fname,
        t.lastname as teacher_lname,
        COUNT(DISTINCT CASE WHEN a.type = 'activity' THEN a.activity_id END) as activity_count,
        COUNT(DISTINCT CASE WHEN a.type = 'quiz' THEN a.activity_id END) as quiz_count,
        COUNT(DISTINCT CASE WHEN a.type = 'assignment' THEN a.activity_id END) as assignment_count
    FROM section_subjects ss
    JOIN subjects s ON ss.subject_id = s.id
    JOIN teacher t ON ss.teacher_id = t.teacher_id
    LEFT JOIN activities a ON ss.id = a.section_subject_id AND a.status = 'active'
    WHERE ss.section_id = (
        SELECT section_id 
        FROM student_sections 
        WHERE student_id = ? 
        AND status = 'active' 
        LIMIT 1
    ) 
    AND ss.status = 'active'
    GROUP BY s.id, s.subject_code, s.subject_name, t.firstname, t.lastname";

$stmt = $db->prepare($subjects_query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$subjects = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// If no subjects found, initialize empty array
if (!$subjects) {
    $subjects = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Section - CamerinoHub</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/dashboard-shared.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>

<?php include 'includes/navigation.php'; ?>
    
    <div class="dashboard-container">
    <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="container-fluid">
                <!-- Section Information Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Section Information</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Section:</strong> <?php echo htmlspecialchars($section_info['section_name'] ?? 'Not Assigned'); ?></p>
                                <p><strong>Grade Level:</strong> <?php echo htmlspecialchars($section_info['grade_level'] ?? 'Not Assigned'); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Adviser:</strong> <?php echo htmlspecialchars($section_info['adviser_name'] ?? 'Not Assigned'); ?></p>
                                <p><strong>School Year:</strong> 
                                    <?php 
                                        $year_start = $section_info['year_start'] ?? date('Y');
                                        $year_end = $section_info['year_end'] ?? (date('Y') + 1);
                                        echo htmlspecialchars($year_start) . '-' . htmlspecialchars($year_end); 
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Subjects Table -->
                <div class="card">
                    <div class="card-header">
                        <h4>My Subjects</h4>
                    </div>
                    <div class="card-body">
                        <?php if (empty($subjects)): ?>
                            <div class="alert alert-info">
                                No subjects assigned yet.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Subject</th>
                                            <th>Teacher</th>
                                            <th>Activities</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($subjects as $subject): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($subject['subject_code']); ?></strong><br>
                                                <small><?php echo htmlspecialchars($subject['subject_name']); ?></small>
                                            </td>
                                            <td><?php echo htmlspecialchars($subject['teacher_fname'] . ' ' . $subject['teacher_lname']); ?></td>
                                            <td>
                                                <div class="activity-counts">
                                                    <span class="badge badge-primary" title="Activities">
                                                        <i class="fas fa-tasks"></i> <?php echo $subject['activity_count']; ?>
                                                    </span>
                                                    <span class="badge badge-info" title="Quizzes">
                                                        <i class="fas fa-question-circle"></i> <?php echo $subject['quiz_count']; ?>
                                                    </span>
                                                    <span class="badge badge-success" title="Assignments">
                                                        <i class="fas fa-book"></i> <?php echo $subject['assignment_count']; ?>
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-primary view-activities" 
                                                            data-subject-id="<?php echo $subject['subject_id']; ?>"
                                                            data-subject-name="<?php echo htmlspecialchars($subject['subject_name']); ?>">
                                                        <i class="fas fa-list-ul"></i> Activities
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-info view-grades"
                                                            data-subject-id="<?php echo $subject['subject_id']; ?>"
                                                            data-subject-name="<?php echo htmlspecialchars($subject['subject_name']); ?>">
                                                        <i class="fas fa-chart-line"></i> Grades
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Modals -->
    <div class="modal fade" id="activitiesModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Subject Activities</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs" id="activityTabs">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#activities">Activities</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#quizzes">Quizzes</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#assignments">Assignments</a>
                        </li>
                    </ul>
                    <div class="tab-content mt-3" id="activitiesContent">
                        <!-- Content will be loaded dynamically -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <script>
    $(document).ready(function() {
        // View Activities Button
        $('.view-activities').click(function() {
            const subjectId = $(this).data('subject-id');
            const subjectName = $(this).data('subject-name');
            
            $('#activitiesModal').modal('show');
            $('.modal-title').text(subjectName + ' - Activities');
            
            // Load activities content
            loadActivitiesContent(subjectId);
        });

        // View Grades Button
        $('.view-grades').click(function() {
            const subjectId = $(this).data('subject-id');
            window.location.href = 'student_grades.php?subject_id=' + subjectId;
        });

        function loadActivitiesContent(subjectId) {
            $.ajax({
                url: 'get_subject_activities.php',
                type: 'GET',
                data: { subject_id: subjectId },
                success: function(response) {
                    $('#activitiesContent').html(response);
                },
                error: function() {
                    $('#activitiesContent').html(
                        '<div class="alert alert-danger">Error loading activities.</div>'
                    );
                }
            });
        }
    });
    </script>

    <style>
    .activity-counts .badge {
        margin-right: 5px;
        padding: 5px 10px;
    }

    .btn-group .btn {
        margin-right: 5px;
    }

    .nav-tabs .nav-link {
        color: #495057;
    }

    .nav-tabs .nav-link.active {
        color: #007bff;
        font-weight: bold;
    }
    </style>
</body>
</html>
