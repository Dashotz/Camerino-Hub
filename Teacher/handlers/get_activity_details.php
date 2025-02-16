<?php
session_start();
require_once('../../db/dbConnector.php');

if (!isset($_SESSION['teacher_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit();
}

if (!isset($_GET['activity_id'])) {
    echo json_encode(['success' => false, 'message' => 'Activity ID is required']);
    exit();
}

$db = new DbConnector();
$activity_id = $_GET['activity_id'];
$teacher_id = $_SESSION['teacher_id'];

// Get activity details
$query = "SELECT 
    a.*,
    ss.id as section_subject_id
FROM activities a
JOIN section_subjects ss ON a.section_subject_id = ss.id
WHERE a.activity_id = ? AND ss.teacher_id = ?";

$stmt = $db->prepare($query);
$stmt->bind_param("ii", $activity_id, $teacher_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Activity not found']);
    exit();
}

$activity = $result->fetch_assoc();
echo json_encode(['success' => true, 'data' => $activity]); 