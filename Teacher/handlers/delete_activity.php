<?php
session_start();
require_once('../../db/dbConnector.php');

if (!isset($_SESSION['teacher_id']) || !isset($_POST['activity_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

$db = new DbConnector();
$activity_id = $_POST['activity_id'];
$teacher_id = $_SESSION['teacher_id'];

try {
    // First check if there are any submissions
    $check_query = "SELECT COUNT(*) as count FROM student_activity_submissions WHERE activity_id = ?";
    $stmt = $db->prepare($check_query);
    $stmt->bind_param("i", $activity_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $submission_count = $result->fetch_assoc()['count'];

    if ($submission_count > 0) {
        echo json_encode([
            'success' => false,
            'error' => 'foreign key constraint fails',
            'message' => 'You can\'t delete this Activity, Assignment or Quiz because there are student submissions.'
        ]);
        exit();
    }

    // If no submissions, proceed with deletion
    $query = "DELETE a FROM activities a 
              JOIN section_subjects ss ON a.section_subject_id = ss.id 
              WHERE a.activity_id = ? AND ss.teacher_id = ?";
    
    $stmt = $db->prepare($query);
    $stmt->bind_param("ii", $activity_id, $teacher_id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Activity deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Activity not found or unauthorized']);
        }
    } else {
        throw new Exception($stmt->error);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'message' => 'You can\'t delete this Activity, Assignment or Quiz because there are student submissions.'
    ]);
}

$db->close();
