<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$db = new DbConnector();
$admin_id = $_SESSION['admin_id'];

// Get admin info
$query = "SELECT * FROM admin WHERE admin_id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Teachers - Admin Dashboard</title>
    
    <!-- External CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/dashboard-shared.css">
    <style>
        .welcome-section {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .stats-cards .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .stats-cards .card:hover {
            transform: translateY(-5px);
        }

        .stats-cards .card-body {
            padding: 1.5rem;
        }

        .stats-cards i {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #3498db;
        }

        .stats-cards h5 {
            color: #7f8c8d;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .stats-cards h3 {
            color: #2c3e50;
            font-size: 1.8rem;
            margin-bottom: 0;
        }

        .stats-cards small {
            font-size: 0.8rem;
            opacity: 0.7;
        }

        /* Card-specific colors */
        .stats-cards .card:nth-child(1) i { color: #3498db; }
        .stats-cards .card:nth-child(2) i { color: #2ecc71; }
        .stats-cards .card:nth-child(3) i { color: #f1c40f; }
        .stats-cards .card:nth-child(4) i { color: #e74c3c; }

        .teacher-assignments {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .teacher-info {
            display: flex;
            flex-direction: column;
        }

        .teacher-name {
            font-weight: bold;
            color: #2c3e50;
        }

        .teacher-dept {
            font-size: 0.9em;
            color: #7f8c8d;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
        }

        .action-buttons button {
            padding: 5px 10px;
            border-radius: 5px;
        }

        .welcome-section .btn-primary {
            padding: 8px 20px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .welcome-section .btn-primary i {
            font-size: 14px;
        }

        /* Add a hover effect */
        .welcome-section .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
    </style>
</head>
<body>
    <?php include 'includes/navigation.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <!-- Welcome Section -->
            <div class="welcome-section">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1>Manage Teachers</h1>
                        <p>Assign and manage teacher schedules and subjects</p>
                    </div>
                    <div>
                        <a href="add_teacher.php" class="btn btn-primary add-new-teacher">
                            <i class="fas fa-plus"></i> Add New Teacher
                        </a>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row stats-cards">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <i class="fas fa-chalkboard-teacher"></i>
                            <h5>Total Teachers</h5>
                            <h3 id="totalTeachers">Loading...</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <i class="fas fa-book"></i>
                            <h5>Assigned Subjects</h5>
                            <h3 id="assignedSubjects">Loading...</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <i class="fas fa-users"></i>
                            <h5>Active Sections</h5>
                            <h3 id="activeSections">Loading...</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <i class="fas fa-building"></i>
                            <h5>Departments</h5>
                            <h3 id="totalDepartments">Loading...</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Teacher Assignments Table -->
            <div class="teacher-assignments">
                <div class="header-actions">
                    <h2>Teacher Assignments</h2>
                    <button type="button" class="btn btn-primary" id="assignTeacherBtn">
                        <i class="fas fa-plus"></i> Assign Teacher
                    </button>
                </div>
                
                <div class="table-responsive mt-3">
                    <table id="assignmentsTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Teacher</th>
                                <th>Subject</th>
                                <th>Section</th>
                                <th>Schedule</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="assignmentsTableBody">
                            <!-- Data will be loaded dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Assign Teacher Modal -->
    <div class="modal fade" id="assignTeacherModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assign Teacher to Subject and Sections</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form action="handlers/teacher_handler.php" method="POST">
                    <input type="hidden" name="action" value="assign_teacher">
                    <div class="modal-body">
                        <!-- Teacher Selection -->
                        <div class="form-group">
                            <label>Select Teacher*</label>
                            <select name="teacher_id" class="form-control" required>
                                <option value="">Select Teacher</option>
                                <?php
                                $teachers_query = "SELECT teacher_id, firstname, lastname, department 
                                                FROM teacher 
                                                WHERE status = 'active' 
                                                ORDER BY lastname, firstname";
                                $teachers = $db->query($teachers_query);
                                while ($teacher = $teachers->fetch_assoc()) {
                                    echo "<option value='{$teacher['teacher_id']}'>{$teacher['lastname']}, {$teacher['firstname']} - {$teacher['department']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Subject Selection -->
                        <div class="form-group">
                            <label>Select Subject*</label>
                            <select name="subject_id" class="form-control" required>
                                <option value="">Select Subject</option>
                                <?php
                                $subjects_query = "SELECT id, subject_code, subject_title 
                                                FROM subjects 
                                                WHERE status = 'active' 
                                                ORDER BY subject_code";
                                $subjects = $db->query($subjects_query);
                                while ($subject = $subjects->fetch_assoc()) {
                                    echo "<option value='{$subject['id']}'>{$subject['subject_code']} - {$subject['subject_title']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Section Selection -->
                        <div class="form-group">
                            <label>Select Sections* (Hold Ctrl/Cmd to select multiple)</label>
                            <select name="section_ids[]" class="form-control" multiple required>
                                <?php
                                $sections_query = "SELECT section_id, section_name, grade_level 
                                                FROM sections 
                                                WHERE status = 'active' 
                                                ORDER BY grade_level, section_name";
                                $sections = $db->query($sections_query);
                                while ($section = $sections->fetch_assoc()) {
                                    echo "<option value='{$section['section_id']}'>Grade {$section['grade_level']} - {$section['section_name']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Schedule -->
                        <div class="form-group">
                            <label>Schedule Day*</label>
                            <select name="schedule_day" class="form-control" required>
                                <option value="">Select Day</option>
                                <option value="Monday">Monday</option>
                                <option value="Tuesday">Tuesday</option>
                                <option value="Wednesday">Wednesday</option>
                                <option value="Thursday">Thursday</option>
                                <option value="Friday">Friday</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Schedule Time*</label>
                            <input type="time" name="schedule_time" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Assign Teacher</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Include your existing JavaScript code here -->
    <script>
        // Define showAssignModal globally
        function showAssignModal() {
            // Show loading state
            Swal.fire({
                title: 'Loading...',
                text: 'Please wait while we prepare the form',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Load data and show modal
            $('#assignTeacherModal').modal('show');
            Swal.close();
        }

        $(document).ready(function() {
            loadDashboardStats();
            loadTeacherAssignments();

            // Handle Add New Teacher button click
            $('.add-new-teacher').on('click', function(e) {
                window.location.href = 'add_teacher.php';
            });

            // Handle Assign Teacher form submission
            $('form[action="handlers/teacher_handler.php"]').on('submit', function(e) {
                e.preventDefault();
                
                // Get selected sections
                const selectedSections = $('select[name="section_ids[]"]').val();
                if (!selectedSections || selectedSections.length === 0) {
                    Swal.fire('Error', 'Please select at least one section', 'error');
                    return;
                }

                const formData = new FormData(this);
                
                $.ajax({
                    url: 'handlers/teacher_handler.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        try {
                            // Parse response if it's a string
                            const result = typeof response === 'string' ? JSON.parse(response) : response;
                            
                            if (result.status === 'success') {
                                $('#assignTeacherModal').modal('hide');
                                Swal.fire('Success', 'Teacher assigned successfully', 'success')
                                    .then(() => {
                                        location.reload();
                                    });
                            } else {
                                Swal.fire('Error', result.message || 'Failed to assign teacher', 'error');
                            }
                        } catch (e) {
                            console.error('Response parsing error:', e);
                            Swal.fire('Error', 'Invalid server response', 'error');
                        }
                    },
                    error: function(xhr) {
                        console.error('AJAX error:', xhr);
                        Swal.fire('Error', 'Failed to process request', 'error');
                    }
                });
            });

            // Initialize modal events
            $('#assignTeacherModal').on('show.bs.modal', function() {
                // Clear previous selections when modal opens
                $(this).find('select').val('');
                $(this).find('input[type="time"]').val('');
            });

            // Add click handler for Assign Teacher button
            $('#assignTeacherBtn').on('click', function() {
                showAssignModal();
            });
        });

        // Keep your existing functions
        function loadDashboardStats() {
            $.ajax({
                url: 'handlers/teacher_handler.php',
                type: 'GET',
                data: { action: 'get_dashboard_stats' },
                success: function(response) {
                    if (response.status === 'success') {
                        const stats = response.data;
                        
                        // Update Total Teachers
                        $('#totalTeachers').html(`
                            ${stats.teachers.total}
                            <small class="text-muted d-block">${stats.teachers.active} Active</small>
                        `);

                        // Update Assigned Subjects
                        $('#assignedSubjects').html(`
                            ${stats.subjects.assigned}
                            <small class="text-muted d-block">Subjects</small>
                        `);

                        // Update Active Sections
                        $('#activeSections').html(`
                            ${stats.sections.active}
                            <small class="text-muted d-block">Sections</small>
                        `);

                        // Update Departments
                        $('#totalDepartments').html(`
                            ${stats.departments.total}
                            <small class="text-muted d-block">Active</small>
                        `);
                    } else {
                        console.error('Failed to load stats:', response.message);
                        // Show error state
                        $('.stats-cards h3').text('Error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Ajax error:', error);
                    // Show error state
                    $('.stats-cards h3').text('Error');
                }
            });
        }

        function loadTeacherAssignments() {
            if ($.fn.DataTable.isDataTable('#assignmentsTable')) {
                $('#assignmentsTable').DataTable().destroy();
            }

            $('#assignmentsTable').DataTable({
                ajax: {
                    url: 'handlers/teacher_handler.php',
                    type: 'GET',
                    data: { action: 'get_teacher_assignments' },
                    dataSrc: 'data'
                },
                columns: [
                    { 
                        data: null,
                        render: function(data) {
                            return `<div class="teacher-info">
                                <span class="teacher-name">${data.teacher_name}</span>
                                <span class="teacher-dept">${data.department}</span>
                            </div>`;
                        }
                    },
                    { 
                        data: null,
                        render: function(data) {
                            return `<div class="subject-info">
                                <span class="subject-code">${data.subject_code}</span>
                                <span class="subject-name">${data.subject_title}</span>
                            </div>`;
                        }
                    },
                    {
                        data: null,
                        render: function(data) {
                            return `${data.grade_level} - ${data.section_name}`;
                        }
                    },
                    {
                        data: null,
                        render: function(data) {
                            return `${data.schedule_day}<br>${data.schedule_time}`;
                        }
                    },
                    {
                        data: 'status',
                        render: function(data) {
                            return `<span class="badge badge-${data === 'active' ? 'success' : 'warning'}">${data}</span>`;
                        }
                    },
                    {
                        data: null,
                        render: function(data) {
                            return `
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-primary" onclick="editAssignment(${data.id})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-assignment" data-id="${data.id}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>`;
                        }
                    }
                ],
                order: [[0, 'asc']],
                responsive: true
            });
        }

        // Add this after your loadTeacherAssignments function
        function deleteAssignment(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This will remove the teacher's assignment",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'handlers/teacher_handler.php',
                        type: 'POST',
                        data: {
                            action: 'delete_teacher_assignment',
                            assignment_id: id
                        },
                        success: function(response) {
                            try {
                                const result = typeof response === 'string' ? JSON.parse(response) : response;
                                if (result.status === 'success') {
                                    Swal.fire(
                                        'Deleted!',
                                        'Assignment has been deleted.',
                                        'success'
                                    ).then(() => {
                                        // Reload the DataTable
                                        $('#assignmentsTable').DataTable().ajax.reload();
                                    });
                                } else {
                                    Swal.fire('Error', result.message || 'Failed to delete assignment', 'error');
                                }
                            } catch (e) {
                                console.error('Response parsing error:', e);
                                Swal.fire('Error', 'Invalid server response', 'error');
                            }
                        },
                        error: function(xhr) {
                            console.error('AJAX error:', xhr);
                            Swal.fire('Error', 'Failed to process request', 'error');
                        }
                    });
                }
            });
        }

        // Add click handler for delete buttons
        $(document).on('click', '.delete-assignment', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            deleteAssignment(id);
        });
    </script>
</body>
</html>