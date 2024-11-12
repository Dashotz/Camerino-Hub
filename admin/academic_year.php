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
    <title>Academic Year Management - Admin Dashboard</title>
    
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
        }

        .stats-info h3 {
            color: #2c3e50;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .current-year-badge {
            background-color: #2ecc71;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
        }

        .year-status {
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.875rem;
        }

        .year-status.active {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .year-status.inactive {
            background-color: #ffebee;
            color: #c62828;
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
                <h1>Academic Year Management</h1>
                <p>Manage school academic years and enrollment periods</p>
            </div>

            <!-- Stats Cards -->
            <div class="row stats-cards mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card stats-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon-container">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div class="stats-info">
                                <h5 class="card-title mb-0">Total Academic Years</h5>
                                <h3 id="totalYears" class="mb-0">Loading...</h3>
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
                                <h5 class="card-title mb-0">Current Enrollees</h5>
                                <h3 id="currentEnrollees" class="mb-0">Loading...</h3>
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
                                <h5 class="card-title mb-0">Active Teachers</h5>
                                <h3 id="activeTeachers" class="mb-0">Loading...</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card stats-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon-container">
                                <i class="fas fa-door-open"></i>
                            </div>
                            <div class="stats-info">
                                <h5 class="card-title mb-0">Active Sections</h5>
                                <h3 id="activeSections" class="mb-0">Loading...</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Academic Years Table Section -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Academic Years</h5>
                    <button class="btn btn-primary" onclick="showAddYearModal()">
                        <i class="fas fa-plus"></i> Add New Academic Year
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="academicYearTable" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>School Year</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Status</th>
                                    <th>Enrollees</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Academic Year Modal -->
    <div class="modal fade" id="academicYearModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add New Academic Year</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="academicYearForm">
                    <div class="modal-body">
                        <input type="hidden" name="year_id" id="year_id">
                        
                        <div class="form-group">
                            <label>School Year</label>
                            <input type="text" class="form-control" name="school_year" 
                                   placeholder="e.g., 2024-2025" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Start Date</label>
                            <input type="date" class="form-control" name="start_date" required>
                        </div>
                        
                        <div class="form-group">
                            <label>End Date</label>
                            <input type="date" class="form-control" name="end_date" required>
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
                        <button type="submit" class="btn btn-primary">Save Academic Year</button>
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
            $('#academicYearTable').DataTable({
                ajax: {
                    url: 'handlers/academic_year_handler.php',
                    type: 'GET',
                    data: { action: 'get_academic_years' }
                },
                columns: [
                    { data: 'school_year' },
                    { 
                        data: 'year_start',
                        render: function(data) {
                            return new Date(data).toLocaleDateString();
                        }
                    },
                    { 
                        data: 'year_end',
                        render: function(data) {
                            return new Date(data).toLocaleDateString();
                        }
                    },
                    { 
                        data: 'status',
                        render: function(data, type, row) {
                            const statusClass = data === 'active' ? 'active' : 'inactive';
                            return `<span class="year-status ${statusClass}">${data}</span>`;
                        }
                    },
                    { data: 'enrollee_count' },
                    {
                        data: 'id',
                        render: function(data, type, row) {
                            return `
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-info" onclick="viewYear(${data})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-primary" onclick="editYear(${data})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteYear(${data})">
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
            $('#academicYearForm').on('submit', handleYearSubmit);
        });

        function loadDashboardStats() {
            $.ajax({
                url: 'handlers/academic_year_handler.php',
                type: 'GET',
                data: { action: 'get_year_stats' },
                success: function(response) {
                    if (response.status === 'success') {
                        updateStatWithAnimation('#totalYears', response.data.total_years);
                        updateStatWithAnimation('#currentEnrollees', response.data.current_enrollees);
                        updateStatWithAnimation('#activeTeachers', response.data.active_teachers);
                        updateStatWithAnimation('#activeSections', response.data.active_sections);
                    }
                }
            });
        }

        function updateStatWithAnimation(elementId, value) {
            const element = $(elementId);
            element.prop('Counter', 0).animate({
                Counter: value
            }, {
                duration: 1000,
                easing: 'swing',
                step: function(now) {
                    $(this).text(Math.ceil(now));
                }
            });
        }

        function showAddYearModal() {
            $('#year_id').val('');
            $('#academicYearForm')[0].reset();
            $('#modalTitle').text('Add New Academic Year');
            $('#academicYearModal').modal('show');
        }

        function handleYearSubmit(e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            
            $.ajax({
                url: 'handlers/academic_year_handler.php',
                type: 'POST',
                data: {
                    action: formData.get('year_id') ? 'update_year' : 'add_year',
                    ...Object.fromEntries(formData)
                },
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire('Success', response.message, 'success');
                        $('#academicYearModal').modal('hide');
                        $('#academicYearTable').DataTable().ajax.reload();
                        loadDashboardStats();
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                }
            });
        }

        function editYear(id) {
            $.ajax({
                url: 'handlers/academic_year_handler.php',
                type: 'GET',
                data: { action: 'get_year_details', id: id },
                success: function(response) {
                    if (response.status === 'success') {
                        const year = response.data;
                        $('#year_id').val(year.id);
                        $('input[name="school_year"]').val(year.school_year);
                        $('input[name="start_date"]').val(year.year_start);
                        $('input[name="end_date"]').val(year.year_end);
                        $('select[name="status"]').val(year.status);
                        
                        $('#modalTitle').text('Edit Academic Year');
                        $('#academicYearModal').modal('show');
                    }
                }
            });
        }

        function deleteYear(id) {
            Swal.fire({
                title: 'Delete Academic Year?',
                text: "This action cannot be undone",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'handlers/academic_year_handler.php',
                        type: 'POST',
                        data: { action: 'delete_year', id: id },
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire('Deleted!', response.message, 'success');
                                $('#academicYearTable').DataTable().ajax.reload();
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