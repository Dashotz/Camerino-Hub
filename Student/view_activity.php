<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$db = new DbConnector();
$student_id = $_SESSION['id'];
$activity_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch activity details with files
$activity_query = "
    SELECT 
        a.*,
        s.subject_name,
        s.subject_code,
        t.firstname as teacher_fname,
        t.lastname as teacher_lname,
        sas.submission_id,
        sas.submitted_at,
        sas.points as achieved_points,
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
    GROUP BY a.activity_id, s.subject_name, s.subject_code, 
             t.firstname, t.lastname, sas.submission_id, 
             sas.submitted_at, sas.points";

try {
    $stmt = $db->prepare($activity_query);
    if (!$stmt) {
        throw new Exception("Query preparation failed");
    }

    $stmt->bind_param("ii", $student_id, $activity_id);
    if (!$stmt->execute()) {
        throw new Exception("Query execution failed");
    }

    $result = $stmt->get_result();
    $activity = $result->fetch_assoc();

    if (!$activity) {
        $_SESSION['error'] = "Activity not found";
        header("Location: student_activities.php");
        exit();
    }

} catch (Exception $e) {
    error_log("Error in view_activity.php: " . $e->getMessage());
    $_SESSION['error'] = "An error occurred while loading the activity";
    header("Location: student_activities.php");
    exit();
}

// Display messages if they exist in URL parameters
$status = isset($_GET['status']) ? $_GET['status'] : '';
$message = isset($_GET['message']) ? urldecode($_GET['message']) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($activity['title']); ?> - CamerinoHub</title>
    
    <!-- External CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href="css/dashboard-shared.css" rel="stylesheet">
    
    <style>
        /* Main container styles */
        .activity-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 0 20px;
            font-family: 'Google Sans', Roboto, Arial, sans-serif;
        }

        /* Enhanced Activity Header */
        .activity-header {
            background: white;
            border-radius: 8px 8px 0 0;
            padding: 24px 24px 16px;
            margin-bottom: 1px;
            box-shadow: 0 1px 2px 0 rgba(60,64,67,0.3);
            border-left: 4px solid #1967d2;
            position: relative;
        }

        .activity-type-badge {
            position: absolute;
            top: 24px;
            right: 24px;
            background: #e8f0fe;
            color: #1967d2;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .activity-title {
            font-size: 1.75rem;
            color: #1967d2;
            margin-bottom: 12px;
            padding-right: 100px;
            font-weight: 500;
        }

        .activity-meta {
            color: #5f6368;
            font-size: 0.875rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #e0e0e0;
            margin-top: 16px;
            padding-top: 16px;
        }

        .due-date {
            background: #f8f9fa;
            padding: 8px 16px;
            border-radius: 4px;
            margin-top: 12px;
            display: inline-block;
            font-size: 0.875rem;
            color: #3c4043;
        }

        /* Enhanced Activity Details */
        .activity-details {
            background: white;
            padding: 24px;
            margin-bottom: 1px;
            box-shadow: 0 1px 2px 0 rgba(60,64,67,0.3);
        }

        .activity-description {
            color: #3c4043;
            white-space: pre-line;
            margin-bottom: 24px;
            line-height: 1.6;
            font-size: 0.95rem;
        }

        /* Enhanced Attachments Section */
        .attachments {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 16px;
            margin-top: 24px;
        }

        .attachments h6 {
            color: #3c4043;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 12px;
        }

        .attachment-item {
            display: flex;
            align-items: center;
            padding: 8px 12px;
            border-radius: 4px;
            transition: all 0.2s;
            margin-bottom: 4px;
        }

        .attachment-item:hover {
            background: #e8f0fe;
        }

        .attachment-item i {
            color: #1967d2;
            margin-right: 12px;
        }

        .attachment-item a {
            color: #3c4043;
            text-decoration: none;
            font-size: 0.875rem;
        }

        /* Enhanced Submission Section */
        .submission-section {
            background: white;
            border-radius: 0 0 8px 8px;
            padding: 24px;
            box-shadow: 0 1px 2px 0 rgba(60,64,67,0.3);
        }

        .submission-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid #e0e0e0;
        }

        .submission-status {
            display: inline-flex;
            align-items: center;
            padding: 6px 16px;
            border-radius: 16px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .status-submitted { 
            background: #e6f4ea; 
            color: #137333;
        }

        .status-submitted i {
            margin-right: 8px;
            color: #137333;
        }

        .status-late { 
            background: #fce8e6; 
            color: #c5221f;
        }

        .status-assigned { 
            background: #e8f0fe; 
            color: #1967d2;
        }

        /* Enhanced File Upload Area */
        .file-upload-area {
            border: 2px dashed #dadce0;
            border-radius: 8px;
            padding: 32px 24px;
            text-align: center;
            margin: 16px 0;
            cursor: pointer;
            transition: all 0.2s;
            background: #f8f9fa;
        }

        .file-upload-area:hover {
            border-color: #1967d2;
            background: #e8f0fe;
        }

        .file-upload-area i {
            color: #1967d2;
            margin-bottom: 12px;
        }

        .file-upload-area p {
            color: #3c4043;
            margin-bottom: 4px;
        }

        .file-upload-area small {
            color: #5f6368;
        }

        /* Enhanced Submit Button */
        .btn-submit-work {
            background: #1967d2;
            color: white;
            padding: 8px 24px;
            border-radius: 4px;
            border: none;
            font-weight: 500;
            transition: all 0.2s;
            display: block;
            width: 100%;
            margin-top: 16px;
        }

        .btn-submit-work:hover {
            background: #1557b0;
            box-shadow: 0 1px 3px rgba(60,64,67,0.3);
        }

        /* Grade Display */
        .grade-display {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 16px;
            margin-top: 16px;
        }

        .grade-display h6 {
            color: #3c4043;
            font-size: 0.875rem;
            margin-bottom: 8px;
        }

        .grade-value {
            font-size: 1.5rem;
            color: #137333;
            font-weight: 500;
        }

        .alert {
            margin: 20px auto;
            max-width: 800px;
        }

        .btn-submit-work:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .file-upload-area {
            border: 2px dashed #ccc;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .file-upload-area:hover {
            border-color: #1967d2;
            background: #f8f9fa;
        }

        .selected-file {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .dragover {
            background: #e8f0fe;
            border-color: #1967d2;
        }

        .file-upload-area {
            transition: all 0.3s ease;
        }

        /* Back Button Styles */
        .btn-outline-primary {
            color: #1967d2;
            border-color: #1967d2;
            padding: 8px 16px;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }

        .btn-outline-primary:hover {
            background-color: #1967d2;
            color: white;
            text-decoration: none;
            box-shadow: 0 1px 3px rgba(60,64,67,0.3);
        }

        .mb-4 {
            margin-bottom: 1.5rem;
        }

        .mr-2 {
            margin-right: 0.5rem;
        }
    </style>
</head>
<body>
    <?php 
    // Show error/success messages from session
    if (isset($_SESSION['error'])) {
        echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error']) . '</div>';
        unset($_SESSION['error']);
    }
    if (isset($_SESSION['success'])) {
        echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success']) . '</div>';
        unset($_SESSION['success']);
    }
    ?>

    
    <div class="dashboard-container">
        
        <div class="main-content">
            <div class="activity-container">
                <!-- Add Back Button -->
                <div class="mb-4">
                    <a href="student_activities.php" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Activities
                    </a>
                </div>

                <!-- Activity Header -->
                <div class="activity-header">
                    <div class="activity-title">
                        <?php echo htmlspecialchars($activity['title']); ?>
                    </div>
                    <div class="activity-meta">
                        <div>
                            <?php echo htmlspecialchars($activity['subject_name']); ?> • 
                            <?php echo htmlspecialchars($activity['teacher_fname'] . ' ' . $activity['teacher_lname']); ?> • 
                            Posted <?php echo date('M j, Y', strtotime($activity['created_at'])); ?>
                        </div>
                        <div><?php echo $activity['points']; ?> points</div>
                    </div>
                </div>

                <!-- Activity Details -->
                <div class="activity-details">
                    <div class="activity-description">
                        <?php echo nl2br(htmlspecialchars($activity['description'])); ?>
                    </div>

                    <?php if ($activity['attachment_names']): ?>
                    <div class="attachments">
                        <h6 class="mb-3">Attachments</h6>
                        <?php
                        $names = explode(',', $activity['attachment_names']);
                        $paths = explode(',', $activity['attachment_paths']);
                        for ($i = 0; $i < count($names); $i++):
                        ?>
                        <div class="attachment-item">
                            <i class="fas fa-file-alt mr-2"></i>
                            <a href="<?php echo htmlspecialchars($paths[$i]); ?>" target="_blank">
                                <?php echo htmlspecialchars($names[$i]); ?>
                            </a>
                        </div>
                        <?php endfor; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Submission Section -->
                <div class="submission-section">
                    <div class="submission-header">
                        <h6>Your work</h6>
                        <div class="submission-status <?php echo 'status-' . $activity['status']; ?>">
                            <i class="fas <?php 
                                echo match($activity['status']) {
                                    'submitted' => 'fa-check-circle',
                                    'late' => 'fa-exclamation-circle',
                                    default => 'fa-clock'
                                };
                            ?>"></i>
                            <?php 
                            echo match($activity['status']) {
                                'submitted' => 'Turned in',
                                'late' => 'Missing',
                                default => 'Assigned'
                            };
                            ?>
                        </div>
                    </div>

                    <?php if ($activity['status'] === 'submitted'): ?>
                        <div class="submitted-work">
                            <div class="submission-details">
                                <p>
                                    <i class="fas fa-calendar-check text-muted mr-2"></i>
                                    Submitted <?php echo date('M j, Y g:i A', strtotime($activity['submitted_at'])); ?>
                                </p>
                                <?php if (isset($activity['achieved_points'])): ?>
                                    <div class="grade-display">
                                        <h6>Grade</h6>
                                        <div class="grade-value">
                                            <?php echo $activity['achieved_points']; ?>/<?php echo $activity['points']; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <form action="handlers/submit_activity.php" method="POST" enctype="multipart/form-data" id="submissionForm" onsubmit="return validateForm()">
                            <input type="hidden" name="activity_id" value="<?php echo $activity_id; ?>">
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

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <script>
        function validateForm() {
            const fileInput = document.getElementById('fileInput');
            if (!fileInput.files || fileInput.files.length === 0) {
                alert('Please select a file to upload');
                return false;
            }
            return true;
        }

        function clearFile() {
            const fileInput = document.getElementById('fileInput');
            const filePreview = document.getElementById('filePreview');
            const uploadArea = document.getElementById('uploadArea');
            const submitBtn = document.getElementById('submitBtn');

            fileInput.value = '';
            filePreview.style.display = 'none';
            uploadArea.style.display = 'block';
            submitBtn.disabled = true;
        }

        document.getElementById('fileInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const maxSize = 10 * 1024 * 1024; // 10MB in bytes
            const allowedTypes = ['application/pdf', 'application/msword', 
                                 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                 'text/plain', 'application/vnd.ms-powerpoint',
                                 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                                 'application/vnd.ms-excel',
                                 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                 'application/zip', 'application/x-rar-compressed',
                                 'image/jpeg', 'image/png'];

            if (file) {
                // Check file size
                if (file.size > maxSize) {
                    alert('File size exceeds 10MB limit');
                    clearFile();
                    return;
                }

                // Check file type
                if (!allowedTypes.includes(file.type)) {
                    alert('Invalid file type. Please upload a supported file format.');
                    clearFile();
                    return;
                }

                // Update UI
                const uploadArea = document.getElementById('uploadArea');
                const filePreview = document.getElementById('filePreview');
                const fileName = document.getElementById('fileName');
                const submitBtn = document.getElementById('submitBtn');

                uploadArea.style.display = 'none';
                filePreview.style.display = 'block';
                fileName.textContent = file.name;
                submitBtn.disabled = false;
            }
        });

        // Add drag and drop support
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
            fileInput.files = e.dataTransfer.files;
            fileInput.dispatchEvent(new Event('change'));
        });
    </script>
</body>
</html>