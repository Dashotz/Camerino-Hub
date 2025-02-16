<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['teacher_id'])) {
    header("Location: Teacher-Login.php");
    exit();
}

if (!isset($_GET['activity_id'])) {
    header("Location: manage_activities.php");
    exit();
}

$db = new DbConnector();
$activity_id = intval($_GET['activity_id']);
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
        sf.file_path,
        sas.remarks,
        sas.status,
        sas.result_file
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
        WHERE id = ?)
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
    <link rel="icon" href="../images/light-logo.png">
    <style>
        .submission-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 1rem;
            border: 1px solid #e0e0e0;
        }
        .student-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .student-avatar {
            width: 40px;
            height: 40px;
            background: #e9ecef;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #495057;
        }
        .grade-form {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
        }
        .file-preview {
            margin-bottom: 2rem;
            background: white;
            border-radius: 8px;
            overflow: hidden;
        }

        .file-info {
            padding: 1.5rem;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            display: flex;
            align-items: center;
        }

        .file-info i {
            font-size: 1.5rem;
            margin-right: 1rem;
            color: #6c757d;
        }

        .file-info span {
            font-size: 1rem;
            color: #495057;
            word-break: break-all;
        }

        .pdf-preview {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 4px;
        }

        .file-submission-info {
            display: flex;
            align-items: center;
            background: #f8f9fa;
            padding: 10px 15px;
            border-radius: 4px;
            border: 1px solid #dee2e6;
            margin-bottom: 15px;
        }

        .file-icon {
            font-size: 1.2rem;
            color: #6c757d;
            margin-right: 10px;
        }

        .file-name {
            flex: 1;
            margin-right: 15px;
            color: #495057;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .submitted-file {
            display: flex;
            align-items: center;
            background: #f8f9fa;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 15px;
            border: 1px solid #dee2e6;
        }

        .submitted-file i {
            font-size: 1.2rem;
            color: #6c757d;
            margin-right: 12px;
        }

        .submitted-file .file-name {
            flex: 1;
            margin-right: 15px;
            color: #495057;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .submitted-file .btn {
            white-space: nowrap;
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
                <div class="stats-container">
                    <div class="card bg-primary text-white mb-4">
                        <div class="card-body">
                            <h5>Total Students</h5>
                            <h2><?php echo count($submissions); ?></h2>
                        </div>
                    </div>
                    <div class="card bg-success text-white mb-4">
                        <div class="card-body">
                            <h5>Submitted</h5>
                            <h2><?php echo count(array_filter($submissions, function($s) { return $s['submission_id'] != null; })); ?></h2>
                        </div>
                    </div>
                    <div class="card bg-warning mb-4">
                        <div class="card-body">
                            <h5>Pending</h5>
                            <h2><?php echo count(array_filter($submissions, function($s) { return $s['submission_id'] == null; })); ?></h2>
                        </div>
                    </div>
                    <div class="card bg-info text-white mb-4">
                        <div class="card-body">
                            <h5>Graded</h5>
                            <h2><?php echo count(array_filter($submissions, function($s) { return $s['points'] !== null; })); ?></h2>
                        </div>
                    </div>
                </div>

                <div class="submissions-container">
                    <?php foreach ($submissions as $submission): ?>
                        <div class="submission-card">
                            <div class="card-body">
                                <div class="student-info">
                                    <div class="student-avatar">
                                        <?php echo strtoupper(substr($submission['firstname'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <h5 class="mb-0"><?php echo htmlspecialchars($submission['firstname'] . ' ' . $submission['lastname']); ?></h5>
                                        <?php if ($submission['submitted_at']): ?>
                                            <small class="text-muted">
                                                Submitted: <?php echo date('M j, Y g:i A', strtotime($submission['submitted_at'])); ?>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <?php if ($submission['submitted_at']): ?>
                                    <div class="mt-3">
                                        <?php if ($submission['file_path']): ?>
                                            <div class="submitted-file">
                                                <i class="fas fa-file-alt"></i>
                                                <span class="file-name"><?php echo basename($submission['file_path']); ?></span>
                                                <a href="handlers/download_submission.php?file=<?php echo urlencode($submission['file_path']); ?>" 
                                                   class="btn btn-sm btn-primary" 
                                                   target="_blank">
                                                   <i class="fas fa-download"></i> Download Submission
                                               </a>
                                           </div>
                                        <?php endif; ?>

                                        <form class="grade-form" onsubmit="return submitGrade(event, <?php echo $submission['submission_id']; ?>)" enctype="multipart/form-data">
                                            <div class="form-group">
                                                <label>Points (out of 100)</label>
                                                <input type="number" name="points" class="form-control" 
                                                       min="0" max="100" step="1"
                                                       oninput="this.value = this.value > 100 ? 100 : Math.abs(this.value)"
                                                       onkeypress="return (event.charCode >= 48 && event.charCode <= 57)"
                                                       value="<?php echo $submission['points']; ?>" required>
                                                <small class="text-muted">Enter a number between 0 and 100</small>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label>Upload Result File (PDF, DOC, DOCX)</label>
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input" id="resultFile" name="result_file" 
                                                           accept=".pdf,.doc,.docx">
                                                    <label class="custom-file-label" for="resultFile">Choose file</label>
                                                </div>
                                                <small class="text-muted">Upload the graded/marked version of student's work</small>
                                            </div>

                                            <?php if (!empty($submission['result_file'])): ?>
                                            <div class="current-result mb-3">
                                                <label>Current Result File:</label>
                                                <div class="file-info p-2 bg-light rounded">
                                                    <i class="fas fa-file-alt"></i>
                                                    <span class="ml-2"><?php echo basename($submission['result_file']); ?></span>
                                                    <a href="handlers/download_result.php?file=<?php echo urlencode($submission['result_file']); ?>" 
                                                       class="btn btn-sm btn-info ml-2" target="_blank">
                                                        <i class="fas fa-download"></i> Download
                                                    </a>
                                                </div>
                                            </div>
                                            <?php endif; ?>

                                            <div class="form-group">
                                                <label>Feedback</label>
                                                <textarea name="feedback" class="form-control" rows="3"
                                                          placeholder="Provide feedback to the student"><?php echo htmlspecialchars($submission['feedback'] ?? ''); ?></textarea>
                                            </div>
                                            
                                            <div class="d-flex justify-content-between align-items-center">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-save"></i> Save Grade
                                                </button>
                                                <?php if ($submission['points'] !== null): ?>
                                                    <span class="text-muted">
                                                        Last graded: <?php echo date('M j, Y g:i A', strtotime($submission['graded_at'])); ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </form>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-secondary mt-3">
                                        <i class="fas fa-info-circle"></i> No submission yet
                                    </div>
                                <?php endif; ?>
                            </div>
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
        document.querySelector('.custom-file-input').addEventListener('change', function(e) {
            var fileName = e.target.files[0]?.name || 'Choose file';
            var next = e.target.nextElementSibling;
            next.innerText = fileName;
        });

        async function submitGrade(event, submissionId) {
            event.preventDefault();
            const form = event.target;
            const points = parseFloat(form.points.value);

            // Validate points
            if (isNaN(points) || points < 0 || points > 100) {
                Swal.fire({
                    title: 'Invalid Points',
                    text: 'Please enter a number between 0 and 100',
                    icon: 'error'
                });
                return false;
            }

            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

            try {
                const formData = new FormData(form);
                formData.append('submission_id', submissionId);

                const response = await fetch('handlers/grade_submission.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();
                
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Grade and result file saved successfully',
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
                submitBtn.innerHTML = '<i class="fas fa-save"></i> Save Grade';
            }
            return false;
        }
    </script>
</body>
</html>
