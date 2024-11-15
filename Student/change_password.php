<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['id'])) {
    header('Location: Student-Login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new DbConnector();
    $student_id = $_SESSION['id'];
    
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    try {
        // Verify current password
        $stmt = $db->prepare("SELECT password FROM student WHERE student_id = ?");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if (!password_verify($current_password, $user['password'])) {
            $_SESSION['error_message'] = "Current password is incorrect.";
            header('Location: student_profile.php');
            exit();
        }
        
        // Validate new password
        if ($new_password !== $confirm_password) {
            $_SESSION['error_message'] = "New passwords do not match.";
            header('Location: student_profile.php');
            exit();
        }
        
        if (strlen($new_password) < 8) {
            $_SESSION['error_message'] = "Password must be at least 8 characters long.";
            header('Location: student_profile.php');
            exit();
        }
        
        // Update password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $updateStmt = $db->prepare("UPDATE student SET password = ? WHERE student_id = ?");
        $updateStmt->bind_param("si", $hashed_password, $student_id);
        
        if ($updateStmt->execute()) {
            $_SESSION['success_message'] = "Password changed successfully!";
        } else {
            $_SESSION['error_message'] = "Failed to update password.";
        }
        
    } catch (Exception $e) {
        $_SESSION['error_message'] = "An error occurred. Please try again.";
    }
    
    header('Location: student_profile.php');
    exit();
}
?> 