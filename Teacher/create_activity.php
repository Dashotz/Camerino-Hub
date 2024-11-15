<?php
// Prevent any output before JSON response
ob_start();

session_start();
require_once('../db/dbConnector.php');

// Set JSON content type header
header('Content-Type: application/json');

// Check authentication
if (!isset($_SESSION['teacher_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

$db = new DbConnector();
$teacher_id = $_SESSION['teacher_id'];

try {
    // Start transaction using the correct method name
    $db->beginTransaction();

    // Check for duplicate activity title for the same section within a time window
    $check_duplicate_sql = "
        SELECT COUNT(*) as count 
        FROM activities a 
        WHERE a.section_subject_id = ? 
        AND a.title = ? 
        AND a.created_at >= NOW() - INTERVAL 5 MINUTE";

    $stmt = $db->prepare($check_duplicate_sql);
    if (!$stmt) {
        throw new Exception('Failed to prepare duplicate check statement');
    }

    $stmt->bind_param("is", 
        $_POST['section_subject_id'],
        $_POST['title']
    );

    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result['count'] > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'An activity with this title was recently created. Please wait a few minutes before trying again or use a different title.'
        ]);
        exit();
    }

    // Create the activity with teacher_id
    $activity_sql = "INSERT INTO activities (
        section_subject_id,
        teacher_id,
        title,
        description,
        type,
        due_date,
        points,
        status,
        created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, 'active', NOW())";

    $stmt = $db->prepare($activity_sql);
    if (!$stmt) {
        throw new Exception('Failed to prepare statement');
    }

    $stmt->bind_param("iissssi", 
        $_POST['section_subject_id'],
        $teacher_id,
        $_POST['title'],
        $_POST['description'],
        $_POST['type'],
        $_POST['due_date'],
        $_POST['points']
    );

    if (!$stmt->execute()) {
        throw new Exception('Failed to create activity: ' . $stmt->error);
    }
    
    // Get the last inserted activity_id using the correct method
    $activity_id = $db->lastInsertId();
    
    if (!$activity_id) {
        throw new Exception('Failed to get activity ID');
    }

    // Handle file uploads
    if (isset($_FILES['activity_files']) && !empty($_FILES['activity_files']['name'][0])) {
        // Define upload directory
        $base_dir = dirname(dirname(__FILE__));
        $upload_dir = $base_dir . '/uploads/activities/';
        
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Track processed files to prevent duplication
        $processed_files = [];

        foreach ($_FILES['activity_files']['tmp_name'] as $key => $tmp_name) {
            // Skip empty or already processed files
            if (empty($tmp_name) || in_array($tmp_name, $processed_files)) {
                continue;
            }
            
            $file_name = $_FILES['activity_files']['name'][$key];
            $file_size = $_FILES['activity_files']['size'][$key];
            $file_type = $_FILES['activity_files']['type'][$key];
            
            // Validate file type
            $allowed_types = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                             'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                             'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
            
            if (!in_array($file_type, $allowed_types)) {
                throw new Exception('Invalid file type: ' . $file_name);
            }
            
            // Generate unique filename with timestamp and random string
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $unique_file_name = uniqid(time() . '_') . '_' . bin2hex(random_bytes(8)) . '.' . $file_ext;
            
            // Create relative path for database
            $db_file_path = 'uploads/activities/' . $unique_file_name;
            
            // Create full server path for move_uploaded_file
            $full_upload_path = $upload_dir . $unique_file_name;
            
            // Move file
            if (!move_uploaded_file($tmp_name, $full_upload_path)) {
                throw new Exception('Failed to upload file: ' . $file_name);
            }
            
            // Add to processed files
            $processed_files[] = $tmp_name;
            
            // Save file information to database
            $file_sql = "INSERT INTO activity_files (
                activity_id,
                file_name,
                file_path,
                file_type,
                file_size,
                created_at
            ) VALUES (?, ?, ?, ?, ?, NOW())";
            
            $stmt = $db->prepare($file_sql);
            if (!$stmt) {
                // Clean up uploaded file if statement preparation fails
                if (file_exists($full_upload_path)) {
                    unlink($full_upload_path);
                }
                throw new Exception('Failed to prepare file statement');
            }

            $stmt->bind_param("isssi", 
                $activity_id,
                $file_name,
                $db_file_path,
                $file_type,
                $file_size
            );

            if (!$stmt->execute()) {
                // Clean up uploaded file if database insert fails
                if (file_exists($full_upload_path)) {
                    unlink($full_upload_path);
                }
                throw new Exception('Failed to save file information: ' . $stmt->error);
            }
        }
    }

    $db->commit();
    
    // Clear any output buffers
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    echo json_encode([
        'success' => true, 
        'message' => 'Activity created successfully',
        'activity_id' => $activity_id
    ]);

} catch (Exception $e) {
    $db->rollback();
    
    // Clear any output buffers
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
}

exit();
?>
