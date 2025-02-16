<?php
session_start();
require_once('../db/dbConnector.php');

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    header('Location: Student-Login.php');
    exit();
}

// Initialize database connection
$db = new DbConnector();
$student_id = $_SESSION['id'];

// Get student data
$stmt = $db->prepare("SELECT * FROM student WHERE student_id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();

// Get student's sections
$sections_query = "
    SELECT section_id 
    FROM student_sections 
    WHERE student_id = ? 
    AND status = 'active'";

$stmt = $db->prepare($sections_query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$sections_result = $stmt->get_result();
$section_ids = [];

while($row = $sections_result->fetch_assoc()) {
    $section_ids[] = $row['section_id'];
}

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10; // Items per page
$offset = ($page - 1) * $limit;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Notifications - CamerinoHub</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
	<link rel="icon" href="../images/light-logo.png">
    <style>
        body { 
            padding-top: 70px; 
            background-color: #f8f9fa;
        }
        .notification-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 15px;
            transition: transform 0.2s;
        }
        .notification-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .notification-header {
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .notification-body {
            padding: 20px;
        }
        .notification-meta {
            font-size: 0.85rem;
            color: #6c757d;
        }
        .badge-subject {
            background-color: #e9ecef;
            color: #495057;
            font-weight: 500;
        }
        .notification-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }
        .icon-quiz { background-color: #cce5ff; color: #004085; }
        .icon-activity { background-color: #d4edda; color: #155724; }
        .icon-assignment { background-color: #fff3cd; color: #856404; }
        .filters {
            background: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>

<?php include('includes/navigation.php'); ?>

<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-3">All Notifications</h2>
            
            <!-- Filters -->
            <div class="filters">
                <div class="row">
                    <div class="col-md-4">
                        <select class="form-control" id="typeFilter">
                            <option value="">All Types</option>
                            <option value="quiz">Quizzes</option>
                            <option value="activity">Activities</option>
                            <option value="assignment">Assignments</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select class="form-control" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="completed">Completed</option>
                            <option value="overdue">Overdue</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-primary btn-block" id="applyFilters">
                            <i class="fas fa-filter mr-2"></i>Apply Filters
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Notifications List -->
            <div id="notificationsList">
                <?php
                if (!empty($section_ids)) {
                    $section_ids_str = implode(',', $section_ids);
                    
                    // Fetch all activities with pagination
                    $activities_query = "
                        SELECT 
                            a.*, 
                            ss.section_id, 
                            s.subject_code,
                            s.subject_name,
                            DATEDIFF(a.due_date, NOW()) as days_left,
                            CASE 
                                WHEN sas.submission_id IS NOT NULL THEN 'completed'
                                WHEN a.due_date < NOW() THEN 'overdue'
                                ELSE 'pending'
                            END as status
                        FROM activities a
                        JOIN section_subjects ss ON a.section_subject_id = ss.id
                        JOIN subjects s ON ss.subject_id = s.id
                        LEFT JOIN student_activity_submissions sas 
                            ON sas.activity_id = a.activity_id 
                            AND sas.student_id = ?
                        WHERE ss.section_id IN ($section_ids_str)
                        AND a.status = 'active'
                        ORDER BY a.due_date DESC
                        LIMIT ? OFFSET ?";
                    
                    $stmt = $db->prepare($activities_query);
                    $stmt->bind_param("iii", $student_id, $limit, $offset);
                    $stmt->execute();
                    $activities = $stmt->get_result();

                    while ($activity = $activities->fetch_assoc()) {
                        $icon_class = '';
                        $icon_bg_class = '';
                        switch ($activity['type']) {
                            case 'quiz':
                                $icon_class = 'fas fa-question-circle';
                                $icon_bg_class = 'icon-quiz';
                                break;
                            case 'activity':
                                $icon_class = 'fas fa-tasks';
                                $icon_bg_class = 'icon-activity';
                                break;
                            case 'assignment':
                                $icon_class = 'fas fa-file-alt';
                                $icon_bg_class = 'icon-assignment';
                                break;
                        }
                        
                        $status_class = '';
                        switch ($activity['status']) {
                            case 'completed':
                                $status_class = 'badge-success';
                                break;
                            case 'overdue':
                                $status_class = 'badge-danger';
                                break;
                            case 'pending':
                                $status_class = 'badge-warning';
                                break;
                        }
                        ?>
                        <div class="notification-card">
                            <div class="notification-header">
                                <div class="d-flex align-items-center">
                                    <div class="notification-icon <?php echo $icon_bg_class; ?>">
                                        <i class="<?php echo $icon_class; ?>"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0"><?php echo htmlspecialchars($activity['title']); ?></h5>
                                        <span class="badge badge-subject mr-2">
                                            <?php echo htmlspecialchars($activity['subject_code']); ?>
                                        </span>
                                        <span class="badge <?php echo $status_class; ?>">
                                            <?php echo ucfirst($activity['status']); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="notification-meta text-right">
                                    <div>Due: <?php echo date('M d, Y', strtotime($activity['due_date'])); ?></div>
                                    <div>
                                        <?php
                                        if ($activity['status'] === 'pending') {
                                            if ($activity['days_left'] == 0) {
                                                echo '<span class="text-danger">Due today</span>';
                                            } elseif ($activity['days_left'] == 1) {
                                                echo '<span class="text-danger">Due tomorrow</span>';
                                            } else {
                                                echo "Due in {$activity['days_left']} days";
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="notification-body">
                                <p class="mb-2"><?php echo htmlspecialchars($activity['description']); ?></p>
                                <?php
                                // Determine the correct link based on activity type
                                $view_link = '';
                                switch ($activity['type']) {
                                    case 'quiz':
                                        $view_link = "student_quizzes.php";
                                        break;
                                    case 'activity':
                                        $view_link = "student_activities.php";
                                        break;
                                    case 'assignment':
                                        $view_link = "student_assignments.php";
                                        break;
                                }
                                ?>
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="<?php echo $view_link; ?>" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-list mr-1"></i>View All <?php echo ucfirst($activity['type']); ?>s
                                    </a>
                                    <a href="view_<?php echo $activity['type']; ?>.php?id=<?php echo $activity['activity_id']; ?>" 
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye mr-1"></i>View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php
                    }

                    // Pagination
                    $total_query = "SELECT COUNT(*) as total FROM activities a
                                  JOIN section_subjects ss ON a.section_subject_id = ss.id
                                  WHERE ss.section_id IN ($section_ids_str)
                                  AND a.status = 'active'";
                    $total_result = $db->query($total_query);
                    $total_row = $total_result->fetch_assoc();
                    $total_pages = ceil($total_row['total'] / $limit);

                    if ($total_pages > 1) {
                        ?>
                        <nav aria-label="Notifications pagination">
                            <ul class="pagination justify-content-center">
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <li class="page-item <?php echo ($page === $i) ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                            </ul>
                        </nav>
                        <?php
                    }
                } else {
                    ?>
                    <div class="alert alert-info">
                        No notifications available.
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
</div>

</body>
</html> 