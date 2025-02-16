<?php
session_start();
require_once('../../db/dbConnector.php');

if (!isset($_SESSION['teacher_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$response = ['success' => false, 'message' => ''];

if (isset($_POST['filename'])) {
    $filename = basename($_POST['filename']);
    $file_path = "../../backups/" . $filename;
    
    if (file_exists($file_path)) {
        if (unlink($file_path)) {
            $response = ['success' => true, 'message' => 'Backup deleted successfully'];
        } else {
            $response = ['success' => false, 'message' => 'Failed to delete backup'];
        }
    } else {
        $response = ['success' => false, 'message' => 'Backup file not found'];
    }
}

header('Content-Type: application/json');
echo json_encode($response); 