<?php
session_start();
require_once('../db/dbConnector.php');

header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

if (!isset($_POST['announcement_id']) || !isset($_POST['comment']) || trim($_POST['comment']) === '') {
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit();
}

try {
    $db = new DbConnector();
    $student_id = $_SESSION['id'];
    $announcement_id = (int)$_POST['announcement_id'];
    $comment = trim($_POST['comment']);

    $query = "INSERT INTO announcement_comments 
              (announcement_id, student_id, comment, created_at) 
              VALUES (?, ?, ?, CURRENT_TIMESTAMP)";
    
    $stmt = $db->prepare($query);
    $stmt->bind_param("iis", $announcement_id, $student_id, $comment);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception("Error submitting comment");
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
