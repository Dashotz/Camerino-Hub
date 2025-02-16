<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$db = new DbConnector();
$student_id = $_SESSION['id'];
$assignment_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch assignment details
$assignment_query = "
    SELECT 
        a.*,
        s.subject_name,
        s.subject_code,
        t.firstname as teacher_fname,
        t.lastname as teacher_lname,
        sas.submission_id,
        sas.submitted_at,
        sas.points as achieved_points,
        sas.feedback,
        sas.result_file,
        GROUP_CONCAT(DISTINCT af.file_name) as attachment_names,
        GROUP_CONCAT(DISTINCT af.file_path) as attachment_paths,
        CASE 
            WHEN sas.submission_id IS NOT NULL THEN 'submitted'
            WHEN NOW() > a.due_date THEN 'late'
            ELSE 'assigned'
        END as status
    FROM activities a
    LEFT JOIN section_subjects ss ON a.section_subject_id = ss.id
    LEFT JOIN subjects s ON ss.subject_id = s.id
    LEFT JOIN teacher t ON ss.teacher_id = t.teacher_id
    LEFT JOIN activity_files af ON a.activity_id = af.activity_id
    LEFT JOIN student_activity_submissions sas ON a.activity_id = sas.activity_id 
        AND sas.student_id = ?
    WHERE a.activity_id = ?
    AND a.type = 'assignment'
    GROUP BY a.activity_id";

try {
    $stmt = $db->prepare($assignment_query);
    $stmt->bind_param("ii", $student_id, $assignment_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $assignment = $result->fetch_assoc();

    if (!$assignment) {
        $_SESSION['error'] = "Assignment not found";
        header("Location: student_assignments.php");
        exit();
    }

} catch (Exception $e) {
    $_SESSION['error'] = "An error occurred while loading the assignment";
    header("Location: student_assignments.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($assignment['title']); ?> - CamerinoHub</title>
    
    <!-- External CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href="css/dashboard-shared.css" rel="stylesheet">
    <link rel="icon" href="../images/light-logo.png">
    <style>
        .activity-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 0 20px;
            font-family: 'Google Sans', Roboto, Arial, sans-serif;
        }

        .activity-header {
            background: white;
            border-radius: 8px 8px 0 0;
            padding: 24px;
            margin-bottom: 1px;
            box-shadow: 0 1px 2px 0 rgba(60,64,67,0.3);
            border-left: 4px solid #1967d2;
        }

        .activity-type-badge {
            color: #5f6368;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 8px;
            display: block;
        }

        .activity-title {
            font-size: 2rem;
            font-weight: 400;
            color: #202124;
            margin-bottom: 8px;
        }

        .activity-meta {
            color: #5f6368;
            font-size: 0.875rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .activity-details {
            background: white;
            padding: 24px;
            margin-bottom: 1px;
            box-shadow: 0 1px 2px 0 rgba(60,64,67,0.3);
        }

        .activity-description {
            color: #3c4043;
            font-size: 0.875rem;
            line-height: 1.5;
            margin-bottom: 20px;
        }

        .attachments {
            margin-top: 16px;
        }

        .attachment-item {
            display: inline-flex;
            align-items: center;
            background: #f8f9fa;
            padding: 8px 12px;
            border-radius: 4px;
            margin: 0 8px 8px 0;
            color: #3c4043;
            text-decoration: none;
            font-size: 0.875rem;
        }

        .attachment-item:hover {
            background: #f1f3f4;
        }

        .submission-section {
            background: white;
            padding: 24px;
            border-radius: 0 0 8px 8px;
            box-shadow: 0 1px 2px 0 rgba(60,64,67,0.3);
        }

        .submission-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            padding-bottom: 16px;
            border-bottom: 1px solid #e0e0e0;
        }

        .submission-header h6 {
            color: #202124;
            font-size: 1rem;
            font-weight: 500;
            margin: 0;
        }

        .submission-status {
            display: flex;
            align-items: center;
            font-size: 0.875rem;
        }

        .submission-status i {
            margin-right: 8px;
        }

        .status-assigned {
            color: #5f6368;
        }

        .status-submitted {
            color: #1e8e3e;
        }

        .status-late {
            color: #d93025;
        }

        .file-upload-area {
            border: 2px dashed #dadce0;
            border-radius: 8px;
            padding: 32px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .file-upload-area:hover {
            background: #f8f9fa;
            border-color: #1967d2;
        }

        .file-upload-area i {
            color: #5f6368;
            margin-bottom: 8px;
        }

        .file-upload-area p {
            color: #1967d2;
            margin: 8px 0 4px;
            font-weight: 500;
        }

        .file-upload-area small {
            color: #5f6368;
        }

        .selected-file {
            display: flex;
            align-items: center;
            background: #f8f9fa;
            padding: 12px;
            border-radius: 4px;
            margin-top: 16px;
        }

        .selected-file i {
            color: #5f6368;
            margin-right: 8px;
        }

        .btn-submit-work {
            background: #1a73e8;
            color: white;
            border: none;
            padding: 8px 24px;
            border-radius: 4px;
            font-size: 0.875rem;
            font-weight: 500;
            margin-top: 16px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-submit-work:hover {
            background: #1557b0;
        }

        .btn-submit-work:disabled {
            background: #dadce0;
            cursor: not-allowed;
        }

        .grade-display {
            background: #f8f9fa;
            padding: 16px;
            border-radius: 4px;
            margin-top: 16px;
        }

        .grade-display h6 {
            color: #202124;
            font-size: 0.875rem;
            margin: 0 0 8px 0;
        }

        .grade-value {
            color: #1e8e3e;
            font-size: 1.25rem;
            font-weight: 500;
        }

        .submission-details {
            color: #5f6368;
            font-size: 0.875rem;
        }

        .submission-details p {
            margin: 0;
        }

        .btn-warning {
            background-color: #ffd600;
            color: #000;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            font-size: 0.875rem;
            font-weight: 500;
            transition: background-color 0.2s;
        }

        .btn-warning:hover {
            background-color: #ffea00;
        }

        .submitted-file {
            display: flex;
            align-items: center;
            background: #f8f9fa;
            padding: 12px;
            border-radius: 4px;
            margin-top: 16px;
        }

        .submitted-file i {
            margin-right: 12px;
            color: #5f6368;
        }

        .submitted-file a {
            color: #1967d2;
            text-decoration: none;
        }

        .submitted-file a:hover {
            text-decoration: underline;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            color: #5f6368;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 4px;
            margin-bottom: 16px;
            transition: all 0.2s;
        }

        .btn-back:hover {
            background: #f1f3f4;
            color: #1967d2;
            text-decoration: none;
        }

        .btn-back i {
            margin-right: 8px;
        }

        .feedback-section {
            margin: 15px 0;
        }

        .feedback-content {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            color: #495057;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .result-file-section {
            margin: 15px 0;
        }

        .result-file {
            border: 1px solid #e9ecef;
            transition: background-color 0.2s;
        }

        .result-file:hover {
            background-color: #e9ecef;
        }

        .result-file .btn {
            white-space: nowrap;
        }

        .result-file i {
            font-size: 1.2rem;
        }
    </style>
</head>
<body>
 
    
    <div class="dashboard-container">
        
        
        <div class="main-content">
            <div class="activity-container">
                <!-- Back Button -->
                <a href="student_assignments.php" class="btn-back">
                    <i class="fas fa-arrow-left"></i>
                    Back to Assignments
                </a>

                <!-- Assignment Header -->
                <div class="activity-header">
                    <span class="activity-type-badge">Assignment</span>
                    <div class="activity-title">
                        <?php echo htmlspecialchars($assignment['title']); ?>
                    </div>
                    <div class="activity-meta">
                        <div>
                            <?php echo htmlspecialchars($assignment['subject_name']); ?> • 
                            <?php echo htmlspecialchars($assignment['teacher_fname'] . ' ' . $assignment['teacher_lname']); ?> • 
                            Posted <?php echo date('M j, Y', strtotime($assignment['created_at'])); ?>
                        </div>
                        <div><?php echo $assignment['points']; ?> points</div>
                    </div>
                </div>

                <!-- Assignment Details -->
                <div class="activity-details">
                    <div class="activity-description">
                        <?php echo nl2br(htmlspecialchars($assignment['description'])); ?>
                    </div>

                    <?php if (!empty($assignment['attachment_paths'])): ?>
                        <div class="attachments">
                            <h6 class="mb-3">Attachments</h6>
                            <?php
                            $names = explode(',', $assignment['attachment_names']);
                            $paths = explode(',', $assignment['attachment_paths']);
                            
                            foreach ($paths as $index => $path):
                                $name = isset($names[$index]) ? trim($names[$index]) : basename(trim($path));
                                $clean_path = trim($path);
                            ?>
                                <div class="attachment-item">
                                    <i class="fas fa-file-alt mr-2"></i>
                                    <a href="handlers/download_file.php?file=<?php echo urlencode($clean_path); ?>&name=<?php echo urlencode($name); ?>" 
                                       class="attachment-link" target="_blank">
                                        <?php echo htmlspecialchars($name); ?>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Submission Section -->
                <div class="submission-section">
                    <div class="submission-header">
                        <h6>Your work</h6>
                        <div class="submission-status <?php echo 'status-' . $assignment['status']; ?>">
                            <i class="fas <?php 
                                echo match($assignment['status']) {
                                    'submitted' => 'fa-check-circle',
                                    'late' => 'fa-exclamation-circle',
                                    default => 'fa-clock'
                                };
                            ?>"></i>
                            <?php 
                            echo match($assignment['status']) {
                                'submitted' => 'Turned in',
                                'late' => 'Missing',
                                default => 'Assigned'
                            };
                            ?>
                        </div>
                    </div>

                    <?php if ($assignment['status'] === 'submitted'): ?>
                        <div class="submitted-work">
                            <div class="submission-details">
                                <p>
                                    <i class="fas fa-calendar-check text-muted mr-2"></i>
                                    Submitted <?php echo date('M j, Y g:i A', strtotime($assignment['submitted_at'])); ?>
                                </p>
                                
                                <!-- Add Feedback Display -->
                                <?php if (!empty($assignment['feedback'])): ?>
                                    <div class="feedback-section mt-3">
                                        <h6 class="mb-2">Teacher's Feedback</h6>
                                        <div class="feedback-content p-3 bg-light rounded">
                                            <?php echo nl2br(htmlspecialchars($assignment['feedback'])); ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Add Result File Display -->
                                <?php if (!empty($assignment['result_file'])): ?>
                                    <div class="result-file-section mt-3">
                                        <h6 class="mb-2">Graded Work</h6>
                                        <div class="result-file p-2 bg-light rounded d-flex align-items-center">
                                            <i class="fas fa-file-alt text-primary mr-2"></i>
                                            <span class="flex-grow-1"><?php echo basename($assignment['result_file']); ?></span>
                                            <a href="handlers/download_result.php?file=<?php echo urlencode($assignment['result_file']); ?>" 
                                               class="btn btn-sm btn-primary" target="_blank">
                                                <i class="fas fa-download mr-1"></i> Download
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if (isset($assignment['achieved_points'])): ?>
                                    <div class="grade-display">
                                        <h6>Grade</h6>
                                        <div class="grade-value">
                                            <?php echo $assignment['achieved_points']; ?>/<?php echo $assignment['points']; ?>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <?php 
                                    // Check if assignment is still within due date
                                    $canUnsubmit = !isset($assignment['achieved_points']) && 
                                                  strtotime($assignment['due_date']) > time();
                                    if ($canUnsubmit): 
                                    ?>
                                        <button type="button" class="btn btn-warning mt-3" 
                                                onclick="unsubmitAssignment(<?php echo $assignment_id; ?>)">
                                            <i class="fas fa-undo mr-2"></i>Unsubmit
                                        </button>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <form id="assignmentSubmissionForm" enctype="multipart/form-data">
                            <input type="hidden" name="assignment_id" value="<?php echo $assignment_id; ?>">
                            <div class="file-upload-area" onclick="document.getElementById('fileInput').click();" id="uploadArea">
                                <i class="fas fa-cloud-upload-alt fa-2x"></i>
                                <p>Click to add your work</p>
                                <small class="text-muted">or drag and drop files here</small>
                                <input type="file" id="fileInput" name="submission_file" style="display: none;" required>
                            </div>
                            <div id="filePreview" style="display: none;" class="mb-3">
                                <div class="selected-file">
                                    <i class="fas fa-file mr-2"></i>
                                    <span id="fileName"></span>
                                    <button type="button" class="btn btn-link text-danger" onclick="clearFile()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <button type="submit" class="btn-submit-work" id="submitBtn" disabled>
                                <i class="fas fa-paper-plane mr-2"></i>Turn in
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Copy all scripts from view_activity.php -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Copy all JavaScript from view_activity.php
        function validateForm() {
            // ... (copy all functions)
        }

        // ... (copy all other JavaScript code)
    </script>
    <script>
    $(document).ready(function() {
        // File input handler
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName);
        });

        // Form submission
        $('#assignmentSubmissionForm').on('submit', function(e) {
            e.preventDefault();
            
            const fileInput = document.getElementById('fileInput');
            if (!fileInput.files || !fileInput.files[0]) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Please select a file to submit',
                    icon: 'error'
                });
                return;
            }

            // Create FormData object
            let formData = new FormData(this);
            formData.append('activity_id', <?php echo $assignment_id; ?>);

            // Disable submit button and show loading state
            const submitBtn = $('#submitBtn');
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Submitting...');

            $.ajax({
                url: 'handlers/submit_assignment.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Assignment submitted successfully',
                            icon: 'success'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: response.message || 'Failed to submit assignment',
                            icon: 'error'
                        });
                        submitBtn.prop('disabled', false).html('<i class="fas fa-paper-plane mr-2"></i>Turn in');
                    }
                },
                error: function() {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to submit assignment',
                        icon: 'error'
                    });
                    submitBtn.prop('disabled', false).html('<i class="fas fa-paper-plane mr-2"></i>Turn in');
                }
            });
        });

        // File selection handler
        $('#fileInput').on('change', function() {
            const file = this.files[0];
            if (file) {
                $('#fileName').text(file.name);
                $('#filePreview').show();
                $('#uploadArea').hide();
                $('#submitBtn').prop('disabled', false);
            }
        });

        // Clear file selection
        window.clearFile = function() {
            $('#fileInput').val('');
            $('#filePreview').hide();
            $('#uploadArea').show();
            $('#submitBtn').prop('disabled', true);
        };

        // Drag and drop handlers
        const uploadArea = document.getElementById('uploadArea');

        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            
            const fileInput = document.getElementById('fileInput');
            const dt = new DataTransfer();
            dt.items.add(e.dataTransfer.files[0]);
            fileInput.files = dt.files;
            
            // Trigger change event
            $(fileInput).trigger('change');
        });
    });
    </script>
    <script>
    function unsubmitAssignment(assignmentId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You will be able to submit a new file for this assignment",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, unsubmit'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Processing...',
                    didOpen: () => {
                        Swal.showLoading();
                    },
                    allowOutsideClick: false,
                    showConfirmButton: false
                });

                $.ajax({
                    url: 'handlers/unsubmit_assignment.php',
                    type: 'POST',
                    data: { assignment_id: assignmentId },
                    success: function(response) {
                        try {
                            const result = typeof response === 'string' ? JSON.parse(response) : response;
                            
                            if (result.success) {
                                Swal.fire({
                                    title: 'Success!',
                                    text: 'Assignment has been unsubmitted. You can now submit a new file.',
                                    icon: 'success'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                throw new Error(result.message || 'Failed to unsubmit assignment');
                            }
                        } catch (error) {
                            console.error('Unsubmit error:', error);
                            Swal.fire('Error!', error.message, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Ajax error:', {xhr, status, error});
                        Swal.fire('Error!', 'Failed to unsubmit assignment', 'error');
                    }
                });
            }
        });
    }
    </script>
</body>
</html>
