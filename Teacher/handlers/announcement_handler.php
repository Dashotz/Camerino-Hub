<?php
header('Content-Type: application/json');
session_start();
require_once('../../db/dbConnector.php');

try {
    if (!isset($_SESSION['teacher_id'])) {
        throw new Exception('Not authorized');
    }

    $db = new DbConnector();
    $teacher_id = $_SESSION['teacher_id'];

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    if (!isset($_POST['action'])) {
        throw new Exception('Action not specified');
    }

    if ($_POST['action'] === 'create') {
        // Validate required fields
        if (empty($_POST['subject_id']) || empty($_POST['content'])) {
            throw new Exception('Missing required fields');
        }

        $subject_id = $_POST['subject_id'];
        $content = $_POST['content'];
        $type = $_POST['type'] ?? 'normal';
        $title = $_POST['title'] ?? null;
        
        // First, get all sections where this teacher teaches this subject
        $query = "SELECT section_id FROM section_subjects 
                 WHERE teacher_id = ? AND subject_id = ? AND status = 'active'";
        $stmt = $db->prepare($query);
        $stmt->bind_param("ii", $teacher_id, $subject_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception('No active sections found for this subject');
        }

        // Handle file upload if present
        $attachment_path = null;
        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../../uploads/announcements/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = strtolower(pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION));
            $timestamp = date('Y_m_d_His');
            $unique_id = uniqid();
            $new_filename = $timestamp . '_' . $unique_id . '_' . basename($_FILES['attachment']['name']);
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['attachment']['tmp_name'], $upload_path)) {
                $attachment_path = 'uploads/announcements/' . $new_filename;
            } else {
                throw new Exception('Failed to upload file');
            }
        }

        // Start transaction
        $db->begin_transaction();
        
        try {
            // Insert announcement for each section
            while ($row = $result->fetch_assoc()) {
                $section_id = $row['section_id'];
                
                $query = "INSERT INTO announcements (teacher_id, section_id, subject_id, content, type, title, attachment, status, created_at) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, 'active', NOW())";
                
                $stmt = $db->prepare($query);
                $stmt->bind_param("iiissss", $teacher_id, $section_id, $subject_id, $content, $type, $title, $attachment_path);
                
                if (!$stmt->execute()) {
                    throw new Exception('Failed to create announcement for section ' . $section_id);
                }
            }
            
            // Commit transaction
            $db->commit();
            
            echo json_encode([
                'success' => true,
                'message' => 'Announcements created successfully'
            ]);
            
        } catch (Exception $e) {
            // Rollback transaction on error
            $db->rollback();
            
            // Delete uploaded file if exists
            if ($attachment_path && file_exists('../../' . $attachment_path)) {
                unlink('../../' . $attachment_path);
            }
            
            throw $e;
        }
    }

    if ($_POST['action'] === 'get_announcement') {
        $announcement_id = $_POST['announcement_id'];
        
        // Get announcement details
        $query = "SELECT 
            a.id,
            a.title,
            a.content,
            a.attachment,
            a.subject_id,
            s.subject_name,
            s.subject_code
        FROM announcements a
        JOIN subjects s ON a.subject_id = s.id
        WHERE a.id = ? AND a.teacher_id = ? AND a.status = 'active'";
        
        $stmt = $db->prepare($query);
        $stmt->bind_param("ii", $announcement_id, $teacher_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            echo json_encode([
                'success' => true,
                'data' => [
                    'id' => $row['id'],
                    'title' => $row['title'],
                    'content' => $row['content'],
                    'subject_id' => $row['subject_id'],
                    'attachment' => $row['attachment'] ? '../' . $row['attachment'] : null,
                    'subject_name' => $row['subject_code'] . ' - ' . $row['subject_name']
                ]
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Announcement not found'
            ]);
        }
        exit;
    }

    if ($_POST['action'] === 'get_teacher_subjects') {
        $query = "SELECT DISTINCT 
            s.id,
            s.subject_name,
            s.subject_code,
            GROUP_CONCAT(DISTINCT sec.section_name) as sections
        FROM section_subjects ss
        JOIN subjects s ON ss.subject_id = s.id
        JOIN sections sec ON ss.section_id = sec.section_id
        WHERE ss.teacher_id = ? 
        AND ss.status = 'active'
        AND s.status = 'active'
        GROUP BY s.id, s.subject_name, s.subject_code
        ORDER BY s.subject_name";
        
        $stmt = $db->prepare($query);
        $stmt->bind_param("i", $teacher_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $subjects = [];
        while ($row = $result->fetch_assoc()) {
            $sections = explode(',', $row['sections']);
            $subjects[] = [
                'id' => $row['id'],
                'subject_name' => $row['subject_code'] . ' - ' . $row['subject_name'],
                'sections' => $sections,
                'display_name' => $row['subject_code'] . ' - ' . $row['subject_name'] . ' (' . count($sections) . ' sections)'
            ];
        }
        
        echo json_encode([
            'success' => true,
            'subjects' => $subjects
        ]);
        exit;
    }

    if ($_POST['action'] === 'get_sections') {
        if (!isset($_POST['subject_id'])) {
            throw new Exception('Subject ID not provided');
        }
        
        $subject_id = $_POST['subject_id'];
        
        $query = "SELECT DISTINCT 
            s.section_id as id,
            s.section_name as name
        FROM section_subjects ss
        JOIN sections s ON ss.section_id = s.section_id
        WHERE ss.teacher_id = ? 
        AND ss.subject_id = ?
        AND ss.status = 'active'
        AND s.status = 'active'
        ORDER BY s.section_name";
        
        $stmt = $db->prepare($query);
        $stmt->bind_param("ii", $teacher_id, $subject_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $sections = [];
        while ($row = $result->fetch_assoc()) {
            $sections[] = $row;
        }
        
        echo json_encode([
            'success' => true,
            'sections' => $sections
        ]);
        exit;
    }

    if ($_POST['action'] === 'update') {
        if (!isset($_POST['announcement_id']) || !isset($_POST['content']) || !isset($_POST['subject_id'])) {
            throw new Exception('Missing required fields');
        }

        $announcement_id = $_POST['announcement_id'];
        $content = $_POST['content'];
        $subject_id = $_POST['subject_id'];

        // Start transaction
        $db->begin_transaction();

        try {
            // First verify the announcement belongs to this teacher
            $verify_query = "SELECT id FROM announcements 
                            WHERE id = ? AND teacher_id = ? AND status = 'active'";
            $stmt = $db->prepare($verify_query);
            $stmt->bind_param("ii", $announcement_id, $teacher_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                throw new Exception('Announcement not found or unauthorized');
            }

            // Handle file upload if present
            $attachment_path = null;
            if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = '../../uploads/announcements/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $timestamp = date('Y_m_d_His');
                $unique_id = uniqid();
                $new_filename = $timestamp . '_' . $unique_id . '_' . basename($_FILES['attachment']['name']);
                $upload_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['attachment']['tmp_name'], $upload_path)) {
                    $attachment_path = 'uploads/announcements/' . $new_filename;
                    
                    // Delete old attachment if exists
                    $old_file_query = "SELECT attachment FROM announcements WHERE id = ?";
                    $stmt = $db->prepare($old_file_query);
                    $stmt->bind_param("i", $announcement_id);
                    $stmt->execute();
                    $old_result = $stmt->get_result();
                    if ($old_file = $old_result->fetch_assoc()) {
                        if ($old_file['attachment'] && file_exists('../../' . $old_file['attachment'])) {
                            unlink('../../' . $old_file['attachment']);
                        }
                    }
                }
            }

            // Update announcement
            $update_query = "UPDATE announcements SET 
                            content = ?, 
                            subject_id = ?";
            $params = [$content, $subject_id];
            $types = "si";

            if ($attachment_path) {
                $update_query .= ", attachment = ?";
                $params[] = $attachment_path;
                $types .= "s";
            }

            $update_query .= " WHERE id = ? AND teacher_id = ?";
            $params[] = $announcement_id;
            $params[] = $teacher_id;
            $types .= "ii";

            $stmt = $db->prepare($update_query);
            $stmt->bind_param($types, ...$params);
            
            if (!$stmt->execute()) {
                throw new Exception('Failed to update announcement');
            }

            $db->commit();
            
            echo json_encode([
                'success' => true,
                'message' => 'Announcement updated successfully'
            ]);

        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
    }

} catch (Exception $e) {
    error_log('Announcement Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 