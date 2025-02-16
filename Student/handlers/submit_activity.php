<?php
session_start();
require_once('../../db/dbConnector.php');

header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

try {
    $db = new DbConnector();
    $student_id = $_SESSION['id'];
    $activity_id = $_POST['activity_id'] ?? null;

    // Validate submission
    if (!isset($_FILES['submission_file'])) {
        throw new Exception('No file uploaded');
    }

    $file = $_FILES['submission_file'];
    
    // Validate file size (10MB max)
    if ($file['size'] > 10 * 1024 * 1024) {
        throw new Exception('File size exceeds 10MB limit');
    }

    // Validate file type
    $allowed_types = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip'];
    if (!in_array($file['type'], $allowed_types)) {
        throw new Exception('Invalid file type. Allowed types: PDF, DOC, DOCX, ZIP');
    }

    // Create upload directory if it doesn't exist
    $upload_dir = '../../uploads/activities/' . $student_id;
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Generate unique filename
    $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $unique_filename = uniqid() . '_' . time() . '.' . $file_ext;
    $upload_path = $upload_dir . '/' . $unique_filename;
    $relative_path = 'uploads/activities/' . $student_id . '/' . $unique_filename;

    // Start transaction
    $db->begin_transaction();

    try {
        // Verify student is enrolled in the section
        $verify_query = "SELECT sts.student_id 
                        FROM student_sections sts
                        JOIN section_subjects ss ON sts.section_id = ss.section_id
                        JOIN activities a ON ss.id = a.section_subject_id
                        WHERE a.activity_id = ? 
                        AND sts.student_id = ? 
                        AND sts.status = 'active'";

        $stmt = $db->prepare($verify_query);
        $stmt->bind_param("ii", $activity_id, $student_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            throw new Exception('Unauthorized access - Not enrolled in this section');
        }

        // Create submission record
        $submission_query = "
            INSERT INTO student_activity_submissions 
            (student_id, activity_id, submitted_at, status) 
            VALUES (?, ?, NOW(), 'submitted')";
        
        $stmt = $db->prepare($submission_query);
        $stmt->bind_param("ii", $student_id, $activity_id);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to create submission record');
        }

        $submission_id = mysqli_insert_id($db->getConnection());

        // Save file details
        $file_query = "
            INSERT INTO submission_files 
            (submission_id, file_name, file_path, file_type, file_size) 
            VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $db->prepare($file_query);
        $stmt->bind_param(
            "isssi", 
            $submission_id, 
            $file['name'], 
            $relative_path, 
            $file['type'], 
            $file['size']
        );
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to save file details');
        }

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
            throw new Exception('Failed to save file');
        }

        // Commit transaction
        $db->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Activity submitted successfully'
        ]);

    } catch (Exception $e) {
        // Rollback transaction
        $db->rollback();
        throw $e;
    }

} catch (Exception $e) {
    // Delete uploaded file if exists
    if (isset($upload_path) && file_exists($upload_path)) {
        unlink($upload_path);
    }

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>