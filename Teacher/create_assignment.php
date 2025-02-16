<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['teacher_id'])) {
    header("Location: Teacher-Login.php");
    exit();
}

$db = new DbConnector();
$teacher_id = $_SESSION['teacher_id'];

// Fetch sections for dropdown
$sections_query = "SELECT ss.id as section_subject_id, s.section_name, sub.subject_name
    FROM section_subjects ss
    JOIN sections s ON ss.section_id = s.section_id
    JOIN subjects sub ON ss.subject_id = sub.id
    WHERE ss.teacher_id = ? AND ss.status = 'active'";
$stmt = $db->prepare($sections_query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$sections = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Assignment - CamerinoHub</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/dashboard-shared.css">
    <link rel="stylesheet" href="css/create-activity.css">
	<link rel="icon" href="../images/light-logo.png">
</head>
<body>
    <?php include 'includes/navigation.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="content-header">
                <h1>Create Assignment</h1>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <form id="assignmentForm" action="handlers/save_assignment.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="type" value="assignment">
                        
                        <div class="form-group">
                            <label>Section & Subject</label>
                            <select name="section_subject_id" class="form-control" required>
                                <option value="" disabled selected>Select Section & Subject</option>
                                <?php foreach ($sections as $section): ?>
                                    <option value="<?php echo $section['section_subject_id']; ?>">
                                        <?php echo htmlspecialchars($section['section_name'] . ' - ' . $section['subject_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Assignment Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="3" required></textarea>
                        </div>

                        <div class="form-row">
                            <div class="col">
                                <div class="form-group">
                                    <label>Due Date</label>
                                    <?php $default_due_date = date('Y-m-d\TH:i', strtotime('+7 days')); ?>
                                    <input type="datetime-local" 
                                           name="due_date" 
                                           class="form-control" 
                                           required 
                                           value="<?php echo $default_due_date; ?>"
                                           min="<?php echo date('Y-m-d\TH:i'); ?>">
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label>Points</label>
                                    <input type="number" name="points" class="form-control" required value="100" min="0">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Assignment Files</label>
                            <input type="file" name="activity_files[]" class="form-control-file" multiple 
                                   accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx">
                            <small class="text-muted">Allowed types: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX</small>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">Create Assignment</button>
                            <a href="manage_activities.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>

                    <?php if (isset($_SESSION['error_message'])): ?>
                        <div class="alert alert-danger mt-3">
                            <?php 
                            echo $_SESSION['error_message'];
                            unset($_SESSION['error_message']);
                            ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['success_message'])): ?>
                        <div class="alert alert-success mt-3">
                            <?php 
                            echo $_SESSION['success_message'];
                            unset($_SESSION['success_message']);
                            ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $('#assignmentForm').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    try {
                        const result = typeof response === 'string' ? JSON.parse(response) : response;
                        if (result.success) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Assignment created successfully!',
                                icon: 'success'
                            }).then(() => {
                                window.location.href = 'manage_activities.php';
                            });
                        } else {
                            Swal.fire('Error!', result.message || 'Failed to create assignment', 'error');
                        }
                    } catch (e) {
                        Swal.fire('Error!', 'Invalid server response', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error!', 'Failed to create assignment', 'error');
                }
            });
        });
    </script>
</body>
</html>
