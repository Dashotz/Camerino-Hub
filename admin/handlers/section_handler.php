<?php
require_once('../../db/dbConnector.php');

if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

$db = new DbConnector();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
    
    switch($action) {
        case 'get_section_stats':
            getSectionStats($db);
            break;
        case 'get_sections':
            getSections($db);
            break;
        // Add other cases as needed
    }
}

function getSectionStats($db) {
    try {
        // Get current academic year
        $academic_year_query = "SELECT id FROM academic_years WHERE status = 'active' LIMIT 1";
        $current_academic_year = $db->query($academic_year_query)->fetch_assoc()['id'];

        $stats_query = "SELECT 
            (SELECT COUNT(*) FROM sections) as total_sections,
            
            (SELECT COUNT(DISTINCT student_id) 
             FROM student_sections 
             WHERE academic_year_id = ?) as total_students,
            
            (SELECT COUNT(DISTINCT adviser_id) 
             FROM sections 
             WHERE adviser_id IS NOT NULL) as assigned_advisers,
            
            (SELECT COUNT(*) 
             FROM sections 
             WHERE status = 'active') as active_sections";
        
        $stmt = $db->prepare($stats_query);
        $stmt->bind_param("i", $current_academic_year);
        $stmt->execute();
        $result = $stmt->get_result();
        $stats = $result->fetch_assoc();

        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'data' => $stats
        ]);
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}

function getSections($db) {
    try {
        $query = "SELECT 
            s.section_id,
            s.section_name,
            s.grade_level,
            CONCAT(t.firstname, ' ', t.lastname) as adviser_name,
            (SELECT COUNT(*) 
             FROM student_sections ss 
             WHERE ss.section_id = s.section_id) as student_count,
            s.status
        FROM sections s
        LEFT JOIN teacher t ON s.adviser_id = t.teacher_id
        ORDER BY s.grade_level, s.section_name";
        
        $result = $db->query($query);
        $sections = [];
        
        while ($row = $result->fetch_assoc()) {
            $sections[] = $row;
        }

        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'data' => $sections
        ]);
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}
