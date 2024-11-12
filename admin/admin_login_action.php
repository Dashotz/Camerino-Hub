<?php
session_start();
require_once('../db/dbConnector.php');

if (isset($_POST['login'])) {
    $db = new DbConnector();
    
    $username = $db->escapeString($_POST['username']);
    $password = md5($db->escapeString($_POST['password'])); // Add MD5 hashing
    
    // Check if admin exists with hashed password
    $query = "SELECT * FROM admin WHERE username = ? AND password = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        
        // Login successful
        $_SESSION['admin_id'] = $admin['admin_id'];
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['admin_last_activity'] = time();
        
        // Log successful login
        $ip = $_SERVER['REMOTE_ADDR'];
        $log_query = "INSERT INTO admin_login_logs (admin_id, ip_address, status) 
                      VALUES (?, ?, 'success')";
        $stmt = $db->prepare($log_query);
        $stmt->bind_param("is", $admin['admin_id'], $ip);
        $stmt->execute();
        
        header("Location: admin_dashboard.php");
        exit();
    } else {
        // Log failed login attempt
        $ip = $_SERVER['REMOTE_ADDR'];
        $log_query = "INSERT INTO admin_login_logs (admin_id, ip_address, status) 
                      VALUES (1, ?, 'failed')"; // Using admin_id 1 for failed attempts
        $stmt = $db->prepare($log_query);
        $stmt->bind_param("s", $ip);
        $stmt->execute();

        $_SESSION['error_type'] = 'invalid_credentials';
        $_SESSION['error_message'] = 'Invalid username or password';
    }
    
    header("Location: login.php");
    exit();
}
?>
