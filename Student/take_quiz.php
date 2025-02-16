<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['student_id']) || !isset($_GET['id'])) {
    header("Location: view_quizzes.php");
    exit();
}

$db = new DbConnector();
$student_id = $_SESSION['student_id'];
$quiz_id = $_GET['id'];

// Fetch quiz details
$query = "SELECT a.*, ss.section_id, s.section_name, sub.subject_name
          FROM activities a
          JOIN section_subjects ss ON a.section_subject_id = ss.id
          JOIN sections s ON ss.section_id = s.section_id
          JOIN subjects sub ON ss.subject_id = sub.id
          WHERE a.activity_id = ? AND a.type = 'quiz'";

$stmt = $db->prepare($query);
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$quiz = $stmt->get_result()->fetch_assoc();

// Verify student access and quiz availability
if (!$quiz || strtotime($quiz['due_date']) < time()) {
    $_SESSION['error'] = "Quiz is not available or has expired.";
    header("Location: view_quizzes.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Quiz - <?php echo htmlspecialchars($quiz['title']); ?></title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
	<link rel="icon" href="../images/light-logo.png">
</head>
<body>
    <div class="container mt-4">
        <div class="card">
            <div class="card-body">
                <h3><?php echo htmlspecialchars($quiz['title']); ?></h3>
                <p><?php echo htmlspecialchars($quiz['description']); ?></p>
                <hr>
                <div class="quiz-container">
                    <iframe 
                        src="<?php echo htmlspecialchars($quiz['quiz_link']); ?>"
                        width="100%" 
                        height="800px" 
                        frameborder="0" 
                        marginheight="0" 
                        marginwidth="0">
                        Loading...
                    </iframe>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        // Add timer and security features here
        $(document).ready(function() {
            let duration = <?php echo $quiz['quiz_duration']; ?> * 60; // Convert to seconds
            
            // Timer countdown
            const timer = setInterval(function() {
                duration--;
                if (duration <= 0) {
                    clearInterval(timer);
                    window.location.href = 'view_quizzes.php';
                }
            }, 1000);

            <?php if ($quiz['prevent_tab_switch']): ?>
            // Prevent tab switching
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    // Log security violation
                    $.post('handlers/log_security_violation.php', {
                        quiz_id: <?php echo $quiz_id; ?>,
                        violation_type: 'tab_switch'
                    });
                }
            });
            <?php endif; ?>

            <?php if ($quiz['fullscreen_required']): ?>
            // Require fullscreen
            document.documentElement.requestFullscreen();
            document.addEventListener('fullscreenchange', function() {
                if (!document.fullscreenElement) {
                    // Log security violation
                    $.post('handlers/log_security_violation.php', {
                        quiz_id: <?php echo $quiz_id; ?>,
                        violation_type: 'fullscreen_exit'
                    });
                }
            });
            <?php endif; ?>
        });
    </script>
</body>
</html>
