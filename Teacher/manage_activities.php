<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['teacher_id'])) {
    header("Location: Teacher-Login.php");
    exit();
}

$db = new DbConnector();
$teacher_id = $_SESSION['teacher_id'];

// Fetch all activities for this teacher
$activities_query = "
    SELECT 
        a.activity_id,
        a.title,
        a.description,
        a.type,
        a.due_date,
        a.points,
        a.status,
        a.created_at,
        sec.section_name,
        sub.subject_name,
        sub.subject_code,
        (SELECT COUNT(*) FROM student_activity_submissions 
         WHERE activity_id = a.activity_id) as submission_count,
        (SELECT COUNT(*) FROM student_sections 
         WHERE section_id = sec.section_id 
         AND status = 'active') as total_students,
        (SELECT COUNT(*) FROM student_activity_submissions 
         WHERE activity_id = a.activity_id 
         AND points IS NOT NULL) as graded_count
    FROM activities a
    JOIN section_subjects ss ON a.section_subject_id = ss.id
    JOIN sections sec ON ss.section_id = sec.section_id
    JOIN subjects sub ON ss.subject_id = sub.id
    WHERE ss.teacher_id = ?
        AND ss.status = 'active'
        AND ss.academic_year_id = (
            SELECT id FROM academic_years 
            WHERE status = 'active' 
            LIMIT 1
        )
    ORDER BY a.created_at DESC";

$stmt = $db->prepare($activities_query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$activities = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch teacher's sections and subjects for the create form
$sections_query = "
    SELECT 
        ss.id as section_subject_id,
        s.section_name,
        sub.subject_name,
        sub.subject_code
    FROM section_subjects ss
    JOIN sections s ON ss.section_id = s.section_id
    JOIN subjects sub ON ss.subject_id = sub.id
    WHERE ss.teacher_id = ?
        AND ss.status = 'active'
        AND ss.academic_year_id = (
            SELECT id FROM academic_years 
            WHERE status = 'active' 
            LIMIT 1
        )
    ORDER BY s.section_name, sub.subject_name";

$stmt = $db->prepare($sections_query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$sections = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Add this function to handle file downloads
function getActivityFiles($activity_id) {
    global $db;
    $query = "SELECT file_id, file_name, file_path, file_type 
              FROM activity_files 
              WHERE activity_id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $activity_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Activities - CamerinoHub</title>
    
    <!-- External CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/dashboard-shared.css">
    <style>
        .activity-card {
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .activity-header {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        .activity-body {
            padding: 15px;
        }
        .activity-footer {
            padding: 15px;
            background: #f8f9fa;
            border-top: 1px solid #eee;
        }
        .subject-badge {
            font-size: 0.8rem;
            padding: 5px 10px;
            border-radius: 15px;
            background: #e3f2fd;
            color: #1976d2;
        }
        .progress {
            height: 6px;
            margin-top: 5px;
        }
        .dropdown-toggle::after {
            display: none; /* Removes the default dropdown arrow */
        }
        
        .dropdown-menu {
            min-width: 200px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            border: 1px solid rgba(0,0,0,0.1);
        }
        
        .dropdown-item {
            padding: 8px 20px;
            color: #333;
        }
        
        .dropdown-item:hover {
            background-color: #f8f9fa;
        }
        
        .dropdown-item i {
            width: 20px;
            text-align: center;
            margin-right: 8px;
        }
        
        .btn-link {
            color: #6c757d;
            padding: 5px 10px;
        }
        
        .btn-link:hover {
            color: #343a40;
            background-color: rgba(0,0,0,0.05);
            border-radius: 4px;
        }
        
        .text-danger {
            color: #dc3545 !important;
        }
        
        .text-danger:hover {
            background-color: #fff5f5;
        }

        /* Add these to your existing styles */
        .btn-icon {
            padding: 0.25rem 0.5rem;
            background: transparent;
            border: none;
            color: #6c757d;
        }

        .btn-icon:hover,
        .btn-icon:focus {
            color: #343a40;
            background-color: rgba(0,0,0,0.05);
        }

        .dropdown-item {
            padding: 0.5rem 1rem;
            cursor: pointer;
        }

        .dropdown-item i {
            width: 1.25rem;
            text-align: center;
        }

        .dropdown-item:hover {
            background-color: #f8f9fa;
        }

        .dropdown-item.text-danger:hover {
            background-color: #fff5f5;
        }

        .dropdown-divider {
            margin: 0.5rem 0;
        }

        /* Add these to your existing styles */
        .action-buttons {
            display: flex;
            align-items: center;
        }

        .action-buttons .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            line-height: 1.5;
            border-radius: 0.2rem;
        }

        .action-buttons .btn i {
            font-size: 0.875rem;
        }

        /* Hover effects */
        .btn-outline-primary:hover {
            background-color: #007bff;
            color: white;
        }

        .btn-outline-info:hover {
            background-color: #17a2b8;
            color: white;
        }

        .btn-outline-warning:hover {
            background-color: #ffc107;
            color: #212529;
        }

        .btn-outline-danger:hover {
            background-color: #dc3545;
            color: white;
        }

        /* Add tooltip styles */
        [title] {
            position: relative;
            cursor: pointer;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <?php include 'includes/navigation.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="content-header d-flex justify-content-between align-items-center">
                <div>
                    <h1>Manage Activities</h1>
                    <p class="text-muted">Create and manage your class activities</p>
                </div>
                <button class="btn btn-primary" data-toggle="modal" data-target="#createActivityModal">
                    <i class="fas fa-plus"></i> Create Activity
                </button>
            </div>

            <!-- Activity Filters -->
            <div class="mb-4">
                <div class="btn-group">
                    <button class="btn btn-outline-primary active" data-filter="all">All Active</button>
                    <button class="btn btn-outline-primary" data-filter="assignment">Assignments</button>
                    <button class="btn btn-outline-primary" data-filter="quiz">Quizzes</button>
                    <button class="btn btn-outline-primary" data-filter="activity">Activities</button>
                    <button class="btn btn-outline-secondary" data-filter="archived">Archived</button>
                </div>
            </div>

            <!-- Activities List -->
            <div class="activities-container">
                <?php foreach ($activities as $activity): ?>
                    <div class="card activity-card" 
                         data-type="<?php echo $activity['type']; ?>"
                         data-status="<?php echo $activity['status']; ?>">
                        <div class="activity-header d-flex justify-content-between align-items-center">
                            <div>
                                <span class="subject-badge"><?php echo htmlspecialchars($activity['subject_code']); ?></span>
                                <?php if ($activity['status'] === 'archived'): ?>
                                    <span class="badge badge-secondary ml-2">Archived</span>
                                <?php endif; ?>
                                <h5 class="mt-2 mb-1"><?php echo htmlspecialchars($activity['title']); ?></h5>
                                <small class="text-muted">
                                    <?php echo htmlspecialchars($activity['section_name']); ?> | 
                                    Due: <?php echo date('M j, Y', strtotime($activity['due_date'])); ?>
                                </small>
                            </div>
                            <div class="action-buttons">
                                <a href="view_submissions.php?id=<?php echo $activity['activity_id']; ?>" 
                                   class="btn btn-sm btn-outline-primary mr-1" 
                                   title="View Submissions">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                <button type="button" 
                                        class="btn btn-sm btn-outline-info mr-1 edit-activity" 
                                        data-id="<?php echo htmlspecialchars($activity['activity_id']); ?>"
                                        title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                
                                <button type="button" 
                                        class="btn btn-sm btn-outline-warning mr-1" 
                                        onclick="toggleActivityStatus(<?php echo $activity['activity_id']; ?>)"
                                        title="<?php echo $activity['status'] === 'active' ? 'Archive' : 'Restore'; ?>">
                                    <i class="fas fa-archive"></i>
                                </button>
                                
                                <button type="button" 
                                        class="btn btn-sm btn-outline-danger" 
                                        onclick="deleteActivity(<?php echo htmlspecialchars($activity['activity_id']); ?>)"
                                        title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="activity-body">
                            <p><?php echo htmlspecialchars($activity['description']); ?></p>
                            <div class="submission-stats">
                                <small class="text-muted">
                                    Submissions: <?php echo $activity['submission_count']; ?>/<?php echo $activity['total_students']; ?>
                                </small>
                                <div class="progress">
                                    <div class="progress-bar" style="width: <?php echo ($activity['submission_count']/$activity['total_students'])*100; ?>%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Create Activity Modal -->
    <div class="modal fade" id="createActivityModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Activity</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="createActivityForm" action="create_activity.php" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>Section & Subject</label>
                            <select name="section_subject_id" class="form-control" required>
                                <option value="">Select Section & Subject</option>
                                <?php foreach ($sections as $section): ?>
                                    <option value="<?php echo $section['section_subject_id']; ?>">
                                        <?php echo htmlspecialchars($section['section_name'] . ' - ' . $section['subject_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Activity Type</label>
                            <select name="type" class="form-control" required>
                                <option value="assignment">Assignment</option>
                                <option value="quiz">Quiz</option>
                                <option value="activity">Activity</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="3" required></textarea>
                        </div>

                        <div class="form-row">
                            <div class="col">
                                <div class="form-group">
                                    <label>Due Date</label>
                                    <input type="datetime-local" name="due_date" class="form-control" required 
                                           value="<?php echo date('Y-m-d\TH:i', strtotime('+1 week')); ?>">
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label>Points</label>
                                    <input type="number" name="points" class="form-control" required value="100" min="0">
                                </div>
                            </div>
                        </div>

                        <div id="quizOptions" style="display: none;">
                            <div class="form-group">
                                <label>Quiz Link</label>
                                <input type="url" name="quiz_link" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Time Limit (minutes)</label>
                                <input type="number" name="time_limit" class="form-control">
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="prevent_tab_switch" class="form-check-input" id="preventTabSwitch">
                                <label class="form-check-label" for="preventTabSwitch">Prevent Tab Switching</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="fullscreen_required" class="form-check-input" id="fullscreenRequired">
                                <label class="form-check-label" for="fullscreenRequired">Require Fullscreen</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Activity Files</label>
                            <input type="file" name="activity_files[]" class="form-control-file" multiple>
                            <small class="text-muted">You can upload multiple files. Allowed types: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" form="createActivityForm" class="btn btn-primary">Create Activity</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Activity Modal -->
    <div class="modal fade" id="editActivityModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Activity</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="editActivityForm" action="update_activity.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="activity_id" id="activity_id">
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="form-row">
                            <div class="col">
                                <div class="form-group">
                                    <label>Due Date</label>
                                    <input type="datetime-local" name="due_date" class="form-control" required>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label>Points</label>
                                    <input type="number" name="points" class="form-control" required value="100" min="0">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Activity Type</label>
                            <select name="type" class="form-control" required>
                                <option value="assignment">Assignment</option>
                                <option value="quiz">Quiz</option>
                                <option value="activity">Activity</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Section & Subject</label>
                            <select name="section_subject_id" class="form-control" required>
                                <option value="">Select Section & Subject</option>
                                <?php foreach ($sections as $section): ?>
                                    <option value="<?php echo $section['section_subject_id']; ?>">
                                        <?php echo htmlspecialchars($section['section_name'] . ' - ' . $section['subject_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div id="quizOptions" style="display: none;">
                            <div class="form-group">
                                <label>Quiz Link</label>
                                <input type="url" name="quiz_link" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Time Limit (minutes)</label>
                                <input type="number" name="time_limit" class="form-control">
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="prevent_tab_switch" class="form-check-input" id="preventTabSwitch">
                                <label class="form-check-label" for="preventTabSwitch">Prevent Tab Switching</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="fullscreen_required" class="form-check-input" id="fullscreenRequired">
                                <label class="form-check-label" for="fullscreenRequired">Require Fullscreen</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Attachments</label>
                            <input type="file" name="attachments[]" class="form-control-file" multiple>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" form="editActivityForm" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <script>
    // Show/hide quiz options based on activity type
    document.querySelector('select[name="type"]').addEventListener('change', function() {
        const quizOptions = document.getElementById('quizOptions');
        quizOptions.style.display = this.value === 'quiz' ? 'block' : 'none';
    });

    // Filter activities
    document.querySelectorAll('[data-filter]').forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.dataset.filter;
            
            // Update active button
            document.querySelectorAll('[data-filter]').forEach(btn => 
                btn.classList.remove('active'));
            this.classList.add('active');
            
            // Filter activities
            document.querySelectorAll('.activity-card').forEach(card => {
                if (filter === 'archived') {
                    // Show only archived activities
                    card.style.display = card.dataset.status === 'archived' ? 'block' : 'none';
                } else if (filter === 'all') {
                    // Show all active (non-archived) activities
                    card.style.display = card.dataset.status !== 'archived' ? 'block' : 'none';
                } else {
                    // Show active activities of specific type
                    card.style.display = (card.dataset.type === filter && card.dataset.status !== 'archived') 
                        ? 'block' : 'none';
                }
            });
        });
    });

    // Edit activity
    $('.edit-activity').click(function() {
        const activityId = $(this).data('id');
        
        // Fetch activity details
        $.get('get_activity.php', { activity_id: activityId }, function(response) {
            if (response.success) {
                const activity = response.activity;
                $('#editActivityForm input[name="activity_id"]').val(activityId);
                $('#editActivityForm input[name="title"]').val(activity.title);
                $('#editActivityForm textarea[name="description"]').val(activity.description);
                $('#editActivityForm input[name="due_date"]').val(activity.due_date);
                $('#editActivityForm input[name="points"]').val(activity.points);
                $('#editActivityForm select[name="type"]').val(activity.type);
                $('#editActivityForm select[name="section_subject_id"]').val(activity.section_subject_id);
                $('#editActivityModal').modal('show');
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        });
    });

    // Toggle activity status (Archive/Restore)
    function toggleActivityStatus(activityId, currentStatus) {
        Swal.fire({
            title: 'Are you sure?',
            text: `Do you want to ${currentStatus === 'active' ? 'archive' : 'restore'} this activity?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('toggle_activity_status.php', {
                    activity_id: activityId,
                    current_status: currentStatus
                }, function(response) {
                    if (response.success) {
                        Swal.fire('Success', response.message, 'success')
                        .then(() => location.reload());
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                });
            }
        });
    }

    // Delete activity handler
    $(document).ready(function() {
        $('.delete-activity').on('click', function() {
            const activityId = $(this).data('id');
            
            Swal.fire({
                title: 'Are you sure?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state
                    Swal.fire({
                        title: 'Deleting...',
                        didOpen: () => {
                            Swal.showLoading();
                        },
                        allowOutsideClick: false
                    });

                    // Send delete request
                    $.ajax({
                        url: 'delete_activity.php',
                        type: 'POST',
                        data: {
                            activity_id: activityId
                        },
                        success: function(response) {
                            try {
                                if (typeof response === 'string') {
                                    response = JSON.parse(response);
                                }
                                
                                if (response.success) {
                                    Swal.fire({
                                        title: 'Deleted!',
                                        text: response.message,
                                        icon: 'success'
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    throw new Error(response.message || 'Failed to delete activity');
                                }
                            } catch (error) {
                                console.error('Delete error:', error);
                                Swal.fire('Error!', error.message, 'error');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Ajax error:', {xhr, status, error});
                            Swal.fire('Error!', 'Failed to delete activity', 'error');
                        }
                    });
                }
            });
        });
    });

    document.getElementById('createActivityForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Basic validation
        const requiredFields = ['section_subject_id', 'type', 'title', 'description', 'due_date', 'points'];
        let isValid = true;
        
        requiredFields.forEach(field => {
            const input = this.elements[field];
            if (!input.value.trim()) {
                isValid = false;
                input.classList.add('is-invalid');
            } else {
                input.classList.remove('is-invalid');
            }
        });
        
        if (!isValid) {
            Swal.fire({
                title: 'Error!',
                text: 'Please fill in all required fields',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            return;
        }
        
        const formData = new FormData(this);
        
        // Debug log
        console.log('Submitting form data:');
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }
        
        fetch('create_activity.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: 'Success!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: data.message || 'An error occurred while creating the activity',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error!',
                text: 'An unexpected error occurred. Please check the console for details.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        });
    });

    // Add these functions to your existing script section
    function toggleActivityStatus(activityId, currentStatus) {
        const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
        const action = currentStatus === 'active' ? 'archive' : 'restore';

        Swal.fire({
            title: 'Are you sure?',
            text: `Do you want to ${action} this activity?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('update_activity_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ 
                        activity_id: activityId,
                        status: newStatus 
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire(
                            'Success!',
                            `Activity has been ${action}d.`,
                            'success'
                        ).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire(
                            'Error!',
                            data.message || 'An error occurred',
                            'error'
                        );
                    }
                });
            }
        });
    }

    // Edit activity function
    $(document).on('click', '.edit-activity', function(e) {
        e.preventDefault();
        const activityId = $(this).data('id');
        
        // Fetch activity details
        fetch(`get_activity_details.php?id=${activityId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const activity = data.activity;
                    // Populate edit modal with activity details
                    $('#editActivityModal').modal('show');
                    // Populate form fields
                    $('#editActivityForm input[name="title"]').val(activity.title);
                    $('#editActivityForm textarea[name="description"]').val(activity.description);
                    $('#editActivityForm input[name="due_date"]').val(activity.due_date);
                    $('#editActivityForm input[name="points"]').val(activity.points);
                    $('#editActivityForm select[name="type"]').val(activity.type);
                    $('#editActivityForm select[name="section_subject_id"]').val(activity.section_subject_id);
                    $('#editActivityForm input[name="activity_id"]').val(activityId);
                    
                    // Show/hide quiz options based on type
                    const quizOptions = $('#editActivityForm #quizOptions');
                    if (activity.type === 'quiz') {
                        quizOptions.show();
                        $('#editActivityForm input[name="quiz_link"]').val(activity.quiz_link);
                        $('#editActivityForm input[name="time_limit"]').val(activity.quiz_duration);
                        $('#editActivityForm input[name="prevent_tab_switch"]').prop('checked', activity.prevent_tab_switch == 1);
                        $('#editActivityForm input[name="fullscreen_required"]').prop('checked', activity.fullscreen_required == 1);
                    } else {
                        quizOptions.hide();
                    }
                } else {
                    Swal.fire('Error!', data.message || 'Failed to load activity details', 'error');
                }
            });
    });

    // Add this to your existing JavaScript
    $(document).ready(function() {
        // Initialize all dropdowns
        $('[data-toggle="dropdown"]').dropdown();
        
        // Prevent dropdown from closing when clicking inside
        $('.dropdown-menu').on('click', function(e) {
            e.stopPropagation();
        });
        
        // Close dropdown when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.dropdown').length) {
                $('.dropdown-menu').removeClass('show');
            }
        });

        // Close dropdown after clicking an item
        $('.dropdown-item').on('click', function() {
            $(this).closest('.dropdown').find('.dropdown-toggle').dropdown('toggle');
        });
    });

    // Function to handle activity status toggle (Archive/Restore)
    function toggleActivityStatus(activityId) {
        $.ajax({
            url: 'toggle_activity_status.php',
            type: 'POST',
            data: {
                activity_id: activityId
            },
            success: function(response) {
                try {
                    const result = JSON.parse(response);
                    if (result.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: result.message,
                            icon: 'success'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        throw new Error(result.message);
                    }
                } catch (error) {
                    Swal.fire('Error!', error.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error!', 'Failed to update activity status', 'error');
            }
        });
    }

    // Function to handle activity deletion
    function deleteActivity(activityId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                Swal.fire({
                    title: 'Deleting...',
                    didOpen: () => {
                        Swal.showLoading();
                    },
                    allowOutsideClick: false
                });

                // Send delete request
                $.ajax({
                    url: 'delete_activity.php',
                    type: 'POST',
                    data: { activity_id: activityId },
                    success: function(response) {
                        try {
                            // Parse response if it's a string
                            const result = typeof response === 'string' ? JSON.parse(response) : response;
                            
                            if (result.success) {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: result.message,
                                    icon: 'success'
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                throw new Error(result.message || 'Failed to delete activity');
                            }
                        } catch (error) {
                            console.error('Delete error:', error);
                            Swal.fire('Error!', error.message, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Ajax error:', {xhr, status, error});
                        Swal.fire('Error!', 'Failed to delete activity', 'error');
                    }
                });
            }
        });
    }

    // Handle edit activity
    $(document).ready(function() {
        $('.edit-activity').click(function() {
            const activityId = $(this).data('id');
            console.log('Activity ID:', activityId); // Debug log
            
            // Fetch activity details
            $.ajax({
                url: 'get_activity_details.php',
                type: 'GET',
                data: { id: activityId },
                dataType: 'json',
                success: function(response) {
                    console.log('Response:', response); // Debug log
                    if (response.success) {
                        const activity = response.activity;
                        
                        // Populate the edit form
                        $('#editActivityForm input[name="activity_id"]').val(activity.activity_id);
                        $('#editActivityForm input[name="title"]').val(activity.title);
                        $('#editActivityForm textarea[name="description"]').val(activity.description);
                        $('#editActivityForm input[name="due_date"]').val(activity.due_date);
                        $('#editActivityForm input[name="points"]').val(activity.points);
                        $('#editActivityForm select[name="type"]').val(activity.type);
                        $('#editActivityForm select[name="section_subject_id"]').val(activity.section_subject_id);
                        
                        // Show the modal
                        $('#editActivityModal').modal('show');
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error details:', {
                        status: status,
                        error: error,
                        response: xhr.responseText
                    });
                    Swal.fire('Error!', 'Failed to fetch activity details', 'error');
                }
            });
        });
    });
    </script>
</body>
</html>
