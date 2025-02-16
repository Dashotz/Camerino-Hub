<?php
session_start();
require_once('../../db/dbConnector.php');

if (!isset($_SESSION['teacher_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$db = new DbConnector();
$section_subject_id = $_POST['section_subject_id'] ?? '';
$date = $_POST['date'] ?? '';
$student_ids = $_POST['student_ids'] ?? [];
$statuses = $_POST['status'] ?? [];
$remarks = $_POST['remarks'] ?? [];

if (empty($section_subject_id) || empty($date) || empty($student_ids)) {
    echo json_encode(['success' => false, 'message' => 'Missing required data']);
    exit;
}

try {
    $db->begin_transaction();

    $insert_query = "INSERT INTO attendance 
        (student_id, section_subject_id, date, status, remarks) 
        VALUES (?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
        status = VALUES(status),
        remarks = VALUES(remarks)";
    
    $stmt = $db->prepare($insert_query);

    foreach ($student_ids as $student_id) {
        $status = $statuses[$student_id] ?? 'absent';
        $remark = $remarks[$student_id] ?? '';
        
        $stmt->bind_param("iisss", 
            $student_id, 
            $section_subject_id, 
            $date,
            $status,
            $remark
        );
        $stmt->execute();
    }

    $db->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $db->rollback();
    echo json_encode(['success' => false, 'message' => 'Error saving attendance: ' . $e->getMessage()]);
}
?> 