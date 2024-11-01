<?php
session_start();
require_once('../../db/dbConnector.php');

if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

$db = new DbConnector();
$action = $_POST['action'] ?? $_GET['action'] ?? '';

if (empty($action)) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'No action specified']);
    exit;
}

switch ($action) {
    case 'get_subjects':
        getSubjects($db);
        break;
    case 'add_subject':
        addSubject($db);
        break;
    default:
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        exit;
}

function getSubjects($db) {
    try {
        $query = "SELECT s.*, 
                  (SELECT COUNT(*) FROM teacher_subject WHERE subject_id = s.subject_id) as teacher_count 
                  FROM subject s";
        $result = $db->query($query);
        
        if (!$result) {
            throw new Exception($db->error);
        }

        $subjects = [];
        while ($row = $result->fetch_assoc()) {
            $subjects[] = [
                'id' => $row['subject_id'],
                'subject_code' => $row['subject_code'],
                'subject_title' => $row['subject_title'],
                'category' => $row['category'],
                'teacher_count' => (int)$row['teacher_count']
            ];
        }
        
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'data' => $subjects
        ]);
        exit;
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to fetch subjects',
            'error' => $e->getMessage()
        ]);
        exit;
    }
}

function addSubject($db) {
    try {
        if (!isset($_POST['subject_code']) || !isset($_POST['subject_title']) || !isset($_POST['category'])) {
            throw new Exception('Missing required fields');
        }

        $subject_code = $_POST['subject_code'];
        $subject_title = $_POST['subject_title'];
        $category = $_POST['category'];

        $query = "INSERT INTO subject (subject_code, subject_title, category) VALUES (?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->bind_param("sss", $subject_code, $subject_title, $category);
        
        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }

        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'message' => 'Subject added successfully'
        ]);
        exit;
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
        exit;
    }
}
?>
