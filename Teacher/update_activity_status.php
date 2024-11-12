<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['teacher_id'])) {
    die(json_encode(['success' => false, 'message' => 'Not authenticated']));
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$activity_id = $data['activity_id'] ?? null;
$new_status = $data['status'] ?? null;

if (!$activity_id || !$new_status) {
    die(json_encode(['success' => false, 'message' => 'Missing required data']));
}

$db = new DbConnector();

// Verify teacher owns this activity
$verify_query = "
    SELECT a.activity_id 
    FROM activities a
    JOIN section_subjects ss ON a.section_subject_id = ss.id
    WHERE a.activity_id = ? AND ss.teacher_id = ?";

$stmt = $db->prepare($verify_query);
$stmt->bind_param("ii", $activity_id, $_SESSION['teacher_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die(json_encode(['success' => false, 'message' => 'Unauthorized access']));
}

// Update activity status
$update_query = "UPDATE activities SET status = ? WHERE activity_id = ?";
$stmt = $db->prepare($update_query);
$stmt->bind_param("si", $new_status, $activity_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update activity status']);
} 