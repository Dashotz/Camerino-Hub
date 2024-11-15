<?php
session_start();
require_once('../db/dbConnector.php');

header('Content-Type: application/json');

if (!isset($_SESSION['teacher_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

$db = new DbConnector();
$teacher_id = $_SESSION['teacher_id'];

try {
    $updates = [];
    $params = [];
    $types = "";

    // Handle email update
    if (!empty($_POST['email'])) {
        $updates[] = "email = ?";
        $params[] = $_POST['email'];
        $types .= "s";
    }

    // Handle password update
    if (!empty($_POST['current_password']) && !empty($_POST['new_password'])) {
        // Verify current password
        $verify_query = "SELECT password FROM teacher WHERE teacher_id = ?";
        $stmt = $db->prepare($verify_query);
        $stmt->bind_param("i", $teacher_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if (md5($_POST['current_password']) !== $result['password']) {
            throw new Exception('Current password is incorrect');
        }

        if ($_POST['new_password'] !== $_POST['confirm_password']) {
            throw new Exception('New passwords do not match');
        }

        $updates[] = "password = ?";
        $params[] = md5($_POST['new_password']);
        $types .= "s";
    }

    // Handle profile image upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['profile_image']['type'], $allowed_types)) {
            throw new Exception('Invalid file type. Only JPG, PNG and GIF are allowed.');
        }

        $upload_dir = '../uploads/teacher_profiles/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_ext = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
        $new_filename = 'teacher_' . $teacher_id . '_' . time() . '.' . $file_ext;
        $upload_path = $upload_dir . $new_filename;

        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_path)) {
            $updates[] = "profile_image = ?";
            $params[] = 'uploads/teacher_profiles/' . $new_filename;
            $types .= "s";
        }
    }

    if (!empty($updates)) {
        $sql = "UPDATE teacher SET " . implode(", ", $updates) . " WHERE teacher_id = ?";
        $params[] = $teacher_id;
        $types .= "i";

        $stmt = $db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to update profile');
        }

        echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No changes to update']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 