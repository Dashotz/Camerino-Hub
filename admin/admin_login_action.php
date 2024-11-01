<?php
session_start();
require_once('../db/dbConnector.php');

if (isset($_POST['login'])) {
    $db = new DbConnector();
    
    $username = $db->escapeString($_POST['username']);
    $password = $db->escapeString($_POST['password']);
    
    // Check if admin exists
    $query = "SELECT * FROM admin WHERE username = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        
        // Verify password
        if ($admin['password'] === $password) {
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
            $_SESSION['error_type'] = 'wrong_password';
        }
    } else {
        $_SESSION['error_type'] = 'wrong_username';
    }
    
    header("Location: login.php");
    exit();
}
?>
