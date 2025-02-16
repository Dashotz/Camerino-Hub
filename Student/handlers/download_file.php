<?php
session_start();
require_once('../../db/dbConnector.php');

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    header("HTTP/1.1 403 Forbidden");
    exit('Access denied');
}

// Validate input parameters
if (!isset($_GET['file']) || empty($_GET['file'])) {
    header("HTTP/1.1 400 Bad Request");
    exit('Missing file parameter');
}

$file_path = urldecode($_GET['file']);
$file_name = isset($_GET['name']) ? urldecode($_GET['name']) : basename($file_path);

// Construct the full server path - adjust this to match your actual directory structure
$base_path = realpath('../../../'); // Go up three levels to reach the root directory
$full_path = $base_path . '/' . $file_path;

// Basic security check
if (!file_exists($full_path)) {
    error_log("File not found: " . $full_path); // Add logging
    header("HTTP/1.1 404 Not Found");
    exit('File not found');
}

// Validate file is within allowed directory
if (strpos(realpath($full_path), realpath($base_path . '/uploads')) !== 0) {
    header("HTTP/1.1 403 Forbidden");
    exit('Access denied');
}

// Get MIME type
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime_type = finfo_file($finfo, $full_path);
finfo_close($finfo);

// Set headers for file download
header('Content-Type: ' . $mime_type);
header('Content-Disposition: attachment; filename="' . basename($file_name) . '"');
header('Content-Length: ' . filesize($full_path));
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: public');

// Output file
readfile($full_path);
exit();
?>