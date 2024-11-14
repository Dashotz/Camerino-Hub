<?php
session_start();
require_once('../db/dbConnector.php');

header('Content-Type: application/json');

if (!isset($_SESSION['teacher_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$db = new DbConnector();
$teacher_id = $_SESSION['teacher_id'];

if (!isset($_POST['announcement_id']) || !isset($_POST['content'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$announcement_id = (int)$_POST['announcement_id'];
$section_id = (int)$_POST['section_id'];
$subject_id = (int)$_POST['subject_id'];
$content = trim($_POST['content']);

// Verify ownership
$verify_query = "SELECT attachment FROM announcements 
                WHERE id = ? AND teacher_id = ? AND status = 'active' 
                LIMIT 1";
$stmt = $db->prepare($verify_query);
$stmt->bind_param("ii", $announcement_id, $teacher_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$current_announcement = $result->fetch_assoc();

// Handle file upload if present
$attachment_path = null;
if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = '../uploads/announcements/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $file_extension = strtolower(pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION));
    $allowed_extensions = ['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png'];
    
    if (!in_array($file_extension, $allowed_extensions)) {
        echo json_encode(['success' => false, 'message' => 'Invalid file type']);
        exit;
    }
    
    $file_name = uniqid() . '.' . $file_extension;
    $upload_path = $upload_dir . $file_name;
    
    if (move_uploaded_file($_FILES['attachment']['tmp_name'], $upload_path)) {
        $attachment_path = 'uploads/announcements/' . $file_name;
        
        // Delete old attachment if exists
        if ($current_announcement['attachment']) {
            $old_file = '../' . $current_announcement['attachment'];
            if (file_exists($old_file)) {
                unlink($old_file);
            }
        }
    }
}

// Update announcement
$update_query = "UPDATE announcements SET 
                section_id = ?, 
                subject_id = ?, 
                content = ?";
$params = [$section_id, $subject_id, $content];
$types = "iis";

if ($attachment_path) {
    $update_query .= ", attachment = ?";
    $params[] = $attachment_path;
    $types .= "s";
}

$update_query .= " WHERE id = ? AND teacher_id = ? AND status = 'active'";
$params[] = $announcement_id;
$params[] = $teacher_id;
$types .= "ii";

$stmt = $db->prepare($update_query);
$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error updating announcement']);
}
