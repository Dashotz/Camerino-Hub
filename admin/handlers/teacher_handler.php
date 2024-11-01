<?php
session_start();
require_once('../../db/dbConnector.php');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

$db = new DbConnector();
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'get_teachers':
        getTeachers($db);
        break;
    case 'add_teacher':
        addTeacher($db);
        break;
    case 'update_teacher':
        updateTeacher($db);
        break;
    case 'delete_teacher':
        deleteTeacher($db);
        break;
    case 'get_teacher_students':
        getTeacherStudents($db);
        break;
    case 'get_teacher_subjects':
        getTeacherSubjects($db);
        break;
    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        break;
}

function getTeachers($db) {
    try {
        $query = "SELECT * FROM teacher ORDER BY teacher_id DESC";
        $result = $db->query($query);
        $teachers = [];
        
        while ($row = $result->fetch_assoc()) {
            $teachers[] = [
                'teacher_id' => $row['teacher_id'],
                'name' => $row['firstname'] . ' ' . ($row['middlename'] ? $row['middlename'] . ' ' : '') . $row['lastname'],
                'username' => $row['username'],
                'department' => $row['department'],
                'location' => $row['location']
            ];
        }
        
        echo json_encode([
            'draw' => isset($_GET['draw']) ? intval($_GET['draw']) : 1,
            'recordsTotal' => count($teachers),
            'recordsFiltered' => count($teachers),
            'data' => $teachers
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

function addTeacher($db) {
    $firstname = $_POST['firstName'] ?? '';
    $middlename = $_POST['middleName'] ?? '';
    $lastname = $_POST['lastName'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? ''; // Will be hashed
    $department = $_POST['department'] ?? '';
    $location = $_POST['location'] ?? '';
    
    // Basic validation
    if (empty($firstname) || empty($lastname) || empty($username) || empty($password) || empty($department)) {
        echo json_encode(['status' => 'error', 'message' => 'Required fields are missing']);
        return;
    }
    
    // Check if username already exists
    $query = "SELECT teacher_id FROM teacher WHERE username = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Username already exists']);
        return;
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new teacher
    $query = "INSERT INTO teacher (firstname, middlename, lastname, username, password, department, location) 
              VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $db->prepare($query);
    $stmt->bind_param("sssssss", $firstname, $middlename, $lastname, $username, $hashed_password, $department, $location);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Teacher added successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add teacher']);
    }
}

function getTeacherStudents($db) {
    $teacher_id = $_GET['teacher_id'] ?? 0;
    
    try {
        $query = "SELECT s.student_id, s.firstname, s.lastname, s.cys 
                 FROM student s 
                 JOIN teacher_student ts ON s.student_id = ts.student_id 
                 WHERE ts.teacher_id = ?";
        
        $stmt = $db->prepare($query);
        $stmt->bind_param("i", $teacher_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $students = [];
        while ($row = $result->fetch_assoc()) {
            $students[] = [
                'student_id' => $row['student_id'],
                'name' => $row['firstname'] . ' ' . $row['lastname'],
                'cys' => $row['cys']
            ];
        }
        
        echo json_encode([
            'status' => 'success',
            'students' => $students
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to fetch teacher\'s students'
        ]);
    }
}

function getTeacherSubjects($db) {
    $teacher_id = $_GET['teacher_id'] ?? 0;
    
    try {
        $query = "SELECT s.subject_id, s.subject_name, s.department 
                 FROM subject s 
                 JOIN teacher_subject ts ON s.subject_id = ts.subject_id 
                 WHERE ts.teacher_id = ?";
        
        $stmt = $db->prepare($query);
        $stmt->bind_param("i", $teacher_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $subjects = [];
        while ($row = $result->fetch_assoc()) {
            $subjects[] = [
                'subject_id' => $row['subject_id'],
                'subject_name' => $row['subject_name'],
                'department' => $row['department']
            ];
        }
        
        echo json_encode([
            'status' => 'success',
            'subjects' => $subjects
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to fetch teacher\'s subjects'
        ]);
    }
}
