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
    <title>Manage Students - Admin Dashboard</title>
    
    <!-- External CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    
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

        .status-archived {
            background: #f8d7da;
            color: #721c24;
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

        .modal-header {
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        .modal-footer {
            background: #f8f9fa;
            border-top: 1px solid #dee2e6;
        }
    </style>
</head>
<body>
    <?php include 'includes/navigation.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="header-actions">
                <h2>Manage Students</h2>
                <button class="add-btn" onclick="showAddStudentModal()">
                    <i class="fas fa-plus"></i> Add New Student
                </button>
            </div>

            <div class="table-container">
                <table id="studentsTable" class="table table-striped">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>CYS</th>
                            <th>Section</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be populated by DataTables -->
                    </tbody>
                </table>
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

    <script>
        $(document).ready(function() {
            const studentsTable = $('#studentsTable').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: 'handlers/student_handler.php',
                    type: 'GET',
                    data: { action: 'get_students' },
                    dataSrc: 'data'
                },
                columns: [
                    { data: 'username' },
                    { 
                        data: null,
                        render: function(data) {
                            let name = `${data.lastname}, ${data.firstname}`;
                            if (data.middlename) {
                                name += ` ${data.middlename.charAt(0)}.`;
                            }
                            return name;
                        }
                    },
                    { data: 'email' },
                    { data: 'cys' },
                    { 
                        data: 'section_name',
                        defaultContent: 'Not Assigned'
                    },
                    { 
                        data: 'status',
                        render: function(data) {
                            const statusClass = {
                                'active': 'success',
                                'inactive': 'warning',
                                'archived': 'danger'
                            }[data.toLowerCase()] || 'secondary';
                            
                            return `<span class="badge badge-${statusClass}">${data}</span>`;
                        }
                    },
                    {
                        data: null,
                        render: function(data) {
                            return `
                                <button class="btn btn-sm btn-primary edit-btn" onclick="editStudent(${data.student_id})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-warning archive-btn" onclick="archiveStudent(${data.student_id})">
                                    <i class="fas fa-archive"></i>
                                </button>
                            `;
                        }
                    }
                ],
                order: [[1, 'asc']]
            });

            // Refresh table every 30 seconds
            setInterval(function() {
                studentsTable.ajax.reload(null, false);
            }, 30000);
        });

        function showAddStudentModal() {
            const modal = `
            <div class="modal fade" id="addStudentModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add New Student</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <form id="addStudentForm" onsubmit="handleAddStudent(event)">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>First Name*</label>
                                    <input type="text" name="firstname" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Last Name*</label>
                                    <input type="text" name="lastname" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Middle Name</label>
                                    <input type="text" name="middlename" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Email*</label>
                                    <input type="email" name="email" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Grade & Section*</label>
                                    <input type="text" name="cys" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control" required>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Add Student</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>`;

            document.body.insertAdjacentHTML('beforeend', modal);
            $('#addStudentModal').modal('show');
            $('#addStudentModal').on('hidden.bs.modal', function() {
                this.remove();
            });
        }

        function handleAddStudent(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            
            $.ajax({
                url: 'handlers/student_handler.php',
                method: 'POST',
                data: {
                    action: 'add_student',
                    ...Object.fromEntries(formData)
                },
                success: function(response) {
                    try {
                        const result = JSON.parse(response);
                        if (result.status === 'success') {
                            Swal.fire({
                                title: 'Success',
                                html: result.message.replace(/\n/g, '<br>'),
                                icon: 'success'
                            });
                            $('#studentsTable').DataTable().ajax.reload();
                            $('#addStudentModal').modal('hide');
                        } else {
                            Swal.fire('Error', result.message, 'error');
                        }
                    } catch (e) {
                        Swal.fire('Error', 'Invalid server response', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Failed to add student', 'error');
                }
            });
        }

        function editStudent(studentId) {
            // Fetch student details first
            $.ajax({
                url: 'handlers/student_handler.php',
                method: 'GET',
                data: {
                    action: 'get_student_details',
                    student_id: studentId
                },
                success: function(response) {
                    try {
                        const result = JSON.parse(response);
                        if (result.status === 'success') {
                            showEditStudentModal(result.data);
                        } else {
                            Swal.fire('Error', result.message, 'error');
                        }
                    } catch (e) {
                        Swal.fire('Error', 'Invalid server response', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Failed to fetch student details', 'error');
                }
            });
        }

        function showEditStudentModal(student) {
            const modal = `
            <div class="modal fade" id="editStudentModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Student</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <form id="editStudentForm" onsubmit="handleEditStudent(event, ${student.student_id})">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>First Name</label>
                                    <input type="text" name="firstname" class="form-control" 
                                           value="${student.firstname}" required>
                                </div>
                                <div class="form-group">
                                    <label>Last Name</label>
                                    <input type="text" name="lastname" class="form-control" 
                                           value="${student.lastname}" required>
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control" 
                                           value="${student.email}" required>
                                </div>
                                <div class="form-group">
                                    <label>LRN</label>
                                    <input type="text" name="lrn" class="form-control" 
                                           value="${student.lrn}" required>
                                </div>
                                <div class="form-group">
                                    <label>Contact Number</label>
                                    <input type="text" name="contact_number" class="form-control" 
                                           value="${student.contact_number || ''}">
                                </div>
                                <div class="form-group">
                                    <label>Gender</label>
                                    <select name="gender" class="form-control" required>
                                        <option value="Male" ${student.gender === 'Male' ? 'selected' : ''}>Male</option>
                                        <option value="Female" ${student.gender === 'Female' ? 'selected' : ''}>Female</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>`;

            document.body.insertAdjacentHTML('beforeend', modal);
            $('#editStudentModal').modal('show');
            $('#editStudentModal').on('hidden.bs.modal', function() {
                this.remove();
            });
        }

        function handleEditStudent(event, studentId) {
            event.preventDefault();
            const formData = new FormData(event.target);
            
            $.ajax({
                url: 'handlers/student_handler.php',
                method: 'POST',
                data: {
                    action: 'edit_student',
                    student_id: studentId,
                    ...Object.fromEntries(formData)
                },
                success: function(response) {
                    try {
                        const result = JSON.parse(response);
                        if (result.status === 'success') {
                            Swal.fire('Success', result.message, 'success');
                            $('#studentsTable').DataTable().ajax.reload();
                            $('#editStudentModal').modal('hide');
                        } else {
                            Swal.fire('Error', result.message, 'error');
                        }
                    } catch (e) {
                        Swal.fire('Error', 'Invalid server response', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Failed to update student', 'error');
                }
            });
        }

        function archiveStudent(studentId) {
            Swal.fire({
                title: 'Archive Student?',
                text: "This student will be archived and can be restored later.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, archive it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'handlers/student_handler.php',
                        method: 'POST',
                        data: {
                            action: 'archive_student',
                            student_id: studentId
                        },
                        success: function(response) {
                            try {
                                const result = JSON.parse(response);
                                if (result.status === 'success') {
                                    Swal.fire('Success', result.message, 'success');
                                    $('#studentsTable').DataTable().ajax.reload();
                                } else {
                                    Swal.fire('Error', result.message, 'error');
                                }
                            } catch (e) {
                                Swal.fire('Error', 'Invalid server response', 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'Failed to archive student', 'error');
                        }
                    });
                }
            });
        }
    </script>
</body>
</html>
