<?php
session_start();
require_once('../../db/dbConnector.php');

if (!isset($_SESSION['teacher_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

try {
    $db = new DbConnector();
    $section_subject_id = $_POST['section_subject_id'];
    $date = $_POST['date'];
    $students = $_POST['students'];
    $statuses = $_POST['status'];
    $remarks = $_POST['remarks'];
    
    // Begin transaction
    $db->begin_transaction();
    
    foreach ($students as $student_id) {
        // Check if attendance record exists
        $check_query = "
            SELECT id FROM attendance 
            WHERE student_id = ? 
            AND section_subject_id = ? 
            AND date = ?";
        
        $stmt = $db->prepare($check_query);
        $stmt->bind_param("iis", $student_id, $section_subject_id, $date);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Update existing record
            $update_query = "
                UPDATE attendance 
                SET status = ?, 
                    remarks = ?
                WHERE student_id = ? 
                AND section_subject_id = ? 
                AND date = ?";
            
            $stmt = $db->prepare($update_query);
            $stmt->bind_param("sssis", 
                $statuses[$student_id],
                $remarks[$student_id],
                $student_id,
                $section_subject_id,
                $date
            );
            $stmt->execute();
        } else {
            // Insert new record
            $insert_query = "
                INSERT INTO attendance 
                    (student_id, section_subject_id, date, status, remarks) 
                VALUES (?, ?, ?, ?, ?)";
            
            $stmt = $db->prepare($insert_query);
            $stmt->bind_param("iisss", 
                $student_id,
                $section_subject_id,
                $date,
                $statuses[$student_id],
                $remarks[$student_id]
            );
            $stmt->execute();
        }
    }
    
    // Log the attendance action
    $log_query = "
        INSERT INTO attendance_logs 
            (attendance_id, teacher_id, action, details) 
        VALUES (LAST_INSERT_ID(), ?, 'save', ?)";
    
    $details = "Attendance saved for section_subject_id: $section_subject_id, date: $date";
    $stmt = $db->prepare($log_query);
    $stmt->bind_param("is", $_SESSION['teacher_id'], $details);
    $stmt->execute();
    
    $db->commit();
    echo json_encode(['success' => true]);
    
} catch (Exception $e) {
    $db->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?> 