<?php
session_start();
require_once('../../db/dbConnector.php');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$db = new DbConnector();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $teacher_id = $_POST['teacher_id'];
        $firstname = $_POST['firstname'];
        $middlename = $_POST['middlename'] ?? null;
        $lastname = $_POST['lastname'];
        $email = $_POST['email'];
        $department_id = $_POST['department_id'];
        $subject_id = $_POST['subject_id'];
        $sections = $_POST['sections'] ?? [];
        
        $db->begin_transaction();
        
        // Update teacher basic info
        $query = "UPDATE teacher 
                 SET firstname = ?, 
                     lastname = ?, 
                     middlename = ?, 
                     email = ?, 
                     department_id = ?
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
        $academic_year = $academic_year_result->fetch_assoc();
        $academic_year_id = $academic_year['id'];

        // First, mark all current assignments as inactive
        $deactivate_query = "UPDATE section_subjects 
                           SET status = 'inactive' 
                           WHERE teacher_id = ? 
                           AND academic_year_id = ?";
        $stmt = $db->prepare($deactivate_query);
        $stmt->bind_param("ii", $teacher_id, $academic_year_id);
        $stmt->execute();

        // For each section, either reactivate existing record or create new one
        foreach ($sections as $section_id) {
            // Get schedule values
            $schedule_day = $_POST['schedule_day'];
            $schedule_time = $_POST['schedule_time'];

            // Check if there's an existing record
            $check_query = "SELECT id, status FROM section_subjects 
                          WHERE teacher_id = ? 
                          AND section_id = ? 
                          AND subject_id = ? 
                          AND academic_year_id = ?";
            $stmt = $db->prepare($check_query);
            $stmt->bind_param("iiii", $teacher_id, $section_id, $subject_id, $academic_year_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $existing = $result->fetch_assoc();

            if ($existing) {
                // Reactivate and update existing record
                $update_query = "UPDATE section_subjects 
                               SET status = 'active',
                                   schedule_day = ?,
                                   schedule_time = ?
                               WHERE id = ?";
                $stmt = $db->prepare($update_query);
                $stmt->bind_param("ssi", $schedule_day, $schedule_time, $existing['id']);
                $stmt->execute();
            } else {
                // Create new record with schedule
                $insert_query = "INSERT INTO section_subjects 
                               (section_id, subject_id, teacher_id, academic_year_id, 
                                schedule_day, schedule_time, status) 
                               VALUES (?, ?, ?, ?, ?, ?, 'active')";
                $stmt = $db->prepare($insert_query);
                $stmt->bind_param("iiiiss", 
                    $section_id, 
                    $subject_id, 
                    $teacher_id, 
                    $academic_year_id,
                    $schedule_day,
                    $schedule_time
                );
                $stmt->execute();
            }
        }
        
        $db->commit();
        echo json_encode(['success' => true, 'message' => 'Teacher updated successfully']);
        
    } catch (Exception $e) {
        $db->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?> 