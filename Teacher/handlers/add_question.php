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
    $db->begin_transaction();

    $quiz_id = intval($_POST['quiz_id']);
    $question_type = $_POST['question_type'];
    $question_text = $_POST['question_text'];
    $question_order = intval($_POST['question_order']);

    // Verify the quiz belongs to this teacher
    $check_quiz = $db->prepare("
        SELECT 1 FROM activities a 
        JOIN section_subjects ss ON a.section_subject_id = ss.id 
        WHERE a.activity_id = ? AND ss.teacher_id = ?
    ");
    $check_quiz->bind_param("ii", $quiz_id, $_SESSION['teacher_id']);
    $check_quiz->execute();
    
    if ($check_quiz->get_result()->num_rows === 0) {
        throw new Exception("Unauthorized access");
    }

    // Insert new question
    $insert_question = $db->prepare("
        INSERT INTO quiz_questions (quiz_id, question_type, question_text, question_order) 
        VALUES (?, ?, ?, ?)
    ");
    $insert_question->bind_param("issi", $quiz_id, $question_type, $question_text, $question_order);
    $insert_question->execute();

    $question_id = $db->insert_id;

    // Handle different question types
    switch ($question_type) {
        case 'multiple_choice':
            // Add default choices
            $choices = [
                ['Choice 1', 1], // First choice is correct by default
                ['Choice 2', 0],
                ['Choice 3', 0],
                ['Choice 4', 0]
            ];
            
            $insert_choice = $db->prepare("
                INSERT INTO question_choices (question_id, choice_text, is_correct, choice_order) 
                VALUES (?, ?, ?, ?)
            ");
            
            foreach ($choices as $index => $choice) {
                $insert_choice->bind_param("isii", $question_id, $choice[0], $choice[1], $index + 1);
                $insert_choice->execute();
            }
            break;

        case 'true_false':
            // Add True/False choices
            $insert_choice = $db->prepare("
                INSERT INTO question_choices (question_id, choice_text, is_correct, choice_order) 
                VALUES (?, 'True', 1, 1), (?, 'False', 0, 2)
            ");
            $insert_choice->bind_param("ii", $question_id, $question_id);
            $insert_choice->execute();
            break;

        case 'short_answer':
            // Add empty default answer
            $insert_answer = $db->prepare("
                INSERT INTO quiz_answers (question_id, answer_text) 
                VALUES (?, 'Answer')
            ");
            $insert_answer->bind_param("i", $question_id);
            $insert_answer->execute();
            break;
    }

    $db->commit();
    echo json_encode(['success' => true, 'question_id' => $question_id]);

} catch (Exception $e) {
    if (isset($db)) {
        $db->rollback();
    }
    error_log("Add question error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} finally {
    if (isset($db)) {
        $db->close();
    }
} 