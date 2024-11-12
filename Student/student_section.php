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
    SELECT DISTINCT
        s.id as subject_id,
        s.subject_code,
        s.subject_name,
        ss.schedule_day,
        ss.schedule_time,
        t.firstname as teacher_firstname,
        t.middlename as teacher_middlename,
        t.lastname as teacher_lastname,
        t.teacher_id,
        (
            SELECT COUNT(a.activity_id) 
            FROM activities a 
            WHERE a.teacher_id = ss.teacher_id
            AND a.type = 'assignment'
            AND EXISTS (
                SELECT 1 
                FROM section_subjects sss 
                WHERE sss.id = ss.id
                AND sss.subject_id = s.id
            )
        ) as assignment_count
    FROM student_sections st
    JOIN sections sec ON st.section_id = sec.section_id
    JOIN section_subjects ss ON sec.section_id = ss.section_id
    JOIN subjects s ON ss.subject_id = s.id
    JOIN teacher t ON ss.teacher_id = t.teacher_id
    JOIN academic_years ay ON ay.status = 'active'
    WHERE st.student_id = ?
    AND st.status = 'active'
    AND ss.status = 'active'
    ORDER BY FIELD(ss.schedule_day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'),
    ss.schedule_time";

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
                                            <th>Subject Code</th>
                                            <th>Subject Name</th>
                                            <th>Schedule</th>
                                            <th>Teacher</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($subjects as $subject): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($subject['subject_code']); ?></td>
                                            <td><?php echo htmlspecialchars($subject['subject_name']); ?></td>
                                            <td>
                                                <?php 
                                                echo htmlspecialchars($subject['schedule_day']) . ' ' . 
                                                     date('h:i A', strtotime($subject['schedule_time'])); 
                                                ?>
                                            </td>
                                            <td>
                                                <?php 
                                                echo htmlspecialchars($subject['teacher_firstname']) . ' ' . 
                                                     htmlspecialchars($subject['teacher_lastname']); 
                                                ?>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-info view-assignments" 
                                                        data-subject-code="<?php echo htmlspecialchars($subject['subject_code']); ?>"
                                                        data-subject-name="<?php echo htmlspecialchars($subject['subject_name']); ?>">
                                                    <i class="fas fa-tasks"></i> Assignments
                                                </button>
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

    <!-- Assignments Modal -->
    <div class="modal fade" id="assignmentsModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Subject Assignments</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" id="assignmentsModalContent">
                    Loading...
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <script>
    $(document).ready(function() {
        $('.view-assignments').click(function() {
            const subjectCode = $(this).data('subject-code');
            const subjectName = $(this).data('subject-name');
            
            $('#assignmentsModal').modal('show');
            $('.modal-title').text(subjectName + ' - Assignments');
            
            $.ajax({
                url: 'get_subject_assignments.php',
                type: 'GET',
                data: { subject_code: subjectCode },
                success: function(response) {
                    $('#assignmentsModalContent').html(response);
                },
                error: function() {
                    $('#assignmentsModalContent').html(
                        '<div class="alert alert-danger">Error loading assignments.</div>'
                    );
                }
            });
        });
    });
    </script>
</body>
</html>
