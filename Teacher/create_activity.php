<?php
// Prevent any output before our JSON response
ob_start();

// Basic error handling
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

header('Content-Type: application/json');

try {
    require_once('../db/dbConnector.php');
    require_once('includes/file_handler.php');
    session_start();

    // Authentication check
    if (!isset($_SESSION['teacher_id'])) {
        throw new Exception('Not authenticated');
    }

    // Method check
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Create database connection
    $db = new DbConnector();
    
    // Basic validation
    if (empty($_POST['section_subject_id']) || empty($_POST['title']) || 
        empty($_POST['type']) || empty($_POST['due_date'])) {
        throw new Exception('Missing required fields');
    }

    // Start transaction
    $db->begin_transaction();

    try {
        // Insert activity
        $stmt = $db->prepare("
            INSERT INTO activities (
                teacher_id, 
                section_subject_id,
                title,
                description,
                type,
                points,
                due_date,
                status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, 'active')
        ");

        if (!$stmt) {
            throw new Exception("Prepare failed: " . $db->error());
        }

        $teacher_id = $_SESSION['teacher_id'];
        $section_subject_id = intval($_POST['section_subject_id']);
        $title = strip_tags($_POST['title']);
        $description = strip_tags($_POST['description'] ?? '');
        $type = $_POST['type'];
        $points = intval($_POST['points'] ?? 100);
        $due_date = date('Y-m-d H:i:s', strtotime($_POST['due_date']));

        $stmt->bind_param("iisssis", 
            $teacher_id,
            $section_subject_id,
            $title,
            $description,
            $type,
            $points,
            $due_date
        );

        if (!$stmt->execute()) {
            throw new Exception("Failed to create activity: " . $stmt->error);
        }

        $activity_id = $stmt->insert_id;

        // Handle file uploads if present
        if (!empty($_FILES['activity_files']['name'][0])) {
            $uploadedFiles = handleActivityFiles($activity_id, $teacher_id, $_FILES);
            
            // Store file information in database
            foreach ($uploadedFiles as $file) {
                $file_stmt = $db->prepare("
                    INSERT INTO activity_files (
                        activity_id, 
                        file_name, 
                        file_path, 
                        file_type, 
                        file_size
                    ) VALUES (?, ?, ?, ?, ?)
                ");
                
                if (!$file_stmt) {
                    throw new Exception("Failed to prepare file statement");
                }

                $file_stmt->bind_param("isssi", 
                    $activity_id, 
                    $file['file_name'], 
                    $file['file_path'], 
                    $file['file_type'], 
                    $file['file_size']
                );
                
                if (!$file_stmt->execute()) {
                    throw new Exception("Failed to save file information");
                }
            }
        }

        // Commit transaction
        $db->commit();

        // Clear any buffered output
        ob_clean();

        // Send success response
        echo json_encode([
            'success' => true,
            'message' => 'Activity created successfully',
            'activity_id' => $activity_id
        ]);

    } catch (Exception $e) {
        // Rollback transaction
        $db->rollback();
        throw $e;
    }

} catch (Exception $e) {
    // Log the error
    error_log("Create activity error: " . $e->getMessage());
    
    // Clear any buffered output
    ob_clean();

    // Send error response
    echo json_encode([
        'success' => false,
        'message' => 'Error creating activity: ' . $e->getMessage()
    ]);
}

// End output buffering and flush
ob_end_flush();
