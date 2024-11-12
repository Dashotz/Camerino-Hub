<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['teacher_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

$db = new DbConnector();
$teacher_id = $_SESSION['teacher_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['type'])) {
        if ($_POST['type'] === 'activity') {
            handleActivityUpload($db, $teacher_id);
        } elseif ($_POST['type'] === 'quiz') {
            handleQuizUpload($db, $teacher_id);
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'delete') {
        handleDelete($db, $teacher_id);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get') {
    $activity_id = $_GET['activity_id'];
    
    $query = "SELECT a.*, c.section_name 
              FROM activities a 
              JOIN classes c ON a.class_id = c.class_id 
              WHERE a.activity_id = ? AND a.teacher_id = ?";
    
    $stmt = $db->prepare($query);
    $stmt->bind_param("ii", $activity_id, $teacher_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    if ($result) {
        echo json_encode($result);
    } else {
        echo json_encode(['error' => 'Activity not found']);
    }
    exit();
}

function handleActivityUpload($db, $teacher_id) {
    // Validate inputs
    if (!isset($_POST['title'], $_POST['class_id'], $_POST['due_date'], $_FILES['activity_file'])) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        return;
    }

    $title = $_POST['title'];
    $description = $_POST['description'] ?? '';
    $class_id = $_POST['class_id'];
    $due_date = $_POST['due_date'];
    $file = $_FILES['activity_file'];

    // Validate file
    $allowed_types = ['application/pdf', 'application/vnd.ms-powerpoint', 
                     'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                     'application/msword', 
                     'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    
    if (!in_array($file['type'], $allowed_types)) {
        echo json_encode(['success' => false, 'message' => 'Invalid file type']);
        return;
    }

    // Create upload directory if it doesn't exist
    $upload_dir = '../uploads/activities/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Generate unique filename
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $file_name = uniqid('activity_') . '.' . $file_extension;
    $file_path = $upload_dir . $file_name;

    // Upload file
    if (move_uploaded_file($file['tmp_name'], $file_path)) {
        // Save to database
        $query = "INSERT INTO activities (teacher_id, class_id, title, description, type, file_path, due_date) 
                 VALUES (?, ?, ?, ?, 'activity', ?, ?)";
        
        $stmt = $db->prepare($query);
        $relative_path = 'uploads/activities/' . $file_name;
        $stmt->bind_param("iissss", $teacher_id, $class_id, $title, $description, $relative_path, $due_date);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'File upload failed']);
    }
}

function handleQuizUpload($db, $teacher_id) {
    if (!isset($_POST['title'], $_POST['class_id'], $_POST['due_date'], $_POST['quiz_link'])) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        return;
    }

    $title = $_POST['title'];
    $description = $_POST['description'] ?? '';
    $class_id = $_POST['class_id'];
    $due_date = $_POST['due_date'];
    $quiz_link = $_POST['quiz_link'];

    // Validate quiz link
    if (!filter_var($quiz_link, FILTER_VALIDATE_URL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid quiz link']);
        return;
    }

    $query = "INSERT INTO activities (teacher_id, class_id, title, description, type, quiz_link, due_date) 
             VALUES (?, ?, ?, ?, 'quiz', ?, ?)";
    
    $stmt = $db->prepare($query);
    $stmt->bind_param("iissss", $teacher_id, $class_id, $title, $description, $quiz_link, $due_date);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}

function handleDelete($db, $teacher_id) {
    if (!isset($_POST['activity_id'])) {
        echo json_encode(['success' => false, 'message' => 'Missing activity ID']);
        return;
    }

    $activity_id = $_POST['activity_id'];

    // Verify ownership
    $verify_query = "SELECT file_path, type FROM activities 
                    WHERE activity_id = ? AND teacher_id = ?";
    $stmt = $db->prepare($verify_query);
    $stmt->bind_param("ii", $activity_id, $teacher_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if (!$result) {
        echo json_encode(['success' => false, 'message' => 'Activity not found']);
        return;
    }

    // Delete file if it's an activity
    if ($result['type'] === 'activity' && $result['file_path']) {
        $file_path = '../' . $result['file_path'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }

    // Delete from database
    $delete_query = "DELETE FROM activities WHERE activity_id = ?";
    $stmt = $db->prepare($delete_query);
    $stmt->bind_param("i", $activity_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Delete failed']);
    }
}

function handleUpdate($db, $teacher_id) {
    if (!isset($_POST['activity_id'], $_POST['title'], $_POST['class_id'], $_POST['due_date'])) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        return;
    }

    $activity_id = $_POST['activity_id'];
    $title = $_POST['title'];
    $description = $_POST['description'] ?? '';
    $class_id = $_POST['class_id'];
    $due_date = $_POST['due_date'];
    $type = $_POST['type'];

    // Verify ownership
    $verify_query = "SELECT * FROM activities WHERE activity_id = ? AND teacher_id = ?";
    $stmt = $db->prepare($verify_query);
    $stmt->bind_param("ii", $activity_id, $teacher_id);
    $stmt->execute();
    if (!$stmt->get_result()->fetch_assoc()) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }

    if ($type === 'quiz') {
        $quiz_link = $_POST['quiz_link'];
        $query = "UPDATE activities SET title = ?, description = ?, class_id = ?, 
                  quiz_link = ?, due_date = ? WHERE activity_id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("ssissi", $title, $description, $class_id, $quiz_link, $due_date, $activity_id);
    } else {
        // Handle file update if new file is uploaded
        $file_update = "";
        $file_path = null;
        
        if (isset($_FILES['activity_file']) && $_FILES['activity_file']['size'] > 0) {
            $file = $_FILES['activity_file'];
            $upload_result = handleFileUpload($file);
            
            if (!$upload_result['success']) {
                echo json_encode($upload_result);
                return;
            }
            
            $file_path = $upload_result['file_path'];
            $file_update = ", file_path = ?";
        }

        $query = "UPDATE activities SET title = ?, description = ?, class_id = ?, 
                  due_date = ?" . $file_update . " WHERE activity_id = ?";
        
        $stmt = $db->prepare($query);
        if ($file_path) {
            $stmt->bind_param("ssiss", $title, $description, $class_id, $due_date, $file_path, $activity_id);
        } else {
            $stmt->bind_param("ssisi", $title, $description, $class_id, $due_date, $activity_id);
        }
    }

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Update failed']);
    }
}
?>
