<?php
session_start();
require_once('../../db/dbConnector.php');

if (!isset($_SESSION['teacher_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

try {
    $db = new DbConnector();
    $submission_id = intval($_POST['submission_id']);
    $points = floatval($_POST['points']);
    $feedback = $_POST['feedback'] ?? '';
    $teacher_id = $_SESSION['teacher_id'];

    // Validate points range
    if ($points < 0 || $points > 100) {
        throw new Exception('Points must be between 0 and 100');
    }

    // Verify teacher owns this submission
    $verify_query = "
        SELECT a.teacher_id 
        FROM student_activity_submissions sas
        JOIN activities a ON sas.activity_id = a.activity_id
        WHERE sas.submission_id = ?";
    
    $stmt = $db->prepare($verify_query);
    $stmt->bind_param("i", $submission_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if (!$result || $result['teacher_id'] != $teacher_id) {
        throw new Exception('Unauthorized to grade this submission');
    }

    // Handle file upload if present
    $result_file_path = null;
    if (isset($_FILES['result_file']) && $_FILES['result_file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['result_file'];
        $allowed_types = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        
        if (!in_array($file['type'], $allowed_types)) {
            throw new Exception('Invalid file type. Only PDF, DOC, and DOCX files are allowed.');
        }

        // Update the upload directory path and create if it doesn't exist
        $upload_base = dirname(dirname(dirname(__FILE__))); // Go up to root directory
        $upload_dir = $upload_base . '/uploads/results/';

        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
            // Create .htaccess to prevent direct access
            $htaccess = $upload_dir . '.htaccess';
            if (!file_exists($htaccess)) {
                file_put_contents($htaccess, 'Deny from all');
            }
        }

        // Generate unique filename
        $filename = uniqid() . '_' . basename($file['name']);
        $filepath = $upload_dir . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            $result_file_path = 'uploads/results/' . $filename;
        } else {
            throw new Exception('Failed to upload result file');
        }
    }

    // Update submission
    $update_query = "
        UPDATE student_activity_submissions 
        SET points = ?, 
            feedback = ?, 
            graded_at = NOW(),
            graded_by = ?,
            status = 'graded'";
    
    if ($result_file_path) {
        $update_query .= ", result_file = ?";
    }
    
    $update_query .= " WHERE submission_id = ?";
    
    $stmt = $db->prepare($update_query);
    
    if ($result_file_path) {
        $stmt->bind_param("dsiss", $points, $feedback, $teacher_id, $result_file_path, $submission_id);
    } else {
        $stmt->bind_param("dsii", $points, $feedback, $teacher_id, $submission_id);
    }

    if ($stmt->execute()) {
        // Add notification for student
        $notify_query = "
            INSERT INTO notifications (
                user_id, 
                section_id,
                subject_id,
                user_type,
                type,
                reference_id,
                title,
                message
            )
            SELECT 
                sas.student_id,
                ss.section_id,
                ss.subject_id,
                'student',
                'activity',
                a.activity_id,
                CONCAT('Activity Graded: ', a.title),
                CONCAT('Your submission for ', a.title, ' has been graded. Score: ', ?)
            FROM student_activity_submissions sas
            JOIN activities a ON sas.activity_id = a.activity_id
            JOIN section_subjects ss ON a.section_subject_id = ss.id
            WHERE sas.submission_id = ?";

        $stmt = $db->prepare($notify_query);
        $stmt->bind_param("di", $points, $submission_id);
        $stmt->execute();

        echo json_encode([
            'success' => true,
            'message' => 'Grade and result file saved successfully'
        ]);
    } else {
        throw new Exception('Failed to save grade');
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
