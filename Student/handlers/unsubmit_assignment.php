<?php
session_start();
require_once('../../db/dbConnector.php');

header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

if (!isset($_POST['assignment_id'])) {
    echo json_encode(['success' => false, 'message' => 'Assignment ID is required']);
    exit();
}

$db = new DbConnector();
$student_id = $_SESSION['id'];
$assignment_id = $_POST['assignment_id'];

// Check if the assignment exists and get its details
$check_query = "
    SELECT 
        a.due_date,
        a.status as assignment_status,
        sas.points,
        sas.status as submission_status,
        sas.late_submission
    FROM activities a
    JOIN section_subjects ss ON a.section_subject_id = ss.id
    JOIN student_sections sts ON ss.section_id = sts.section_id
    LEFT JOIN student_activity_submissions sas ON a.activity_id = sas.activity_id 
        AND sas.student_id = ?
    WHERE a.activity_id = ? 
    AND sts.student_id = ?
    AND a.status = 'active'
    AND a.type = 'assignment'
    LIMIT 1";

try {
    $stmt = $db->prepare($check_query);
    $stmt->bind_param("iii", $student_id, $assignment_id, $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('Assignment not found');
    }

    $assignment = $result->fetch_assoc();

    // Check various conditions
    if (strtotime($assignment['due_date']) < time()) {
        throw new Exception('Cannot unsubmit after due date');
    }

    if (isset($assignment['points'])) {
        throw new Exception('Cannot unsubmit graded assignments');
    }

    if ($assignment['late_submission']) {
        throw new Exception('Cannot unsubmit late submissions');
    }

    if ($assignment['submission_status'] === 'graded') {
        throw new Exception('Cannot unsubmit graded submissions');
    }

    // Begin transaction
    $db->begin_transaction();

    // Delete submission files
    $files_query = "SELECT file_path FROM submission_files WHERE submission_id IN (
        SELECT submission_id FROM student_activity_submissions 
        WHERE activity_id = ? AND student_id = ?)";
    $stmt = $db->prepare($files_query);
    $stmt->bind_param("ii", $assignment_id, $student_id);
    $stmt->execute();
    $files = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    foreach ($files as $file) {
        if (file_exists('../../' . $file['file_path'])) {
            unlink('../../' . $file['file_path']);
        }
    }

    // Delete records
    $delete_files = "DELETE FROM submission_files WHERE submission_id IN (
        SELECT submission_id FROM student_activity_submissions 
        WHERE activity_id = ? AND student_id = ?)";
    $stmt = $db->prepare($delete_files);
    $stmt->bind_param("ii", $assignment_id, $student_id);
    $stmt->execute();

    $delete_submission = "DELETE FROM student_activity_submissions 
        WHERE activity_id = ? AND student_id = ?";
    $stmt = $db->prepare($delete_submission);
    $stmt->bind_param("ii", $assignment_id, $student_id);
    $stmt->execute();

    $db->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollback();
    }
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?> 