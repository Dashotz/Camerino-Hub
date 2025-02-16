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

    $question_id = intval($_POST['question_id']);
    $quiz_id = intval($_POST['quiz_id']);

    // First, delete student answers for this question
    $delete_answers = "DELETE FROM student_answers WHERE question_id = ?";
    $stmt = $db->prepare($delete_answers);
    $stmt->bind_param("i", $question_id);
    $stmt->execute();

    // Delete from quiz_answers table for short answer questions
    $delete_quiz_answers = "DELETE FROM quiz_answers WHERE question_id = ?";
    $stmt = $db->prepare($delete_quiz_answers);
    $stmt->bind_param("i", $question_id);
    $stmt->execute();

    // Delete choices for multiple choice/true false questions
    $delete_choices = "DELETE FROM question_choices WHERE question_id = ?";
    $stmt = $db->prepare($delete_choices);
    $stmt->bind_param("i", $question_id);
    $stmt->execute();

    // Finally delete the question
    $delete_question = "DELETE FROM quiz_questions WHERE question_id = ? AND quiz_id = ?";
    $stmt = $db->prepare($delete_question);
    $stmt->bind_param("ii", $question_id, $quiz_id);
    $stmt->execute();

    $db->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    if (isset($db)) {
        $db->rollback();
    }
    error_log("Error deleting question: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

$db->close(); 