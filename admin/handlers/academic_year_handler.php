<?php
session_start();
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
        case 'get_year_stats':
            getYearStats($db);
            break;
        case 'get_academic_years':
            getAcademicYears($db);
            break;
        case 'get_year_details':
            getYearDetails($db);
            break;
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch($action) {
        case 'add_year':
            addAcademicYear($db);
            break;
        case 'update_year':
            updateAcademicYear($db);
            break;
        case 'delete_year':
            deleteAcademicYear($db);
            break;
    }
}

function getYearStats($db) {
    try {
        $stats_query = "SELECT 
            (SELECT COUNT(*) FROM academic_years) as total_years,
            
            (SELECT COUNT(DISTINCT ss.student_id) 
             FROM student_sections ss
             JOIN academic_years ay ON ss.academic_year_id = ay.id
             WHERE ay.status = 'active') as current_enrollees,
            
            (SELECT COUNT(*) 
             FROM teacher 
             WHERE status = 'active') as active_teachers,
            
            (SELECT COUNT(*) 
             FROM sections 
             WHERE status = 'active') as active_sections";
        
        $result = $db->query($stats_query);
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

function getAcademicYears($db) {
    try {
        $query = "SELECT 
            ay.*,
            (SELECT COUNT(DISTINCT student_id) 
             FROM student_sections 
             WHERE academic_year_id = ay.id) as enrollee_count
        FROM academic_years ay
        ORDER BY ay.year_start DESC";
        
        $result = $db->query($query);
        $years = [];
        
        while ($row = $result->fetch_assoc()) {
            $years[] = $row;
        }

        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'data' => $years
        ]);
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}

// Add other necessary functions (addAcademicYear, updateAcademicYear, etc.)
