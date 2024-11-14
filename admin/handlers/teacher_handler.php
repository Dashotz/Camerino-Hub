<?php
session_start();
require_once('../../db/dbConnector.php');

if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

$db = new DbConnector();
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'get_dashboard_stats':
        getDashboardStats($db);
        break;
    case 'get_teacher_assignments':
        getTeacherAssignments($db);
        break;
    case 'add_teacher_assignment':
        addTeacherAssignment($db);
        break;
    case 'update_teacher_assignment':
        updateTeacherAssignment($db);
        break;
    case 'delete_teacher_assignment':
        deleteTeacherAssignment($db);
        break;
    case 'assign_teacher':
        assignTeacher($db);
        break;
    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        break;
}

function getDashboardStats($db) {
    try {
        // Get total teachers and active teachers
        $teachers_query = "SELECT 
            COUNT(*) as total_teachers,
            SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_teachers
        FROM teacher";
        $teachers_stats = $db->query($teachers_query)->fetch_assoc();

        // Get assigned subjects count
        $subjects_query = "SELECT 
            COUNT(DISTINCT ss.subject_id) as total_assigned_subjects
        FROM section_subjects ss
        WHERE ss.status = 'active'";
        $subjects_stats = $db->query($subjects_query)->fetch_assoc();

        // Get active sections count
        $sections_query = "SELECT 
            COUNT(*) as total_active_sections
        FROM sections 
        WHERE status = 'active'";
        $sections_stats = $db->query($sections_query)->fetch_assoc();

        // Get unique departments count
        $departments_query = "SELECT 
            COUNT(DISTINCT department) as total_departments
        FROM teacher 
        WHERE status = 'active' 
        AND department IS NOT NULL 
        AND department != ''";
        $departments_stats = $db->query($departments_query)->fetch_assoc();

        // Return all stats
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'data' => [
                'teachers' => [
                    'total' => $teachers_stats['total_teachers'],
                    'active' => $teachers_stats['active_teachers']
                ],
                'subjects' => [
                    'assigned' => $subjects_stats['total_assigned_subjects']
                ],
                'sections' => [
                    'active' => $sections_stats['total_active_sections']
                ],
                'departments' => [
                    'total' => $departments_stats['total_departments']
                ]
            ]
        ]);

    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}

function getTeacherAssignments($db) {
    try {
        $query = "SELECT 
            ss.id,
            CONCAT(t.firstname, ' ', t.lastname) as teacher_name,
            t.department,
            s.subject_code,
            s.subject_title,
            sec.section_name,
            sec.grade_level,
            ss.schedule_day,
            ss.schedule_time,
            ss.status
        FROM section_subjects ss
        JOIN teacher t ON ss.teacher_id = t.teacher_id
        JOIN subjects s ON ss.subject_id = s.id
        JOIN sections sec ON ss.section_id = sec.section_id
        WHERE ss.status = 'active'
        ORDER BY t.lastname, t.firstname, s.subject_code";
        
        $result = $db->query($query);
        $assignments = [];
        
        while ($row = $result->fetch_assoc()) {
            $assignments[] = $row;
        }
        
        echo json_encode([
            'status' => 'success',
            'data' => $assignments
        ]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

function addTeacherAssignment($db) {
    try {
        $db->begin_transaction();

        // Check for schedule conflicts
        $conflict_query = "SELECT COUNT(*) as conflict_count 
        FROM section_subjects 
        WHERE teacher_id = ? 
        AND schedule_day = ? 
        AND schedule_time = ? 
        AND status = 'active'";
        
        $stmt = $db->prepare($conflict_query);
        $stmt->bind_param("iss", 
            $_POST['teacher_id'], 
            $_POST['schedule_day'], 
            $_POST['schedule_time']
        );
        $stmt->execute();
        $conflict = $stmt->get_result()->fetch_assoc();

        if ($conflict['conflict_count'] > 0) {
            throw new Exception('Schedule conflict detected');
        }

        // Insert new assignment
        $query = "INSERT INTO section_subjects (
            teacher_id,
            subject_id,
            section_id,
            schedule_day,
            schedule_time,
            status,
            created_at
        ) VALUES (?, ?, ?, ?, ?, 'active', NOW())";

        $stmt = $db->prepare($query);
        $stmt->bind_param("iiiss",
            $_POST['teacher_id'],
            $_POST['subject_id'],
            $_POST['section_id'],
            $_POST['schedule_day'],
            $_POST['schedule_time']
        );

        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }

        $db->commit();
        echo json_encode([
            'status' => 'success',
            'message' => 'Teacher assignment added successfully'
        ]);
    } catch (Exception $e) {
        $db->rollback();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

function updateTeacherAssignment($db) {
    try {
        $db->begin_transaction();

        $query = "UPDATE section_subjects SET
            teacher_id = ?,
            subject_id = ?,
            section_id = ?,
            schedule_day = ?,
            schedule_time = ?,
            updated_at = NOW()
        WHERE id = ?";

        $stmt = $db->prepare($query);
        $stmt->bind_param("iiissi",
            $_POST['teacher_id'],
            $_POST['subject_id'],
            $_POST['section_id'],
            $_POST['schedule_day'],
            $_POST['schedule_time'],
            $_POST['assignment_id']
        );

        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }

        $db->commit();
        echo json_encode([
            'status' => 'success',
            'message' => 'Assignment updated successfully'
        ]);
    } catch (Exception $e) {
        $db->rollback();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

function deleteTeacherAssignment($db) {
    header('Content-Type: application/json');
    
    try {
        if (!isset($_POST['assignment_id'])) {
            throw new Exception('Assignment ID is required');
        }

        $assignment_id = intval($_POST['assignment_id']);
        
        $db->begin_transaction();

        // First check if the assignment exists and is active
        $check_query = "SELECT id FROM section_subjects WHERE id = ? AND status = 'active'";
        $stmt = $db->prepare($check_query);
        $stmt->bind_param("i", $assignment_id);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows === 0) {
            throw new Exception('Assignment not found or already inactive');
        }

        // Update the assignment status to inactive
        $query = "UPDATE section_subjects SET 
                    status = 'inactive'
                 WHERE id = ?";

        $stmt = $db->prepare($query);
        $stmt->bind_param("i", $assignment_id);

        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }

        $db->commit();
        echo json_encode([
            'status' => 'success',
            'message' => 'Assignment deleted successfully'
        ]);

    } catch (Exception $e) {
        $db->rollback();
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}

function assignTeacher($db) {
    header('Content-Type: application/json');
    
    try {
        if (!isset($_POST['teacher_id'], $_POST['subject_id'], $_POST['section_ids'], $_POST['schedule_day'], $_POST['schedule_time'])) {
            throw new Exception('Missing required fields');
        }

        $db->begin_transaction();

        $teacher_id = $_POST['teacher_id'];
        $subject_id = $_POST['subject_id'];
        $section_ids = is_array($_POST['section_ids']) ? $_POST['section_ids'] : [$_POST['section_ids']];
        $schedule_day = $_POST['schedule_day'];
        $schedule_time = $_POST['schedule_time'];

        // Validate inputs
        if (empty($teacher_id) || empty($subject_id) || empty($section_ids) || empty($schedule_day) || empty($schedule_time)) {
            throw new Exception('All fields are required');
        }

        // Check if teacher already has a different subject assigned
        $existing_subject_check = "SELECT DISTINCT subject_id 
                                 FROM section_subjects 
                                 WHERE teacher_id = ? 
                                 AND subject_id != ? 
                                 AND status = 'active'";
        $stmt = $db->prepare($existing_subject_check);
        $stmt->bind_param("ii", $teacher_id, $subject_id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            throw new Exception('This teacher is already assigned to a different subject');
        }

        // Check schedule conflicts for each section
        foreach ($section_ids as $section_id) {
            $conflict_check = "SELECT COUNT(*) as conflict_count 
                             FROM section_subjects 
                             WHERE section_id = ? 
                             AND schedule_day = ? 
                             AND schedule_time = ? 
                             AND status = 'active'";
            
            $stmt = $db->prepare($conflict_check);
            $stmt->bind_param("iss", $section_id, $schedule_day, $schedule_time);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            
            if ($result['conflict_count'] > 0) {
                throw new Exception('Schedule conflict detected for one or more sections');
            }
        }

        // Get current academic year
        $academic_year_query = "SELECT id FROM academic_years WHERE status = 'active' LIMIT 1";
        $academic_year_result = $db->query($academic_year_query);
        if ($academic_year_result->num_rows === 0) {
            throw new Exception('No active academic year found');
        }
        $academic_year_id = $academic_year_result->fetch_assoc()['id'];

        // Insert assignments for each section
        $insert_query = "INSERT INTO section_subjects (
            teacher_id, subject_id, section_id, academic_year_id,
            schedule_day, schedule_time, enrollment_code, status, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, 'active', NOW())";

        $stmt = $db->prepare($insert_query);

        foreach ($section_ids as $section_id) {
            $enrollment_code = generateEnrollmentCode($db);
            $stmt->bind_param("iiiisss", 
                $teacher_id, $subject_id, $section_id, $academic_year_id,
                $schedule_day, $schedule_time, $enrollment_code
            );
            
            if (!$stmt->execute()) {
                throw new Exception($stmt->error);
            }
        }

        $db->commit();
        echo json_encode([
            'status' => 'success',
            'message' => 'Teacher successfully assigned to sections'
        ]);

    } catch (Exception $e) {
        $db->rollback();
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}

function generateEnrollmentCode($db) {
    do {
        $code = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
        $check = $db->query("SELECT id FROM section_subjects WHERE enrollment_code = '$code'");
    } while ($check->num_rows > 0);
    
    return $code;
}
?>
