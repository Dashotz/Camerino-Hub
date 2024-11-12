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
        try {
            $query = "SELECT 
                s.id,
                s.subject_code,
                s.subject_title,
                s.subject_name,
                s.category,
                s.description,
                COUNT(DISTINCT ss.section_id) as section_count
            FROM subjects s
            LEFT JOIN section_subjects ss ON s.id = ss.subject_id
            WHERE s.status = 'active'
            GROUP BY s.id, s.subject_code, s.subject_title, s.subject_name, s.category, s.description
            ORDER BY s.subject_code ASC";
            
            $result = $db->query($query);
            $subjects = [];
            
            while ($row = $result->fetch_assoc()) {
                $subjects[] = [
                    'id' => $row['id'],
                    'subject_code' => $row['subject_code'],
                    'subject_title' => $row['subject_title'],
                    'subject_name' => $row['subject_name'],
                    'category' => $row['category'],
                    'description' => $row['description'],
                    'section_count' => (int)$row['section_count']
                ];
            }
            
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'success',
                'data' => $subjects
            ]);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
        break;
    case 'add_subject':
        addSubject($db);
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
    default:
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        exit;
}

function addSubject($db) {
    try {
        if (!isset($_POST['subject_code']) || !isset($_POST['subject_title']) || !isset($_POST['category'])) {
            throw new Exception('Missing required fields');
        }

        // Validate subject code format
        if (!preg_match('/^[A-Z0-9]{3,10}$/', $_POST['subject_code'])) {
            throw new Exception('Invalid subject code format');
        }

        // Check if subject code exists
        $check_query = "SELECT id FROM subjects WHERE subject_code = ? AND status = 'active'";
        $check_stmt = $db->prepare($check_query);
        $check_stmt->bind_param("s", $_POST['subject_code']);
        $check_stmt->execute();
        if ($check_stmt->get_result()->num_rows > 0) {
            throw new Exception('Subject code already exists');
        }

        // Insert new subject
        $query = "INSERT INTO subjects (subject_code, subject_title, subject_name, category, description, status) 
                 VALUES (?, ?, ?, ?, ?, 'active')";
        $stmt = $db->prepare($query);
        $stmt->bind_param("sssss", 
            $_POST['subject_code'],
            $_POST['subject_title'],
            $_POST['subject_title'], // subject_name same as title
            $_POST['category'],
            $_POST['description'] ?? ''
        );
        
        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }

        echo json_encode([
            'status' => 'success',
            'message' => 'Subject added successfully'
        ]);
    } catch (Exception $e) {
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
        $id = $_GET['id'];
        $query = "SELECT s.*, 
            COUNT(DISTINCT ss.teacher_id) as teacher_count,
            COUNT(DISTINCT ss.section_id) as section_count
        FROM subjects s
        LEFT JOIN section_subjects ss ON s.id = ss.subject_id AND ss.status = 'active'
        WHERE s.id = ?
        GROUP BY s.id";
        
        $stmt = $db->prepare($query);
        $stmt->bind_param("i", $id);
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

function editSubject($db) {
    try {
        if (!isset($_POST['id']) || !isset($_POST['subject_code']) || !isset($_POST['subject_title'])) {
            throw new Exception('Missing required fields');
        }

        $id = $_POST['id'];
        
        // Check if subject code exists for other subjects
        $check_query = "SELECT id FROM subjects WHERE subject_code = ? AND id != ? AND status = 'active'";
        $check_stmt = $db->prepare($check_query);
        $check_stmt->bind_param("si", $_POST['subject_code'], $id);
        $check_stmt->execute();
        if ($check_stmt->get_result()->num_rows > 0) {
            throw new Exception('Subject code already exists');
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
            $_POST['subject_code'],
            $_POST['subject_title'],
            $_POST['subject_title'], // subject_name same as title
            $_POST['category'],
            $_POST['description'],
            $id
        );
        
        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }

        echo json_encode([
            'status' => 'success',
            'message' => 'Subject updated successfully'
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}

function archiveSubject($db) {
    try {
        if (!isset($_POST['id'])) {
            throw new Exception('Subject ID is required');
        }

        $id = $_POST['id'];
        
        // Check if subject is being used in active sections
        $check_query = "SELECT COUNT(*) as count FROM section_subjects WHERE subject_id = ? AND status = 'active'";
        $check_stmt = $db->prepare($check_query);
        $check_stmt->bind_param("i", $id);
        $check_stmt->execute();
        $result = $check_stmt->get_result()->fetch_assoc();
        
        if ($result['count'] > 0) {
            throw new Exception('Cannot archive subject that is currently being used in active sections');
        }

        // Archive the subject
        $query = "UPDATE subjects SET status = 'archived' WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("i", $id);
        
        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }

        echo json_encode([
            'status' => 'success',
            'message' => 'Subject archived successfully'
        ]);
    } catch (Exception $e) {
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

        // Get all stats in one query
        $stats_query = "SELECT 
            (SELECT COUNT(*) 
             FROM subjects) as total_subjects,
            
            (SELECT COUNT(*) 
             FROM subjects 
             WHERE status = 'active') as active_subjects,
            
            (SELECT COUNT(DISTINCT teacher_id) 
             FROM section_subjects 
             WHERE academic_year_id = ? 
             AND status = 'active') as assigned_teachers,
            
            (SELECT COUNT(*) 
             FROM subjects 
             WHERE status = 'inactive') as archived_subjects";
        
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
?>
