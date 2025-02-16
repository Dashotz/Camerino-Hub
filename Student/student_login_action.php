<?php
session_start();
require_once('../db/dbConnector.php');

// Function to reset login attempts
function resetAttempts($db, $lrn) {
    $query = "UPDATE student SET login_attempts = 0, lockout_until = NULL 
              WHERE lrn = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $lrn);
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
    $lrn = $db->escapeString($_POST['lrn']);
    $password = md5($_POST['password']); 
    
    // First check if user exists and is already logged in
    $check_online_query = "SELECT user_online, session_id, last_activity FROM student WHERE lrn = ? AND status = 'active'";
    $stmt = $db->prepare($check_online_query);
    $stmt->bind_param("s", $lrn);
    $stmt->execute();
    $online_result = $stmt->get_result();
    
    if ($online_result && $online_result->num_rows > 0) {
        $online_status = $online_result->fetch_assoc();
        
        // Check if user is already logged in
        if ($online_status['user_online'] == 1) {
            // Check if last activity was more than 30 minutes ago
            $last_activity = strtotime($online_status['last_activity']);
            $current_time = time();
            $inactive_threshold = 1800; // 30 minutes in seconds
            
            if (($current_time - $last_activity) < $inactive_threshold) {
                $_SESSION['error_type'] = 'already_logged_in';
                $_SESSION['error_message'] = 'This account is already logged in on another device. Please logout from other devices first.';
                header("Location: Student-Login.php");
                exit();
            } else {
                // If inactive for more than 30 minutes, force logout the previous session
                $update_query = "UPDATE student SET user_online = 0, session_id = NULL, last_activity = NULL WHERE lrn = ?";
                $stmt = $db->prepare($update_query);
                $stmt->bind_param("s", $lrn);
                $stmt->execute();
            }
        }
    }

    // Continue with regular login process
    $query = "SELECT * FROM student WHERE lrn = ? AND status = 'active'";
    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $lrn);
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

        // Verify password using MD5
        if ($password === $user['password']) {
            // Reset login attempts on successful login
            resetAttempts($db, $user['lrn']);
            
            // Set session variables
            $_SESSION['id'] = $user['student_id'];
            $_SESSION['firstname'] = $user['firstname'];
            $_SESSION['lastname'] = $user['lastname'];
            
            // Check if this is a temporary password login
            $check_temp_pwd = "SELECT password_recovery FROM student 
                             WHERE student_id = ? 
                             AND password_recovery = 'yes'";
            $stmt = $db->prepare($check_temp_pwd);
            $stmt->bind_param("i", $user['student_id']);
            $stmt->execute();
            $temp_pwd_result = $stmt->get_result();
            
            if ($temp_pwd_result && $temp_pwd_result->num_rows > 0) {
                // Set session for password change requirement
                $_SESSION['require_password_change'] = true;
                
                // Update user online status
                $session_id = session_id();
                $update_session = "UPDATE student 
                                 SET user_online = 1, 
                                     session_id = ?, 
                                     last_activity = NOW() 
                                 WHERE student_id = ?";
                $stmt = $db->prepare($update_session);
                $stmt->bind_param("si", $session_id, $user['student_id']);
                $stmt->execute();
                
                // Redirect to profile settings
                header("Location: student_profile.php?tab=settings");
                exit();
            }
            
            // Normal login process continues...
            $ip = $_SERVER['REMOTE_ADDR'];
            $query = "UPDATE student SET user_online = 1 WHERE student_id = ?";
            $stmt = $db->prepare($query);
            $stmt->bind_param("i", $user['student_id']);
            $stmt->execute();
            
            // Log successful login
            $query = "INSERT INTO student_login_logs (student_id, ip_address, status) 
                     VALUES (?, ?, 'success')";
            $stmt = $db->prepare($query);
            $stmt->bind_param("is", $user['student_id'], $ip);
            $stmt->execute();
            
            $session_id = session_id();
            $update_session = "UPDATE student 
                              SET user_online = 1, 
                                  session_id = ?, 
                                  last_activity = NOW() 
                              WHERE student_id = ?";
            $stmt = $db->prepare($update_session);
            $stmt->bind_param("si", $session_id, $user['student_id']);
            $stmt->execute();
            
            $_SESSION['just_logged_in'] = true;
            $_SESSION['welcome_name'] = $user['firstname'];
            header("Location: student_dashboard.php");
            exit();
        } else {
            // Wrong password
            $attempts = $user['login_attempts'] + 1;
            $max_attempts = 5;
            
            if ($attempts >= $max_attempts) {
                $lockout_until = date('Y-m-d H:i:s', strtotime('+15 minutes'));
                $query = "UPDATE student SET login_attempts = ?, lockout_until = ? 
                         WHERE lrn = ?";
                $stmt = $db->prepare($query);
                $stmt->bind_param("iss", $attempts, $lockout_until, $lrn);
                $_SESSION['error_type'] = 'max_attempts';
            } else {
                $query = "UPDATE student SET login_attempts = ? WHERE lrn = ?";
                $stmt = $db->prepare($query);
                $stmt->bind_param("is", $attempts, $lrn);
                $_SESSION['error_type'] = 'wrong_password';
            }
            $stmt->execute();
        }
    } else {
        $_SESSION['error_type'] = 'wrong_lrn';
    }
    
    header("Location: Student-Login.php");
    exit();
}

// Check Remember Me token
if (!isset($_SESSION['id']) && isset($_COOKIE['remember_token'])) {
    $db = new DbConnector();
    $token = $db->escapeString($_COOKIE['remember_token']);
    
    $query = "SELECT s.*, rt.token 
              FROM student s 
              JOIN remember_tokens rt ON s.student_id = rt.student_id 
              WHERE rt.token = ? 
              AND rt.expiry > NOW() 
              AND s.status = 'active'";
    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Update session
        $_SESSION['id'] = $user['student_id'];
        $_SESSION['firstname'] = $user['firstname'];
        $_SESSION['lastname'] = $user['lastname'];
        
        // Update last activity
        $update_activity = "UPDATE student 
                           SET last_activity = NOW(), 
                               user_online = 1,
                               session_id = ? 
                           WHERE student_id = ?";
        $stmt = $db->prepare($update_activity);
        $session_id = session_id();
        $stmt->bind_param("si", $session_id, $user['student_id']);
        $stmt->execute();
        
        header("Location: student_dashboard.php");
        exit();
    } else {
        // Invalid or expired token - clear it
        setcookie('remember_token', '', time() - 3600, '/');
    }
}

header("Location: Student-Login.php");
exit();
?>

