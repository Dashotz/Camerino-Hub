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

// Get current academic year
$year_query = "SELECT * FROM academic_years WHERE status = 'active' LIMIT 1";
$current_year = $db->query($year_query)->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports Dashboard - Admin</title>
    
    <!-- External CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/dashboard-shared.css">
    <style>
        .report-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }

        .report-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .report-card .card-header {
            background: none;
            border-bottom: 1px solid rgba(0,0,0,0.1);
            padding: 1.25rem;
        }

        .report-card .card-header h5 {
            margin: 0;
            color: #2c3e50;
            font-weight: 600;
        }

        .report-card .card-body {
            padding: 1.5rem;
        }

        .chart-container {
            position: relative;
            height: 300px;
            margin-bottom: 1rem;
        }

        .filter-section {
            background: #fff;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .filter-section .form-group {
            margin-bottom: 0;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #7f8c8d;
            font-size: 0.9rem;
        }

        .trend-indicator {
            font-size: 0.9rem;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            margin-left: 0.5rem;
        }

        .trend-up {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .trend-down {
            background-color: #ffebee;
            color: #c62828;
        }

        .export-btn {
            padding: 0.5rem 1rem;
            border-radius: 5px;
            font-size: 0.9rem;
        }

        .export-btn i {
            margin-right: 0.5rem;
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
                <h1>Reports Dashboard</h1>
                <p>Comprehensive analytics and reporting tools</p>
            </div>

            <!-- Filters Section -->
            <div class="filter-section">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Academic Year</label>
                            <select class="form-control" id="academicYear">
                                <?php
                                $years_query = "SELECT * FROM academic_years ORDER BY year_start DESC";
                                $years_result = $db->query($years_query);
                                while($year = $years_result->fetch_assoc()):
                                ?>
                                <option value="<?= $year['id'] ?>" <?= $year['status'] == 'active' ? 'selected' : '' ?>>
                                    <?= $year['school_year'] ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Grade Level</label>
                            <select class="form-control" id="gradeLevel">
                                <option value="">All Grades</option>
                                <?php for($i = 7; $i <= 12; $i++): ?>
                                <option value="<?= $i ?>">Grade <?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Section</label>
                            <select class="form-control" id="section">
                                <option value="">All Sections</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button class="btn btn-primary btn-block" onclick="generateReport()">
                                Generate Report
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row">
                <!-- Enrollment Trends -->
                <div class="col-md-6">
                    <div class="report-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5>Enrollment Trends</h5>
                            <button class="btn btn-sm btn-outline-secondary export-btn" onclick="exportChart('enrollment')">
                                <i class="fas fa-download"></i> Export
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="enrollmentChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Academic Performance -->
                <div class="col-md-6">
                    <div class="report-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5>Academic Performance</h5>
                            <button class="btn btn-sm btn-outline-secondary export-btn" onclick="exportChart('performance')">
                                <i class="fas fa-download"></i> Export
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="performanceChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Second Row of Charts -->
            <div class="row">
                <!-- Attendance Overview -->
                <div class="col-md-6">
                    <div class="report-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5>Attendance Overview</h5>
                            <button class="btn btn-sm btn-outline-secondary export-btn" onclick="exportChart('attendance')">
                                <i class="fas fa-download"></i> Export
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="attendanceChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Teacher Performance -->
                <div class="col-md-6">
                    <div class="report-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5>Teacher Performance Metrics</h5>
                            <button class="btn btn-sm btn-outline-secondary export-btn" onclick="exportChart('teacher')">
                                <i class="fas fa-download"></i> Export
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="teacherChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Reports Table -->
            <div class="report-card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Detailed Reports</h5>
                    <button class="btn btn-sm btn-outline-secondary export-btn" onclick="exportTable()">
                        <i class="fas fa-download"></i> Export to Excel
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="reportsTable" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Student Name</th>
                                    <th>Grade Level</th>
                                    <th>Section</th>
                                    <th>Attendance Rate</th>
                                    <th>Average Grade</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>

    <script>
        // Initialize charts and data
        let enrollmentChart, performanceChart, attendanceChart, teacherChart;
        let reportsTable;

        $(document).ready(function() {
            initializeCharts();
            initializeTable();
            loadInitialData();

            // Handle grade level change
            $('#gradeLevel').change(function() {
                loadSections($(this).val());
            });
        });

        function initializeCharts() {
            // Enrollment Chart
            enrollmentChart = new Chart(document.getElementById('enrollmentChart'), {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Total Enrollees',
                        data: [],
                        borderColor: '#3498db',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            // Performance Chart
            performanceChart = new Chart(document.getElementById('performanceChart'), {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Average Grade',
                        data: [],
                        backgroundColor: '#2ecc71'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            // Attendance Chart
            attendanceChart = new Chart(document.getElementById('attendanceChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Present', 'Absent', 'Late'],
                    datasets: [{
                        data: [],
                        backgroundColor: ['#2ecc71', '#e74c3c', '#f1c40f']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            // Teacher Performance Chart
            teacherChart = new Chart(document.getElementById('teacherChart'), {
                type: 'radar',
                data: {
                    labels: ['Attendance', 'Grades Submission', 'Student Feedback', 'Activity Creation', 'Communication'],
                    datasets: [{
                        label: 'Performance Metrics',
                        data: [],
                        borderColor: '#9b59b6',
                        backgroundColor: 'rgba(155, 89, 182, 0.2)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        r: {
                            beginAtZero: true,
                            max: 100
                        }
                    }
                }
            });
        }

        function initializeTable() {
            reportsTable = $('#reportsTable').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: 'handlers/reports_handler.php',
                    type: 'GET',
                    data: function(d) {
                        d.action = 'get_detailed_reports';
                        d.academic_year = $('#academicYear').val();
                        d.grade_level = $('#gradeLevel').val();
                        d.section = $('#section').val();
                    }
                },
                columns: [
                    { data: 'student_name' },
                    { data: 'grade_level' },
                    { data: 'section' },
                    { 
                        data: 'attendance_rate',
                        render: function(data) {
                            return (data || 0).toFixed(2) + '%';
                        }
                    },
                    { 
                        data: 'average_grade',
                        render: function(data) {
                            return (data || 0).toFixed(2);
                        }
                    },
                    {
                        data: 'status',
                        render: function(data) {
                            const colors = {
                                'Excellent': 'success',
                                'Good': 'info',
                                'Average': 'warning',
                                'Needs Improvement': 'danger'
                            };
                            return `<span class="badge badge-${colors[data] || 'secondary'}">${data || 'N/A'}</span>`;
                        }
                    }
                ],
                order: [[4, 'desc']],
                responsive: true
            });
        }

        // Add the rest of your JavaScript functions here
        // (loadInitialData, generateReport, exportChart, exportTable, etc.)
    </script>
</body>
</html>
