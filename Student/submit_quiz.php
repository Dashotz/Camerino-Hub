<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['id'])) {
    header('Content-Type: application/json');
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);
$quiz_id = $data['quiz_id'] ?? null;
$submitted_at = $data['submitted_at'] ?? date('Y-m-d H:i:s');
$student_id = $_SESSION['id'];
$security_violation = $data['security_violation'] ?? false;
$violation_count = $data['violation_count'] ?? 0;
$time_spent = $data['time_spent'] ?? null;

if (!$quiz_id) {
    header('Content-Type: application/json');
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing quiz ID']);
    exit();
}

$db = new DbConnector();

try {
    $db->begin_transaction();

    // Check if already submitted
    $check_query = "
        SELECT submission_id, status 
        FROM student_activity_submissions 
        WHERE student_id = ? AND activity_id = ?";
    $stmt = $db->prepare($check_query);
    $stmt->bind_param("ii", $student_id, $quiz_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $submission = $result->fetch_assoc();
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false, 
            'error' => 'Quiz already submitted',
            'status' => $submission['status']
        ]);
        exit();
    }

    // Set status based on security violation
    $status = 'submitted';
    $score = null;
    $remarks = null;

    // Handle security violations
    if ($security_violation && $violation_count >= 3) {
        $status = 'missing';
        $score = 0;
        $remarks = 'Quiz marked as missing due to security violations (3 tab switches detected)';
        
        // Log the security violation
        $violation_query = "INSERT INTO security_violations (student_id, quiz_id, violation_type, details) 
                          VALUES (?, ?, 'tab_switch', ?)";
        $stmt = $db->prepare($violation_query);
        $details = "Multiple tab switches detected ($violation_count times)";
        $stmt->bind_param("iis", $student_id, $quiz_id, $details);
        $stmt->execute();
    }

    // Insert submission with all required fields
    $insert_query = "
        INSERT INTO student_activity_submissions (
            student_id,
            activity_id,
            score,
            time_spent,
            submitted_at,
            status,
            remarks,
            security_violation
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $db->prepare($insert_query);
    $stmt->bind_param("iiiisssi", 
        $student_id, 
        $quiz_id, 
        $score,
        $time_spent,
        $submitted_at,
        $status,
        $remarks,
        $security_violation
    );

    if (!$stmt->execute()) {
        throw new Exception("Failed to insert submission: " . $stmt->error);
    }

    $db->commit();
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true, 
        'message' => $security_violation ? 'Quiz submitted with violations' : 'Quiz submitted successfully',
        'status' => $status
    ]);

} catch (Exception $e) {
    $db->rollback();
    error_log("Quiz submission error: " . $e->getMessage());
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'error' => $e->getMessage()
    ]);
} finally {
    $db->close();
}
