<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['teacher_id'])) {
    header("Location: Teacher-Login.php");
    exit();
}

$db = new DbConnector();
$teacher_id = $_SESSION['teacher_id'];

// Get teacher's classes
$classes_query = "SELECT 
    ss.id as class_id,
    CONCAT(sec.section_name, ' - ', sub.subject_code) as class_name,
    sub.subject_name,
    ay.year_start,
    ay.year_end,
    sec.section_name,
    sub.subject_code
FROM section_subjects ss
JOIN sections sec ON ss.section_id = sec.section_id
JOIN subjects sub ON ss.subject_id = sub.id
JOIN academic_years ay ON ss.academic_year_id = ay.id
WHERE ss.teacher_id = ? 
AND ss.status = 'active'
ORDER BY sec.section_name, sub.subject_code";

$stmt = $db->prepare($classes_query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$classes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$selected_class = isset($_GET['class_id']) ? $_GET['class_id'] : null;

if ($selected_class) {
    // Get performance data for selected class
    $performance_query = "SELECT 
        s.student_id,
        s.firstname,
        s.lastname,
        s.cys,
        COUNT(DISTINCT sas.submission_id) as submissions_count,
        COALESCE(AVG(sas.points), 0) as average_grade,
        COUNT(DISTINCT CASE WHEN a.status = 'present' THEN a.id END) as present_count,
        COUNT(DISTINCT a.id) as total_attendance,
        COALESCE(
            (COUNT(DISTINCT CASE WHEN a.status = 'present' THEN a.id END) * 100.0) / 
            NULLIF(COUNT(DISTINCT a.id), 0),
            0
        ) as attendance_rate
    FROM student s
    JOIN student_sections ss ON s.student_id = ss.student_id
    JOIN sections sec ON ss.section_id = sec.section_id
    JOIN section_subjects ssub ON sec.section_id = ssub.section_id
    LEFT JOIN activities act ON ssub.id = act.section_subject_id
    LEFT JOIN student_activity_submissions sas ON s.student_id = sas.student_id AND act.activity_id = sas.activity_id
    LEFT JOIN attendance a ON s.student_id = a.student_id AND ssub.id = a.section_subject_id
    WHERE ssub.id = ?
    AND ss.status = 'active'
    AND s.status = 'active'
    GROUP BY 
        s.student_id, 
        s.firstname, 
        s.lastname,
        s.cys
    ORDER BY s.lastname, s.firstname";

    $stmt = $db->prepare($performance_query);
    $stmt->bind_param("i", $selected_class);
    $stmt->execute();
    $performance_data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getDateRange($range_type) {
    $today = new DateTime();
    
    switch($range_type) {
        case 'This Month':
            $start = new DateTime('first day of this month');
            $end = new DateTime('last day of this month');
            break;
        case 'Last Month':
            $start = new DateTime('first day of last month');
            $end = new DateTime('last day of last month');
            break;
        case 'This Week':
            $start = new DateTime('monday this week');
            $end = new DateTime('sunday this week');
            break;
        case 'Last Week':
            $start = new DateTime('monday last week');
            $end = new DateTime('sunday last week');
            break;
        default:
            $start = new DateTime('first day of this month');
            $end = new DateTime('last day of this month');
    }
    
    return [
        'start' => $start->format('Y-m-d'),
        'end' => $end->format('Y-m-d')
    ];
}

// Usage in your queries
$date_range = getDateRange($selected_range); // $selected_range would be 'This Month', 'Last Month', etc.
$start_date = $date_range['start'];
$end_date = $date_range['end'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - CamerinoHub</title>
    
    <!-- External CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/1.6.5/css/buttons.bootstrap4.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/dashboard-shared.css">
    <style>
        .report-card {
            margin-bottom: 1.5rem;
        }
        .stat-card {
            padding: 1.5rem;
            border-radius: 0.5rem;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
        }
        .performance-table th {
            background-color: #f8f9fa;
        }
        .dashboard-container {
            display: flex;
            min-height: 100vh;
            padding-top: 60px; /* Match your navigation height */
        }

        .main-content {
            flex: 1;
            padding: 20px;
            background: #f8f9fa;
            margin-left: 250px; /* Match your sidebar width */
        }

        .content-header {
            margin-bottom: 25px;
        }

        .content-header h1 {
            font-size: 1.8rem;
            margin-bottom: 5px;
            color: #2c3e50;
        }

        .content-header p {
            color: #6c757d;
            margin-bottom: 0;
        }

        /* Fix the navigation gap */
        .navigation {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
        }

        /* Fix the sidebar */
        .sidebar {
            position: fixed;
            top: 60px; /* Match navigation height */
            left: 0;
            bottom: 0;
            width: 250px;
            background: #fff;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            z-index: 1020;
            overflow-y: auto;
        }

        /* Improve card styling */
        .stat-card {
            padding: 1.5rem;
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: transform 0.2s;
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            line-height: 1.2;
        }

        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
            font-weight: 500;
        }

        /* Improve table styling */
        .performance-table {
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
        }

        .performance-table th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
        }

        .performance-table .progress {
            height: 8px;
            border-radius: 4px;
            background-color: #e9ecef;
        }

        .performance-table .badge {
            padding: 6px 12px;
            font-weight: 500;
        }

        /* Filter section styling */
        .filter-section {
            background: #fff;
            border-radius: 8px;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .filter-section select {
            min-width: 200px;
        }

        .filter-section .btn-primary {
            padding: 0.375rem 1rem;
        }

        /* DataTables customization */
        .dataTables_wrapper .btn-group {
            margin-bottom: 1rem;
        }

        .dataTables_wrapper .btn {
            margin-right: 0.5rem;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 15px;
            }

            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .stat-card {
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/navigation.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="content-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1>Reports</h1>
                        <p>View and generate class performance reports</p>
                    </div>
                </div>
            </div>

            <!-- Report Filters -->
            <div class="filter-section">
                <form method="GET" class="form-inline">
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <select name="class_id" class="form-control mr-2 mb-2">
                            <option value="">Select Class</option>
                            <?php foreach ($classes as $class): ?>
                                <option value="<?php echo $class['class_id']; ?>" 
                                    <?php echo ($selected_class == $class['class_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($class['class_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <select name="date_range" class="form-control mr-2 mb-2">
                            <option value="this_month">This Month</option>
                            <option value="last_month">Last Month</option>
                            <option value="this_week">This Week</option>
                            <option value="last_week">Last Week</option>
                        </select>

                        <button type="submit" class="btn btn-primary mb-2">
                            <i class="fas fa-filter"></i> Apply Filters
                        </button>
                    </div>
                </form>
            </div>

            <?php if ($selected_class && !empty($performance_data)): ?>
                <!-- Class Statistics -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="stat-card text-center">
                            <div class="stat-value text-primary">
                                <?php 
                                    $avg_grade = array_reduce($performance_data, function($carry, $item) {
                                        return $carry + $item['average_grade'];
                                    }, 0) / count($performance_data);
                                    echo number_format($avg_grade, 1);
                                ?>%
                            </div>
                            <div class="stat-label">Average Class Grade</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card text-center">
                            <div class="stat-value text-success">
                                <?php 
                                    $avg_attendance = array_reduce($performance_data, function($carry, $item) {
                                        return $carry + ($item['present_count'] / max(1, $item['total_attendance']) * 100);
                                    }, 0) / count($performance_data);
                                    echo number_format($avg_attendance, 1);
                                ?>%
                            </div>
                            <div class="stat-label">Average Attendance Rate</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card text-center">
                            <div class="stat-value text-info">
                                <?php echo count($performance_data); ?>
                            </div>
                            <div class="stat-label">Total Students</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card text-center">
                            <div class="stat-value text-warning">
                                <?php 
                                    $avg_submissions = array_reduce($performance_data, function($carry, $item) {
                                        return $carry + $item['submissions_count'];
                                    }, 0) / count($performance_data);
                                    echo number_format($avg_submissions, 1);
                                ?>
                            </div>
                            <div class="stat-label">Average Submissions</div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Performance Table -->
                <div class="card mt-4">
                    <div class="card-body">
                        <table id="performanceTable" class="table table-striped performance-table">
                            <thead>
                                <tr>
                                    <th>Student Name</th>
                                    <th>Average Grade</th>
                                    <th>Attendance Rate</th>
                                    <th>Submissions</th>
                                    <th>Performance Level</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($performance_data as $student): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($student['lastname'] . ', ' . $student['firstname']); ?></td>
                                        <td>
                                            <div class="progress">
                                                <div class="progress-bar bg-primary" role="progressbar" 
                                                     style="width: <?php echo $student['average_grade']; ?>%">
                                                    <?php echo number_format($student['average_grade'], 1); ?>%
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php 
                                                $attendance_rate = ($student['total_attendance'] > 0) 
                                                    ? ($student['present_count'] / $student['total_attendance'] * 100) 
                                                    : 0;
                                            ?>
                                            <div class="progress">
                                                <div class="progress-bar bg-success" role="progressbar" 
                                                     style="width: <?php echo $attendance_rate; ?>%">
                                                    <?php echo number_format($attendance_rate, 1); ?>%
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo $student['submissions_count']; ?></td>
                                        <td>
                                            <?php
                                                $performance_level = '';
                                                $badge_class = '';
                                                if ($student['average_grade'] >= 90) {
                                                    $performance_level = 'Excellent';
                                                    $badge_class = 'badge-success';
                                                } elseif ($student['average_grade'] >= 80) {
                                                    $performance_level = 'Good';
                                                    $badge_class = 'badge-primary';
                                                } elseif ($student['average_grade'] >= 75) {
                                                    $performance_level = 'Satisfactory';
                                                    $badge_class = 'badge-info';
                                                } else {
                                                    $performance_level = 'Needs Improvement';
                                                    $badge_class = 'badge-warning';
                                                }
                                            ?>
                                            <span class="badge <?php echo $badge_class; ?>">
                                                <?php echo $performance_level; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php elseif ($selected_class): ?>
                <div class="alert alert-info">
                    No performance data available for the selected class and date range.
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    Please select a class to view performance reports.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.print.min.js"></script>
    
    <script>
    $(document).ready(function() {
        $('#performanceTable').DataTable({
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="fas fa-file-excel"></i> Export Excel',
                    className: 'btn btn-success btn-sm',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4]
                    }
                },
                {
                    extend: 'pdf',
                    text: '<i class="fas fa-file-pdf"></i> Export PDF',
                    className: 'btn btn-danger btn-sm',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4]
                    }
                },
                {
                    extend: 'print',
                    text: '<i class="fas fa-print"></i> Print',
                    className: 'btn btn-info btn-sm',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4]
                    }
                }
            ]
        });
    });
    </script>
</body>
</html>
