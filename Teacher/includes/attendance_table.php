<?php
if (!isset($_SESSION['teacher_id'])) {
    exit('Direct access not permitted');
}

// Get current academic year
$year_query = "SELECT id FROM academic_years WHERE status = 'active' LIMIT 1";
$year_result = $db->query($year_query);
$academic_year_id = $year_result->fetch_assoc()['id'];

// Get sections taught by this teacher
$sections_query = "SELECT DISTINCT s.section_id, s.section_name 
                  FROM sections s
                  JOIN section_subjects ss ON s.section_id = ss.section_id
                  WHERE ss.teacher_id = ? 
                  AND ss.academic_year_id = ?
                  AND ss.status = 'active'
                  ORDER BY s.section_name";

$stmt = $db->prepare($sections_query);
$stmt->bind_param("ii", $teacher_id, $academic_year_id);
$stmt->execute();
$sections_result = $stmt->get_result();
?>

<div class="section-selector mb-4">
    <label for="section">Select Section:</label>
    <select name="section" id="section" class="form-control" onchange="loadSectionAttendance(this.value)">
        <option value="">Choose a section...</option>
        <?php while ($section = $sections_result->fetch_assoc()): ?>
            <option value="<?php echo $section['section_id']; ?>">
                <?php echo $section['section_name']; ?>
            </option>
        <?php endwhile; ?>
    </select>
</div>

<form id="attendanceForm" action="process_selected.php" method="post">
    <input type="hidden" name="section_id" id="section_id">
    <table class="attendance-table">
        <thead>
            <tr>
                <th>Select</th>
                <th>Student ID</th>
                <th>Name</th>
                <th>Status</th>
                <th>Last Updated</th>
            </tr>
        </thead>
        <tbody id="studentList">
            <tr>
                <td colspan="5" class="text-center">Please select a section to view students</td>
            </tr>
        </tbody>
    </table>

    <div class="button-container mt-3">
        <button type="submit" class="btn-export">
            <i class="fas fa-save"></i> Submit Attendance
        </button>
    </div>
</form>

<script>
function loadSectionAttendance(sectionId) {
    if (!sectionId) return;
    
    document.getElementById('section_id').value = sectionId;
    
    fetch(`get_section_attendance.php?section_id=${sectionId}`)
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('studentList');
            tbody.innerHTML = data.map(student => `
                <tr class="${student.status || 'absent'}">
                    <td>
                        <input type="checkbox" name="selected_ids[]" 
                               value="${student.student_id}" 
                               ${student.status === 'present' ? 'checked' : ''}>
                    </td>
                    <td>${student.student_id}</td>
                    <td>${student.lastname}, ${student.firstname}</td>
                    <td>${student.status || 'Not marked'}</td>
                    <td>${student.last_updated || 'N/A'}</td>
                </tr>
            `).join('');
        })
        .catch(error => console.error('Error:', error));
}
</script>