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
    <link rel="icon" href="../images/light-logo.png">
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
    </style>
</head>
<body>
    <?php include 'includes/navigation.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="welcome-section">
                <h1>Reports Dashboard</h1>
                <p>Comprehensive analytics and reporting tools</p>
            </div>

            <!-- Filters Section -->
            <div class="filter-section">
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Academic Year</label>
                            <select class="form-control" id="academicYear">
                                <?php
                                $year_query = "SELECT * FROM academic_years ORDER BY school_year DESC";
                                $years = $db->query($year_query);
                                while ($year = $years->fetch_assoc()):
                                ?>
                                <option value="<?= htmlspecialchars($year['id']) ?>" 
                                        <?= $year['status'] == 'active' ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($year['school_year']) ?>
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
                                <?php for($i = 7; $i <= 10; $i++): ?>
                                <option value="<?= $i ?>">Grade <?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Section</label>
                            <select class="form-control" id="section" disabled>
                                <option value="">Select Grade Level First</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button class="btn btn-primary btn-block" id="generateReport">
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
                        <div class="card-header">
                            <h5>Enrollment Trends</h5>
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
                        <div class="card-header">
                            <h5>Academic Performance</h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="performanceChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Teacher Performance -->
            <div class="row">
                <div class="col-md-12">
                    <div class="report-card">
                        <div class="card-header">
                            <h5>Teacher Performance Metrics</h5>
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
                <div class="card-header">
                    <h5>Detailed Reports</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="reportsTable" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Student Name</th>
                                    <th>Grade Level</th>
                                    <th>Section</th>
                                    <th>Average Grade</th>
                                    <th>Academic Status</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
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

    <script>
        let enrollmentChart, performanceChart, teacherChart;
        let reportsTable;

        $(document).ready(function() {
            console.log('Initial grade level:', $('#gradeLevel').val());
            console.log('Initial academic year:', $('#academicYear').val());

            initializeCharts();
            initializeTable();

            $('#gradeLevel').change(function() {
                var selectedGrade = $(this).val();
                console.log('Grade level changed to:', selectedGrade);
                loadSections(selectedGrade);
            });

            // Load sections on page load if grade level is selected
            if ($('#gradeLevel').val()) {
                loadSections($('#gradeLevel').val());
            }

            $('#academicYear, #gradeLevel, #section').change(function() {
                // Optional: Clear current data
                if (reportsTable) {
                    reportsTable.clear().draw();
                }
            });

            // Add click handler for generate button
            $('#generateReport').click(function(e) {
                e.preventDefault();
                generateReport();
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

            // Teacher Performance Chart
            teacherChart = new Chart(document.getElementById('teacherChart'), {
                type: 'radar',
                data: {
                    labels: ['Grades Submission', 'Student Feedback', 'Activity Creation', 'Communication'],
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
            if ($.fn.DataTable.isDataTable('#reportsTable')) {
                $('#reportsTable').DataTable().destroy();
            }
            
            reportsTable = $('#reportsTable').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: 'handlers/reports_handler.php',
                    type: 'GET',
                    data: function(d) {
                        return {
                            action: 'get_detailed_reports',
                            academic_year: $('#academicYear').val(),
                            grade_level: $('#gradeLevel').val(),
                            section: $('#section').val()
                        };
                    },
                    dataSrc: 'data'
                },
                columns: [
                    { 
                        data: 'student_name',
                        render: function(data) {
                            return data || 'N/A';
                        }
                    },
                    { data: 'grade_level' },
                    { data: 'section_name' },
                    { 
                        data: 'average_grade',
                        render: function(data) {
                            return parseFloat(data || 0).toFixed(2);
                        }
                    },
                    {
                        data: 'academic_status',
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
                order: [[3, 'desc']],
                responsive: true,
                pageLength: 10,
                language: {
                    emptyTable: 'No data available',
                    processing: 'Loading...'
                }
            });
        }

        function loadSections(gradeLevel) {
            const academicYear = $('#academicYear').val();
            
            if (!gradeLevel || !academicYear) {
                $('#section').html('<option value="">All Sections</option>');
                return;
            }

            $.ajax({
                url: 'handlers/reports_handler.php',
                type: 'GET',
                data: {
                    action: 'get_sections',
                    grade_level: gradeLevel,
                    academic_year: academicYear
                },
                beforeSend: function() {
                    $('#section').html('<option value="">Loading sections...</option>');
                },
                success: function(response) {
                    $('#section').html(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading sections:', error);
                    $('#section').html('<option value="">Error loading sections</option>');
                }
            });
        }

        function generateReport() {
            const academicYear = $('#academicYear').val();
            const gradeLevel = $('#gradeLevel').val();
            const section = $('#section').val();

            // Show loading state
            $('#generateReport').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Loading...');

            // Reload DataTable with new filters
            if (reportsTable) {
                reportsTable.ajax.reload(function() {
                    // Re-enable button after data loads
                    $('#generateReport').prop('disabled', false).html('Generate Report');
                });
            }

            // Update charts
            $.ajax({
                url: 'handlers/reports_handler.php',
                type: 'GET',
                data: {
                    action: 'get_detailed_reports',
                    academic_year: academicYear,
                    grade_level: gradeLevel,
                    section: section
                },
                success: function(response) {
                    if (response.data) {
                        updateCharts(response.data);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error generating report:', error);
                    alert('Error generating report. Please try again.');
                    $('#generateReport').prop('disabled', false).html('Generate Report');
                }
            });
        }

        function updateCharts(data) {
            // Update Enrollment Chart
            const enrollmentData = processEnrollmentData(data);
            enrollmentChart.data.labels = enrollmentData.labels;
            enrollmentChart.data.datasets[0].data = enrollmentData.values;
            enrollmentChart.update();

            // Update Performance Chart
            const performanceData = processPerformanceData(data);
            performanceChart.data.labels = performanceData.labels;
            performanceChart.data.datasets[0].data = performanceData.values;
            performanceChart.update();

            // Helper functions to process data
            function processEnrollmentData(data) {
                const sections = [...new Set(data.map(item => item.section_name))];
                const counts = sections.map(section => 
                    data.filter(item => item.section_name === section).length
                );
                return {
                    labels: sections,
                    values: counts
                };
            }

            function processPerformanceData(data) {
                const gradeRanges = ['90-100', '80-89', '75-79', 'Below 75'];
                const counts = gradeRanges.map(range => {
                    switch(range) {
                        case '90-100':
                            return data.filter(item => item.average_grade >= 90).length;
                        case '80-89':
                            return data.filter(item => item.average_grade >= 80 && item.average_grade < 90).length;
                        case '75-79':
                            return data.filter(item => item.average_grade >= 75 && item.average_grade < 80).length;
                        case 'Below 75':
                            return data.filter(item => item.average_grade < 75).length;
                    }
                });
                return {
                    labels: gradeRanges,
                    values: counts
                };
            }
        }
    </script>
</body>
</html>
