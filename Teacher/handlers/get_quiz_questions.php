<?php
session_start();
require_once('../../db/dbConnector.php');

header('Content-Type: application/json');

if (!isset($_SESSION['teacher_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

try {
    $quiz_id = $_GET['quiz_id'] ?? null;
    if (!$quiz_id) {
        throw new Exception('Quiz ID is required');
    }

    $db = new DbConnector();
    
    $query = "SELECT 
        q.question_id,
        q.question_text,
        q.question_type,
        q.points,
        CASE 
            WHEN q.question_type IN ('multiple_choice', 'true_false') THEN
                GROUP_CONCAT(
                    CONCAT(qc.choice_id, ':', qc.choice_text, ':', qc.is_correct)
                    ORDER BY qc.choice_order
                    SEPARATOR '|'
                )
            ELSE
                (SELECT answer_text FROM quiz_answers WHERE question_id = q.question_id)
        END as answer_data
    FROM quiz_questions q
    LEFT JOIN question_choices qc ON q.question_id = qc.question_id
    WHERE q.quiz_id = ?
    GROUP BY q.question_id
    ORDER BY q.question_order";

    $stmt = $db->prepare($query);
    $stmt->bind_param('i', $quiz_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $questions = [];
    while ($row = $result->fetch_assoc()) {
        $questions[] = $row;
    }

    echo json_encode(['success' => true, 'questions' => $questions]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} 