<?php
session_start();
require_once('../db/dbConnector.php');

header('Content-Type: application/json');

if (!isset($_SESSION['teacher_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing announcement ID']);
    exit;
}

$db = new DbConnector();
$teacher_id = $_SESSION['teacher_id'];
$announcement_id = (int)$_GET['id'];

// Get announcement details with section and subject info
$query = "SELECT 
            a.*,
            s.section_name,
            sub.subject_name
          FROM announcements a
          JOIN sections s ON a.section_id = s.section_id
          JOIN subjects sub ON a.subject_id = sub.id
          WHERE a.id = ? 
          AND a.teacher_id = ? 
          AND a.status = 'active'
          LIMIT 1";

$stmt = $db->prepare($query);
$stmt->bind_param("ii", $announcement_id, $teacher_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Announcement not found']);
    exit;
}

$announcement = $result->fetch_assoc();

// Create response array with necessary data
$response = [
    'success' => true,
    'announcement' => [
        'id' => $announcement['id'],
        'section_id' => $announcement['section_id'],
        'subject_id' => $announcement['subject_id'],
        'content' => $announcement['content'],
        'attachment' => $announcement['attachment']
    ]
];

echo json_encode($response); 