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
    <link rel="icon" href="../images/light-logo.png">
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

        #yearDetailsModal .modal-xl {
            max-width: 95%;
        }

        #yearDetailsModal .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }

        #yearDetailsModal .card:hover {
            transform: translateY(-5px);
        }

        #yearDetailsModal .card i {
            font-size: 24px;
            display: block;
            margin-bottom: 10px;
        }

        #yearDetailsModal .table {
            margin-bottom: 0;
        }

        #yearDetailsModal .table thead th {
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            color: #495057;
        }

        #yearDetailsModal .table td {
            vertical-align: middle;
        }

        .badge-active {
            background-color: #28a745;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: 500;
        }

        .badge-inactive {
            background-color: #6c757d;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: 500;
        }

        /* DataTables customization */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 15px;
        }

        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            margin-top: 15px;
        }

        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px 10px;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            #yearDetailsModal .modal-xl {
                max-width: 100%;
                margin: 10px;
            }
            
            #yearDetailsModal .card {
                margin-bottom: 15px;
            }
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
                    <button class="btn btn-primary mb-3" onclick="showAddYearModal()">
                        <i class="fas fa-plus"></i> Add New Academic Year
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="academicYearTable" class="table table-striped table-bordered">
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

            <!-- Archived Academic Years Table Section -->
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Archived Academic Years</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="archivedYearsTable" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>School Year</th>
                                    <th>Original Dates</th>
                                    <th>Archive Date</th>
                                    <th>Archived By</th>
                                    <th>Status</th>
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

    <!-- Add this modal after your existing modals -->
    <div class="modal fade" id="yearDetailsModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-users mr-2"></i>
                        Academic Year Enrollment Details
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h6 class="card-title mb-3">
                                        <i class="fas fa-users mb-2"></i>
                                        Total Enrollees
                                    </h6>
                                    <h3 id="yearTotalEnrollees" class="mb-0">0</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h6 class="card-title mb-3">
                                        <i class="fas fa-user-check mb-2"></i>
                                        Active Students
                                    </h6>
                                    <h3 id="yearActiveEnrollees" class="mb-0">0</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-secondary text-white">
                                <div class="card-body text-center">
                                    <h6 class="card-title mb-3">
                                        <i class="fas fa-user-clock mb-2"></i>
                                        Archived Students
                                    </h6>
                                    <h3 id="yearArchivedEnrollees" class="mb-0">0</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Enrollment Table -->
                    <div class="table-responsive">
                        <table id="enrollmentTable" class="table table-striped table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th>Student Name</th>
                                    <th>Email</th>
                                    <th>Section</th>
                                    <th>Status</th>
                                    <th>Date Enrolled</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Academic Year Modal -->
    <div class="modal fade" id="editYearModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Academic Year</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="editYearForm">
                    <div class="modal-body">
                        <input type="hidden" id="edit_year_id" name="year_id">
                        <div class="form-group">
                            <label>School Year</label>
                            <input type="text" class="form-control" id="edit_school_year" name="school_year" required 
                                   pattern="\d{4}-\d{4}" title="Format: YYYY-YYYY (e.g., 2024-2025)">
                        </div>
                        <div class="form-group">
                            <label>Start Date</label>
                            <input type="date" class="form-control" id="edit_start_date" name="start_date" required>
                        </div>
                        <div class="form-group">
                            <label>End Date</label>
                            <input type="date" class="form-control" id="edit_end_date" name="end_date" required>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select class="form-control" id="edit_status" name="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Academic Year Modal -->
    <div class="modal fade" id="addYearModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Academic Year</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="addYearForm">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>School Year</label>
                            <input type="text" class="form-control" id="school_year" name="school_year" required 
                                   pattern="\d{4}-\d{4}" title="Format: YYYY-YYYY (e.g., 2024-2025)">
                        </div>
                        <div class="form-group">
                            <label>Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" required>
                        </div>
                        <div class="form-group">
                            <label>End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" required>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Academic Year</button>
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
            const academicYearTable = $('#academicYearTable').DataTable({
                ajax: {
                    url: 'handlers/academic_year_handler.php?action=get_academic_years',
                    dataSrc: 'data'
                },
                columns: [
                    { data: 'school_year' },
                    { 
                        data: 'start_date',
                        render: function(data) {
                            return new Date(data).toLocaleDateString();
                        }
                    },
                    { 
                        data: 'end_date',
                        render: function(data) {
                            return new Date(data).toLocaleDateString();
                        }
                    },
                    { 
                        data: 'status',
                        render: function(data) {
                            const statusClass = data === 'active' ? 'badge-active' : 'badge-inactive';
                            return `<span class="badge ${statusClass}">${data}</span>`;
                        }
                    },
                    { data: 'enrollee_count' },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return `
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-primary" onclick="editYear(${row.id})">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-info" onclick="viewYear(${row.id})">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <button class="btn btn-sm btn-warning" onclick="archiveYear(${row.id})">
                                        <i class="fas fa-archive"></i> Archive
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteYear(${row.id})">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>`;
                        }
                    }
                ],
                order: [[0, 'desc']],
                pageLength: 10,
                responsive: true
            });

            // Load dashboard stats
            loadDashboardStats();
            
            // Form submission handler
            $('#academicYearForm').on('submit', handleYearSubmit);

            // Add this after your existing DataTable initialization
            const archivedYearsTable = $('#archivedYearsTable').DataTable({
                ajax: {
                    url: 'handlers/academic_year_handler.php?action=get_archived_years',
                    dataSrc: 'data'
                },
                columns: [
                    { data: 'school_year' },
                    { 
                        data: null,
                        render: function(data) {
                            const start = new Date(data.start_date).toLocaleDateString();
                            const end = new Date(data.end_date).toLocaleDateString();
                            return `${start} - ${end}`;
                        }
                    },
                    { 
                        data: 'archived_at',
                        render: function(data) {
                            return new Date(data).toLocaleString();
                        }
                    },
                    { data: 'archived_by_admin' },
                    { 
                        data: 'status',
                        render: function(data) {
                            return `<span class="badge badge-secondary">archived</span>`;
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return `
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-info" onclick="viewArchivedYear(${row.id})" title="View Details">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <button class="btn btn-sm btn-success" onclick="restoreYear(${row.id})" title="Restore">
                                        <i class="fas fa-undo"></i> Restore
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteArchivedYear(${row.id})" title="Delete">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>`;
                        },
                        orderable: false,
                        className: 'text-center'
                    }
                ],
                order: [[2, 'desc']],
                pageLength: 10,
                responsive: true,
                dom: '<"d-flex justify-content-between align-items-center mb-3"lf>rtip',
                language: {
                    search: "Search archives:",
                    lengthMenu: "Show _MENU_ archives per page"
                }
            });
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
                data: { 
                    action: 'get_year_details', 
                    id: id 
                },
                success: function(response) {
                    if (response.status === 'success') {
                        const year = response.data.year_details;
                        
                        // Populate the edit modal
                        $('#edit_year_id').val(year.id);
                        $('#edit_school_year').val(year.school_year);
                        $('#edit_start_date').val(year.start_date);
                        $('#edit_end_date').val(year.end_date);
                        $('#edit_status').val(year.status);
                        
                        // Show the edit modal
                        $('#editYearModal').modal('show');
                    } else {
                        Swal.fire('Error', 'Failed to load year details', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Failed to connect to server', 'error');
                }
            });
        }

        // Handle edit form submission
        $('#editYearForm').on('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'update_year');
            
            $.ajax({
                url: 'handlers/academic_year_handler.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire('Success', 'Academic year updated successfully', 'success');
                        $('#editYearModal').modal('hide');
                        $('#academicYearsTable').DataTable().ajax.reload();
                    } else {
                        Swal.fire('Error', response.message || 'Failed to update academic year', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Failed to connect to server', 'error');
                }
            });
        });

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

        function viewYear(id) {
            $.ajax({
                url: 'handlers/academic_year_handler.php',
                type: 'GET',
                data: { action: 'get_year_details', id: id },
                success: function(response) {
                    if (response.status === 'success') {
                        const { year_details, enrollments, statistics } = response.data;
                        
                        // Update statistics
                        $('#yearTotalEnrollees').text(statistics.total_enrollees || 0);
                        $('#yearActiveEnrollees').text(statistics.active_enrollees || 0);
                        $('#yearArchivedEnrollees').text(statistics.archived_enrollees || 0);
                        
                        // Initialize enrollment table
                        if ($.fn.DataTable.isDataTable('#enrollmentTable')) {
                            $('#enrollmentTable').DataTable().destroy();
                        }

                        $('#enrollmentTable').DataTable({
                            data: enrollments || [],
                            columns: [
                                { 
                                    data: null,
                                    render: function(data) {
                                        return `<strong>${data.lastname}, ${data.firstname}</strong>`;
                                    }
                                },
                                { data: 'email' },
                                { 
                                    data: 'section_name',
                                    render: function(data) {
                                        return `<span class="badge badge-info">${data}</span>`;
                                    }
                                },
                                { 
                                    data: 'enrollment_status',
                                    render: function(data) {
                                        const statusClass = data === 'active' ? 'badge-active' : 'badge-inactive';
                                        return `<span class="badge ${statusClass}">${data}</span>`;
                                    }
                                },
                                { 
                                    data: 'date_enrolled',
                                    render: function(data) {
                                        return new Date(data).toLocaleDateString('en-US', {
                                            year: 'numeric',
                                            month: 'short',
                                            day: 'numeric'
                                        });
                                    }
                                }
                            ],
                            responsive: true,
                            dom: '<"d-flex justify-content-between align-items-center mb-3"lf>rtip',
                            language: {
                                search: "Search students:",
                                lengthMenu: "Show _MENU_ students per page"
                            },
                            pageLength: 10,
                            order: [[0, 'asc']]
                        });
                        
                        $('#yearDetailsModal').modal('show');
                    } else {
                        Swal.fire('Error', 'Failed to load year details', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Failed to connect to server', 'error');
                }
            });
        }

        // Add button click handler
        function showAddYearModal() {
            $('#addYearModal').modal('show');
        }

        // Form submission handler
        $('#addYearForm').on('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'add_year');
            
            $.ajax({
                url: 'handlers/academic_year_handler.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Academic year added successfully'
                        }).then(() => {
                            $('#addYearModal').modal('hide');
                            $('#addYearForm')[0].reset();
                            $('#academicYearsTable').DataTable().ajax.reload();
                        });
                    } else {
                        Swal.fire('Error', response.message || 'Failed to add academic year', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Failed to connect to server', 'error');
                }
            });
        });

        function archiveYear(id) {
            Swal.fire({
                title: 'Archive Academic Year?',
                text: "This will move the academic year to archives. You can restore it later.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, archive it'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'handlers/academic_year_handler.php',
                        type: 'POST',
                        data: { 
                            action: 'archive_year', 
                            year_id: id 
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire('Archived!', response.message, 'success');
                                // Refresh both tables
                                $('#academicYearTable').DataTable().ajax.reload();
                                $('#archivedYearsTable').DataTable().ajax.reload();
                                loadDashboardStats();
                            } else {
                                Swal.fire('Error', response.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'Failed to connect to server', 'error');
                        }
                    });
                }
            });
        }

        function restoreYear(id) {
            Swal.fire({
                title: 'Restore Academic Year?',
                text: "This will restore the academic year to its previous state.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, restore it'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'handlers/academic_year_handler.php',
                        type: 'POST',
                        data: { 
                            action: 'restore_year', 
                            archive_id: id 
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire('Restored!', response.message, 'success');
                                // Refresh both tables
                                $('#academicYearTable').DataTable().ajax.reload();
                                $('#archivedYearsTable').DataTable().ajax.reload();
                                loadDashboardStats();
                            } else {
                                Swal.fire('Error', response.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'Failed to connect to server', 'error');
                        }
                    });
                }
            });
        }

        // Add this function to view archived year details
        function viewArchivedYear(id) {
            $.ajax({
                url: 'handlers/academic_year_handler.php',
                type: 'GET',
                data: { 
                    action: 'get_archived_year_details',
                    archive_id: id 
                },
                success: function(response) {
                    if (response.status === 'success' && response.data) {
                        // Safely destructure with default empty objects
                        const { 
                            year_details = {},
                            enrollments = [], 
                            statistics = {
                                total_enrollees: 0,
                                active_enrollees: 0,
                                archived_enrollees: 0
                            }
                        } = response.data;

                        // Check if year_details exists before using it
                        if (year_details) {
                            // Update modal title safely
                            $('#yearDetailsModal .modal-title').html(`
                                <i class="fas fa-archive mr-2"></i>
                                Archived Academic Year Details: ${year_details.school_year || 'Unknown Year'}
                            `);
                            
                            // Create archive info only if we have the required data
                            if (year_details.archived_at) {
                                const archiveInfo = `
                                    <div class="alert alert-info mb-4">
                                        <h6 class="mb-2"><i class="fas fa-info-circle mr-2"></i>Archive Information</h6>
                                        <p class="mb-1"><strong>Archived Date:</strong> ${new Date(year_details.archived_at).toLocaleString()}</p>
                                        <p class="mb-1"><strong>Archived By:</strong> ${year_details.archived_by_admin || 'Unknown'}</p>
                                        ${year_details.archive_notes ? `<p class="mb-0"><strong>Notes:</strong> ${year_details.archive_notes}</p>` : ''}
                                    </div>
                                `;
                                
                                // Remove any existing archive info before adding new one
                                $('.modal-body .alert.alert-info').remove();
                                $('.modal-body .row.mb-4').after(archiveInfo);
                            }
                        }

                        // Update statistics safely
                        $('#yearTotalEnrollees').text(statistics.total_enrollees || 0);
                        $('#yearActiveEnrollees').text(statistics.active_enrollees || 0);
                        $('#yearArchivedEnrollees').text(statistics.archived_enrollees || 0);
                        
                        // Initialize enrollment table
                        if ($.fn.DataTable.isDataTable('#enrollmentTable')) {
                            $('#enrollmentTable').DataTable().destroy();
                        }

                        $('#enrollmentTable').DataTable({
                            data: enrollments,
                            columns: [
                                { 
                                    data: null,
                                    render: function(data) {
                                        return `<strong>${data.lastname || ''}, ${data.firstname || ''}</strong>`;
                                    }
                                },
                                { 
                                    data: 'email',
                                    render: function(data) {
                                        return data || 'N/A';
                                    }
                                },
                                { 
                                    data: 'section_name',
                                    render: function(data) {
                                        return data ? `<span class="badge badge-info">${data}</span>` : 'N/A';
                                    }
                                },
                                { 
                                    data: 'enrollment_status',
                                    render: function(data) {
                                        const statusClass = data === 'active' ? 'badge-success' : 'badge-secondary';
                                        return `<span class="badge ${statusClass}">${data || 'Unknown'}</span>`;
                                    }
                                },
                                { 
                                    data: 'date_enrolled',
                                    render: function(data) {
                                        return data ? new Date(data).toLocaleDateString('en-US', {
                                            year: 'numeric',
                                            month: 'short',
                                            day: 'numeric'
                                        }) : 'N/A';
                                    }
                                }
                            ],
                            responsive: true,
                            dom: '<"d-flex justify-content-between align-items-center mb-3"lf>rtip',
                            language: {
                                search: "Search archived students:",
                                lengthMenu: "Show _MENU_ students per page",
                                emptyTable: "No student records found for this academic year"
                            },
                            pageLength: 10,
                            order: [[0, 'asc']]
                        });
                        
                        // Show the modal
                        $('#yearDetailsModal').modal('show');
                    } else {
                        Swal.fire('Error', response.message || 'Failed to load archived year details', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Ajax error:', error);
                    Swal.fire('Error', 'Failed to connect to server', 'error');
                }
            });
        }

        // Add this event handler to clean up archive info when modal is hidden
        $('#yearDetailsModal').on('hidden.bs.modal', function () {
            // Remove the archive info alert if it exists
            $(this).find('.alert.alert-info').remove();
            // Reset modal title
            $(this).find('.modal-title').html(`
                <i class="fas fa-users mr-2"></i>
                Academic Year Enrollment Details
            `);
        });

        function deleteArchivedYear(id) {
            Swal.fire({
                title: 'Delete Archived Year?',
                text: "This action cannot be undone and will permanently delete this record.",
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
                        data: { 
                            action: 'delete_archived_year', 
                            archive_id: id 
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire('Deleted!', response.message, 'success');
                                $('#archivedYearsTable').DataTable().ajax.reload();
                                $('#academicYearTable').DataTable().ajax.reload();
                                loadDashboardStats();
                            } else {
                                Swal.fire('Error', response.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'Failed to connect to server', 'error');
                        }
                    });
                }
            });
        }
    </script>
</body>
</html> 