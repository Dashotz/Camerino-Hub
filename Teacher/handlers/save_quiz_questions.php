<?php
session_start();
require_once('../../db/dbConnector.php');

header('Content-Type: application/json');

if (!isset($_SESSION['teacher_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['questions'])) {
        throw new Exception('No questions data received');
    }

    $db = new DbConnector();
    $db->begin_transaction();

    foreach ($data['questions'] as $question) {
        // Update question text and points
        $update_query = "UPDATE quiz_questions 
                        SET question_text = ?, points = ? 
                        WHERE question_id = ?";
        $stmt = $db->prepare($update_query);
        $stmt->bind_param('sii', 
            $question['question_text'],
            $question['points'],
            $question['question_id']
        );
        $stmt->execute();

        // Update answers based on question type
        if ($question['type'] === 'multiple_choice') {
            foreach ($question['choices'] as $choice) {
                $update_choice = "UPDATE question_choices 
                                SET choice_text = ?, is_correct = ? 
                                WHERE choice_id = ?";
                $stmt = $db->prepare($update_choice);
                $stmt->bind_param('sii',
                    $choice['text'],
                    $choice['is_correct'],
                    $choice['choice_id']
                );
                $stmt->execute();
            }
        } else if ($question['type'] === 'short_answer') {
            $update_answer = "UPDATE quiz_answers 
                            SET answer_text = ? 
                            WHERE question_id = ?";
            $stmt = $db->prepare($update_answer);
            $stmt->bind_param('si',
                $question['correct_answer'],
                $question['question_id']
            );
            $stmt->execute();
        }
    }

    $db->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    if (isset($db)) {
        $db->rollback();
    }
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} 