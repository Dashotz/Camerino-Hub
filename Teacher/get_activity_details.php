<?php
session_start();
require_once('../db/dbConnector.php');

// Ensure no output before headers
ob_start();

// Set proper headers
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    if (!isset($_SESSION['teacher_id'])) {
        throw new Exception('Unauthorized access');
    }

    if (!isset($_GET['id'])) {
        throw new Exception('Activity ID is required');
    }

    $db = new DbConnector();
    $teacher_id = $_SESSION['teacher_id'];
    $activity_id = intval($_GET['id']);

    // Debug log
    error_log("Fetching activity ID: $activity_id for teacher: $teacher_id");

    $query = "SELECT 
                a.activity_id,
                a.title,
                a.description,
                a.type,
                a.due_date,
                a.points,
                a.status,
                ss.id as section_subject_id
              FROM activities a
              JOIN section_subjects ss ON a.section_subject_id = ss.id
              WHERE a.activity_id = ? 
              AND ss.teacher_id = ?
              LIMIT 1";

    $stmt = $db->prepare($query);
    
    if (!$stmt) {
        throw new Exception("Query preparation failed: " . $db->error);
    }

    $stmt->bind_param("ii", $activity_id, $teacher_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Query execution failed: " . $stmt->error);
    }

    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("Activity not found");
    }

    $activity = $result->fetch_assoc();
    
    // Format date
    $activity['due_date'] = date('Y-m-d\TH:i', strtotime($activity['due_date']));

    // Clear any output buffers
    ob_clean();

    // Return JSON response
    echo json_encode([
        'success' => true,
        'activity' => $activity
    ]);

} catch (Exception $e) {
    // Log the error
    error_log("Error in get_activity_details.php: " . $e->getMessage());
    
    // Clear any output buffers
    ob_clean();
    
    // Return error response
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// End output buffering
ob_end_flush();
exit;
?> 