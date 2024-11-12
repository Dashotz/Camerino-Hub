<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['teacher_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new DbConnector();
    
    $teacher_id = $_SESSION['teacher_id'];
    $course_id = $db->escapeString($_POST['course_id']);
    $subject_id = $db->escapeString($_POST['subject_id']);
    
    $query = "INSERT INTO classes (teacher_id, course_id, subject_id) 
              VALUES ('$teacher_id', '$course_id', '$subject_id')";
    
    if ($db->query($query)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create class']);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>
