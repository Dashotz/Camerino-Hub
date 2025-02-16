<?php
session_start();
require_once('../../db/dbConnector.php');

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

$db = new DbConnector();
$student_id = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'join_subject':
            $code = $_POST['code'] ?? '';
            
            if (empty($code)) {
                echo json_encode(['status' => 'error', 'message' => 'Enrollment code is required']);
                exit();
            }

            // Begin transaction
            $db->begin_transaction();

            try {
                // First, delete any existing enrollment for this academic year
                $delete_query = "DELETE FROM student_sections 
                                WHERE student_id = ? 
                                AND academic_year_id IN (
                                    SELECT academic_year_id 
                                    FROM section_subjects 
                                    WHERE enrollment_code = ?
                                )";
                $stmt = $db->prepare($delete_query);
                $stmt->bind_param("is", $student_id, $code);
                $stmt->execute();

                // Now get the section details
                $query = "SELECT ss.*, s.section_id, ay.id as academic_year_id 
                         FROM section_subjects ss
                         JOIN sections s ON ss.section_id = s.section_id
                         JOIN academic_years ay ON ss.academic_year_id = ay.id
                         WHERE ss.enrollment_code = ? 
                         AND ss.status = 'active'
                         AND ay.status = 'active'";
                
                $stmt = $db->prepare($query);
                $stmt->bind_param("s", $code);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows === 0) {
                    throw new Exception('Invalid enrollment code or inactive section');
                }

                $section_data = $result->fetch_assoc();

                // Insert new enrollment
                $insert_query = "INSERT INTO student_sections 
                               (student_id, section_id, academic_year_id, status, enrolled_at) 
                               VALUES (?, ?, ?, 'active', NOW())";
                
                $stmt = $db->prepare($insert_query);
                $stmt->bind_param("iii", 
                    $student_id, 
                    $section_data['section_id'], 
                    $section_data['academic_year_id']
                );
                
                if (!$stmt->execute()) {
                    throw new Exception($stmt->error);
                }

                $db->commit();
                echo json_encode(['status' => 'success']);
            } catch (Exception $e) {
                $db->rollback();
                error_log("Join section error: " . $e->getMessage());
                echo json_encode([
                    'status' => 'error', 
                    'message' => 'Failed to join section. Error: ' . $e->getMessage()
                ]);
            }
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
            break;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
