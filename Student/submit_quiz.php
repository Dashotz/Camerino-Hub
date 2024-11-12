<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['id'])) {
    http_response_code(401);
    exit('Unauthorized');
}

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);
$quiz_id = $data['quiz_id'] ?? null;
$submitted_at = $data['submitted_at'] ?? date('Y-m-d H:i:s');
$student_id = $_SESSION['id'];

if (!$quiz_id) {
    http_response_code(400);
    exit('Missing quiz ID');
}

$db = new DbConnector();

// Check if already submitted
$check_query = "
    SELECT submission_id 
    FROM student_activity_submissions 
    WHERE student_id = ? AND activity_id = ?";
$stmt = $db->prepare($check_query);
$stmt->bind_param("ii", $student_id, $quiz_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    http_response_code(400);
    exit('Quiz already submitted');
}

// Insert submission
$insert_query = "
    INSERT INTO student_activity_submissions 
    (student_id, activity_id, submitted_at) 
    VALUES (?, ?, ?)";
$stmt = $db->prepare($insert_query);
$stmt->bind_param("iis", $student_id, $quiz_id, $submitted_at);

if ($stmt->execute()) {
    http_response_code(200);
    echo json_encode(['message' => 'Quiz submitted successfully']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to submit quiz']);
}
