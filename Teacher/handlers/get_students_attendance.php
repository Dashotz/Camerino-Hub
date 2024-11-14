<?php
session_start();
require_once('../../db/dbConnector.php');

if (!isset($_SESSION['teacher_id'])) {
    die('Unauthorized access');
}

if (!isset($_POST['section_subject_id']) || !isset($_POST['date'])) {
    die('Missing required parameters');
}

$db = new DbConnector();
$section_subject_id = $_POST['section_subject_id'];
$date = $_POST['date'];

// First, get the section_id from section_subjects
$section_query = "
    SELECT section_id 
    FROM section_subjects 
    WHERE id = ? AND teacher_id = ? AND status = 'active'";

$stmt = $db->prepare($section_query);
$stmt->bind_param("ii", $section_subject_id, $_SESSION['teacher_id']);
$stmt->execute();
$section_result = $stmt->get_result();
$section_data = $section_result->fetch_assoc();

if (!$section_data) {
    die('Invalid section or unauthorized access');
}

// Get students in the section with their attendance status
$query = "
    SELECT 
        s.student_id,
        s.firstname,
        s.lastname,
        s.middlename,
        COALESCE(a.status, '') as attendance_status,
        COALESCE(a.remarks, '') as remarks
    FROM student s
    JOIN student_sections ss ON s.student_id = ss.student_id
    LEFT JOIN attendance a ON s.student_id = a.student_id 
        AND a.section_subject_id = ? 
        AND a.date = ?
    WHERE ss.section_id = ?
    AND ss.status = 'active'
    AND s.status = 'active'
    ORDER BY s.lastname, s.firstname";

$stmt = $db->prepare($query);
$stmt->bind_param("isi", $section_subject_id, $date, $section_data['section_id']);
$stmt->execute();
$students = $stmt->get_result();

if ($students->num_rows === 0) {
    echo '<div class="alert alert-info">No students found in this class.</div>';
    exit;
}
?>

<table class="table table-striped">
    <thead>
        <tr>
            <th width="40%">Student Name</th>
            <th width="30%">Status</th>
            <th width="30%">Remarks</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($student = $students->fetch_assoc()): ?>
            <tr>
                <td>
                    <?php 
                    echo htmlspecialchars($student['lastname'] . ', ' . 
                         $student['firstname'] . ' ' . 
                         ($student['middlename'] ? substr($student['middlename'], 0, 1) . '.' : ''));
                    ?>
                    <input type="hidden" name="students[]" value="<?php echo $student['student_id']; ?>">
                </td>
                <td>
                    <select class="form-control status-badge" 
                            name="status[<?php echo $student['student_id']; ?>]" required>
                        <option value="present" <?php echo $student['attendance_status'] === 'present' ? 'selected' : ''; ?>>Present</option>
                        <option value="absent" <?php echo $student['attendance_status'] === 'absent' ? 'selected' : ''; ?>>Absent</option>
                        <option value="late" <?php echo $student['attendance_status'] === 'late' ? 'selected' : ''; ?>>Late</option>
                        <option value="excused" <?php echo $student['attendance_status'] === 'excused' ? 'selected' : ''; ?>>Excused</option>
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control" 
                           name="remarks[<?php echo $student['student_id']; ?>]"
                           value="<?php echo htmlspecialchars($student['remarks']); ?>"
                           placeholder="Optional remarks">
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<script>
// Add this to style the status options
$('.status-badge').change(function() {
    $(this).removeClass('bg-success bg-danger bg-warning bg-secondary text-white');
    
    switch($(this).val()) {
        case 'present':
            $(this).addClass('bg-success text-white');
            break;
        case 'absent':
            $(this).addClass('bg-danger text-white');
            break;
        case 'late':
            $(this).addClass('bg-warning text-white');
            break;
        case 'excused':
            $(this).addClass('bg-secondary text-white');
            break;
    }
}).trigger('change');
</script> 