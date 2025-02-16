<?php
session_start();
require_once('../db/dbConnector.php');

if (isset($_SESSION['id'])) {
    $db = new DbConnector();
    $student_id = $_SESSION['id'];
    
    // Update student's online status
    $update_query = "UPDATE student SET 
        user_online = 0, 
        session_id = NULL, 
        last_activity = NULL 
        WHERE student_id = ?";
    $stmt = $db->prepare($update_query);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
}

// Clear all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: ../login.php");
exit();
?>
