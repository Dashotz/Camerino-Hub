<?php
session_start();
require_once('../../db/dbConnector.php');

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    http_response_code(401);
    die('Unauthorized access');
}

// Get student ID from session
$student_id = $_SESSION['id'];

// Validate file parameter
if (!isset($_GET['file']) || empty($_GET['file'])) {
    http_response_code(400);
    die('Invalid file parameter');
}

$file_path = urldecode($_GET['file']);

// Security: Ensure the file path is within the uploads directory
$uploads_dir = realpath(__DIR__ . '/../../uploads/results');
$requested_path = realpath($uploads_dir . '/' . basename($file_path));

// Debug information (remove in production)
error_log("Uploads dir: " . $uploads_dir);
error_log("Requested path: " . $requested_path);
error_log("File path: " . $file_path);

if ($requested_path === false || strpos($requested_path, $uploads_dir) !== 0) {
    http_response_code(403);
    die('Invalid file path');
}

// Verify that this student has access to this result file
$db = new DbConnector();
$query = "
    SELECT sas.result_file 
    FROM student_activity_submissions sas
    JOIN activities a ON sas.activity_id = a.activity_id
    WHERE sas.student_id = ? 
    AND sas.result_file = ?";

$stmt = $db->prepare($query);
$stmt->bind_param("is", $student_id, $file_path);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(403);
    die('Access denied');
}

// Check if file exists
if (!file_exists($requested_path)) {
    http_response_code(404);
    die('File not found');
}

// Get file information
$file_info = pathinfo($requested_path);
$file_name = basename($requested_path);

// Set content type based on file extension
$file_extension = strtolower(pathinfo($requested_path, PATHINFO_EXTENSION));
$content_type = 'application/octet-stream';

switch ($file_extension) {
    case 'pdf':
        $content_type = 'application/pdf';
        break;
    case 'doc':
    case 'docx':
        $content_type = 'application/msword';
        break;
}

// Set appropriate headers for file download
header('Content-Type: ' . $content_type);
header('Content-Disposition: attachment; filename="' . $file_name . '"');
header('Content-Length: ' . filesize($requested_path));
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: public');

// Clear output buffer
if (ob_get_level()) ob_end_clean();

// Read and output file
readfile($requested_path);
exit();
?>