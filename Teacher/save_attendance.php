<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['teacher_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

$db = new DbConnector();
$teacher_id = $_SESSION['teacher_id'];

// Validate input
if (!isset($_POST['student_id'], $_POST['class_id'], $_POST['date'], $_POST['status'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit();
}

$student_id = $_POST['student_id'];
$class_id = $_POST['class_id'];
$date = $_POST['date'];
$status = $_POST['status'];
$attendance_id = $_POST['attendance_id'] ?? null;

// Verify this class belongs to the teacher
$verify_query = "SELECT COUNT(*) as count FROM classes WHERE class_id = ? AND teacher_id = ?";
$stmt = $db->prepare($verify_query);
$stmt->bind_param("ii", $class_id, $teacher_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

if ($result['count'] == 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid class']);
    exit();
}

// Insert or update attendance record
if ($attendance_id) {
    $query = "UPDATE attendance SET status = ? WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("si", $status, $attendance_id);
} else {
    $query = "INSERT INTO attendance (student_id, course_id, date, status) VALUES (?, ?, ?, ?)";
    $stmt = $db->prepare($query);
    $stmt->bind_param("iiss", $student_id, $class_id, $date, $status);
}

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>
