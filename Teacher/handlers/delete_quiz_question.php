<?php
session_start();
require_once('../../db/dbConnector.php');

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['question_id'])) {
        throw new Exception('Question ID is required');
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
    if ($result->num_rows === 0) {
        throw new Exception('Unauthorized access');
    }
    $stmt->close();

    // Delete choices
    $stmt = $db->prepare("DELETE FROM question_choices WHERE question_id = ?");
    $stmt->bind_param("i", $data['question_id']);
    $stmt->execute();
    $stmt->close();

    // Delete answers
    $stmt = $db->prepare("DELETE FROM quiz_answers WHERE question_id = ?");
    $stmt->bind_param("i", $data['question_id']);
    $stmt->execute();
    $stmt->close();

    // Delete question
    $stmt = $db->prepare("DELETE FROM quiz_questions WHERE question_id = ?");
    $stmt->bind_param("i", $data['question_id']);
    $stmt->execute();
    $stmt->close();

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