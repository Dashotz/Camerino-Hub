<?php
session_start();
require_once('../db/dbConnector.php');

header('Content-Type: application/json');

if (!isset($_SESSION['teacher_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

if (!isset($_POST['announcement_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing announcement ID']);
    exit;
}

$db = new DbConnector();
$teacher_id = $_SESSION['teacher_id'];
$announcement_id = (int)$_POST['announcement_id'];

// Verify ownership
$verify_query = "SELECT attachment FROM announcements WHERE id = ? AND teacher_id = ? LIMIT 1";
$stmt = $db->prepare($verify_query);
$stmt->bind_param("ii", $announcement_id, $teacher_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Delete the announcement (soft delete)
$delete_query = "UPDATE announcements SET status = 'deleted' WHERE id = ? AND teacher_id = ?";
$stmt = $db->prepare($delete_query);
$stmt->bind_param("ii", $announcement_id, $teacher_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error deleting announcement']);
}