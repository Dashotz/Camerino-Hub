<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['id'])) {
    header('HTTP/1.0 403 Forbidden');
    exit('Access Denied');
}

if (!isset($_GET['file']) || !isset($_GET['name'])) {
    header('HTTP/1.0 400 Bad Request');
    exit('Invalid Request');
}

$filePath = $_GET['file'];
$fileName = $_GET['name'];

// Validate file path to prevent directory traversal
$realPath = realpath($filePath);
$basePath = realpath(__DIR__ . '/../uploads/activities/');

if ($realPath === false || strpos($realPath, $basePath) !== 0) {
    header('HTTP/1.0 403 Forbidden');
    exit('Invalid File Path');
}

if (!file_exists($filePath)) {
    header('HTTP/1.0 404 Not Found');
    exit('File not found');
}

// Get file mime type
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $filePath);
finfo_close($finfo);

// Set headers for download
header('Content-Type: ' . $mimeType);
header('Content-Disposition: attachment; filename="' . basename($fileName) . '"');
header('Content-Length: ' . filesize($filePath));
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: public');

// Output file
readfile($filePath);
exit();
?> 