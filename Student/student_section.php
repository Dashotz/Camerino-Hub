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
        COALESCE(ay.school_year, 
            CONCAT(YEAR(CURRENT_DATE), '-', YEAR(CURRENT_DATE) + 1)) as school_year,
        COALESCE(ss.status, 'inactive') as enrollment_status,
        (SELECT COUNT(*) 
         FROM student_sections 
         WHERE section_id = sec.section_id 
         AND status = 'active') as total_students
    FROM student s
    LEFT JOIN student_sections ss ON s.student_id = ss.student_id AND ss.status = 'active'
    LEFT JOIN sections sec ON ss.section_id = sec.section_id
    LEFT JOIN academic_years ay ON ay.id = ss.academic_year_id 
    WHERE s.student_id = ?
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
        'school_year' => date('Y') . '-' . (date('Y') + 1),
        'enrollment_status' => 'inactive',
        'total_students' => 0
    ];
}

// Fetch student's subjects
$subjects_query = "
    SELECT 
        s.id as subject_id,
        s.subject_name,
        s.subject_code,
        CONCAT(t.firstname, ' ', t.lastname) as teacher_name,
        t.firstname as teacher_fname,
        t.lastname as teacher_lname,
        COALESCE((SELECT COUNT(*) FROM activities a 
         WHERE a.section_subject_id = ss.id 
         AND a.type = 'activity' 
         AND a.status = 'active'), 0) as activity_count,
        COALESCE((SELECT COUNT(*) FROM activities a 
         WHERE a.section_subject_id = ss.id 
         AND a.type = 'quiz' 
         AND a.status = 'active'), 0) as quiz_count,
        COALESCE((SELECT COUNT(*) FROM activities a 
         WHERE a.section_subject_id = ss.id 
         AND a.type = 'assignment' 
         AND a.status = 'active'), 0) as assignment_count
    FROM student st
    LEFT JOIN student_sections st_sec ON st.student_id = st_sec.student_id AND st_sec.status = 'active'
    LEFT JOIN sections sec ON st_sec.section_id = sec.section_id
    LEFT JOIN section_subjects ss ON sec.section_id = ss.section_id AND ss.status = 'active'
    LEFT JOIN subjects s ON ss.subject_id = s.id
    LEFT JOIN teacher t ON ss.teacher_id = t.teacher_id
    WHERE st.student_id = ?
    ORDER BY s.subject_name";

$stmt = $db->prepare($subjects_query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$subjects = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// If no subjects found, initialize empty array
if (!$subjects) {
    $subjects = [];
}

// Add query to get classmates
$classmates_query = "
    SELECT 
        s.student_id,
        s.firstname,
        s.lastname,
        s.profile_image,
        s.email,
        CASE WHEN s.student_id = ? THEN 1 ELSE 0 END as is_current_user
    FROM student_sections ss
    JOIN student s ON ss.student_id = s.student_id
    WHERE ss.section_id = (
        SELECT section_id 
        FROM student_sections 
        WHERE student_id = ? 
        AND status = 'active'
    )
    AND ss.status = 'active'
    ORDER BY is_current_user DESC, s.lastname, s.firstname";

$stmt = $db->prepare($classmates_query);
$stmt->bind_param("ii", $student_id, $student_id);
$stmt->execute();
$classmates = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
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
	<link rel="icon" href="../images/light-logo.png">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
</head>
<body>

<?php include 'includes/navigation.php'; ?>
    
    <div class="dashboard-container">
    <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="container-fluid">
                <!-- Section Information Card -->
                <div class="card mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0 text-primary">Section Information</h4>
                        <div>
                            <span class="badge badge-soft-primary mr-3">
                                <?php echo $section_info['total_students']; ?> Students
                            </span>
                            <?php if ($section_info['enrollment_status'] === 'active'): ?>
                                <button class="btn btn-danger btn-sm" onclick="confirmUnenroll()">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Un-enroll from Section
                                </button>
                            <?php endif; ?>
                            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#joinSubjectModal">
                                <i class="fas fa-plus-circle mr-2"></i> Join to Section
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-group mb-4">
                                    <label class="text-muted small">Section Name</label>
                                    <h5 class="text-dark mb-0"><?php echo htmlspecialchars($section_info['section_name']); ?></h5>
                                </div>
                                <div class="info-group mb-4">
                                    <label class="text-muted small">Grade Level</label>
                                    <h5 class="text-dark mb-0">Grade <?php echo htmlspecialchars($section_info['grade_level']); ?></h5>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-group mb-4">
                                    <label class="text-muted small">Academic Year</label>
                                    <h5 class="text-dark mb-0">
                                        <?php echo htmlspecialchars($section_info['school_year']); ?>
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Classmates List -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h4 class="mb-0 text-primary">My Classmates</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php
                            if (empty($classmates)) {
                                echo '<div class="col-12">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle mr-2"></i>
                                            No students enrolled in this section.
                                        </div>
                                      </div>';
                            } else {
                                foreach ($classmates as $classmate):
                                    $cardClass = $classmate['is_current_user'] ? 'border-primary' : '';
                            ?>
                                <div class="col-md-6 mb-4">
                                    <div class="card h-100 classmate-card <?php echo $cardClass; ?>">
                                        <div class="card-body d-flex align-items-center">
                                            <div class="avatar-circle mr-3">
                                                <?php if (!empty($classmate['profile_image'])): ?>
                                                    <img src="<?php echo htmlspecialchars($classmate['profile_image']); ?>" 
                                                         alt="Profile Image" class="rounded-circle">
                                                <?php else: ?>
                                                    <img src="../images/default-avatar.png" 
                                                         alt="Default Profile" class="rounded-circle">
                                                <?php endif; ?>
                                            </div>
                                            <div class="student-info">
                                                <h6 class="mb-1">
                                                    <?php 
                                                    echo htmlspecialchars($classmate['firstname'] . ' ' . $classmate['lastname']);
                                                    if ($classmate['is_current_user']) {
                                                        echo ' <span class="badge badge-primary">You</span>';
                                                    }
                                                    ?>
                                                </h6>
                                                <small class="text-muted">
                                                    <i class="fas fa-envelope mr-1"></i>
                                                    <?php echo htmlspecialchars($classmate['email']); ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php 
                                endforeach;
                            }
                            ?>
                        </div>
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

    <!-- Join Section Modal -->
    <div class="modal fade" id="joinSubjectModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Join to Section</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="enrollmentCode">Enter Subject Code</label>
                        <input type="text" class="form-control" id="enrollmentCode" 
                               placeholder="Enter the code provided by your teacher">
                        <small class="form-text text-muted">
                            Ask your teacher for the subject enrollment code
                        </small>
                    </div>
                    <div id="joinError" class="alert alert-danger d-none"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="joinSubject()">Join Section</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Add SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

    function joinSubject() {
        const code = document.getElementById('enrollmentCode').value.trim();
        const errorDiv = document.getElementById('joinError');
        
        if (!code) {
            errorDiv.textContent = 'Please enter an enrollment code';
            errorDiv.classList.remove('d-none');
            return;
        }

        errorDiv.classList.add('d-none');

        $.ajax({
            url: 'handlers/subject_handler.php',
            type: 'POST',
            data: {
                action: 'join_subject',
                code: code
            },
            dataType: 'json',
            success: function(response) {
                $('#joinSubjectModal').modal('hide');
                
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'You have successfully joined the subject',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'An error occurred. Please try again.'
                    });
                }
            },
            error: function(xhr, status, error) {
                $('#joinSubjectModal').modal('hide');
                console.error('Ajax Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred. Please try again.'
                });
            }
        });
    }

    function confirmUnenroll() {
        Swal.fire({
            title: 'Are you sure?',
            text: 'You are about to un-enroll from this section. This will remove you from all subjects in this section.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, un-enroll',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                unenrollFromSection();
            }
        });
    }

    function unenrollFromSection() {
        $.ajax({
            url: 'handlers/section_handler.php',
            type: 'POST',
            data: {
                action: 'unenroll_section'
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'You have been un-enrolled from the section',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'An error occurred. Please try again.'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred. Please try again.'
                });
            }
        });
    }
    </script>

    <style>
    .info-group label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
    }

    .info-group h5 {
        margin-bottom: 0;
        font-size: 1rem;
        font-weight: 500;
    }

    .badge-soft-primary {
        background-color: rgba(13, 110, 253, 0.1);
        color: #0d6efd;
        font-weight: 500;
        font-size: 0.875rem;
    }

    .avatar-circle {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        overflow: hidden;
        background-color: #e9ecef;
    }

    .avatar-circle img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .classmate-card {
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .classmate-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    .card-header {
        border-bottom: 1px solid rgba(0, 0, 0, 0.125);
    }

    .card-header h4 {
        font-weight: 600;
        font-size: 1.1rem;
    }

    .student-info h6 {
        font-size: 0.95rem;
        font-weight: 600;
    }

    .student-info small {
        font-size: 0.8rem;
        color: #6c757d;
    }

    /* Add smooth transitions */
    .card {
        transition: all 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    .classmate-card {
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .classmate-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
    }

    .avatar-circle {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        overflow: hidden;
        background-color: #e9ecef;
    }

    .avatar-circle img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .student-info h6 {
        font-size: 0.95rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .student-info small {
        font-size: 0.8rem;
        color: #6c757d;
    }

    .badge-primary {
        background-color: #007bff;
        color: white;
        font-size: 0.75rem;
        padding: 0.25em 0.5em;
        margin-left: 0.5rem;
    }
    </style>
</body>
</html>
