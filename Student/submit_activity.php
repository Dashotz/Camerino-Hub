<?php
session_start();
require_once('../db/dbConnector.php');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

try {
    // Check authentication
    if (!isset($_SESSION['id'])) {
        throw new Exception('Unauthorized access');
    }

    // Validate activity_id
    if (!isset($_POST['activity_id']) || !is_numeric($_POST['activity_id'])) {
        throw new Exception('Invalid activity ID');
    }

    // Validate file upload
    if (!isset($_FILES['submission_file']) || $_FILES['submission_file']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('No file uploaded or upload error occurred');
    }

    $db = new DbConnector();
    $student_id = $_SESSION['id'];
    $activity_id = (int)$_POST['activity_id'];
    $file = $_FILES['submission_file'];

    // Create upload directory if it doesn't exist
    $uploadDir = "uploads/submissions/";
    if (!file_exists($uploadDir)) {
        if (!mkdir($uploadDir, 0777, true)) {
            throw new Exception('Failed to create upload directory');
        }
    }

    // Generate unique filename
    $filename = time() . '_' . basename($file['name']);
    $uploadPath = $uploadDir . $filename;
    
    // Start transaction
    $db->begin_transaction();

    try {
        // Insert submission record
        $submission_query = "INSERT INTO student_activity_submissions 
            (student_id, activity_id, submitted_at, status) 
            VALUES (?, ?, NOW(), 'submitted')";
        
        $stmt = $db->prepare($submission_query);
        if (!$stmt) {
            throw new Exception('Database error: ' . $db->error);
        }
        
        $stmt->bind_param("ii", $student_id, $activity_id);
        if (!$stmt->execute()) {
            throw new Exception('Failed to save submission: ' . $stmt->error);
        }
        
        $submission_id = $db->insert_id;

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            throw new Exception('Failed to save uploaded file');
        }

        // Save file details
        $file_query = "INSERT INTO submission_files 
            (submission_id, file_name, file_path, file_type, file_size, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())";
        
        $stmt = $db->prepare($file_query);
        $stmt->bind_param("isssi", 
            $submission_id,
            $file['name'],
            $uploadPath,
            $file['type'],
            $file['size']
        );
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to save file details');
        }

        $db->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Activity submitted successfully'
        ]);

    } catch (Exception $e) {
        $db->rollback();
        // Delete uploaded file if exists
        if (file_exists($uploadPath)) {
            unlink($uploadPath);
        }
        throw $e;
    }

} catch (Exception $e) {
    error_log("Submission error: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>