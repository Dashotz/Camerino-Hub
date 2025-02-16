<?php
session_start();
require_once('../../db/dbConnector.php');

// Set JSON content type header
header('Content-Type: application/json');

// Check authentication
if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

// Validate activity_id
if (!isset($_POST['activity_id']) || !is_numeric($_POST['activity_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid activity ID']);
    exit();
}

$db = new DbConnector();
$student_id = $_SESSION['id'];
$activity_id = intval($_POST['activity_id']);

// Check if the activity exists and get its details
$check_query = "
    SELECT 
        a.due_date,
        a.status as activity_status,
        sas.points,
        sas.status as submission_status,
        sas.late_submission,
        sas.submission_id
    FROM activities a
    JOIN section_subjects ss ON a.section_subject_id = ss.id
    JOIN student_sections sts ON ss.section_id = sts.section_id
    LEFT JOIN student_activity_submissions sas ON a.activity_id = sas.activity_id 
        AND sas.student_id = ?
    WHERE a.activity_id = ? 
    AND sts.student_id = ?
    AND a.status = 'active'
    LIMIT 1";

try {
    $stmt = $db->prepare($check_query);
    if (!$stmt) {
        throw new Exception("Failed to prepare query");
    }

    $stmt->bind_param("iii", $student_id, $activity_id, $student_id);
    if (!$stmt->execute()) {
        throw new Exception("Failed to execute query");
    }

    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Activity not found or not accessible']);
        exit();
    }

    $activity = $result->fetch_assoc();

    // Validate submission conditions
    if (!isset($activity['submission_id'])) {
        echo json_encode(['success' => false, 'message' => 'No submission found to unsubmit']);
        exit();
    }

    if (strtotime($activity['due_date']) < time()) {
        echo json_encode(['success' => false, 'message' => 'Cannot unsubmit after due date']);
        exit();
    }

    if (isset($activity['points'])) {
        echo json_encode(['success' => false, 'message' => 'Cannot unsubmit graded activities']);
        exit();
    }

    if ($activity['late_submission']) {
        echo json_encode(['success' => false, 'message' => 'Cannot unsubmit late submissions']);
        exit();
    }

    if ($activity['submission_status'] === 'graded') {
        echo json_encode(['success' => false, 'message' => 'Cannot unsubmit graded submissions']);
        exit();
    }

    // Begin transaction
    $db->begin_transaction();

    // Delete files and records
    $files_query = "SELECT file_path FROM submission_files WHERE submission_id = ?";
    $stmt = $db->prepare($files_query);
    $stmt->bind_param("i", $activity['submission_id']);
    $stmt->execute();
    $files = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Delete physical files
    foreach ($files as $file) {
        $filepath = '../../' . $file['file_path'];
        if (file_exists($filepath)) {
            if (!unlink($filepath)) {
                throw new Exception("Failed to delete file: " . $file['file_path']);
            }
        }
    }

    // Delete database records
    $delete_files = "DELETE FROM submission_files WHERE submission_id = ?";
    $stmt = $db->prepare($delete_files);
    $stmt->bind_param("i", $activity['submission_id']);
    if (!$stmt->execute()) {
        throw new Exception("Failed to delete submission files");
    }

    $delete_submission = "DELETE FROM student_activity_submissions WHERE submission_id = ?";
    $stmt = $db->prepare($delete_submission);
    $stmt->bind_param("i", $activity['submission_id']);
    if (!$stmt->execute()) {
        throw new Exception("Failed to delete submission");
    }

    // Commit transaction
    $db->commit();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Activity unsubmitted successfully'
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    if ($db->inTransaction()) {
        $db->rollback();
    }
    error_log("Unsubmit activity error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Failed to unsubmit activity. Please try again later.'
    ]);
}
?> 