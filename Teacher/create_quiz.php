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
    <title>Create Quiz - CamerinoHub</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/dashboard-shared.css">
    <link rel="stylesheet" href="css/create-quiz.css">
    <link rel="icon" href="../images/light-logo.png">
    <style>
    /* Add these styles to your existing CSS */
    .figure img:focus {
        outline: 2px solid #007bff;
        outline-offset: 2px;
    }

    .custom-file-input:focus ~ .custom-file-label {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
    }

    /* Ensure modal content is properly accessible */
    .modal-content:focus {
        outline: none;
    }

    .modal.show {
        display: flex !important;
        align-items: center;
        background-color: rgba(0,0,0,0.5);
    }

    /* Improve button focus states */
    .btn:focus {
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
    }
    </style>
</head>
<body>
    <?php include 'includes/navigation.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="content-header">
                <h1><i class="fas fa-edit mr-2"></i>Create Quiz</h1>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <form id="quizForm" action="handlers/save_quiz.php" method="POST">
                        <!-- Quiz Details Section -->
                        <div class="section mb-4">
                            <h4 class="mb-3">Quiz Details</h4>
                            <div class="form-group">
                                <label><i class="fas fa-chalkboard-teacher mr-2"></i>Section & Subject</label>
                                <select name="section_subject_id" class="form-control" required>
                                    <option value="">Select Section & Subject</option>
                                    <?php foreach ($sections as $section): ?>
                                        <option value="<?php echo $section['section_subject_id']; ?>">
                                            <?php echo $section['section_name'] . ' - ' . $section['subject_name']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label><i class="fas fa-heading mr-2"></i>Quiz Title</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label><i class="fas fa-align-left mr-2"></i>Description</label>
                                <textarea name="description" class="form-control" rows="3" required></textarea>
                            </div>
                        </div>

                        <!-- Questions Section -->
                        <div class="section mb-4">
                            <h4 class="mb-3">Questions
                                <button type="button" class="btn btn-primary btn-sm float-right" data-toggle="modal" data-target="#addQuestionModal">
                                    <i class="fas fa-plus mr-2"></i>Add Question
                                </button>
                            </h4>
                            <div id="questionsContainer">
                                <!-- Questions will be added here dynamically -->
                            </div>
                        </div>

                        <!-- Quiz Settings Section -->
                        <div class="section mb-4">
                            <h4 class="mb-3">Quiz Settings</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-calendar-alt mr-2"></i>Due Date</label>
                                        <input type="datetime-local" name="due_date" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-star mr-2"></i>Total Points</label>
                                        <input type="number" name="points" class="form-control" required min="1" value="0" readonly>
                                        <small class="text-muted">Points are automatically set based on number of questions</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-hourglass-half mr-2"></i>Duration (minutes)</label>
                                        <input type="number" name="quiz_duration" class="form-control" required min="1" value="60">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="preventTabSwitch" name="prevent_tab_switch" value="1">
                                    <label class="custom-control-label" for="preventTabSwitch">
                                        <i class="fas fa-window-restore mr-2"></i>Prevent Tab Switching
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="fullscreenRequired" name="fullscreen_required" value="1">
                                    <label class="custom-control-label" for="fullscreenRequired">
                                        <i class="fas fa-expand mr-2"></i>Require Fullscreen
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-2"></i>Create Quiz
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

    <!-- Add Question Modal -->
    <div class="modal fade" id="addQuestionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Question</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Question Type</label>
                        <select class="form-control" id="questionType">
                            <option value="multiple_choice">Multiple Choice</option>
                            <option value="true_false">True/False</option>
                            <option value="short_answer">Short Answer</option>
                        </select>
                    </div>
                    
                    <!-- Add image upload field -->
                    <div class="form-group">
                        <label>Question Image (Optional)</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="questionImage" accept="image/*">
                            <label class="custom-file-label" for="questionImage">Choose image...</label>
                        </div>
                        <small class="text-muted">Supported formats: JPG, PNG, GIF (Max 5MB)</small>
                        <!-- Updated preview container -->
                        <div id="imagePreview" class="mt-2" style="display: none;" role="region" aria-label="Image preview">
                            <figure class="figure mb-2">
                                <img src="" alt="" class="img-fluid" style="max-height: 200px;">
                                <figcaption class="figure-caption">
                                    <button type="button" class="btn btn-sm btn-danger" onclick="removeImage()" aria-label="Remove image">
                                        <i class="fas fa-times" aria-hidden="true"></i> Remove Image
                                    </button>
                                </figcaption>
                            </figure>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Question Text</label>
                        <textarea class="form-control" id="questionText" rows="3" required></textarea>
                    </div>
                    <div id="choicesContainer">
                        <!-- Choices will be added here based on question type -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="addQuestionBtn">Add Question</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    $(document).ready(function() {
        let questionCount = 0;

        // Handle question type change
        $('#questionType').change(function() {
            const type = $(this).val();
            let choicesHtml = '';

            if (type === 'multiple_choice') {
                choicesHtml = `
                    <div class="choices-container">
                        <div class="form-group">
                            <label>Choices</label>
                            <div class="choice-inputs">
                                <div class="input-group mb-2">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <input type="radio" name="correct_choice" value="0" required>
                                        </div>
                                    </div>
                                    <input type="text" class="form-control choice-input" placeholder="Choice 1" required>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-danger remove-choice" style="display: none;">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="input-group mb-2">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <input type="radio" name="correct_choice" value="1" required>
                                        </div>
                                    </div>
                                    <input type="text" class="form-control choice-input" placeholder="Choice 2" required>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-danger remove-choice" style="display: none;">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-secondary btn-sm mt-2" id="addChoiceBtn">
                                <i class="fas fa-plus"></i> Add Choice
                            </button>
                        </div>
                    </div>`;
            } else if (type === 'true_false') {
                choicesHtml = `
                    <div class="form-group">
                        <label>Correct Answer</label>
                        <div class="custom-control custom-radio">
                            <input type="radio" id="true" name="correct_tf" class="custom-control-input" value="true" required>
                            <label class="custom-control-label" for="true">True</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" id="false" name="correct_tf" class="custom-control-input" value="false" required>
                            <label class="custom-control-label" for="false">False</label>
                        </div>
                    </div>`;
            } else if (type === 'short_answer') {
                choicesHtml = `
                    <div class="form-group">
                        <label>Correct Answer</label>
                        <input type="text" 
                               class="form-control" 
                               name="correct_answer" 
                               required 
                               placeholder="Enter the correct answer">
                        <small class="text-muted">
                            Student's answer must match this exactly (case-insensitive)
                        </small>
                    </div>`;
            }

            $('#choicesContainer').html(choicesHtml);
        });

        // Trigger initial question type change
        $('#questionType').trigger('change');

        // Image preview handling
        $('#questionImage').change(function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 5 * 1024 * 1024) { // 5MB limit
                    Swal.fire('Error', 'Image size should not exceed 5MB', 'error');
                    this.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    // Update preview container
                    const previewContainer = $('#imagePreview');
                    const previewImage = previewContainer.find('img');
                    
                    // Set tabindex and remove aria-hidden
                    previewContainer.removeAttr('aria-hidden');
                    previewImage.attr({
                        'src': e.target.result,
                        'tabindex': '0',
                        'role': 'img',
                        'aria-label': 'Question image preview'
                    });
                    
                    previewContainer.show();
                    $('.custom-file-label').text(file.name);
                }
                reader.readAsDataURL(file);
            }
        });

        // Update remove image function
        window.removeImage = function() {
            const previewContainer = $('#imagePreview');
            const previewImage = previewContainer.find('img');
            
            $('#questionImage').val('');
            previewContainer.hide();
            previewImage.removeAttr('src tabindex role aria-label');
            $('.custom-file-label').text('Choose image...');
        }

        // Update modal handling
        $('#addQuestionModal').on('show.bs.modal', function() {
            $(this).find('[aria-hidden]').removeAttr('aria-hidden');
        });

        $('#addQuestionModal').on('hidden.bs.modal', function() {
            removeImage();
        });

        // Modify the addQuestionBtn click handler to include image
        $('#addQuestionBtn').click(function() {
            const type = $('#questionType').val();
            const questionText = $('#questionText').val();
            
            if (!questionText) {
                Swal.fire('Error', 'Please enter question text', 'error');
                return;
            }

            // Get choices based on question type
            let choices = [];
            let correctAnswer = '';
            
            if (type === 'multiple_choice') {
                let hasCorrectAnswer = false;
                $('.choice-input').each(function(index) {
                    const choiceText = $(this).val().trim();
                    if (!choiceText) {
                        Swal.fire('Error', 'Please fill in all choices', 'error');
                        return false;
                    }
                    choices.push(choiceText);
                    if ($(`input[name="correct_choice"][value="${index}"]`).is(':checked')) {
                        hasCorrectAnswer = true;
                        correctAnswer = index;
                    }
                });
                
                if (!hasCorrectAnswer) {
                    Swal.fire('Error', 'Please select the correct answer', 'error');
                    return;
                }
            } else if (type === 'true_false') {
                correctAnswer = $('input[name="correct_tf"]:checked').val();
                if (!correctAnswer) {
                    Swal.fire('Error', 'Please select the correct answer', 'error');
                    return;
                }
            } else if (type === 'short_answer') {
                correctAnswer = $('input[name="correct_answer"]').val().trim();
                if (!correctAnswer) {
                    Swal.fire('Error', 'Please enter the correct answer', 'error');
                    return;
                }
            }

            // Handle image upload
            const imageFile = $('#questionImage')[0].files[0];
            if (imageFile) {
                const formData = new FormData();
                formData.append('image', imageFile);
                
                const addBtn = $(this);
                addBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Adding...');
                
                $.ajax({
                    url: 'handlers/upload_question_image.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        try {
                            const result = typeof response === 'string' ? JSON.parse(response) : response;
                            if (result.success) {
                                addQuestionToForm(type, questionText, choices, correctAnswer, result.image_path);
                            } else {
                                throw new Error(result.error || 'Failed to upload image');
                            }
                        } catch (error) {
                            console.error('Upload error:', error);
                            Swal.fire('Error', error.message, 'error');
                        }
                        addBtn.prop('disabled', false).html('Add Question');
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error:', {xhr, status, error});
                        Swal.fire('Error', 'Failed to upload image', 'error');
                        addBtn.prop('disabled', false).html('Add Question');
                    }
                });
            } else {
                addQuestionToForm(type, questionText, choices, correctAnswer);
            }
        });

        // Update the addQuestionToForm function
        function addQuestionToForm(type, questionText, choices, correctAnswer, imagePath = null) {
            const questionCount = $('.question-card').length;
            
            let questionHtml = `
                <div class="card mb-3 question-card" data-question="${questionCount}">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Question ${questionCount + 1}</span>
                        <div>
                            <input type="hidden" name="questions[${questionCount}][points]" value="1">
                            <span class="badge badge-primary mr-2">1 point</span>
                            <button type="button" class="btn btn-sm btn-danger remove-question">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <input type="hidden" name="questions[${questionCount}][type]" value="${type}">
                        <input type="hidden" name="questions[${questionCount}][text]" value="${questionText}">`;

            // Add image if exists
            if (imagePath) {
                questionHtml += `
                    <input type="hidden" name="questions[${questionCount}][image_path]" value="${imagePath}">
                    <div class="question-image mb-3">
                        <img src="../${imagePath}" alt="Question Image" class="img-fluid" style="max-height: 200px;">
                    </div>`;
            }

            // Add question text
            questionHtml += `<p class="mb-3"><strong>Question:</strong> ${questionText}</p>`;

            // Add choices based on question type
            if (type === 'multiple_choice') {
                const choices = [];
                let correctChoice = -1;
                
                $('.choice-input').each(function(index) {
                    choices.push($(this).val());
                    if ($(`input[name="correct_choice"][value="${index}"]`).is(':checked')) {
                        correctChoice = index;
                    }
                });

                if (correctChoice === -1) {
                    Swal.fire('Error', 'Please select the correct answer', 'error');
                    return;
                }

                questionHtml += `<input type="hidden" name="questions[${questionCount}][correct_choice]" value="${correctChoice}">`;
                choices.forEach((choice, index) => {
                    questionHtml += `
                        <input type="hidden" name="questions[${questionCount}][choices][]" value="${choice}">
                        <div class="ml-3">
                            <i class="fas ${index === correctChoice ? 'fa-check-circle text-success' : 'fa-circle'}"></i>
                            ${choice}
                        </div>`;
                });
            } else if (type === 'true_false') {
                const correctAnswer = $('input[name="correct_tf"]:checked').val();
                if (!correctAnswer) {
                    Swal.fire('Error', 'Please select the correct answer', 'error');
                    return;
                }
                questionHtml += `
                    <input type="hidden" name="questions[${questionCount}][correct_choice]" value="${correctAnswer}">
                    <div class="ml-3">
                        <i class="fas fa-check-circle text-success"></i> Correct Answer: ${correctAnswer}
                    </div>`;
            } else if (type === 'short_answer') {
                const correctAnswer = $('input[name="correct_answer"]').val().trim();
                if (!correctAnswer) {
                    Swal.fire('Error', 'Please enter the correct answer', 'error');
                    return;
                }
                questionHtml += `
                    <input type="hidden" name="questions[${questionCount}][type]" value="${type}">
                    <input type="hidden" name="questions[${questionCount}][text]" value="${questionText}">
                    <input type="hidden" name="questions[${questionCount}][correct_answer]" value="${correctAnswer}">
                    <div class="ml-3">
                        <i class="fas fa-check-circle text-success"></i> Correct Answer: ${correctAnswer}
                    </div>`;
            }

            questionHtml += `
                    </div>
                </div>`;

            $('#questionsContainer').append(questionHtml);
            $('#addQuestionModal').modal('hide');
            resetModal();
            updateTotalPoints();
        }

        // Update the resetModal function
        function resetModal() {
            $('#questionText').val('');
            $('#questionType').val('multiple_choice').trigger('change');
            $('input[name="correct_choice"]').prop('checked', false);
            $('input[name="correct_tf"]').prop('checked', false);
            $('input[name="correct_answer"]').val('');
            $('.choice-input').val('');
            $('#choicesContainer').find('.is-invalid').removeClass('is-invalid');
            removeImage(); // Clear image preview
        }

        // Delete question
        $(document).on('click', '.remove-question', function() {
            const card = $(this).closest('.question-card');
            
            Swal.fire({
                title: 'Delete Question?',
                text: 'Are you sure you want to delete this question?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    card.remove();
                    // Renumber remaining questions
                    $('.question-card').each(function(index) {
                        $(this).find('.card-header span').text(`Question ${index + 1}`);
                        $(this).attr('data-question', index);
                        $(this).find('input[name^="questions["]').each(function() {
                            const oldName = $(this).attr('name');
                            const newName = oldName.replace(/questions\[\d+\]/, `questions[${index}]`);
                            $(this).attr('name', newName);
                        });
                    });
                    updateTotalPoints();
                }
            });
        });

        // Form submission
        $('#quizForm').on('submit', function(e) {
            e.preventDefault();
            
            if ($('.question-card').length === 0) {
                Swal.fire('Error', 'Please add at least one question', 'error');
                return;
            }

            // Validate that all questions have points
            let valid = true;
            $('.question-card').each(function() {
                const points = $(this).find('input[name$="[points]"]').val();
                if (!points || points < 1) {
                    valid = false;
                    $(this).find('input[name$="[points]"]').addClass('is-invalid');
                }
            });

            if (!valid) {
                Swal.fire('Error', 'All questions must have valid points', 'error');
                return;
            }

            const formData = new FormData(this);

            Swal.fire({
                title: 'Creating Quiz...',
                html: '<i class="fas fa-spinner fa-spin"></i> Please wait...',
                allowOutsideClick: false,
                showConfirmButton: false
            });

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: response.swal.title,
                            text: response.swal.text,
                            confirmButtonText: response.swal.confirmButtonText
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'manage_activities.php';
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: response.swal.title,
                            text: response.swal.text,
                            confirmButtonText: response.swal.confirmButtonText
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to create quiz. Please try again.'
                    });
                }
            });
        });

        // Add this function to handle automatic point calculation
        function updateTotalPoints() {
            const questionCount = $('.question-card').length;
            $('input[name="points"]').val(questionCount);
        }

        // Add new choice
        $(document).on('click', '#addChoiceBtn', function() {
            const choiceInputs = $('.choice-inputs');
            const newIndex = choiceInputs.children().length;
            
            const newChoice = `
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <input type="radio" name="correct_choice" value="${newIndex}" required>
                        </div>
                    </div>
                    <input type="text" class="form-control choice-input" placeholder="Choice ${newIndex + 1}" required>
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger remove-choice">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>`;
            
            choiceInputs.append(newChoice);
            
            // Show/hide remove buttons based on number of choices
            updateRemoveButtons();
        });

        // Remove choice
        $(document).on('click', '.remove-choice', function() {
            const choiceGroup = $(this).closest('.input-group');
            choiceGroup.remove();
            
            // Renumber remaining choices
            $('.choice-inputs .input-group').each(function(index) {
                $(this).find('input[type="radio"]').val(index);
                $(this).find('.choice-input').attr('placeholder', `Choice ${index + 1}`);
            });
            
            // Show/hide remove buttons based on number of choices
            updateRemoveButtons();
        });

        // Function to update remove buttons visibility
        function updateRemoveButtons() {
            const choices = $('.choice-inputs .input-group');
            const removeButtons = $('.remove-choice');
            
            if (choices.length > 2) {
                removeButtons.show();
            } else {
                removeButtons.hide();
            }
        }
    });
    </script>
</body>
</html>