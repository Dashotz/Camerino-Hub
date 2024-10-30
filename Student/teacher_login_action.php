<?php
session_start();
require_once('../db/dbConnector.php');

if (isset($_POST['login'])) {
    $db = new DbConnector();
    
    // Escape input to prevent SQL injection
    $username = $db->escapeString($_POST['username']);
    $password = $db->escapeString($_POST['password']);
    
    // Check if username and password match directly from teacher table
    $query = "SELECT * FROM teacher WHERE LOWER(username) = LOWER('$username') AND password = '$password'";
    $result = $db->query($query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_array($result);
        
        // Check if account is locked (3 or more attempts)
        if ($user['login_attempts'] >= 3) {
            $_SESSION['error_type'] = 'max_attempts';
            header("Location: Teacher-Login.php");
            exit();
        }
        
        // Reset login attempts on successful login
        $update_query = "UPDATE teacher SET login_attempts = 0 WHERE teacher_id = " . (int)$user['teacher_id'];
        $db->query($update_query);
        
        // Successful login
        $_SESSION['id'] = $user['teacher_id'];
        $_SESSION['just_logged_in'] = true;
        header("Location: ../teacher/home.php");
        exit();
    } else {
        // Check if username exists to determine specific error message
        $username_check = "SELECT * FROM teacher WHERE LOWER(username) = LOWER('$username')";
        $username_result = $db->query($username_check);
        
        if (mysqli_num_rows($username_result) > 0) {
            // Username exists but password is wrong
            $user = mysqli_fetch_array($username_result);
            
            // Increment login attempts
            $new_attempts = $user['login_attempts'] + 1;
            $update_query = "UPDATE teacher SET login_attempts = $new_attempts WHERE teacher_id = " . (int)$user['teacher_id'];
            $db->query($update_query);
            
            $_SESSION['error_type'] = 'wrong_password';
        } else {
            // Username doesn't exist
            $_SESSION['error_type'] = 'wrong_username';
        }
        
        header("Location: Teacher-Login.php");
        exit();
    }
}

// If reached here, redirect back with generic error
$_SESSION['error_type'] = 'unknown';
header("Location: Teacher-Login.php");
exit();
?>
