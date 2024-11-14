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

switch ($action) {
    case 'get_subjects':
        getSubjects($db);
        break;
    case 'edit_subject':
        editSubject($db);
        break;
    case 'delete_subject':
        deleteSubject($db);
        break;
    case 'get_subject_teachers':
        getSubjectTeachers($db);
        break;
    case 'get_subject_details':
        getSubjectDetails($db);
        break;
    case 'archive_subject':
        archiveSubject($db);
        break;
    case 'get_subject_stats':
        getSubjectStats($db);
        break;
    case 'get_subject_full_details':
        getSubjectFullDetails($db);
        break;
    case 'get_adjacent_subjects':
        getAdjacentSubjects($db);
        break;
    case 'restore_subject':
        restoreSubject($db);
        break;
    default:
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        exit;
}

function getSubjects($db) {
    try {
        $status = $_GET['status'] ?? 'active';
        
        // Get current academic year
        $academic_year_query = "SELECT id FROM academic_years WHERE status = 'active' LIMIT 1";
        $academic_year_result = $db->query($academic_year_query);
        $current_academic_year = $academic_year_result->fetch_assoc()['id'];

        $query = "SELECT s.*, 
            COALESCE((
                SELECT COUNT(DISTINCT ss.teacher_id) 
                FROM section_subjects ss 
                WHERE ss.subject_id = s.id 
                AND ss.status = 'active'
                AND ss.academic_year_id = ?
            ), 0) as assigned_teachers
            FROM subjects s 
            WHERE s.status = ?
            ORDER BY s.subject_code ASC";
        
        $stmt = $db->prepare($query);
        $stmt->bind_param("is", $current_academic_year, $status);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $subjects = [];
        while ($row = $result->fetch_assoc()) {
            $subjects[] = $row;
        }
        
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'data' => $subjects,
            'recordsTotal' => count($subjects),
            'recordsFiltered' => count($subjects)
        ]);
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}

function getSubjectTeachers($db) {
    try {
        $id = $_GET['id'];
        $query = "SELECT 
            ss.*,
            CONCAT(t.firstname, ' ', t.lastname) as teacher_name,
            CONCAT(s.grade_level, ' - ', s.section_name) as section_name
        FROM section_subjects ss
        JOIN teacher t ON ss.teacher_id = t.teacher_id
        JOIN sections s ON ss.section_id = s.section_id
        WHERE ss.subject_id = ?
        ORDER BY ss.status DESC, s.grade_level ASC, s.section_name ASC";
        
        $stmt = $db->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $assignments = [];
        while ($row = $result->fetch_assoc()) {
            $assignments[] = $row;
        }
        
        echo json_encode([
            'status' => 'success',
            'data' => $assignments
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}

function getSubjectDetails($db) {
    try {
        if (!isset($_GET['id'])) {
            throw new Exception('Subject ID is required');
        }

        $id = $_GET['id'];
        
        // Get current academic year
        $academic_year_query = "SELECT id FROM academic_years WHERE status = 'active' LIMIT 1";
        $academic_year_result = $db->query($academic_year_query);
        $current_academic_year = $academic_year_result->fetch_assoc()['id'];

        // Get subject details with teacher count
        $query = "SELECT s.*, 
            (SELECT COUNT(DISTINCT ss.teacher_id) 
             FROM section_subjects ss 
             WHERE ss.subject_id = s.id 
             AND ss.status = 'active'
             AND ss.academic_year_id = ?) as teacher_count
            FROM subjects s
            WHERE s.id = ?";
        
        $stmt = $db->prepare($query);
        $stmt->bind_param("ii", $current_academic_year, $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($subject = $result->fetch_assoc()) {
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'success',
                'data' => $subject
            ]);
        } else {
            throw new Exception('Subject not found');
        }
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}

function editSubject($db) {
    try {
        if (!isset($_POST['subject_id'], $_POST['subject_code'], $_POST['subject_title'], $_POST['category'])) {
            throw new Exception('Missing required fields');
        }

        $id = $_POST['subject_id'];
        $subject_code = $_POST['subject_code'];
        $subject_title = $_POST['subject_title'];
        $category = $_POST['category'];
        $description = $_POST['description'] ?? '';  // Store in variable first
        
        // Check if subject code exists for other subjects
        $check_query = "SELECT id FROM subjects WHERE subject_code = ? AND id != ? AND status = 'active'";
        $check_stmt = $db->prepare($check_query);
        $check_stmt->bind_param("si", $subject_code, $id);
        $check_stmt->execute();
        if ($check_stmt->get_result()->num_rows > 0) {
            $_SESSION['error_message'] = 'Subject code already exists';
            header('Location: ../manage_subjects.php');
            exit();
        }

        // Update subject
        $query = "UPDATE subjects SET 
            subject_code = ?,
            subject_title = ?,
            subject_name = ?,
            category = ?,
            description = ?
            WHERE id = ?";
        
        $stmt = $db->prepare($query);
        $stmt->bind_param("sssssi", 
            $subject_code,
            $subject_title,
            $subject_title, // subject_name same as title
            $category,
            $description,
            $id
        );
        
        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }

        $_SESSION['success_message'] = 'Subject updated successfully';
        header('Location: ../manage_subjects.php');
        exit();
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
        header('Location: ../manage_subjects.php');
        exit();
    }
}

function archiveSubject($db) {
    try {
        if (!isset($_POST['id'])) {
            throw new Exception('Subject ID is required');
        }

        $id = $_POST['id'];
        
        // First deactivate all section_subjects assignments
        $deactivate_query = "UPDATE section_subjects SET status = 'inactive' 
                           WHERE subject_id = ? AND status = 'active'";
        $deactivate_stmt = $db->prepare($deactivate_query);
        $deactivate_stmt->bind_param("i", $id);
        $deactivate_stmt->execute();

        // Then archive the subject
        $archive_query = "UPDATE subjects SET status = 'inactive' WHERE id = ?";
        $archive_stmt = $db->prepare($archive_query);
        $archive_stmt->bind_param("i", $id);
        
        if (!$archive_stmt->execute()) {
            throw new Exception($archive_stmt->error);
        }

        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'message' => 'Subject archived successfully'
        ]);
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}

function getSubjectStats($db) {
    try {
        // Get current academic year
        $academic_year_query = "SELECT id FROM academic_years WHERE status = 'active' LIMIT 1";
        $academic_year_result = $db->query($academic_year_query);
        $current_academic_year = $academic_year_result->fetch_assoc()['id'];

        // Updated stats query to correctly count assigned teachers
        $stats_query = "SELECT 
            (SELECT COUNT(*) FROM subjects) as total_subjects,
            
            (SELECT COUNT(*) FROM subjects WHERE status = 'active') as active_subjects,
            
            (SELECT COUNT(*) FROM (
                SELECT DISTINCT teacher_id 
                FROM section_subjects 
                WHERE status = 'active' 
                AND academic_year_id = ?
            ) as unique_teachers) as assigned_teachers,
            
            (SELECT COUNT(*) FROM subjects WHERE status = 'inactive') as archived_subjects";
        
        $stmt = $db->prepare($stats_query);
        $stmt->bind_param("i", $current_academic_year);
        $stmt->execute();
        $result = $stmt->get_result();
        $stats = $result->fetch_assoc();

        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'data' => $stats
        ]);
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}

function getSubjectFullDetails($db) {
    try {
        $id = $_GET['id'];
        
        // Get current academic year
        $academic_year_query = "SELECT id FROM academic_years WHERE status = 'active' LIMIT 1";
        $academic_year_result = $db->query($academic_year_query);
        $current_academic_year = $academic_year_result->fetch_assoc()['id'];

        // Get subject details with teacher assignments
        $query = "SELECT s.*, 
            (SELECT COUNT(DISTINCT ss.teacher_id) 
             FROM section_subjects ss 
             WHERE ss.subject_id = s.id 
             AND ss.status = 'active'
             AND ss.academic_year_id = ?) as teacher_count,
            (SELECT GROUP_CONCAT(DISTINCT 
                CONCAT(t.firstname, ' ', t.lastname, ' (', sec.grade_level, '-', sec.section_name, ')')
                SEPARATOR ', ')
             FROM section_subjects ss
             JOIN teacher t ON ss.teacher_id = t.teacher_id
             JOIN sections sec ON ss.section_id = sec.section_id
             WHERE ss.subject_id = s.id 
             AND ss.status = 'active'
             AND ss.academic_year_id = ?) as assigned_teachers
        FROM subjects s
        WHERE s.id = ?";
        
        $stmt = $db->prepare($query);
        $stmt->bind_param("iii", $current_academic_year, $current_academic_year, $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($subject = $result->fetch_assoc()) {
            echo json_encode([
                'status' => 'success',
                'data' => $subject
            ]);
        } else {
            throw new Exception('Subject not found');
        }
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}

function getAdjacentSubjects($db) {
    try {
        $current_id = $_GET['id'];
        
        // Get the current subject's code for ordering
        $current_query = "SELECT subject_code FROM subjects WHERE id = ?";
        $stmt = $db->prepare($current_query);
        $stmt->bind_param("i", $current_id);
        $stmt->execute();
        $current_subject = $stmt->get_result()->fetch_assoc();
        
        // Get previous and next subjects
        $query = "SELECT 
            (SELECT id FROM subjects 
             WHERE subject_code < ? AND status = 'active'
             ORDER BY subject_code DESC LIMIT 1) as prev_id,
            (SELECT id FROM subjects 
             WHERE subject_code > ? AND status = 'active'
             ORDER BY subject_code ASC LIMIT 1) as next_id";
        
        $stmt = $db->prepare($query);
        $stmt->bind_param("ss", 
            $current_subject['subject_code'],
            $current_subject['subject_code']
        );
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        echo json_encode([
            'status' => 'success',
            'data' => $result
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}

function restoreSubject($db) {
    header('Content-Type: application/json');
    
    try {
        if (!isset($_POST['id'])) {
            throw new Exception('Subject ID is required');
        }

        $subject_id = intval($_POST['id']);
        
        // Check if subject exists
        $check_query = "SELECT id FROM subjects WHERE id = ?";
        $check_stmt = $db->prepare($check_query);
        if (!$check_stmt) {
            throw new Exception('Database prepare error: ' . $db->error);
        }
        
        $check_stmt->bind_param("i", $subject_id);
        if (!$check_stmt->execute()) {
            throw new Exception('Database execute error: ' . $check_stmt->error);
        }
        
        $result = $check_stmt->get_result();
        if ($result->num_rows === 0) {
            throw new Exception('Subject not found');
        }

        // Update subject status
        $update_query = "UPDATE subjects 
                        SET status = 'active', 
                            updated_at = CURRENT_TIMESTAMP 
                        WHERE id = ?";
                        
        $update_stmt = $db->prepare($update_query);
        if (!$update_stmt) {
            throw new Exception('Database prepare error: ' . $db->error);
        }
        
        $update_stmt->bind_param("i", $subject_id);
        if (!$update_stmt->execute()) {
            throw new Exception('Database execute error: ' . $update_stmt->error);
        }

        if ($update_stmt->affected_rows === 0) {
            throw new Exception('No changes made to subject');
        }

        echo json_encode([
            'status' => 'success',
            'message' => 'Subject restored successfully'
        ]);

    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}
?>
