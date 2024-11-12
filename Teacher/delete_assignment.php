<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['teacher_id']) || !isset($_POST['assignment_id'])) {
    http_response_code(403);
    exit('Unauthorized');
}

$db = new DbConnector();
$teacher_id = $_SESSION['teacher_id'];
$assignment_id = (int)$_POST['assignment_id'];

// Verify the assignment belongs to one of this teacher's courses
$verify_query = "SELECT a.assignment_id 
                FROM assignments a
                JOIN courses c ON a.course_id = c.course_id
                WHERE a.assignment_id = ? AND c.teacher_id = ?";

$stmt = $db->prepare($verify_query);
$stmt->bind_param("ii", $assignment_id, $teacher_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(403);
    exit('Unauthorized');
}

// Delete related submissions first
$delete_submissions = "DELETE FROM student_submissions WHERE assignment_id = ?";
$stmt = $db->prepare($delete_submissions);
$stmt->bind_param("i", $assignment_id);
$stmt->execute();

// Then delete the assignment
$delete_assignment = "DELETE FROM assignments WHERE assignment_id = ?";
$stmt = $db->prepare($delete_assignment);
$stmt->bind_param("i", $assignment_id);

if ($stmt->execute()) {
    echo 'Success';
} else {
    http_response_code(500);
    echo 'Error deleting assignment';
}
