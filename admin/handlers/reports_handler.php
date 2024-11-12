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
        case 'get_enrollment_stats':
            getEnrollmentStats($db);
            break;
        case 'get_performance_stats':
            getPerformanceStats($db);
            break;
        case 'get_attendance_stats':
            getAttendanceStats($db);
            break;
        case 'get_teacher_stats':
            getTeacherStats($db);
            break;
        case 'get_detailed_reports':
            getDetailedReports($db);
            break;
    }
}

function getEnrollmentStats($db) {
    try {
        $academic_year = $_GET['academic_year'] ?? null;
        $grade_level = $_GET['grade_level'] ?? null;
        $section = $_GET['section'] ?? null;

        $query = "SELECT 
            COUNT(DISTINCT ss.student_id) as total_students,
            s.grade_level,
            DATE_FORMAT(ss.created_at, '%Y-%m') as enrollment_month
        FROM student_sections ss
        JOIN sections s ON ss.section_id = s.section_id
        WHERE ss.academic_year_id = ?";

        $params = [$academic_year];
        $types = "i";

        if ($grade_level) {
            $query .= " AND s.grade_level = ?";
            $params[] = $grade_level;
            $types .= "i";
        }

        if ($section) {
            $query .= " AND ss.section_id = ?";
            $params[] = $section;
            $types .= "i";
        }

        $query .= " GROUP BY enrollment_month, s.grade_level
                   ORDER BY enrollment_month";

        $stmt = $db->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();

        $stats = [];
        while ($row = $result->fetch_assoc()) {
            $stats[] = $row;
        }

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

function getPerformanceStats($db) {
    try {
        $academic_year = $_GET['academic_year'] ?? null;
        $grade_level = $_GET['grade_level'] ?? null;
        
        $query = "SELECT 
            s.grade_level,
            ROUND(AVG(sas.score), 2) as average_score
        FROM student_activity_submissions sas
        JOIN activities a ON sas.activity_id = a.activity_id
        JOIN section_subjects ss ON a.section_subject_id = ss.id
        JOIN sections s ON ss.section_id = s.section_id
        WHERE ss.academic_year_id = ?
        GROUP BY s.grade_level
        ORDER BY s.grade_level";

        $stmt = $db->prepare($query);
        $stmt->bind_param("i", $academic_year);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $stats = [];
        while ($row = $result->fetch_assoc()) {
            $stats[] = $row;
        }

        echo json_encode(['status' => 'success', 'data' => $stats]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

function getAttendanceStats($db) {
    try {
        $academic_year = $_GET['academic_year'] ?? null;
        $section_id = $_GET['section'] ?? null;

        $query = "SELECT 
            status,
            COUNT(*) as count
        FROM attendance
        WHERE section_subject_id IN (
            SELECT id FROM section_subjects 
            WHERE academic_year_id = ?" .
            ($section_id ? " AND section_id = ?" : "") . 
        ")
        GROUP BY status";

        $stmt = $db->prepare($query);
        if ($section_id) {
            $stmt->bind_param("ii", $academic_year, $section_id);
        } else {
            $stmt->bind_param("i", $academic_year);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        $stats = [];
        while ($row = $result->fetch_assoc()) {
            $stats[] = $row;
        }

        echo json_encode(['status' => 'success', 'data' => $stats]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

function getDetailedReports($db) {
    try {
        $academic_year = $_GET['academic_year'] ?? null;
        $grade_level = $_GET['grade_level'] ?? null;
        $section = $_GET['section'] ?? null;

        $query = "SELECT 
            CONCAT(s.firstname, ' ', s.lastname) as student_name,
            sec.grade_level,
            sec.section_name as section,
            (SELECT 
                ROUND(COUNT(CASE WHEN status = 'present' THEN 1 END) * 100.0 / COUNT(*), 2)
                FROM attendance a
                JOIN section_subjects ss ON a.section_subject_id = ss.id
                WHERE ss.academic_year_id = ? 
                AND a.student_id = s.student_id
            ) as attendance_rate,
            (SELECT 
                ROUND(AVG(sas.points), 2)
                FROM student_activity_submissions sas
                JOIN activities act ON sas.activity_id = act.activity_id
                JOIN section_subjects ss ON act.section_subject_id = ss.id
                WHERE ss.academic_year_id = ?
                AND sas.student_id = s.student_id
            ) as average_grade,
            CASE 
                WHEN AVG(sas.points) >= 90 THEN 'Excellent'
                WHEN AVG(sas.points) >= 80 THEN 'Good'
                WHEN AVG(sas.points) >= 75 THEN 'Average'
                ELSE 'Needs Improvement'
            END as status
        FROM student s
        JOIN student_sections ss ON s.student_id = ss.student_id
        JOIN sections sec ON ss.section_id = sec.section_id
        LEFT JOIN student_activity_submissions sas ON s.student_id = sas.student_id
        WHERE ss.academic_year_id = ?";

        $params = [$academic_year, $academic_year, $academic_year];
        $types = "iii";

        if ($grade_level) {
            $query .= " AND sec.grade_level = ?";
            $params[] = $grade_level;
            $types .= "i";
        }

        if ($section) {
            $query .= " AND sec.section_id = ?";
            $params[] = $section;
            $types .= "i";
        }

        $query .= " GROUP BY s.student_id, s.firstname, s.lastname, sec.grade_level, sec.section_name";

        $stmt = $db->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $data = [];
        while ($row = $result->fetch_assoc()) {
            // Ensure numeric values are properly formatted
            $row['attendance_rate'] = floatval($row['attendance_rate'] ?? 0);
            $row['average_grade'] = floatval($row['average_grade'] ?? 0);
            $data[] = $row;
        }

        // Format response according to DataTables specification
        header('Content-Type: application/json');
        echo json_encode([
            'draw' => intval($_GET['draw'] ?? 1),
            'recordsTotal' => count($data),
            'recordsFiltered' => count($data),
            'data' => $data
        ]);

    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode([
            'draw' => intval($_GET['draw'] ?? 1),
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => [],
            'error' => $e->getMessage()
        ]);
    }
}

// Add other functions (getTeacherStats, getDetailedReports, etc.)
