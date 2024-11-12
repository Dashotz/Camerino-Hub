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
    $query = "SELECT * FROM student WHERE student_id = '$student_id'";
    $result = $db->query($query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $userData = mysqli_fetch_array($result);
    }
}

// Only redirect if trying to access protected pages
if (isset($requireLogin) && $requireLogin && !$isLoggedIn) {
    header("Location: Student-Login.php");
    exit();
}
?>
