<?php
session_start();

// Store logout time in database if needed
if (isset($_SESSION['id'])) {
    require_once('../db/dbConnector.php');
    $db = new DbConnector();
    
    $user_id = $_SESSION['id'];
    $ip = $_SERVER['REMOTE_ADDR'];
    
    // Log the logout using your existing table structure
    $query = "INSERT INTO login_logs (student_id, ip_address, status) 
              VALUES (?, ?, 'logout')";
    $stmt = $db->prepare($query);
    $stmt->bind_param("is", $user_id, $ip);
    $stmt->execute();
    
    // Clear remember me token if exists
    if (isset($_COOKIE['remember_token'])) {
        $token = $db->escapeString($_COOKIE['remember_token']);
        $query = "DELETE FROM remember_tokens WHERE token = '$token'";
        $db->query($query);
        setcookie('remember_token', '', time() - 3600, '/');
    }
}

// Clear all session variables
$_SESSION = array();

// Destroy the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// Destroy the session
session_destroy();

// Show SweetAlert and redirect
?>
<!DOCTYPE html>
<html>
<head>
    <title>Logging Out...</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <script>
        Swal.fire({
            title: 'Goodbye!',
            text: 'You have been successfully logged out.',
            icon: 'success',
            timer: 2000,
            timerProgressBar: true,
            showConfirmButton: false
        }).then(() => {
            window.location.href = 'Student-Login.php';
        });
    </script>
</body>
</html>
