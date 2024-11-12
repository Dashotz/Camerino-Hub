<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['id']) || !isset($_POST['assignment_id'])) {
    http_response_code(403);
    exit('Unauthorized');
}

$db = new DbConnector();
$student_id = $_SESSION['id'];
$assignment_id = (int)$_POST['assignment_id'];

// Verify student is enrolled in the course
$verify_query = "SELECT sc.student_id 
                FROM student_courses sc
                JOIN assignments a ON sc.course_id = a.course_id
                WHERE a.assignment_id = ? AND sc.student_id = ? AND sc.status = 'active'";

$stmt = $db->prepare($verify_query);
$stmt->bind_param("ii", $assignment_id, $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(403);
    exit('Unauthorized');
}

// Handle file upload if needed
$submission_file = null;
if (isset($_FILES['submission_file'])) {
    $file = $_FILES['submission_file'];
    $filename = time() . '_' . $file['name'];
    $upload_path = '../uploads/submissions/' . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        $submission_file = $filename;
    }
}

// Insert or update submission
$upsert_query = "INSERT INTO student_submissions 
                 (student_id, assignment_id, submission_file, submission_date) 
                 VALUES (?, ?, ?, NOW())
                 ON DUPLICATE KEY UPDATE 
                 submission_file = VALUES(submission_file),
                 submission_date = VALUES(submission_date)";

$stmt = $db->prepare($upsert_query);
$stmt->bind_param("isi", $student_id, $assignment_id, $submission_file);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo 'Submission updated successfully!';
} else {
    echo 'No changes made to the submission.';
}
