<?php
session_start();
require_once('../../db/dbConnector.php');

if (!isset($_SESSION['teacher_id']) || !isset($_GET['file'])) {
    http_response_code(403);
    exit('Unauthorized access');
}

try {
    $db = new DbConnector();
    $file_path = $_GET['file'];
    $teacher_id = $_SESSION['teacher_id'];

    // Verify teacher has access to this file
    $verify_query = "
        SELECT sf.file_path 
        FROM submission_files sf
        JOIN student_activity_submissions sas ON sf.submission_id = sas.submission_id
        JOIN activities a ON sas.activity_id = a.activity_id
        JOIN section_subjects ss ON a.section_subject_id = ss.id
        WHERE sf.file_path = ? AND ss.teacher_id = ?";

    $stmt = $db->prepare($verify_query);
    $stmt->bind_param("si", $file_path, $teacher_id);
    $stmt->execute();
    
    if (!$stmt->get_result()->fetch_assoc()) {
        throw new Exception('Unauthorized access to file');
    }

    // Get the full file path
    $full_path = '../../' . $file_path;
    
    if (!file_exists($full_path)) {
        throw new Exception('File not found');
    }

    // Get file info
    $file_name = basename($file_path);
    $file_size = filesize($full_path);
    $file_type = mime_content_type($full_path);

    // Set headers for download
    header('Content-Type: ' . $file_type);
    header('Content-Length: ' . $file_size);
    header('Content-Disposition: attachment; filename="' . $file_name . '"');

    // Output file
    readfile($full_path);
    exit();

} catch (Exception $e) {
    http_response_code(404);
    exit($e->getMessage());
}
?>
