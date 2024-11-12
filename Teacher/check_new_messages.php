<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['teacher_id'])) {
    http_response_code(401);
    exit(json_encode(['error' => 'Unauthorized']));
}

$db = new DbConnector();
$teacher_id = $_SESSION['teacher_id'];

// Check for unread messages
$query = "SELECT COUNT(*) as unread_count 
          FROM messages m
          JOIN conversations c ON m.conversation_id = c.conversation_id
          WHERE c.teacher_id = ? 
          AND m.receiver_id = ? 
          AND m.read_status = 0";

$stmt = $db->prepare($query);
$stmt->bind_param("ii", $teacher_id, $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

// Return JSON response
header('Content-Type: application/json');
echo json_encode([
    'hasNewMessages' => $data['unread_count'] > 0,
    'unreadCount' => $data['unread_count']
]);
?>
