<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['teacher_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['student_id'])) {
    header("Location: manage_students.php");
    exit();
}

$db = new DbConnector();
$student_id = intval($_GET['student_id']);

// Fetch student details
$student_query = "
    SELECT s.*, ss.section_name
    FROM student s
    LEFT JOIN student_sections ssc ON s.student_id = ssc.student_id AND ssc.status = 'active'
    LEFT JOIN sections ss ON ssc.section_id = ss.section_id
    WHERE s.student_id = ?";

$stmt = $db->prepare($student_query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

// Fetch student's activities and submissions
$activities_query = "
    SELECT 
        a.*,
        sas.points,
        sas.submitted_at,
        sas.status as submission_status,
        s.subject_name
    FROM activities a
    JOIN section_subjects ss ON a.section_subject_id = ss.id
    JOIN subjects s ON ss.subject_id = s.id
    LEFT JOIN student_activity_submissions sas 
        ON a.activity_id = sas.activity_id 
        AND sas.student_id = ?
    WHERE ss.section_id IN (
        SELECT section_id 
        FROM student_sections 
        WHERE student_id = ? AND status = 'active'
    )
    ORDER BY a.due_date DESC";

$stmt = $db->prepare($activities_query);
$stmt->bind_param("ii", $student_id, $student_id);
$stmt->execute();
$activities = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Progress - <?php echo htmlspecialchars($student['firstname'] . ' ' . $student['lastname']); ?></title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href="css/dashboard-shared.css" rel="stylesheet">
	<link rel="icon" href="../images/light-logo.png">
    <style>
        .back-button {
            margin-bottom: 20px;
        }
        
        .back-button a {
            color: #1967d2;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .back-button a:hover {
            color: #1557b0;
        }
        
        .content-wrapper {
            margin-left: 250px; /* Adjust based on your sidebar width */
            padding: 20px;
        }

        @media (max-width: 768px) {
            .content-wrapper {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/navigation.php'; ?>

    
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="back-button">
                <a href="manage_students.php">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Students List</span>
                </a>
            </div>

            <div class="row mb-4">
                <div class="col-md-12">
                    <h2>Student Progress</h2>
                    <h4><?php echo htmlspecialchars($student['firstname'] . ' ' . $student['lastname']); ?></h4>
                    <p class="text-muted">
                        Section: <?php echo htmlspecialchars($student['section_name'] ?? 'Not Assigned'); ?>
                    </p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Activities and Submissions</h5>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Subject</th>
                                            <th>Activity</th>
                                            <th>Type</th>
                                            <th>Due Date</th>
                                            <th>Status</th>
                                            <th>Score</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($activities as $activity): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($activity['subject_name']); ?></td>
                                                <td><?php echo htmlspecialchars($activity['title']); ?></td>
                                                <td><?php echo ucfirst($activity['type']); ?></td>
                                                <td><?php echo date('M j, Y', strtotime($activity['due_date'])); ?></td>
                                                <td>
                                                    <?php
                                                    $status_class = '';
                                                    $status_text = '';
                                                    if ($activity['submission_status'] === 'graded') {
                                                        $status_class = 'success';
                                                        $status_text = 'Graded';
                                                    } elseif ($activity['submission_status'] === 'submitted') {
                                                        $status_class = 'info';
                                                        $status_text = 'Submitted';
                                                    } else {
                                                        $status_class = 'warning';
                                                        $status_text = 'Pending';
                                                    }
                                                    ?>
                                                    <span class="badge badge-<?php echo $status_class; ?>">
                                                        <?php echo $status_text; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php 
                                                    if ($activity['points'] !== null) {
                                                        echo $activity['points'] . '/' . $activity['points'];
                                                    } else {
                                                        echo '-';
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html> 