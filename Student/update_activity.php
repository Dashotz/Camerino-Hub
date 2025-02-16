<?php
session_start();
require_once('../db/dbConnector.php');

header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    echo json_encode(['status' => 'logged_out']);
    exit();
}

$db = new DbConnector();
$student_id = $_SESSION['id'];
$current_time = time();

// Check if user is logged in elsewhere
$check_query = "SELECT user_online, last_activity FROM student WHERE student_id = ?";
$stmt = $db->prepare($check_query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$user_status = $result->fetch_assoc();

if ($user_status['user_online'] == 1 && $user_status['last_activity'] != $_SESSION['last_activity']) {
    // Another session is active
    session_destroy();
    echo json_encode(['status' => 'logged_out']);
    exit();
}

// Update last activity
$_SESSION['last_activity'] = $current_time;
$update_query = "UPDATE student SET last_activity = ? WHERE student_id = ?";
$stmt = $db->prepare($update_query);
$stmt->bind_param("ii", $current_time, $student_id);
$stmt->execute();

echo json_encode(['status' => 'active']);
?> 