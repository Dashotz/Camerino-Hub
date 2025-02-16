<?php
session_start();
require_once('../../db/dbConnector.php');

if (!isset($_SESSION['teacher_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

$db = new DbConnector();
$teacher_id = $_SESSION['teacher_id'];

try {
    // Validate required fields
    $required_fields = ['title', 'description', 'section_subject_id', 'due_date', 'points'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("All fields are required");
        }
    }

    // Start transaction
    $db->begin_transaction();

    // Prepare assignment data
    $data = [
        'title' => $_POST['title'],
        'description' => $_POST['description'],
        'section_subject_id' => $_POST['section_subject_id'],
        'type' => 'assignment',
        'due_date' => $_POST['due_date'],
        'points' => $_POST['points'],
        'teacher_id' => $teacher_id,
        'status' => 'active',
        'created_at' => date('Y-m-d H:i:s')
    ];

    // Insert assignment
    $query = "INSERT INTO activities (
                title, 
                description, 
                section_subject_id, 
                type, 
                due_date, 
                points, 
                teacher_id, 
                status, 
                created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $db->prepare($query);
    $stmt->bind_param(
        "ssississs", 
        $data['title'],
        $data['description'],
        $data['section_subject_id'],
        $data['type'],
        $data['due_date'],
        $data['points'],
        $data['teacher_id'],
        $data['status'],
        $data['created_at']
    );
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to save assignment");
    }

    $assignment_id = $stmt->insert_id;

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

        // Change upload directory to match the desired path
        $upload_dir = '../../../uploads/activities/';
        
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        foreach ($_FILES['activity_files']['tmp_name'] as $key => $tmp_name) {
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

            // Generate unique filename with timestamp format
            $timestamp = time();
            $unique_id = uniqid();
            $random_string = bin2hex(random_bytes(8));
            $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
            
            $unique_filename = $timestamp . '_' . $unique_id . '_' . $random_string . '.' . $file_extension;
            $file_path = $upload_dir . $unique_filename;
            $db_path = 'uploads/activities/' . $unique_filename; // Path to store in database

            // Move and save file
            if (move_uploaded_file($tmp_name, $file_path)) {
                $query = "INSERT INTO activity_files (
                            activity_id, 
                            file_name, 
                            file_path, 
                            file_type,
                            file_size
                        ) VALUES (?, ?, ?, ?, ?)";
                
                $stmt = $db->prepare($query);
                $stmt->bind_param(
                    "isssi",
                    $assignment_id,
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

    // Commit transaction
    $db->commit();

    // Send success response
    echo json_encode([
        'success' => true,
        'message' => 'Assignment created successfully',
        'assignment_id' => $assignment_id
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    $db->rollback();

    // Delete any uploaded files if they exist
    if (isset($assignment_id) && isset($_FILES['activity_files'])) {
        $query = "SELECT file_path FROM activity_files WHERE activity_id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("i", $assignment_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            if (file_exists($row['file_path'])) {
                unlink($row['file_path']);
            }
        }
    }

    // Send error response
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// Close database connection
$db->close();
