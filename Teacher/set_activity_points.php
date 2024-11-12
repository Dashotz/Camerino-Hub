<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['teacher_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

if (!isset($_POST['activity_id'], $_POST['points'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit();
}

$db = new DbConnector();
$teacher_id = $_SESSION['teacher_id'];
$activity_id = $_POST['activity_id'];
$points = $_POST['points'];

// Verify ownership
$verify_query = "SELECT teacher_id FROM activities WHERE activity_id = ?";
$stmt = $db->prepare($verify_query);
$stmt->bind_param("i", $activity_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

if (!$result || $result['teacher_id'] != $teacher_id) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Update points
$update_query = "UPDATE activities SET points = ? WHERE activity_id = ?";
$stmt = $db->prepare($update_query);
$stmt->bind_param("ii", $points, $activity_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>
