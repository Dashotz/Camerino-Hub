<?php
session_start();
require_once('../../db/dbConnector.php');

if (!isset($_SESSION['id']) || !isset($_POST['section_subject_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

$db = new DbConnector();
$student_id = $_SESSION['id'];
$section_subject_id = $_POST['section_subject_id'];
$current_time = date('H:i:s');
$current_hour = (int)date('H');

// Check if current time is between 6 AM and 12 PM
if ($current_hour < 6 || $current_hour >= 12) {
    echo json_encode(['success' => false, 'message' => 'Attendance can only be marked between 6 AM and 12 PM']);
    exit();
}

try {
    // Check if attendance already marked
    $check_query = "SELECT id FROM attendance 
        WHERE student_id = ? 
        AND section_subject_id = ? 
        AND date = CURRENT_DATE()";
    $stmt = $db->prepare($check_query);
    $stmt->bind_param("ii", $student_id, $section_subject_id);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Attendance already marked for today']);
        exit();
    }
    
    // Mark attendance
    $insert_query = "INSERT INTO attendance 
        (student_id, section_subject_id, date, status, time_in, time_status) 
        VALUES (?, ?, CURRENT_DATE(), 'present', ?, 'present')";
    
    $stmt = $db->prepare($insert_query);
    $stmt->bind_param("iis", $student_id, $section_subject_id, $current_time);
    $stmt->execute();
    
    echo json_encode([
        'success' => true,
        'time' => date('h:i A', strtotime($current_time))
    ]);
    
} catch (Exception $e) {
    error_log("Error marking attendance: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error']);
}
?> 