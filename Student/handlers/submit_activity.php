<?php
session_start();
require_once('../../db/dbConnector.php');

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

$db = new DbConnector();
$student_id = $_SESSION['id'];
$activity_id = $_POST['activity_id'] ?? 0;

try {
    // Validate submission
    if (!isset($_FILES['submission_file'])) {
        throw new Exception('No file uploaded');
    }

    // File details
    $file = $_FILES['submission_file'];
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];
    $fileType = $file['type'];

    // Start transaction
    $db->beginTransaction();

    try {
        // First, create the submission record and get the ID
        $submission_query = "
            INSERT INTO student_activity_submissions 
            (student_id, activity_id, submitted_at, status) 
            VALUES (?, ?, NOW(), 'submitted')";
        
        $stmt = $db->prepare($submission_query);
        $stmt->bind_param("ii", $student_id, $activity_id);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to create submission record');
        }

        // Get the submission ID using mysqli_insert_id
        $submission_id = mysqli_insert_id($db->getConnection());

        // Create submission directory if it doesn't exist
        $uploadDir = '../uploads/submissions/' . $activity_id . '/';
        if (!file_exists($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true)) {
                throw new Exception('Failed to create upload directory');
            }
        }

        // Generate unique filename
        $newFileName = uniqid('submission_') . '_' . $fileName;
        $uploadPath = $uploadDir . $newFileName;

        // Save file details
        $file_query = "
            INSERT INTO submission_files 
            (submission_id, file_name, file_path, file_type, file_size, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())";
        
        $relativePath = 'uploads/submissions/' . $activity_id . '/' . $newFileName;
        
        $stmt = $db->prepare($file_query);
        $stmt->bind_param("isssi", 
            $submission_id, 
            $fileName, 
            $relativePath, 
            $fileType, 
            $fileSize
        );
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to save file details');
        }

        // Move uploaded file
        if (!move_uploaded_file($fileTmpName, $uploadPath)) {
            throw new Exception('Failed to save file');
        }

        // Commit transaction
        $db->commit();
        
        // Redirect back with success message
        header("Location: ../view_activity.php?id=$activity_id&status=success&message=Work submitted successfully");

    } catch (Exception $e) {
        // Rollback transaction
        $db->rollback();
        throw $e;
    }

} catch (Exception $e) {
    // Delete uploaded file if exists
    if (isset($uploadPath) && file_exists($uploadPath)) {
        unlink($uploadPath);
    }

    // Log the error
    error_log("Submission error: " . $e->getMessage());
    
    // Redirect back with error message
    header("Location: ../view_activity.php?id=$activity_id&status=error&message=" . urlencode($e->getMessage()));
}
?>
