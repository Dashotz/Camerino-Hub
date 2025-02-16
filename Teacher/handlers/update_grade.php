<?php
session_start();
require_once('../../db/dbConnector.php');
header('Content-Type: application/json');

if (!isset($_SESSION['teacher_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$db = new DbConnector();

$submission_id = $_POST['submission_id'] ?? 0;
$points = $_POST['points'] ?? 0;

try {
    $update_query = "UPDATE student_activity_submissions 
                    SET points = ?, 
                        graded_by = ?,
                        graded_at = NOW(),
                        status = 'graded'
                    WHERE submission_id = ?";
    
    $stmt = $db->prepare($update_query);
    $stmt->bind_param("dii", $points, $_SESSION['teacher_id'], $submission_id);
    $success = $stmt->execute();

    echo json_encode(['success' => $success]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
