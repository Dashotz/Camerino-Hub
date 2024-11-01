<?php
session_start();
require_once('../db/dbConnector.php');

if (isset($_POST['login'])) {
    $db = new DbConnector();
    
    $username = $db->escapeString($_POST['username']);
    $password = $db->escapeString($_POST['password']);
    
    // First check if username exists in student table
    $student_check = "SELECT * FROM student WHERE LOWER(username) = LOWER('$username')";
    $student_result = $db->query($student_check);
    
    if ($student_result && mysqli_num_rows($student_result) > 0) {
        header("Location: Teacher-Login.php");
        $_SESSION['error_type'] = 'student_account';
        exit();
    }
    
    // Continue with teacher login check
    $query = "SELECT * FROM teacher WHERE LOWER(username) = LOWER('$username')";
    $result = $db->query($query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_array($result);
        
        // Check if account is locked
        if ($user['lockout_time'] !== null) {
            $lockout_time = strtotime($user['lockout_time']);
            $current_time = time();
            
            if ($current_time < $lockout_time) {
                header("Location: Teacher-Login.php");
                $_SESSION['error_type'] = 'max_attempts';
                exit();
            } else {
                // Reset lockout if time has passed
                $reset_query = "UPDATE teacher SET login_attempts = 0, lockout_time = NULL 
                              WHERE teacher_id = '{$user['teacher_id']}'";
                $db->query($reset_query);
                $user['login_attempts'] = 0;
            }
        }
        
        // Verify credentials
        if ($password === $user['password']) {
            // Successful login - reset attempts
            $reset_query = "UPDATE teacher SET login_attempts = 0, lockout_time = NULL 
                          WHERE teacher_id = '{$user['teacher_id']}'";
            $db->query($reset_query);
            
            $_SESSION['id'] = $user['teacher_id'];
            $_SESSION['just_logged_in'] = true;
            header("Location: ../teacher/home.php");
            exit();
        }
        
        // Failed login - increment attempts
        $attempts = $user['login_attempts'] + 1;
        $lockout_time = ($attempts >= 3) ? date('Y-m-d H:i:s', strtotime('+2 minutes')) : null;
        
        $update_query = "UPDATE teacher SET login_attempts = $attempts, 
                        lockout_time = " . ($lockout_time ? "'$lockout_time'" : "NULL") . " 
                        WHERE teacher_id = '{$user['teacher_id']}'";
        $db->query($update_query);
        
        if ($attempts >= 3) {
            header("Location: Teacher-Login.php");
            $_SESSION['error_type'] = 'max_attempts';
        } else {
            header("Location: Teacher-Login.php");
            $_SESSION['error_type'] = 'wrong_password';
        }
        exit();
    }
    
    header("Location: Teacher-Login.php");
    $_SESSION['error_type'] = 'wrong_username';
    exit();
}

header("Location: Teacher-Login.php");
$_SESSION['error_type'] = 'wrong_username';
exit();
?>
