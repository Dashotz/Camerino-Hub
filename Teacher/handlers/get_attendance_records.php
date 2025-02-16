<?php
session_start();
require_once('../../db/dbConnector.php');

if (!isset($_POST['section_subject_id']) || !isset($_POST['date'])) {
    die("Invalid request");
}

$db = new DbConnector();
$section_subject_id = $_POST['section_subject_id'];
$date = $_POST['date'];

// Get attendance records with student details and time
$query = "SELECT 
    s.student_id,
    s.firstname,
    s.lastname,
    a.status,
    a.time_in,
    TIME_FORMAT(a.time_in, '%h:%i %p') as formatted_time
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

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['lastname'] . ", " . $row['firstname']) . "</td>";
    echo "<td>" . ($row['status'] ?? 'absent') . "</td>";
    echo "<td>" . ($row['formatted_time'] ?? 'N/A') . "</td>";
    echo "</tr>";
}
?> 