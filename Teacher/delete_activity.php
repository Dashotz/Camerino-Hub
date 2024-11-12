<?php
session_start();
require_once('../db/dbConnector.php');

// Clear any previous output
ob_clean();

// Set JSON header
header('Content-Type: application/json');

try {
    // Check authentication
    if (!isset($_SESSION['teacher_id'])) {
        throw new Exception('Unauthorized access');
    }

    // Validate input
    if (!isset($_POST['activity_id'])) {
        throw new Exception('Activity ID is required');
    }

    $db = new DbConnector();
    $teacher_id = $_SESSION['teacher_id'];
    $activity_id = intval($_POST['activity_id']);

    // Start transaction
    $db->begin_transaction();

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

        // Delete files
        $stmt = $db->prepare("DELETE FROM activity_files WHERE activity_id = ?");
        $stmt->bind_param("i", $activity_id);
        $stmt->execute();

        // Delete submissions
        $stmt = $db->prepare("DELETE FROM student_activity_submissions WHERE activity_id = ?");
        $stmt->bind_param("i", $activity_id);
        $stmt->execute();

        // Delete activity
        $stmt = $db->prepare("DELETE FROM activities WHERE activity_id = ?");
        $stmt->bind_param("i", $activity_id);
        $stmt->execute();

        // Commit transaction
        $db->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Activity deleted successfully'
        ]);

    } catch (Exception $e) {
        $db->rollback();
        throw $e;
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

exit();
?>
