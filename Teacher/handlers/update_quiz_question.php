<?php
session_start();
require_once('../../db/dbConnector.php');

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    error_log('Update request data: ' . print_r($data, true));
    
    if (!isset($data['question_id']) || !isset($data['text']) || !isset($data['type'])) {
        throw new Exception('Missing required fields');
    }

    $db = new DbConnector();
    $db->begin_transaction();

    // Validate teacher owns this question
    $validate_query = "SELECT 1 FROM section_subjects ss 
                      JOIN activities a ON ss.id = a.section_subject_id
                      JOIN quiz_questions qq ON a.activity_id = qq.quiz_id
                      WHERE ss.teacher_id = ? AND qq.question_id = ?";
    $stmt = $db->prepare($validate_query);
    $stmt->bind_param("ii", $_SESSION['teacher_id'], $data['question_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    error_log('Validation result rows: ' . $result->num_rows);
    if ($result->num_rows === 0) {
        throw new Exception('Unauthorized access');
    }
    $stmt->close();

    // Update question
    $update_query = "UPDATE quiz_questions SET question_text = ?, question_type = ? WHERE question_id = ?";
    $stmt = $db->prepare($update_query);
    $text = strip_tags($data['text']);
    $type = in_array($data['type'], ['multiple_choice', 'true_false', 'short_answer']) ? $data['type'] : 'multiple_choice';
    error_log('Updating question - Text: ' . $text . ', Type: ' . $type . ', ID: ' . $data['question_id']);
    $stmt->bind_param("ssi", $text, $type, $data['question_id']);
    if (!$stmt->execute()) {
        error_log('Failed to update question: ' . $stmt->error);
        throw new Exception('Failed to update question');
    }
    $stmt->close();

    // Delete old choices/answers
    $stmt = $db->prepare("DELETE FROM question_choices WHERE question_id = ?");
    $stmt->bind_param("i", $data['question_id']);
    $stmt->execute();
    $stmt->close();
    
    $stmt = $db->prepare("DELETE FROM quiz_answers WHERE question_id = ?");
    $stmt->bind_param("i", $data['question_id']);
    $stmt->execute();
    $stmt->close();

    // Add new choices/answers based on type
    if ($data['type'] === 'multiple_choice' && isset($data['choices'])) {
        foreach ($data['choices'] as $index => $choice) {
            $choice_query = "INSERT INTO question_choices (question_id, choice_text, is_correct, choice_order) 
                           VALUES (?, ?, ?, ?)";
            $stmt = $db->prepare($choice_query);
            $stmt->bind_param("isii", 
                $data['question_id'],
                $choice['text'],
                $choice['is_correct'],
                $index + 1
            );
            if (!$stmt->execute()) {
                throw new Exception('Failed to save choice');
            }
        }
    } else if ($data['type'] === 'true_false') {
        // Add True choice
        $stmt = $db->prepare("INSERT INTO question_choices (question_id, choice_text, is_correct, choice_order) VALUES (?, 'True', ?, 1)");
        $is_true = $data['correct_tf'] === 'true' ? 1 : 0;
        $stmt->bind_param("ii", $data['question_id'], $is_true);
        $stmt->execute();

        // Add False choice
        $stmt = $db->prepare("INSERT INTO question_choices (question_id, choice_text, is_correct, choice_order) VALUES (?, 'False', ?, 2)");
        $is_false = $data['correct_tf'] === 'false' ? 1 : 0;
        $stmt->bind_param("ii", $data['question_id'], $is_false);
        $stmt->execute();
    } else if ($data['type'] === 'short_answer') {
        $stmt = $db->prepare("INSERT INTO quiz_answers (question_id, answer_text) VALUES (?, ?)");
        $stmt->bind_param("is", $data['question_id'], $data['correct_answer']);
        $stmt->execute();
    }

    $db->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    if (isset($db)) {
        $db->rollback();
    }
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} finally {
    if (isset($db)) {
        $db->close();
    }
} 