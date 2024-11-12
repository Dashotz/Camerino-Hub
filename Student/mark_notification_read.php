<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['id'])) {
    http_response_code(401);
    exit('Unauthorized');
}

$data = json_decode(file_get_contents('php://input'), true);
$notification_id = $data['notification_id'] ?? null;
$student_id = $_SESSION['id'];

if (!$notification_id) {
    http_response_code(400);
    exit('Missing notification ID');
}

$db = new DbConnector();
$query = "
    UPDATE notifications 
    SET is_read = 1 
    WHERE id = ? 
    AND user_id = ? 
    AND user_type = 'student'";

$stmt = $db->prepare($query);
$stmt->bind_param("ii", $notification_id, $student_id);

if ($stmt->execute()) {
    http_response_code(200);
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to mark notification as read']);
}
