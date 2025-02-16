<?php
session_start();
require_once('../../db/dbConnector.php');

header('Content-Type: application/json');

if (!isset($_SESSION['teacher_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

try {
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('No image uploaded or upload failed');
    }

    $file = $_FILES['image'];
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    
    if (!in_array($file['type'], $allowed_types)) {
        throw new Exception('Invalid file type. Only JPG, PNG and GIF are allowed.');
    }

    if ($file['size'] > 5 * 1024 * 1024) { // 5MB limit
        throw new Exception('File size too large. Maximum size is 5MB.');
    }

    $upload_dir = '../../uploads/quiz_images/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $filename = uniqid() . '_' . basename($file['name']);
    $filepath = $upload_dir . $filename;

    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        echo json_encode([
            'success' => true,
            'image_path' => 'uploads/quiz_images/' . $filename
        ]);
    } else {
        throw new Exception('Failed to upload image');
    }

} catch (Exception $e) {
    error_log('Image upload error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?> 