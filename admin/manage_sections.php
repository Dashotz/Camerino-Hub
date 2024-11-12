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
    <title>Manage Sections - Admin Dashboard</title>
    
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

        .section-table th {
            font-weight: 600;
            color: #2c3e50;
        }

        .section-table td {
            vertical-align: middle;
        }

        .action-buttons .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
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
                <h1>Manage Sections</h1>
                <p>Add, edit, and manage school sections</p>
            </div>

            <!-- Stats Cards -->
            <div class="row stats-cards mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card stats-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon-container">
                                <i class="fas fa-door-open"></i>
                            </div>
                            <div class="stats-info">
                                <h5 class="card-title mb-0">Total Sections</h5>
                                <h3 id="totalSections" class="mb-0">Loading...</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card stats-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon-container">
                                <i class="fas fa-user-friends"></i>
                            </div>
                            <div class="stats-info">
                                <h5 class="card-title mb-0">Total Students</h5>
                                <h3 id="totalStudents" class="mb-0">Loading...</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card stats-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon-container">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                            <div class="stats-info">
                                <h5 class="card-title mb-0">Assigned Advisers</h5>
                                <h3 id="assignedAdvisers" class="mb-0">Loading...</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card stats-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon-container">
                                <i class="fas fa-book-reader"></i>
                            </div>
                            <div class="stats-info">
                                <h5 class="card-title mb-0">Active Sections</h5>
                                <h3 id="activeSections" class="mb-0">Loading...</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sections Table Section -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Section List</h5>
                    <button class="btn btn-primary" onclick="showAddSectionModal()">
                        <i class="fas fa-plus"></i> Add New Section
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="sectionTable" class="table table-striped section-table">
                            <thead>
                                <tr>
                                    <th>Section Name</th>
                                    <th>Grade Level</th>
                                    <th>Adviser</th>
                                    <th>Students</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Table data will be loaded dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Section Modal -->
    <div class="modal fade" id="sectionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add New Section</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="sectionForm">
                    <div class="modal-body">
                        <input type="hidden" name="section_id" id="section_id">
                        
                        <div class="form-group">
                            <label>Section Name*</label>
                            <input type="text" class="form-control" name="section_name" required>
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
                            <label>Adviser</label>
                            <select class="form-control" name="adviser_id">
                                <option value="">Select Adviser</option>
                                <!-- Advisers will be loaded dynamically -->
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Status</label>
                            <select class="form-control" name="status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Section</button>
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
    
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#sectionTable').DataTable({
                ajax: {
                    url: 'handlers/section_handler.php',
                    type: 'GET',
                    data: { action: 'get_sections' }
                },
                columns: [
                    { data: 'section_name' },
                    { data: 'grade_level' },
                    { data: 'adviser_name' },
                    { data: 'student_count' },
                    { 
                        data: 'status',
                        render: function(data) {
                            return `<span class="badge badge-${data === 'active' ? 'success' : 'secondary'}">${data}</span>`;
                        }
                    },
                    {
                        data: 'section_id',
                        render: function(data) {
                            return `
                                <div class="btn-group action-buttons">
                                    <button class="btn btn-sm btn-info" onclick="viewSection(${data})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-primary" onclick="editSection(${data})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteSection(${data})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            `;
                        }
                    }
                ]
            });

            // Load dashboard stats
            loadDashboardStats();
            
            // Form submission handler
            $('#sectionForm').on('submit', handleSectionSubmit);
        });

        function loadDashboardStats() {
            $.ajax({
                url: 'handlers/section_handler.php',
                type: 'GET',
                data: { action: 'get_section_stats' },
                success: function(response) {
                    if (response.status === 'success') {
                        updateStatWithAnimation('#totalSections', response.data.total_sections, 'Total');
                        updateStatWithAnimation('#totalStudents', response.data.total_students, 'Students');
                        updateStatWithAnimation('#assignedAdvisers', response.data.assigned_advisers, 'Advisers');
                        updateStatWithAnimation('#activeSections', response.data.active_sections, 'Active');
                    }
                }
            });
        }

        function updateStatWithAnimation(elementId, value, label) {
            const element = $(elementId);
            element.prop('Counter', 0).animate({
                Counter: value
            }, {
                duration: 1000,
                easing: 'swing',
                step: function(now) {
                    $(this).html(`
                        ${Math.ceil(now)}
                        <small class="text-muted d-block">${label}</small>
                    `);
                }
            });
        }

        // Add the rest of your JavaScript functions here
        // (showAddSectionModal, editSection, deleteSection, etc.)
    </script>
</body>
</html>
