<?php
session_start();
require_once('../../db/dbConnector.php');

if (!isset($_SESSION['teacher_id'])) {
    die('Unauthorized access');
}

$db = new DbConnector();
$teacher_id = $_SESSION['teacher_id'];
$section_subject_id = $_POST['section_subject_id']; // Add this parameter to your form

// First, verify that this teacher has access to this section_subject
$verify_query = "
    SELECT ss.section_id, s.section_name, sub.subject_name 
    FROM section_subjects ss
    JOIN sections s ON ss.section_id = s.section_id
    JOIN subjects sub ON ss.subject_id = sub.id
    WHERE ss.id = ? AND ss.teacher_id = ? AND ss.status = 'active'";

$stmt = $db->prepare($verify_query);
$stmt->bind_param("ii", $section_subject_id, $teacher_id);
$stmt->execute();
$section_result = $stmt->get_result();
$section_data = $section_result->fetch_assoc();

if (!$section_data) {
    die('Invalid section or unauthorized access');
}

// Base query for getting attendance data
$base_query = "
    SELECT 
        s.student_id,
        s.firstname,
        s.lastname,
        s.middlename,
        a.date,
        a.status,
        a.remarks
    FROM student s
    JOIN student_sections ss ON s.student_id = ss.student_id
    LEFT JOIN attendance a ON s.student_id = a.student_id 
        AND a.section_subject_id = ?
    WHERE ss.section_id = ?
    AND ss.status = 'active'
    AND s.status = 'active'";

// Modify query based on export type
if (isset($_POST['export_today'])) {
    $base_query .= " AND DATE(a.date) = CURDATE()";
    $filename = "attendance_{$section_data['section_name']}_{$section_data['subject_name']}_" . date('Y-m-d') . ".csv";
} elseif (isset($_POST['export_month'])) {
    $month = $_POST['month'];
    $year = $_POST['year'];
    $base_query .= " AND MONTH(a.date) = ? AND YEAR(a.date) = ?";
    $filename = "attendance_{$section_data['section_name']}_{$section_data['subject_name']}_{$year}_{$month}.csv";
} elseif (isset($_POST['export_range'])) {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $base_query .= " AND DATE(a.date) BETWEEN ? AND ?";
    $filename = "attendance_{$section_data['section_name']}_{$section_data['subject_name']}_{$start_date}_to_{$end_date}.csv";
}

$base_query .= " ORDER BY s.lastname, s.firstname, a.date";

$stmt = $db->prepare($base_query);

// Bind parameters based on export type
if (isset($_POST['export_today'])) {
    $stmt->bind_param("ii", $section_subject_id, $section_data['section_id']);
} elseif (isset($_POST['export_month'])) {
    $stmt->bind_param("iiii", $section_subject_id, $section_data['section_id'], $month, $year);
} elseif (isset($_POST['export_range'])) {
    $stmt->bind_param("iiss", $section_subject_id, $section_data['section_id'], $start_date, $end_date);
}

$stmt->execute();
$result = $stmt->get_result();

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Create CSV file
$output = fopen('php://output', 'w');

// Add headers to CSV
fputcsv($output, ['Student ID', 'Last Name', 'First Name', 'Middle Name', 'Date', 'Status', 'Remarks']);

// Add data to CSV
while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['student_id'],
        $row['lastname'],
        $row['firstname'],
        $row['middlename'],
        $row['date'],
        $row['status'],
        $row['remarks']
    ]);
}

fclose($output);
exit;
?>
