<?php
session_start();
require_once('../db/dbConnector.php');

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['teacher_id'])) {
        throw new Exception('Not authorized');
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    if (!isset($_POST['current_password']) || !isset($_POST['new_password'])) {
        throw new Exception('Missing required fields');
    }

    $db = new DbConnector();
    $teacher_id = $_SESSION['teacher_id'];
    $current_password = $_POST['current_password']; // Already MD5 hashed
    $new_password = $_POST['new_password']; // Already MD5 hashed
    
    // Verify current password
    $stmt = $db->prepare("SELECT password FROM teacher WHERE teacher_id = ?");
    if (!$stmt) {
        throw new Exception('Database error');
    }
    
    $stmt->bind_param("i", $teacher_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $teacher = $result->fetch_assoc();
    
    if (!$teacher) {
        throw new Exception('Teacher not found');
    }

    if ($teacher['password'] !== $current_password) {
        throw new Exception('Current password is incorrect');
    }
    
    // Update password and reset password_recovery status
    $updateStmt = $db->prepare("UPDATE teacher SET password = ?, password_recovery = 'no' WHERE teacher_id = ?");
    if (!$updateStmt) {
        throw new Exception('Database error');
    }
    
    $updateStmt->bind_param("si", $new_password, $teacher_id);
    
    if (!$updateStmt->execute()) {
        throw new Exception('Failed to update password');
    }
    
    // Clear the password change requirement session variable
    unset($_SESSION['require_password_change']);
    
    echo json_encode([
        'success' => true,
        'message' => 'Password updated successfully'
    ]);

} catch (Exception $e) {
    error_log('Password update error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 