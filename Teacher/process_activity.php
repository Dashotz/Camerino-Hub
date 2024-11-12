<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['teacher_id']) || empty($_POST)) {
    header("Location: manage_activities.php");
    exit();
}

$db = new DbConnector();
$teacher_id = $_SESSION['teacher_id'];

try {
    // Start transaction
    $db->begin_transaction();

    // Insert activity
    $activity_query = "INSERT INTO activities (
        teacher_id, 
        section_subject_id,
        title,
        description,
        type,
        points,
        due_date
    ) VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $db->prepare($activity_query);
    $stmt->bind_param("iisssss", 
        $teacher_id,
        $_POST['section_subject_id'],
        $_POST['title'],
        $_POST['description'],
        $_POST['type'],
        $_POST['points'],
        $_POST['due_date']
    );
    $stmt->execute();
    $activity_id = $db->insert_id;

    // Handle file upload if present
    if (isset($_FILES['activity_file']) && $_FILES['activity_file']['error'] == 0) {
        $file_name = $_FILES['activity_file']['name'];
        $file_type = $_FILES['activity_file']['type'];
        $file_size = $_FILES['activity_file']['size'];
        
        // Generate unique filename
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $new_filename = uniqid() . '.' . $file_ext;
        $upload_path = '../uploads/activities/' . $new_filename;
        
        // Move file to upload directory
        if (move_uploaded_file($_FILES['activity_file']['tmp_name'], $upload_path)) {
            $file_query = "INSERT INTO activity_files (
                activity_id,
                file_name,
                file_path,
                file_type,
                file_size
            ) VALUES (?, ?, ?, ?, ?)";
            
            $stmt = $db->prepare($file_query);
            $stmt->bind_param("isssi", 
                $activity_id,
                $file_name,
                $upload_path,
                $file_type,
                $file_size
            );
            $stmt->execute();
        }
    }

    // Commit transaction
    $db->commit();
    
    $_SESSION['success_message'] = "Activity created successfully!";
    header("Location: manage_activities.php");
    exit();

} catch (Exception $e) {
    // Rollback on error
    $db->rollback();
    $_SESSION['error_message'] = "Error creating activity: " . $e->getMessage();
    header("Location: manage_activities.php");
    exit();
}
