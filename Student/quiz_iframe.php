<?php
session_start();
require_once('../db/dbConnector.php');

// Add error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['id'])) {
    header("Location: Student-Login.php");
    exit();
}

$db = new DbConnector();
$student_id = $_SESSION['id'];
$quiz_id = isset($_GET['quiz_id']) ? intval($_GET['quiz_id']) : 0;

if (!$quiz_id) {
    die("Invalid quiz ID");
}

// Add this near the top of the file after session_start()
if (!isset($_SESSION['quiz_seed'])) {
    $_SESSION['quiz_seed'] = mt_rand();
}
mt_srand($_SESSION['quiz_seed']); // Set the random seed

// Fetch quiz details and questions
$query = "SELECT 
    a.*, 
    ss.section_id, 
    s.section_name, 
    sub.subject_name,
    t.firstname as teacher_firstname,
    t.lastname as teacher_lastname
    FROM activities a
    JOIN section_subjects ss ON a.section_subject_id = ss.id
    JOIN sections s ON ss.section_id = s.section_id
    JOIN subjects sub ON ss.subject_id = sub.id
    JOIN teacher t ON ss.teacher_id = t.teacher_id
    WHERE a.activity_id = ? AND a.type = 'quiz'";

try {
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $quiz_id);
    $stmt->execute();
    $quiz = $stmt->get_result()->fetch_assoc();

    if (!$quiz) {
        die("Quiz not found");
    }

    // Check if quiz is already submitted
    $check_submission = "SELECT * FROM student_activity_submissions 
                        WHERE student_id = ? AND activity_id = ?";
    $stmt = $db->prepare($check_submission);
    $stmt->bind_param("ii", $student_id, $quiz_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        die("Quiz already submitted");
    }

    // Fetch questions with error handling
    $questions_query = "SELECT 
        q.*,
        GROUP_CONCAT(
            CONCAT(c.choice_id, ':', c.choice_text, ':', c.is_correct)
            ORDER BY RAND()
            SEPARATOR '|'
        ) as choices
        FROM quiz_questions q
        LEFT JOIN question_choices c ON q.question_id = c.question_id
        WHERE q.quiz_id = ?
        GROUP BY q.question_id
        ORDER BY RAND()";

    $stmt = $db->prepare($questions_query);
    $stmt->bind_param("i", $quiz_id);
    $stmt->execute();
    $questions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    if (empty($questions)) {
        die("No questions found for this quiz");
    }

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

// Add this function at the top of the file
function scrambleChoices($choicesString, $questionId) {
    if (empty($choicesString)) return [];
    
    $choices = array_map(function($choice) {
        list($id, $text, $isCorrect) = explode(':', $choice);
        return [
            'choice_id' => $id,
            'choice_text' => $text,
            'is_correct' => $isCorrect
        ];
    }, explode('|', $choicesString));
    
    // Use the question ID and session seed for consistent scrambling
    $seed = $_SESSION['quiz_seed'] . $questionId;
    mt_srand($seed);
    shuffle($choices);
    
    return $choices;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($quiz['title']); ?> - CamerinoHub</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="../images/light-logo.png">
    <style>
        body {
            background-color: #f5f5f5;
        }
        .quiz-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        .quiz-header {
            background: white;
            border-radius: 8px;
            padding: 2rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .question-card {
            background: white;
            border-radius: 8px;
            padding: 2rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .timer-container {
            position: fixed;
            top: 1rem;
            right: 1rem;
            background: white;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        .choice-label {
            display: block;
            padding: 0.75rem;
            margin: 0.5rem 0;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .choice-label:hover {
            background-color: #f8f9fa;
        }
        input[type="radio"]:checked + .choice-label {
            background-color: #e3f2fd;
            border-color: #90caf9;
        }
        .question-number {
            color: #666;
            margin-bottom: 0.5rem;
        }
        .progress {
            height: 0.5rem;
        }
        .warning {
            color: #dc3545;
            display: none;
            margin-top: 0.5rem;
        }
        .score-modal {
            max-width: 500px;
        }

        .score-summary {
            padding: 1rem;
        }

        .score-summary .display-4 {
            font-size: 3.5rem;
            font-weight: bold;
            color: #2196F3;
        }

        .score-summary p {
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }

        .score-summary i {
            margin-right: 0.5rem;
        }

        .question-img {
            transition: all 0.3s ease;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            cursor: pointer;
        }

        .question-img:hover {
            opacity: 0.8;
            transform: scale(1.02);
        }

        #imagePreviewModal .modal-content {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            overflow: hidden;
        }

        #imagePreviewModal .modal-header {
            border-bottom: 1px solid #dee2e6;
            padding: 1rem;
        }

        #imagePreviewModal .modal-body {
            padding: 0;
            background-color: #f8f9fa;
        }

        #previewImage {
            max-height: 80vh;
            width: auto;
            object-fit: contain;
            margin: 0 auto;
            display: block;
        }

        .modal-backdrop.show {
            opacity: 0.9;
        }

        /* Add visual feedback for clickable images */
        .question-image {
            position: relative;
            display: inline-block;
        }

        .question-image:after {
            content: '🔍';
            position: absolute;
            right: 10px;
            top: 10px;
            background: rgba(255,255,255,0.9);
            padding: 5px;
            border-radius: 50%;
            font-size: 14px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <!-- Add debug information at the top -->
    <?php if (isset($_GET['debug'])): ?>
    <div class="debug-info" style="background: #f8f9fa; padding: 20px; margin: 20px;">
        <h3>Debug Information</h3>
        <pre><?php 
            echo "Quiz ID: " . $quiz_id . "\n";
            echo "Number of questions: " . count($questions) . "\n";
            echo "Quiz type: " . $quiz['type'] . "\n";
            echo "Student ID: " . $student_id . "\n";
        ?></pre>
    </div>
    <?php endif; ?>

    <!-- Add new warning modal that shows first -->
    <div class="modal fade" id="warningModal" data-backdrop="static" data-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">⚠️ Important Quiz Rules</h5>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <h6 class="font-weight-bold">Security Measures:</h6>
                        <ul class="mb-0">
                            <li>Full Screen Mode is Mandatory</li>
                            <li>No Tab Switching Allowed</li>
                            <li>No Minimizing Window</li>
                            <li>No Page Refresh Allowed</li>
                            <li>No Right-Click Actions Allowed</li>
                            <li>Cheating is Strictly Prohibited</li>
                        </ul>
                    </div>
                    <div class="alert alert-danger">
                        <strong>Warning:</strong> Violating any of these rules will result in automatic quiz submission with zero points!
                    </div>
                    <div class="text-center mt-3">
                        <button id="understandBtn" class="btn btn-primary" disabled>
                            <span class="spinner-border spinner-border-sm mr-2 d-none" role="status"></span>
                            Please wait... (<span id="countdown">5</span>)
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="timer-container">
        <div class="text-center">
            <h5>Time Remaining</h5>
            <div id="timer" class="h4 mb-0"></div>
        </div>
        <div class="progress mt-2">
            <div class="progress-bar" role="progressbar" style="width: 100%"></div>
        </div>
        <div id="tabWarning" class="warning">
            <i class="fas fa-exclamation-triangle"></i> Tab switching detected!
        </div>
    </div>

    <div class="quiz-container">
        <div class="quiz-header">
            <h2><?php echo htmlspecialchars($quiz['title']); ?></h2>
            <p class="text-muted mb-0">
                <?php echo htmlspecialchars($quiz['subject_name']); ?> | 
                <?php echo htmlspecialchars($quiz['section_name']); ?>
            </p>
            <p class="text-muted">
                Teacher: <?php echo htmlspecialchars($quiz['teacher_firstname'] . ' ' . $quiz['teacher_lastname']); ?>
            </p>
            <hr>
            <p><?php echo htmlspecialchars($quiz['description']); ?></p>
            <div class="d-flex justify-content-between align-items-center">
                <span>Points: <?php echo $quiz['points']; ?></span>
                <span>Due: <?php echo date('M j, Y g:i A', strtotime($quiz['due_date'])); ?></span>
            </div>
        </div>

        <form id="quizForm">
            <?php foreach ($questions as $index => $question): ?>
                <div class="question-card">
                    <div class="card-header">
                        <span class="question-number">Question <?php echo $index + 1; ?></span>
                        <span class="float-right text-muted">
                            Points: <?php echo $question['points']; ?>
                        </span>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($question['image_path'])): ?>
                        <div class="question-image mb-3">
                            <img src="../<?php echo htmlspecialchars($question['image_path']); ?>" 
                                 alt="Question Image" 
                                 class="img-fluid question-img" 
                                 style="max-height: 200px; cursor: pointer;"
                                 onclick="showImagePreview(this.src); return false;">
                            <small class="text-muted d-block mt-1">Click image to enlarge</small>
                        </div>
                        <?php endif; ?>

                        <h5 class="mb-4"><?php echo htmlspecialchars($question['question_text']); ?></h5>

                        <?php if ($question['question_type'] === 'multiple_choice'): ?>
                            <div class="form-group">
                                <?php 
                                $choices = scrambleChoices($question['choices'], $question['question_id']);
                                foreach ($choices as $choice): 
                                ?>
                                    <div class="custom-control custom-radio mb-2">
                                        <input type="radio" 
                                               id="choice_<?php echo $choice['choice_id']; ?>" 
                                               name="answer_<?php echo $question['question_id']; ?>" 
                                               value="<?php echo $choice['choice_id']; ?>" 
                                               class="custom-control-input"
                                               required>
                                        <label class="choice-label custom-control-label" 
                                               for="choice_<?php echo $choice['choice_id']; ?>">
                                            <?php echo htmlspecialchars($choice['choice_text']); ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php elseif ($question['question_type'] === 'true_false'): ?>
                            <div class="form-group">
                                <div class="custom-control custom-radio">
                                    <input type="radio" 
                                           id="true_<?php echo $question['question_id']; ?>" 
                                           name="answer_<?php echo $question['question_id']; ?>" 
                                           value="true" 
                                           class="custom-control-input" 
                                           required>
                                    <label class="custom-control-label" for="true_<?php echo $question['question_id']; ?>">
                                        True
                                    </label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" 
                                           id="false_<?php echo $question['question_id']; ?>" 
                                           name="answer_<?php echo $question['question_id']; ?>" 
                                           value="false" 
                                           class="custom-control-input" 
                                           required>
                                    <label class="custom-control-label" for="false_<?php echo $question['question_id']; ?>">
                                        False
                                    </label>
                                </div>
                            </div>
                        <?php elseif ($question['question_type'] === 'short_answer'): ?>
                            <div class="form-group">
                                <input type="text" 
                                       class="form-control" 
                                       name="answer_<?php echo $question['question_id']; ?>" 
                                       id="answer_<?php echo $question['question_id']; ?>"
                                       required 
                                       placeholder="Enter your answer">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="text-center mb-5">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-paper-plane mr-2"></i>Submit Quiz
                </button>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Move showImagePreview function outside document.ready
        function showImagePreview(src) {
            if (window.event) {
                window.event.preventDefault();
                window.event.stopPropagation();
            }
            
            const modal = $('#imagePreviewModal');
            const previewImg = $('#previewImage');
            
            previewImg.attr('src', src);
            modal.modal('show');
            
            // Prevent any clicks inside modal from triggering quiz events
            modal.off('click').on('click', function(e) {
                e.stopPropagation();
            });
        }

        $(document).ready(function() {
            let quizSubmitted = false;
            let rightClickWarningShown = false;
            
            // Prevent right-click
            document.addEventListener('contextmenu', function(e) {
                e.preventDefault();
                if (!quizSubmitted) {
                    if (!rightClickWarningShown) {
                        rightClickWarningShown = true;
                        Swal.fire({
                            icon: 'warning',
                            title: 'Warning!',
                            text: 'Right-clicking is not allowed. Next attempt will submit your quiz with zero points.',
                            confirmButtonText: 'I Understand'
                        });
                    } else {
                        submitQuiz(true, 'Right-click detected');
                    }
                }
            });

            // Prevent refresh using F5 key
            document.addEventListener('keydown', function(e) {
                if ((e.key === 'F5' || 
                    (e.ctrlKey && e.key === 'r') || 
                    (e.metaKey && e.key === 'r'))) {
                    e.preventDefault();
                    if (!quizSubmitted) {
                        submitQuiz(true, 'Page refresh attempted');
                    }
                    return false;
                }
            });

            // Handle page refresh or unload
            window.addEventListener('beforeunload', function(e) {
                if (!quizSubmitted) {
                    submitQuiz(true, 'Page refresh detected');
                    e.preventDefault();
                    e.returnValue = '';
                }
            });

            // Prevent browser back/forward
            history.pushState(null, null, location.href);
            window.onpopstate = function() {
                history.go(1);
                if (!quizSubmitted) {
                    submitQuiz(true, 'Browser navigation attempted');
                }
            };

            // Show warning modal immediately
            $('#warningModal').modal('show');

            // Handle countdown for understand button
            let countdown = 5;
            const countdownInterval = setInterval(() => {
                countdown--;
                $('#countdown').text(countdown);
                if (countdown <= 0) {
                    clearInterval(countdownInterval);
                    $('#understandBtn')
                        .html('I Understand')
                        .prop('disabled', false)
                        .removeClass('btn-secondary')
                        .addClass('btn-primary');
                }
            }, 1000);

            // Handle understand button click
            $('#understandBtn').click(function() {
                $(this).prop('disabled', true);
                $('.spinner-border').removeClass('d-none');
                
                setTimeout(() => {
                    $('#warningModal').modal('hide');
                    enableFullScreen();
                }, 1000);
            });

            // Fullscreen handling
            function enableFullScreen() {
                const elem = document.documentElement;
                if (elem.requestFullscreen) {
                    elem.requestFullscreen();
                } else if (elem.webkitRequestFullscreen) {
                    elem.webkitRequestFullscreen();
                } else if (elem.msRequestFullscreen) {
                    elem.msRequestFullscreen();
                }
            }

            // Handle tab visibility
            document.addEventListener('visibilitychange', function() {
                if (document.hidden && !quizSubmitted) {
                    submitQuiz(true, 'Tab switching detected');
                }
            });

            // Prevent minimizing (window blur)
            window.addEventListener('blur', function() {
                if (!quizSubmitted) {
                    submitQuiz(true, 'Window minimized');
                }
            });

            // Force fullscreen when exited
            document.addEventListener('fullscreenchange', function() {
                if (!document.fullscreenElement && !quizSubmitted) {
                    submitQuiz(true, 'Fullscreen mode exited');
                }
            });

            let duration = <?php echo $quiz['quiz_duration']; ?> * 60; // Convert to seconds
            let startTime = new Date();

            // Timer function
            function updateTimer() {
                const minutes = Math.floor(duration / 60);
                const seconds = duration % 60;
                $('#timer').text(`${minutes}:${seconds.toString().padStart(2, '0')}`);
                
                const progress = (duration / (<?php echo $quiz['quiz_duration']; ?> * 60)) * 100;
                $('.progress-bar').css('width', `${progress}%`);

                if (duration <= 300) { // Last 5 minutes
                    $('.progress-bar').removeClass('bg-primary').addClass('bg-danger');
                    $('#timer').addClass('text-danger');
                }

                if (duration <= 0) {
                    submitQuiz(true);
                }

                duration--;
            }

            const timer = setInterval(updateTimer, 1000);

            // Form submission
            $('#quizForm').on('submit', function(e) {
                e.preventDefault();
                if (quizSubmitted) return; // Prevent multiple submissions
                quizSubmitted = true;

                const answers = collectAnswers();
                console.log('Submitting answers:', answers);

                const submissionData = {
                    quiz_id: <?php echo $quiz_id; ?>,
                    answers: answers,
                    time_spent: Math.floor((Date.now() - startTime) / 1000),
                    security_violation: false,
                    violation_count: 0
                };

                console.log('Full submission data:', submissionData);

                // Show loading
                Swal.fire({
                    title: 'Submitting...',
                    text: 'Please wait while your answers are being submitted',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });

                submitQuizData(submissionData);
            });

            // Prevent accidental navigation
            $(window).on('beforeunload', function(e) {
                if (!quizSubmitted) {
                    return "Are you sure you want to leave? Your quiz progress will be lost.";
                }
            });

            // Update submitQuiz function
            function submitQuiz(forced = false, violationType = '') {
                if (quizSubmitted) return;
                quizSubmitted = true;

                if (forced) {
                    const formData = {
                        quiz_id: <?php echo $quiz_id; ?>,
                        answers: collectAnswers(),
                        time_spent: Math.floor((Date.now() - startTime) / 1000),
                        security_violation: true,
                        violation_type: violationType,
                        points: 0
                    };

                    Swal.fire({
                        icon: 'error',
                        title: 'Quiz Automatically Submitted',
                        text: violationType,
                        allowOutsideClick: false,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        submitQuizData(formData);
                    });
                }
            }

            function submitQuizData(formData) {
                $('#quizForm :input').prop('disabled', true);
                
                $.ajax({
                    url: 'handlers/submit_quiz.php',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(formData),
                    success: function(response) {
                        if (response.success) {
                            if (formData.security_violation) {
                                // Show security violation message
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Quiz Submitted with Violations',
                                    html: `
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            <strong>Important Notice:</strong>
                                            <p class="mb-0">Your quiz has been submitted with zero points due to a security violation.</p>
                                            <p class="mb-0">Please contact your teacher to request another attempt.</p>
                                        </div>
                                    `,
                                    allowOutsideClick: false,
                                    confirmButtonText: 'Return to Quizzes'
                                }).then(() => {
                                    document.exitFullscreen().then(() => {
                                        window.location.href = 'student_quizzes.php';
                                    }).catch(() => {
                                        window.location.href = 'student_quizzes.php';
                                    });
                                });
                            } else {
                                // Calculate percentage
                                const percentage = ((response.score / response.total_points) * 100).toFixed(1);
                                let resultIcon = 'success';
                                if (percentage < 50) resultIcon = 'error';
                                else if (percentage < 75) resultIcon = 'warning';

                                // Show simplified score modal without answer review
                                Swal.fire({
                                    icon: resultIcon,
                                    title: 'Quiz Completed!',
                                    html: `
                                        <div class="score-summary">
                                            <div class="mb-4">
                                                <h3 class="text-center mb-3">Your Score</h3>
                                                <div class="display-4 text-center mb-2">
                                                    ${response.score}/${response.total_points}
                                                </div>
                                                <div class="h5 text-center text-muted">
                                                    ${percentage}%
                                                </div>
                                            </div>
                                        </div>
                                    `,
                                    allowOutsideClick: false,
                                    confirmButtonText: 'Return to Quizzes',
                                    customClass: {
                                        popup: 'score-modal'
                                    }
                                }).then(() => {
                                    document.exitFullscreen().then(() => {
                                        window.location.href = 'student_quizzes.php';
                                    }).catch(() => {
                                        window.location.href = 'student_quizzes.php';
                                    });
                                });
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Submission Failed',
                                text: response.error || 'Failed to submit quiz'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to submit quiz. Please try again.'
                        });
                    }
                });
            }

            function collectAnswers() {
                const answers = {};
                
                // For short answer questions
                $('input[type="text"][name^="answer_"]').each(function() {
                    const questionId = this.name.replace('answer_', '');
                    answers[questionId] = $(this).val().trim(); // Trim whitespace
                    console.log('Short answer collected:', {
                        questionId: questionId,
                        answer: answers[questionId]
                    });
                });

                // For multiple choice and true/false questions
                $('input[type="radio"]:checked').each(function() {
                    const name = $(this).attr('name');
                    const questionId = name.replace('answer_', '');
                    answers[questionId] = $(this).val();
                });

                return answers;
            }

            // Modal event handlers
            const modal = $('#imagePreviewModal');
            
            // Prevent modal events from triggering quiz events
            modal.on('show.bs.modal', function(e) {
                e.stopPropagation();
            });
            
            modal.on('shown.bs.modal', function(e) {
                e.stopPropagation();
            });
            
            modal.on('hide.bs.modal', function(e) {
                e.stopPropagation();
            });
            
            modal.on('hidden.bs.modal', function(e) {
                e.stopPropagation();
            });
            
            // Prevent clicks inside modal from bubbling up
            modal.find('.modal-content').on('click', function(e) {
                e.stopPropagation();
            });
            
            // Prevent close button from triggering quiz events
            modal.find('.close').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                modal.modal('hide');
            });

            // Initialize hover effect for images
            $('.question-img').hover(
                function() { $(this).css('opacity', '0.8'); },
                function() { $(this).css('opacity', '1'); }
            );

            // Prevent text selection
            document.body.style.userSelect = 'none';
            document.body.style.webkitUserSelect = 'none';
            document.body.style.msUserSelect = 'none';
            document.body.style.mozUserSelect = 'none';
            
            // Prevent copy/paste
            $(document).on('copy paste', function(e) {
                e.preventDefault();
                return false;
            });
            
            // Prevent keyboard shortcuts
            $(document).on('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && 
                    (e.key === 'c' || e.key === 'v' || e.key === 'u' || 
                     e.key === 'i' || e.key === 's')) {
                    e.preventDefault();
                    return false;
                }
            });
        });
    </script>

    <!-- Update the modal HTML -->
    <div class="modal fade" id="imagePreviewModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" onclick="event.stopPropagation();">
                <div class="modal-header">
                    <h5 class="modal-title">Image Preview</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="event.stopPropagation();">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center p-0" onclick="event.stopPropagation();">
                    <img src="" id="previewImage" class="img-fluid" alt="Question Image" style="max-width: 100%;" onclick="event.stopPropagation();">
                </div>
            </div>
        </div>
    </div>
</body>
</html>
