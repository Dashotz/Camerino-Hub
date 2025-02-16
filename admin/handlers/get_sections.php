<?php
session_start();
require_once('../../db/dbConnector.php');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$db = new DbConnector();

$grade_level = $_GET['grade_level'] ?? null;
$academic_year = $_GET['academic_year'] ?? null;

if (!$grade_level || !$academic_year) {
    echo json_encode([]);
    exit();
}

try {
    $query = "SELECT DISTINCT 
                s.section_id,
                s.section_name
              FROM sections s
              JOIN student_sections ss ON s.section_id = ss.section_id
              WHERE s.grade_level = ?
              AND s.academic_year_id = ?
              AND s.status = 'active'
              ORDER BY s.section_name";

    $stmt = $db->prepare($query);
    $stmt->bind_param("ii", $grade_level, $academic_year);
    $stmt->execute();
    $result = $stmt->get_result();

    $sections = [];
    while ($row = $result->fetch_assoc()) {
        $sections[] = [
            'section_id' => $row['section_id'],
            'section_name' => $row['section_name']
        ];
    }

    echo json_encode($sections);

} catch (Exception $e) {
    error_log("Error fetching sections: " . $e->getMessage());
    echo json_encode([]);
}
?> 