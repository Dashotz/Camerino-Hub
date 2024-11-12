<?php
session_start();
require_once('../db/dbConnector.php');

// Add session check and debugging
if (!isset($_SESSION['teacher_id'])) {
    error_log("Teacher session not found: " . print_r($_SESSION, true));
    header("Location: Teacher-Login.php");
    exit();
}

// Add session timeout check
$session_timeout = 7200; // 2 hours in seconds
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $session_timeout)) {
    session_unset();
    session_destroy();
    header("Location: Teacher-Login.php?msg=session_expired");
    exit();
}
$_SESSION['last_activity'] = time();

// Handle POST request for grading
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    // Debug POST data
    error_log("POST Request received: " . file_get_contents('php://input'));
    
    // Get JSON data from request body
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate required fields with detailed error messages
    if (!isset($data['submission_id']) || !isset($data['points'])) {
        error_log("Missing fields in grade submission: " . print_r($data, true));
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit();
    }

    $db = new DbConnector();
    $teacher_id = $_SESSION['teacher_id'];
    $submission_id = intval($data['submission_id']);
    $points = intval($data['points']);
    $feedback = $data['feedback'] ?? '';

    // Verify submission belongs to teacher's activity
    $verify_query = "SELECT a.teacher_id, a.points as max_points
                    FROM student_activity_submissions sas
                    JOIN activities a ON sas.activity_id = a.activity_id
                    JOIN section_subjects ss ON a.section_subject_id = ss.id
                    WHERE sas.submission_id = ? 
                    AND ss.teacher_id = ?
                    AND a.status = 'active'";

    $stmt = $db->prepare($verify_query);
    $stmt->bind_param("ii", $submission_id, $teacher_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if (!$result) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized access to this submission']);
        exit();
    }

    // Validate points
    if ($points > $result['max_points']) {
        echo json_encode(['success' => false, 'message' => 'Points cannot exceed maximum activity points']);
        exit();
    }

    // Update points and feedback
    $update_query = "UPDATE student_activity_submissions 
                    SET points = ?, 
                        feedback = ?,
                        graded_at = CURRENT_TIMESTAMP
                    WHERE submission_id = ?";

    $stmt = $db->prepare($update_query);
    $stmt->bind_param("isi", $points, $feedback, $submission_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Submission graded successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error while updating grade']);
    }
    exit();
}

// Handle GET request for viewing submissions
if (!isset($_SESSION['teacher_id']) || !isset($_GET['id'])) {
    header("Location: Teacher-Login.php");
    exit();
}

$db = new DbConnector();
$activity_id = $_GET['id'];
$teacher_id = $_SESSION['teacher_id'];

// Fetch activity details
$activity_query = "
    SELECT a.*, sec.section_name, sub.subject_name, sub.subject_code
    FROM activities a
    JOIN section_subjects ss ON a.section_subject_id = ss.id
    JOIN sections sec ON ss.section_id = sec.section_id
    JOIN subjects sub ON ss.subject_id = sub.id
    WHERE a.activity_id = ? AND ss.teacher_id = ?";

$stmt = $db->prepare($activity_query);
$stmt->bind_param("ii", $activity_id, $teacher_id);
$stmt->execute();
$activity = $stmt->get_result()->fetch_assoc();

if (!$activity) {
    header("Location: manage_activities.php");
    exit();
}

// Fetch submissions
$submissions_query = "
    SELECT 
        s.student_id,
        s.firstname,
        s.lastname,
        sas.submission_id,
        sas.submitted_at,
        sas.points,
        sas.feedback,
        sas.graded_at,
        GROUP_CONCAT(sf.file_path) as files
    FROM student_sections ss
    JOIN student s ON ss.student_id = s.student_id
    LEFT JOIN student_activity_submissions sas 
        ON s.student_id = sas.student_id 
        AND sas.activity_id = ?
    LEFT JOIN submission_files sf 
        ON sas.submission_id = sf.submission_id
    WHERE ss.section_id = (
        SELECT section_id 
        FROM section_subjects 
        WHERE id = ?
    )
    AND ss.status = 'active'
    GROUP BY s.student_id
    ORDER BY s.lastname, s.firstname";

$stmt = $db->prepare($submissions_query);
$stmt->bind_param("ii", $activity_id, $activity['section_subject_id']);
$stmt->execute();
$submissions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grade Submissions - <?php echo htmlspecialchars($activity['title']); ?></title>
    <!-- Include your CSS files here -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/dashboard-shared.css">
</head>
<body>
    <?php include 'includes/navigation.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <!-- Activity Header -->
            <div class="content-header">
                <h1><?php echo htmlspecialchars($activity['title']); ?></h1>
                <p class="text-muted">
                    <?php echo htmlspecialchars($activity['section_name']); ?> | 
                    <?php echo htmlspecialchars($activity['subject_code']); ?> - 
                    <?php echo htmlspecialchars($activity['subject_name']); ?>
                </p>
            </div>

            <!-- Submissions List -->
            <div class="submissions-list">
                <?php foreach ($submissions as $submission): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">
                                <?php echo htmlspecialchars($submission['lastname'] . ', ' . $submission['firstname']); ?>
                            </h5>
                            <?php if ($submission['submission_id']): ?>
                                <p class="text-muted">
                                    Submitted: <?php echo date('M d, Y h:i A', strtotime($submission['submitted_at'])); ?>
                                </p>
                                <form class="grade-form" onsubmit="submitGrade(event, <?php echo $submission['submission_id']; ?>)" 
                                      data-submission-id="<?php echo $submission['submission_id']; ?>">
                                    <div class="form-group">
                                        <label>Points (max: <?php echo $activity['points']; ?>)</label>
                                        <input type="number" class="form-control" name="points" 
                                               value="<?php echo $submission['points']; ?>"
                                               min="0" max="<?php echo $activity['points']; ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Feedback</label>
                                        <textarea class="form-control" name="feedback"><?php echo $submission['feedback']; ?></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Save Grade</button>
                                </form>
                            <?php else: ?>
                                <p class="text-warning">No submission yet</p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
        async function submitGrade(event, submissionId) {
            event.preventDefault();
            const form = event.target;
            const data = {
                submission_id: submissionId,
                points: form.points.value,
                feedback: form.feedback.value
            };

            try {
                // Add loading state
                Swal.fire({
                    title: 'Saving...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const response = await fetch('grade_submissions.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data),
                    // Add these options to maintain session
                    credentials: 'same-origin',
                    cache: 'no-cache'
                });

                // Check if redirect occurred (session expired)
                if (response.redirected) {
                    window.location.href = response.url;
                    return;
                }

                const result = await response.json();
                
                if (result.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: result.message,
                        icon: 'success'
                    }).then(() => {
                        // Refresh only the specific submission card instead of full page
                        updateSubmissionCard(submissionId, data.points, data.feedback);
                    });
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                console.error('Grade submission error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: error.message || 'Failed to save grade',
                    icon: 'error'
                });
            }
        }

        // Function to update submission card without page reload
        function updateSubmissionCard(submissionId, points, feedback) {
            const card = document.querySelector(`form[data-submission-id="${submissionId}"]`).closest('.card');
            const pointsInput = card.querySelector('input[name="points"]');
            const feedbackTextarea = card.querySelector('textarea[name="feedback"]');
            
            pointsInput.value = points;
            feedbackTextarea.value = feedback;
            
            // Add visual feedback
            card.classList.add('border-success');
            setTimeout(() => {
                card.classList.remove('border-success');
            }, 2000);
        }
    </script>

    <style>
        .border-success {
            border: 2px solid #28a745 !important;
            transition: border-color 0.3s ease;
        }
        
        .card {
            transition: all 0.3s ease;
        }
        
        .grade-form {
            margin-top: 1rem;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
    </style>
</body>
</html>
