<?php
session_start();
require_once('../../db/dbConnector.php');

if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

$db = new DbConnector();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
    
    switch($action) {
        case 'get_year_stats':
            getYearStats($db);
            break;
        case 'get_academic_years':
            getAcademicYears($db);
            break;
        case 'get_year_details':
            getYearDetails($db);
            break;
        case 'get_archived_year_details':
            getArchivedYearDetails($db);
            break;
        case 'get_archived_years':
            getArchivedYears($db);
            break;
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch($action) {
        case 'add_year':
            addAcademicYear($db);
            break;
        case 'update_year':
            updateAcademicYear($db);
            break;
        case 'delete_year':
            deleteAcademicYear($db);
            break;
        case 'archive_year':
            archiveAcademicYear($db);
            break;
        case 'restore_year':
            restoreAcademicYear($db);
            break;
        case 'delete_archived_year':
            deleteArchivedYear($db);
            break;
    }
}

function getYearStats($db) {
    try {
        $stats_query = "SELECT 
            (SELECT COUNT(*) FROM academic_years) as total_years,
            
            (SELECT COUNT(DISTINCT ss.student_id) 
             FROM student_sections ss
             JOIN academic_years ay ON ss.academic_year_id = ay.id
             WHERE ay.status = 'active') as current_enrollees,
            
            (SELECT COUNT(*) 
             FROM teacher 
             WHERE status = 'active') as active_teachers,
            
            (SELECT COUNT(*) 
             FROM sections 
             WHERE status = 'active') as active_sections";
        
        $result = $db->query($stats_query);
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

function getAcademicYears($db) {
    try {
        $query = "SELECT 
            ay.id,
            ay.school_year,
            ay.start_date,
            ay.end_date,
            ay.status,
            (SELECT COUNT(*) 
             FROM student_sections ss 
             WHERE ss.academic_year_id = ay.id) as enrollee_count
        FROM academic_years ay
        WHERE ay.is_archived = 0
        ORDER BY ay.school_year DESC";
                  
        $result = $db->query($query);
        $years = [];
        
        while ($row = $result->fetch_assoc()) {
            $years[] = $row;
        }
        
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'data' => $years
        ]);
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}

function getYearDetails($db) {
    try {
        $year_id = $_GET['id'] ?? 0;
        
        // Get academic year details
        $year_query = "SELECT * FROM academic_years WHERE id = ?";
        $stmt = $db->prepare($year_query);
        $stmt->bind_param("i", $year_id);
        $stmt->execute();
        $year_result = $stmt->get_result();
        $year_data = $year_result->fetch_assoc();

        // Get enrollment details for this academic year
        $enrollment_query = "
            SELECT 
                s.*,
                sec.section_name,
                ss.status as enrollment_status,
                ss.created_at as date_enrolled
            FROM student_sections ss
            JOIN student s ON ss.student_id = s.student_id
            JOIN sections sec ON ss.section_id = sec.section_id
            WHERE ss.academic_year_id = ?
            ORDER BY s.lastname, s.firstname";
        
        $stmt = $db->prepare($enrollment_query);
        $stmt->bind_param("i", $year_id);
        $stmt->execute();
        $enrollment_result = $stmt->get_result();
        
        $enrollments = [];
        while ($row = $enrollment_result->fetch_assoc()) {
            $enrollments[] = [
                'firstname' => $row['firstname'],
                'lastname' => $row['lastname'],
                'email' => $row['email'],
                'section_name' => $row['section_name'],
                'enrollment_status' => $row['enrollment_status'],
                'date_enrolled' => $row['date_enrolled']
            ];
        }

        // Get enrollment statistics
        $stats_query = "
            SELECT 
                COUNT(*) as total_enrollees,
                SUM(CASE WHEN ss.status = 'active' THEN 1 ELSE 0 END) as active_enrollees,
                SUM(CASE WHEN ss.status = 'inactive' THEN 1 ELSE 0 END) as archived_enrollees
            FROM student_sections ss
            WHERE ss.academic_year_id = ?";
        
        $stmt = $db->prepare($stats_query);
        $stmt->bind_param("i", $year_id);
        $stmt->execute();
        $stats_result = $stmt->get_result();
        $stats = $stats_result->fetch_assoc();

        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'data' => [
                'year_details' => $year_data,
                'enrollments' => $enrollments,
                'statistics' => $stats
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

function updateAcademicYear($db) {
    try {
        $year_id = $_POST['year_id'] ?? 0;
        $school_year = $_POST['school_year'] ?? '';
        $start_date = $_POST['start_date'] ?? '';
        $end_date = $_POST['end_date'] ?? '';
        $status = $_POST['status'] ?? 'inactive';

        // Validate dates
        if (strtotime($end_date) <= strtotime($start_date)) {
            throw new Exception('End date must be after start date');
        }

        $query = "UPDATE academic_years 
                 SET school_year = ?, 
                     start_date = ?, 
                     end_date = ?, 
                     status = ? 
                 WHERE id = ?";
                 
        $stmt = $db->prepare($query);
        $stmt->bind_param("ssssi", $school_year, $start_date, $end_date, $status, $year_id);
        
        if ($stmt->execute()) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success']);
        } else {
            throw new Exception('Failed to update academic year');
        }
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}

function addAcademicYear($db) {
    try {
        $school_year = $_POST['school_year'] ?? '';
        $start_date = $_POST['start_date'] ?? '';
        $end_date = $_POST['end_date'] ?? '';
        $status = $_POST['status'] ?? 'inactive';

        // Validate dates
        if (strtotime($end_date) <= strtotime($start_date)) {
            throw new Exception('End date must be after start date');
        }

        // Check if school year already exists
        $check_query = "SELECT id FROM academic_years WHERE school_year = ?";
        $stmt = $db->prepare($check_query);
        $stmt->bind_param("s", $school_year);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            throw new Exception('School year already exists');
        }

        // Insert new academic year
        $query = "INSERT INTO academic_years (school_year, start_date, end_date, status) 
                 VALUES (?, ?, ?, ?)";
                 
        $stmt = $db->prepare($query);
        $stmt->bind_param("ssss", $school_year, $start_date, $end_date, $status);
        
        if ($stmt->execute()) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success']);
        } else {
            throw new Exception('Failed to add academic year');
        }
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}

function deleteAcademicYear($db) {
    try {
        $year_id = $_POST['id'] ?? 0;

        // Start transaction
        $db->begin_transaction();

        // Check if academic year exists and is not archived
        $check_query = "SELECT id FROM academic_years WHERE id = ? AND is_archived = 0";
        $stmt = $db->prepare($check_query);
        $stmt->bind_param("i", $year_id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows === 0) {
            throw new Exception('Academic year not found or is already archived');
        }

        // Check for existing enrollments
        $check_enrollments = "SELECT COUNT(*) as count FROM student_sections WHERE academic_year_id = ?";
        $stmt = $db->prepare($check_enrollments);
        $stmt->bind_param("i", $year_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        if ($result['count'] > 0) {
            throw new Exception('Cannot delete academic year with existing enrollments. Archive it instead.');
        }

        // Delete associated activities and their files
        $get_section_subjects = "SELECT id FROM section_subjects WHERE academic_year_id = ?";
        $stmt = $db->prepare($get_section_subjects);
        $stmt->bind_param("i", $year_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            // Delete activity files
            $delete_activity_files = "DELETE af FROM activity_files af 
                                    INNER JOIN activities a ON af.activity_id = a.activity_id 
                                    WHERE a.section_subject_id = ?";
            $stmt2 = $db->prepare($delete_activity_files);
            $stmt2->bind_param("i", $row['id']);
            $stmt2->execute();

            // Delete activities
            $delete_activities = "DELETE FROM activities WHERE section_subject_id = ?";
            $stmt2 = $db->prepare($delete_activities);
            $stmt2->bind_param("i", $row['id']);
            $stmt2->execute();
        }

        // Delete associated records in section_subjects
        $delete_subjects = "DELETE FROM section_subjects WHERE academic_year_id = ?";
        $stmt = $db->prepare($delete_subjects);
        $stmt->bind_param("i", $year_id);
        $stmt->execute();

        // Delete associated records in section_advisers
        $delete_advisers = "DELETE FROM section_advisers WHERE academic_year_id = ?";
        $stmt = $db->prepare($delete_advisers);
        $stmt->bind_param("i", $year_id);
        $stmt->execute();

        // Delete sections associated with this academic year
        $delete_sections = "DELETE FROM sections WHERE school_year = (
            SELECT school_year FROM academic_years WHERE id = ?
        )";
        $stmt = $db->prepare($delete_sections);
        $stmt->bind_param("i", $year_id);
        $stmt->execute();

        // Finally delete the academic year
        $delete_year = "DELETE FROM academic_years WHERE id = ?";
        $stmt = $db->prepare($delete_year);
        $stmt->bind_param("i", $year_id);
        $stmt->execute();

        $db->commit();

        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'message' => 'Academic year deleted successfully'
        ]);

    } catch (Exception $e) {
        $db->rollback();
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}

function archiveAcademicYear($db) {
    try {
        $year_id = $_POST['year_id'] ?? 0;
        $admin_id = $_SESSION['admin_id'];

        $db->begin_transaction();

        // Get academic year details first
        $get_year = "SELECT * FROM academic_years WHERE id = ?";
        $stmt = $db->prepare($get_year);
        $stmt->bind_param("i", $year_id);
        $stmt->execute();
        $year_result = $stmt->get_result();
        $year_data = $year_result->fetch_assoc();

        if (!$year_data) {
            throw new Exception('Academic year not found');
        }

        // Update student_sections status to inactive for this academic year
        $update_enrollments = "UPDATE student_sections 
                             SET status = 'inactive' 
                             WHERE academic_year_id = ? 
                             AND status = 'active'";
        $stmt = $db->prepare($update_enrollments);
        $stmt->bind_param("i", $year_id);
        $stmt->execute();

        // Insert into archive_academic_years
        $archive_query = "INSERT INTO archive_academic_years 
                         (original_id, school_year, start_date, end_date, 
                          status, archived_by, archived_at) 
                         VALUES (?, ?, ?, ?, 'archived', ?, NOW())";
        
        $stmt = $db->prepare($archive_query);
        $stmt->bind_param("isssi", 
            $year_id, 
            $year_data['school_year'], 
            $year_data['start_date'], 
            $year_data['end_date'], 
            $admin_id
        );
        $stmt->execute();

        // Update original record
        $update_query = "UPDATE academic_years SET is_archived = 1 WHERE id = ?";
        $stmt = $db->prepare($update_query);
        $stmt->bind_param("i", $year_id);
        $stmt->execute();

        $db->commit();

        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Academic year archived successfully']);

    } catch (Exception $e) {
        $db->rollback();
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

function restoreAcademicYear($db) {
    try {
        $archive_id = $_POST['archive_id'] ?? 0;
        $admin_id = $_SESSION['admin_id'];

        $db->begin_transaction();

        // Get archived year details
        $get_archived = "SELECT * FROM archive_academic_years WHERE id = ?";
        $stmt = $db->prepare($get_archived);
        $stmt->bind_param("i", $archive_id);
        $stmt->execute();
        $archive_result = $stmt->get_result();
        $archive_data = $archive_result->fetch_assoc();

        // Update original academic year - set status to active and remove archived flag
        $restore_query = "UPDATE academic_years 
                         SET is_archived = 0, 
                             status = 'active',
                             archived_at = NULL 
                         WHERE id = ?";
        $stmt = $db->prepare($restore_query);
        $stmt->bind_param("i", $archive_data['original_id']);
        $stmt->execute();

        // Reactivate student enrollments for this academic year
        $reactivate_enrollments = "UPDATE student_sections 
                                 SET status = 'active' 
                                 WHERE academic_year_id = ? 
                                 AND status = 'inactive'";
        $stmt = $db->prepare($reactivate_enrollments);
        $stmt->bind_param("i", $archive_data['original_id']);
        $stmt->execute();

        // Delete the archive record since it's been restored
        $delete_archive = "DELETE FROM archive_academic_years WHERE id = ?";
        $stmt = $db->prepare($delete_archive);
        $stmt->bind_param("i", $archive_id);
        $stmt->execute();

        $db->commit();

        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success', 
            'message' => 'Academic year restored successfully'
        ]);

    } catch (Exception $e) {
        $db->rollback();
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}

function getArchivedYearDetails($db) {
    try {
        $archive_id = $_GET['archive_id'] ?? 0;
        
        $query = "SELECT 
            ar.*,
            a1.username as archived_by_admin,
            a2.username as restored_by_admin
        FROM archive_academic_years ar
        LEFT JOIN admin a1 ON ar.archived_by = a1.admin_id
        LEFT JOIN admin a2 ON ar.restored_by = a2.admin_id
        WHERE ar.id = ?";
        
        $stmt = $db->prepare($query);
        $stmt->bind_param("i", $archive_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $archive_data = $result->fetch_assoc();
        
        if (!$archive_data) {
            throw new Exception('Archive record not found');
        }
        
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'data' => $archive_data
        ]);
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}

function getArchivedYears($db) {
    try {
        $query = "SELECT 
            ar.*,
            a1.username as archived_by_admin,
            a2.username as restored_by_admin
        FROM archive_academic_years ar
        LEFT JOIN admin a1 ON ar.archived_by = a1.admin_id
        LEFT JOIN admin a2 ON ar.restored_by = a2.admin_id
        WHERE ar.status = 'archived'  -- Only show archived status records
        ORDER BY ar.archived_at DESC";
                  
        $result = $db->query($query);
        $archives = [];
        
        while ($row = $result->fetch_assoc()) {
            $archives[] = [
                'id' => $row['id'],
                'school_year' => $row['school_year'],
                'start_date' => $row['start_date'],
                'end_date' => $row['end_date'],
                'status' => $row['status'],
                'archived_at' => $row['archived_at'],
                'archived_by_admin' => $row['archived_by_admin']
            ];
        }
        
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'data' => $archives
        ]);
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}

function deleteArchivedYear($db) {
    try {
        $archive_id = $_POST['archive_id'] ?? 0;
        
        // Start transaction
        $db->begin_transaction();
        
        // Get archive record details first
        $get_archive = "SELECT original_id FROM archive_academic_years WHERE id = ?";
        $stmt = $db->prepare($get_archive);
        $stmt->bind_param("i", $archive_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $archive_data = $result->fetch_assoc();
        
        if (!$archive_data) {
            throw new Exception('Archive record not found');
        }
        
        // Delete from archive_academic_years
        $delete_archive = "DELETE FROM archive_academic_years WHERE id = ?";
        $stmt = $db->prepare($delete_archive);
        $stmt->bind_param("i", $archive_id);
        $stmt->execute();
        
        // Update original academic year record to remove archived flag
        $update_original = "UPDATE academic_years 
                          SET is_archived = 0, archived_at = NULL 
                          WHERE id = ?";
        $stmt = $db->prepare($update_original);
        $stmt->bind_param("i", $archive_data['original_id']);
        $stmt->execute();
        
        $db->commit();
        
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'message' => 'Archived year record has been permanently deleted'
        ]);
        
    } catch (Exception $e) {
        $db->rollback();
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}

// Add other necessary functions (addAcademicYear, updateAcademicYear, etc.)
