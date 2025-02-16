<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
    exit();
}

$db = new DbConnector();

// Get all archived teachers with their departments
$teacher_query = "
    SELECT t.*, d.department_name,
    GROUP_CONCAT(DISTINCT CASE WHEN ss.status = 'active' THEN s.subject_name END) as subjects,
    GROUP_CONCAT(DISTINCT CASE WHEN ss.status = 'active' THEN sec.section_name END) as sections
    FROM teacher t
    LEFT JOIN departments d ON t.department_id = d.department_id
    LEFT JOIN section_subjects ss ON t.teacher_id = ss.teacher_id
    LEFT JOIN subjects s ON ss.subject_id = s.id
    LEFT JOIN sections sec ON ss.section_id = sec.section_id
    WHERE t.status = 'archived'
    GROUP BY t.teacher_id";
$teachers = $db->query($teacher_query)->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archived Teachers - Admin Dashboard</title>
    
    <!-- External CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href="css/dashboard-shared.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="icon" href="../images/light-logo.png">
    <style>
        /* Main Layout */
        .main-content {
            flex: 1;
            padding: 30px;
            width: 100%;
            background-color: #f8f9fa;
        }

        .container-fluid {
            max-width: 100%;
            padding: 0;
        }

        /* Card Styling */
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
            margin-bottom: 30px;
            border: none;
        }

        .card-body {
            padding: 25px;
        }

        /* Header Section */
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
        }

        /* Button Styling */
        .btn-primary {
            background-color: #3498db;
            border-color: #3498db;
            padding: 8px 20px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
            transform: translateY(-1px);
        }

        /* Table Styling */
        .table-responsive {
            margin: 0;
            padding: 0;
        }

        .table {
            width: 100% !important;
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            color: #2c3e50;
            font-weight: 600;
            padding: 15px;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table tbody td {
            padding: 15px;
            vertical-align: middle;
            border-top: 1px solid #dee2e6;
            color: #2c3e50;
        }

        /* Column Widths */
        .table th:nth-child(1) { width: 22%; }  /* Name */
        .table th:nth-child(2) { width: 28%; }  /* Email */
        .table th:nth-child(3) { width: 20%; }  /* Department */
        .table th:nth-child(4) { width: 20%; }  /* Archived Date */
        .table th:nth-child(5) { width: 10%; }  /* Actions */

        /* DataTables Styling */
        .dataTables_wrapper {
            padding: 20px 0;
        }

        .dataTables_length select {
            padding: 6px 30px 6px 10px;
            border-radius: 4px;
            border: 1px solid #dce4ec;
            background-color: white;
        }

        .dataTables_filter input {
            padding: 6px 12px;
            border-radius: 4px;
            border: 1px solid #dce4ec;
            background-color: white;
            margin-left: 8px;
        }

        .dataTables_info {
            color: #7f8c8d;
            font-size: 0.9rem;
            padding-top: 15px;
        }

        .dataTables_paginate {
            padding-top: 15px;
        }

        .paginate_button {
            padding: 5px 12px;
            margin: 0 3px;
            border-radius: 4px;
            cursor: pointer;
        }

        /* Restore Button */
        .restore-btn {
            background-color: #27ae60;
            border-color: #27ae60;
            color: white;
            padding: 6px 15px;
            border-radius: 4px;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }

        .restore-btn:hover {
            background-color: #219a52;
            border-color: #219a52;
            transform: translateY(-1px);
        }

        .restore-btn i {
            margin-right: 5px;
        }

        /* Hover Effects */
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .main-content {
                padding: 20px;
            }
            
            .card-body {
                padding: 15px;
            }
            
            .table thead th,
            .table tbody td {
                padding: 12px 8px;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/navigation.php'; ?>
    
    <div class="d-flex">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="container-fluid">
                <div class="header-section">
                    <h2 class="page-title">Archived Teachers</h2>
                    <a href="manage_teachers.php" class="btn btn-primary">
                        <i class="fas fa-users"></i> Active Teachers
                    </a>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="archivedTeachersTable" class="table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Department</th>
                                        <th>Archived Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($teachers as $teacher): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($teacher['firstname'] . ' ' . $teacher['lastname']); ?></td>
                                        <td><?php echo htmlspecialchars($teacher['email']); ?></td>
                                        <td><?php echo htmlspecialchars($teacher['department_name'] ?? 'No Department'); ?></td>
                                        <td><?php echo date('M d, Y h:i A', strtotime($teacher['archived_at'])); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-success restore-btn" 
                                                    data-teacher-id="<?php echo $teacher['teacher_id']; ?>">
                                                <i class="fas fa-undo"></i> Restore
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            const table = $('#archivedTeachersTable').DataTable({
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

            // Existing restore button click handler
            $('.restore-btn').click(function() {
                const teacherId = $(this).data('teacher-id');
                const row = $(this).closest('tr');
                
                Swal.fire({
                    title: 'Restore Teacher?',
                    text: "This will restore the teacher's access to the system.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#dc3545',
                    confirmButtonText: 'Yes, restore it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading state
                        Swal.fire({
                            title: 'Processing...',
                            text: 'Please wait while we restore the teacher.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        
                        restoreTeacher(teacherId, row, table);
                    }
                });
            });
        });

        function restoreTeacher(teacherId, row, table) {
            $.ajax({
                url: 'handlers/teacher_handler.php',
                type: 'POST',
                data: {
                    action: 'restore',
                    teacher_id: teacherId
                },
                success: function(response) {
                    try {
                        const result = JSON.parse(response);
                        if (result.success) {
                            // Remove the row from DataTable
                            table.row(row).remove().draw();
                            
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Teacher has been restored successfully!'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: result.message || 'Failed to restore teacher'
                            });
                        }
                    } catch (e) {
                        console.error('Parse error:', e);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Failed to process server response'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Failed to connect to server'
                    });
                }
            });
        }
    </script>
</body>
</html> 