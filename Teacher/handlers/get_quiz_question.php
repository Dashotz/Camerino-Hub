<?php
session_start();
require_once('../../db/dbConnector.php');

header('Content-Type: application/json');

try {
    if (!isset($_GET['question_id'])) {
        throw new Exception('Question ID is required');
    }

    $db = new DbConnector();
    
    // Get question data
    $query = "SELECT * FROM quiz_questions WHERE question_id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $_GET['question_id']);
    $stmt->execute();
    $question = $stmt->get_result()->fetch_assoc();
    
    if (!$question) {
        throw new Exception('Question not found');
    }
    
    // Get choices or answer based on type
    if ($question['question_type'] === 'multiple_choice' || $question['question_type'] === 'true_false') {
        $query = "SELECT * FROM question_choices WHERE question_id = ? ORDER BY choice_order";
        $stmt = $db->prepare($query);
        $stmt->bind_param("i", $_GET['question_id']);
        $stmt->execute();
        $question['choices'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    } else if ($question['question_type'] === 'short_answer') {
        $query = "SELECT answer_text FROM quiz_answers WHERE question_id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("i", $_GET['question_id']);
        $stmt->execute();
        $answer = $stmt->get_result()->fetch_assoc();
        $question['correct_answer'] = $answer['answer_text'];
    }
    
    // Validate teacher owns this question
    $validate_query = "SELECT 1 FROM section_subjects ss 
                      JOIN activities a ON ss.id = a.section_subject_id
                      JOIN quiz_questions qq ON a.activity_id = qq.quiz_id
                      WHERE ss.teacher_id = ? AND qq.question_id = ?";
    $stmt = $db->prepare($validate_query);
    $stmt->bind_param("ii", $_SESSION['teacher_id'], $_GET['question_id']);
    $stmt->execute();
    if ($stmt->get_result()->num_rows === 0) {
        throw new Exception('Unauthorized access');
    }
    
    echo json_encode(['success' => true, 'data' => $question]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} finally {
    if (isset($db)) {
        $db->close();
    }
} 