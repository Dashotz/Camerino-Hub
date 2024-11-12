<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['id']) || !isset($_GET['file_id'])) {
    header("HTTP/1.1 403 Forbidden");
    exit('Access denied');
}

$db = new DbConnector();
$student_id = $_SESSION['id'];
$file_id = intval($_GET['file_id']);

// Verify student has access to this file
$verify_query = "
    SELECT 
        af.file_path,
        af.file_name,
        af.file_type,
        a.title as activity_title
    FROM activity_files af
    JOIN activities a ON af.activity_id = a.activity_id
    JOIN section_subjects ss ON a.section_subject_id = ss.id
    JOIN student_sections sts ON ss.section_id = sts.section_id
    WHERE af.file_id = ?
        AND sts.student_id = ?
        AND sts.status = 'active'
        AND a.status = 'active'";

$stmt = $db->prepare($verify_query);
$stmt->bind_param("ii", $file_id, $student_id);
$stmt->execute();
$file = $stmt->get_result()->fetch_assoc();

if (!$file) {
    header("HTTP/1.1 404 Not Found");
    exit('File not found');
}

$file_path = '../uploads/activities/' . $file['file_path'];

if (!file_exists($file_path)) {
    header("HTTP/1.1 404 Not Found");
    exit('File not found');
}

// Get MIME type
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime_type = finfo_file($finfo, $file_path);
finfo_close($finfo);

// Set appropriate headers for download
header('Content-Type: ' . $mime_type);
header('Content-Disposition: attachment; filename="' . $file['file_name'] . '"');
header('Content-Length: ' . filesize($file_path));
header('Cache-Control: no-cache');
header('Pragma: no-cache');
header('Expires: 0');

// Log the download (optional)
$log_query = "INSERT INTO activity_file_downloads 
              (student_id, file_id, downloaded_at) 
              VALUES (?, ?, NOW())";
$stmt = $db->prepare($log_query);
$stmt->bind_param("ii", $student_id, $file_id);
$stmt->execute();

// Output file content
readfile($file_path);
exit();
