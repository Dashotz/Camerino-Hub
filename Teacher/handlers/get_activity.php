<?php
session_start();
require_once('../../db/dbConnector.php');

if (!isset($_SESSION['teacher_id']) || !isset($_GET['activity_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

$db = new DbConnector();
$activity_id = $_GET['activity_id'];
$teacher_id = $_SESSION['teacher_id'];

// Fetch activity details with security check
$query = "
    SELECT 
        a.*,
        ss.id as section_subject_id,
        sec.section_name,
        sub.subject_name,
        sub.subject_code
    FROM activities a
    JOIN section_subjects ss ON a.section_subject_id = ss.id
    JOIN sections sec ON ss.section_id = sec.section_id
    JOIN subjects sub ON ss.subject_id = sub.id
    WHERE a.activity_id = ? 
    AND ss.teacher_id = ?";

$stmt = $db->prepare($query);
$stmt->bind_param("ii", $activity_id, $teacher_id);
$stmt->execute();
$result = $stmt->get_result();

if ($activity = $result->fetch_assoc()) {
    echo json_encode([
        'success' => true,
        'activity' => [
            'activity_id' => $activity['activity_id'],
            'title' => $activity['title'],
            'description' => $activity['description'],
            'type' => $activity['type'],
            'due_date' => date('Y-m-d\TH:i', strtotime($activity['due_date'])),
            'points' => $activity['points'],
            'section_subject_id' => $activity['section_subject_id'],
            'status' => $activity['status'],
            'quiz_link' => $activity['quiz_link'] ?? '',
            'quiz_duration' => $activity['quiz_duration'] ?? '',
            'prevent_tab_switch' => $activity['prevent_tab_switch'] ?? 0,
            'fullscreen_required' => $activity['fullscreen_required'] ?? 0
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Activity not found']);
} 