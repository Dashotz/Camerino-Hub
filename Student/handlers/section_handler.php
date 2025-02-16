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
        case 'unenroll_section':
            // Begin transaction
            $db->begin_transaction();

            try {
                // Get current active section and academic year
                $query = "SELECT ss.section_id, ss.academic_year_id 
                         FROM student_sections ss
                         JOIN academic_years ay ON ss.academic_year_id = ay.id
                         WHERE ss.student_id = ? 
                         AND ss.status = 'active'
                         AND ay.status = 'active'";
                $stmt = $db->prepare($query);
                $stmt->bind_param("i", $student_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows === 0) {
                    throw new Exception('No active section found');
                }

                $section = $result->fetch_assoc();

                // First, delete any existing student_sections record for this academic year
                $delete_query = "DELETE FROM student_sections 
                               WHERE student_id = ? 
                               AND academic_year_id = ?";
                
                $stmt = $db->prepare($delete_query);
                $stmt->bind_param("ii", $student_id, $section['academic_year_id']);
                
                if (!$stmt->execute()) {
                    throw new Exception('Failed to remove existing enrollment');
                }

                $db->commit();
                echo json_encode(['status' => 'success']);
            } catch (Exception $e) {
                $db->rollback();
                error_log("Unenroll error: " . $e->getMessage());
                echo json_encode(['status' => 'error', 'message' => 'Failed to unenroll from section. ' . $e->getMessage()]);
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