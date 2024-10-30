<?php
session_start();
require_once('../db/dbConnector.php');

if (isset($_POST['login'])) {
    $db = new DbConnector();
    
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Check if username exists
    $query = "SELECT * FROM student WHERE username = '$username'";
    $result = $db->query($query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_array($result);
        
        // Check if account is locked (3 or more attempts)
        if ($user['login_attempts'] >= 3) {
            $_SESSION['error_type'] = 'max_attempts';
            header("Location: Student-Login.php");
            exit();
        }
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Reset login attempts on successful login
            $update_query = "UPDATE student SET login_attempts = 0 WHERE student_id = '{$user['student_id']}'";
            $db->query($update_query);
            
            // Successful login
            $_SESSION['id'] = $user['student_id'];
            $_SESSION['just_logged_in'] = true;
            header("Location: home.php");
            exit();
        } else {
            // Increment login attempts
            $new_attempts = $user['login_attempts'] + 1;
            $update_query = "UPDATE student SET login_attempts = $new_attempts WHERE student_id = '{$user['student_id']}'";
            $db->query($update_query);
            
            $_SESSION['error_type'] = 'wrong_password';
            header("Location: Student-Login.php");
            exit();
        }
    } else {
        // Username not found
        $_SESSION['error_type'] = 'wrong_username';
        header("Location: Student-Login.php");
        exit();
    }
}

// If reached here, redirect back with generic error
$_SESSION['error_type'] = 'unknown';
header("Location: Student-Login.php");
exit();
?>

