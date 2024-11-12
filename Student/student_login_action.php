<?php
session_start();
require_once('../db/dbConnector.php');

// Function to reset login attempts
function resetAttempts($db, $student_id) {
    $query = "UPDATE student SET login_attempts = 0, lockout_until = NULL 
              WHERE student_id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
}

// Function to check if account is locked
function isAccountLocked($lockout_time) {
    if (!$lockout_time) return false;
    return strtotime($lockout_time) > time();
}

if (isset($_POST['login'])) {
    $db = new DbConnector();
    
    // Escape and sanitize input
    $username = $db->escapeString($_POST['username']);
    $password = md5($_POST['password']); // Using MD5 hashing
    $remember = isset($_POST['remember']) ? true : false;
    
    // First check if this is a teacher account
    $query = "SELECT teacher_id FROM teacher WHERE username = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $teacher_result = $stmt->get_result();
    
    if ($teacher_result && $teacher_result->num_rows > 0) {
        $_SESSION['error_type'] = 'teacher_account';
        header("Location: Student-Login.php");
        exit();
    }

    // Check student credentials with MD5 password
    $query = "SELECT * FROM student WHERE username = ? AND password = ? AND status = 'active'";
    $stmt = $db->prepare($query);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Check if account is locked
        if (isAccountLocked($user['lockout_until'])) {
            $_SESSION['error_type'] = 'account_locked';
            $_SESSION['error_message'] = 'Account is temporarily locked. Please try again later.';
            header("Location: Student-Login.php");
            exit();
        }

        // Password is correct (already verified in the SELECT query)
        // Reset login attempts on successful login
        resetAttempts($db, $user['student_id']);
        
        // Set session variables
        $_SESSION['id'] = $user['student_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['firstname'] = $user['firstname'];
        $_SESSION['lastname'] = $user['lastname'];
        
        // Handle Remember Me functionality
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+30 days'));
            
            $query = "INSERT INTO remember_tokens (student_id, token, expiry) VALUES (?, ?, ?)";
            $stmt = $db->prepare($query);
            $stmt->bind_param("iss", $user['student_id'], $token, $expiry);
            $stmt->execute();
            
            setcookie('remember_token', $token, strtotime('+30 days'), '/', '', true, true);
        }
        
        // Log successful login
        $ip = $_SERVER['REMOTE_ADDR'];
        $query = "INSERT INTO student_login_logs (student_id, ip_address, status) VALUES (?, ?, 'success')";
        $stmt = $db->prepare($query);
        $stmt->bind_param("is", $user['student_id'], $ip);
        $stmt->execute();
        
        // Update user online status
        $query = "UPDATE student SET user_online = 1 WHERE student_id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("i", $user['student_id']);
        $stmt->execute();
        
        header("Location: home.php");
        exit();
    } else {
        // Check if username exists to determine error type
        $query = "SELECT student_id, login_attempts FROM student WHERE username = ? AND status = 'active'";
        $stmt = $db->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            // Username exists but password is wrong
            $user = $result->fetch_assoc();
            $attempts = $user['login_attempts'] + 1;
            $max_attempts = 5;
            
            if ($attempts >= $max_attempts) {
                $lockout_until = date('Y-m-d H:i:s', strtotime('+15 minutes'));
                $query = "UPDATE student SET login_attempts = ?, lockout_until = ? WHERE student_id = ?";
                $stmt = $db->prepare($query);
                $stmt->bind_param("isi", $attempts, $lockout_until, $user['student_id']);
                $_SESSION['error_type'] = 'max_attempts';
            } else {
                $query = "UPDATE student SET login_attempts = ? WHERE student_id = ?";
                $stmt = $db->prepare($query);
                $stmt->bind_param("ii", $attempts, $user['student_id']);
                $_SESSION['error_type'] = 'wrong_password';
            }
            $stmt->execute();
            
            // Log failed attempt
            $ip = $_SERVER['REMOTE_ADDR'];
            $query = "INSERT INTO student_login_logs (student_id, ip_address, status) VALUES (?, ?, 'failed')";
            $stmt = $db->prepare($query);
            $stmt->bind_param("is", $user['student_id'], $ip);
            $stmt->execute();
        } else {
            $_SESSION['error_type'] = 'wrong_username';
        }
    }
    
    header("Location: Student-Login.php");
    exit();
}

// Check Remember Me token
if (!isset($_SESSION['id']) && isset($_COOKIE['remember_token'])) {
    $db = new DbConnector();
    $token = $db->escapeString($_COOKIE['remember_token']);
    
    $query = "SELECT s.* FROM student s 
              JOIN remember_tokens rt ON s.student_id = rt.student_id 
              WHERE rt.token = ? AND rt.expiry > NOW() AND s.status = 'active'";
    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['id'] = $user['student_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['firstname'] = $user['firstname'];
        $_SESSION['lastname'] = $user['lastname'];
        
        header("Location: home.php");
        exit();
    }
}

header("Location: Student-Login.php");
exit();
?>

