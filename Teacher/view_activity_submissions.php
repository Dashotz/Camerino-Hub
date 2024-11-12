<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['teacher_id']) || !isset($_GET['activity_id'])) {
    header("Location: Teacher-Login.php");
    exit();
}

$db = new DbConnector();
$teacher_id = $_SESSION['teacher_id'];
$activity_id = $_GET['activity_id'];

// Fetch activity details
$activity_query = "SELECT 
    a.*,
    c.section_name,
    s.subject_code,
    s.subject_name
FROM activities a
JOIN classes c ON a.class_id = c.class_id
JOIN subjects s ON c.subject_id = s.id
WHERE a.activity_id = ? AND a.teacher_id = ?";

$stmt = $db->prepare($activity_query);
$stmt->bind_param("ii", $activity_id, $teacher_id);
$stmt->execute();
$activity = $stmt->get_result()->fetch_assoc();

if (!$activity) {
    header("Location: manage_activities.php");
    exit();
}

// Fetch submissions
$submissions_query = "SELECT 
    sa.submission_id,
    sa.submitted_at,
    sa.file_path,
    sa.points,
    sa.feedback,
    s.student_id,
    s.firstname,
    s.lastname
FROM student_activity_submissions sa
JOIN student s ON sa.student_id = s.student_id
WHERE sa.activity_id = ?
ORDER BY sa.submitted_at DESC";

$stmt = $db->prepare($submissions_query);
$stmt->bind_param("i", $activity_id);
$stmt->execute();
$submissions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get all students in the class who haven't submitted
$pending_query = "SELECT 
    s.student_id,
    s.firstname,
    s.lastname
FROM student s
JOIN student_courses sc ON s.student_id = sc.student_id
LEFT JOIN student_activity_submissions sa 
    ON s.student_id = sa.student_id 
    AND sa.activity_id = ?
WHERE sc.class_id = ? AND sa.submission_id IS NULL";

$stmt = $db->prepare($pending_query);
$stmt->bind_param("ii", $activity_id, $activity['class_id']);
$stmt->execute();
$pending_students = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Submissions - CamerinoHub</title>
    
    <!-- External CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/dashboard-shared.css">
    <style>
        .submission-card {
            transition: transform 0.2s;
        }
        .submission-card:hover {
            transform: translateY(-2px);
        }
        .grade-badge {
            font-size: 1.2rem;
            padding: 0.5rem 1rem;
        }
    </style>
</head>
<body>
    <?php include 'includes/navigation.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="content-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1><?php echo htmlspecialchars($activity['title']); ?></h1>
                        <p>
                            <?php echo htmlspecialchars($activity['subject_code'] . ' - ' . $activity['section_name']); ?>
                            <span class="badge badge-info ml-2">
                                <?php echo ucfirst($activity['type']); ?>
                            </span>
                        </p>
                    </div>
                    <a href="manage_activities.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Activities
                    </a>
                </div>
            </div>

            <!-- Modified Submission Stats -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5>Total Submissions</h5>
                            <h2><?php echo count($submissions); ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5>Pending Submissions</h5>
                            <h2><?php echo count($pending_students); ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5>Total Points</h5>
                            <h2><?php echo isset($activity['points']) ? $activity['points'] : 'Not Set'; ?></h2>
                            <button class="btn btn-light btn-sm mt-2" onclick="setPoints()">
                                <i class="fas fa-edit"></i> Set Points
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modified Submissions Table -->
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#submitted">Submitted</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#pending">Pending</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="submitted">
                            <table class="table table-striped" id="submissionsTable">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Submitted At</th>
                                        <th>File</th>
                                        <th>Points</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($submissions as $submission): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($submission['lastname'] . ', ' . $submission['firstname']); ?></td>
                                            <td><?php echo date('M j, Y g:i A', strtotime($submission['submitted_at'])); ?></td>
                                            <td>
                                                <?php if ($submission['file_path']): ?>
                                                    <a href="../<?php echo $submission['file_path']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-download"></i> Download
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">No file</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($submission['points']): ?>
                                                    <span class="badge badge-success">
                                                        <?php echo $submission['points']; ?> / <?php echo $activity['points']; ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">Not graded</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-primary" onclick="gradeSubmission(<?php echo $submission['submission_id']; ?>)">
                                                    <i class="fas fa-check"></i> Grade
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane fade" id="pending">
                            <table class="table" id="pendingTable">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pending_students as $student): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($student['lastname'] . ', ' . $student['firstname']); ?></td>
                                            <td>
                                                <span class="badge badge-warning">Pending</span>
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

    <!-- Points Setting Modal -->
    <div class="modal fade" id="pointsModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Set Activity Points</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="pointsForm">
                        <input type="hidden" name="activity_id" value="<?php echo $activity_id; ?>">
                        <div class="form-group">
                            <label>Total Points</label>
                            <input type="number" class="form-control" name="points" min="1" max="100" 
                                   value="<?php echo $activity['points'] ?? 100; ?>" required>
                            <small class="form-text text-muted">Set the maximum points for this activity</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="savePoints()">Save Points</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modified Grade Modal -->
    <div class="modal fade" id="gradeModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Grade Submission</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="gradeForm">
                        <input type="hidden" name="submission_id" id="submissionId">
                        <div class="form-group">
                            <label>Points (out of <?php echo $activity['points'] ?? '100'; ?>)</label>
                            <input type="number" class="form-control" name="points" 
                                   min="0" max="<?php echo $activity['points'] ?? 100; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Private Comments</label>
                            <textarea class="form-control" name="feedback" rows="3" 
                                    placeholder="Add private comments for the student"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="submitGrade()">Return</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>
    
    <script>
    $(document).ready(function() {
        $('#submissionsTable').DataTable({
            order: [[1, 'desc']]
        });
        $('#pendingTable').DataTable();
    });

    function gradeSubmission(submissionId) {
        $('#submissionId').val(submissionId);
        $('#gradeModal').modal('show');
    }

    function setPoints() {
        $('#pointsModal').modal('show');
    }

    function savePoints() {
        const formData = new FormData(document.getElementById('pointsForm'));
        
        $.ajax({
            url: 'set_activity_points.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                const result = JSON.parse(response);
                if (result.success) {
                    $('#pointsModal').modal('hide');
                    location.reload();
                } else {
                    alert(result.message);
                }
            }
        });
    }

    function submitGrade() {
        const formData = new FormData(document.getElementById('gradeForm'));
        
        $.ajax({
            url: 'grade_submissions.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                const result = JSON.parse(response);
                if (result.success) {
                    $('#gradeModal').modal('hide');
                    location.reload();
                } else {
                    alert(result.message);
                }
            }
        });
    }
    </script>
</body>
</html>
