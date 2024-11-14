<?php
session_start();
require_once('../db/dbConnector.php');

header('Content-Type: application/json');

if (!isset($_SESSION['teacher_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit();
}

if (!isset($_POST['section_id']) || !isset($_POST['subject_id']) || !isset($_POST['content'])) {
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit();
}

try {
    $db = new DbConnector();
    
    // Get active academic year
    $year_query = "SELECT id FROM academic_years WHERE status = 'active' LIMIT 1";
    $year_result = $db->query($year_query);
    $academic_year = $year_result->fetch_assoc();
    
    if (!$academic_year) {
        throw new Exception('No active academic year found');
    }

    $teacher_id = $_SESSION['teacher_id'];
    $section_id = (int)$_POST['section_id'];
    $subject_id = (int)$_POST['subject_id'];
    $content = trim($_POST['content']);
    $attachment_path = null;

    // Handle file upload if present
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/announcements/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_extension = strtolower(pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];

        if (!in_array($file_extension, $allowed_extensions)) {
            throw new Exception('Invalid file type');
        }

        $file_name = uniqid() . '.' . $file_extension;
        $target_path = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['attachment']['tmp_name'], $target_path)) {
            $attachment_path = 'uploads/announcements/' . $file_name;
        } else {
            throw new Exception('Error uploading file');
        }
    }

    // Verify teacher has access to this section and subject
    $verify_query = "SELECT 1 FROM section_subjects 
                    WHERE teacher_id = ? 
                    AND section_id = ? 
                    AND subject_id = ?
                    AND status = 'active'
                    AND academic_year_id = ?";
    
    $stmt = $db->prepare($verify_query);
    $stmt->bind_param("iiii", $teacher_id, $section_id, $subject_id, $academic_year['id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('Unauthorized access to section/subject');
    }

    // Insert announcement
    $insert_query = "INSERT INTO announcements 
                    (teacher_id, section_id, subject_id, content, attachment, status, created_at) 
                    VALUES (?, ?, ?, ?, ?, 'active', CURRENT_TIMESTAMP)";
    
    $stmt = $db->prepare($insert_query);
    $stmt->bind_param("iiiss", $teacher_id, $section_id, $subject_id, $content, $attachment_path);
    
    if ($stmt->execute()) {
        $announcement_id = $db->insert_id;
        
        // Get teacher name for notification
        $teacher_query = "SELECT CONCAT(firstname, ' ', lastname) as name FROM teacher WHERE teacher_id = ?";
        $teacher_stmt = $db->prepare($teacher_query);
        $teacher_stmt->bind_param("i", $teacher_id);
        $teacher_stmt->execute();
        $teacher_result = $teacher_stmt->get_result();
        $teacher = $teacher_result->fetch_assoc();
        
        // Create notifications
        createAnnouncementNotifications($db, $announcement_id, $section_id, $teacher['name']);
        
        echo json_encode(['success' => true]);
    } else {
        throw new Exception('Error creating announcement');
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

// After successfully creating the announcement, add notifications for all students in the section
function createAnnouncementNotifications($db, $announcement_id, $section_id, $teacher_name) {
    // Get all students in the section
    $student_query = "
        SELECT s.student_id 
        FROM student s
        JOIN student_sections ss ON s.student_id = ss.student_id
        WHERE ss.section_id = ? 
        AND ss.status = 'active'
        AND s.status = 'active'";
    
    $stmt = $db->prepare($student_query);
    $stmt->bind_param("i", $section_id);
    $stmt->execute();
    $students = $stmt->get_result();

    // Prepare notification insert statement
    $notification_query = "
        INSERT INTO notifications 
        (user_id, user_type, type, announcement_id, title, message, is_read, created_at) 
        VALUES (?, 'student', 'announcement', ?, ?, ?, 0, CURRENT_TIMESTAMP)";
    
    $notify_stmt = $db->prepare($notification_query);

    while ($student = $students->fetch_assoc()) {
        $title = "New Announcement";
        $message = "Teacher {$teacher_name} posted a new announcement";
        
        $notify_stmt->bind_param("iiss", 
            $student['student_id'],
            $announcement_id,
            $title,
            $message
        );
        $notify_stmt->execute();
    }
}
?>
