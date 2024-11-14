<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['teacher_id']) || !isset($_GET['id'])) {
    header("Location: Teacher-Login.php");
    exit();
}

$db = new DbConnector();
$activity_id = $_GET['id'];
$teacher_id = $_SESSION['teacher_id'];

// Verify teacher owns this activity
$verify_query = "
    SELECT a.*, sec.section_name, sub.subject_name, sub.subject_code
    FROM activities a
    JOIN section_subjects ss ON a.section_subject_id = ss.id
    JOIN sections sec ON ss.section_id = sec.section_id
    JOIN subjects sub ON ss.subject_id = sub.id
    WHERE a.activity_id = ? AND ss.teacher_id = ?";

$stmt = $db->prepare($verify_query);
$stmt->bind_param("ii", $activity_id, $teacher_id);
$stmt->execute();
$activity = $stmt->get_result()->fetch_assoc();

if (!$activity) {
    header("Location: manage_activities.php");
    exit();
}

// Fetch all submissions
$submissions_query = "
    SELECT 
        s.student_id,
        s.firstname,
        s.lastname,
        sas.submission_id,
        sas.submitted_at,
        sas.points,
        sas.feedback,
        GROUP_CONCAT(sf.file_path) as files
    FROM student_sections ss
    JOIN student s ON ss.student_id = s.student_id
    LEFT JOIN student_activity_submissions sas 
        ON s.student_id = sas.student_id 
        AND sas.activity_id = ?
    LEFT JOIN submission_files sf 
        ON sas.submission_id = sf.submission_id
    WHERE ss.section_id = (
        SELECT section_id 
        FROM section_subjects 
        WHERE id = ?
    )
    AND ss.status = 'active'
    GROUP BY s.student_id
    ORDER BY s.lastname, s.firstname";

$stmt = $db->prepare($submissions_query);
$stmt->bind_param("ii", $activity_id, $activity['section_subject_id']);
$stmt->execute();
$submissions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Submissions - <?php echo htmlspecialchars($activity['title']); ?></title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/dashboard-shared.css">
    <style>
        .submission-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            margin-bottom: 1rem;
            padding: 1.5rem;
            background: white;
            transition: all 0.3s ease;
        }
        .submission-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .status-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
        }
        .feedback-form {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
            margin-top: 1rem;
        }
        .feedback-form.active {
            display: block;
        }
        .file-preview {
            text-align: center;
            padding: 1rem;
        }
        .submission-files {
            display: flex;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }
        .submission-files .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .grade-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            font-size: 1.1rem;
        }
        .grade-input {
            max-width: 100px;
        }
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 500;
        }
        .download-all {
            margin-bottom: 1.5rem;
        }
        .submission-files {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 8px;
        }

        .submission-files .btn-group {
            display: inline-flex;
        }

        .submission-files .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 8px;
        }

        .submission-files .btn-outline-secondary {
            border-left: none;
            padding-left: 8px;
            padding-right: 8px;
        }

        .file-preview {
            text-align: center;
            padding: 20px;
        }

        .file-preview i {
            color: #1967d2;
        }

        .file-preview p {
            margin: 10px 0;
            word-break: break-all;
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
                        <h1><?php echo htmlspecialchars($activity['title']); ?></h1>
                        <p class="text-muted">
                            <?php echo htmlspecialchars($activity['section_name']); ?> | 
                            <?php echo htmlspecialchars($activity['subject_code']); ?> - 
                            <?php echo htmlspecialchars($activity['subject_name']); ?>
                        </p>
                    </div>
                    <a href="manage_activities.php" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left"></i> Back to Activities
                    </a>
                </div>
            </div>

            <div class="content-body">
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Students</h5>
                                <h2><?php echo count($submissions); ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Submitted</h5>
                                <h2><?php echo count(array_filter($submissions, function($s) { return $s['submission_id'] != null; })); ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h5 class="card-title">Pending</h5>
                                <h2><?php echo count(array_filter($submissions, function($s) { return $s['submission_id'] == null; })); ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title">Graded</h5>
                                <h2><?php echo count(array_filter($submissions, function($s) { return $s['points'] !== null; })); ?></h2>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="submissions-container">
                    <?php foreach ($submissions as $submission): ?>
                        <div class="submission-card p-3 position-relative">
                            <div class="row">
                                <div class="col-md-4">
                                    <h5><?php echo htmlspecialchars($submission['lastname'] . ', ' . $submission['firstname']); ?></h5>
                                    <?php if ($submission['submission_id']): ?>
                                        <small class="text-muted">
                                            Submitted: <?php echo date('M d, Y h:i A', strtotime($submission['submitted_at'])); ?>
                                        </small>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-4">
                                    <?php if ($submission['files']): ?>
                                        <div class="submission-files">
                                            <?php 
                                            $files = explode(',', $submission['files']);
                                            foreach ($files as $file): 
                                                $fileName = basename($file);
                                                $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                                                $icon = match($fileExt) {
                                                    'pdf' => 'fa-file-pdf',
                                                    'doc', 'docx' => 'fa-file-word',
                                                    'zip' => 'fa-file-archive',
                                                    default => 'fa-file'
                                                };
                                            ?>
                                                <div class="btn-group">
                                                    <button type="button" 
                                                            onclick="viewSubmission('<?php echo htmlspecialchars($file); ?>', '<?php echo $fileExt; ?>')" 
                                                            class="btn btn-sm btn-outline-primary">
                                                        <i class="fas <?php echo $icon; ?> mr-1"></i> 
                                                        View
                                                    </button>
                                                    <a href="handlers/view_submission.php?file=<?php echo urlencode($file); ?>" 
                                                       class="btn btn-sm btn-outline-secondary" 
                                                       download>
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-4 text-right">
                                    <?php if ($submission['submission_id']): ?>
                                        <button class="btn btn-primary grade-btn" 
                                                onclick="toggleFeedback(<?php echo $submission['submission_id']; ?>)">
                                            <?php echo $submission['points'] !== null ? 'Update Grade' : 'Grade'; ?>
                                        </button>
                                    <?php else: ?>
                                        <span class="badge badge-warning">No Submission</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <?php if ($submission['submission_id']): ?>
                                <div class="feedback-form mt-3" id="feedback-<?php echo $submission['submission_id']; ?>">
                                    <form onsubmit="submitGrade(event, <?php echo $submission['submission_id']; ?>)">
                                        <div class="form-group">
                                            <label>Points (max: <?php echo $activity['points']; ?>)</label>
                                            <input type="number" class="form-control" name="points" 
                                                   value="<?php echo $submission['points']; ?>"
                                                   min="0" max="<?php echo $activity['points']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Feedback</label>
                                            <textarea class="form-control" name="feedback" rows="3"><?php echo $submission['feedback']; ?></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-success">Save Grade</button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <script>
        function toggleFeedback(submissionId) {
            $('.feedback-form').removeClass('active'); // Hide all other forms
            $(`#feedback-${submissionId}`).addClass('active');
        }

        async function submitGrade(event, submissionId) {
            event.preventDefault();
            const form = event.target;
            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

            try {
                const response = await fetch('handlers/grade_submission.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        submission_id: submissionId,
                        points: parseFloat(form.points.value),
                        feedback: form.feedback.value
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Grade saved successfully',
                        icon: 'success'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    throw new Error(data.message || 'Failed to save grade');
                }
            } catch (error) {
                Swal.fire({
                    title: 'Error!',
                    text: error.message || 'Failed to save grade',
                    icon: 'error'
                });
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Save Grade';
            }
        }

        // Add download all submissions functionality
        function downloadAllSubmissions(activityId) {
            window.location.href = `handlers/download_submissions.php?activity_id=${activityId}`;
        }

        // Preview file before downloading
        function previewFile(filePath) {
            const fileExt = filePath.split('.').pop().toLowerCase();
            const isPDF = fileExt === 'pdf';
            
            if (isPDF) {
                window.open(filePath, '_blank');
            } else {
                Swal.fire({
                    title: 'File Preview',
                    html: `
                        <div class="file-preview">
                            <i class="fas fa-file fa-3x mb-3"></i>
                            <p>${filePath.split('/').pop()}</p>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Download',
                    cancelButtonText: 'Close'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = filePath;
                    }
                });
            }
        }

        function viewSubmission(filePath, fileType) {
            // Create the full path to the file
            const viewerPath = 'handlers/view_submission.php?file=' + encodeURIComponent(filePath);
            
            if (fileType === 'pdf') {
                // Open PDF in a new window
                const newWindow = window.open(viewerPath, '_blank');
                
                // Check if the window was blocked
                if (newWindow === null) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Please allow pop-ups to view PDF files',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            } else {
                // Show preview modal for other file types
                Swal.fire({
                    title: 'File Preview',
                    html: `
                        <div class="file-preview">
                            <i class="fas fa-file-${getFileIcon(fileType)} fa-3x mb-3"></i>
                            <p class="mb-2">${filePath.split('/').pop()}</p>
                            <small class="text-muted">Click Download to view this file type</small>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Download',
                    cancelButtonText: 'Close'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Try to download the file
                        fetch(viewerPath)
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('File not found or access denied');
                                }
                                return response.blob();
                            })
                            .then(blob => {
                                const url = window.URL.createObjectURL(blob);
                                const a = document.createElement('a');
                                a.href = url;
                                a.download = filePath.split('/').pop();
                                document.body.appendChild(a);
                                a.click();
                                window.URL.revokeObjectURL(url);
                                document.body.removeChild(a);
                            })
                            .catch(error => {
                                Swal.fire({
                                    title: 'Error!',
                                    text: error.message,
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            });
                    }
                });
            }
        }

        function getFileIcon(fileType) {
            switch(fileType) {
                case 'pdf': return 'pdf';
                case 'doc':
                case 'docx': return 'word';
                case 'zip': return 'archive';
                default: return 'alt';
            }
        }
    </script>
</body>
</html>
