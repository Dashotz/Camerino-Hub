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
    <title>Manage Subjects - Gov D.M. Camerino</title>
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
            z-index: 1000;
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
            border-radius: 50%;
        }

        .sidebar-header h3 {
            color: white;
            font-size: 1.2rem;
            font-weight: 600;
        }

        .menu-items {
            list-style: none;
            padding: 0;
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
            background: #34495e;
        }

        .menu-items i {
            font-size: 1.2rem;
        }

        /* Main Content Styles */
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }

        /* Header Styles */
        .header {
            background: white;
            padding: 15px 30px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }

        /* Content Styles */
        .content-wrapper {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .management-header {
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
            transition: all 0.3s ease;
        }

        .add-btn:hover {
            background: #2980b9;
        }

        /* Table Styles */
        .display {
            width: 100% !important;
            margin-top: 20px;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .action-buttons button {
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .action-buttons button i {
            font-size: 1rem;
            width: 16px;
            text-align: center;
        }

        .action-buttons .edit-btn {
            background: #2ecc71;
            color: white;
        }

        .action-buttons .delete-btn {
            background: #e74c3c;
            color: white;
        }

        .action-buttons .view-btn {
            background: #3498db;
            color: white;
        }

        .action-buttons button:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        /* Sweet Alert Customization */
        .swal2-popup {
            font-family: 'Poppins', sans-serif;
        }

        .swal2-input {
            margin: 10px 0;
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
            <li><a href="manage_teachers.php"><i class="fas fa-chalkboard-teacher"></i> <span>Teachers</span></a></li>
            <li><a href="manage_subjects.php" class="active"><i class="fas fa-book"></i> <span>Subjects</span></a></li>
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
        <div class="header">
            <h2>Manage Subjects</h2>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($admin['firstname']); ?></span>
                <img src="../images/admin.png" alt="Admin">
            </div>
        </div>

        <div class="content-wrapper">
            <div class="management-header">
                <h3>Subject List</h3>
                <button class="add-btn" onclick="showAddSubjectModal()">
                    <i class="fas fa-plus"></i> Add New Subject
                </button>
            </div>

            <table id="subjectTable" class="display">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Subject Code</th>
                        <th>Subject Title</th>
                        <th>Category</th>
                        <th>Teachers</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be populated by DataTables -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        $(document).ready(function() {
            const subjectTable = $('#subjectTable').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: 'handlers/subject_handler.php',
                    type: 'GET',
                    data: {
                        action: 'get_subjects'
                    },
                    dataSrc: function(json) {
                        if (json.status === 'success') {
                            return json.data;
                        }
                        // If there's an error, show it and return empty array
                        if (json.status === 'error') {
                            Swal.fire('Error', json.message, 'error');
                        }
                        return [];
                    }
                },
                columns: [
                    { data: 'id' },
                    { data: 'subject_code' },
                    { data: 'subject_title' },
                    { data: 'category' },
                    { 
                        data: 'teacher_count',
                        render: function(data) {
                            return `<span class="badge">${data || 0}</span>`;
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                                <div class="action-buttons">
                                    <button class="view-btn" onclick="viewSubjectTeachers(${row.id})">
                                        <i class="fas fa-users"></i> Teachers
                                    </button>
                                    <button class="edit-btn" onclick="editSubject(${row.id})">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="delete-btn" onclick="deleteSubject(${row.id})">
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
                order: [[0, 'desc']]
            });

            // Add error handling for ajax failures
            $(document).ajaxError(function(event, jqxhr, settings, thrownError) {
                Swal.fire('Error', 'Failed to load data from server', 'error');
            });
        });

        function showAddSubjectModal() {
            Swal.fire({
                title: 'Add New Subject',
                html: `
                    <form id="addSubjectForm">
                        <div class="form-group">
                            <input type="text" id="subject_code" name="subject_code" class="swal2-input" placeholder="Subject Code" required>
                        </div>
                        <div class="form-group">
                            <input type="text" id="subject_title" name="subject_title" class="swal2-input" placeholder="Subject Title" required>
                        </div>
                        <div class="form-group">
                            <input type="text" id="category" name="category" class="swal2-input" placeholder="Category" required>
                        </div>
                    </form>
                `,
                showCancelButton: true,
                confirmButtonText: 'Add Subject',
                cancelButtonText: 'Cancel',
                preConfirm: () => {
                    const subject_code = document.getElementById('subject_code').value;
                    const subject_title = document.getElementById('subject_title').value;
                    const category = document.getElementById('category').value;
                    
                    if (!subject_code || !subject_title || !category) {
                        Swal.showValidationMessage('Please fill in all fields');
                        return false;
                    }
                    
                    return {
                        subject_code: subject_code,
                        subject_title: subject_title,
                        category: category
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'handlers/subject_handler.php',
                        method: 'POST',
                        data: {
                            action: 'add_subject',
                            ...result.value
                        },
                        success: function(response) {
                            try {
                                const data = JSON.parse(response);
                                if (data.status === 'success') {
                                    Swal.fire('Success', data.message, 'success');
                                    $('#subjectTable').DataTable().ajax.reload();
                                } else {
                                    Swal.fire('Error', data.message || 'Unknown error occurred', 'error');
                                }
                            } catch (e) {
                                console.error('JSON parse error:', e);
                                Swal.fire('Error', 'Invalid server response', 'error');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Ajax error:', error);
                            Swal.fire('Error', 'Failed to add subject', 'error');
                        }
                    });
                }
            });
        }

        // Add other necessary functions

        // Add this temporarily to see the actual response
        $.get('handlers/subject_handler.php?action=get_subjects', function(response) {
            console.log('Raw response:', response);
            try {
                const json = JSON.parse(response);
                console.log('Parsed JSON:', json);
            } catch (e) {
                console.error('JSON parse error:', e);
            }
        });
    </script>
</body>
</html>