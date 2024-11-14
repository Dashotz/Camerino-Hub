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
    <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/select/1.3.3/css/select.dataTables.min.css" rel="stylesheet">
    
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

        /* DataTable Styling */
        .dataTables_wrapper .dataTables_length select {
            min-width: 60px;
            padding: 4px 8px;
        }
        
        .dataTables_wrapper .dataTables_filter input {
            margin-left: 8px;
            padding: 4px 8px;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.5em 1em;
            margin: 0 2px;
        }
        
        .dataTables_wrapper .dataTables_info {
            padding-top: 1em;
        }
        
        .dt-buttons {
            margin-bottom: 1em;
        }
        
        .dt-button {
            padding: 0.3em 1em;
            margin-right: 0.5em;
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
                    <h4 class="mb-0">Manage Subjects</h4>
                    <a href="add_subject.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Subject
                    </a>
                </div>
                <div class="card-body">
                    <!-- Add tabs -->
                    <ul class="nav nav-tabs mb-3" id="subjectTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="active-tab" data-toggle="tab" href="#active" role="tab">
                                Active Subjects
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="archived-tab" data-toggle="tab" href="#archived" role="tab">
                                Archived Subjects
                            </a>
                        </li>
                    </ul>
                    
                    <!-- Tab content -->
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="active" role="tabpanel">
                            <div class="table-responsive">
                                <table id="activeSubjectTable" class="table table-striped">
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
                        <div class="tab-pane fade" id="archived" role="tabpanel">
                            <div class="table-responsive">
                                <table id="archivedSubjectTable" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Subject Code</th>
                                            <th>Subject Title</th>
                                            <th>Category</th>
                                            <th>Updated Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Subject Modal -->
    <div class="modal fade" id="editSubjectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Subject</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form action="handlers/subject_handler.php" method="POST">
                    <input type="hidden" name="action" value="edit_subject">
                    <input type="hidden" name="subject_id" value="<?php echo isset($_GET['edit_id']) ? htmlspecialchars($_GET['edit_id']) : ''; ?>">
                    
                    <div class="modal-body">
                        <?php
                        if (isset($_GET['edit_id'])) {
                            $edit_id = $_GET['edit_id'];
                            $query = "SELECT * FROM subjects WHERE id = ?";
                            $stmt = $db->prepare($query);
                            $stmt->bind_param("i", $edit_id);
                            $stmt->execute();
                            $subject = $stmt->get_result()->fetch_assoc();
                            if ($subject) {
                        ?>
                            <div class="form-group">
                                <label>Subject Code*</label>
                                <input type="text" class="form-control" name="subject_code" value="<?php echo htmlspecialchars($subject['subject_code']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label>Subject Title*</label>
                                <input type="text" class="form-control" name="subject_title" value="<?php echo htmlspecialchars($subject['subject_title']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label>Category*</label>
                                <select class="form-control" name="category" required>
                                    <option value="Core" <?php echo $subject['category'] == 'Core' ? 'selected' : ''; ?>>Core Subject</option>
                                    <option value="Major" <?php echo $subject['category'] == 'Major' ? 'selected' : ''; ?>>Major Subject</option>
                                    <option value="Minor" <?php echo $subject['category'] == 'Minor' ? 'selected' : ''; ?>>Minor Subject</option>
                                    <option value="Elective" <?php echo $subject['category'] == 'Elective' ? 'selected' : ''; ?>>Elective</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Description</label>
                                <textarea class="form-control" name="description" rows="3"><?php echo htmlspecialchars($subject['description'] ?? ''); ?></textarea>
                            </div>
                        <?php 
                            }
                        }
                        ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Subject</button>
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
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

    <!-- Your existing scripts here -->

    <script>
        $(document).ready(function() {
            // Initialize active subjects table
            const activeTable = $('#activeSubjectTable').DataTable({
                ajax: {
                    url: 'handlers/subject_handler.php',
                    type: 'GET',
                    data: { 
                        action: 'get_subjects',
                        status: 'active'
                    }
                },
                processing: true,
                serverSide: false,
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                columns: [
                    { data: 'subject_code' },
                    { data: 'subject_title' },
                    { 
                        data: 'category',
                        render: function(data) {
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
                        data: 'assigned_teachers',
                        render: function(data) {
                            return `<span class="badge badge-info">${data} Teacher(s)</span>`;
                        }
                    },
                    {
                        data: 'status',
                        render: function(data) {
                            return `<span class="badge badge-success">Active</span>`;
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return `
                                <div class="btn-group">
                                    <a href="view_subject.php?id=${row.id}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="edit_subject.php?id=${row.id}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button onclick="archiveSubject(${row.id})" class="btn btn-danger btn-sm">
                                        <i class="fas fa-archive"></i>
                                    </button>
                                </div>
                            `;
                        }
                    }
                ],
                order: [[0, 'asc']],
                dom: '<"top"<"d-flex justify-content-between"<"length-menu"l><"search-box"f>>>rt<"bottom"<"d-flex justify-content-between"<"showing-entries"i><"pagination-wrapper"p>>>',
                language: {
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    search: "Search:",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    },
                    emptyTable: "No subjects found",
                    zeroRecords: "No matching records found"
                },
                drawCallback: function(settings) {
                    // Refresh the table when length menu changes
                    $('.dataTables_length select').on('change', function() {
                        $(this).closest('.dataTables_wrapper').find('.dataTable').DataTable().ajax.reload();
                    });
                }
            });

            // Initialize archived subjects table
            const archivedTable = $('#archivedSubjectTable').DataTable({
                ajax: {
                    url: 'handlers/subject_handler.php',
                    type: 'GET',
                    data: { 
                        action: 'get_subjects',
                        status: 'inactive'
                    },
                    dataSrc: function(json) {
                        return json.data || [];
                    }
                },
                processing: true,
                serverSide: false,
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                columns: [
                    { data: 'subject_code' },
                    { data: 'subject_title' },
                    { 
                        data: 'category',
                        render: function(data) {
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
                        data: 'updated_at',
                        render: function(data) {
                            return moment(data).format('MMM DD, YYYY');
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return `
                                <div class="btn-group">
                                    <a href="view_subject.php?id=${row.id}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button onclick="restoreSubject(${row.id})" class="btn btn-success btn-sm">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                    <button onclick="deleteSubject(${row.id})" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            `;
                        }
                    }
                ],
                order: [[0, 'asc']],
                dom: '<"top"<"d-flex justify-content-between"<"length-menu"l><"search-box"f>>>rt<"bottom"<"d-flex justify-content-between"<"showing-entries"i><"pagination-wrapper"p>>>',
                language: {
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    search: "Search:",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    },
                    emptyTable: "No archived subjects found",
                    zeroRecords: "No matching records found"
                }
            });

            // Add restore function
            window.restoreSubject = function(id) {
                Swal.fire({
                    title: 'Restore Subject?',
                    text: "This will make the subject active again",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, restore it'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading state
                        Swal.fire({
                            title: 'Restoring...',
                            text: 'Please wait',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            willOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: 'handlers/subject_handler.php',
                            type: 'POST',
                            dataType: 'json',
                            data: { 
                                action: 'restore_subject', 
                                id: id 
                            },
                            success: function(response) {
                                if (response.status === 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success!',
                                        text: response.message,
                                        showConfirmButton: false,
                                        timer: 1500
                                    }).then(() => {
                                        // Refresh both tables
                                        $('#activeSubjectTable').DataTable().ajax.reload();
                                        $('#archivedSubjectTable').DataTable().ajax.reload();
                                        // Refresh stats
                                        loadDashboardStats();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: response.message || 'Failed to restore subject'
                                    });
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('Restore error:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Failed to connect to server. Please try again.'
                                });
                            }
                        });
                    }
                });
            };

            // Add delete function
            function deleteSubject(id) {
                Swal.fire({
                    title: 'Delete Subject?',
                    text: "This action cannot be undone",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'handlers/subject_handler.php',
                            type: 'POST',
                            data: { 
                                action: 'delete_subject', 
                                id: id 
                            },
                            success: function(response) {
                                if (response.status === 'success') {
                                    Swal.fire('Deleted!', response.message, 'success');
                                    archivedTable.ajax.reload();
                                    loadDashboardStats();
                                } else {
                                    Swal.fire('Error', response.message, 'error');
                                }
                            }
                        });
                    }
                });
            }

            // Refresh tables when switching tabs
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
                if (e.target.id === 'archived-tab') {
                    archivedTable.ajax.reload();
                } else {
                    activeTable.ajax.reload();
                }
            });

            // Load initial stats
            loadDashboardStats();
        });

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

        function archiveSubject(id) {
            Swal.fire({
                title: 'Archive Subject?',
                text: "This will archive the subject and all its assignments",
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
                                // Update both active and archived tables
                                $('#activeSubjectTable').DataTable().ajax.reload();
                                $('#archivedSubjectTable').DataTable().ajax.reload();
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