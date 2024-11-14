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

// Handle notification marking as read
if (isset($_GET['notification_id'])) {
    $notification_id = (int)$_GET['notification_id'];
    
    $update_query = "UPDATE notifications 
                    SET is_read = 1 
                    WHERE id = ? 
                    AND user_id = ? 
                    AND user_type = 'student'";
    
    $stmt = $db->prepare($update_query);
    $stmt->bind_param("ii", $notification_id, $student_id);
    $stmt->execute();
} 
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

                <div class="announcements-list" id="announcementsList">
                    <!-- Announcements will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="js/announcements.js"></script>

    <style>
    .announcement-card.highlight {
        animation: highlight 3s;
    }

    @keyframes highlight {
        0% { background-color: #fff3cd; }
        70% { background-color: #fff3cd; }
        100% { background-color: white; }
    }
    </style>
</body>
</html>
