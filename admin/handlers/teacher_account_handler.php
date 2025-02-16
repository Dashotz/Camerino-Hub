<?php
session_start();
require_once('../../db/dbConnector.php');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

$db = new DbConnector();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $action = $_POST['action'] ?? '';

        switch ($action) {
            case 'create_teacher':
                createTeacher($db);
                break;
                
            default:
                throw new Exception('Invalid action');
        }
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}

function createTeacher($db) {
    try {
        // Start transaction
        $db->begin_transaction();

        // Validate required fields
        $required_fields = ['username', 'email', 'firstname', 'lastname', 'department', 'password', 'confirm_password'];
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("$field is required");
            }
        }

        // Validate password
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        if (strlen($password) < 8) {
            throw new Exception('Password must be at least 8 characters long');
        }

        if ($password !== $confirm_password) {
            throw new Exception('Passwords do not match');
        }

        // Validate email format
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email format');
        }

        // Check if username already exists
        $username = $db->escapeString($_POST['username']);
        $check_username = "SELECT teacher_id FROM teacher WHERE username = '$username'";
        $result = $db->query($check_username);
        if ($result && $result->num_rows > 0) {
            throw new Exception('Username already exists');
        }

        // Check if email already exists
        $email = $db->escapeString($_POST['email']);
        $check_email = "SELECT teacher_id FROM teacher WHERE email = '$email'";
        $result = $db->query($check_email);
        if ($result && $result->num_rows > 0) {
            throw new Exception('Email already exists');
        }

        // Hash password using MD5
        $hashed_password = md5($password); // Changed from password_hash to md5

        // Insert query
        $query = "INSERT INTO teacher (
            username,
            password,
            email,
            firstname,
            lastname,
            middlename,
            department_id,
            status
        ) VALUES (
            '" . $db->escapeString($_POST['username']) . "',
            '" . $db->escapeString($hashed_password) . "',
            '" . $db->escapeString($_POST['email']) . "',
            '" . $db->escapeString($_POST['firstname']) . "',
            '" . $db->escapeString($_POST['lastname']) . "',
            '" . $db->escapeString($_POST['middlename'] ?? '') . "',
            '" . $db->escapeString($_POST['department']) . "',
            'active'
        )";

        if (!$db->query($query)) {
            throw new Exception("Database Error: " . mysqli_error($db->getConnection()));
        }

        $db->commit();

        echo json_encode([
            'status' => 'success',
            'message' => 'Teacher account created successfully'
        ]);

    } catch (Exception $e) {
        $db->rollback();
        error_log("Teacher Creation Error: " . $e->getMessage());
        error_log("Last Query: " . $db->theQuery);
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}
?>