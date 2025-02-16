<?php
session_start();
require_once('../db/dbConnector.php');

if (isset($_SESSION['id'])) {
    $db = new DbConnector();
    $student_id = $_SESSION['id'];
    $currentTimestamp = date('YmdHis');
    
    $query = "UPDATE student SET 
              last_activity = ?, 
              session_id = ? 
              WHERE student_id = ?";
              
    $stmt = $db->prepare($query);
    $stmt->bind_param('ssi', $currentTimestamp, session_id(), $student_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false]);
}
?> 