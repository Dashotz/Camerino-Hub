<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Get admin data
require_once('../db/dbConnector.php');
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
    <title>Manage Students - Gov D.M. Camerino</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Add DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <style>
        /* Base Styles */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #f4f6f9;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100%;
            width: 250px;
            background: #2c3e50;
            padding: 20px;
            transition: all 0.3s ease;
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            gap: 15px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }

        .sidebar-header img {
            width: 40px;
            height: 40px;
        }

        .sidebar-header h3 {
            color: white;
            font-size: 1.2rem;
        }

        .menu-items {
            list-style: none;
        }

        .menu-items li {
            margin-bottom: 5px;
        }

        .menu-items a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 15px;
            color: #ecf0f1;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .menu-items a:hover, .menu-items a.active {
            background: rgba(255,255,255,0.1);
            transform: translateX(5px);
        }

        /* Main Content Layout */
        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: all 0.3s ease;
        }

        /* Top Bar Styles */
        .top-bar {
            background: white;
            padding: 15px 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .user-info span {
            color: #2c3e50;
            font-weight: 500;
        }

        /* Student Management Section */
        .student-management {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .management-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .management-header h3 {
            color: #2c3e50;
            font-size: 1.25rem;
        }

        /* Button Styles */
        .add-student-btn {
            background: #3498db;
            color: white;
            padding: 12px 24px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .add-student-btn:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }

        .add-student-btn i {
            font-size: 0.9rem;
        }

        /* Table Styles */
        .student-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 10px;
        }

        .student-table th,
        .student-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .student-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
            white-space: nowrap;
        }

        .student-table tr:hover {
            background-color: #f8f9fa;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .action-buttons button {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s ease;
        }

        .edit-btn {
            background: #2ecc71;
            color: white;
        }

        .delete-btn {
            background: #e74c3c;
            color: white;
        }

        .edit-btn:hover {
            background: #27ae60;
            transform: translateY(-2px);
        }

        .delete-btn:hover {
            background: #c0392b;
            transform: translateY(-2px);
        }

        /* DataTables Custom Styling */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 20px;
        }

        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            padding: 6px 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 8px 14px;
            margin: 0 4px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #3498db;
            color: white !important;
            border: 1px solid #3498db;
        }

        /* Sweet Alert Custom Styling */
        .swal2-input {
            margin: 10px 0;
            padding: 10px;
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .main-content {
                margin-left: 200px;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 70px;
            }

            .management-header {
                flex-direction: column;
                gap: 15px;
            }

            .student-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar (same as admin_dashboard.php) -->
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="../images/Logo.png" alt="Logo">
            <h3>Admin Panel</h3>
        </div>
        <ul class="menu-items">
            <li><a href="admin_dashboard.php"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
            <li><a href="manage_students.php" class="active"><i class="fas fa-users"></i> <span>Students</span></a></li>
            <li><a href="manage_teachers.php"><i class="fas fa-chalkboard-teacher"></i> <span>Teachers</span></a></li>
            <li><a href="manage_subjects.php"><i class="fas fa-book"></i> <span>Subjects</span></a></li>
            <li><a href="settings.php"><i class="fas fa-cog"></i> <span>Settings</span></a></li>
            <li>
                <a href="#" onclick="confirmLogout(event)">
                    <i class="fas fa-sign-out-alt"></i> 
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="top-bar">
            <h2>Manage Students</h2>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($admin['firstname']); ?></span>
                <img src="../images/admin.png" alt="Admin">
            </div>
        </div>

        <!-- Student Management Section -->
        <div class="student-management">
            <div class="management-header">
                <h3>Student List</h3>
                <button class="add-student-btn" onclick="showAddStudentModal()">
                    <i class="fas fa-plus"></i> Add New Student
                </button>
            </div>
            
            <!-- Student Table -->
            <table id="studentTable" class="student-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Class/Year/Section</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Table data will be populated dynamically -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add necessary scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
    $(document).ready(function() {
        // Initialize DataTable with AJAX
        const studentTable = $('#studentTable').DataTable({
            processing: true,
            serverSide: false, // Set to true if you want server-side processing
            ajax: {
                url: 'handlers/student_handler.php?action=get_students',
                type: 'GET',
                dataSrc: 'data'
            },
            columns: [
                { data: 'student_id' },
                { data: 'name' },
                { data: 'username' },
                { data: 'cys' },
                { data: 'location' },
                { data: 'status' },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return `
                            <div class="action-buttons">
                                <button class="edit-btn" onclick="editStudent(${row.student_id})">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="delete-btn" onclick="deleteStudent(${row.student_id})">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        `;
                    }
                }
            ],
            responsive: true,
            dom: 'Bfrtip',
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            pageLength: 10,
            order: [[0, 'desc']], // Order by student_id descending
            language: {
                emptyTable: "No students found",
                loadingRecords: "Loading...",
                processing: "Processing...",
                zeroRecords: "No matching students found"
            }
        });

        // Add Student Form Submission
        window.showAddStudentModal = function() {
            Swal.fire({
                title: 'Add New Student',
                html: `
                    <form id="addStudentForm">
                        <input type="text" id="firstName" class="swal2-input" placeholder="First Name" required>
                        <input type="text" id="middleName" class="swal2-input" placeholder="Middle Name">
                        <input type="text" id="lastName" class="swal2-input" placeholder="Last Name" required>
                        <input type="text" id="username" class="swal2-input" placeholder="Username" required>
                        <input type="password" id="password" class="swal2-input" placeholder="Password" required>
                        <input type="text" id="cys" class="swal2-input" placeholder="Class, Year & Section" required>
                        <input type="text" id="location" class="swal2-input" placeholder="Location">
                    </form>
                `,
                showCancelButton: true,
                confirmButtonText: 'Add Student',
                cancelButtonText: 'Cancel',
                preConfirm: () => {
                    const firstName = document.getElementById('firstName').value;
                    const middleName = document.getElementById('middleName').value;
                    const lastName = document.getElementById('lastName').value;
                    const username = document.getElementById('username').value;
                    const password = document.getElementById('password').value;
                    const cys = document.getElementById('cys').value;
                    const location = document.getElementById('location').value;

                    if (!firstName || !lastName || !username || !password || !cys) {
                        Swal.showValidationMessage('Please fill all required fields');
                        return false;
                    }

                    return { firstName, middleName, lastName, username, password, cys, location };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'handlers/student_handler.php',
                        method: 'POST',
                        data: {
                            action: 'add_student',
                            ...result.value
                        },
                        success: function(response) {
                            const data = JSON.parse(response);
                            if (data.status === 'success') {
                                Swal.fire('Success', data.message, 'success');
                                studentTable.ajax.reload();
                            } else {
                                Swal.fire('Error', data.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'Failed to add student', 'error');
                        }
                    });
                }
            });
        };

        // Edit Student
        window.editStudent = function(studentId) {
            // First fetch student data
            $.get(`handlers/student_handler.php?action=get_student&id=${studentId}`, function(response) {
                const student = JSON.parse(response).data;
                
                Swal.fire({
                    title: 'Edit Student',
                    html: `
                        <form id="editStudentForm">
                            <input type="text" id="firstName" class="swal2-input" value="${student.firstname}" required>
                            <input type="text" id="lastName" class="swal2-input" value="${student.lastname}" required>
                            <input type="email" id="email" class="swal2-input" value="${student.email}" required>
                            <select id="course" class="swal2-input" required>
                                <option value="BSIT" ${student.course === 'BSIT' ? 'selected' : ''}>BSIT</option>
                                <option value="BSCS" ${student.course === 'BSCS' ? 'selected' : ''}>BSCS</option>
                            </select>
                            <select id="status" class="swal2-input" required>
                                <option value="Active" ${student.status === 'Active' ? 'selected' : ''}>Active</option>
                                <option value="Inactive" ${student.status === 'Inactive' ? 'selected' : ''}>Inactive</option>
                            </select>
                        </form>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Update',
                    cancelButtonText: 'Cancel',
                    preConfirm: () => {
                        // Similar validation as add student
                        return {
                            student_id: studentId,
                            firstName: document.getElementById('firstName').value,
                            lastName: document.getElementById('lastName').value,
                            email: document.getElementById('email').value,
                            course: document.getElementById('course').value,
                            status: document.getElementById('status').value
                        };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'handlers/student_handler.php',
                            method: 'POST',
                            data: {
                                action: 'update_student',
                                ...result.value
                            },
                            success: function(response) {
                                const data = JSON.parse(response);
                                if (data.status === 'success') {
                                    Swal.fire('Success', data.message, 'success');
                                    studentTable.ajax.reload();
                                } else {
                                    Swal.fire('Error', data.message, 'error');
                                }
                            }
                        });
                    }
                });
            });
        };

        // Delete Student
        window.deleteStudent = function(studentId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'handlers/student_handler.php',
                        method: 'POST',
                        data: {
                            action: 'delete_student',
                            student_id: studentId
                        },
                        success: function(response) {
                            const data = JSON.parse(response);
                            if (data.status === 'success') {
                                Swal.fire('Deleted!', data.message, 'success');
                                studentTable.ajax.reload();
                            } else {
                                Swal.fire('Error', data.message, 'error');
                            }
                        }
                    });
                }
            });
        };
    });

    // Keep the logout confirmation function from admin_dashboard.php
    function confirmLogout(event) {
        // ... (same as in admin_dashboard.php)
    }
    </script>
</body>
</html>
