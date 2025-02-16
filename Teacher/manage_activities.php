<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['teacher_id'])) {
    header("Location: Teacher-Login.php");
    exit();
}

$db = new DbConnector();
$teacher_id = $_SESSION['teacher_id'];

// Updated query to fetch all types of activities
$query = "SELECT 
    a.activity_id,
    a.title,
    a.description,
    a.type,
    a.points,
    a.due_date,
    a.status,
    a.created_at,
    s.section_name,
    sub.subject_name,
    sub.subject_code,
    (SELECT COUNT(*) FROM student_activity_submissions sas 
     WHERE sas.activity_id = a.activity_id) as submission_count
FROM activities a
JOIN section_subjects ss ON a.section_subject_id = ss.id
JOIN sections s ON ss.section_id = s.section_id
JOIN subjects sub ON ss.subject_id = sub.id
WHERE ss.teacher_id = ?
ORDER BY a.created_at DESC";

$stmt = $db->prepare($query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$activities = $result->fetch_all(MYSQLI_ASSOC);
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
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/dashboard-shared.css">
    <link rel="stylesheet" href="css/manage-activities.css">
</head>
<body>
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    // Define the functions before they're used
    function deleteActivity(activityId) {
        if (!activityId) return;
        
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('activity_id', activityId);

                fetch('handlers/delete_activity.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Deleted!',
                            text: data.message,
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        throw new Error(data.message || 'Failed to delete activity');
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error!',
                        text: error.message,
                        icon: 'error'
                    });
                });
            }
        });
    }

    function archiveActivity(activityId, currentStatus) {
        if (!activityId) return;
        
        Swal.fire({
            title: 'Are you sure?',
            text: `Do you want to ${currentStatus === 'active' ? 'archive' : 'restore'} this activity?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('activity_id', activityId);
                formData.append('action', currentStatus === 'active' ? 'archive' : 'restore');

                fetch('handlers/toggle_activity_status.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: data.message,
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        throw new Error(data.message || 'Failed to update activity');
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error!',
                        text: error.message,
                        icon: 'error'
                    });
                });
            }
        });
    }
    </script>

    <div class="wrapper">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Page Content -->
        <div class="content-wrapper">
            <!-- Navbar -->
            <?php include 'includes/navigation.php'; ?>

            <!-- Main Content -->
            <div class="activities-container">
                <!-- Create Buttons -->
                <div class="create-buttons">
                    <a href="create_activity.php" class="btn btn-success mr-2">
                        <i class="fas fa-plus-circle"></i> New Activity
                    </a>
                    <a href="create_assignment.php" class="btn btn-primary mr-2">
                        <i class="fas fa-file-alt"></i> New Assignment
                    </a>
                    <a href="create_quiz.php" class="btn btn-info mr-2">
                        <i class="fas fa-question-circle"></i> New Quiz
                    </a>
                </div>

                <!-- Filter Buttons -->
                <div class="filter-buttons">
                    <div class="btn-group" role="group" aria-label="Activity filters">
                        <button type="button" class="btn btn-outline-primary active" data-filter="all">
                            <i class="fas fa-list"></i> All Active
                        </button>
                        <button type="button" class="btn btn-outline-primary" data-filter="assignment">
                            <i class="fas fa-file-alt"></i> Assignments
                        </button>
                        <button type="button" class="btn btn-outline-primary" data-filter="quiz">
                            <i class="fas fa-question-circle"></i> Quizzes
                        </button>
                        <button type="button" class="btn btn-outline-primary" data-filter="activity">
                            <i class="fas fa-tasks"></i> Activities
                        </button>
                        <button type="button" class="btn btn-outline-secondary" data-filter="archived">
                            <i class="fas fa-archive"></i> Archived
                        </button>
                    </div>
                </div>

                <!-- Activities Grid -->
                <div class="row">
                    <?php foreach ($activities as $activity): ?>
                    <div class="col-md-6 mb-4">
                        <div class="activity-card" 
                             data-type="<?php echo htmlspecialchars($activity['type']); ?>"
                             data-status="<?php echo htmlspecialchars($activity['status'] ?? 'active'); ?>">
                            
                            <div class="activity-header">
                                <span class="activity-type-badge">
                                    <?php 
                                    switch($activity['type']) {
                                        case 'quiz':
                                            echo '<i class="fas fa-question-circle"></i> Quiz';
                                            break;
                                        case 'assignment':
                                            echo '<i class="fas fa-file-alt"></i> Assignment';
                                            break;
                                        case 'activity':
                                            echo '<i class="fas fa-tasks"></i> Activity';
                                            break;
                                    }
                                    ?>
                                </span>
                                <h5><?php echo htmlspecialchars($activity['title']); ?></h5>
                                <span class="status-badge <?php echo $activity['status'] === 'active' ? 'status-active' : 'status-archived'; ?>">
                                    <?php echo ucfirst($activity['status']); ?>
                                </span>
                            </div>

                            <div class="activity-body">
                                <div class="activity-meta">
                                    <span><i class="fas fa-clock"></i> Due: <?php echo date('M d, Y', strtotime($activity['due_date'])); ?></span>
                                    <span><i class="fas fa-users"></i> <?php echo htmlspecialchars($activity['section_name']); ?></span>
                                    <span><i class="fas fa-star"></i> Points: <?php echo htmlspecialchars($activity['points']); ?></span>
                                </div>

                                <div class="activity-description">
                                    <?php echo nl2br(htmlspecialchars($activity['description'])); ?>
                                </div>

                                <div class="submission-stats">
                                    <div class="stat-item">
                                        <div class="stat-value"><?php echo $activity['submission_count']; ?></div>
                                        <div class="stat-label">Submissions</div>
                                    </div>
                                </div>
                            </div>

                            <div class="action-buttons">
                                <a href="<?php 
                                    if ($activity['type'] === 'quiz') {
                                        echo 'view_submissions.php?activity_id=' . $activity['activity_id'];
                                    } else {
                                        echo 'view_activity_submissions.php?activity_id=' . $activity['activity_id'];
                                    }
                                ?>" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i> View Submissions
                                </a>
                                
                                <a href="edit_activity.php?id=<?php echo $activity['activity_id']; ?>&type=<?php echo $activity['type']; ?>" 
                                   class="btn btn-primary">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                
                                <button type="button" 
                                        class="btn btn-warning"
                                        onclick="archiveActivity(<?php echo $activity['activity_id']; ?>, '<?php echo $activity['status']; ?>')">
                                    <i class="fas fa-archive"></i> 
                                    <?php echo $activity['status'] === 'active' ? 'Archive' : 'Restore'; ?>
                                </button>
                                
                                <button type="button" 
                                        class="btn btn-danger"
                                        onclick="deleteActivity(<?php echo $activity['activity_id']; ?>)">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Your existing scripts -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize filter functionality
        const filterButtons = document.querySelectorAll('[data-filter]');
        const activityCards = document.querySelectorAll('.activity-card');

        // Function to handle filtering
        function filterActivities(filterType) {
            activityCards.forEach(card => {
                const cardType = card.getAttribute('data-type');
                const cardStatus = card.getAttribute('data-status');
                const cardContainer = card.closest('.col-md-6');

                if (filterType === 'all') {
                    cardContainer.style.display = cardStatus === 'active' ? 'block' : 'none';
                } else if (filterType === 'archived') {
                    cardContainer.style.display = cardStatus === 'archived' ? 'block' : 'none';
                } else if (filterType === 'assignment' || filterType === 'quiz' || filterType === 'activity') {
                    cardContainer.style.display = (cardType === filterType && cardStatus === 'active') ? 'block' : 'none';
                }
            });
        }

        // Add click event listeners to filter buttons
        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Remove active class from all buttons
                filterButtons.forEach(btn => btn.classList.remove('active'));
                // Add active class to clicked button
                this.classList.add('active');
                // Apply filter
                filterActivities(this.getAttribute('data-filter'));
            });
        });

        // Set initial filter to 'all'
        document.querySelector('[data-filter="all"]').click();

        // Update the delete function
        window.deleteActivity = function(activityId) {
            if (!activityId) return;
            
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('activity_id', activityId);

                    fetch('handlers/delete_activity.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: data.message,
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            // Check for foreign key constraint error
                            if (data.error && data.error.includes('foreign key constraint fails')) {
                                Swal.fire({
                                    title: 'Cannot Delete',
                                    text: 'You can\'t delete this Activity, Assignment or Quiz because there are student submissions.',
                                    icon: 'warning',
                                    confirmButtonColor: '#3085d6'
                                });
                            } else {
                                throw new Error(data.message || 'Failed to delete activity');
                            }
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Error!',
                            text: 'You can\'t delete this Activity, Assignment or Quiz because there are student submissions.',
                            icon: 'warning'
                        });
                    });
                }
            });
        };

        // Keep existing archive functionality
        window.archiveActivity = function(activityId, currentStatus) {
            if (!activityId) return;
            
            Swal.fire({
                title: 'Are you sure?',
                text: `Do you want to ${currentStatus === 'active' ? 'archive' : 'restore'} this activity?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('activity_id', activityId);
                    formData.append('action', currentStatus === 'active' ? 'archive' : 'restore');

                    fetch('handlers/toggle_activity_status.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Success!',
                                text: data.message,
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            throw new Error(data.message || 'Failed to update activity');
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Error!',
                            text: error.message,
                            icon: 'error'
                        });
                    });
                }
            });
        };

        // Initialize sidebar toggle
        const sidebarCollapse = document.getElementById('sidebarCollapse');
        if (sidebarCollapse) {
            sidebarCollapse.addEventListener('click', function() {
                document.querySelector('.sidebar').classList.toggle('active');
                document.querySelector('.content-wrapper').classList.toggle('active');
            });
        }
    });
    </script>
</body>
</html>
