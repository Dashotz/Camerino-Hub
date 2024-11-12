<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['teacher_id']) || !isset($_POST['submission_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

$db = new DbConnector();
$teacher_id = $_SESSION['teacher_id'];
$submission_id = $_POST['submission_id'];
$grade = $_POST['grade'];
$feedback = $_POST['feedback'];

// Verify this submission belongs to a class taught by this teacher
$verify_query = "SELECT COUNT(*) as count 
                FROM student_submissions ss
                JOIN assignments a ON ss.assignment_id = a.assignment_id
                WHERE ss.submission_id = ? AND a.teacher_id = ?";
$stmt = $db->prepare($verify_query);
$stmt->bind_param("ii", $submission_id, $teacher_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

if ($result['count'] == 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid submission']);
    exit();
}

// Update the grade and feedback
$update_query = "UPDATE student_submissions 
                SET grade = ?, feedback = ?, graded_at = NOW() 
                WHERE submission_id = ?";
$stmt = $db->prepare($update_query);
$stmt->bind_param("dsi", $grade, $feedback, $submission_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>
