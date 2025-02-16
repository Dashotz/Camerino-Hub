<?php
session_start();
require_once('../../db/dbConnector.php');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$db = new DbConnector();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($_GET['action'] === 'get_teacher') {
        getTeacher($db);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'add':
            addTeacher($db);
            break;
        case 'edit':
            editTeacher($db);
            break;
        case 'delete':
            deleteTeacher($db);
            break;
        case 'archive':
            archiveTeacher($db);
            break;
        case 'restore':
            restoreTeacher($db);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
}

function getTeacher($db) {
    try {
        $teacher_id = $_GET['teacher_id'];
        
        // Get teacher basic info
        $query = "SELECT t.*, 
                        GROUP_CONCAT(DISTINCT ss.subject_id) as subjects,
                        GROUP_CONCAT(DISTINCT ss.section_id) as sections
                 FROM teacher t
                 LEFT JOIN section_subjects ss ON t.teacher_id = ss.teacher_id
                 WHERE t.teacher_id = ? AND t.status = 'active'
                 GROUP BY t.teacher_id";
        
        $stmt = $db->prepare($query);
        $stmt->bind_param("i", $teacher_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $teacher = $result->fetch_assoc();
        
        if ($teacher) {
            // Convert comma-separated strings to arrays
            $teacher['subjects'] = $teacher['subjects'] ? explode(',', $teacher['subjects']) : [];
            $teacher['sections'] = $teacher['sections'] ? explode(',', $teacher['sections']) : [];
            
            echo json_encode(['success' => true, 'teacher' => $teacher]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Teacher not found']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function editTeacher($db) {
    try {
        $teacher_id = $_POST['teacher_id'];
        $firstname = $_POST['firstname'];
        $middlename = $_POST['middlename'] ?? null;
        $lastname = $_POST['lastname'];
        $email = $_POST['email'];
        $department_id = $_POST['department_id'];
        $subjects = $_POST['subjects'] ?? [];
        $sections = $_POST['sections'] ?? [];
        
        $db->begin_transaction();
        
        // Update teacher basic info
        $query = "UPDATE teacher 
                 SET firstname = ?, lastname = ?, middlename = ?, 
                     email = ?, department_id = ?
                 WHERE teacher_id = ?";
        
        $stmt = $db->prepare($query);
        $stmt->bind_param("ssssii", 
            $firstname, 
            $lastname, 
            $middlename, 
            $email, 
            $department_id, 
            $teacher_id
        );
        $stmt->execute();
        
        // Get active academic year
        $active_academic_year_query = "SELECT id FROM academic_years WHERE status = 'active' LIMIT 1";
        $academic_year_result = $db->query($active_academic_year_query);
        
        if (!$academic_year_result || $academic_year_result->num_rows === 0) {
            throw new Exception('No active academic year found');
        }
        
        $academic_year = $academic_year_result->fetch_assoc();
        $academic_year_id = $academic_year['id'];

        // Get current active assignments with proper JOIN
        $current_query = "SELECT ss.subject_id, ss.section_id 
                         FROM section_subjects ss
                         JOIN subjects s ON ss.subject_id = s.id
                         WHERE ss.teacher_id = ? 
                         AND ss.status = 'active'
                         AND s.status = 'active'";
                         
        $stmt = $db->prepare($current_query);
        $stmt->bind_param("i", $teacher_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $current_assignments = [];
        while ($row = $result->fetch_assoc()) {
            $current_assignments[] = $row['subject_id'] . '-' . $row['section_id'];
        }

        // Add new assignments only if they don't exist
        if (!empty($subjects) && !empty($sections)) {
            foreach ($subjects as $subject_id) {
                foreach ($sections as $section_id) {
                    $assignment_key = $subject_id . '-' . $section_id;
                    
                    // Only insert if this combination doesn't already exist
                    if (!in_array($assignment_key, $current_assignments)) {
                        $query = "INSERT INTO section_subjects 
                                 (section_id, subject_id, teacher_id, status, academic_year_id) 
                                 VALUES (?, ?, ?, 'active', ?)
                                 ON DUPLICATE KEY UPDATE status = 'active'";
                        $stmt = $db->prepare($query);
                        $stmt->bind_param("iiii", 
                            $section_id, 
                            $subject_id, 
                            $teacher_id, 
                            $academic_year_id
                        );
                        $stmt->execute();
                    }
                }
            }
        }
        
        $db->commit();
        echo json_encode(['success' => true, 'message' => 'Teacher updated successfully']);
        
    } catch (Exception $e) {
        $db->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function addTeacher($db) {
    try {
        $firstname = $_POST['firstname'];
        $middlename = $_POST['middlename'] ?? null;
        $lastname = $_POST['lastname'];
        $email = $_POST['email'];
        $department_id = $_POST['department_id'];
        $subjects = $_POST['subjects'] ?? [];
        $sections = $_POST['sections'] ?? [];
        
        // Generate username from email
        $username = explode('@', $email)[0];
        
        // Generate temporary password
        $temp_password = generateTempPassword();
        $hashed_password = md5($temp_password); // Using MD5 instead of password_hash
        
        $db->begin_transaction();
        
        // Insert teacher
        $query = "INSERT INTO teacher (username, password, email, firstname, lastname, middlename, department_id) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $db->prepare($query);
        $stmt->bind_param("ssssssi", 
            $username, 
            $hashed_password, // MD5 hashed password
            $email, 
            $firstname, 
            $lastname, 
            $middlename, 
            $department_id
        );
        $stmt->execute();
        
        $teacher_id = $db->insert_id;
        
        // Assign subjects and sections
        foreach ($subjects as $subject_id) {
            foreach ($sections as $section_id) {
                $query = "INSERT INTO section_subjects (section_id, subject_id, teacher_id, school_year) 
                         VALUES (?, ?, ?, ?)";
                $stmt = $db->prepare($query);
                $school_year = date('Y') . '-' . (date('Y') + 1);
                $stmt->bind_param("iiis", $section_id, $subject_id, $teacher_id, $school_year);
                $stmt->execute();
            }
        }
        
        $db->commit();
        
        // TODO: Send email with credentials to teacher
        
        echo json_encode([
            'success' => true, 
            'message' => 'Teacher added successfully! Temporary password: ' . $temp_password
        ]);
        
    } catch (Exception $e) {
        $db->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function deleteTeacher($db) {
    try {
        $teacher_id = $_POST['teacher_id'];
        
        $db->begin_transaction();
        
        // Soft delete by updating status
        $query = "UPDATE teacher SET status = 'inactive' WHERE teacher_id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("i", $teacher_id);
        $stmt->execute();
        
        // Also update related section_subjects
        $query = "UPDATE section_subjects SET status = 'inactive' WHERE teacher_id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("i", $teacher_id);
        $stmt->execute();
        
        $db->commit();
        
        echo json_encode(['success' => true, 'message' => 'Teacher deleted successfully']);
        
    } catch (Exception $e) {
        $db->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function generateTempPassword($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $password;
}

function archiveTeacher($db) {
    try {
        if (!isset($_POST['teacher_id']) || !is_numeric($_POST['teacher_id'])) {
            throw new Exception('Invalid teacher ID');
        }

        $teacher_id = (int)$_POST['teacher_id'];
        $admin_id = (int)$_SESSION['admin_id'];
        
        $db->begin_transaction();
        
        // Update teacher status
        $update_query = "UPDATE teacher 
                        SET status = 'archived',
                            archived_at = CURRENT_TIMESTAMP,
                            archived_by = ?,
                            updated_at = CURRENT_TIMESTAMP
                        WHERE teacher_id = ? AND status = 'active'";
        
        $stmt = $db->prepare($update_query);
        $stmt->bind_param("ii", $admin_id, $teacher_id);
        $stmt->execute();
        
        if ($stmt->affected_rows === 0) {
            throw new Exception('Teacher not found or already archived');
        }
        
        // Archive related section_subjects
        $update_sections = "UPDATE section_subjects 
                           SET status = 'archived' 
                           WHERE teacher_id = ? AND status = 'active'";
        $stmt = $db->prepare($update_sections);
        $stmt->bind_param("i", $teacher_id);
        $stmt->execute();
        
        $db->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Teacher has been successfully archived'
        ]);
        
    } catch (Exception $e) {
        $db->rollback();
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}
function restoreTeacher($db) {
    try {
        if (!isset($_POST['teacher_id']) || !is_numeric($_POST['teacher_id'])) {
            throw new Exception('Invalid teacher ID');
        }

        $teacher_id = (int)$_POST['teacher_id'];
        
        $db->begin_transaction();

        // Restore teacher status
        $update_query = "UPDATE teacher 
                        SET status = 'active',
                            archived_at = NULL,
                            archived_by = NULL,
                            updated_at = CURRENT_TIMESTAMP
                        WHERE teacher_id = ? AND status = 'archived'";
        
        $stmt = $db->prepare($update_query);
        $stmt->bind_param("i", $teacher_id);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to restore teacher');
        }

        if ($stmt->affected_rows === 0) {
            throw new Exception('Teacher not found or already active');
        }

        $db->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Teacher has been restored successfully'
        ]);

    } catch (Exception $e) {
        $db->rollback();
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}
?>
