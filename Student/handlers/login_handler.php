<?php
session_start();
require_once('../../db/dbConnector.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new DbConnector();
    
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $query = "SELECT * FROM student WHERE username = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['id'] = $user['student_id']; // This is the key session variable
            $_SESSION['username'] = $user['username'];
            $_SESSION['name'] = $user['firstname'] . ' ' . $user['lastname'];
            $_SESSION['role'] = 'student';
            
            // Debug session after setting
            error_log('Login Session contents: ' . print_r($_SESSION, true));
            
            echo json_encode(['success' => true]);
            exit;
        }
    }
    
    echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
}
?> 