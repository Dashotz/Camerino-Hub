<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['student_id'])) {
    http_response_code(403);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$db = new DbConnector();

$student_id = $_SESSION['student_id'];
$quiz_id = $data['quiz_id'];
$violation_type = $data['violation_type'];
$timestamp = date('Y-m-d H:i:s');

$query = "INSERT INTO security_violations (student_id, quiz_id, violation_type, timestamp) 
          VALUES (?, ?, ?, ?)";
$stmt = $db->prepare($query);
$stmt->bind_param("iiss", $student_id, $quiz_id, $violation_type, $timestamp);
$stmt->execute();
?>
