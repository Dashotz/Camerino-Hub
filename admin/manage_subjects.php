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
    <title>Manage Subjects - Admin Dashboard</title>
    
    <!-- External CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/dashboard-shared.css">
    <style>
        /* Your existing styles */
        /* Stats Cards Styles */
        .stats-cards .card {
            border: none;
            border-radius: 0.35rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        .stats-cards .card .card-body {
            padding: 1.25rem;
        }

        .stats-cards .border-left-primary {
            border-left: 0.25rem solid #4e73df;
        }

        /* Stats Cards Styling */
        .stats-card {
            transition: all 0.3s ease;
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .stats-card .card-body {
            padding: 1.5rem;
        }

        .stats-card .icon-container {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            background-color: rgba(52, 152, 219, 0.1);
        }

        .stats-card i {
            font-size: 1.5rem;
        }

        .stats-card:nth-child(1) .icon-container {
            background-color: rgba(52, 152, 219, 0.1);
        }
        .stats-card:nth-child(1) i {
            color: #3498db;
        }

        .stats-card:nth-child(2) .icon-container {
            background-color: rgba(46, 204, 113, 0.1);
        }
        .stats-card:nth-child(2) i {
            color: #2ecc71;
        }

        .stats-card:nth-child(3) .icon-container {
            background-color: rgba(241, 196, 15, 0.1);
        }
        .stats-card:nth-child(3) i {
            color: #f1c40f;
        }

        .stats-card:nth-child(4) .icon-container {
            background-color: rgba(231, 76, 60, 0.1);
        }
        .stats-card:nth-child(4) i {
            color: #e74c3c;
        }

        .stats-info h5 {
            color: #7f8c8d;
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .stats-info h3 {
            color: #2c3e50;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .stats-info small {
            color: #95a5a6;
            font-size: 0.8rem;
        }

        /* Loading Animation */
        @keyframes pulse {
            0% { opacity: 0.6; }
            50% { opacity: 1; }
            100% { opacity: 0.6; }
        }

        .stats-card h3:contains('Loading...') {
            animation: pulse 1.5s infinite;
            color: #95a5a6;
        }

        .welcome-section {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .stats-card {
            transition: all 0.3s ease;
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .stats-card .card-body {
            padding: 1.5rem;
        }

        .stats-card .icon-container {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            background-color: rgba(52, 152, 219, 0.1);
        }

        .stats-card i {
            font-size: 1.5rem;
        }

        .stats-card:nth-child(1) .icon-container {
            background-color: rgba(52, 152, 219, 0.1);
        }
        .stats-card:nth-child(1) i { color: #3498db; }

        .stats-card:nth-child(2) .icon-container {
            background-color: rgba(46, 204, 113, 0.1);
        }
        .stats-card:nth-child(2) i { color: #2ecc71; }

        .stats-card:nth-child(3) .icon-container {
            background-color: rgba(241, 196, 15, 0.1);
        }
        .stats-card:nth-child(3) i { color: #f1c40f; }

        .stats-card:nth-child(4) .icon-container {
            background-color: rgba(231, 76, 60, 0.1);
        }
        .stats-card:nth-child(4) i { color: #e74c3c; }

        .stats-info h5 {
            color: #7f8c8d;
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .stats-info h3 {
            color: #2c3e50;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .stats-info small {
            color: #95a5a6;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    <?php include 'includes/navigation.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Welcome Section -->
            <div class="welcome-section">
                <h1>Manage Subjects</h1>
                <p>Add, edit, and manage school subjects</p>
            </div>

            <!-- Stats Cards -->
            <div class="row stats-cards">
                <div class="col-md-3 mb-3">
                    <div class="card stats-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon-container">
                                <i class="fas fa-book"></i>
                            </div>
                            <div class="stats-info">
                                <h5 class="card-title mb-0">Total Subjects</h5>
                                <h3 id="totalSubjects" class="mb-0">Loading...</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card stats-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon-container">
                                <i class="fas fa-chalkboard"></i>
                            </div>
                            <div class="stats-info">
                                <h5 class="card-title mb-0">Active Subjects</h5>
                                <h3 id="activeSubjects" class="mb-0">Loading...</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card stats-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon-container">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <div class="stats-info">
                                <h5 class="card-title mb-0">Assigned Teachers</h5>
                                <h3 id="assignedTeachers" class="mb-0">Loading...</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card stats-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon-container">
                                <i class="fas fa-archive"></i>
                            </div>
                            <div class="stats-info">
                                <h5 class="card-title mb-0">Archived Subjects</h5>
                                <h3 id="archivedSubjects" class="mb-0">Loading...</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Subjects Table Section -->
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Subject List</h5>
                    <button class="btn btn-primary" onclick="showAddSubjectModal()">
                        <i class="fas fa-plus"></i> Add New Subject
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="subjectTable" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Subject Code</th>
                                    <th>Subject Title</th>
                                    <th>Category</th>
                                    <th>Assigned Teachers</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Subject Modal -->
    <div class="modal fade" id="subjectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add New Subject</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="subjectForm">
                    <div class="modal-body">
                        <input type="hidden" name="subject_id" id="subject_id">
                        
                        <div class="form-group">
                            <label>Subject Code*</label>
                            <input type="text" class="form-control" name="subject_code" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Subject Title*</label>
                            <input type="text" class="form-control" name="subject_title" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Category*</label>
                            <select class="form-control" name="category" required>
                                <option value="">Select Category</option>
                                <option value="Core">Core Subject</option>
                                <option value="Major">Major Subject</option>
                                <option value="Minor">Minor Subject</option>
                                <option value="Elective">Elective</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Grade Level*</label>
                            <select class="form-control" name="grade_level" required>
                                <option value="">Select Grade Level</option>
                                <option value="7">Grade 7</option>
                                <option value="8">Grade 8</option>
                                <option value="9">Grade 9</option>
                                <option value="10">Grade 10</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Subject</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Subject Modal -->
    <div class="modal fade" id="viewSubjectModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Subject Details</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="subject-info"></div>
                    <div class="teacher-assignments mt-4">
                        <h6>Teacher Assignments</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Teacher</th>
                                        <th>Section</th>
                                        <th>Schedule</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="teacherAssignmentsList"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
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

    <!-- Your existing scripts here -->

    <script>
        function loadDashboardStats() {
            $.ajax({
                url: 'handlers/subject_handler.php',
                type: 'GET',
                data: { action: 'get_subject_stats' },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        const stats = response.data;
                        
                        // Update with animation
                        updateStatWithAnimation('#totalSubjects', stats.total_subjects || 0, 'Total');
                        updateStatWithAnimation('#activeSubjects', stats.active_subjects || 0, 'Active');
                        updateStatWithAnimation('#assignedTeachers', stats.assigned_teachers || 0, 'Teachers');
                        updateStatWithAnimation('#archivedSubjects', stats.archived_subjects || 0, 'Archived');
                        
                        // Add animation to cards
                        $('.stats-card').each(function(index) {
                            $(this).delay(100 * index).animate({
                                opacity: 1
                            }, 500);
                        });
                    } else {
                        showErrorState(response.message || 'Failed to load stats');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Stats loading error:', error);
                    showErrorState('Connection failed');
                }
            });
        }

        function updateStatWithAnimation(elementId, value, label) {
            const element = $(elementId);
            element.prop('Counter', 0).animate({
                Counter: parseInt(value)
            }, {
                duration: 1000,
                easing: 'swing',
                step: function(now) {
                    $(this).html(`
                        ${Math.ceil(now)}
                        <small class="text-muted d-block">${label}</small>
                    `);
                },
                complete: function() {
                    // Ensure final value is exact
                    $(this).html(`
                        ${value}
                        <small class="text-muted d-block">${label}</small>
                    `);
                }
            });
        }

        function showErrorState(message = 'Failed to load') {
            $('.stats-card h3').html(`
                <span class="text-danger">Error</span>
                <small class="text-muted d-block">${message}</small>
            `);
        }

        // Initialize on page load
        $(document).ready(function() {
            // Set initial state of cards
            $('.stats-card').css({
                opacity: 0
            });
            
            // Load stats
            loadDashboardStats();
            
            // Refresh stats every 30 seconds
            setInterval(loadDashboardStats, 30000);
        });

        // Initialize DataTable
        $(document).ready(function() {
            $('#subjectTable').DataTable({
                ajax: {
                    url: 'handlers/subject_handler.php',
                    type: 'GET',
                    data: { action: 'get_subjects' }
                },
                columns: [
                    { data: 'subject_code' },
                    { data: 'subject_title' },
                    { 
                        data: 'category',
                        render: function(data, type, row) {
                            const badges = {
                                'Core': 'primary',
                                'Major': 'success',
                                'Minor': 'info',
                                'Elective': 'warning'
                            };
                            return `<span class="badge badge-${badges[data] || 'secondary'}">${data}</span>`;
                        }
                    },
                    { 
                        data: 'teacher_count',
                        render: function(data) {
                            return `<span class="badge badge-info">${data} Teacher(s)</span>`;
                        }
                    },
                    {
                        data: 'status',
                        render: function(data) {
                            return `<span class="badge badge-${data === 'active' ? 'success' : 'danger'}">${data}</span>`;
                        }
                    },
                    {
                        data: null,
                        render: function(data) {
                            return `
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-info" onclick="viewSubject(${data.id})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-primary" onclick="editSubject(${data.id})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger" onclick="archiveSubject(${data.id})">
                                        <i class="fas fa-archive"></i>
                                    </button>
                                </div>`;
                        }
                    }
                ],
                order: [[0, 'asc']]
            });
        });

        // Form handling
        $('#subjectForm').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const subjectId = $('#subject_id').val();
            
            formData.append('action', subjectId ? 'edit_subject' : 'add_subject');
            
            $.ajax({
                url: 'handlers/subject_handler.php',
                type: 'POST',
                data: Object.fromEntries(formData),
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire('Success', response.message, 'success');
                        $('#subjectModal').modal('hide');
                        $('#subjectTable').DataTable().ajax.reload();
                        loadDashboardStats();
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                }
            });
        });

        function showAddSubjectModal() {
            $('#modalTitle').text('Add New Subject');
            $('#subjectForm')[0].reset();
            $('#subject_id').val('');
            $('#subjectModal').modal('show');
        }

        function editSubject(id) {
            $.ajax({
                url: 'handlers/subject_handler.php',
                type: 'GET',
                data: { action: 'get_subject_details', id: id },
                success: function(response) {
                    if (response.status === 'success') {
                        const subject = response.data;
                        $('#modalTitle').text('Edit Subject');
                        $('#subject_id').val(subject.id);
                        $('input[name="subject_code"]').val(subject.subject_code);
                        $('input[name="subject_title"]').val(subject.subject_title);
                        $('select[name="category"]').val(subject.category);
                        $('select[name="grade_level"]').val(subject.grade_level);
                        $('textarea[name="description"]').val(subject.description);
                        $('#subjectModal').modal('show');
                    }
                }
            });
        }

        function viewSubject(id) {
            $.ajax({
                url: 'handlers/subject_handler.php',
                type: 'GET',
                data: { action: 'get_subject_details', id: id },
                success: function(response) {
                    if (response.status === 'success') {
                        const subject = response.data;
                        $('.subject-info').html(`
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Subject Code:</strong> ${subject.subject_code}</p>
                                    <p><strong>Subject Title:</strong> ${subject.subject_title}</p>
                                    <p><strong>Category:</strong> ${subject.category}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Grade Level:</strong> ${subject.grade_level}</p>
                                    <p><strong>Status:</strong> ${subject.status}</p>
                                    <p><strong>Description:</strong> ${subject.description || 'N/A'}</p>
                                </div>
                            </div>
                        `);
                        loadTeacherAssignments(id);
                        $('#viewSubjectModal').modal('show');
                    }
                }
            });
        }

        function loadTeacherAssignments(subjectId) {
            $.ajax({
                url: 'handlers/subject_handler.php',
                type: 'GET',
                data: { action: 'get_subject_teachers', id: subjectId },
                success: function(response) {
                    if (response.status === 'success') {
                        const assignments = response.data;
                        let html = '';
                        assignments.forEach(assignment => {
                            html += `
                                <tr>
                                    <td>${assignment.teacher_name}</td>
                                    <td>${assignment.section_name}</td>
                                    <td>${assignment.schedule_day} ${assignment.schedule_time}</td>
                                    <td><span class="badge badge-${assignment.status === 'active' ? 'success' : 'warning'}">${assignment.status}</span></td>
                                </tr>
                            `;
                        });
                        $('#teacherAssignmentsList').html(html || '<tr><td colspan="4" class="text-center">No assignments found</td></tr>');
                    }
                }
            });
        }

        function archiveSubject(id) {
            Swal.fire({
                title: 'Archive Subject?',
                text: "This will archive the subject and remove it from active assignments",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, archive it'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'handlers/subject_handler.php',
                        type: 'POST',
                        data: { action: 'archive_subject', id: id },
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire('Success', response.message, 'success');
                                $('#subjectTable').DataTable().ajax.reload();
                                loadDashboardStats();
                            } else {
                                Swal.fire('Error', response.message, 'error');
                            }
                        }
                    });
                }
            });
        }
    </script>
</body>
</html>