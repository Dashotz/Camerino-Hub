<?php
session_start();
require_once('../../db/dbConnector.php');

if (!isset($_SESSION['teacher_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

header('Content-Type: application/json');

try {
    if (!isset($_POST['student_id'])) {
        throw new Exception('Student ID is required');
    }

    $db = new DbConnector();
    
    // Validate required fields
    $required_fields = ['firstname', 'lastname', 'email'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            throw new Exception("$field is required");
        }
    }

    // Update student information
    $query = "UPDATE student SET 
              firstname = ?,
              lastname = ?,
              email = ?,
              contact_number = ?,
              gender = ?
              WHERE student_id = ?";

    $stmt = $db->prepare($query);
    $stmt->bind_param("sssssi", 
        $_POST['firstname'],
        $_POST['lastname'],
        $_POST['email'],
        $_POST['contact_number'],
        $_POST['gender'],
        $_POST['student_id']
    );

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Student information updated successfully'
        ]);
    } else {
        throw new Exception($stmt->error);
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 