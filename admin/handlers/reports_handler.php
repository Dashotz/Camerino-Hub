<?php
session_start();
require_once('../../db/dbConnector.php');

header('Content-Type: application/json'); // Set JSON header

try {
    if (!isset($_SESSION['admin_id'])) {
        throw new Exception('Unauthorized access');
    }

    $db = new DbConnector();
    $action = $_GET['action'] ?? '';

    switch($action) {
        case 'get_detailed_reports':
            try {
                $academic_year = $_GET['academic_year'] ?? '';
                $grade_level = $_GET['grade_level'] ?? '';
                $section = $_GET['section'] ?? '';

                $query = "
                    SELECT 
                        s.student_id,
                        CONCAT(s.firstname, ' ', s.lastname) as student_name,
                        sec.grade_level,
                        sec.section_name,
                        COALESCE(
                            (SELECT AVG(sas.points)
                            FROM student_activity_submissions sas
                            JOIN activities a ON sas.activity_id = a.activity_id
                            JOIN section_subjects ss ON a.section_subject_id = ss.id
                            WHERE sas.student_id = s.student_id
                            AND ss.academic_year_id = st_sec.academic_year_id
                            AND a.status = 'active'), 0
                        ) as average_grade
                    FROM student s
                    JOIN student_sections st_sec ON s.student_id = st_sec.student_id
                    JOIN sections sec ON st_sec.section_id = sec.section_id
                    WHERE st_sec.academic_year_id = ?
                    AND st_sec.status = 'active'
                    AND sec.status = 'active'
                ";

                $params = [$academic_year];
                $types = "i";

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
                if (!$stmt) {
                    throw new Exception("Failed to prepare statement");
                }

                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $result = $stmt->get_result();
                
                $data = [];
                while ($row = $result->fetch_assoc()) {
                    // Add academic status based on average grade
                    $avg_grade = floatval($row['average_grade']);
                    if ($avg_grade >= 90) {
                        $status = 'Excellent';
                    } elseif ($avg_grade >= 80) {
                        $status = 'Good';
                    } elseif ($avg_grade >= 75) {
                        $status = 'Average';
                    } else {
                        $status = 'Needs Improvement';
                    }
                    
                    $row['academic_status'] = $status;
                    $data[] = $row;
                }

                echo json_encode(['data' => $data]);
                
            } catch (Exception $e) {
                error_log($e->getMessage());
                echo json_encode([
                    'data' => [],
                    'error' => $e->getMessage()
                ]);
            }
            break;

        case 'get_sections':
            try {
                $grade_level = $_GET['grade_level'] ?? '';
                $academic_year = $_GET['academic_year'] ?? '';
                
                // Set content type to HTML since we're returning HTML options
                header('Content-Type: text/html; charset=utf-8');
                
                if (empty($grade_level)) {
                    echo '<option value="">All Sections</option>';
                    exit;
                }
                
                $query = "
                    SELECT DISTINCT s.section_id, s.section_name 
                    FROM sections s
                    LEFT JOIN student_sections ss ON s.section_id = ss.section_id
                    WHERE s.grade_level = ?
                    AND s.status = 'active'
                    AND (ss.academic_year_id = ? OR ss.academic_year_id IS NULL)
                    ORDER BY s.section_name
                ";

                $stmt = $db->prepare($query);
                if (!$stmt) {
                    throw new Exception("Failed to prepare statement: " . $db->error);
                }

                $stmt->bind_param("si", $grade_level, $academic_year);
                if (!$stmt->execute()) {
                    throw new Exception("Failed to execute query: " . $stmt->error);
                }
                
                $result = $stmt->get_result();
                
                $options = '<option value="">All Sections</option>';
                while ($row = $result->fetch_assoc()) {
                    $options .= sprintf(
                        '<option value="%s">%s</option>',
                        htmlspecialchars($row['section_id']),
                        htmlspecialchars($row['section_name'])
                    );
                }
                
                echo $options;
                
            } catch (Exception $e) {
                error_log("Section loading error: " . $e->getMessage());
                echo '<option value="">Error loading sections</option>';
            }
            break;

        default:
            throw new Exception('Invalid action');
    }

} catch (Exception $e) {
    error_log($e->getMessage());
    echo json_encode([
        'error' => true,
        'message' => 'An error occurred while processing your request',
        'debug' => $e->getMessage() // Remove this in production
    ]);
}
?>
