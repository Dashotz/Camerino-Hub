<?php
session_start();
require_once('../../db/dbConnector.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

$db = new DbConnector();
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'get_students':
        getStudents($db);
        break;
    case 'add_student':
        addStudent($db);
        break;
    case 'update_student':
        updateStudent($db);
        break;
    case 'delete_student':
        deleteStudent($db);
        break;
    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        break;
}

function getStudents($db) {
    try {
        $query = "SELECT * FROM student ORDER BY student_id DESC";
        $result = $db->query($query);
        $students = [];
        
        while ($row = $result->fetch_assoc()) {
            $students[] = [
                'student_id' => $row['student_id'],
                'name' => $row['firstname'] . ' ' . ($row['middle_name'] ? $row['middle_name'] . ' ' : '') . $row['lastname'],
                'username' => $row['username'],
                'location' => $row['location'],
                'cys' => $row['cys'], // class, year and section
                'status' => $row['status']
            ];
        }
        
        echo json_encode([
            'draw' => isset($_GET['draw']) ? intval($_GET['draw']) : 1,
            'recordsTotal' => count($students),
            'recordsFiltered' => count($students),
            'data' => $students
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'draw' => isset($_GET['draw']) ? intval($_GET['draw']) : 1,
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => [],
            'error' => $e->getMessage()
        ]);
    }
}

function addStudent($db) {
    $firstname = $_POST['firstName'] ?? '';
    $middlename = $_POST['middleName'] ?? '';
    $lastname = $_POST['lastName'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? ''; // You should hash this
    $cys = $_POST['cys'] ?? '';
    $location = $_POST['location'] ?? '';
    
    // Basic validation
    if (empty($firstname) || empty($lastname) || empty($username) || empty($password) || empty($cys)) {
        echo json_encode(['status' => 'error', 'message' => 'Required fields are missing']);
        return;
    }
    
    // Check if username already exists
    $query = "SELECT student_id FROM student WHERE username = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Username already exists']);
        return;
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new student
    $query = "INSERT INTO student (firstname, middle_name, lastname, username, password, cys, location, status) 
              VALUES (?, ?, ?, ?, ?, ?, ?, 'active')";
    $stmt = $db->prepare($query);
    $stmt->bind_param("sssssss", $firstname, $middlename, $lastname, $username, $hashed_password, $cys, $location);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Student added successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add student']);
    }
}

function updateStudent($db) {
    $student_id = $_POST['student_id'] ?? '';
    $firstname = $_POST['firstName'] ?? '';
    $lastname = $_POST['lastName'] ?? '';
    $email = $_POST['email'] ?? '';
    $course = $_POST['course'] ?? '';
    $status = $_POST['status'] ?? '';
    
    if (empty($student_id)) {
        echo json_encode(['status' => 'error', 'message' => 'Student ID is required']);
        return;
    }
    
    $query = "UPDATE student SET firstname = ?, lastname = ?, email = ?, course = ?, status = ? WHERE student_id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("sssssi", $firstname, $lastname, $email, $course, $status, $student_id);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Student updated successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update student']);
    }
}

function deleteStudent($db) {
    $student_id = $_POST['student_id'] ?? '';
    
    if (empty($student_id)) {
        echo json_encode(['status' => 'error', 'message' => 'Student ID is required']);
        return;
    }
    
    $query = "DELETE FROM student WHERE student_id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $student_id);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Student deleted successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete student']);
    }
}
