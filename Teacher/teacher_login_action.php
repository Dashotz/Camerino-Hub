<?php
session_start();
require_once('../db/dbConnector.php');

if (isset($_POST['login'])) {
    $db = new DbConnector();
    
    $username = $db->escapeString($_POST['username']);
    $password = md5($db->escapeString($_POST['password']));
    
    // First check if username exists in student table (using LRN)
    $student_check = "SELECT * FROM student WHERE lrn = ?";
    $stmt = $db->prepare($student_check);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $student_result = $stmt->get_result();
    
    if ($student_result && $student_result->num_rows > 0) {
        $_SESSION['error_type'] = 'student_account';
        header("Location: Teacher-Login.php");
        exit();
    }
    
    // Check teacher credentials - modified query
    $query = "SELECT * FROM teacher WHERE username = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Check if account is archived
        if ($user['status'] === 'archived') {
            $_SESSION['error_type'] = 'archived_account';
            $_SESSION['error_message'] = 'Your account has been archived. Please contact the administrator.';
            header("Location: Teacher-Login.php");
            exit();
        }
        
        // Check if account is inactive
        if ($user['status'] !== 'active') {
            $_SESSION['error_type'] = 'inactive_account';
            $_SESSION['error_message'] = 'Your account is currently inactive. Please contact the administrator.';
            header("Location: Teacher-Login.php");
            exit();
        }
        
        // Check if account is locked
        if ($user['login_attempts'] >= 3 && $user['lockout_time'] > date('Y-m-d H:i:s')) {
            $_SESSION['error_type'] = 'max_attempts';
            header("Location: Teacher-Login.php");
            exit();
        } else if ($user['lockout_time'] !== null && $user['lockout_time'] <= date('Y-m-d H:i:s')) {
            // Reset lockout if time has passed
            $reset_query = "UPDATE teacher SET login_attempts = 0, lockout_time = NULL 
                          WHERE teacher_id = ?";
            $stmt = $db->prepare($reset_query);
            $stmt->bind_param("i", $user['teacher_id']);
            $stmt->execute();
            $user['login_attempts'] = 0;
        }
        
        // Verify credentials with MD5 hash
        if ($password === $user['password']) {
            // Log successful login
            $ip = $_SERVER['REMOTE_ADDR'];
            $log_query = "INSERT INTO teacher_login_logs (teacher_id, ip_address, status) 
                         VALUES (?, ?, 'success')";
            $stmt = $db->prepare($log_query);
            $stmt->bind_param("is", $user['teacher_id'], $ip);
            $stmt->execute();
            
            // Reset attempts
            $reset_query = "UPDATE teacher SET login_attempts = 0, lockout_time = NULL 
                          WHERE teacher_id = ?";
            $stmt = $db->prepare($reset_query);
            $stmt->bind_param("i", $user['teacher_id']);
            $stmt->execute();
            
            // Set session variables
            $_SESSION['teacher_id'] = $user['teacher_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['firstname'] = $user['firstname'];
            $_SESSION['lastname'] = $user['lastname'];
            $_SESSION['middlename'] = $user['middlename'];
            $_SESSION['department'] = $user['department'];
            $_SESSION['just_logged_in'] = true;
            $_SESSION['last_activity'] = time();
            
            // Check if this is a temporary password login
            $check_temp_pwd = "SELECT password_recovery FROM teacher 
                             WHERE teacher_id = ? 
                             AND password_recovery = 'yes'";
            $stmt = $db->prepare($check_temp_pwd);
            $stmt->bind_param("i", $user['teacher_id']);
            $stmt->execute();
            $temp_pwd_result = $stmt->get_result();
            
            if ($temp_pwd_result && $temp_pwd_result->num_rows > 0) {
                // Redirect to profile settings with password change requirement
                $_SESSION['require_password_change'] = true;
                header("Location: teacher_profile.php?tab=settings");
                exit();
            }
            
            // Normal login redirect
            header("Location: teacher_dashboard.php");
            exit();
        }
        
        // Failed login - increment attempts
        $attempts = $user['login_attempts'] + 1;
        $lockout_time = ($attempts >= 3) ? date('Y-m-d H:i:s', strtotime('+2 minutes')) : null;
        
        $update_query = "UPDATE teacher SET login_attempts = ?, 
                        lockout_time = ? 
                        WHERE teacher_id = ?";
        $stmt = $db->prepare($update_query);
        $stmt->bind_param("isi", $attempts, $lockout_time, $user['teacher_id']);
        $stmt->execute();
        
        $_SESSION['error_type'] = ($attempts >= 3) ? 'max_attempts' : 'wrong_password';
        header("Location: Teacher-Login.php");
        exit();
    }
    
    $_SESSION['error_type'] = 'wrong_username';
    header("Location: Teacher-Login.php");
    exit();
}

$_SESSION['error_type'] = 'invalid_request';
header("Location: Teacher-Login.php");
exit();
?>
