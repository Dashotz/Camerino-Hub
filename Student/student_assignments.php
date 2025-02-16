<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: Student-Login.php");
    exit();
}

require_once('../db/dbConnector.php');
$db = new DbConnector();
$student_id = $_SESSION['id'];

// Fetch assignments with their status
$assignments_query = "
    SELECT 
        a.*,
        s.subject_name,
        s.subject_code,
        GROUP_CONCAT(DISTINCT af.file_path) as file_paths,
        GROUP_CONCAT(DISTINCT af.file_name) as file_names,
        sas.submission_id,
        sas.submitted_at,
        sas.points as grade,
        CASE 
            WHEN sas.submission_id IS NOT NULL THEN 'submitted'
            WHEN NOW() > a.due_date THEN 'late'
            ELSE 'assigned'
        END as status
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
    AND a.type = 'assignment'
    AND ss.status = 'active'
    GROUP BY 
        a.activity_id,
        s.subject_name,
        s.subject_code,
        sas.submitted_at,
        sas.points
    ORDER BY 
        CASE 
            WHEN sas.submission_id IS NULL AND a.due_date >= NOW() THEN 1
            WHEN sas.submission_id IS NULL AND a.due_date < NOW() THEN 2
            ELSE 3
        END,
        a.due_date ASC";

$stmt = $db->prepare($assignments_query);
$stmt->bind_param("ii", $student_id, $student_id);
$stmt->execute();
$assignments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignments - CamerinoHub</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href="css/dashboard-shared.css" rel="stylesheet">
    
	<link rel="icon" href="../images/light-logo.png">
    <style>
        .assignments-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 0 20px;
        }

        .assignment-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 2px 0 rgba(60,64,67,0.3), 0 2px 6px 2px rgba(60,64,67,0.15);
            margin-bottom: 24px;
            transition: box-shadow 0.2s;
            border-left: 4px solid #1967d2;
        }

        .assignment-card:hover {
            box-shadow: 0 2px 4px 0 rgba(60,64,67,0.3), 0 4px 12px 4px rgba(60,64,67,0.15);
        }

        .assignment-header {
            padding: 16px 24px;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .assignment-icon {
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

        .assignment-title {
            flex: 1;
        }

        .assignment-title h3 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 500;
            color: #1967d2;
        }

        .assignment-meta {
            color: #5f6368;
            font-size: 0.875rem;
            margin-top: 4px;
        }

        .assignment-points {
            color: #5f6368;
            font-size: 0.875rem;
        }

        .assignment-body {
            padding: 0 24px 16px;
            color: #3c4043;
        }

        .assignment-description {
            margin-bottom: 16px;
        }

        .assignment-files {
            background: #f8f9fa;
            border-radius: 4px;
            padding: 12px;
            margin-bottom: 16px;
        }

        .file-link {
            display: block;
            color: #1967d2;
            text-decoration: none;
            padding: 4px 0;
        }

        .file-link:hover {
            text-decoration: underline;
        }

        .assignment-actions {
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
            text-decoration: none;
        }

        .btn-submit:hover {
            background: #1557b0;
            color: white;
            text-decoration: none;
        }

        .grade-badge {
            background: #e8f0fe;
            color: #1967d2;
            padding: 4px 12px;
            border-radius: 16px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .action-buttons {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-info {
            background: #1967d2;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            font-size: 0.875rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }

        .btn-info:hover {
            background: #1557b0;
            color: white;
            text-decoration: none;
        }

        .btn-warning {
            background: #ffd600;
            color: #000;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            font-size: 0.875rem;
            display: inline-flex;
            align-items: center;
        }

        .btn-warning:hover {
            background: #ffea00;
            color: #000;
        }

        .mr-1 {
            margin-right: 0.25rem;
        }

        .mr-2 {
            margin-right: 0.5rem;
        }

        .ml-2 {
            margin-left: 0.5rem;
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
</head>
<body>
    <?php include 'includes/navigation.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="assignments-container">
                <h1 class="mb-4">Assignments</h1>
                
                <?php if (empty($assignments)): ?>
                    <div class="no-assignments">
                        <i class="fas fa-tasks fa-3x mb-3"></i>
                        <h3>No assignments yet</h3>
                        <p>When your teachers assign work, you'll see it here</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($assignments as $assignment): ?>
                        <div class="assignment-card">
                            <div class="assignment-header">
                                <div class="assignment-icon">
                                    <i class="fas fa-book"></i>
                                </div>
                                <div class="assignment-title">
                                    <h3><?php echo htmlspecialchars($assignment['title']); ?></h3>
                                    <div class="assignment-meta">
                                        <?php echo htmlspecialchars($assignment['subject_name']); ?> • 
                                        Due <?php echo date('M j, Y g:i A', strtotime($assignment['due_date'])); ?>
                                    </div>
                                </div>
                                <div class="assignment-points">
                                    <?php echo $assignment['points']; ?> points
                                </div>
                            </div>
                            
                            <div class="assignment-body">
                                <div class="assignment-description">
                                    <?php echo nl2br(htmlspecialchars($assignment['description'])); ?>
                                </div>
                                
                                <?php if (!empty($assignment['file_paths'])): ?>
                                    <div class="assignment-files">
                                        <h6>Attachments</h6>
                                        <?php 
                                        $paths = explode(',', $assignment['file_paths']);
                                        $names = explode(',', $assignment['file_names']);
                                        foreach ($paths as $index => $path): 
                                            $clean_path = trim($path);
                                            $name = isset($names[$index]) ? trim($names[$index]) : basename($clean_path);
                                        ?>
                                            <a href="handlers/download_file.php?file=<?php echo urlencode($clean_path); ?>&name=<?php echo urlencode($name); ?>" 
                                               class="file-link">
                                                <i class="fas fa-paperclip mr-2"></i>
                                                <?php echo htmlspecialchars($name); ?>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="assignment-actions">
                                    <span class="status-badge status-<?php echo $assignment['status']; ?>">
                                        <?php echo ucfirst($assignment['status']); ?>
                                    </span>
                                    
                                    <?php if ($assignment['status'] === 'submitted'): ?>
                                        <div class="action-buttons">
                                            <a href="view_assignment.php?id=<?php echo $assignment['activity_id']; ?>" 
                                               class="btn btn-info mr-2">
                                                <i class="fas fa-eye mr-1"></i>View Assignment
                                            </a>
                                            <?php if (!isset($assignment['grade']) && strtotime($assignment['due_date']) > time()): ?>
                                                <button type="button" 
                                                        class="btn btn-warning"
                                                        onclick="unsubmitActivity(<?php echo $assignment['activity_id']; ?>)">
                                                    <i class="fas fa-undo mr-1"></i>Unsubmit
                                                </button>
                                            <?php endif; ?>
                                            <?php if (isset($assignment['grade'])): ?>
                                                <span class="grade-badge ml-2">
                                                    Score: <?php echo $assignment['grade']; ?>/<?php echo $assignment['points']; ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <a href="view_assignment.php?id=<?php echo $assignment['activity_id']; ?>" 
                                           class="btn btn-submit">
                                            <?php echo strtotime($assignment['due_date']) < time() ? 'Submit Late' : 'Submit'; ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Submission Modal -->
    <div class="modal fade" id="submissionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Submit Assignment</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="submissionForm" enctype="multipart/form-data">
                        <input type="hidden" name="activity_id" id="activity_id">
                        <div class="form-group">
                            <label>Upload File</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="submission_file" name="submission_file" required>
                                <label class="custom-file-label" for="submission_file">Choose file...</label>
                            </div>
                            <small class="form-text text-muted">Accepted formats: PDF, DOC, DOCX, ZIP (Max size: 10MB)</small>
                        </div>
                        <div class="form-group">
                            <label>Comments (Optional)</label>
                            <textarea class="form-control" name="comments" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="submitForm()">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        // File input handler
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName);
        });
    });

    // Update the assignment action button to use this function
    function openSubmissionModal(activityId, title) {
        $('#activity_id').val(activityId);
        $('.modal-title').text('Submit Assignment: ' + title);
        $('#submissionModal').modal('show');
    }

    function submitForm() {
        const formData = new FormData($('#submissionForm')[0]);
        
        // Disable submit button and show loading state
        const submitButton = document.querySelector('.btn-primary');
        submitButton.disabled = true;
        submitButton.innerHTML = 'Submitting...';

        // Send the form data to the server
        fetch('submit_assignment.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Submission successful, redirect to view_activity.php
                window.location.href = 'view_activity.php?id=' + formData.get('activity_id');
            } else {
                // Submission failed, show error message
                alert('Submission failed. Please try again.');
                submitButton.disabled = false;
                submitButton.innerHTML = 'Submit';
            }
        })
        .catch(error => {
            console.error('Error submitting assignment:', error);
            submitButton.disabled = false;
            submitButton.innerHTML = 'Submit';
        });
    }
    </script>

    <!-- Add this before closing body tag -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    function unsubmitActivity(activityId) {
        console.log('Unsubmit clicked for activity:', activityId);
        
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
                console.log('Confirmation accepted');
                
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
                        console.log('Response received:', response);
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