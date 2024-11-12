<?php
session_start();
require_once('../db/dbConnector.php');

if (isset($_GET['username']) && isset($_GET['password'])) {
    $db = new DbConnector();
    
    $username = $db->escapeString($_GET['username']);
    $password = $db->escapeString($_GET['password']);
    $remember = isset($_GET['remember']) ? $_GET['remember'] === '1' : false;
    
    // Force logout from all devices
    $query = "UPDATE student SET user_online = 0 WHERE username = '$username'";
    $db->query($query);
    
    // Redirect back to login action with same credentials
    $_POST['username'] = $username;
    $_POST['password'] = $password;
    $_POST['remember'] = $remember;
    $_POST['login'] = true;
    
    include 'student_login_action.php';
} else {
    header("Location: Student-Login.php");
    exit();
}
?>
