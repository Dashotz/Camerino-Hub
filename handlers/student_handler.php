<?php
require_once('../db/dbConnector.php');
require_once('../models/Student.php');

try {
    $db = new DbConnector();
    $studentModel = new Student($db);
    
    $action = $_REQUEST['action'] ?? '';
    $response = ['status' => 'error', 'message' => 'Invalid action'];
    
    switch ($action) {
        case 'get_students':
            $result = $studentModel->getAllStudents();
            $students = [];
            while ($row = $result->fetch_assoc()) {
                $students[] = $row;
            }
            $response = ['status' => 'success', 'data' => $students];
            break;
            
        case 'get_student_details':
            $studentId = $_GET['student_id'] ?? 0;
            $result = $studentModel->getStudentById($studentId);
            $student = $result->fetch_assoc();
            
            if ($student) {
                $response = ['status' => 'success', 'data' => $student];
            } else {
                $response = ['status' => 'error', 'message' => 'Student not found'];
            }
            break;
            
        case 'add_student':
            $result = $studentModel->addStudent($_POST);
            $response = ['status' => 'success', 'message' => 'Student added successfully'];
            break;
            
        case 'edit_student':
            $studentId = $_POST['student_id'] ?? 0;
            $result = $studentModel->updateStudent($studentId, $_POST);
            $response = ['status' => 'success', 'message' => 'Student updated successfully'];
            break;
            
        case 'archive_student':
            $studentId = $_POST['student_id'] ?? 0;
            $result = $studentModel->archiveStudent($studentId);
            $response = ['status' => 'success', 'message' => 'Student archived successfully'];
            break;
    }
    
    echo json_encode($response);
    
} catch (Exception $e) {
    error_log("Student Handler Error: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred while processing your request'
    ]);
}