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

// Fetch active courses
$courses_query = "SELECT 
    c.*,
    sc.status as enrollment_status,
    t.firstname as teacher_firstname,
    t.lastname as teacher_lastname,
    t.middlename as teacher_middlename,
    t.department as teacher_department,
    (SELECT COUNT(*) FROM assignments a WHERE a.course_id = c.course_id) as total_assignments,
    (SELECT COUNT(*) FROM student_submissions ss 
     JOIN assignments a ON ss.assignment_id = a.assignment_id 
     WHERE a.course_id = c.course_id AND ss.student_id = ?) as completed_assignments
FROM student_courses sc
JOIN courses c ON sc.course_id = c.course_id
LEFT JOIN teachers t ON c.teacher_id = t.teacher_id
WHERE sc.student_id = ? AND sc.status = 'active'
ORDER BY c.course_name";

$stmt = $db->prepare($courses_query);
$stmt->bind_param("ii", $student_id, $student_id);
$stmt->execute();
$courses_result = $stmt->get_result();
$active_courses = $courses_result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Courses - Gov D.M. Camerino</title>
    
    <!-- External CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Product+Sans:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/dashboard-shared.css">
    <link rel="stylesheet" href="css/courses.css">
</head>
<body>
    <?php include 'includes/navigation.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="content-header">
                <h1>My Courses</h1>
                <p>View and manage your enrolled courses</p>
            </div>

            <div class="row">
                <?php foreach ($active_courses as $course): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="course-card">
                            <div class="course-header">
                                <h3><?php echo htmlspecialchars($course['course_name']); ?></h3>
                            </div>
                            <div class="course-body">
                                <div class="course-info">
                                    <p><i class="fas fa-user-tie"></i> 
                                       <?php 
                                       $teacher_firstname = $course['teacher_firstname'] ?? '';
                                       $teacher_middlename = $course['teacher_middlename'] ?? '';
                                       $teacher_lastname = $course['teacher_lastname'] ?? '';
                                       
                                       $teacher_name = trim($teacher_firstname . ' ' . $teacher_middlename . ' ' . $teacher_lastname);
                                       echo $teacher_name ? htmlspecialchars($teacher_name) : 'No teacher assigned';
                                       ?>
                                    </p>
                                    <p><i class="fas fa-building"></i> 
                                       <?php echo htmlspecialchars($course['teacher_department'] ?? 'Department not assigned'); ?>
                                    </p>
                                    <p><i class="fas fa-tasks"></i> 
                                       <?php echo (int)$course['completed_assignments']; ?> of <?php echo (int)$course['total_assignments']; ?> assignments completed
                                    </p>
                                </div>
                                
                                <?php 
                                $completion_percentage = $course['total_assignments'] > 0 
                                    ? ($course['completed_assignments'] / $course['total_assignments']) * 100 
                                    : 0;
                                ?>
                                <div class="progress">
                                    <div class="progress-bar bg-primary" 
                                         role="progressbar" 
                                         style="width: <?php echo $completion_percentage; ?>%" 
                                         aria-valuenow="<?php echo $completion_percentage; ?>" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                    </div>
                                </div>
                                
                                <div class="course-actions">
                                    <button type="button" 
                                            class="btn btn-outline-primary view-course" 
                                            data-course-id="<?php echo $course['course_id']; ?>"
                                            data-course-name="<?php echo htmlspecialchars($course['course_name']); ?>">
                                        <i class="fas fa-book-open mr-2"></i>View Course
                                    </button>
                                    <button type="button" 
                                            class="btn btn-outline-primary view-assignments" 
                                            data-course-id="<?php echo $course['course_id']; ?>"
                                            data-course-name="<?php echo htmlspecialchars($course['course_name']); ?>">
                                        <i class="fas fa-tasks mr-2"></i>Assignments
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <?php if (empty($active_courses)): ?>
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            You are not enrolled in any courses at the moment.
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- View Course Modal -->
    <div class="modal fade" id="viewCourseModal" tabindex="-1" role="dialog" aria-labelledby="courseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="courseModalLabel">Course Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="courseModalContent">
                    Loading...
                </div>
            </div>
        </div>
    </div>

    <!-- Assignments Modal -->
    <div class="modal fade" id="assignmentsModal" tabindex="-1" role="dialog" aria-labelledby="assignmentsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignmentsModalLabel">Course Assignments</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="assignmentsModalContent">
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
    $(document).ready(function() {
        // View Course Modal
        $('.view-course').click(function() {
            const courseId = $(this).data('course-id');
            const courseName = $(this).data('course-name');
            
            $('#courseModalLabel').text(courseName + ' - Details');
            $('#courseModalContent').html('Loading...');
            $('#viewCourseModal').modal('show');
            
            // Fetch course details using $.ajax
            $.ajax({
                url: 'get_course_details.php',
                type: 'GET',
                data: { course_id: courseId },
                success: function(response) {
                    $('#courseModalContent').html(response);
                },
                error: function() {
                    $('#courseModalContent').html('<div class="alert alert-danger">Error loading course details.</div>');
                }
            });
        });
        
        // Assignments Modal
        $('.view-assignments').click(function() {
            const courseId = $(this).data('course-id');
            const courseName = $(this).data('course-name');
            
            $('#assignmentsModalLabel').text(courseName + ' - Assignments');
            $('#assignmentsModalContent').html('Loading...');
            $('#assignmentsModal').modal('show');
            
            // Fetch assignments using $.ajax
            $.ajax({
                url: 'get_course_assignments.php',
                type: 'GET',
                data: { course_id: courseId },
                success: function(response) {
                    $('#assignmentsModalContent').html(response);
                },
                error: function(xhr, status, error) {
                    console.log('Error:', error); // Add this for debugging
                    $('#assignmentsModalContent').html('<div class="alert alert-danger">Error loading assignments.</div>');
                }
            });
        });
    });
    </script>
</body>
</html>
