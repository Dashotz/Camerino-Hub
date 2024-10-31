<?php
session_start();
require_once('../db/dbConnector.php');

// Function to reset login attempts
function resetAttempts($db, $student_id) {
    $query = "UPDATE student SET login_attempts = 0, lockout_until = NULL WHERE student_id = $student_id";
    $db->query($query);
}

// Function to check if account is locked
function isAccountLocked($lockout_time) {
    if (!$lockout_time) return false;
    return strtotime($lockout_time) > time();
}

if (isset($_POST['login'])) {
    $db = new DbConnector();
    
    // Escape input to prevent SQL injection
    $username = $db->escapeString($_POST['username']);
    $password = $db->escapeString($_POST['password']);
    $remember = isset($_POST['remember']) ? true : false;
    
    // First check if user exists
    $query = "SELECT * FROM student WHERE username = '$username'";
    $result = $db->query($query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_array($result);
        
        // Check if account is locked
        if (isAccountLocked($user['lockout_until'])) {
            $_SESSION['error_type'] = 'account_locked';
            $_SESSION['error_message'] = 'Account is locked. Please try again later.';
            header("Location: Student-Login.php");
            exit();
        }
        
        // Verify credentials
        if ($user['password'] === $password) {
            // Login successful
            $_SESSION['id'] = $user['student_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['just_logged_in'] = true;
            
            // Reset login attempts
            resetAttempts($db, $user['student_id']);
            
            // Handle Remember Me
            if ($remember) {
                $token = bin2hex(random_bytes(32)); // Generate secure token
                $expiry = date('Y-m-d H:i:s', strtotime('+30 days'));
                
                // Store token in database
                $query = "INSERT INTO remember_tokens (student_id, token, expiry) 
                         VALUES ({$user['student_id']}, '$token', '$expiry')";
                $db->query($query);
                
                // Set remember me cookie
                setcookie('remember_token', $token, time() + (86400 * 30), '/', '', true, true); // 30 days
            }
            
            // Log successful login
            $ip = $_SERVER['REMOTE_ADDR'];
            $query = "INSERT INTO login_logs (student_id, ip_address, status) 
                     VALUES ({$user['student_id']}, '$ip', 'success')";
            $db->query($query);
            
            header("Location: home.php");
            exit();
        } else {
            // Failed login
            $attempts = $user['login_attempts'] + 1;
            
            // Update attempts and possibly lock account
            if ($attempts >= 3) {
                $lockout_until = date('Y-m-d H:i:s', strtotime('+15 minutes'));
                $query = "UPDATE student SET login_attempts = $attempts, 
                         lockout_until = '$lockout_until' 
                         WHERE student_id = {$user['student_id']}";
                $_SESSION['error_type'] = 'max_attempts';
            } else {
                $query = "UPDATE student SET login_attempts = $attempts 
                         WHERE student_id = {$user['student_id']}";
                $_SESSION['error_type'] = 'wrong_password';
            }
            $db->query($query);
            
            // Log failed attempt
            $ip = $_SERVER['REMOTE_ADDR'];
            $query = "INSERT INTO login_logs (student_id, ip_address, status) 
                     VALUES ({$user['student_id']}, '$ip', 'failed')";
            $db->query($query);
        }
    } else {
        $_SESSION['error_type'] = 'wrong_username';
    }
    
    header("Location: Student-Login.php");
    exit();
}

// Check Remember Me token on page load
if (!isset($_SESSION['id']) && isset($_COOKIE['remember_token'])) {
    $token = $db->escapeString($_COOKIE['remember_token']);
    $query = "SELECT s.* FROM student s 
              JOIN remember_tokens rt ON s.student_id = rt.student_id 
              WHERE rt.token = '$token' AND rt.expiry > NOW()";
    $result = $db->query($query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_array($result);
        $_SESSION['id'] = $user['student_id'];
        $_SESSION['username'] = $user['username'];
    }
}

header("Location: Student-Login.php");
exit();
?>

