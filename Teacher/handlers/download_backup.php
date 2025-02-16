<?php
session_start();
require_once('../../db/dbConnector.php');

if (!isset($_SESSION['teacher_id'])) {
    die('Unauthorized');
}

if (isset($_GET['file'])) {
    $filename = basename($_GET['file']);
    $file_path = "../../backups/" . $filename;
    
    if (file_exists($file_path)) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($file_path));
        readfile($file_path);
        exit;
    }
}

header('HTTP/1.0 404 Not Found');
echo 'File not found';
?> 