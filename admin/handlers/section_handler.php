<?php
session_start();
require_once('../../db/dbConnector.php');

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

$db = new DbConnector();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $action = $_POST['action'] ?? '';

        switch ($action) {
            case 'add_section':
                // Validate required fields
                if (empty($_POST['section_name']) || empty($_POST['grade_level'])) {
                    throw new Exception('Section name and grade level are required');
                }

                // Get current academic year
                $academic_year_query = "SELECT id, school_year FROM academic_years WHERE status = 'active' LIMIT 1";
                $academic_year_result = $db->query($academic_year_query);
                $academic_year = $academic_year_result->fetch_assoc();
                
                if (!$academic_year) {
                    throw new Exception('No active academic year found');
                }

                // Sanitize inputs
                $section_name = $db->real_escape_string($_POST['section_name']);
                $grade_level = $db->real_escape_string($_POST['grade_level']);
                $status = $db->real_escape_string($_POST['status'] ?? 'active');
                $school_year = $academic_year['school_year'];

                // Check if section name already exists in the same grade level
                $check_query = "SELECT section_id FROM sections 
                               WHERE section_name = '$section_name' 
                               AND grade_level = '$grade_level' 
                               AND school_year = '$school_year'";
                $check_result = $db->query($check_query);

                if ($check_result->num_rows > 0) {
                    throw new Exception('Section name already exists in this grade level');
                }

                // Insert new section
                $insert_query = "INSERT INTO sections (section_name, grade_level, school_year, status) 
                                VALUES ('$section_name', '$grade_level', '$school_year', '$status')";
                
                if (!$db->query($insert_query)) {
                    throw new Exception("Failed to add section: " . mysqli_error($db->getConnection()));
                }

                echo json_encode([
                    'status' => 'success',
                    'message' => 'Section added successfully'
                ]);
                break;

            case 'edit_section':
                if (empty($_POST['section_id']) || empty($_POST['section_name']) || empty($_POST['grade_level'])) {
                    throw new Exception('Required fields are missing');
                }

                $section_id = intval($_POST['section_id']);
                $section_name = $db->real_escape_string($_POST['section_name']);
                $grade_level = $db->real_escape_string($_POST['grade_level']);
                $status = $db->real_escape_string($_POST['status'] ?? 'active');

                // Check if section name exists (excluding current section)
                $check_query = "SELECT section_id FROM sections 
                              WHERE section_name = '$section_name' 
                              AND grade_level = '$grade_level' 
                              AND section_id != $section_id";
                $check_result = $db->query($check_query);

                if ($check_result->num_rows > 0) {
                    throw new Exception('Section name already exists in this grade level');
                }

                // Update section
                $update_query = "UPDATE sections 
                               SET section_name = '$section_name',
                                   grade_level = '$grade_level',
                                   status = '$status'
                               WHERE section_id = $section_id";
                
                if (!$db->query($update_query)) {
                    throw new Exception("Failed to update section");
                }

                echo json_encode([
                    'status' => 'success',
                    'message' => 'Section updated successfully'
                ]);
                break;

            case 'delete_section':
                if (empty($_POST['section_id'])) {
                    throw new Exception('Section ID is required');
                }

                $section_id = intval($_POST['section_id']);

                // Check if section has active students
                $check_students = "SELECT COUNT(*) as count 
                                 FROM student_sections 
                                 WHERE section_id = $section_id 
                                 AND status = 'active'";
                $result = $db->query($check_students);
                $student_count = $result->fetch_assoc()['count'];

                if ($student_count > 0) {
                    throw new Exception('Cannot delete section with active students');
                }

                // Delete section
                $delete_query = "DELETE FROM sections WHERE section_id = $section_id";
                
                if (!$db->query($delete_query)) {
                    throw new Exception("Failed to delete section");
                }

                echo json_encode([
                    'status' => 'success',
                    'message' => 'Section deleted successfully'
                ]);
                break;

            case 'assign_adviser':
                try {
                    $section_id = $_POST['section_id'] ?? '';
                    $adviser_id = $_POST['adviser_id'] ?? '';

                    if (empty($section_id) || empty($adviser_id)) {
                        throw new Exception('Missing required fields');
                    }

                    $query = "UPDATE sections SET adviser_id = ? WHERE section_id = ?";
                    $stmt = $db->prepare($query);
                    $stmt->bind_param("ii", $adviser_id, $section_id);

                    if ($stmt->execute()) {
                        echo json_encode(['status' => 'success']);
                    } else {
                        throw new Exception('Failed to assign adviser');
                    }
                } catch (Exception $e) {
                    echo json_encode([
                        'status' => 'error',
                        'message' => $e->getMessage()
                    ]);
                }
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
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method'
    ]);
}
?>
