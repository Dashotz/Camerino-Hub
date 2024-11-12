<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['teacher_id'])) {
    http_response_code(401);
    exit(json_encode(['success' => false, 'message' => 'Unauthorized']));
}

$data = json_decode(file_get_contents('php://input'), true);
$submission_id = $data['submission_id'] ?? null;
$points = $data['points'] ?? null;
$feedback = $data['feedback'] ?? null;

if (!$submission_id || !isset($points)) {
    http_response_code(400);
    exit(json_encode(['success' => false, 'message' => 'Missing required fields']));
}

$db = new DbConnector();

// Verify teacher owns this submission
$verify_query = "
    SELECT a.activity_id 
    FROM student_activity_submissions sas
    JOIN activities a ON sas.activity_id = a.activity_id
    JOIN section_subjects ss ON a.section_subject_id = ss.id
    WHERE sas.submission_id = ? AND ss.teacher_id = ?";

$stmt = $db->prepare($verify_query);
$stmt->bind_param("ii", $submission_id, $_SESSION['teacher_id']);
$stmt->execute();

if ($stmt->get_result()->num_rows === 0) {
    http_response_code(403);
    exit(json_encode(['success' => false, 'message' => 'Unauthorized']));
}

// Update grade
$update_query = "
    UPDATE student_activity_submissions 
    SET points = ?, feedback = ? 
    WHERE submission_id = ?";

$stmt = $db->prepare($update_query);
$stmt->bind_param("isi", $points, $feedback, $submission_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to save grade']);
}
