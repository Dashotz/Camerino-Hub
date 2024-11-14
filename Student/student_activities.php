<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$db = new DbConnector();
$student_id = $_SESSION['id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activities - CamerinoHub</title>
    
    <!-- External CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href="css/dashboard-shared.css" rel="stylesheet">
    
    <!-- Custom CSS for Google Classroom style -->
    <style>
        .activities-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 0 20px;
        }

        .activity-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 2px 0 rgba(60,64,67,0.3), 0 2px 6px 2px rgba(60,64,67,0.15);
            margin-bottom: 24px;
            transition: box-shadow 0.2s;
            border-left: 4px solid #1967d2;
        }

        .activity-card:hover {
            box-shadow: 0 2px 4px 0 rgba(60,64,67,0.3), 0 4px 12px 4px rgba(60,64,67,0.15);
        }

        .activity-header {
            padding: 16px 24px;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            background: #1967d2;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
        }

        .activity-title {
            flex: 1;
        }

        .activity-title h3 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 500;
            color: #1967d2;
        }

        .activity-meta {
            color: #5f6368;
            font-size: 0.875rem;
            margin-top: 4px;
        }

        .activity-points {
            color: #5f6368;
            font-size: 0.875rem;
        }

        .activity-content {
            padding: 0 24px 16px;
            color: #3c4043;
        }

        .activity-actions {
            padding: 8px 24px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 16px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .status-assigned {
            background: #e8f0fe;
            color: #1967d2;
        }

        .status-submitted {
            background: #e6f4ea;
            color: #137333;
        }

        .status-late {
            background: #fce8e6;
            color: #c5221f;
        }

        .btn-submit {
            background: #1967d2;
            color: white;
            border-radius: 4px;
            padding: 8px 24px;
            border: none;
            font-weight: 500;
            transition: background 0.2s;
        }

        .btn-submit:hover {
            background: #1557b0;
            color: white;
        }

        .subject-filter {
            margin-bottom: 24px;
        }

        .filter-chip {
            display: inline-block;
            padding: 6px 16px;
            margin: 4px;
            border-radius: 16px;
            background: #e8f0fe;
            color: #1967d2;
            cursor: pointer;
            transition: background 0.2s;
        }

        .filter-chip:hover, .filter-chip.active {
            background: #1967d2;
            color: white;
        }

        .no-activities {
            text-align: center;
            padding: 48px 0;
            color: #5f6368;
        }
    </style>
</head>
<body>
    <?php include 'includes/navigation.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="activities-container">
                <h1 class="mb-4">Activities</h1>
                
                <!-- Subject Filter -->
                <div class="subject-filter">
                    <div class="filter-chip active">All</div>
                    <?php
                    // Get student's subjects
                    $subjects_query = "
                        SELECT DISTINCT s.subject_name, s.id
                        FROM subjects s
                        JOIN section_subjects ss ON s.id = ss.subject_id
                        JOIN student_sections sts ON ss.section_id = sts.section_id
                        WHERE sts.student_id = ? AND ss.status = 'active'
                    ";
                    $stmt = $db->prepare($subjects_query);
                    $stmt->bind_param("i", $student_id);
                    $stmt->execute();
                    $subjects = $stmt->get_result();
                    
                    while($subject = $subjects->fetch_assoc()) {
                        echo "<div class='filter-chip' data-subject-id='{$subject['id']}'>{$subject['subject_name']}</div>";
                    }
                    ?>
                </div>

                <!-- Activities List -->
                <div class="activities-list">
                    <?php
                    $activities_query = "
                        SELECT a.*, s.subject_name, ss.section_id,
                               sas.submission_id, sas.submitted_at,
                               CASE 
                                   WHEN sas.submission_id IS NOT NULL THEN 'submitted'
                                   WHEN NOW() > a.due_date THEN 'late'
                                   ELSE 'assigned'
                               END as status
                        FROM activities a
                        JOIN section_subjects ss ON a.section_subject_id = ss.id
                        JOIN subjects s ON ss.subject_id = s.id
                        JOIN student_sections sts ON ss.section_id = sts.section_id
                        LEFT JOIN student_activity_submissions sas 
                            ON sas.activity_id = a.activity_id 
                            AND sas.student_id = ?
                        WHERE sts.student_id = ? 
                        AND a.status = 'active'
                        AND a.type = 'activity'
                        ORDER BY a.due_date DESC";
                    
                    $stmt = $db->prepare($activities_query);
                    $stmt->bind_param("ii", $student_id, $student_id);
                    $stmt->execute();
                    $activities = $stmt->get_result();
                    
                    if($activities->num_rows > 0) {
                        while($activity = $activities->fetch_assoc()) {
                            $status_class = match($activity['status']) {
                                'submitted' => 'status-submitted',
                                'late' => 'status-late',
                                default => 'status-assigned'
                            };
                            
                            $status_text = match($activity['status']) {
                                'submitted' => 'Submitted',
                                'late' => 'Missing',
                                default => 'Assigned'
                            };
                            ?>
                            
                            <div class="activity-card">
                                <div class="activity-header">
                                    <div class="activity-icon">
                                        <i class="fas fa-book"></i>
                                    </div>
                                    <div class="activity-title">
                                        <h3><?php echo htmlspecialchars($activity['title']); ?></h3>
                                        <div class="activity-meta">
                                            <?php echo htmlspecialchars($activity['subject_name']); ?> • 
                                            Due <?php echo date('M j, Y g:i A', strtotime($activity['due_date'])); ?>
                                        </div>
                                    </div>
                                    <div class="activity-points">
                                        <?php echo $activity['points']; ?> points
                                    </div>
                                </div>
                                
                                <div class="activity-content">
                                    <?php echo nl2br(htmlspecialchars($activity['description'])); ?>
                                </div>
                                
                                <div class="activity-actions">
                                    <span class="status-badge <?php echo $status_class; ?>">
                                        <?php echo $status_text; ?>
                                    </span>
                                    <?php if($activity['status'] !== 'submitted'): ?>
                                        <a href="view_activity.php?id=<?php echo $activity['activity_id']; ?>" 
                                           class="btn btn-submit">
                                            View Activity
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo '<div class="no-activities">
                                <i class="fas fa-tasks fa-3x mb-3"></i>
                                <h3>No activities yet</h3>
                                <p>When your teachers assign activities, you\'ll see them here</p>
                              </div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Subject filter functionality
            $('.filter-chip').click(function() {
                $('.filter-chip').removeClass('active');
                $(this).addClass('active');
                
                const subjectId = $(this).data('subject-id');
                filterActivities(subjectId);
            });

            function filterActivities(subjectId) {
                $.ajax({
                    url: 'handlers/get_filtered_activities.php',
                    method: 'POST',
                    data: { subject_id: subjectId },
                    success: function(response) {
                        $('.activities-list').html(response);
                    },
                    error: function() {
                        alert('Error loading activities');
                    }
                });
            }
        });
    </script>
</body>
</html>