<?php
// Function to render quiz questions
function renderQuizQuestions($questions) {
    $html = '';
    foreach ($questions as $index => $question) {
        $questionNumber = $index + 1;
        $html .= '<div class="question-container mb-4">';
        $html .= "<h5>Question {$questionNumber}</h5>";
        $html .= "<p class='mb-3'>{$question['question_text']}</p>";

        switch ($question['question_type']) {
            case 'multiple_choice':
                $choices = explode('|', $question['choices']);
                foreach ($choices as $choice) {
                    list($choice_id, $is_correct) = explode(':', $choice);
                    $html .= '
                    <div class="form-check mb-2">
                        <input type="radio" 
                               id="choice_' . $choice_id . '" 
                               name="answer_' . $question['question_id'] . '" 
                               value="' . $choice_id . '" 
                               class="form-check-input">
                        <label class="form-check-label" for="choice_' . $choice_id . '">' 
                            . $choice['choice_text'] . 
                        '</label>
                    </div>';
                }
                break;

            case 'true_false':
                $html .= '
                <div class="form-check mb-2">
                    <input type="radio" 
                           id="true_' . $question['question_id'] . '" 
                           name="answer_' . $question['question_id'] . '" 
                           value="true" 
                           class="form-check-input">
                    <label class="form-check-label" for="true_' . $question['question_id'] . '">True</label>
                </div>
                <div class="form-check mb-2">
                    <input type="radio" 
                           id="false_' . $question['question_id'] . '" 
                           name="answer_' . $question['question_id'] . '" 
                           value="false" 
                           class="form-check-input">
                    <label class="form-check-label" for="false_' . $question['question_id'] . '">False</label>
                </div>';
                break;

            case 'short_answer':
                $html .= '
                <div class="form-group">
                    <textarea class="form-control" 
                             name="answer_' . $question['question_id'] . '" 
                             rows="3" 
                             placeholder="Enter your answer here"></textarea>
                </div>';
                break;
        }
        $html .= '</div>';
    }
    return $html;
}
?>

<form id="quizForm" class="mt-4">
    <?php echo renderQuizQuestions($questions); ?>
    <button type="submit" class="btn btn-primary">Submit Quiz</button>
</form> 