<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$db = new DbConnector();

// Get admin info
$admin_id = $_SESSION['admin_id'];
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
    <title>Manage Teachers - Gov D.M. Camerino</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
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

        /* Teacher Management Section */
        .teacher-management {
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
        .add-teacher-btn {
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

        .add-teacher-btn:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }

        .add-teacher-btn i {
            font-size: 0.9rem;
        }

        /* Table Styles */
        .teacher-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 10px;
        }

        .teacher-table th,
        .teacher-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .teacher-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
        }

        .teacher-table tr:hover {
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

            .teacher-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
        }

        /* Add to your existing CSS */
        .action-buttons {
            display: flex;
            gap: 5px;
        }

        .view-btn {
            background: #3498db;
            color: white;
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

        .view-btn:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }

        .swal2-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .swal2-table th,
        .swal2-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .swal2-table th {
            background: #f8f9fa;
            font-weight: 600;
        }

        .swal2-table tr:hover {
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="../images/Logo.png" alt="Logo">
            <h3>Admin Panel</h3>
        </div>
        <ul class="menu-items">
            <li><a href="admin_dashboard.php"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
            <li><a href="manage_students.php"><i class="fas fa-users"></i> <span>Students</span></a></li>
            <li><a href="manage_teachers.php" class="active"><i class="fas fa-chalkboard-teacher"></i> <span>Teachers</span></a></li>
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
            <h2>Manage Teachers</h2>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($admin['firstname']); ?></span>
                <img src="../images/admin.png" alt="Admin">
            </div>
        </div>

        <!-- Teacher Management Section -->
        <div class="teacher-management">
            <div class="management-header">
                <h3>Teacher List</h3>
                <button class="add-teacher-btn" onclick="showAddTeacherModal()">
                    <i class="fas fa-plus"></i> Add New Teacher
                </button>
            </div>
            
            <!-- Teacher Table -->
            <table id="teacherTable" class="teacher-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Department</th>
                        <th>Location</th>
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
        // Initialize DataTable
        const teacherTable = $('#teacherTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: 'handlers/teacher_handler.php?action=get_teachers',
                type: 'GET'
            },
            columns: [
                { data: 'teacher_id' },
                { data: 'name' },
                { data: 'username' },
                { data: 'department' },
                { data: 'location' },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return `
                            <div class="action-buttons">
                                <button class="view-btn" onclick="viewTeacherStudents(${row.teacher_id})">
                                    <i class="fas fa-users"></i> Students
                                </button>
                                <button class="view-btn" onclick="viewTeacherSubjects(${row.teacher_id})">
                                    <i class="fas fa-book"></i> Subjects
                                </button>
                                <button class="edit-btn" onclick="editTeacher(${row.teacher_id})">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="delete-btn" onclick="deleteTeacher(${row.teacher_id})">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        `;
                    }
                }
            ]
        });
    });

    function showAddTeacherModal() {
        Swal.fire({
            title: 'Add New Teacher',
            html: `
                <form id="addTeacherForm">
                    <input type="text" id="firstName" class="swal2-input" placeholder="First Name" required>
                    <input type="text" id="middleName" class="swal2-input" placeholder="Middle Name">
                    <input type="text" id="lastName" class="swal2-input" placeholder="Last Name" required>
                    <input type="text" id="username" class="swal2-input" placeholder="Username" required>
                    <input type="password" id="password" class="swal2-input" placeholder="Password" required>
                    <input type="text" id="department" class="swal2-input" placeholder="Department" required>
                    <input type="text" id="location" class="swal2-input" placeholder="Location">
                </form>
            `,
            showCancelButton: true,
            confirmButtonText: 'Add Teacher',
            cancelButtonText: 'Cancel',
            preConfirm: () => {
                const firstName = document.getElementById('firstName').value;
                const middleName = document.getElementById('middleName').value;
                const lastName = document.getElementById('lastName').value;
                const username = document.getElementById('username').value;
                const password = document.getElementById('password').value;
                const department = document.getElementById('department').value;
                const location = document.getElementById('location').value;

                if (!firstName || !lastName || !username || !password || !department) {
                    Swal.showValidationMessage('Please fill all required fields');
                    return false;
                }

                return { firstName, middleName, lastName, username, password, department, location };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'handlers/teacher_handler.php',
                    method: 'POST',
                    data: {
                        action: 'add_teacher',
                        ...result.value
                    },
                    success: function(response) {
                        const data = JSON.parse(response);
                        if (data.status === 'success') {
                            Swal.fire('Success', data.message, 'success');
                            teacherTable.ajax.reload();
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    }
                });
            }
        });
    }

    // Keep the logout confirmation function
    function confirmLogout(event) {
        event.preventDefault();
        
        Swal.fire({
            title: 'Ready to Leave?',
            text: "Are you sure you want to logout?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, logout',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'logout.php';
            }
        });
    }

    function viewTeacherStudents(teacherId) {
        $.ajax({
            url: 'handlers/teacher_handler.php',
            method: 'GET',
            data: {
                action: 'get_teacher_students',
                teacher_id: teacherId
            },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.status === 'success') {
                    let studentsHtml = `
                        <table class="swal2-table">
                            <thead>
                                <tr>
                                    <th>Student ID</th>
                                    <th>Name</th>
                                    <th>Course</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;
                    
                    data.students.forEach(student => {
                        studentsHtml += `
                            <tr>
                                <td>${student.student_id}</td>
                                <td>${student.name}</td>
                                <td>${student.cys}</td>
                            </tr>
                        `;
                    });
                    
                    studentsHtml += '</tbody></table>';
                    
                    Swal.fire({
                        title: 'Teacher\'s Students',
                        html: studentsHtml,
                        width: '800px',
                        confirmButtonText: 'Close'
                    });
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            }
        });
    }

    function viewTeacherSubjects(teacherId) {
        $.ajax({
            url: 'handlers/teacher_handler.php',
            method: 'GET',
            data: {
                action: 'get_teacher_subjects',
                teacher_id: teacherId
            },
            success: function(response) {
                const data = JSON.parse(response);
                if (data.status === 'success') {
                    let subjectsHtml = `
                        <table class="swal2-table">
                            <thead>
                                <tr>
                                    <th>Subject ID</th>
                                    <th>Subject Name</th>
                                    <th>Department</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;
                    
                    data.subjects.forEach(subject => {
                        subjectsHtml += `
                            <tr>
                                <td>${subject.subject_id}</td>
                                <td>${subject.subject_name}</td>
                                <td>${subject.department}</td>
                            </tr>
                        `;
                    });
                    
                    subjectsHtml += '</tbody></table>';
                    
                    Swal.fire({
                        title: 'Teacher\'s Subjects',
                        html: subjectsHtml,
                        width: '800px',
                        confirmButtonText: 'Close'
                    });
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            }
        });
    }
    </script>
</body>
</html>