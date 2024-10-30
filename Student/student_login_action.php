<?php
session_start();
require_once('../db/dbConnector.php');

if (isset($_POST['login'])) {
    $db = new DbConnector();
    
    // Escape input to prevent SQL injection
    $username = $db->escapeString($_POST['username']);
    $password = $db->escapeString($_POST['password']);
    
    // Check if username and password match directly
    $query = "SELECT * FROM student WHERE LOWER(username) = LOWER('$username') AND password = '$password'";
    $result = $db->query($query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_array($result);
        
        // Check if account is locked (3 or more attempts)
        if ($user['login_attempts'] >= 3) {
            $_SESSION['error_type'] = 'max_attempts';
            header("Location: Student-Login.php");
            exit();
        }
        
        // Reset login attempts on successful login
        $update_query = "UPDATE student SET login_attempts = 0 WHERE student_id = " . (int)$user['student_id'];
        $db->query($update_query);
        
        // Successful login
        $_SESSION['id'] = $user['student_id'];
        $_SESSION['just_logged_in'] = true;
        header("Location: home.php");
        exit();
    } else {
        // Wrong username or password
        $_SESSION['error_type'] = 'wrong_credentials';
        header("Location: Student-Login.php");
        exit();
    }
}

// If reached here, redirect back with generic error
$_SESSION['error_type'] = 'unknown';
header("Location: Student-Login.php");
exit();
?>

