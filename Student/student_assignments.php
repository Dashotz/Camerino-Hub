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
            border-bottom: 1px solid #e0e0e0;
        }

        .assignment-title {
            margin: 0;
            font-size: 1.25rem;
            color: #1967d2;
        }

        .assignment-meta {
            color: #5f6368;
            font-size: 0.875rem;
            margin-top: 4px;
        }

        .assignment-body {
            padding: 16px 24px;
        }

        .assignment-description {
            color: #3c4043;
            margin-bottom: 16px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 16px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .status-submitted {
            background: #e8f5e9;
            color: #1b5e20;
        }

        .status-late {
            background: #fbe9e7;
            color: #c41c00;
        }

        .status-assigned {
            background: #e3f2fd;
            color: #0d47a1;
        }

        .assignment-files {
            margin-top: 16px;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 4px;
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
            margin-top: 16px;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            align-items: center;
        }

        .btn-submit {
            background: #1967d2;
            color: white;
            padding: 8px 24px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-submit:hover {
            background: #1557b0;
        }

        .grade-badge {
            background: #e8f5e9;
            color: #1b5e20;
            padding: 4px 12px;
            border-radius: 16px;
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
                                <h3 class="assignment-title"><?php echo htmlspecialchars($assignment['title']); ?></h3>
                                <div class="assignment-meta">
                                    <?php echo htmlspecialchars($assignment['subject_name']); ?> • 
                                    Due <?php echo date('M j, Y g:i A', strtotime($assignment['due_date'])); ?>
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
                                        foreach ($paths as $index => $path): ?>
                                            <a href="<?php echo htmlspecialchars($path); ?>" 
                                               class="file-link" target="_blank">
                                                <i class="fas fa-paperclip"></i>
                                                <?php echo htmlspecialchars($names[$index]); ?>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="assignment-actions">
                                    <span class="status-badge status-<?php echo $assignment['status']; ?>">
                                        <?php echo ucfirst($assignment['status']); ?>
                                    </span>
                                    
                                    <?php if ($assignment['status'] !== 'submitted'): ?>
                                        <a href="view_assignment.php?id=<?php echo $assignment['activity_id']; ?>" 
                                           class="btn btn-submit">
                                            View Assignment
                                        </a>
                                    <?php else: ?>
                                        <?php if (isset($assignment['grade'])): ?>
                                            <span class="grade-badge">
                                                Score: <?php echo $assignment['grade']; ?>/<?php echo $assignment['points']; ?>
                                            </span>
                                        <?php endif; ?>
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
</body>
</html>