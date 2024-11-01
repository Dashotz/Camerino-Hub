<?php
session_start();
require_once('../db/dbConnector.php');

// Check if admin is logged in
if (isset($_SESSION['admin_id'])) {
    $db = new DbConnector();
    $admin_id = $_SESSION['admin_id'];
    $ip = $_SERVER['REMOTE_ADDR'];
    
    // Log the logout
    $query = "INSERT INTO admin_login_logs (admin_id, ip_address, status) VALUES (?, ?, 'logout')";
    $stmt = $db->prepare($query);
    $stmt->bind_param("is", $admin_id, $ip);
    $stmt->execute();
    
    // Clear all session variables
    $_SESSION = array();
    
    // Destroy the session cookie
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time()-3600, '/');
    }
    
    // Destroy the session
    session_destroy();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logging Out - Gov D.M. Camerino</title>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
    </style>
</head>
<body>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: 'Goodbye!',
            text: 'You have been successfully logged out.',
            icon: 'success',
            timer: 2000,
            timerProgressBar: true,
            showConfirmButton: false,
            willClose: () => {
                window.location.href = 'login.php';
            }
        });
    });
    </script>
</body>
</html> 