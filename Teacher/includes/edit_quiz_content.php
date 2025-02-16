<style>
    .js-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
    }
    
    .js-modal.show {
        display: flex;
    }
    
    .js-modal-dialog {
        width: auto;
        max-width: 800px;
        margin: 0 15px;
    }
    
    .js-modal-content {
        position: relative;
        background-color: #fff;
        border-radius: 0.3rem;
        box-shadow: 0 0 15px rgba(0,0,0,0.2);
        margin: 0 15px;
    }
    
    .js-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem;
        border-bottom: 1px solid #dee2e6;
        background: #f8f9fa;
        border-radius: 0.3rem 0.3rem 0 0;
    }
    
    .js-modal-body {
        padding: 1rem;
        max-height: calc(100vh - 200px);
        overflow-y: auto;
    }
    
    .js-modal-close {
        padding: 0.5rem;
        margin: -0.5rem;
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        opacity: 0.5;
        transition: opacity 0.2s;
    }

    .js-modal-close:hover {
        opacity: 1;
    }

    .js-modal:focus,
    .js-modal-content:focus {
        outline: none;
    }

    .js-modal.show {
        visibility: visible !important;
        display: block !important;
    }

    @media (max-width: 768px) {
        .js-modal-dialog {
            margin: 10px auto;
        }
        
        .js-modal-body {
            max-height: calc(100vh - 120px);
        }
    }

    .question-form-container {
        display: none;
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 0.3rem;
        margin: 1rem 0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .question-form-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem;
        border-bottom: 1px solid #dee2e6;
        background: #f8f9fa;
        border-radius: 0.3rem 0.3rem 0 0;
    }
    
    .question-form-body {
        padding: 1rem;
    }
    
    .question-form-close {
        padding: 0.5rem;
        margin: -0.5rem;
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
    }
</style>

<!-- Questions Section -->
<div class="section mb-4">
    <h4 class="mb-3">Questions
        <button type="button" class="btn btn-primary btn-sm float-right" onclick="showAddQuestionForm()">
            <i class="fas fa-plus mr-2"></i>Add Question
        </button>
    </h4>

    <!-- Add Question Form (Initially Hidden) -->
    <div id="addQuestionForm" style="display: none;" class="card mb-4">
        <div class="question-form-header">
            <h5>Add New Question</h5>
            <button type="button" class="close" onclick="hideAddQuestionForm()">
                <span>&times;</span>
            </button>
        </div>
        <div class="question-form-body">
            <form id="newQuestionForm" onsubmit="return false;">
                <div class="form-group">
                    <label>Question Text</label>
                    <textarea class="form-control" id="newQuestionText" rows="2" required></textarea>
                </div>
                
                <div class="form-group">
                    <label>Question Type</label>
                    <select class="form-control" id="newQuestionType" onchange="updateChoicesContainer()" required>
                        <option value="multiple_choice">Multiple Choice</option>
                        <option value="true_false">True/False</option>
                        <option value="short_answer">Short Answer</option>
                    </select>
                </div>
                
                <div id="newChoicesContainer">
                    <!-- Dynamic choices will be added here -->
                </div>
                
                <div class="text-right mt-3">
                    <button type="button" class="btn btn-secondary" onclick="hideAddQuestionForm()">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveQuestion()">Save Question</button>
                </div>
            </form>
        </div>
    </div>

    <div id="questionsContainer">
        <?php foreach ($questions as $index => $question): ?>
            <div class="card mb-3 question-card" data-question-id="<?php echo $question['question_id']; ?>" 
                 data-question-type="<?php echo $question['question_type']; ?>">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Question <?php echo $index + 1; ?> (<?php echo ucfirst(str_replace('_', ' ', $question['question_type'])); ?>)</span>
                    <div>
                        <button type="button" class="btn btn-danger btn-sm delete-question" 
                                data-question-id="<?php echo $question['question_id']; ?>">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Question:</strong> <?php echo htmlspecialchars($question['question_text']); ?></p>
                    <?php if ($question['question_type'] === 'multiple_choice'): ?>
                        <?php 
                        $choices = array_filter(explode('|', $question['choices']));
                        foreach ($choices as $choice):
                            list($choice_id, $choice_text, $is_correct) = explode(':', $choice);
                        ?>
                            <div class="ml-3 choice-item">
                                <i class="fas <?php echo $is_correct ? 'fa-check-circle text-success' : 'fa-circle'; ?>"></i>
                                <?php echo htmlspecialchars($choice_text); ?>
                            </div>
                        <?php endforeach; ?>
                    <?php elseif ($question['question_type'] === 'true_false'): ?>
                        <?php 
                        $choices = array_filter(explode('|', $question['choices']));
                        foreach ($choices as $choice):
                            list($choice_id, $choice_text, $is_correct) = explode(':', $choice);
                            if ($is_correct):
                        ?>
                            <div class="ml-3">
                                <i class="fas fa-check-circle text-success"></i> Correct Answer: <?php echo $choice_text; ?>
                            </div>
                        <?php endif; endforeach; ?>
                    <?php elseif ($question['question_type'] === 'short_answer'): ?>
                        <div class="ml-3">
                            <i class="fas fa-check-circle text-success"></i> 
                            Correct Answer: <?php echo htmlspecialchars($question['correct_answer'] ?? ''); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Quiz Settings Section -->
<div class="section mb-4">
    <h4 class="mb-3">Quiz Settings</h4>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label><i class="fas fa-hourglass-half mr-2"></i>Duration (minutes)</label>
                <input type="number" name="quiz_duration" class="form-control" 
                       value="<?php echo $activity['quiz_duration']; ?>" required min="1">
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="preventTabSwitch" 
                   name="prevent_tab_switch" value="1" <?php echo $activity['prevent_tab_switch'] ? 'checked' : ''; ?>>
            <label class="custom-control-label" for="preventTabSwitch">
                <i class="fas fa-window-restore mr-2"></i>Prevent Tab Switching
            </label>
        </div>
    </div>

    <div class="form-group">
        <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="fullscreenRequired" 
                   name="fullscreen_required" value="1" <?php echo $activity['fullscreen_required'] ? 'checked' : ''; ?>>
            <label class="custom-control-label" for="fullscreenRequired">
                <i class="fas fa-expand mr-2"></i>Require Fullscreen
            </label>
        </div>
    </div>
</div>

<!-- Add this script at the bottom of the file -->
<script>
$(document).ready(function() {
    // Initialize question type change handler
    $('#newQuestionType').change(function() {
        updateChoicesContainer();
    });

    // Form submission handler
    $('#newQuestionForm').submit(function(e) {
        e.preventDefault();
        saveQuestion();
    });

    // Delete question handler
    $('.delete-question').click(function() {
        const questionId = $(this).data('question-id');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                deleteQuestion(questionId);
            }
        });
    });

    // Initialize points on page load
    updateTotalPoints();
});

// Show/Hide form functions
function showAddQuestionForm() {
    resetQuestionForm();
    $('#addQuestionForm').slideDown();
    updateChoicesContainer();
}

function hideAddQuestionForm() {
    $('#addQuestionForm').slideUp();
    resetQuestionForm();
}

// Reset form function
function resetQuestionForm() {
    if ($('#newQuestionForm').length) {
        $('#newQuestionForm')[0].reset();
    }
    $('#newQuestionText').val('');
    $('#newQuestionType').val('multiple_choice');
    $('#newChoicesContainer').empty();
}

// Update choices container based on question type
function updateChoicesContainer() {
    const type = $('#newQuestionType').val();
    let html = '';
    
    if (type === 'multiple_choice') {
        html = `
            <div class="form-group">
                <label>Choices</label>
                <div class="choices">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <input type="radio" name="new_correct_choice" value="0" required>
                            </div>
                        </div>
                        <input type="text" class="form-control new-choice-input" required>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary remove-choice">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-secondary mt-2" onclick="addChoice()">
                    Add Choice
                </button>
            </div>`;
    } else if (type === 'true_false') {
        html = `
            <div class="form-group">
                <label>Correct Answer</label>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="new_correct_tf" value="true" required>
                    <label class="form-check-label">True</label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="new_correct_tf" value="false" required>
                    <label class="form-check-label">False</label>
                </div>
            </div>`;
    } else if (type === 'short_answer') {
        html = `
            <div class="form-group">
                <label>Correct Answer</label>
                <input type="text" class="form-control" name="new_correct_answer" required>
                <small class="form-text text-muted">Enter the correct answer for this question.</small>
            </div>`;
    }
    
    $('#newChoicesContainer').html(html);
    
    if (type === 'multiple_choice') {
        addChoice(); // Add initial choice
    }
}

// Update addChoice function
function addChoice() {
    const choicesCount = $('.new-choice-input').length;
    const newChoice = `
        <div class="input-group mb-2">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <input type="radio" name="new_correct_choice" value="${choicesCount}" required>
                </div>
            </div>
            <input type="text" class="form-control new-choice-input" required>
            <div class="input-group-append">
                <button type="button" class="btn btn-outline-secondary remove-choice">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>`;
    $('.choices').append(newChoice);
}

// Save question function
function saveQuestion() {
    const questionText = $('#newQuestionText').val();
    const questionType = $('#newQuestionType').val();
    
    console.log('Saving question:', { questionText, questionType });

    if (!questionText) {
        Swal.fire('Error', 'Please enter question text', 'error');
        return;
    }

    const questionData = {
        quiz_id: <?php echo $activity_id; ?>,
        text: questionText,
        type: questionType
    };

    console.log('Initial question data:', questionData);

    // Add type-specific data
    if (questionType === 'multiple_choice') {
        const choices = [];
        const correctChoice = $('input[name="new_correct_choice"]:checked').val();
        
        console.log('Multiple choice - correct choice:', correctChoice);

        if (!correctChoice && correctChoice !== '0') {
            Swal.fire('Error', 'Please select the correct answer', 'error');
            return;
        }

        let allChoicesValid = true;
        $('.new-choice-input').each(function(index) {
            const choiceText = $(this).val().trim();
            console.log('Choice text:', choiceText, 'Index:', index);
            if (!choiceText) {
                allChoicesValid = false;
                return false;
            }
            choices.push({
                text: String(choiceText),
                is_correct: Number(index === parseInt(correctChoice))
            });
        });

        console.log('Collected choices:', choices);

        if (!allChoicesValid) {
            Swal.fire('Error', 'Please fill in all choices', 'error');
            return;
        }

        if (choices.length < 2) {
            Swal.fire('Error', 'Multiple choice questions require at least 2 choices', 'error');
            return;
        }

        questionData.choices = choices;
        console.log('Final question data with choices:', JSON.stringify(questionData, null, 2));
    } else if (questionType === 'true_false') {
        const correctAnswer = $('input[name="new_correct_tf"]:checked').val();
        if (!correctAnswer) {
            Swal.fire('Error', 'Please select the correct answer', 'error');
            return;
        }
        questionData.correct_tf = correctAnswer;
    } else if (questionType === 'short_answer') {
        const correctAnswer = $('input[name="new_correct_answer"]').val().trim();
        if (!correctAnswer) {
            Swal.fire('Error', 'Please enter the correct answer', 'error');
            return;
        }
        questionData.correct_answer = correctAnswer;
    }

    console.log('Final question data:', questionData);

    // Show loading state
    const submitBtn = $('#newQuestionForm').find('button[type="button"].btn-primary');
    submitBtn.prop('disabled', true);
    submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Saving...');

    // Show processing alert
    Swal.fire({
        title: 'Processing...',
        text: 'Saving question',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Send AJAX request
    $.ajax({
        url: 'handlers/save_quiz_question.php',
        type: 'POST',
        data: JSON.stringify(questionData),
        contentType: 'application/json',
        dataType: 'json',
        beforeSend: function() {
            console.log('Sending data:', questionData);
        },
        success: function(response) {
            console.log('Save response:', response);
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Question saved successfully',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire('Error', response.error || 'Failed to save question', 'error');
                submitBtn.prop('disabled', false);
                submitBtn.html('Save Question');
            }
        },
        error: function(xhr, status, error) {
            console.error('Save error:', {
                status: status,
                error: error,
                responseText: xhr.responseText,
                readyState: xhr.readyState,
                statusText: xhr.statusText
            });
            let errorMessage = 'Failed to save question';
            try {
                const response = JSON.parse(xhr.responseText);
                if (response.error) {
                    errorMessage = response.error;
                }
            } catch (e) {
                console.error('Error parsing response:', e);
                if (xhr.responseText) {
                    errorMessage += ': ' + xhr.responseText;
                }
            }
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errorMessage,
                footer: 'Check console for more details'
            });
            submitBtn.prop('disabled', false);
            submitBtn.html('Save Question');
        }
    });
}

// Make sure the form submission is properly bound
$(document).ready(function() {
    console.log('Document ready');
    
    $('#newQuestionForm').on('submit', function(e) {
        console.log('Form submitted');
        e.preventDefault();
        saveQuestion();
    });
});

// Remove choice handler
$(document).on('click', '.remove-choice', function() {
    $(this).closest('.input-group').remove();
});

// Add these functions to your script section
function showEditQuestionForm(questionId) {
    // Remove any existing edit forms first
    $('.edit-question-form').remove();
    // Show all question cards that might be hidden
    $('.question-card').show();
    
    // Clone the add question form
    const questionCard = $(`[data-question-id="${questionId}"]`);
    const editForm = $('#addQuestionForm').clone()
        .attr('id', `editForm_${questionId}`)
        .addClass('edit-question-form')
        .insertAfter(questionCard);
    
    // Update form title and buttons
    editForm.find('.question-form-header h5').text('Edit Question');
    editForm.find('button[onclick="saveQuestion()"]')
        .attr('onclick', `updateQuestion(${questionId})`);
    editForm.find('button[onclick="hideAddQuestionForm()"]')
        .attr('onclick', `hideEditForm(${questionId})`);
    
    // Initialize type change handler for edit form
    const typeSelect = editForm.find('#newQuestionType');
    typeSelect.off('change').on('change', function() {
        const form = $(this).closest('.edit-question-form');
        updateChoicesContainer(form);
    });
    
    // Load question data
    $.ajax({
        url: 'handlers/get_quiz_question.php',
        type: 'GET',
        data: { question_id: questionId },
        success: function(response) {
            if (response.success) {
                const question = response.data;
                editForm.find('#newQuestionText').val(question.question_text);
                editForm.find('#newQuestionType').val(question.question_type);
                typeSelect.trigger('change'); // This will call updateChoicesContainer with the correct form
                
                // Handle different question types
                if (question.question_type === 'multiple_choice') {
                    question.choices.forEach((choice, index) => {
                        if (index > 0) addChoice(editForm);
                        const inputs = editForm.find('.new-choice-input');
                        inputs.eq(index).val(choice.choice_text);
                        if (choice.is_correct === '1') {
                            editForm.find(`input[name="new_correct_choice"][value="${index}"]`).prop('checked', true);
                        }
                    });
                } else if (question.question_type === 'true_false') {
                    const correctAnswer = question.choices.find(c => c.is_correct === '1');
                    if (correctAnswer) {
                        editForm.find(`input[name="new_correct_tf"][value="${correctAnswer.choice_text.toLowerCase()}"]`).prop('checked', true);
                    }
                } else if (question.question_type === 'short_answer') {
                    editForm.find('input[name="new_correct_answer"]').val(question.correct_answer);
                }
                
                editForm.slideDown();
                questionCard.hide();
            }
        }
    });
}

// Add this new function to hide edit form
function hideEditForm(questionId) {
    $(`.edit-question-form`).slideUp(400, function() {
        $(this).remove();
    });
    $(`[data-question-id="${questionId}"]`).show();
}

// Update the updateQuestion function
function updateQuestion(questionId) {
    const form = $(`#editForm_${questionId}`);
    const questionText = form.find('#newQuestionText').val();
    const type = form.find('#newQuestionType').val();
    
    console.log('Updating question:', { questionId, questionText, type });
    
    if (!questionText) {
        Swal.fire('Error', 'Please enter question text', 'error');
        return;
    }

    const questionData = {
        question_id: questionId,
        quiz_id: <?php echo $activity_id; ?>,
        text: questionText,
        type: type,
        points: 1
    };

    console.log('Question data before type-specific:', questionData);

    // Add type-specific data based on current type
    if (type === 'multiple_choice') {
        const choices = [];
        const correctChoice = form.find('input[name="new_correct_choice"]:checked').val();
        
        console.log('Multiple choice - correct choice:', correctChoice);
        
        if (!correctChoice && correctChoice !== '0') {
            Swal.fire('Error', 'Please select the correct answer', 'error');
            submitBtn.prop('disabled', false);
            submitBtn.html('Update Question');
            return;
        }

        let allChoicesValid = true;
        form.find('.new-choice-input').each(function(index) {
            const choiceText = $(this).val().trim();
            if (!choiceText) {
                allChoicesValid = false;
                return false;
            }
            choices.push({
                text: choiceText,
                is_correct: index == correctChoice ? 1 : 0
            });
        });

        console.log('Multiple choice - choices:', choices);

        if (!allChoicesValid) {
            Swal.fire('Error', 'Please fill in all choices', 'error');
            submitBtn.prop('disabled', false);
            submitBtn.html('Update Question');
            return;
        }

        questionData.choices = choices;
    } else if (type === 'true_false') {
        const correctAnswer = form.find('input[name="new_correct_tf"]:checked').val();
        console.log('True/False - correct answer:', correctAnswer);
        if (!correctAnswer) {
            Swal.fire('Error', 'Please select the correct answer', 'error');
            submitBtn.prop('disabled', false);
            submitBtn.html('Update Question');
            return;
        }
        questionData.correct_tf = correctAnswer;
    } else if (type === 'short_answer') {
        const correctAnswer = form.find('input[name="new_correct_answer"]').val().trim();
        console.log('Short answer - correct answer:', correctAnswer);
        if (!correctAnswer) {
            Swal.fire('Error', 'Please enter the correct answer', 'error');
            submitBtn.prop('disabled', false);
            submitBtn.html('Update Question');
            return;
        }
        questionData.correct_answer = correctAnswer;
    }

    console.log('Final question data:', questionData);

    const submitBtn = form.find('button[type="button"][onclick^="updateQuestion"]');
    submitBtn.prop('disabled', true);
    submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Updating...');

    console.log('Sending AJAX request with data:', questionData);

    $.ajax({
        url: 'handlers/update_quiz_question.php',
        type: 'POST',
        data: JSON.stringify(questionData),
        contentType: 'application/json',
        dataType: 'json',
        success: function(response) {
            console.log('Update response:', response);
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Question updated successfully',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire('Error', response.error || 'Failed to update question', 'error');
                submitBtn.prop('disabled', false);
                submitBtn.html('Update Question');
            }
        },
        error: function(xhr, status, error) {
            console.error('Update error:', { xhr, status, error });
            Swal.fire('Error', 'Failed to update question', 'error');
            submitBtn.prop('disabled', false);
            submitBtn.html('Update Question');
        }
    });
}

// Add delete question function
function deleteQuestion(questionId) {
    $.ajax({
        url: 'handlers/delete_quiz_question.php',
        type: 'POST',
        data: JSON.stringify({ question_id: questionId }),
        contentType: 'application/json',
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Deleted!',
                    text: 'Question has been deleted.',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    updateTotalPoints();
                    location.reload();
                });
            } else {
                Swal.fire('Error', response.error || 'Failed to delete question', 'error');
            }
        },
        error: function() {
            Swal.fire('Error', 'Failed to delete question', 'error');
        }
    });
}

// Add this function to update points
function updateTotalPoints() {
    const questionCount = $('.question-card').length;
    $('input[name="points"]').val(questionCount);
}
</script>