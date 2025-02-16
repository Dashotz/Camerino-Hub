<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('../../db/dbConnector.php');

if (!isset($_SESSION['teacher_id']) || empty($_POST)) {
    $_SESSION['error_message'] = "Invalid access or empty form submission";
    header("Location: ../manage_activities.php");
    exit();
}

$db = new DbConnector();
$teacher_id = $_SESSION['teacher_id'];

try {
    // Validate required fields
    if (empty($_POST['section_subject_id']) || 
        empty($_POST['title']) || 
        empty($_POST['description']) || 
        empty($_POST['due_date']) || 
        !isset($_POST['points'])) {
        throw new Exception('All fields are required');
    }

    // Start transaction
    $db->begin_transaction();

    // Format due date
    $due_date = date('Y-m-d H:i:s', strtotime($_POST['due_date']));

    // Insert activity
    $activity_query = "INSERT INTO activities (
        teacher_id, 
        section_subject_id,
        title,
        description,
        type,
        points,
        due_date,
        status,
        created_at
    ) VALUES (?, ?, ?, ?, 'activity', ?, ?, 'active', NOW())";

    $stmt = $db->prepare($activity_query);
    $stmt->bind_param("iissis", 
        $teacher_id,
        $_POST['section_subject_id'],
        $_POST['title'],
        $_POST['description'],
        $_POST['points'],
        $due_date
    );

    if (!$stmt->execute()) {
        throw new Exception("Error executing query: " . $stmt->error);
    }

    // Get the activity ID right after insertion
    $activity_id = $stmt->insert_id;

    // Handle file uploads if any
    if (!empty($_FILES['activity_files']['name'][0])) {
        $allowed_types = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ];

        $upload_dir = '../../uploads/activities/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        foreach ($_FILES['activity_files']['tmp_name'] as $key => $tmp_name) {
            if (empty($tmp_name)) continue;

            $file_name = $_FILES['activity_files']['name'][$key];
            $file_type = $_FILES['activity_files']['type'][$key];
            $file_size = $_FILES['activity_files']['size'][$key];

            // Validate file type and size
            if (!in_array($file_type, $allowed_types)) {
                throw new Exception("Invalid file type: " . $file_name);
            }
            if ($file_size > 10 * 1024 * 1024) {
                throw new Exception("File too large: " . $file_name);
            }

            // Generate unique filename
            $timestamp = time();
            $unique_id = uniqid();
            $random_string = bin2hex(random_bytes(8));
            $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
            
            $unique_filename = $timestamp . '_' . $unique_id . '_' . $random_string . '.' . $file_extension;
            $file_path = $upload_dir . $unique_filename;
            $db_path = 'uploads/activities/' . $unique_filename;

            if (move_uploaded_file($tmp_name, $file_path)) {
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
                    $db_path,
                    $file_type,
                    $file_size
                );
                
                if (!$stmt->execute()) {
                    throw new Exception("Failed to save file information: " . $file_name);
                }
            } else {
                throw new Exception("Failed to upload file: " . $file_name);
            }
        }
    }

    $db->commit();
    $_SESSION['success_message'] = "Activity created successfully!";
    header("Location: ../manage_activities.php");
    exit();

} catch (Exception $e) {
    $db->rollback();
    
    // Clean up any uploaded files
    if (isset($activity_id)) {
        $query = "SELECT file_path FROM activity_files WHERE activity_id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("i", $activity_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $full_path = '../../' . $row['file_path'];
            if (file_exists($full_path)) {
                unlink($full_path);
            }
        }
    }

    error_log("Activity Creation Error: " . $e->getMessage());
    $_SESSION['error_message'] = "Error creating activity: " . $e->getMessage();
    header("Location: ../create_activity.php");
    exit();
}

$db->close(); 