<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
    exit();
}

$db = new DbConnector();

// Get all departments
$dept_query = "SELECT * FROM departments WHERE status = 'active'";
$departments = $db->query($dept_query)->fetch_all(MYSQLI_ASSOC);

// Get all subjects
$subj_query = "SELECT * FROM subjects WHERE status = 'active'";
$subjects = $db->query($subj_query)->fetch_all(MYSQLI_ASSOC);

// Get all sections
$section_query = "SELECT * FROM sections WHERE status = 'active'";
$sections = $db->query($section_query)->fetch_all(MYSQLI_ASSOC);

// Get all teachers with their departments
$teacher_query = "
    SELECT t.*, d.department_name,
    GROUP_CONCAT(DISTINCT CASE WHEN ss.status = 'active' THEN s.subject_name END) as subjects,
    GROUP_CONCAT(DISTINCT CASE WHEN ss.status = 'active' THEN sec.section_name END) as sections
    FROM teacher t
    LEFT JOIN departments d ON t.department_id = d.department_id
    LEFT JOIN section_subjects ss ON t.teacher_id = ss.teacher_id
    LEFT JOIN subjects s ON ss.subject_id = s.id
    LEFT JOIN sections sec ON ss.section_id = sec.section_id
    WHERE t.status = 'active'
    GROUP BY t.teacher_id";
$teachers = $db->query($teacher_query)->fetch_all(MYSQLI_ASSOC);
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
    <link rel="icon" href="../images/light-logo.png">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/dashboard-shared.css">
    <style>
        .table-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .add-btn {
            background: #3498db;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .add-btn:hover {
            background: #2980b9;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.85em;
        }

        .status-active {
            background: #d4edda;
            color: #155724;
        }

        .action-btn {
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9em;
            margin: 0 2px;
        }

        .edit-btn {
            background: #2ecc71;
            color: white;
        }

        .archive-btn {
            background: #e74c3c;
            color: white;
        }

        .table td {
            vertical-align: middle !important;
        }

        /* DataTables Customization */
        .dataTables_wrapper .dataTables_length, 
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 15px;
        }

        .dataTables_length select {
            margin: 0 5px;
        }

        .dataTables_filter input {
            margin-left: 5px;
        }

        .page-item.active .page-link {
            background-color: #007bff;
            border-color: #007bff;
        }

        /* Add these styles to your existing CSS */
        .btn-danger.archive-btn {
            background-color: #dc3545;
            border-color: #dc3545;
            color: white;
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            line-height: 1.5;
            border-radius: 0.2rem;
            transition: all 0.15s ease-in-out;
        }

        .btn-danger.archive-btn:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }

        .btn-danger.archive-btn i {
            font-size: 0.875rem;
        }

        /* Disable button while processing */
        .btn-danger.archive-btn:disabled {
            opacity: 0.65;
            cursor: not-allowed;
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
            color: white;
            text-decoration: none;
        }

        .btn-secondary i {
            font-size: 0.9em;
        }

        .col.text-right {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .mr-2 {
            margin-right: 0.5rem;
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
            color: white;
            text-decoration: none;
        }

        .btn-primary.add-btn {
            background-color: #3498db;
            border-color: #3498db;
            padding: 10px 20px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .btn-primary.add-btn:hover {
            background-color: #2980b9;
            border-color: #2980b9;
        }

        .mr-2 {
            margin-right: 0.5rem !important;
        }

        .text-right {
            text-align: right !important;
        }

        /* Icon styles */
        .btn i {
            font-size: 0.9em;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .col-md-6.text-right {
                text-align: left !important;
                margin-top: 1rem;
            }
            
            .btn {
                display: inline-flex;
                margin-bottom: 0.5rem;
                width: auto;
            }
        }

        .gap-2 {
            gap: 0.5rem !important;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
            color: white;
            text-decoration: none;
        }

        .btn-secondary i {
            font-size: 0.9em;
        }

        /* Ensure proper spacing between buttons */
        .d-flex.gap-2 > * {
            margin-left: 0.5rem;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .d-flex.gap-2 {
                flex-direction: column;
                gap: 0.5rem !important;
            }
            
            .d-flex.gap-2 > * {
                margin-left: 0;
                margin-bottom: 0.5rem;
            }
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
</head>
<body>
    <?php include 'includes/navigation.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Manage Teachers</h2>
                <div class="d-flex gap-2">
                    <a href="manage_archived_teachers.php" class="btn btn-secondary">
                        <i class="fas fa-archive"></i> Archived Teachers
                    </a>
                    <a href="add_teacher.php" class="btn btn-primary add-btn">
                        <i class="fas fa-plus"></i> Add Teacher
                    </a>
                </div>
            </div>

            <div class="table-container">
                <table id="teachersTable" class="table table-striped">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Department</th>
                            <th>Section</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($teachers as $teacher): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($teacher['username']); ?></td>
                            <td><?php echo htmlspecialchars($teacher['lastname'] . ', ' . $teacher['firstname']); ?></td>
                            <td><?php echo htmlspecialchars($teacher['email']); ?></td>
                            <td><?php echo htmlspecialchars($teacher['department_name'] ?? 'Not Assigned'); ?></td>
                            <td><?php echo $teacher['sections'] ? htmlspecialchars($teacher['sections']) : 'Not Assigned'; ?></td>
                            <td><span class="badge badge-success">active</span></td>
                            <td>
                                <a href="teacher_editpage.php?id=<?php echo $teacher['teacher_id']; ?>" class="btn btn-sm btn-info">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger archive-btn" data-teacher-id="<?php echo $teacher['teacher_id']; ?>">
                                    <i class="fas fa-archive"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Initialize DataTable -->
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            const table = $('#teachersTable').DataTable({
                "pageLength": 10,
                "ordering": true,
                "info": true,
                "lengthChange": true,
                "searching": true,
                "language": {
                    "lengthMenu": "Show _MENU_ entries",
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                    "search": "Search:",
                    "paginate": {
                        "first": "First",
                        "last": "Last",
                        "next": "Next",
                        "previous": "Previous"
                    }
                }
            });

            // Archive Teacher Button Click Handler
            $('.archive-btn').on('click', function() {
                const teacherId = $(this).data('teacher-id');
                const row = $(this).closest('tr');
                
                Swal.fire({
                    title: 'Archive Teacher?',
                    text: "This teacher will be moved to archived status. You can restore them later.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, archive it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading state
                        Swal.fire({
                            title: 'Processing...',
                            text: 'Please wait while we archive the teacher.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Send AJAX request
                        $.ajax({
                            url: 'functions/archive_teacher.php',
                            type: 'POST',
                            data: {
                                teacher_id: teacherId
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    // Remove the row from the table
                                    const table = $('#teachersTable').DataTable();
                                    table.row(row).remove().draw();
                                    
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success!',
                                        text: response.message,
                                        showConfirmButton: true
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error!',
                                        text: response.message || 'Failed to archive teacher'
                                    });
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('Archive Error:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: 'Failed to process the archive request. Please try again.'
                                });
                            }
                        });
                    }
                });
            });

            // Handle DataTable Search
            $('#teacherSearch').on('keyup', function() {
                table.search(this.value).draw();
            });

            // Handle Entries per page
            $('#teachersPerPage').on('change', function() {
                table.page.len(this.value).draw();
            });
        });
    </script>

    <!-- Rest of your existing JavaScript code -->
</body>
</html>
