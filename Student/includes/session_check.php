<?php
function checkSession() {
    if (isset($_SESSION['id'])) {
        // Update last activity time
        $currentTime = time();
        
        // Only update if more than 5 minutes have passed since last update
        if (!isset($_SESSION['last_activity']) || ($currentTime - $_SESSION['last_activity']) > 300) {
            require_once('../db/dbConnector.php');
            $db = new DbConnector();
            
            $student_id = $_SESSION['id'];
            $currentTimestamp = date('YmdHis');
            
            // Update last_activity in database
            $query = "UPDATE student SET 
                     last_activity = ?, 
                     session_id = ? 
                     WHERE student_id = ?";
            
            $stmt = $db->prepare($query);
            $stmt->bind_param('ssi', $currentTimestamp, session_id(), $student_id);
            $stmt->execute();
            
            $_SESSION['last_activity'] = $currentTime;
        }
        
        return true;
    }
    return false;
}

// Set session timeout to 2 hours
ini_set('session.gc_maxlifetime', 7200);
session_set_cookie_params(7200);
