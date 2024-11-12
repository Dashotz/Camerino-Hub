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
$userData = $stmt->get_result()->fetch_assoc();

// Fetch assignments with their status
$assignments_query = "
    SELECT 
        a.activity_id,
        a.title,
        a.description,
        a.due_date,
        a.points,
        s.subject_name,
        s.subject_code,
        GROUP_CONCAT(DISTINCT af.file_path) as file_paths,
        GROUP_CONCAT(DISTINCT af.file_name) as file_names,
        CASE 
            WHEN sas.submission_id IS NOT NULL THEN 'Submitted'
            WHEN a.due_date < NOW() THEN 'Overdue'
            ELSE 'Pending'
        END as status,
        sas.points as grade,
        sas.submitted_at
    FROM student_sections ss
    JOIN sections sec ON ss.section_id = sec.section_id
    JOIN teacher_subjects ts ON sec.section_id = ts.section_id
    JOIN subjects s ON ts.subject_id = s.id
    JOIN activities a ON ts.teacher_id = a.teacher_id 
        AND ts.subject_id = s.id
    LEFT JOIN activity_files af ON a.activity_id = af.activity_id
    LEFT JOIN student_activity_submissions sas ON a.activity_id = sas.activity_id 
        AND sas.student_id = ?
    WHERE ss.student_id = ?
        AND ss.status = 'active'
        AND ts.status = 'active'
        AND a.type = 'assignment'
    GROUP BY 
        a.activity_id,
        a.title,
        a.description,
        a.due_date,
        a.points,
        s.subject_name,
        s.subject_code,
        sas.points,
        sas.submitted_at
    ORDER BY 
        CASE 
            WHEN sas.submission_id IS NULL AND a.due_date >= NOW() THEN 1
            WHEN sas.submission_id IS NULL AND a.due_date < NOW() THEN 2
            ELSE 3
        END,
        a.due_date ASC";

$stmt = $db->prepare($assignments_query);
$stmt->bind_param("ii", $student_id, $student_id);
$stmt->execute();
$assignments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignments - Gov D.M. Camerino</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/dashboard-shared.css">
    <link rel="stylesheet" href="css/assignments.css">
</head>
<body>
    <?php include 'includes/navigation.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="content-header">
                <h1>My Assignments</h1>
                <div class="filters">
                    <input type="text" id="searchInput" placeholder="Search assignments...">
                    <div class="filter-buttons">
                        <button class="filter-btn active" data-filter="all">All</button>
                        <button class="filter-btn" data-filter="pending">Pending</button>
                        <button class="filter-btn" data-filter="submitted">Submitted</button>
                        <button class="filter-btn" data-filter="overdue">Overdue</button>
                    </div>
                </div>
            </div>

            <div class="assignments-container">
                <?php if (empty($assignments)): ?>
                    <div class="no-assignments">
                        <p>No assignments found.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($assignments as $assignment): ?>
                        <div class="assignment-card" data-status="<?php echo strtolower($assignment['status']); ?>">
                            <div class="assignment-header">
                                <h3 class="assignment-title"><?php echo htmlspecialchars($assignment['title']); ?></h3>
                                <span class="assignment-course"><?php echo htmlspecialchars($assignment['subject_name']); ?></span>
                            </div>
                            <div class="assignment-body">
                                <div class="assignment-details">
                                    <div class="due-date">
                                        <i class="far fa-calendar-alt"></i>
                                        Due: <?php echo date('M j, Y', strtotime($assignment['due_date'])); ?>
                                    </div>
                                    <span class="status-badge status-<?php echo strtolower($assignment['status']); ?>">
                                        <?php echo $assignment['status']; ?>
                                    </span>
                                </div>
                                <p class="assignment-description">
                                    <?php echo htmlspecialchars($assignment['description']); ?>
                                </p>
                                <?php if (!empty($assignment['file_paths'])): ?>
                                    <div class="assignment-files">
                                        <h5>Attached Files:</h5>
                                        <?php 
                                        $file_paths = explode(',', $assignment['file_paths']);
                                        $file_names = explode(',', $assignment['file_names']);
                                        foreach ($file_paths as $index => $path): ?>
                                            <a href="<?php echo htmlspecialchars($path); ?>" class="file-link" target="_blank">
                                                <i class="fas fa-file"></i> 
                                                <?php echo htmlspecialchars($file_names[$index]); ?>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                <div class="assignment-actions">
                                    <?php if ($assignment['status'] === 'Pending'): ?>
                                        <button class="btn btn-submit" onclick="submitAssignment(<?php echo $assignment['activity_id']; ?>)">
                                            Submit Assignment
                                        </button>
                                    <?php elseif ($assignment['status'] === 'Submitted'): ?>
                                        <button class="btn btn-outline-primary" onclick="viewSubmission(<?php echo $assignment['activity_id']; ?>)">
                                            View Submission
                                        </button>
                                        <?php if (isset($assignment['grade'])): ?>
                                            <span class="grade-badge">
                                                Score: <?php echo $assignment['grade']; ?>/<?php echo $assignment['points']; ?>
                                            </span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const searchText = this.value.toLowerCase();
            document.querySelectorAll('.assignment-card').forEach(card => {
                const title = card.querySelector('.assignment-title').textContent.toLowerCase();
                const course = card.querySelector('.assignment-course').textContent.toLowerCase();
                card.style.display = (title.includes(searchText) || course.includes(searchText)) ? '' : 'none';
            });
        });

        // Filter functionality
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Update active state
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                // Filter assignments
                const filter = this.dataset.filter;
                document.querySelectorAll('.assignment-card').forEach(card => {
                    if (filter === 'all' || card.dataset.status === filter) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });

        // Submit assignment function
        function submitAssignment(assignmentId) {
            // Add your submission logic here
            console.log('Submitting assignment:', assignmentId);
        }

        // View submission function
        function viewSubmission(assignmentId) {
            // Add your view submission logic here
            console.log('Viewing submission:', assignmentId);
        }
    </script>
</body>
</html>
