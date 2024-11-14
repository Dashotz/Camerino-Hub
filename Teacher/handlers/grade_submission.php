<?php
session_start();
require_once('../../db/dbConnector.php');

header('Content-Type: application/json');

if (!isset($_SESSION['teacher_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

try {
    $db = new DbConnector();
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['submission_id']) || !isset($data['points'])) {
        throw new Exception('Missing required fields');
    }

    // Verify teacher owns this submission
    $verify_query = "
        SELECT a.activity_id, a.points as max_points 
        FROM student_activity_submissions sas
        JOIN activities a ON sas.activity_id = a.activity_id
        JOIN section_subjects ss ON a.section_subject_id = ss.id
        WHERE sas.submission_id = ? AND ss.teacher_id = ?";

    $stmt = $db->prepare($verify_query);
    $stmt->bind_param("ii", $data['submission_id'], $_SESSION['teacher_id']);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if (!$result) {
        throw new Exception('Unauthorized access to submission');
    }

    // Validate points
    if ($data['points'] < 0 || $data['points'] > $result['max_points']) {
        throw new Exception('Invalid points value');
    }

    // Update submission with grade and feedback
    $update_query = "
        UPDATE student_activity_submissions 
        SET points = ?, 
            feedback = ?,
            graded_at = NOW(),
            graded_by = ?
        WHERE submission_id = ?";

    $stmt = $db->prepare($update_query);
    $stmt->bind_param(
        "isii",
        $data['points'],
        $data['feedback'] ?? null,
        $_SESSION['teacher_id'],
        $data['submission_id']
    );

    if (!$stmt->execute()) {
        throw new Exception('Failed to save grade');
    }

    echo json_encode([
        'success' => true,
        'message' => 'Grade saved successfully'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
