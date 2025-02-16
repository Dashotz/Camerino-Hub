<?php
session_start();
require_once('../db/dbConnector.php');

function logError($message) {
    error_log("[Download Result Error] " . $message);
}

if (!isset($_SESSION['id'])) {
    logError("Unauthorized access attempt");
    header("HTTP/1.1 401 Unauthorized");
    exit('Unauthorized');
}

if (!isset($_GET['file'])) {
    logError("No file specified in request");
    header("HTTP/1.1 400 Bad Request");
    exit('No file specified');
}

// Clean the file path to prevent directory traversal
$file_path = realpath('../' . $_GET['file']);
$uploads_dir = realpath('../uploads/results');

// Verify the file exists and is within the allowed directory
if (!$file_path || !file_exists($file_path) || !is_file($file_path) || 
    strpos($file_path, $uploads_dir) !== 0) {
    header("HTTP/1.1 404 Not Found");
    exit('File not found');
}

// Verify student has access to this result file
$db = new DbConnector();
$student_id = $_SESSION['id'];
$file_name = basename($_GET['file']);

$verify_query = "
    SELECT sas.result_file 
    FROM student_activity_submissions sas
    WHERE sas.student_id = ? 
    AND sas.result_file = ?";

$stmt = $db->prepare($verify_query);
$stmt->bind_param("is", $student_id, $_GET['file']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("HTTP/1.1 403 Forbidden");
    exit('Access denied');
}

// Get file mime type
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime_type = finfo_file($finfo, $file_path);
finfo_close($finfo);

// Set headers for file download
header('Content-Type: ' . $mime_type);
header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
header('Content-Length: ' . filesize($file_path));
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: public');

// Output file contents
readfile($file_path);
exit();
?> 