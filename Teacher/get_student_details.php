<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['teacher_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if (!isset($_GET['student_id'])) {
    echo json_encode(['success' => false, 'message' => 'Student ID is required']);
    exit();
}

$db = new DbConnector();
$student_id = $_GET['student_id'];

$query = "SELECT 
            s.student_id,
            s.firstname,
            s.lastname,
            s.email,
            s.contact_number,
            s.gender,
            s.lrn
          FROM student s
          WHERE s.student_id = ?";

$stmt = $db->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $student = $result->fetch_assoc();
    echo json_encode(['success' => true, 'data' => $student]);
} else {
    echo json_encode(['success' => false, 'message' => 'Student not found']);
}
?>
