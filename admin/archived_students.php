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
    <title>Archived Students - Admin Dashboard</title>
    
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
            margin-top: 20px;
        }

        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 20px 0;
        }

        .back-btn {
            background: #6c757d;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .back-btn:hover {
            background: #5a6268;
            color: white;
            text-decoration: none;
        }

        .restore-btn {
            background: #28a745;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9em;
        }

        .restore-btn:hover {
            background: #218838;
        }

        main {
            margin-left: 250px; /* Adjust based on your sidebar width */
            padding: 20px;
        }
    </style>
</head>
<body>
    <?php include 'includes/navigation.php'; ?>
    <?php include 'includes/sidebar.php'; ?>

    <main>
        <div class="header-actions">
            <h2>Archived Students</h2>
            <a href="manage_students.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back to Students
            </a>
        </div>

        <div class="table-container">
            <table id="archivedStudentsTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>LRN</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </main>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            $('#archivedStudentsTable').DataTable({
                ajax: {
                    url: 'handlers/student_handler.php',
                    type: 'POST',
                    data: {
                        action: 'get_archived_students'
                    }
                },
                columns: [
                    { data: 'student_id' },
                    { 
                        data: null,
                        render: function(data) {
                            return `${data.firstname} ${data.lastname}`;
                        }
                    },
                    { data: 'lrn' },
                    { 
                        data: 'status',
                        render: function(data) {
                            return `<span class="badge badge-danger">Archived</span>`;
                        }
                    },
                    {
                        data: null,
                        render: function(data) {
                            return `
                                <div class="btn-group" role="group">
                                    <button class="btn btn-success btn-sm mr-2" onclick="restoreStudent('${data.student_id}')">
                                        <i class="fas fa-undo"></i> Restore
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="deleteStudent('${data.student_id}')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>`;
                        }
                    }
                ],
                order: [[0, 'desc']],
                responsive: true,
                processing: true,
                serverSide: false,
                language: {
                    emptyTable: "No archived students found",
                    processing: "Loading archived students..."
                }
            });
        });

        function restoreStudent(studentId) {
            Swal.fire({
                title: 'Restore Student?',
                text: "This will restore the student's account to active status.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, restore it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'handlers/student_handler.php',
                        method: 'POST',
                        data: {
                            action: 'restore_student',
                            student_id: studentId
                        },
                        success: function(response) {
                            try {
                                const result = JSON.parse(response);
                                if (result.status === 'success') {
                                    Swal.fire('Success', result.message, 'success');
                                    $('#archivedStudentsTable').DataTable().ajax.reload();
                                } else {
                                    Swal.fire('Error', result.message, 'error');
                                }
                            } catch (e) {
                                Swal.fire('Error', 'Invalid server response', 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'Failed to restore student', 'error');
                        }
                    });
                }
            });
        }

        function deleteStudent(studentId) {
            Swal.fire({
                title: 'Delete Student Account?',
                text: "This action cannot be undone. All student data will be permanently deleted.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'handlers/student_handler.php',
                        method: 'POST',
                        data: {
                            action: 'delete_archived_student',
                            student_id: studentId
                        },
                        success: function(response) {
                            try {
                                const result = JSON.parse(response);
                                if (result.status === 'success') {
                                    Swal.fire('Deleted!', result.message, 'success');
                                    $('#archivedStudentsTable').DataTable().ajax.reload();
                                } else {
                                    Swal.fire('Error', result.message, 'error');
                                }
                            } catch (e) {
                                Swal.fire('Error', 'Invalid server response', 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'Failed to delete student', 'error');
                        }
                    });
                }
            });
        }
    </script>
</body>
</html> 