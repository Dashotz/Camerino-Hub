<?php
session_start();
require_once('../../db/dbConnector.php');

if (!isset($_SESSION['teacher_id'])) {
    http_response_code(401);
    exit(json_encode(['success' => false, 'message' => 'Unauthorized']));
}

$data = json_decode(file_get_contents('php://input'), true);
$student_id = $data['student_id'] ?? null;
$quiz_id = $data['quiz_id'] ?? null;

if (!$student_id || !$quiz_id) {
    http_response_code(400);
    exit(json_encode(['success' => false, 'message' => 'Missing required data']));
}

$db = new DbConnector();

try {
    // Verify teacher owns this quiz
    $verify_query = "
        SELECT a.activity_id 
        FROM activities a
        JOIN section_subjects ss ON a.section_subject_id = ss.id
        WHERE a.activity_id = ? AND ss.teacher_id = ?";
    
    $stmt = $db->prepare($verify_query);
    $stmt->bind_param("ii", $quiz_id, $_SESSION['teacher_id']);
    $stmt->execute();
    
    if (!$stmt->get_result()->fetch_assoc()) {
        throw new Exception('Unauthorized access to this quiz');
    }

    // Start transaction
    $db->begin_transaction();

    // Delete student answers
    $delete_answers = "DELETE FROM student_answers 
                      WHERE student_id = ? AND quiz_id = ?";
    $stmt = $db->prepare($delete_answers);
    $stmt->bind_param("ii", $student_id, $quiz_id);
    $stmt->execute();

    // Delete the submission
    $delete_submission = "DELETE FROM student_activity_submissions 
                         WHERE student_id = ? AND activity_id = ?";
    $stmt = $db->prepare($delete_submission);
    $stmt->bind_param("ii", $student_id, $quiz_id);
    $stmt->execute();

    $db->commit();

    echo json_encode([
        'success' => true, 
        'message' => 'Quiz attempt reset successfully'
    ]);

} catch (Exception $e) {
    $db->rollback();
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
} finally {
    $db->close();
}
