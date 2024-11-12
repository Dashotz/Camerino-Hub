<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['id'])) {
    header("Location: Student-Login.php");
    exit();
}

$db = new DbConnector();
$quiz_id = $_GET['quiz_id'] ?? null;

if (!$quiz_id) {
    die("Quiz ID is required.");
}

// Fetch quiz details
$query = "
    SELECT 
        activity_id,
        title,
        quiz_link
    FROM activities
    WHERE activity_id = ? AND type = 'quiz'";

$stmt = $db->prepare($query);
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$result = $stmt->get_result();
$quiz = $result->fetch_assoc();

if (!$quiz || empty($quiz['quiz_link'])) {
    die("Quiz not found or invalid.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($quiz['title']); ?></title>
    <style>
        body, html {
            margin: 0;
            padding: 20px;
            overflow: hidden;
            background-color: #f5f5f5;
        }
        .quiz-container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .quiz-header {
            margin-bottom: 20px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .quiz-footer {
            padding: 15px;
            background: #f8f9fa;
            border-top: 1px solid #ddd;
            text-align: right;
            position: fixed;
            bottom: 0;
            width: 100%;
            left: 0;
        }
        .btn-finish {
            background: #28a745;
            color: white;
            padding: 10px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn-finish:hover {
            background: #218838;
        }
        iframe {
            width: 100%;
            height: calc(80vh - 60px);
            border: none;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="quiz-container">
        <div class="quiz-header">
            <h2><?php echo htmlspecialchars($quiz['title']); ?></h2>
        </div>

        <iframe id="quiz-frame" 
                src="<?php echo htmlspecialchars($quiz['quiz_link'], ENT_QUOTES, 'UTF-8'); ?>" 
                frameborder="0" 
                allowfullscreen>
        </iframe>

        <div class="quiz-footer">
            <button class="btn-finish" onclick="finishQuiz()">
                <i class="fas fa-check-circle"></i> Finish Quiz
            </button>
        </div>
    </div>

    <!-- Add SweetAlert2 for confirmations -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        function finishQuiz() {
            Swal.fire({
                title: 'Finish Quiz?',
                text: 'Are you sure you want to submit your quiz? This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#dc3545',
                confirmButtonText: 'Yes, submit it!',
                cancelButtonText: 'No, continue quiz'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitQuiz();
                }
            });
        }

        async function submitQuiz() {
            try {
                const response = await fetch('submit_quiz.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        quiz_id: <?php echo $quiz_id; ?>,
                        submitted_at: new Date().toISOString()
                    })
                });

                if (response.ok) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Your quiz has been submitted successfully.',
                        icon: 'success',
                        confirmButtonColor: '#28a745'
                    }).then(() => {
                        window.location.href = 'student_quizzes.php';
                    });
                } else {
                    throw new Error('Failed to submit quiz');
                }
            } catch (error) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to submit quiz. Please try again.',
                    icon: 'error',
                    confirmButtonColor: '#dc3545'
                });
            }
        }
    </script>
</body>
</html>
