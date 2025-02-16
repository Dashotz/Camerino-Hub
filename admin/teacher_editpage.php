<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
    exit();
}

$db = new DbConnector();
$teacher_id = $_GET['id'] ?? null;

if (!$teacher_id) {
    header("Location: manage_teachers.php");
    exit();
}

// Get teacher details with proper field names
$teacher_query = "
    SELECT t.*, d.department_name,
           GROUP_CONCAT(DISTINCT ss.subject_id) as subject_ids,
           GROUP_CONCAT(DISTINCT ss.section_id) as section_ids
    FROM teacher t
    LEFT JOIN departments d ON t.department_id = d.department_id
    LEFT JOIN section_subjects ss ON t.teacher_id = ss.teacher_id AND ss.status = 'active'
    WHERE t.teacher_id = ? AND t.status = 'active'
    GROUP BY t.teacher_id";

$stmt = $db->prepare($teacher_query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$teacher = $stmt->get_result()->fetch_assoc();

// Get current subjects for this teacher (limit to one)
$current_subjects_query = "
    SELECT DISTINCT s.id, s.subject_name 
    FROM section_subjects ss
    JOIN subjects s ON ss.subject_id = s.id
    WHERE ss.teacher_id = ? 
    AND ss.status = 'active'
    AND s.status = 'active'
    LIMIT 1";
$stmt = $db->prepare($current_subjects_query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$current_subjects = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get current sections for this teacher
$current_sections_query = "
    SELECT DISTINCT s.section_id as id, s.section_name as name
    FROM section_subjects ss
    JOIN sections s ON ss.section_id = s.section_id
    WHERE ss.teacher_id = ? 
    AND ss.status = 'active'
    AND s.status = 'active'";
$stmt = $db->prepare($current_sections_query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$current_sections = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get all active departments, subjects, and sections
$dept_query = "SELECT * FROM departments WHERE status = 'active'";
$departments = $db->query($dept_query)->fetch_all(MYSQLI_ASSOC);

$subject_query = "SELECT id, subject_name, subject_code 
                 FROM subjects 
                 WHERE status = 'active'";
$subjects = $db->query($subject_query)->fetch_all(MYSQLI_ASSOC);

$section_query = "SELECT * FROM sections WHERE status = 'active'";
$sections = $db->query($section_query)->fetch_all(MYSQLI_ASSOC);

// Modify the subject query to show currently selected subject
$current_subject_query = "
    SELECT DISTINCT s.id, s.subject_name, s.subject_code, 
           ss.schedule_day, ss.schedule_time
    FROM section_subjects ss
    JOIN subjects s ON ss.subject_id = s.id
    WHERE ss.teacher_id = ? 
    AND ss.status = 'active'
    AND s.status = 'active'
    LIMIT 1";
$stmt = $db->prepare($current_subject_query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$current_subject = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Teacher - Admin Dashboard</title>
    
    <!-- External CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="css/dashboard-shared.css" rel="stylesheet">
	<link rel="icon" href="../images/light-logo.png">
</head>
<body>
    <?php include 'includes/navigation.php'; ?>
    
    <div class="d-flex">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="container-fluid px-4">
                <h1 class="mt-4">Edit Teacher</h1>

                
                <div class="card mb-4">
                    <div class="card-body">
                        <form id="editTeacherForm">
                            <input type="hidden" name="teacher_id" value="<?php echo $teacher_id; ?>">
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>First Name</label>
                                        <input type="text" class="form-control" name="firstname" 
                                               value="<?php echo htmlspecialchars($teacher['firstname']); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Middle Name</label>
                                        <input type="text" class="form-control" name="middlename" 
                                               value="<?php echo htmlspecialchars($teacher['middlename'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Last Name</label>
                                        <input type="text" class="form-control" name="lastname" 
                                               value="<?php echo htmlspecialchars($teacher['lastname']); ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" class="form-control" name="email" 
                                               value="<?php echo htmlspecialchars($teacher['email']); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Department</label>
                                        <select class="form-control" name="department_id" required>
                                            <?php foreach ($departments as $dept): ?>
                                                <option value="<?php echo $dept['department_id']; ?>" 
                                                    <?php echo ($dept['department_id'] == $teacher['department_id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($dept['department_name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Assign Subject</label>
                                <select class="form-control select2-single" name="subject_id" required>
                                    <option value="">Select a subject</option>
                                    <?php foreach ($subjects as $subject): ?>
                                        <option value="<?php echo $subject['id']; ?>" 
                                            <?php echo ($current_subject && $current_subject['id'] == $subject['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($subject['subject_code'] . ' - ' . $subject['subject_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Schedule Day</label>
                                        <select class="form-control" name="schedule_day" required>
                                            <option value="">Select day</option>
                                            <option value="Monday" <?php echo ($current_subject && isset($current_subject['schedule_day']) && $current_subject['schedule_day'] == 'Monday') ? 'selected' : ''; ?>>Monday</option>
                                            <option value="Tuesday" <?php echo ($current_subject && isset($current_subject['schedule_day']) && $current_subject['schedule_day'] == 'Tuesday') ? 'selected' : ''; ?>>Tuesday</option>
                                            <option value="Wednesday" <?php echo ($current_subject && isset($current_subject['schedule_day']) && $current_subject['schedule_day'] == 'Wednesday') ? 'selected' : ''; ?>>Wednesday</option>
                                            <option value="Thursday" <?php echo ($current_subject && isset($current_subject['schedule_day']) && $current_subject['schedule_day'] == 'Thursday') ? 'selected' : ''; ?>>Thursday</option>
                                            <option value="Friday" <?php echo ($current_subject && isset($current_subject['schedule_day']) && $current_subject['schedule_day'] == 'Friday') ? 'selected' : ''; ?>>Friday</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Schedule Time</label>
                                        <input type="time" class="form-control" name="schedule_time" 
                                               value="<?php echo ($current_subject && isset($current_subject['schedule_time'])) ? $current_subject['schedule_time'] : ''; ?>" 
                                               required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Assign Sections</label>
                                <select class="form-control select2-multiple" name="sections[]" multiple required>
                                    <option value="">Select sections</option>
                                    <?php foreach ($sections as $section): ?>
                                        <option value="<?php echo $section['section_id']; ?>"
                                            <?php 
                                            foreach ($current_sections as $current_section) {
                                                if ($current_section['id'] == $section['section_id']) {
                                                    echo 'selected';
                                                    break;
                                                }
                                            }
                                            ?>>
                                            <?php echo htmlspecialchars($section['section_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="text-right">
                                <button type="button" class="btn btn-secondary" onclick="window.location.href='manage_teachers.php'">
                                    Cancel
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- External Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Custom Scripts -->
    <script>
        $(document).ready(function() {
            // Initialize select2 for single subject
            $('.select2-single').select2({
                width: '100%'
            });

            // Initialize select2 for multiple sections
            $('.select2-multiple').select2({
                width: '100%',
                placeholder: 'Select sections'
            });

            // Update form submission handler
            $('#editTeacherForm').submit(function(e) {
                e.preventDefault();
                
                // Validate subject selection
                if (!$('select[name="subject_id"]').val()) {
                    Swal.fire('Error!', 'Please select a subject', 'error');
                    return;
                }
                
                // Validate sections selection
                if (!$('select[name="sections[]"]').val() || $('select[name="sections[]"]').val().length === 0) {
                    Swal.fire('Error!', 'Please select at least one section', 'error');
                    return;
                }
                
                $.ajax({
                    url: 'functions/edit_teacher.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Teacher updated successfully',
                                icon: 'success'
                            }).then(() => {
                                window.location.href = 'manage_teachers.php';
                            });
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error!', 'Failed to update teacher', 'error');
                    }
                });
            });
        });
    </script>
</body>
</html>
