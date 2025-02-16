<?php
session_start();
require_once('../../db/dbConnector.php');

if (!isset($_SESSION['teacher_id'])) {
    exit('Unauthorized');
}

$db = new DbConnector();
$section_subject_id = $_POST['section_subject_id'] ?? '';
$range = $_POST['range'] ?? 'today';
$start_date = $_POST['start_date'] ?? '';
$end_date = $_POST['end_date'] ?? '';

if (empty($section_subject_id)) {
    exit('Missing section');
}

// Calculate date range based on selection
switch($range) {
    case 'today':
        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d');
        break;
    case 'week':
        $start_date = date('Y-m-d', strtotime('monday this week'));
        $end_date = date('Y-m-d', strtotime('sunday this week'));
        break;
    case 'month':
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t');
        break;
    case 'custom':
        // Use the provided dates
        break;
}

$query = "SELECT 
    a.date,
    s.firstname,
    s.lastname,
    a.status,
    a.attendance_time
FROM student_sections ss
JOIN student s ON ss.student_id = s.student_id
JOIN section_subjects ssub ON ss.section_id = ssub.section_id
LEFT JOIN attendance a ON s.student_id = a.student_id 
    AND a.section_subject_id = ? 
    AND a.date BETWEEN ? AND ?
WHERE ssub.id = ?
AND ss.status = 'active'
ORDER BY a.date DESC, s.lastname, s.firstname";

$stmt = $db->prepare($query);
$stmt->bind_param("issi", $section_subject_id, $start_date, $end_date, $section_subject_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo '<tr><td colspan="4" class="text-center">No records found for the selected date range</td></tr>';
    exit;
}

while ($row = $result->fetch_assoc()) {
    $statusClass = '';
    $status = $row['status'] ?? 'Absent';
    switch($status) {
        case 'present':
            $statusClass = 'text-success';
            break;
        case 'absent':
            $statusClass = 'text-danger';
            break;
        case 'late':
            $statusClass = 'text-warning';
            break;
    }
    
    echo '<tr>
        <td>' . date('M d, Y', strtotime($row['date'])) . '</td>
        <td>' . htmlspecialchars($row['lastname'] . ', ' . $row['firstname']) . '</td>
        <td class="' . $statusClass . '">' . ucfirst($status) . '</td>
        <td>' . ($row['attendance_time'] ? date('h:i A', strtotime($row['attendance_time'])) : '-') . '</td>
    </tr>';
}
?> 