<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['teacher_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

$db = new DbConnector();
$teacher_id = $_SESSION['teacher_id'];
$activity_id = $_POST['activity_id'];

// First verify teacher owns this activity
$verify_query = "SELECT a.activity_id, a.status 
                FROM activities a 
                JOIN section_subjects ss ON a.section_subject_id = ss.id 
                WHERE a.activity_id = ? AND ss.teacher_id = ?";

$stmt = $db->prepare($verify_query);
$stmt->bind_param("ii", $activity_id, $teacher_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access to this activity']);
    exit();
}

$activity = $result->fetch_assoc();
$current_status = $activity['status'];

// Determine new status
$new_status = match($current_status) {
    'active' => 'archived',
    'archived' => 'active',
    'inactive' => 'active',
    default => 'active'
};

// Update status
$update_query = "UPDATE activities SET status = ? WHERE activity_id = ?";
$stmt = $db->prepare($update_query);
$stmt->bind_param("si", $new_status, $activity_id);

if ($stmt->execute()) {
    $message = match($new_status) {
        'archived' => 'Activity archived successfully',
        'active' => 'Activity restored successfully',
        default => 'Status updated successfully'
    };
    
    echo json_encode([
        'success' => true,
        'message' => $message,
        'new_status' => $new_status
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to update activity status'
    ]);
}
?>
