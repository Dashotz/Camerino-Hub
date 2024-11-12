<?php
session_start();
require_once('../../db/dbConnector.php');

if (!isset($_SESSION['teacher_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

$db = new DbConnector();
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'regenerate_code':
        try {
            $class_id = $_POST['class_id'];
            
            // Verify teacher owns this class
            $check = $db->prepare("SELECT id FROM section_subjects WHERE id = ? AND teacher_id = ?");
            $check->bind_param("ii", $class_id, $_SESSION['teacher_id']);
            $check->execute();
            if ($check->get_result()->num_rows === 0) {
                throw new Exception("Unauthorized access to this class");
            }
            
            // Generate new code
            do {
                $code = 'CMRH' . str_pad(mt_rand(1000, 9999), 4, '0', STR_PAD_LEFT);
                $check = $db->prepare("SELECT id FROM section_subjects WHERE enrollment_code = ?");
                $check->bind_param("s", $code);
                $check->execute();
            } while ($check->get_result()->num_rows > 0);
            
            // Update the code
            $update = $db->prepare("UPDATE section_subjects SET enrollment_code = ? WHERE id = ?");
            $update->bind_param("si", $code, $class_id);
            $update->execute();
            
            echo json_encode(['status' => 'success', 'code' => $code]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        break;
}
?>
