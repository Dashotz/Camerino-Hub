<?php
session_start();
require_once('../db/dbConnector.php');

// Check if teacher is logged in
if (!isset($_SESSION['teacher_id'])) {
    header('HTTP/1.0 403 Forbidden');
    exit('Access Denied');
}

$db = new DbConnector();
$teacher_id = $_SESSION['teacher_id'];

// Fetch students with their data
$students_query = "
    SELECT DISTINCT
        s.student_id,
        s.firstname,
        s.lastname,
        s.email,
        sec.section_name,
        subj.subject_name,
        subj.subject_code,
        ss.created_at as enrollment_date,
        COALESCE(AVG(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) * 100, 0) as attendance_rate,
        COALESCE(AVG(sas.points), 0) as performance,
        COUNT(DISTINCT sas.submission_id) as total_submissions,
        COUNT(DISTINCT CASE WHEN sas.status = 'graded' THEN sas.submission_id END) as graded_submissions
    FROM student s
    JOIN student_sections st_sec ON s.student_id = st_sec.student_id
    JOIN sections sec ON st_sec.section_id = sec.section_id
    JOIN section_subjects ss ON sec.section_id = ss.section_id
    JOIN subjects subj ON ss.subject_id = subj.id
    LEFT JOIN attendance a ON s.student_id = a.student_id AND a.section_subject_id = ss.id
    LEFT JOIN student_activity_submissions sas ON s.student_id = sas.student_id
    WHERE ss.teacher_id = ?
    AND ss.status = 'active'
    AND st_sec.status = 'active'
    GROUP BY 
        s.student_id, 
        s.firstname, 
        s.lastname, 
        s.email,
        sec.section_name,
        subj.subject_name,
        subj.subject_code,
        ss.created_at
    ORDER BY subj.subject_name, sec.section_name, s.lastname, s.firstname";

$stmt = $db->prepare($students_query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$students = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="students_data_' . date('Y-m-d') . '.csv"');
header('Pragma: no-cache');
header('Expires: 0');

// Create output stream
$output = fopen('php://output', 'w');

// Add UTF-8 BOM for proper Excel encoding
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Add CSV headers
fputcsv($output, [
    'Student ID',
    'First Name',
    'Last Name',
    'Email',
    'Section',
    'Subject',
    'Subject Code',
    'Enrollment Date',
    'Attendance Rate (%)',
    'Performance (%)',
    'Total Submissions',
    'Graded Submissions'
]);

// Add data rows
foreach ($students as $student) {
    fputcsv($output, [
        $student['student_id'],
        $student['firstname'],
        $student['lastname'],
        $student['email'],
        $student['section_name'],
        $student['subject_name'],
        $student['subject_code'],
        date('Y-m-d', strtotime($student['enrollment_date'])),
        number_format($student['attendance_rate'], 2),
        number_format($student['performance'], 2),
        $student['total_submissions'],
        $student['graded_submissions']
    ]);
}

// Close the output stream
fclose($output);
exit(); 