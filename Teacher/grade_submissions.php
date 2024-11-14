<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['teacher_id'])) {
    header("Location: Teacher-Login.php");
    exit();
}

$db = new DbConnector();
$teacher_id = $_SESSION['teacher_id'];

// Get all sections and subjects taught by the teacher
$query = "
    SELECT DISTINCT 
        ss.id as section_subject_id,
        s.section_name,
        sub.subject_name,
        sub.subject_code
    FROM section_subjects ss
    JOIN sections s ON ss.section_id = s.section_id
    JOIN subjects sub ON ss.subject_id = sub.id
    WHERE ss.teacher_id = ? AND ss.status = 'active'
    ORDER BY s.section_name, sub.subject_name";

$stmt = $db->prepare($query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$classes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grade Submissions - Teacher Dashboard</title>
    
    <!-- External CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/dashboard-shared.css">
    
    <style>
    .card {
        border: none;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        margin-bottom: 1.5rem;
    }

    .card-header {
        background-color: #fff;
        border-bottom: 1px solid #eee;
        padding: 1rem 1.5rem;
    }

    .card-title {
        color: #333;
        font-weight: 500;
        margin: 0;
    }

    .table {
        margin-bottom: 0;
    }

    .table th {
        border-top: none;
        color: #666;
        font-weight: 500;
        text-transform: uppercase;
        font-size: 0.85rem;
    }

    .table td {
        vertical-align: middle;
        color: #333;
    }

    .badge {
        padding: 0.5rem 0.75rem;
        font-weight: 500;
        font-size: 0.85rem;
    }

    .progress {
        height: 0.8rem;
        border-radius: 1rem;
        background-color: #e9ecef;
    }

    .progress-bar {
        background-color: #4e73df;
        border-radius: 1rem;
        font-size: 0.7rem;
        line-height: 0.8rem;
    }

    .btn-primary {
        background-color: #4e73df;
        border-color: #4e73df;
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
    }

    .btn-primary:hover {
        background-color: #2e59d9;
        border-color: #2653d4;
    }

    .badge.bg-warning {
        background-color: #f6c23e !important;
        color: #fff;
    }

    .badge.bg-primary {
        background-color: #4e73df !important;
        color: #fff;
    }

    .badge.bg-success {
        background-color: #1cc88a !important;
        color: #fff;
    }

    .table-hover tbody tr:hover {
        background-color: #f8f9fc;
    }

    .dashboard-container {
        display: flex;
        min-height: 100vh;
    }

    .main-content {
        flex: 1;
        padding: 2rem;
        background-color: #f8f9fc;
    }

    @media (max-width: 768px) {
        .card-body {
            padding: 1rem;
        }

        .table {
            font-size: 0.875rem;
        }

        .badge {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
    }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <?php include 'includes/navigation.php'; ?>
            
            <div class="container-fluid">
                <h1 class="h3 mb-4 text-gray-800">Grade Submissions</h1>
                
                <?php foreach ($classes as $class): ?>
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <?php echo $class['section_name'] . ' - ' . $class['subject_name']; ?>
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php
                            // Get all activities for this class
                            $activities_query = "
                                SELECT 
                                    a.activity_id,
                                    a.title,
                                    a.type,
                                    a.points as max_points,
                                    COUNT(DISTINCT sas.submission_id) as submission_count,
                                    COUNT(DISTINCT CASE WHEN sas.points IS NOT NULL THEN sas.submission_id END) as graded_count
                                FROM activities a
                                LEFT JOIN student_activity_submissions sas ON a.activity_id = sas.activity_id
                                WHERE a.section_subject_id = ? AND a.status = 'active'
                                GROUP BY a.activity_id
                                ORDER BY a.created_at DESC";
                            
                            $stmt = $db->prepare($activities_query);
                            $stmt->bind_param("i", $class['section_subject_id']);
                            $stmt->execute();
                            $activities = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                            ?>

                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Type</th>
                                            <th>Max Points</th>
                                            <th>Submissions</th>
                                            <th>Graded</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($activities as $activity): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($activity['title']); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php 
                                                        echo match($activity['type']) {
                                                            'quiz' => 'warning',
                                                            'activity' => 'primary',
                                                            'assignment' => 'success'
                                                        };
                                                    ?>">
                                                        <?php echo ucfirst($activity['type']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo $activity['max_points']; ?></td>
                                                <td><?php echo $activity['submission_count']; ?></td>
                                                <td>
                                                    <?php 
                                                    $progress = $activity['submission_count'] > 0 
                                                        ? ($activity['graded_count'] / $activity['submission_count']) * 100 
                                                        : 0;
                                                    ?>
                                                    <div class="progress">
                                                        <div class="progress-bar" role="progressbar" 
                                                             style="width: <?php echo $progress; ?>%"
                                                             aria-valuenow="<?php echo $progress; ?>" 
                                                             aria-valuemin="0" 
                                                             aria-valuemax="100">
                                                            <?php echo $activity['graded_count']; ?>/<?php echo $activity['submission_count']; ?>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <a href="view_submissions.php?id=<?php echo $activity['activity_id']; ?>" 
                                                       class="btn btn-sm btn-primary">
                                                        View Submissions
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html> 