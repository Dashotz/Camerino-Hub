<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['teacher_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

$db = new DbConnector();
$teacher_id = $_SESSION['teacher_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $activity_id = $_POST['activity_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];
    $points = $_POST['points'];
    $type = $_POST['type'];
    $section_subject_id = $_POST['section_subject_id'];

    // Verify teacher owns this activity
    $verify_query = "SELECT a.activity_id 
                    FROM activities a 
                    JOIN section_subjects ss ON a.section_subject_id = ss.id 
                    WHERE a.activity_id = ? AND ss.teacher_id = ?";
    
    $stmt = $db->prepare($verify_query);
    $stmt->bind_param("ii", $activity_id, $teacher_id);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized access to this activity']);
        exit();
    }

    // Update activity
    $update_query = "UPDATE activities 
                    SET title = ?, 
                        description = ?, 
                        due_date = ?, 
                        points = ?, 
                        type = ?,
                        section_subject_id = ?,
                        updated_at = CURRENT_TIMESTAMP 
                    WHERE activity_id = ?";

    $stmt = $db->prepare($update_query);
    $stmt->bind_param("sssisii", $title, $description, $due_date, $points, $type, $section_subject_id, $activity_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Activity updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update activity']);
    }
}
?>
