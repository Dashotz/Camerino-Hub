<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['teacher_id'])) {
    header("Location: Teacher-Login.php");
    exit();
}

$db = new DbConnector();
$teacher_id = $_SESSION['teacher_id'];

// Retrieve selected IDs from form
$selected_ids = isset($_POST['selected_ids']) ? $_POST['selected_ids'] : [];
$current_date = date("Y-m-d");
$section_id = $_POST['section_id'] ?? null;

if (!$section_id) {
    header("Location: attendance.php?error=no_section");
    exit();
}

try {
    $db->begin_transaction();

    // First, mark all students in the section as absent for today
    $update_query = "INSERT INTO attendance (student_id, section_id, teacher_id, date, status) 
                    SELECT 
                        s.student_id,
                        ?,
                        ?,
                        ?,
                        'absent'
                    FROM student s
                    JOIN student_sections ss ON s.student_id = ss.student_id
                    WHERE ss.section_id = ?
                    ON DUPLICATE KEY UPDATE status = 'absent'";
    
    $stmt = $db->prepare($update_query);
    $stmt->bind_param("iisi", $section_id, $teacher_id, $current_date, $section_id);
    $stmt->execute();

    // Then, update selected students as present
    if (!empty($selected_ids)) {
        $placeholders = str_repeat('?,', count($selected_ids) - 1) . '?';
        $update_present = "UPDATE attendance 
                          SET status = 'present' 
                          WHERE student_id IN ($placeholders) 
                          AND date = ? 
                          AND section_id = ?";
        
        $types = str_repeat('i', count($selected_ids)) . 'si';
        $params = array_merge($selected_ids, [$current_date, $section_id]);
        
        $stmt = $db->prepare($update_present);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
    }

    // Log the attendance update
    $log_query = "INSERT INTO attendance_logs 
                  (teacher_id, section_id, date, action) 
                  VALUES (?, ?, ?, 'Attendance marked')";
    $stmt = $db->prepare($log_query);
    $stmt->bind_param("iis", $teacher_id, $section_id, $current_date);
    $stmt->execute();

    $db->commit();
    header("Location: attendance.php?success=true&section=" . $section_id);

} catch (Exception $e) {
    $db->rollback();
    error_log("Attendance Error: " . $e->getMessage());
    header("Location: attendance.php?error=db_error&section=" . $section_id);
} finally {
    $db->close();
}
?>
