<?php
session_start();
require_once('../../db/dbConnector.php');

if (!isset($_SESSION['teacher_id'])) {
    exit('Unauthorized');
}

$db = new DbConnector();
$section_subject_id = $_POST['section_subject_id'] ?? '';
$date = $_POST['date'] ?? '';

if (empty($section_subject_id) || empty($date)) {
    exit('Missing parameters');
}

// Get students and their attendance status for the selected date
$query = "SELECT 
    s.student_id,
    s.firstname,
    s.lastname,
    a.status,
    a.time_status,
    a.attendance_time,
    a.remarks
FROM student_sections ss
JOIN student s ON ss.student_id = s.student_id
JOIN section_subjects ssub ON ss.section_id = ssub.section_id
LEFT JOIN attendance a ON s.student_id = a.student_id 
    AND a.section_subject_id = ? 
    AND a.date = ?
WHERE ssub.id = ?
AND ss.status = 'active'
ORDER BY s.lastname, s.firstname";

$stmt = $db->prepare($query);
$stmt->bind_param("isi", $section_subject_id, $date, $section_subject_id);
$stmt->execute();
$result = $stmt->get_result();

$output = '<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Status</th>
                <th>Time</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>';

while ($row = $result->fetch_assoc()) {
    $output .= '<tr>
        <td>' . htmlspecialchars($row['lastname'] . ', ' . $row['firstname']) . '
            <input type="hidden" name="student_ids[]" value="' . $row['student_id'] . '">
        </td>
        <td>
            <select name="status[' . $row['student_id'] . ']" class="form-control form-control-sm">
                <option value="present" ' . ($row['status'] == 'present' ? 'selected' : '') . '>Present</option>
                <option value="absent" ' . ($row['status'] == 'absent' ? 'selected' : '') . '>Absent</option>
                <option value="excused" ' . ($row['status'] == 'excused' ? 'selected' : '') . '>Excused</option>
            </select>
        </td>
        <td>' . ($row['attendance_time'] ? date('h:i A', strtotime($row['attendance_time'])) : '-') . '</td>
        <td>
            <input type="text" name="remarks[' . $row['student_id'] . ']" 
                class="form-control form-control-sm" 
                value="' . htmlspecialchars($row['remarks'] ?? '') . '">
        </td>
    </tr>';
}

$output .= '</tbody></table></div>';
echo $output;
?> 