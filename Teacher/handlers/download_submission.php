<?php
session_start();
require_once('../../db/dbConnector.php');

if (!isset($_SESSION['teacher_id'])) {
    header("HTTP/1.1 403 Forbidden");
    exit('Unauthorized access');
}

if (!isset($_GET['file'])) {
    header("HTTP/1.1 400 Bad Request");
    exit('No file specified');
}

$file_path = '../../' . $_GET['file'];

if (!file_exists($file_path)) {
    header("HTTP/1.1 404 Not Found");
    exit('File not found: ' . $file_path);
}

// Get file mime type
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime_type = finfo_file($finfo, $file_path);
finfo_close($finfo);

// Set headers
header('Content-Type: ' . $mime_type);
header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
header('Content-Length: ' . filesize($file_path));
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: public');

// Output file
readfile($file_path);
exit();
?>
