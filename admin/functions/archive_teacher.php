<?php
session_start();
require_once('../../db/dbConnector.php');

header('Content-Type: application/json');

// Check for admin session
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Check for teacher ID
if (!isset($_POST['teacher_id']) || !is_numeric($_POST['teacher_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid teacher ID']);
    exit;
}

$db = new DbConnector();

try {
    $teacher_id = (int)$_POST['teacher_id'];
    $admin_id = (int)$_SESSION['admin_id'];
    
    $db->begin_transaction();
    
    // First check if teacher exists and is active
    $check_query = "SELECT teacher_id FROM teacher WHERE teacher_id = ? AND status = 'active'";
    $stmt = $db->prepare($check_query);
    $stmt->bind_param("i", $teacher_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Teacher not found or already archived');
    }
    
    // Update teacher status to archived
    $update_query = "UPDATE teacher 
                    SET status = 'archived',
                        archived_at = CURRENT_TIMESTAMP,
                        archived_by = ?
                    WHERE teacher_id = ? AND status = 'active'";
                    
    $stmt = $db->prepare($update_query);
    $stmt->bind_param("ii", $admin_id, $teacher_id);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to archive teacher');
    }
    
    // Update related records
    // 1. Archive section subjects
    $update_sections = "UPDATE section_subjects 
                       SET status = 'archived'
                       WHERE teacher_id = ? AND status = 'active'";
    $stmt = $db->prepare($update_sections);
    $stmt->bind_param("i", $teacher_id);
    $stmt->execute();
    
    // 2. Archive section adviser assignments
    $update_advisers = "UPDATE section_advisers 
                       SET status = 'archived'
                       WHERE teacher_id = ? AND status = 'active'";
    $stmt = $db->prepare($update_advisers);
    $stmt->bind_param("i", $teacher_id);
    $stmt->execute();
    
    // 3. Archive section schedules
    $update_schedules = "UPDATE section_schedules 
                        SET status = 'archived'
                        WHERE teacher_id = ? AND status = 'active'";
    $stmt = $db->prepare($update_schedules);
    $stmt->bind_param("i", $teacher_id);
    $stmt->execute();
    
    $db->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Teacher has been successfully archived',
        'teacher_id' => $teacher_id
    ]);
    
} catch (Exception $e) {
    $db->rollback();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($db)) {
        $db->close();
    }
}
?>
