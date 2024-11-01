<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$db = new DbConnector();

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['action'])) {
    switch ($data['action']) {
        case 'toggle_archive':
            $student_id = $db->escapeString($data['student_id']);
            $new_status = $data['current_status'] === 'active' ? 'archived' : 'active';
            
            $query = "UPDATE student SET status = ? WHERE student_id = ?";
            $stmt = $db->prepare($query);
            $stmt->bind_param("si", $new_status, $student_id);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update status']);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No action specified']);
}
?>
