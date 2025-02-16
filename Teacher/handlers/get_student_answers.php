<?php
session_start();
require_once('../../db/dbConnector.php');

header('Content-Type: application/json');

if (!isset($_SESSION['teacher_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

try {
    $db = new DbConnector();
    $student_id = intval($_GET['student_id']);
    $quiz_id = intval($_GET['quiz_id']);

    $query = "
        SELECT 
            qq.question_id,
            qq.question_text,
            qq.question_type,
            qq.points,
            sa.text_answer,
            CASE 
                WHEN qq.question_type = 'short_answer' THEN sa.text_answer
                ELSE (
                    SELECT choice_text 
                    FROM question_choices 
                    WHERE choice_id = sa.selected_choice_id
                )
            END as student_answer,
            CASE 
                WHEN qq.question_type = 'short_answer' THEN qa.answer_text
                ELSE (
                    SELECT choice_text 
                    FROM question_choices qc 
                    WHERE qc.question_id = qq.question_id 
                    AND qc.is_correct = 1 
                    LIMIT 1
                )
            END as correct_answer,
            sa.is_correct
        FROM quiz_questions qq
        LEFT JOIN student_answers sa ON qq.question_id = sa.question_id 
            AND sa.student_id = ? AND sa.quiz_id = ?
        LEFT JOIN quiz_answers qa ON qq.question_id = qa.question_id
        WHERE qq.quiz_id = ?
        ORDER BY qq.question_id
    ";

    $stmt = $db->prepare($query);
    $stmt->bind_param("iii", $student_id, $quiz_id, $quiz_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $answers = $result->fetch_all(MYSQLI_ASSOC);

    // Process the answers
    $processed_answers = array_map(function($answer) {
        // For short answer questions, use the text_answer directly
        if ($answer['question_type'] === 'short_answer') {
            $answer['student_answer'] = $answer['text_answer'] ?? 'No answer';
        }
        
        // Make sure we have valid answers to display
        $answer['student_answer'] = $answer['student_answer'] ?? 'No answer';
        $answer['correct_answer'] = $answer['correct_answer'] ?? 'No answer provided';
        
        return $answer;
    }, $answers);

    error_log("Raw answers from database: " . print_r($answers, true));
    error_log("Processed answers: " . print_r($processed_answers, true));

    echo json_encode([
        'success' => true,
        'answers' => $processed_answers
    ]);

} catch (Exception $e) {
    error_log("Error in get_student_answers.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?> 