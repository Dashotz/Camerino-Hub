<?php
session_start();

// Set session timeout duration (in seconds)
$timeout_duration = 1800; // 30 minutes

// Check if last activity time is set
if (isset($_SESSION['last_activity'])) {
    // Calculate time difference
    $inactive_time = time() - $_SESSION['last_activity'];
    
    // Check if inactive time exceeds timeout duration
    if ($inactive_time >= $timeout_duration) {
        // Clear all session variables
        $_SESSION = array();
        
        // Destroy the session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time()-3600, '/');
        }
        
        // Destroy the session
        session_destroy();
        
        // Return JSON response indicating timeout
        echo json_encode(['status' => 'timeout']);
        exit();
    }
}

// Update last activity time
$_SESSION['last_activity'] = time();

// Return JSON response indicating active session
echo json_encode(['status' => 'active']);
?>
