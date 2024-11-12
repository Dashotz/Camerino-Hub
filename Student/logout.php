<?php
session_start();
require_once('../db/dbConnector.php');

if (isset($_SESSION['id'])) {
    $db = new DbConnector();
    
    // Update user_online status to 0
    $update_offline_status = "UPDATE student SET user_online = 0 WHERE student_id = ?";
    $stmt = $db->prepare($update_offline_status);
    $stmt->bind_param("i", $_SESSION['id']);
    $stmt->execute();
    
    // Remove remember me token if exists
    if (isset($_COOKIE['remember_token'])) {
        $token = $db->escapeString($_COOKIE['remember_token']);
        $query = "DELETE FROM remember_tokens WHERE student_id = {$_SESSION['id']} AND token = '$token'";
        $db->query($query);
        setcookie('remember_token', '', time() - 3600, '/'); // Delete cookie
    }
    
    // Clear all session variables
    $_SESSION = array();
    
    // Destroy the session
    session_destroy();
}

header("Location: Student-Login.php");
exit();
?>
