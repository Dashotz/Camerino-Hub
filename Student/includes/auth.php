<?php
session_start();

// Initialize login status
$isLoggedIn = isset($_SESSION['id']);

// Get user data if logged in
$userData = null;
if ($isLoggedIn) {
    require_once('../db/dbConnector.php');
    $db = new DbConnector();
    
    $student_id = $_SESSION['id'];
    $query = "SELECT * FROM student WHERE student_id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $userData = $result->fetch_assoc();
    }
}

// Only redirect if trying to access protected pages
if (isset($requireLogin) && $requireLogin && !$isLoggedIn) {
    header("Location: ../login.php");
    exit();
}

// Add this after session check
if (isset($_SESSION['require_password_change']) && 
    !in_array(basename($_SERVER['PHP_SELF']), ['student_profile.php', 'change_password.php', 'logout.php'])) {
    header("Location: student_profile.php?tab=security&force_change=1");
    exit();
}
?>
