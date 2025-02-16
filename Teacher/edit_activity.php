<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['teacher_id'])) {
    header("Location: Teacher-Login.php");
    exit();
}

$db = new DbConnector();
$activity_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$type = isset($_GET['type']) ? $_GET['type'] : '';

// Fetch activity details
$query = "SELECT a.*, ss.section_id, ss.subject_id, s.section_name, sub.subject_name 
          FROM activities a
          JOIN section_subjects ss ON a.section_subject_id = ss.id
          JOIN sections s ON ss.section_id = s.section_id
          JOIN subjects sub ON ss.subject_id = sub.id
          WHERE a.activity_id = ? AND ss.teacher_id = ?";

$stmt = $db->prepare($query);
$stmt->bind_param("ii", $activity_id, $_SESSION['teacher_id']);
$stmt->execute();
$activity = $stmt->get_result()->fetch_assoc();

if (!$activity) {
    header("Location: manage_activities.php");
    exit();
}

// Fetch quiz questions if it's a quiz
$questions = [];
if ($activity['type'] === 'quiz') {
    $questions_query = "SELECT 
        q.*,
        GROUP_CONCAT(
            DISTINCT CONCAT(
                c.choice_id, ':', 
                c.choice_text, ':', 
                c.is_correct, ':', 
                c.choice_order
            ) ORDER BY c.choice_order ASC SEPARATOR '|'
        ) as choices,
        qa.answer_text as correct_answer
        FROM quiz_questions q
        LEFT JOIN question_choices c ON q.question_id = c.question_id
        LEFT JOIN quiz_answers qa ON q.question_id = qa.question_id
        WHERE q.quiz_id = ?
        GROUP BY q.question_id
        ORDER BY q.question_order";
    
    $stmt = $db->prepare($questions_query);
    $stmt->bind_param("i", $activity_id);
    $stmt->execute();
    $questions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Fetch activity files if it's an activity or assignment
$files = [];
if ($activity['type'] === 'activity' || $activity['type'] === 'assignment') {
    $files_query = "SELECT * FROM activity_files WHERE activity_id = ?";
    $stmt = $db->prepare($files_query);
    $stmt->bind_param("i", $activity_id);
    $stmt->execute();
    $files = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Fetch sections for dropdown
$sections_query = "SELECT ss.id as section_subject_id, s.section_name, sub.subject_name
                  FROM section_subjects ss
                  JOIN sections s ON ss.section_id = s.section_id
                  JOIN subjects sub ON ss.subject_id = sub.id
                  WHERE ss.teacher_id = ? AND ss.status = 'active'
                  ORDER BY s.section_name";

$stmt = $db->prepare($sections_query);
$stmt->bind_param("i", $_SESSION['teacher_id']);
$stmt->execute();
$sections = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit <?php echo ucfirst($activity['type']); ?> - CamerinoHub</title>
    
    <!-- Load jQuery first -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    
    <!-- Then Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <!-- Then SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- CSS files -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/dashboard-shared.css">
    <?php if ($activity['type'] === 'quiz'): ?>
    <link rel="stylesheet" href="css/create-quiz.css">
    <?php endif; ?>
    <style>
    .question-image {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 8px;
        text-align: center;
    }

    .question-image img {
        max-width: 100%;
        height: auto;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .card-body {
        position: relative;
    }

    .remove-question {
        position: absolute;
        top: 1rem;
        right: 1rem;
    }

    .question-card {
        border: 1px solid #dee2e6;
        margin-bottom: 1rem;
    }

    .question-card .card-header {
        background-color: #f8f9fa;
        padding: 0.75rem 1rem;
    }

    .choices-list {
        margin-top: 1rem;
    }

    .choice-item {
        padding: 0.5rem;
        margin-bottom: 0.25rem;
        border-radius: 4px;
        background-color: #f8f9fa;
    }

    .choice-item.correct {
        background-color: #d4edda;
    }

    .correct-answer-display {
        padding: 0.5rem;
        background-color: #d4edda;
        border-radius: 4px;
        margin-top: 1rem;
    }

    .true-false-answer {
        padding: 0.5rem;
        background-color: #f8f9fa;
        border-radius: 4px;
        margin-top: 1rem;
    }
    </style>
</head>
<body>
    <?php include 'includes/navigation.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="content-header">
                <h1>Edit <?php echo ucfirst($activity['type']); ?></h1>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <form id="editActivityForm">
                        <input type="hidden" name="activity_id" value="<?php echo $activity_id; ?>">
                        <input type="hidden" name="type" value="<?php echo $activity['type']; ?>">

                        <!-- Common Fields for All Types -->
                        <div class="section mb-4">
                            <h4 class="mb-3"><?php echo ucfirst($activity['type']); ?> Details</h4>
                            <div class="form-group">
                                <label><i class="fas fa-chalkboard-teacher mr-2"></i>Section & Subject</label>
                                <select name="section_subject_id" class="form-control" required>
                                    <?php foreach ($sections as $section): ?>
                                        <option value="<?php echo $section['section_subject_id']; ?>" 
                                                <?php echo ($section['section_subject_id'] == $activity['section_subject_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($section['section_name'] . ' - ' . $section['subject_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label><i class="fas fa-heading mr-2"></i>Title</label>
                                <input type="text" name="title" class="form-control" 
                                       value="<?php echo htmlspecialchars($activity['title']); ?>" required>
                            </div>

                            <div class="form-group">
                                <label><i class="fas fa-align-left mr-2"></i>Description</label>
                                <textarea name="description" class="form-control" rows="3" required><?php 
                                    echo htmlspecialchars($activity['description']); 
                                ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-calendar-alt mr-2"></i>Due Date</label>
                                        <input type="datetime-local" name="due_date" class="form-control" 
                                               value="<?php echo date('Y-m-d\TH:i', strtotime($activity['due_date'])); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-star mr-2"></i>Points</label>
                                        <input type="number" name="points" class="form-control" 
                                               value="<?php echo count($questions); ?>" readonly>
                                        <small class="form-text text-muted">Points are automatically calculated based on the number of questions.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if ($activity['type'] === 'quiz'): ?>
                            <!-- Quiz-specific content -->
                            <?php include 'includes/edit_quiz_content.php'; ?>
                        <?php endif; ?>

                        <?php if ($activity['type'] === 'activity' || $activity['type'] === 'assignment'): ?>
                            <!-- Activity/Assignment-specific content -->
                            <div class="section mb-4">
                                <h4 class="mb-3">Current Files</h4>
                                <div class="list-group mb-3">
                                    <?php foreach ($files as $file): ?>
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><?php echo htmlspecialchars($file['file_name']); ?></span>
                                            <button type="button" class="btn btn-danger btn-sm delete-file" 
                                                    data-file-id="<?php echo $file['file_id']; ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <div class="form-group">
                                    <label>Add New Files</label>
                                    <input type="file" name="files[]" class="form-control-file" multiple>
                                    <small class="text-muted">You can select multiple files</small>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="form-group mt-4">
                            <button type="button" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-save mr-2"></i>Save Changes
                            </button>
                            <a href="manage_activities.php" class="btn btn-secondary">
                                <i class="fas fa-times mr-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div> 
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Keep only the activity edit form handler -->
    <script>
    $(document).ready(function() {
        // Initialize submit button handler
        $('#submitBtn').on('click', function(e) {
            e.preventDefault();
            submitForm();
        });

        // Add Question button handler
        $('#addQuestionBtn').on('click', function() {
            addNewQuestion();
        });
    });

    function submitForm() {
        const formData = new FormData($('#editActivityForm')[0]);
        
        // Add quiz-specific data
        if ('<?php echo $activity['type']; ?>' === 'quiz') {
            // Add only quiz settings, not questions
            formData.append('quiz_duration', $('input[name="quiz_duration"]').val());
            formData.append('prevent_tab_switch', $('#preventTabSwitch').is(':checked') ? 1 : 0);
            formData.append('fullscreen_required', $('#fullscreenRequired').is(':checked') ? 1 : 0);
        }

        // Show loading state
        Swal.fire({
            title: 'Saving...',
            text: 'Please wait while we save your changes',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: 'handlers/update_quiz.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Quiz updated successfully!',
                        showConfirmButton: true
                    }).then(() => {
                        // Stay on the same page to preserve questions
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.error || 'Failed to update quiz',
                        showConfirmButton: true
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to update quiz. Please try again.',
                    showConfirmButton: true
                });
            }
        });
    }

    function addNewQuestion() {
        // Show question type selection dialog first
        Swal.fire({
            title: 'Select Question Type',
            input: 'select',
            inputOptions: {
                'multiple_choice': 'Multiple Choice',
                'true_false': 'True/False',
                'short_answer': 'Short Answer'
            },
            inputPlaceholder: 'Select question type',
            showCancelButton: true,
            confirmButtonText: 'Add',
            showLoaderOnConfirm: true,
            preConfirm: (questionType) => {
                return $.ajax({
                    url: 'handlers/add_question.php',
                    type: 'POST',
                    data: {
                        quiz_id: <?php echo $activity_id; ?>,
                        question_type: questionType,
                        question_text: 'New Question',
                        question_order: $('.question-card').length + 1
                    }
                }).then(response => {
                    if (!response.success) {
                        throw new Error(response.error || 'Failed to add question');
                    }
                    return response;
                }).catch(error => {
                    Swal.showValidationMessage(`Request failed: ${error}`);
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                location.reload();
            }
        });
    }
    </script>

    <!-- Add this modal for editing questions -->
    <div class="modal fade" id="editQuestionModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Question</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Question Text</label>
                        <textarea class="form-control" id="editQuestionText" rows="3" required></textarea>
                    </div>

                    <div class="form-group">
                        <label>Question Type</label>
                        <select class="form-control" id="editQuestionType" onchange="handleQuestionTypeChange(this.value)">
                            <option value="multiple_choice">Multiple Choice</option>
                            <option value="true_false">True/False</option>
                            <option value="short_answer">Short Answer</option>
                        </select>
                    </div>

                    <!-- Multiple Choice Options -->
                    <div id="multipleChoiceOptions" class="question-options">
                        <div class="form-group">
                            <label>Choices</label>
                            <div class="choice-inputs">
                                <!-- Choices will be added here -->
                            </div>
                            <button type="button" class="btn btn-sm btn-primary mt-2" id="addChoiceBtn">
                                <i class="fas fa-plus"></i> Add Choice
                            </button>
                        </div>
                    </div>

                    <!-- True/False Options -->
                    <div id="trueFalseOptions" class="question-options" style="display:none;">
                        <div class="form-group">
                            <label>Correct Answer</label>
                            <div class="true-false-options">
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="editTrueOption" name="editCorrectAnswer" value="true" class="custom-control-input">
                                    <label class="custom-control-label" for="editTrueOption">True</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="editFalseOption" name="editCorrectAnswer" value="false" class="custom-control-input">
                                    <label class="custom-control-label" for="editFalseOption">False</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Short Answer Options -->
                    <div id="shortAnswerOptions" class="question-options" style="display:none;">
                        <div class="form-group">
                            <label>Correct Answer</label>
                            <input type="text" class="form-control" id="editShortAnswer" placeholder="Enter the correct answer">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveQuestionBtn">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Add these functions to your existing JavaScript
    function initializeImageHandling() {
        $('#editQuestionImage').change(function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 5 * 1024 * 1024) {
                    Swal.fire('Error', 'Image size should not exceed 5MB', 'error');
                    this.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#editImagePreview img').attr('src', e.target.result);
                    $('#editImagePreview').show();
                    $('.custom-file-label').text(file.name);
                }
                reader.readAsDataURL(file);
            }
        });
    }

    function removeEditImage() {
        $('#editQuestionImage').val('');
        $('#editImagePreview').hide();
        $('#editImagePreview img').attr('src', '');
        $('.custom-file-label').text('Choose image...');
    }

    // Add this to your edit question function
    function editQuestion(questionCard) {
        const questionId = questionCard.data('question-id');
        const questionText = questionCard.find('.question-text').text().trim();
        const questionType = questionCard.find('input[name$="[type]"]').val();
        
        $('#editQuestionText').val(questionText);
        $('#editQuestionType').val(questionType).trigger('change');
        
        // Handle different question types
        switch(questionType) {
            case 'multiple_choice':
                setupMultipleChoiceEdit(questionCard);
                break;
            case 'true_false':
                setupTrueFalseEdit(questionCard);
                break;
            case 'short_answer':
                setupShortAnswerEdit(questionCard);
                break;
        }

        $('#editQuestionModal').modal('show');
        
        // Handle save
        $('#saveQuestionBtn').off('click').on('click', function() {
            saveQuestionChanges(questionCard);
        });
    }

    function setupMultipleChoiceEdit(questionCard) {
        const choices = questionCard.find('.choice-text').map(function() {
            return $(this).text().trim();
        }).get();
        
        const correctChoice = questionCard.find('input[name$="[correct_choice]"]').val();
        
        // Clear and rebuild choices
        $('.choice-inputs').empty();
        choices.forEach((choice, index) => {
            addChoiceInput(choice, index === parseInt(correctChoice));
        });
    }

    function setupTrueFalseEdit(questionCard) {
        const correctAnswer = questionCard.find('input[name$="[correct_choice]"]').val();
        $(`#edit${correctAnswer.charAt(0).toUpperCase() + correctAnswer.slice(1)}Option`).prop('checked', true);
    }

    function setupShortAnswerEdit(questionCard) {
        const correctAnswer = questionCard.find('input[name$="[correct_answer]"]').val();
        $('#editShortAnswer').val(correctAnswer);
    }

    function saveQuestionChanges(questionCard) {
        const questionType = $('#editQuestionType').val();
        const questionText = $('#editQuestionText').val();
        let correctAnswer, choices;

        switch(questionType) {
            case 'multiple_choice':
                choices = [];
                correctAnswer = $('input[name="correct_choice"]:checked').val();
                $('.choice-input').each(function() {
                    choices.push($(this).val());
                });
                break;
            case 'true_false':
                correctAnswer = $('input[name="editCorrectAnswer"]:checked').val();
                break;
            case 'short_answer':
                correctAnswer = $('#editShortAnswer').val();
                break;
        }

        // Update the question card
        questionCard.find('.question-text').text(questionText);
        questionCard.find('input[name$="[type]"]').val(questionType);

        if (questionType === 'short_answer') {
            questionCard.find('input[name$="[correct_answer]"]').val(correctAnswer);
            questionCard.find('.correct-answer-display').text('Correct Answer: ' + correctAnswer);
        }

        $('#editQuestionModal').modal('hide');
    }

    // Add choice input for multiple choice questions
    function addChoiceInput(value = '', isCorrect = false) {
        const newIndex = $('.choice-inputs').children().length;
        const newChoice = `
            <div class="input-group mb-2">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <input type="radio" name="correct_choice" value="${newIndex}" ${isCorrect ? 'checked' : ''} required>
                    </div>
                </div>
                <input type="text" class="form-control choice-input" value="${value}" placeholder="Choice ${newIndex + 1}" required>
                <div class="input-group-append">
                    <button type="button" class="btn btn-danger remove-choice">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>`;
        
        $('.choice-inputs').append(newChoice);
        updateRemoveButtons();
    }

    // Initialize event handlers
    $(document).ready(function() {
        $('#addChoiceBtn').click(function() {
            addChoiceInput();
        });

        $(document).on('click', '.remove-choice', function() {
            $(this).closest('.input-group').remove();
            updateRemoveButtons();
        });

        $('#editQuestionType').change(function() {
            handleQuestionTypeChange($(this).val());
        });
    });

    function handleQuestionTypeChange(type) {
        $('.question-options').hide();
        switch(type) {
            case 'multiple_choice':
                $('#multipleChoiceOptions').show();
                break;
            case 'true_false':
                $('#trueFalseOptions').show();
                break;
            case 'short_answer':
                $('#shortAnswerOptions').show();
                break;
        }
    }

    // Add this function to handle question deletion
    function deleteQuestion(questionId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This will delete the question and all associated student answers. This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'handlers/delete_question.php',
                    type: 'POST',
                    data: {
                        question_id: questionId,
                        quiz_id: <?php echo $activity_id; ?>
                    },
                    success: function(response) {
                        if (response.success) {
                            // Remove the question card from the UI
                            $(`.question-card[data-question-id="${questionId}"]`).remove();
                            
                            // Update question numbers
                            $('.question-card').each(function(index) {
                                $(this).find('.card-header').text(`Question ${index + 1}`);
                            });

                            Swal.fire(
                                'Deleted!',
                                'Question has been deleted.',
                                'success'
                            );
                        } else {
                            Swal.fire(
                                'Error!',
                                response.error || 'Failed to delete question',
                                'error'
                            );
                        }
                    },
                    error: function() {
                        Swal.fire(
                            'Error!',
                            'Failed to delete question. Please try again.',
                            'error'
                        );
                    }
                });
            }
        });
    }

    // Update the delete button click handler
    $(document).on('click', '.delete-question', function(e) {
        e.preventDefault();
        const questionId = $(this).closest('.question-card').data('question-id');
        deleteQuestion(questionId);
    });
    </script>
</body>
</html>
