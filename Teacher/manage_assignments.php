<?php
session_start();
require_once('../db/dbConnector.php');

// Check if teacher is logged in
if (!isset($_SESSION['teacher_id'])) {
    header("Location: Teacher-Login.php");
    exit();
}

$db = new DbConnector();
$teacher_id = $_SESSION['teacher_id'];

// Fetch assignments with submission stats
$assignments_query = "SELECT 
    a.assignment_id,
    a.title,
    a.description,
    a.due_date,
    a.created_at,
    COUNT(DISTINCT c.student_id) as total_students,
    COUNT(DISTINCT ss.submission_id) as submitted_count,
    COALESCE(AVG(ss.grade), 0) as average_grade
FROM assignments a
LEFT JOIN classes c ON a.teacher_id = c.teacher_id
LEFT JOIN student_submissions ss ON a.assignment_id = ss.assignment_id
WHERE a.teacher_id = ?
GROUP BY a.assignment_id
ORDER BY a.due_date DESC";

$stmt = $db->prepare($assignments_query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$assignments_result = $stmt->get_result();
$assignments = $assignments_result->fetch_all(MYSQLI_ASSOC);

// Get classes for dropdown
$classes_query = "SELECT class_id, section_name FROM classes WHERE teacher_id = ?";
$stmt = $db->prepare($classes_query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$classes_result = $stmt->get_result();
$classes = $classes_result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Assignments - Gov D.M. Camerino</title>
    
    <!-- External CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/dashboard-shared.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
    <?php include 'includes/navigation.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="content-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1>Manage Assignments</h1>
                        <p>Create and manage your class assignments</p>
                    </div>
                    <button class="btn btn-primary" data-toggle="modal" data-target="#addAssignmentModal">
                        <i class="fas fa-plus"></i> Create Assignment
                    </button>
                </div>
            </div>

            <!-- Assignment Cards -->
            <div class="row mt-4">
                <?php foreach ($assignments as $assignment): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><?php echo htmlspecialchars($assignment['title']); ?></h5>
                                <div class="dropdown">
                                    <button class="btn btn-link" data-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="#" onclick="editAssignment(<?php echo $assignment['assignment_id']; ?>)">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a class="dropdown-item" href="#" onclick="viewSubmissions(<?php echo $assignment['assignment_id']; ?>)">
                                            <i class="fas fa-eye"></i> View Submissions
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item text-danger" href="#" onclick="deleteAssignment(<?php echo $assignment['assignment_id']; ?>)">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="card-text"><?php echo htmlspecialchars(substr($assignment['description'], 0, 100)) . '...'; ?></p>
                                <div class="assignment-stats">
                                    <div class="stat-item">
                                        <i class="fas fa-users"></i>
                                        <span><?php echo $assignment['submitted_count']; ?>/<?php echo $assignment['total_students']; ?></span>
                                        <small>Submissions</small>
                                    </div>
                                    <div class="stat-item">
                                        <i class="fas fa-chart-line"></i>
                                        <span><?php echo number_format($assignment['average_grade'], 1); ?>%</span>
                                        <small>Average</small>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-muted">
                                <small>
                                    <i class="fas fa-clock"></i>
                                    Due: <?php echo date('M j, Y g:i A', strtotime($assignment['due_date'])); ?>
                                </small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Add Assignment Modal -->
    <div class="modal fade" id="addAssignmentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Assignment</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="assignmentForm">
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" name="description" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Class</label>
                            <select class="form-control" name="class_id" required>
                                <?php foreach ($classes as $class): ?>
                                    <option value="<?php echo $class['class_id']; ?>">
                                        <?php echo htmlspecialchars($class['section_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Due Date</label>
                            <input type="datetime-local" class="form-control" name="due_date" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveAssignment()">Create Assignment</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <script>
    function saveAssignment() {
        const form = document.getElementById('assignmentForm');
        const formData = new FormData(form);
        formData.append('action', 'add');
        
        $.ajax({
            url: 'assignment_actions.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                const result = JSON.parse(response);
                if (result.success) {
                    location.reload();
                } else {
                    alert(result.message);
                }
            }
        });
    }

    function editAssignment(assignmentId) {
        // Implement edit functionality
    }

    function viewSubmissions(assignmentId) {
        window.location.href = `view_submissions.php?assignment_id=${assignmentId}`;
    }

    function deleteAssignment(assignmentId) {
        if (confirm('Are you sure you want to delete this assignment?')) {
            $.post('assignment_actions.php', {
                action: 'delete',
                assignment_id: assignmentId
            }, function(response) {
                const result = JSON.parse(response);
                if (result.success) {
                    location.reload();
                } else {
                    alert(result.message);
                }
            });
        }
    }
    </script>
</body>
</html>
