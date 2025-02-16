<?php
session_start();
require_once('../../db/dbConnector.php');

header('Content-Type: application/json');

if (!isset($_SESSION['teacher_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

try {
    $db = new DbConnector();
    $db->begin_transaction();

    $quiz_id = intval($_POST['activity_id']);
    $teacher_id = $_SESSION['teacher_id'];

    // Update basic quiz details
    $update_quiz = "UPDATE activities SET 
        title = ?,
        description = ?,
        section_subject_id = ?,
        due_date = ?,
        quiz_duration = ?,
        prevent_tab_switch = ?,
        fullscreen_required = ?
        WHERE activity_id = ? AND type = 'quiz'";

    $stmt = $db->prepare($update_quiz);
    $prevent_tab_switch = isset($_POST['prevent_tab_switch']) ? 1 : 0;
    $fullscreen_required = isset($_POST['fullscreen_required']) ? 1 : 0;
    
    $stmt->bind_param("ssissiii", 
        $_POST['title'],
        $_POST['description'],
        $_POST['section_subject_id'],
        $_POST['due_date'],
        $_POST['quiz_duration'],
        $prevent_tab_switch,
        $fullscreen_required,
        $quiz_id
    );
    
    $stmt->execute();

    $db->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    if (isset($db)) {
        $db->rollback();
    }
    error_log("Quiz update error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} finally {
    if (isset($db)) {
        $db->close();
    }
} 