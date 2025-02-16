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
    <link rel="icon" href="../images/light-logo.png">
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

        .status-graded {
            background: #e6f4ea;
            color: #137333;
            border: 1px solid #137333;
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

        .btn-success {
            background-color: #137333;
            border-color: #137333;
            color: white;
        }

        .btn-success:hover {
            background-color: #0f6429;
            border-color: #0f6429;
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

        .attachments {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 12px;
            margin-top: 16px;
        }

        .attachment-item {
            padding: 8px;
            border-radius: 4px;
            transition: background 0.2s;
        }

        .attachment-item:hover {
            background: #e8f0fe;
        }

        .attachment-link {
            color: #1967d2;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .attachment-link:hover {
            text-decoration: underline;
            color: #1557b0;
        }

        .attachment-item i {
            color: #5f6368;
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
    </style>
    
    <!-- Add SweetAlert2 CSS and JS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <?php include 'includes/navigation.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="activities-container">
                <h1 class="mb-4">Activities</h1>
                
                <!-- Activities List -->
                <div class="activities-list">
                    <?php
                    $activities_query = "
                        SELECT 
                            a.*,
                            s.subject_name,
                            ss.section_id,
                            sas.submission_id,
                            sas.submitted_at,
                            sas.points,
                            sas.status as submission_status,
                            sas.late_submission,
                            GROUP_CONCAT(af.file_name) as attachment_names,
                            GROUP_CONCAT(af.file_path) as attachment_paths
                        FROM activities a
                        JOIN section_subjects ss ON a.section_subject_id = ss.id
                        JOIN subjects s ON ss.subject_id = s.id
                        JOIN student_sections sts ON ss.section_id = sts.section_id
                        LEFT JOIN activity_files af ON a.activity_id = af.activity_id
                        LEFT JOIN student_activity_submissions sas 
                            ON sas.activity_id = a.activity_id 
                            AND sas.student_id = ?
                        WHERE sts.student_id = ? 
                        AND a.status = 'active'
                        AND a.type = 'activity'
                        GROUP BY a.activity_id, s.subject_name, ss.section_id,
                                 sas.submission_id, sas.submitted_at, sas.points,
                                 sas.status, sas.late_submission
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
                                    
                                    <?php if (!empty($activity['attachment_names'])): ?>
                                        <div class="attachments mt-3">
                                            <h6 class="mb-2">Attachments</h6>
                                            <?php
                                            $names = explode(',', $activity['attachment_names']);
                                            $paths = explode(',', $activity['attachment_paths']);
                                            for ($i = 0; $i < count($names); $i++):
                                            ?>
                                            <div class="attachment-item">
                                                <i class="fas fa-file-alt mr-2"></i>
                                                <a href="download_activity.php?file=<?php echo urlencode($paths[$i]); ?>&name=<?php echo urlencode($names[$i]); ?>" 
                                                   class="attachment-link">
                                                    <?php echo htmlspecialchars($names[$i]); ?>
                                                </a>
                                            </div>
                                            <?php endfor; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="activity-actions">
                                    <?php 
                                    // Determine status badge class and text
                                    if (isset($activity['points'])) {
                                        $status_class = 'status-graded';
                                        $status_text = 'Graded: ' . $activity['points'] . ' points';
                                    } elseif ($activity['submission_status'] === 'submitted') {
                                        $status_class = 'status-submitted';
                                        $status_text = 'Submitted';
                                    } elseif (strtotime($activity['due_date']) < time()) {
                                        $status_class = 'status-late';
                                        $status_text = 'Missing';
                                    } else {
                                        $status_class = 'status-assigned';
                                        $status_text = 'Assigned';
                                    }
                                    ?>
                                    <span class="status-badge <?php echo $status_class; ?>">
                                        <?php echo $status_text; ?>
                                    </span>
                                    
                                    <?php if($activity['submission_status'] === 'submitted' || $activity['submission_status'] === 'graded' || isset($activity['points'])): ?>
                                        <div class="btn-group">
                                            <a href="view_activity.php?id=<?php echo $activity['activity_id']; ?>" 
                                               class="btn <?php echo isset($activity['points']) ? 'btn-success' : 'btn-info'; ?> mr-2">
                                                View Submission
                                            </a>
                                            <?php if (!isset($activity['points']) && 
                                                     strtotime($activity['due_date']) > time() && 
                                                     !$activity['late_submission'] && 
                                                     $activity['submission_status'] !== 'graded'): ?>
                                                <button type="button" 
                                                        class="btn btn-warning"
                                                        onclick="unsubmitActivity(<?php echo $activity['activity_id']; ?>)">
                                                    Unsubmit
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <?php if(!isset($activity['points'])): ?>
                                            <a href="view_activity.php?id=<?php echo $activity['activity_id']; ?>" 
                                               class="btn btn-submit">
                                                <?php echo strtotime($activity['due_date']) < time() ? 'Submit Late' : 'Submit'; ?>
                                            </a>
                                        <?php endif; ?>
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
    <script>
    function unsubmitActivity(activityId) {
        console.log('Unsubmit clicked for activity:', activityId); // Debug log
        
        Swal.fire({
            title: 'Are you sure?',
            text: "You will be able to submit a new file for this activity",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, unsubmit'
        }).then((result) => {
            if (result.isConfirmed) {
                console.log('Confirmation accepted'); // Debug log
                
                // Show loading state
                Swal.fire({
                    title: 'Processing...',
                    didOpen: () => {
                        Swal.showLoading();
                    },
                    allowOutsideClick: false,
                    showConfirmButton: false
                });

                // Send unsubmit request
                $.ajax({
                    url: 'handlers/unsubmit_activity.php',
                    type: 'POST',
                    data: { activity_id: activityId },
                    success: function(response) {
                        console.log('Response received:', response); // Debug log
                        try {
                            const result = typeof response === 'string' ? JSON.parse(response) : response;
                            
                            if (result.success) {
                                Swal.fire({
                                    title: 'Success!',
                                    text: 'Activity has been unsubmitted. You can now submit a new file.',
                                    icon: 'success'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                throw new Error(result.message || 'Failed to unsubmit activity');
                            }
                        } catch (error) {
                            console.error('Unsubmit error:', error);
                            Swal.fire('Error!', error.message, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Ajax error:', {xhr, status, error});
                        Swal.fire('Error!', 'Failed to unsubmit activity', 'error');
                    }
                });
            }
        });
    }
    </script>
</body>
</html>