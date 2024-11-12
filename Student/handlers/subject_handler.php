<?php
session_start();
require_once('../../db/dbConnector.php');

if (!isset($_SESSION['id'])) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

$db = new DbConnector();
$student_id = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch($action) {
        case 'join_subject':
            try {
                $code = $_POST['code'] ?? '';
                
                // Validate code format
                if (!preg_match('/^CMRH\d{4}$/', $code)) {
                    throw new Exception('Invalid enrollment code format');
                }
                
                // Find the section_subject with this code
                $query = "SELECT 
                    ss.id as section_subject_id,
                    ss.section_id,
                    ss.academic_year_id,
                    s.subject_name,
                    sec.section_name
                FROM section_subjects ss
                JOIN sections sec ON ss.section_id = sec.section_id
                JOIN subjects s ON ss.subject_id = s.id
                WHERE ss.enrollment_code = ? 
                AND ss.status = 'active'";
                
                $stmt = $db->prepare($query);
                $stmt->bind_param("s", $code);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows === 0) {
                    throw new Exception('Invalid enrollment code');
                }
                
                $subject = $result->fetch_assoc();
                
                // Check if student is already enrolled
                $check_query = "SELECT id FROM student_sections 
                    WHERE student_id = ? 
                    AND section_id = ? 
                    AND academic_year_id = ?";
                $check = $db->prepare($check_query);
                $check->bind_param("iii", $student_id, $subject['section_id'], $subject['academic_year_id']);
                $check->execute();
                
                if ($check->get_result()->num_rows > 0) {
                    throw new Exception('You are already enrolled in this subject');
                }
                
                // Enroll the student
                $enroll_query = "INSERT INTO student_sections 
                    (student_id, section_id, academic_year_id, status, created_at) 
                    VALUES (?, ?, ?, 'active', NOW())";
                $enroll = $db->prepare($enroll_query);
                $enroll->bind_param("iii", $student_id, $subject['section_id'], $subject['academic_year_id']);
                
                if (!$enroll->execute()) {
                    throw new Exception('Failed to enroll in subject');
                }
                
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Successfully joined ' . $subject['subject_name'] . ' - ' . $subject['section_name']
                ]);
                
            } catch (Exception $e) {
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ]);
            }
            break;
    }
}
?>
