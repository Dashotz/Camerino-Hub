<?php
session_start();
require_once('../db/dbConnector.php');

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: Student-Login.php");
    exit();
}

$db = new DbConnector();
$student_id = $_SESSION['id'];

// Fetch announcements with quiz details
$announcements_query = "
    SELECT 
        a.*,
        t.firstname as teacher_firstname,
        t.lastname as teacher_lastname,
        s.section_name,
        sub.subject_name,
        act.activity_id,
        act.title as quiz_title,
        act.description as quiz_description,
        act.due_date as quiz_due_date,
        COALESCE(sas.status, 'pending') as submission_status,
        a.attachment
    FROM announcements a
    JOIN teacher t ON a.teacher_id = t.teacher_id
    JOIN sections s ON a.section_id = s.section_id
    JOIN subjects sub ON a.subject_id = sub.id
    LEFT JOIN activities act ON a.reference_id = act.activity_id AND a.type = 'quiz'
    LEFT JOIN student_activity_submissions sas ON act.activity_id = sas.activity_id 
        AND sas.student_id = ?
    WHERE a.status = 'active'
    ORDER BY a.created_at DESC";

$stmt = $db->prepare($announcements_query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$announcements = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements - Student Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/dashboard-shared.css">
    <link rel="stylesheet" href="css/announcements.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
	<link rel="icon" href="../images/light-logo.png">
    <style>
        .quiz-preview {
            margin-top: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }
        .quiz-actions {
            margin-top: 10px;
        }
        .quiz-status {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
            text-align: center;
            min-width: 100px;
        }
        .status-pending {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }
        .status-submitted {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .status-missing {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .announcements-container {   
            top: -1.5%;       
            max-width: 1200px;
            margin: 0 auto;
        }

        .announcement-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            transition: transform 0.2s, box-shadow 0.2s;
            border: 1px solid #eaeaea;
            overflow: hidden;
        }

        .announcement-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .announcement-header {
            padding: 10px 15px;
            border-bottom: 1px solid #f0f0f0;
            background: #f8f9fa;
        }

        .announcement-header h3 {
            margin: 0;
            color: #2c3e50;
            font-size: 1.3rem;
            font-weight: 600;
        }

        .announcement-meta {
            display: block;
            margin-top: 8px;
            color: #6c757d;
            font-size: 0.9rem;
        }

        .announcement-meta i {
            margin-right: 5px;
        }

        .announcement-content {
            padding: 10px 15px;
            color: #4a5568;
            line-height: 1.6;
        }

        .quiz-preview {
            margin-top: -20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
            border: 1px solid #e9ecef;
        }

        .quiz-preview h4 {
            color: #2d3748;
            font-size: 1.1rem;
            margin-bottom: 12px;
            font-weight: 600;
        }

        .quiz-actions {
            margin-top: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .quiz-status {
            display: inline-flex;
            align-items: center;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
            text-align: center;
            min-width: 100px;
            justify-content: center;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }

        .status-pending::before {
            content: '⏳';
            margin-right: 5px;
        }

        .status-submitted {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-submitted::before {
            content: '✓';
            margin-right: 5px;
        }

        .status-missing {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .status-missing::before {
            content: '⚠';
            margin-right: 5px;
        }

        .filter-section {
            margin-bottom: 25px;
            padding: 15px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .announcement-type-filter {
            padding: 8px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #fff;
            color: #495057;
            font-size: 0.95rem;
            width: 200px;
            transition: all 0.3s ease;
        }

        .announcement-type-filter:focus {
            outline: none;
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }

        .page-title {
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e9ecef;
        }

        .page-title h1 {
            color: #2d3748;
            font-size: 1.8rem;
            font-weight: 600;
            margin: 0;
        }

        /* Add animation for new announcements */
        .announcement-card.new {
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Due date styling */
        .due-date {
            display: inline-flex;
            align-items: center;
            margin-top: 10px;
            padding: 5px 10px;
            background: #e8f4ff;
            border-radius: 5px;
            color: #0066cc;
            font-size: 0.9rem;
        }

        .due-date i {
            margin-right: 5px;
        }

        /* Add these to your existing styles */
        .attachment-section {
            margin-top: 15px;
        }
        
        .attachment-box {
            display: inline-flex;
            align-items: center;
            padding: 10px 15px;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .attachment-box:hover {
            background: #e9ecef;
            border-color: #dee2e6;
        }
        
        .attachment-icon {
            margin-right: 10px;
            font-size: 1.2em;
            color: #6c757d;
        }
        
        .attachment-link {
            color: #007bff;
            text-decoration: none;
            font-size: 0.95em;
            display: flex;
            align-items: center;
        }
        
        .attachment-link:hover {
            color: #0056b3;
            text-decoration: underline;
        }
        
        .attachment-box .fa-paperclip {
            margin-right: 10px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <?php include 'includes/navigation.php'; ?>

    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="announcements-container">
                <div class="page-title">
                    <h1>Announcements</h1>
                </div>

                <div class="filter-section">
                    <div class="filter-group">
                        <select class="announcement-type-filter">
                            <option value="">All Announcements</option>
                            <option value="quiz">Quizzes</option>
                            <option value="activity">Activities</option>
                            <option value="assignment">Assignments</option>
                            <option value="normal">General Announcements</option>
                        </select>
                    </div>
                </div>

                <div class="announcements-list">
                    <?php foreach ($announcements as $announcement): ?>
                        <div class="announcement-card" data-type="<?php echo htmlspecialchars($announcement['type'] ?? ''); ?>">
                            <div class="announcement-header">
                                <h3><?php echo htmlspecialchars($announcement['title'] ?? 'Untitled Announcement'); ?></h3>
                                <span class="announcement-meta">
                                    Posted by: <?php echo htmlspecialchars(($announcement['teacher_firstname'] ?? '') . ' ' . ($announcement['teacher_lastname'] ?? '')); ?>
                                    | <?php echo date('M j, Y g:i A', strtotime($announcement['created_at'] ?? 'now')); ?>
                                </span>
                            </div>
                            
                            <div class="announcement-content">
                                <p><?php echo nl2br(htmlspecialchars($announcement['content'] ?? '')); ?></p>
                                
                                <?php if (!empty($announcement['attachment'])): ?>
                                    <div class="attachment-section">
                                        <hr>
                                        <div class="attachment-box">
                                            <i class="fas fa-paperclip"></i>
                                            <?php
                                            $filename = basename($announcement['attachment']);
                                            $file_extension = pathinfo($filename, PATHINFO_EXTENSION);
                                            $icon_class = 'fa-file';
                                            
                                            // Set appropriate icon based on file type
                                            switch(strtolower($file_extension)) {
                                                case 'pdf':
                                                    $icon_class = 'fa-file-pdf';
                                                    break;
                                                case 'doc':
                                                case 'docx':
                                                    $icon_class = 'fa-file-word';
                                                    break;
                                                case 'xls':
                                                case 'xlsx':
                                                    $icon_class = 'fa-file-excel';
                                                    break;
                                                case 'jpg':
                                                case 'jpeg':
                                                case 'png':
                                                case 'gif':
                                                    $icon_class = 'fa-file-image';
                                                    break;
                                            }
                                            ?>
                                            <i class="fas <?php echo $icon_class; ?> attachment-icon"></i>
                                            <a href="../<?php echo htmlspecialchars($announcement['attachment']); ?>" 
                                               class="attachment-link" 
                                               download="<?php echo htmlspecialchars($filename); ?>"
                                               target="_blank">
                                                <?php echo htmlspecialchars($filename); ?>
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (($announcement['type'] ?? '') === 'quiz' && isset($announcement['activity_id'])): ?>
                                    <div class="quiz-preview">
                                        <h4><?php echo htmlspecialchars($announcement['quiz_title'] ?? 'Untitled Quiz'); ?></h4>
                                        <p><?php echo htmlspecialchars($announcement['quiz_description'] ?? 'No description available'); ?></p>
                                        <div class="due-date">
                                            <i class="fas fa-calendar-alt"></i>
                                            Due: <?php echo isset($announcement['quiz_due_date']) ? date('M j, Y g:i A', strtotime($announcement['quiz_due_date'])) : 'No due date set'; ?>
                                        </div>
                                        
                                        <div class="quiz-actions">
                                            <?php
                                            $status_class = '';
                                            $status_text = '';
                                            switch($announcement['submission_status']) {
                                                case 'submitted':
                                                    $status_class = 'status-submitted';
                                                    $status_text = 'Submitted';
                                                    break;
                                                case 'missing':
                                                    $status_class = 'status-missing';
                                                    $status_text = 'Missing';
                                                    break;
                                                default:
                                                    $status_class = 'status-pending';
                                                    $status_text = 'Pending';
                                            }
                                            ?>
                                            <span class="quiz-status <?php echo $status_class; ?>">
                                                <?php echo $status_text; ?>
                                            </span>
                                        </div>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
    function viewQuiz(quizId) {
        // Show loading state
        Swal.fire({
            title: 'Loading Quiz Details',
            text: 'Please wait...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Fetch quiz details
        $.ajax({
            url: 'get_quiz_details.php',
            method: 'GET',
            data: { quiz_id: quizId },
            dataType: 'json',
            success: function(response) {
                Swal.close();
                
                if (response.success) {
                    const quiz = response.data;
                    
                    // Show confirmation dialog
                    Swal.fire({
                        title: 'Start Quiz?',
                        html: `
                            <div class="text-left">
                                <p><strong>${quiz.title}</strong></p>
                                <p>${quiz.description}</p>
                                <hr>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-clock"></i> Duration: ${quiz.quiz_duration} minutes</li>
                                    <li><i class="fas fa-calendar"></i> Due: ${new Date(quiz.due_date).toLocaleString()}</li>
                                    <li><i class="fas fa-star"></i> Points: ${quiz.points}</li>
                                    <li><i class="fas fa-book"></i> Subject: ${quiz.subject_code} - ${quiz.subject_name}</li>
                                    <li><i class="fas fa-users"></i> Section: ${quiz.section_name}</li>
                                </ul>
                                <div class="alert alert-warning mt-3">
                                    <i class="fas fa-exclamation-triangle"></i> 
                                    <strong>Important:</strong>
                                    <ul class="mb-0">
                                        <li>Once started, you must complete the quiz in one session</li>
                                        ${quiz.prevent_tab_switch ? '<li>Tab switching is not allowed</li>' : ''}
                                        ${quiz.fullscreen_required ? '<li>Fullscreen mode is required</li>' : ''}
                                    </ul>
                                </div>
                            </div>
                        `,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Start Quiz',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = `quiz_iframe.php?quiz_id=${quizId}`;
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: response.message || 'Failed to load quiz details',
                        icon: 'error',
                        confirmButtonColor: '#d33'
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    title: 'Error',
                    text: 'Failed to connect to the server. Please check your internet connection.',
                    icon: 'error',
                    confirmButtonColor: '#d33'
                });
            }
        });
    }

    // Filter functionality
    $('.announcement-type-filter').change(function() {
        const selectedType = $(this).val();
        $('.announcement-card').each(function() {
            if (!selectedType || $(this).data('type') === selectedType) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Add this function to handle AJAX errors
    function handleAjaxError(xhr, status, error) {
        console.error('AJAX Error:', {xhr, status, error});
        let errorMessage = 'An error occurred while processing your request.';
        
        try {
            const response = JSON.parse(xhr.responseText);
            errorMessage = response.message || errorMessage;
        } catch (e) {
            console.error('Parse error:', e);
            errorMessage = 'Server returned an invalid response.';
        }

        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: errorMessage
        });
    }

    // Update the announcement creation function
    function createAnnouncement(formData) {
        $.ajax({
            url: 'handlers/announcement_handler.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                try {
                    // Ensure response is parsed as JSON if it's a string
                    const data = (typeof response === 'string') ? JSON.parse(response) : response;
                    
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: data.message || 'Announcement created successfully',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            // Refresh the page or update the announcements list
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Failed to create announcement'
                        });
                    }
                } catch (e) {
                    console.error('Parse error:', e);
                    console.error('Raw response:', response);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Invalid server response'
                    });
                }
            },
            error: handleAjaxError
        });
    }

    // Update the form submission handler
    $('#announcementForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('action', 'create');
        
        Swal.fire({
            title: 'Creating Announcement',
            text: 'Please wait...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        createAnnouncement(formData);
    });

    // Add this to your existing JavaScript
    $(document).ready(function() {
        // Handle attachment downloads
        $('.attachment-link').on('click', function(e) {
            e.preventDefault();
            
            const downloadUrl = $(this).attr('href');
            const filename = $(this).attr('download');
            
            // Show loading indicator
            Swal.fire({
                title: 'Downloading...',
                text: 'Please wait while we prepare your download',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Create a temporary link element
            const link = document.createElement('a');
            link.href = downloadUrl;
            link.download = filename;
            link.target = '_blank';
            
            // Append to body, click, and remove
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            // Close loading indicator after a short delay
            setTimeout(() => {
                Swal.close();
            }, 1000);
        });
    });
    </script>
</body>
</html>
