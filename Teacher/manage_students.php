<?php
session_start();
require_once('../db/dbConnector.php');

// Check if teacher is logged in
if (!isset($_SESSION['teacher_id'])) {
    header("Location: Teacher-Login.php");
    exit();
}

$db = new DbConnector();
$teacher_id = $_SESSION['teacher_id'];

// At the top of your file, get the selected section
$selected_section = isset($_GET['section']) ? $_GET['section'] : 'All Classes';

// Modify the query to handle both all classes and specific sections
$students_query = "
    SELECT DISTINCT
        s.student_id,
        s.firstname,
        s.lastname,
        s.email,
        s.profile_image,
        s.lrn,
        sec.section_name,
        subj.subject_name,
        subj.subject_code,
        ss.id as section_subject_id,
        st_sec.created_at as enrollment_date,
        COALESCE(AVG(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) * 100, 0) as attendance_rate,
        COALESCE(AVG(sas.points), 0) as performance
    FROM section_subjects ss
    JOIN sections sec ON ss.section_id = sec.section_id
    JOIN subjects subj ON ss.subject_id = subj.id
    JOIN student_sections st_sec ON sec.section_id = st_sec.section_id
    JOIN student s ON st_sec.student_id = s.student_id
    LEFT JOIN attendance a ON s.student_id = a.student_id 
        AND a.section_subject_id = ss.id
    LEFT JOIN student_activity_submissions sas ON s.student_id = sas.student_id 
        AND sas.activity_id IN (SELECT activity_id FROM activities WHERE section_subject_id = ss.id)
    WHERE ss.teacher_id = ?
    AND ss.status = 'active'
    AND st_sec.status = 'active'";

// Add section filter only if a specific section is selected
if ($selected_section != 'All Classes') {
    $section_parts = explode(' - ', $selected_section);
    if (count($section_parts) == 2) {
        $section_name = trim($section_parts[0]);
        $subject_code = trim($section_parts[1]);
        $students_query .= " AND sec.section_name = ? AND subj.subject_code = ?";
    }
}

$students_query .= "
    GROUP BY 
        s.student_id, 
        ss.id,
        s.firstname, 
        s.lastname, 
        s.email,
        s.lrn,
        sec.section_name,
        subj.subject_name,
        subj.subject_code,
        st_sec.created_at
    ORDER BY sec.section_name, s.lastname, s.firstname";

// Prepare and execute the query based on whether a section is selected
if ($selected_section != 'All Classes') {
    $stmt = $db->prepare($students_query);
    $stmt->bind_param("iss", $teacher_id, $section_name, $subject_code);
} else {
    $stmt = $db->prepare($students_query);
    $stmt->bind_param("i", $teacher_id);
}

// Debug information
echo "<!-- Selected section: " . $selected_section . " -->";
if ($selected_section != 'All Classes') {
    echo "<!-- Section name: " . $section_name . " -->";
    echo "<!-- Subject code: " . $subject_code . " -->";
}

if (!$stmt->execute()) {
    echo "<!-- Query execution failed: " . $db->error . " -->";
    die("Query failed: " . $db->error);
}

$result = $stmt->get_result();
if (!$result) {
    echo "<!-- Result fetch failed: " . $db->error . " -->";
    die("Result fetch failed: " . $db->error);
}

$students = $result->fetch_all(MYSQLI_ASSOC);
echo "<!-- Number of students found: " . count($students) . " -->";

// More debug information
echo "<!-- SQL Query: " . str_replace(array("\r", "\n"), ' ', $students_query) . " -->";

// Get classes for filter dropdown
$classes_query = "SELECT DISTINCT 
    ss.id as class_id,
    CONCAT(sec.section_name, ' - ', sub.subject_code) as section_name
FROM section_subjects ss
JOIN sections sec ON ss.section_id = sec.section_id
JOIN subjects sub ON ss.subject_id = sub.id
WHERE ss.teacher_id = ? 
AND ss.status = 'active'
ORDER BY sec.section_name";

$stmt = $db->prepare($classes_query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$classes_result = $stmt->get_result();
$classes = $classes_result->fetch_all(MYSQLI_ASSOC);

// Add this where you generate your dropdown
$sections_query = "
    SELECT DISTINCT 
        sec.section_name, 
        subj.subject_code
    FROM section_subjects ss
    JOIN sections sec ON ss.section_id = sec.section_id
    JOIN subjects subj ON ss.subject_id = subj.id
    WHERE ss.teacher_id = ?
    AND ss.status = 'active'
    ORDER BY sec.section_name";

$stmt = $db->prepare($sections_query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$sections = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students - Gov D.M. Camerino</title>
    
    <!-- External CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link rel="icon" href="../images/light-logo.png">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/dashboard-shared.css">
    <link rel="stylesheet" href="css/manage-students.css">
</head>
<body>
    <?php include 'includes/navigation.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="content-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1>Manage Students</h1>
                        <p>View and manage your students' information</p>
                    </div>
                    <div class="header-actions">
                        <select class="form-control mr-2" id="classFilter">
                            <option value="">All Classes</option>
                            <?php foreach ($classes as $class): ?>
                                <option value="<?php echo htmlspecialchars($class['section_name']); ?>">
                                    <?php echo htmlspecialchars($class['section_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button class="btn btn-primary" onclick="exportToExcel()">
                            <i class="fas fa-download"></i> Export to Excel
                        </button>
                    </div>
                </div>
            </div>

            <!-- Students Table -->
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Enrolled Students</h4>
                </div>
                <div class="card-body">
                    <table id="studentsTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Subject</th>
                                <th>Section</th>
                                <th>Enrollment Date</th>
                                <th>Attendance</th>
                                <th>Performance</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td data-label="Student">
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo $student['profile_image'] ?? '../images/default-avatar.png'; ?>" 
                                                 class="student-avatar mr-2" 
                                                 alt="Profile Image">
                                            <div>
                                                <div class="font-weight-bold">
                                                    <?php echo htmlspecialchars($student['firstname'] . ' ' . $student['lastname']); ?>
                                                </div>
                                                <small class="text-muted"><?php echo htmlspecialchars($student['email']); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-label="Subject">
                                        <span class="badge badge-info">
                                            <?php echo htmlspecialchars($student['subject_code']); ?>
                                        </span>
                                        <div class="small">
                                            <?php echo htmlspecialchars($student['subject_name']); ?>
                                        </div>
                                    </td>
                                    <td data-label="Section"><?php echo htmlspecialchars($student['section_name']); ?></td>
                                    <td data-label="Enrollment Date"><?php echo date('M d, Y', strtotime($student['enrollment_date'])); ?></td>
                                    <td data-label="Attendance">
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar bg-success" 
                                                 style="width: <?php echo $student['attendance_rate']; ?>%"
                                                 title="<?php echo number_format($student['attendance_rate'], 1); ?>%">
                                            </div>
                                        </div>
                                        <small class="text-muted">
                                            <?php echo number_format($student['attendance_rate'], 1); ?>%
                                        </small>
                                    </td>
                                    <td data-label="Performance">
                                        <?php 
                                            $performanceClass = $student['performance'] >= 85 ? 'success' : 
                                                ($student['performance'] >= 75 ? 'warning' : 'danger');
                                        ?>
                                        <span class="badge badge-<?php echo $performanceClass; ?>">
                                            <?php echo number_format($student['performance'], 1); ?>%
                                        </span>
                                    </td>
                                    <td data-label="Actions">
                                        <div class="btn-group">
                                            <button type="button" 
                                                    class="btn btn-sm btn-info" 
                                                    onclick="viewStudentDetails(<?php echo $student['student_id']; ?>)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-sm btn-primary" 
                                                    onclick="editStudent(<?php echo $student['student_id']; ?>)"
                                                    data-student-id="<?php echo $student['student_id']; ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Details Modal -->
    <div class="modal fade" id="studentDetailsModal" tabindex="-1" role="dialog" aria-labelledby="studentDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="studentDetailsModalLabel">Student Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Content will be loaded dynamically -->
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
    $(document).ready(function() {
        // Initialize DataTable
        const table = $('#studentsTable').DataTable({
            order: [[2, 'asc'], [0, 'asc']], // Sort by section then name
            pageLength: 10,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search students..."
            }
        });

        // Class filter
        $('#classFilter').on('change', function() {
            const selectedValue = $(this).val();
            if (selectedValue) {
                window.location.href = 'manage_students.php?section=' + encodeURIComponent(selectedValue);
            } else {
                window.location.href = 'manage_students.php';
            }
        });

        // Set the selected value in the dropdown
        const urlParams = new URLSearchParams(window.location.search);
        const sectionParam = urlParams.get('section');
        if (sectionParam) {
            $('#classFilter').val(decodeURIComponent(sectionParam));
        }

        // Initialize edit buttons
        $('.edit-btn').on('click', function() {
            const studentId = $(this).data('student-id');
            editStudent(studentId);
        });
    });

    // Function to generate student details HTML
    function generateStudentDetailsHtml(student) {
        return `
            <div class="student-details">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Name:</strong> ${student.firstname} ${student.lastname}</p>
                        <p><strong>LRN:</strong> ${student.lrn}</p>
                        <p><strong>Email:</strong> ${student.email || 'Not provided'}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Contact:</strong> ${student.contact_number || 'Not provided'}</p>
                        <p><strong>Gender:</strong> ${student.gender}</p>
                    </div>
                </div>
            </div>
        `;
    }

    // Function to view student details
    function viewStudentDetails(studentId) {
        $.ajax({
            url: 'get_student_details.php',
            method: 'GET',
            data: { student_id: studentId },
            success: function(response) {
                try {
                    const data = JSON.parse(response);
                    if (data.success) {
                        $('#studentDetailsModal .modal-body').html(generateStudentDetailsHtml(data.data));
                        $('#studentDetailsModal').modal('show');
                    } else {
                        Swal.fire('Error', data.message || 'Failed to load student details', 'error');
                    }
                } catch (e) {
                    console.error('Parse error:', e);
                    Swal.fire('Error', 'Invalid server response', 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Failed to load student details', 'error');
            }
        });
    }

    // Function to edit student
    function editStudent(studentId) {
        // Show loading state
        Swal.fire({
            title: 'Loading...',
            text: 'Fetching student details',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Fetch student details
        $.ajax({
            url: 'get_student_details.php',
            method: 'GET',
            data: { student_id: studentId },
            success: function(response) {
                try {
                    const data = JSON.parse(response);
                    if (data.success) {
                        showEditModal(data.data);
                        Swal.close();
                    } else {
                        Swal.fire('Error', data.message || 'Failed to load student details', 'error');
                    }
                } catch (e) {
                    console.error('Parse error:', e);
                    Swal.fire('Error', 'Invalid server response', 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Failed to fetch student details', 'error');
            }
        });
    }

    // Function to show edit modal
    function showEditModal(student) {
        // Remove existing modal if any
        $('#editStudentModal').remove();

        // Create modal HTML
        const modalHtml = `
            <div class="modal fade" id="editStudentModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Student</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <form id="editStudentForm">
                                <input type="hidden" name="student_id" value="${student.student_id}">
                                <div class="form-group">
                                    <label>First Name</label>
                                    <input type="text" class="form-control" name="firstname" 
                                           value="${student.firstname}" required>
                                </div>
                                <div class="form-group">
                                    <label>Last Name</label>
                                    <input type="text" class="form-control" name="lastname" 
                                           value="${student.lastname}" required>
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" class="form-control" name="email" 
                                           value="${student.email || ''}" required>
                                </div>
                                <div class="form-group">
                                    <label>Contact Number</label>
                                    <input type="text" class="form-control" name="contact_number" 
                                           value="${student.contact_number || ''}">
                                </div>
                                <div class="form-group">
                                    <label>Gender</label>
                                    <select name="gender" class="form-control" required>
                                        <option value="Male" ${student.gender === 'Male' ? 'selected' : ''}>Male</option>
                                        <option value="Female" ${student.gender === 'Female' ? 'selected' : ''}>Female</option>
                                    </select>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" onclick="saveStudentChanges()">Save Changes</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Add modal to body and show it
        $('body').append(modalHtml);
        $('#editStudentModal').modal('show');
    }

    // Function to save student changes
    function saveStudentChanges() {
        const formData = new FormData(document.getElementById('editStudentForm'));
        const data = Object.fromEntries(formData.entries());

        $.ajax({
            url: 'handlers/update_student.php',
            method: 'POST',
            data: data,
            success: function(response) {
                try {
                    const result = typeof response === 'string' ? JSON.parse(response) : response;
                    if (result.success) {
                        Swal.fire({
                            title: 'Success',
                            text: 'Student information updated successfully',
                            icon: 'success'
                        }).then(() => {
                            $('#editStudentModal').modal('hide');
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', result.message || 'Failed to update student', 'error');
                    }
                } catch (e) {
                    console.error('Parse error:', e);
                    Swal.fire('Error', 'Invalid server response', 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Failed to update student information', 'error');
            }
        });
    }

    function viewStudentProgress(studentId) {
        window.location.href = `student_progress.php?student_id=${studentId}`;
    }

    function exportToExcel() {
        // Get the table data
        const table = document.getElementById('studentsTable');
        let data = [];
        
        // Get headers
        const headers = [];
        table.querySelectorAll('thead th').forEach(th => {
            headers.push(th.textContent.trim());
        });
        data.push(headers);
        
        // Get row data
        table.querySelectorAll('tbody tr').forEach(tr => {
            const rowData = [];
            tr.querySelectorAll('td').forEach((td, index) => {
                // Handle different column types
                switch(index) {
                    case 0: // Student column
                        const name = td.querySelector('.font-weight-bold').textContent.trim();
                        const email = td.querySelector('.text-muted').textContent.trim();
                        rowData.push(`${name} (${email})`);
                        break;
                    case 4: // Attendance column
                        const attendance = td.querySelector('.text-muted').textContent.trim();
                        rowData.push(attendance);
                        break;
                    case 5: // Performance column
                        const performance = td.querySelector('.badge').textContent.trim();
                        rowData.push(performance);
                        break;
                    default: // Other columns
                        rowData.push(td.textContent.trim());
                }
            });
            data.push(rowData);
        });
        
        // Create CSV content
        const csvContent = data.map(row => row.join(',')).join('\n');
        
        // Create blob and download link
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        
        // Set filename with current date
        const date = new Date().toISOString().slice(0,10);
        const filename = `students_report_${date}.csv`;
        
        // Trigger download
        link.setAttribute('href', url);
        link.setAttribute('download', filename);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        // Show success message
        Swal.fire({
            title: 'Success!',
            text: 'The student data has been exported successfully.',
            icon: 'success',
            timer: 2000,
            showConfirmButton: false
        });
    }
    </script>
</body>
</html>
