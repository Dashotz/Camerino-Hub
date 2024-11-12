<?php
function checkSession() {
    $inactive = 28800; // 8 hours in seconds
    
    if (isset($_SESSION['last_activity'])) {
        $session_life = time() - $_SESSION['last_activity'];
        
        if ($session_life > $inactive) {
            require_once('../db/dbConnector.php');
            $db = new DbConnector();
            
            // Update user_online status to 0
            if (isset($_SESSION['id'])) {
                $update_status = "UPDATE student SET user_online = 0 WHERE student_id = ?";
                $stmt = $db->prepare($update_status);
                $stmt->bind_param("i", $_SESSION['id']);
                $stmt->execute();
            }
            
            // Clear session
            session_unset();
            session_destroy();
            return false;
        }
    }
    
    $_SESSION['last_activity'] = time();
    return true;
}
