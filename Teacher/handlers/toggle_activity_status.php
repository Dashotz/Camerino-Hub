<?php
session_start();
require_once('../../db/dbConnector.php');

header('Content-Type: application/json');

if (!isset($_SESSION['teacher_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit();
}

if (!isset($_POST['activity_id']) || !isset($_POST['action'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit();
}

$db = new DbConnector();
$activity_id = intval($_POST['activity_id']);
$teacher_id = $_SESSION['teacher_id'];
$action = $_POST['action'];

$new_status = ($action === 'archive') ? 'archived' : 'active';

try {
    // Verify ownership
    $verify_query = "SELECT a.activity_id 
                    FROM activities a
                    JOIN section_subjects ss ON a.section_subject_id = ss.id
                    WHERE a.activity_id = ? AND ss.teacher_id = ?";
    
    $stmt = $db->prepare($verify_query);
    $stmt->bind_param("ii", $activity_id, $teacher_id);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows === 0) {
        throw new Exception('Activity not found or unauthorized');
    }

    // Update status
    $update_query = "UPDATE activities SET status = ? WHERE activity_id = ?";
    $stmt = $db->prepare($update_query);
    $stmt->bind_param("si", $new_status, $activity_id);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to update activity status');
    }

    echo json_encode(['success' => true, 'message' => 'Activity status updated successfully']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$db->close();
