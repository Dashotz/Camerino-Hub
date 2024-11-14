<?php
session_start();
require_once('../../db/dbConnector.php');

if (!isset($_SESSION['teacher_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

try {
    $db = new DbConnector();
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['student_id']) || !isset($data['section_subject_id'])) {
        throw new Exception('Missing required fields');
    }

    // Get all activities and their weights
    $query = "
        SELECT 
            a.activity_id,
            a.type,
            a.points as max_points,
            sas.points as earned_points
        FROM activities a
        LEFT JOIN student_activity_submissions sas 
            ON a.activity_id = sas.activity_id 
            AND sas.student_id = ?
        WHERE a.section_subject_id = ? 
        AND a.status = 'active'";

    $stmt = $db->prepare($query);
    $stmt->bind_param("ii", $data['student_id'], $data['section_subject_id']);
    $stmt->execute();
    $activities = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Calculate grades by type
    $totals = [
        'activity' => ['earned' => 0, 'max' => 0, 'weight' => 0.3],
        'assignment' => ['earned' => 0, 'max' => 0, 'weight' => 0.3],
        'quiz' => ['earned' => 0, 'max' => 0, 'weight' => 0.4]
    ];

    foreach ($activities as $activity) {
        $type = $activity['type'];
        if (isset($totals[$type])) {
            $totals[$type]['earned'] += $activity['earned_points'] ?? 0;
            $totals[$type]['max'] += $activity['max_points'];
        }
    }

    // Calculate weighted average
    $final_grade = 0;
    $total_weight = 0;

    foreach ($totals as $type => $data) {
        if ($data['max'] > 0) {
            $percentage = ($data['earned'] / $data['max']) * 100;
            $weighted_score = $percentage * $data['weight'];
            $final_grade += $weighted_score;
            $total_weight += $data['weight'];
        }
    }

    // Adjust final grade if not all activity types are present
    if ($total_weight > 0) {
        $final_grade = ($final_grade / $total_weight) * 100;
    }

    // Update or insert final grade
    $update_query = "
        INSERT INTO student_grades 
            (student_id, section_subject_id, final_grade, calculated_at) 
        VALUES (?, ?, ?, NOW())
        ON DUPLICATE KEY UPDATE 
            final_grade = VALUES(final_grade),
            calculated_at = VALUES(calculated_at)";

    $stmt = $db->prepare($update_query);
    $stmt->bind_param("iid", 
        $data['student_id'], 
        $data['section_subject_id'],
        $final_grade
    );
    $stmt->execute();

    echo json_encode([
        'success' => true,
        'data' => [
            'final_grade' => round($final_grade, 2),
            'breakdown' => $totals
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 