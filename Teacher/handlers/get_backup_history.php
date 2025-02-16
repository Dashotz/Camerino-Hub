<?php
session_start();
require_once('../../db/dbConnector.php');

header('Content-Type: application/json');

if (!isset($_SESSION['teacher_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $backup_dir = "../../backups/";
    $response = ['success' => true, 'data' => []];

    if (file_exists($backup_dir) && is_dir($backup_dir)) {
        $files = glob($backup_dir . "*.sql");
        
        if ($files !== false) {
            foreach ($files as $file) {
                if (is_file($file)) {
                    $filename = basename($file);
                    $response['data'][] = [
                        'filename' => $filename,
                        'date' => date("Y-m-d H:i:s", filemtime($file)),
                        'size' => formatSize(filesize($file))
                    ];
                }
            }
            
            // Sort by date (newest first)
            usort($response['data'], function($a, $b) {
                return strtotime($b['date']) - strtotime($a['date']);
            });
        }
    }

    echo json_encode($response);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error processing backup history: ' . $e->getMessage()
    ]);
}

function formatSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $i = 0;
    while ($bytes >= 1024 && $i < count($units) - 1) {
        $bytes /= 1024;
        $i++;
    }
    return round($bytes, 2) . ' ' . $units[$i];
}
?> 