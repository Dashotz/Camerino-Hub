<?php
session_start();
require_once('../db/dbConnector.php');

header('Content-Type: application/json');

if (!isset($_SESSION['id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'student') {
    echo json_encode(['status' => 'logged_out']);
    exit();
}

$db = new DbConnector();
$student_id = $_SESSION['id'];

// Check session validity
$check_query = "SELECT session_id, user_online, last_activity 
                FROM student 
                WHERE student_id = ? AND status = 'active'";
$stmt = $db->prepare($check_query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$current_session = $result->fetch_assoc();

if (!$current_session || 
    $current_session['user_online'] != 1 || 
    $current_session['session_id'] !== session_id()) {
    
    // Clear the invalid session
    $update_status = "UPDATE student 
                     SET user_online = 0, 
                         session_id = NULL, 
                         last_activity = NULL 
                     WHERE student_id = ?";
    $stmt = $db->prepare($update_status);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    
    session_destroy();
    echo json_encode(['status' => 'logged_out']);
    exit();
}

// Update last activity timestamp
$update_activity = "UPDATE student 
                   SET last_activity = NOW() 
                   WHERE student_id = ?";
$stmt = $db->prepare($update_activity);
$stmt->bind_param("i", $student_id);
$stmt->execute();

echo json_encode(['status' => 'active']);
?>
