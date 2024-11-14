<?php
session_start();
require_once('../../db/dbConnector.php');

// Set JSON header immediately
header('Content-Type: application/json');

try {
    // Check authentication
    if (!isset($_SESSION['admin_id'])) {
        throw new Exception('Unauthorized access');
    }

    // Initialize database connection
    $db = new DbConnector();
    $conn = $db->getConnection();

    // Validate required fields
    if (empty($_POST['subject_code']) || 
        empty($_POST['subject_title']) || 
        empty($_POST['category']) || 
        empty($_POST['grade_level'])) {
        throw new Exception('All required fields must be filled out');
    }

    // Sanitize inputs using the connection object
    $subject_code = $conn->real_escape_string($_POST['subject_code']);
    $subject_title = $conn->real_escape_string($_POST['subject_title']);
    $category = $conn->real_escape_string($_POST['category']);
    $description = isset($_POST['description']) ? $conn->real_escape_string($_POST['description']) : '';
    $grade_levels = $_POST['grade_level'];

    // Check if subject code already exists
    $check_query = "SELECT id FROM subjects WHERE subject_code = '$subject_code' AND status = 'active'";
    $check_result = $conn->query($check_query);
    
    if ($check_result->num_rows > 0) {
        throw new Exception('Subject code already exists');
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // Insert into subjects table
        $query = "INSERT INTO subjects (subject_code, subject_title, category, description, status) 
                  VALUES ('$subject_code', '$subject_title', '$category', '$description', 'active')";
        
        if (!$conn->query($query)) {
            throw new Exception("Failed to add subject: " . $conn->error);
        }

        $subject_id = $conn->insert_id;

        // Insert grade levels
        foreach ($grade_levels as $grade) {
            $grade = $conn->real_escape_string($grade);
            $grade_query = "INSERT INTO subject_grade_levels (subject_id, grade_level) 
                           VALUES ($subject_id, '$grade')";
            
            if (!$conn->query($grade_query)) {
                throw new Exception("Failed to add grade level: " . $conn->error);
            }
        }

        // Commit transaction
        $conn->commit();

        echo json_encode([
            'status' => 'success',
            'message' => 'Subject added successfully'
        ]);

    } catch (Exception $e) {
        // Rollback transaction
        $conn->rollback();
        throw $e;
    }

} catch (Exception $e) {
    error_log("Add Subject Error: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>