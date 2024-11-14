<?php
session_start();
require_once('../db/dbConnector.php');

header('Content-Type: application/json');

if (!isset($_SESSION['teacher_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

if (!isset($_GET['type'])) {
    echo json_encode(['success' => false, 'message' => 'Missing type parameter']);
    exit;
}

$db = new DbConnector();
$teacher_id = $_SESSION['teacher_id'];
$type = $_GET['type'];

$query = "";
switch ($type) {
    case 'quiz':
        $query = "SELECT activity_id as id, title FROM activities 
                 WHERE teacher_id = ? AND type = 'quiz' AND status = 'active'";
        break;
    case 'activity':
        $query = "SELECT activity_id as id, title FROM activities 
                 WHERE teacher_id = ? AND type = 'activity' AND status = 'active'";
        break;
    case 'assignment':
        $query = "SELECT assignment_id as id, title FROM assignments 
                 WHERE teacher_id = ? AND status = 'active'";
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid type']);
        exit;
}

$stmt = $db->prepare($query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();

$references = [];
while ($row = $result->fetch_assoc()) {
    $references[] = $row;
}

echo json_encode(['success' => true, 'references' => $references]);
