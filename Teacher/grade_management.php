<?php
session_start();
require_once('../db/dbConnector.php');
$db = new DbConnector();

// Check if teacher is logged in
if (!isset($_SESSION['teacher_id'])) {
    header("Location: Teacher-Login.php");
    exit();
}

$teacher_id = $_SESSION['teacher_id'];

// Get sections and subjects taught by the teacher
$sections_query = "SELECT DISTINCT 
    s.section_id, 
    s.section_name,
    ss.subject_id,
    sub.subject_name
FROM sections s
JOIN section_subjects ss ON s.section_id = ss.section_id
JOIN subjects sub ON ss.subject_id = sub.id
WHERE ss.teacher_id = ? 
AND ss.status = 'active'
AND ss.academic_year_id = (SELECT id FROM academic_years WHERE status = 'active' LIMIT 1)";

$stmt = $db->prepare($sections_query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$sections_result = $stmt->get_result();

// Add this helper function
function findSubmission($submissions, $studentId, $activityId) {
    foreach ($submissions as $submission) {
        if ($submission['student_id'] == $studentId && 
            $submission['activity_id'] == $activityId) {
            return $submission;
        }
    }
    return null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grade Management - CamerinoHub</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href="css/dashboard-shared.css" rel="stylesheet">
	<link rel="icon" href="../images/light-logo.png">
    <style>
        /* Navigation and Sidebar Adjustments */
        body {
            padding-top: 60px; /* Height of the top navigation */
            margin: 0;
            min-height: 100vh;
            background: #f8f9fa;
        }

        /* Main Content Layout */
        .main-content {
            margin-left: 250px;
            padding: 1.5rem;
            min-height: calc(100vh - 60px);
            background: #f8f9fa;
            position: relative;
            width: calc(100% - 250px);
            transition: all 0.3s ease;
        }

        /* Sidebar Adjustments */
        .sidebar {
            position: fixed;
            top: 60px;
            left: 0;
            width: 250px;
            height: calc(100vh - 60px);
            background: #fff;
            z-index: 1000;
            overflow-y: auto;
        }

        /* Navigation Adjustments */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 60px;
            z-index: 1030;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        /* Container Adjustments */
        .container-fluid {
            padding: 0 1.5rem;
            max-width: 100%;
        }

        /* Rest of your existing styles... */
        .card {
            background: #fff;
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
        }

        /* Your other existing styles remain the same... */

        /* Update Responsive Design */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 70px;
                width: calc(100% - 70px);
                padding: 1rem;
            }

            .sidebar {
                width: 41px;
                transform: translateX(0);
            }

            .sidebar .nav-link span {
                display: none;
            }

            .sidebar .nav-link i {
                margin: 0;
                width: 100%;
                text-align: center;
                font-size: 1.2rem;
            }

            .help-card {
                padding: 10px;
            }

            .help-card p,
            .help-card h6 {
                display: none;
            }
        }

        /* Add styles for sidebar toggle button if needed */
        .sidebar-toggle {
            display: none;
        }

        @media (max-width: 768px) {
            .sidebar-toggle {
                display: block;
                position: fixed;
                left: 1rem;
                top: 1rem;
                z-index: 1040;
            }
        }

        /* Card Styles */
        .card {
            background: #fff;
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
        }

        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #eee;
            padding: 1rem 1.5rem;
            border-radius: 10px 10px 0 0;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Form Controls */
        .form-control {
            border-radius: 5px;
            border: 1px solid #ddd;
            padding: 0.5rem 1rem;
            min-width: 200px;
            height: calc(1.5em + 1rem + 2px);
        }

        .form-inline {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        /* Button Styles */
        .btn-primary {
            background-color: #007bff;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 5px;
            transition: all 0.3s ease;
            height: calc(1.5em + 1rem + 2px);
            line-height: 1.5;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            transform: translateY(-1px);
        }

        /* Table Styles */
        .grade-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 1rem;
        }

        .grade-table th {
            background-color: #f8f9fa;
            color: #495057;
            font-weight: 600;
            padding: 1rem;
            border-bottom: 2px solid #dee2e6;
            white-space: nowrap;
        }

        .grade-table td {
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #dee2e6;
        }

        /* Activity Type Badges */
        .activity-type {
            font-size: 0.85rem;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-weight: 500;
            display: inline-block;
            margin: 0.25rem 0;
        }

        .type-quiz { 
            background-color: #e3f2fd; 
            color: #1565c0;
        }

        .type-activity { 
            background-color: #f1f8e9; 
            color: #2e7d32;
        }

        .type-assignment { 
            background-color: #fce4ec; 
            color: #c2185b;
        }

        /* Grade Input Fields */
        .grade-input {
            width: 70px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 0.25rem;
            transition: all 0.3s ease;
        }

        .grade-input:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
        }

        /* Status Badges */
        .status-badge {
            font-size: 0.85rem;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-weight: 500;
            display: inline-block;
            margin-top: 0.5rem;
        }

        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }

        .badge-warning {
            background-color: #fff3cd;
            color: #856404;
        }

        .badge-info {
            background-color: #cce5ff;
            color: #004085;
        }

        /* Table Row Hover Effect */
        .student-row:hover {
            background-color: #f8f9fa;
        }

        /* Average Column */
        .average {
            font-weight: 600;
            color: #007bff;
        }

        /* Loading State */
        .loading {
            position: relative;
            min-height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .loading:after {
            content: 'Loading...';
            font-size: 1.2rem;
            color: #6c757d;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .main-content {
                padding: 1.5rem;
            }
        }

        @media (max-width: 992px) {
            .form-inline {
                flex-wrap: wrap;
            }
            
            .form-control {
                width: 100%;
                margin-bottom: 1rem;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                width: 100%;
                padding: 1rem;
                left: 7%;
            }

            .card {
                margin-bottom: 1rem;
            }

            .table-responsive {
                margin: 0 -1rem;
                padding: 0 1rem;
                overflow-x: auto;
            }

            .grade-table {
                min-width: 800px;
            }
        }


        /* Search Bar Styles */
.search-container {
    position: relative;
    max-width: 300px;
}

.search-input {
    width: 100%;
    padding: 0.5rem 1rem 0.5rem 2.5rem;
    border: 1px solid #e0e0e0;
    border-radius: 20px;
    background: #f8f9fa;
    transition: all 0.3s ease;
}

.search-input:focus {
    outline: none;
    border-color: #3498db;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

.search-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
}

/* Header Layout */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.page-title {
    margin: 0;
    color: #2c3e50;
    font-size: 1.75rem;
    font-weight: 500;
}

/* Add these styles to your existing CSS */
.average {
    font-size: 1.1em;
    font-weight: bold !important;
}

.text-danger {
    color: #dc3545 !important;
}

.text-warning {
    color: #ffc107 !important;
}

.text-success {
    color: #28a745 !important;
}

.grade-table td {
    vertical-align: middle !important;
}
    </style>
</head>
<body>
    <!-- Navigation bar -->
    <?php include 'includes/navigation.php'; ?>
    
    <!-- Sidebar -->
    <?php include 'includes/sidebar.php'; ?>
    
    <!-- Main content -->
    <div class="main-content">
        <div class="container-fluid">
            <h2 class="mb-4">Grade Management</h2>

            <!-- Section and Subject Selection -->
            <div class="card mb-4">
                <div class="card-body">
                    <form id="sectionForm" class="form-inline">
                        <select class="form-control mr-2" id="sectionSelect" required>
                            <option value="">Select Section & Subject</option>
                            <?php while ($section = $sections_result->fetch_assoc()): ?>
                                <option value="<?php echo $section['section_id'] . ',' . $section['subject_id']; ?>">
                                    <?php echo $section['section_name'] . ' - ' . $section['subject_name']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <button type="submit" class="btn btn-primary">View Grades</button>
                    </form>
                </div>
            </div>

            <!-- Grades Table -->
            <div class="card">
                <div class="card-body">
                    <div id="gradesContent">
                        <!-- Grades will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#sectionForm').on('submit', function(e) {
            e.preventDefault();
            const [sectionId, subjectId] = $('#sectionSelect').val().split(',');
            
            $.ajax({
                url: 'handlers/get_grades.php',
                method: 'GET',
                data: { 
                    section_id: sectionId,
                    subject_id: subjectId
                },
                success: function(response) {
                    $('#gradesContent').html(response);
                    initializeGradeHandlers();
                }
            });
        });

        function initializeGradeHandlers() {
            $('.grade-input').on('change', function() {
                const submissionId = $(this).data('submission-id');
                const points = $(this).val();
                const maxPoints = $(this).data('max-points');
                const activityType = $(this).data('activity-type');

                if (activityType === 'activity' || activityType === 'assignment') {
                    if (points > 100) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Invalid Grade',
                            text: 'Maximum grade for activities and assignments is 100'
                        });
                        $(this).val('');
                        return;
                    }
                } else if (activityType === 'quiz') {
                    if (points > maxPoints) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Invalid Grade',
                            text: `Maximum points allowed: ${maxPoints}`
                        });
                        $(this).val('');
                        return;
                    }
                }

                $.ajax({
                    url: 'handlers/update_grade.php',
                    method: 'POST',
                    data: {
                        submission_id: submissionId,
                        points: points
                    },
                    success: function(response) {
                        if (response.success) {
                            calculateAverages();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to update grade'
                            });
                        }
                    }
                });
            });
        }

        function calculateAverages() {
            $('.student-row').each(function() {
                let total = 0;
                let count = 0;
                
                // Count all activities (including missing/unsubmitted)
                $(this).find('td:not(:first-child):not(:last-child)').each(function() {
                    const activityType = $(this).find('.grade-input').data('activity-type');
                    const maxPoints = parseFloat($(this).find('.grade-input').data('max-points')) || 
                                    (activityType === 'quiz' ? $(this).find('.grade-input').attr('max') : 100);
                    
                    let points = 0; // Default to 0 for missing/unsubmitted
                    
                    // If there's an input with a value, use that instead
                    if ($(this).find('.grade-input').length && $(this).find('.grade-input').val()) {
                        points = parseFloat($(this).find('.grade-input').val());
                    }
                    
                    // Calculate percentage based on activity type
                    if (activityType === 'quiz') {
                        total += (points / maxPoints) * 100;
                    } else {
                        total += points; // Activities and assignments are already in percentage
                    }
                    count++;
                });

                // Calculate and display average
                const average = count ? (total / count).toFixed(2) : 'N/A';
                $(this).find('.average').text(average + '%');
                
                // Add color coding for the average
                const averageCell = $(this).find('.average');
                averageCell.removeClass('text-danger text-warning text-success');
                if (average !== 'N/A') {
                    if (average < 75) {
                        averageCell.addClass('text-danger');
                    } else if (average < 85) {
                        averageCell.addClass('text-warning');
                    } else {
                        averageCell.addClass('text-success');
                    }
                }
            });
        }
    });
    </script>
</body>
</html>
