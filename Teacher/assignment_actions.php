<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['teacher_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit();
}

$db = new DbConnector();
$teacher_id = $_SESSION['teacher_id'];

$action = $_POST['action'] ?? '';

switch($action) {
    case 'add':
        $title = $_POST['title'];
        $description = $_POST['description'];
        $class_id = $_POST['class_id'];
        $due_date = $_POST['due_date'];
        
        $query = "INSERT INTO assignments (teacher_id, class_id, title, description, due_date) 
                 VALUES (?, ?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->bind_param("iisss", $teacher_id, $class_id, $title, $description, $due_date);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Assignment created successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error creating assignment']);
        }
        break;
        
    case 'delete':
        $assignment_id = $_POST['assignment_id'];
        
        $query = "DELETE FROM assignments WHERE assignment_id = ? AND teacher_id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("ii", $assignment_id, $teacher_id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Assignment deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error deleting assignment']);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>
