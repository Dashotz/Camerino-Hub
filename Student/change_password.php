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
    
    $current_password = md5($_POST['current_password']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Get current password from database
    $stmt = $db->prepare("SELECT password FROM student WHERE student_id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    // Check if current password is correct using MD5
    if ($current_password !== $user['password']) {
        $_SESSION['error_message'] = "Current password is incorrect.";
    } elseif ($new_password !== $confirm_password) {
        $_SESSION['error_message'] = "New passwords do not match.";
    } elseif (strlen($new_password) < 8) {
        $_SESSION['error_message'] = "Password must be at least 8 characters long.";
    } elseif (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/", $new_password)) {
        $_SESSION['error_message'] = "Password must contain at least one uppercase letter, one lowercase letter, and one number.";
    } else {
        // Hash new password with MD5
        $hashed_password = md5($new_password);
        
        // Update password and reset password recovery status
        $update_stmt = $db->prepare("UPDATE student 
                                   SET password = ?, 
                                       password_recovery = 'no' 
                                   WHERE student_id = ?");
        $update_stmt->bind_param("si", $hashed_password, $student_id);
        
        if ($update_stmt->execute()) {
            // Clear the password change requirement
            unset($_SESSION['require_password_change']);
            
            // Show success message with SweetAlert
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Your password has been updated successfully.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        window.location.href = 'student_profile.php?tab=security';
                    });
                });
            </script>";
            exit();
        } else {
            $_SESSION['error_message'] = "Failed to update password.";
        }
    }
    
    if (isset($_SESSION['error_message'])) {
        // Show error message with SweetAlert
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Error!',
                    text: '" . $_SESSION['error_message'] . "',
                    icon: 'error',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    window.location.href = 'student_profile.php?tab=security';
                });
            });
        </script>";
        unset($_SESSION['error_message']);
        exit();
    }
}

// If no POST request, redirect back to profile
header('Location: student_profile.php?tab=security');
exit();
?> 