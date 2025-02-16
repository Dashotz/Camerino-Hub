<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['id'])) {
    http_response_code(401);
    exit('Unauthorized');
}

$data = json_decode(file_get_contents('php://input'), true);
$student_id = $_SESSION['id'];
$quiz_id = $data['quiz_id'] ?? null;
$violation_type = $data['violation_type'] ?? null;

if (!$quiz_id || !$violation_type) {
    http_response_code(400);
    exit('Missing required data');
}

$db = new DbConnector();

try {
    // Count existing violations
    $count_query = "SELECT COUNT(*) as count FROM security_violations 
                   WHERE student_id = ? AND quiz_id = ? AND violation_type = 'tab_switch'";
    $stmt = $db->prepare($count_query);
    $stmt->bind_param("ii", $student_id, $quiz_id);
    $stmt->execute();
    $count = $stmt->get_result()->fetch_assoc()['count'];

    // Record the violation
    $query = "INSERT INTO security_violations (
        student_id, 
        quiz_id, 
        violation_type, 
        details
    ) VALUES (?, ?, ?, ?)";
    
    $details = json_encode([
        'timestamp' => date('Y-m-d H:i:s'),
        'user_agent' => $_SERVER['HTTP_USER_AGENT'],
        'violation_count' => $count + 1
    ]);
    
    $stmt = $db->prepare($query);
    $stmt->bind_param("iiss", $student_id, $quiz_id, $violation_type, $details);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'violation_count' => $count + 1]);
    } else {
        throw new Exception($stmt->error);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

$db->close();
