<?php
session_start();
require_once('../db/dbConnector.php');

// Check both session and activity ID
if (!isset($_SESSION['teacher_id'])) {
    header("Location: Teacher-Login.php");
    exit();
}

// Check if activity_id exists in GET parameters
if (!isset($_GET['activity_id'])) {
    header("Location: manage_activities.php");
    exit();
}

$db = new DbConnector();
$activity_id = intval($_GET['activity_id']); // Use activity_id instead of id
$teacher_id = $_SESSION['teacher_id'];

// Verify teacher owns this activity
$verify_query = "
    SELECT a.*, sec.section_name, sub.subject_name, sub.subject_code
    FROM activities a
    JOIN section_subjects ss ON a.section_subject_id = ss.id
    JOIN sections sec ON ss.section_id = sec.section_id
    JOIN subjects sub ON ss.subject_id = sub.id
    WHERE a.activity_id = ? AND ss.teacher_id = ?";

$stmt = $db->prepare($verify_query);
$stmt->bind_param("ii", $activity_id, $teacher_id);
$stmt->execute();
$activity = $stmt->get_result()->fetch_assoc();

// If activity doesn't exist or doesn't belong to teacher, redirect
if (!$activity) {
    header("Location: manage_activities.php");
    exit();
}

// Fetch all submissions
$submissions_query = "
    SELECT 
        s.student_id,
        s.firstname,
        s.lastname,
        sas.submission_id,
        sas.submitted_at,
        sas.points,
        sas.feedback,
        sas.security_violation,
        sas.status,
        sas.remarks,
        sas.time_spent,
        COUNT(sa.answer_id) as total_answers,
        SUM(sa.is_correct) as correct_answers
    FROM student_sections ss
    JOIN student s ON ss.student_id = s.student_id
    LEFT JOIN student_activity_submissions sas 
        ON s.student_id = sas.student_id 
        AND sas.activity_id = ?
    LEFT JOIN student_answers sa
        ON sas.student_id = sa.student_id
        AND sa.quiz_id = ?
    WHERE ss.section_id = (
        SELECT section_id 
        FROM section_subjects 
        WHERE id = ?)
    GROUP BY s.student_id";

$stmt = $db->prepare($submissions_query);
$stmt->bind_param("iii", $activity_id, $activity_id, $activity['section_subject_id']);
$stmt->execute();
$submissions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

function getScoreBadgeClass($score, $total) {
    if (!$score) return 'secondary';
    $percentage = ($score / $total) * 100;
    if ($percentage >= 90) return 'success';
    if ($percentage >= 75) return 'primary';
    if ($percentage >= 60) return 'warning';
    return 'danger';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Submissions - <?php echo htmlspecialchars($activity['title']); ?></title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/dashboard-shared.css">
	<link rel="icon" href="../images/light-logo.png">
    <style>
        .content-header {
            padding: 1.5rem;
            background: white;
            border-bottom: 1px solid #e0e0e0;
            margin-bottom: 2rem;
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            padding: 1.5rem;
            border-radius: 10px;
            color: white;
        }

        .stat-card h5 {
            font-size: 1rem;
            margin-bottom: 0.5rem;
            opacity: 0.9;
        }

        .stat-card h2 {
            font-size: 2rem;
            margin: 0;
            font-weight: 600;
        }

        .total-students { background: #007bff; }
        .submitted { background: #28a745; }
        .pending { background: #ffc107; color: #000; }
        .graded { background: #17a2b8; }

        .submission-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 1rem;
            border: 1px solid #e0e0e0;
            position: relative;
        }

        .student-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .student-avatar {
            width: 40px;
            height: 40px;
            background: #e9ecef;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #495057;
        }

        .grade-section {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
        }

        .grade-input {
            width: 100px !important;
            display: inline-block;
            margin-right: 1rem;
        }

        .feedback-form {
            display: none;
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            margin-top: 1rem;
            border: 1px solid #dee2e6;
        }

        .feedback-form.active {
            display: block;
        }

        .submission-files .btn-group {
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .back-button {
            padding: 0.5rem 1rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .status-badge {
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.875rem;
        }

        .badge-danger {
            background-color: #dc3545;
            color: white;
        }
        
        .badge-warning {
            background-color: #ffc107;
            color: #000;
        }
        
        .alert-warning {
            background-color: #fff3cd;
            border-color: #ffeeba;
            color: #856404;
        }

        @media (max-width: 768px) {
            .stats-container {
                grid-template-columns: repeat(2, 1fr); /* Show 2 cards per row on mobile */
                gap: 0.5rem;
                padding: 0 10px;
            }

            .stat-card {
                padding: 1rem;
            }

            .stat-card h2 {
                font-size: 1.5rem;
            }

            .content-header {
                padding: 1rem;
            }

            .content-header h1 {
                font-size: 1.5rem;
            }

            .submission-card {
                margin: 0.5rem 10px;
            }

            .student-info {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }

            .student-info .ml-auto {
                margin-left: 0 !important;
                width: 100%;
                margin-top: 0.5rem;
            }

            .student-info .btn-group {
                display: flex;
                width: 100%;
            }

            .submission-files .btn-group {
                display: flex;
                width: 100%;
                margin-bottom: 0.5rem;
            }

            .submission-files .btn-group .btn {
                flex: 1;
            }

            .feedback-form {
                padding: 1rem;
            }

            /* Adjust the back button */
            .content-header .d-flex {
                flex-direction: column;
                gap: 1rem;
            }

            .content-header .btn-outline-primary {
                width: 100%;
                margin-top: 0.5rem;
            }

            /* Make buttons full width on mobile */
            .btn-sm {
                width: 100%;
                margin: 0.25rem 0;
            }

            /* Adjust badge positioning */
            .badge {
                display: inline-block;
                margin-bottom: 0.5rem;
                width: 100%;
                text-align: center;
            }
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
                        <p class="text-muted">
                            <?php echo htmlspecialchars($activity['section_name']); ?> | 
                            <?php echo htmlspecialchars($activity['subject_code']); ?> - 
                            <?php echo htmlspecialchars($activity['subject_name']); ?>
                        </p>
                    </div>
                    <a href="manage_activities.php" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left"></i> Back to Activities
                    </a>
                </div>
            </div>

            <div class="content-body">
                <div class="stats-container">
                    <div class="stat-card total-students">
                        <h5>Total Students</h5>
                        <h2><?php echo count($submissions); ?></h2>
                    </div>
                    <div class="stat-card submitted">
                        <h5>Submitted</h5>
                        <h2><?php echo count(array_filter($submissions, function($s) { return $s['submission_id'] != null; })); ?></h2>
                    </div>
                    <div class="stat-card pending">
                        <h5>Pending</h5>
                        <h2><?php echo count(array_filter($submissions, function($s) { return $s['submission_id'] == null; })); ?></h2>
                    </div>
                    <div class="stat-card graded">
                        <h5>Graded</h5>
                        <h2><?php echo count(array_filter($submissions, function($s) { return $s['points'] !== null; })); ?></h2>
                    </div>
                </div>

                <div class="submissions-container">
                    <?php foreach ($submissions as $submission): ?>
                        <div class="submission-card">
                            <div class="card-body">
                                <div class="student-info">
                                    <div class="student-avatar">
                                        <?php echo strtoupper(substr($submission['firstname'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <h5 class="mb-0"><?php echo htmlspecialchars($submission['firstname'] . ' ' . $submission['lastname']); ?></h5>
                                        <?php if ($submission['submitted_at']): ?>
                                            <small class="text-muted">
                                                Submitted: <?php echo date('M j, Y g:i A', strtotime($submission['submitted_at'])); ?>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <?php if ($submission['submitted_at']): ?>
                                    <div class="mt-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-0">
                                                    Score: 
                                                    <span class="badge badge-<?php echo getScoreBadgeClass($submission['points'], $activity['points']); ?>">
                                                        <?php 
                                                        // Get total questions count
                                                        $stmt = $db->prepare("SELECT COUNT(*) as total FROM quiz_questions WHERE quiz_id = ?");
                                                        $stmt->bind_param("i", $activity_id);
                                                        $stmt->execute();
                                                        $result = $stmt->get_result();
                                                        $total_questions = $result->fetch_assoc()['total'];
                                                        
                                                        // Display the score based on correct answers out of total questions
                                                        $score = $submission['correct_answers'];
                                                        $percentage = min(($score / $total_questions) * 100, 100); // Cap at 100%
                                                        ?>
                                                        <?php echo $score; ?> / <?php echo $total_questions; ?>
                                                        (<?php echo round($percentage); ?>%)
                                                    </span>
                                                    <?php if ($submission['correct_answers']): ?>
                                                        <small class="text-muted ml-2">
                                                            (<?php echo $submission['correct_answers']; ?> of <?php echo $total_questions; ?> correct)
                                                        </small>
                                                    <?php endif; ?>
                                                </h6>
                                                <?php if ($submission['time_spent']): ?>
                                                    <small class="text-muted">
                                                        Time spent: <?php echo floor($submission['time_spent'] / 60); ?> minutes
                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <button type="button" 
                                                        class="btn btn-info btn-sm mr-2" 
                                                        onclick="viewAnswers(<?php echo $submission['student_id']; ?>, <?php echo $activity_id; ?>)">
                                                    <i class="fas fa-eye mr-1"></i>View Answers
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-warning btn-sm" 
                                                        onclick="resetAttempt(<?php echo $submission['student_id']; ?>, <?php echo $activity_id; ?>)">
                                                    <i class="fas fa-redo mr-1"></i>Reset Attempt
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <?php if ($submission['security_violation']): ?>
                                        <div class="alert alert-warning mt-2">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            Security violation detected during quiz
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($submission['remarks'])): ?>
                                        <div class="alert alert-info mt-2">
                                            <small><strong>Remarks:</strong> <?php echo htmlspecialchars($submission['remarks']); ?></small>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="alert alert-secondary mt-3">
                                        <i class="fas fa-info-circle mr-1"></i>No submission yet
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <script>
        function toggleFeedback(submissionId) {
            $('.feedback-form').removeClass('active'); // Hide all other forms
            $(`#feedback-${submissionId}`).addClass('active');
        }

        async function submitGrade(event, submissionId) {
            event.preventDefault();
            const form = event.target;
            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

            try {
                const response = await fetch('handlers/grade_submission.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        submission_id: submissionId,
                        points: parseFloat(form.points.value),
                        feedback: form.feedback.value
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Grade saved successfully',
                        icon: 'success'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    throw new Error(data.message || 'Failed to save grade');
                }
            } catch (error) {
                Swal.fire({
                    title: 'Error!',
                    text: error.message || 'Failed to save grade',
                    icon: 'error'
                });
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Save Grade';
            }
        }

        // Add download all submissions functionality
        function downloadAllSubmissions(activityId) {
            window.location.href = `handlers/download_submissions.php?activity_id=${activityId}`;
        }

        // Preview file before downloading
        function previewFile(filePath) {
            const fileExt = filePath.split('.').pop().toLowerCase();
            const isPDF = fileExt === 'pdf';
            
            if (isPDF) {
                window.open(filePath, '_blank');
            } else {
                Swal.fire({
                    title: 'File Preview',
                    html: `
                        <div class="file-preview">
                            <i class="fas fa-file fa-3x mb-3"></i>
                            <p>${filePath.split('/').pop()}</p>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Download',
                    cancelButtonText: 'Close'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = filePath;
                    }
                });
            }
        }

        function viewSubmission(filePath, fileType) {
            // Create the full path to the file
            const viewerPath = 'handlers/view_submission.php?file=' + encodeURIComponent(filePath);
            
            if (fileType === 'pdf') {
                // Open PDF in a new window
                const newWindow = window.open(viewerPath, '_blank');
                
                // Check if the window was blocked
                if (newWindow === null) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Please allow pop-ups to view PDF files',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            } else {
                // Show preview modal for other file types
                Swal.fire({
                    title: 'File Preview',
                    html: `
                        <div class="file-preview">
                            <i class="fas fa-file-${getFileIcon(fileType)} fa-3x mb-3"></i>
                            <p class="mb-2">${filePath.split('/').pop()}</p>
                            <small class="text-muted">Click Download to view this file type</small>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Download',
                    cancelButtonText: 'Close'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Try to download the file
                        fetch(viewerPath)
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('File not found or access denied');
                                }
                                return response.blob();
                            })
                            .then(blob => {
                                const url = window.URL.createObjectURL(blob);
                                const a = document.createElement('a');
                                a.href = url;
                                a.download = filePath.split('/').pop();
                                document.body.appendChild(a);
                                a.click();
                                window.URL.revokeObjectURL(url);
                                document.body.removeChild(a);
                            })
                            .catch(error => {
                                Swal.fire({
                                    title: 'Error!',
                                    text: error.message,
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            });
                    }
                });
            }
        }

        function getFileIcon(fileType) {
            switch(fileType) {
                case 'pdf': return 'pdf';
                case 'doc':
                case 'docx': return 'word';
                case 'zip': return 'archive';
                default: return 'alt';
            }
        }

        async function resetAttempt(studentId, quizId) {
            try {
                const result = await Swal.fire({
                    title: 'Reset Quiz Attempt?',
                    html: 'This will:<br>' +
                          '- Delete the student\'s current submission<br>' +
                          '- Allow the student to retake the quiz<br>' +
                          '- Reset their score<br><br>' +
                          'Are you sure you want to continue?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ffc107',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, reset attempt',
                    cancelButtonText: 'Cancel'
                });

                if (result.isConfirmed) {
                    const response = await fetch('handlers/reset_quiz_attempt.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            student_id: studentId,
                            quiz_id: quizId
                        })
                    });

                    const data = await response.json();
                    
                    if (data.success) {
                        Swal.fire({
                            title: 'Reset Successful',
                            text: 'The student can now retake the quiz',
                            icon: 'success'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        throw new Error(data.message || 'Failed to reset attempt');
                    }
                }
            } catch (error) {
                Swal.fire({
                    title: 'Error!',
                    text: error.message || 'Failed to reset attempt',
                    icon: 'error'
                });
            }
        }

        function formatStudentAnswer(answer) {
            // If the answer is null or undefined, return 'No answer'
            if (answer === null || answer === undefined) {
                return 'No answer';
            }

            // For short answer questions, ensure we get the text_answer
            if (answer.question_type === 'short_answer') {
                return answer.text_answer || answer.selected_answer || answer.student_answer || 'No answer';
            }

            // For multiple choice and true/false, return the student_answer
            return answer.student_answer || 'No answer';
        }

        function viewAnswers(studentId, quizId) {
            $('#viewAnswersModal').modal('show');
            $('#answersContainer').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading answers...</div>');

            $.ajax({
                url: 'handlers/get_student_answers.php',
                type: 'GET',
                data: { student_id: studentId, quiz_id: quizId },
                success: function(response) {
                    console.log('Raw response:', response);
                    let html = '';
                    
                    if (typeof response === 'string') {
                        response = JSON.parse(response);
                    }
                    
                    if (response.success) {
                        response.answers.forEach((answer, index) => {
                            const isCorrect = answer.is_correct == 1;
                            const studentAnswer = answer.student_answer || 'No answer';
                            const correctAnswer = answer.correct_answer || 'No answer provided';

                            html += `
                                <div class="card mb-3">
                                    <div class="card-header d-flex justify-content-between align-items-center ${isCorrect ? 'bg-success text-white' : 'bg-danger text-white'}">
                                        <span>Question ${index + 1} (${answer.question_type})</span>
                                        <span class="badge badge-${isCorrect ? 'light' : 'light'}">
                                            ${isCorrect ? 'Correct' : 'Incorrect'}
                                        </span>
                                    </div>
                                    <div class="card-body">
                                        <p class="font-weight-bold mb-3">${answer.question_text}</p>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="p-3 bg-light rounded">
                                                    <h6 class="text-muted mb-2">Student's Answer:</h6>
                                                    <p class="mb-0">${studentAnswer}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="p-3 bg-light rounded">
                                                    <h6 class="text-muted mb-2">Correct Answer:</h6>
                                                    <p class="mb-0">${correctAnswer}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                    } else {
                        html = '<div class="alert alert-danger">Failed to load answers</div>';
                    }
                    $('#answersContainer').html(html);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching answers:', error);
                    $('#answersContainer').html('<div class="alert alert-danger">Failed to load answers</div>');
                }
            });
        }
    </script>

    <!-- View Answers Modal -->
    <div class="modal fade" id="viewAnswersModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Student Answers</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div id="answersContainer">
                        <!-- Answers will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
