<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['teacher_id'])) {
    header("Location: Teacher-Login.php");
    exit();
}

$db = new DbConnector();
$teacher_id = $_SESSION['teacher_id'];

// Get teacher's classes and schedule
$schedule_query = "SELECT 
    ss.id as class_id,
    CONCAT(sec.section_name, ' - ', sub.subject_code) as section_name,
    sub.subject_code,
    sub.subject_name,
    ss.schedule_day,
    ss.schedule_time
FROM section_subjects ss
JOIN sections sec ON ss.section_id = sec.section_id
JOIN subjects sub ON ss.subject_id = sub.id
WHERE ss.teacher_id = ?
AND ss.status = 'active'
ORDER BY ss.schedule_day, ss.schedule_time";

$stmt = $db->prepare($schedule_query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$schedules = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get assignments due dates
$assignments_query = "SELECT 
    a.activity_id,
    a.title,
    a.due_date,
    sub.subject_code,
    sec.section_name
FROM activities a
JOIN section_subjects ss ON a.section_subject_id = ss.id
JOIN sections sec ON ss.section_id = sec.section_id
JOIN subjects sub ON ss.subject_id = sub.id
WHERE ss.teacher_id = ?
AND a.status = 'active'
AND a.due_date >= CURDATE()
ORDER BY a.due_date ASC";

$stmt = $db->prepare($assignments_query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$assignments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar - CamerinoHub</title>
    
    <!-- External CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css' rel='stylesheet'>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/dashboard-shared.css">
    <style>
        .calendar-container {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .schedule-list {
            margin-top: 20px;
        }
        .schedule-item {
            padding: 10px;
            border-left: 4px solid #007bff;
            margin-bottom: 10px;
            background: #f8f9fa;
        }
        .assignment-item {
            padding: 10px;
            border-left: 4px solid #28a745;
            margin-bottom: 10px;
            background: #f8f9fa;
        }
        .fc-event {
            cursor: pointer;
        }
        .legend {
            margin-top: 20px;
            padding: 10px;
        }
        .legend-item {
            display: inline-block;
            margin-right: 20px;
        }
        .legend-color {
            display: inline-block;
            width: 15px;
            height: 15px;
            margin-right: 5px;
            vertical-align: middle;
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
                        <h1>Calendar</h1>
                        <p>View your schedule and upcoming events</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="calendar-container">
                        <div id="calendar"></div>
                        <div class="legend">
                            <div class="legend-item">
                                <span class="legend-color" style="background: #007bff;"></span>
                                Classes
                            </div>
                            <div class="legend-item">
                                <span class="legend-color" style="background: #28a745;"></span>
                                Assignments Due
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Weekly Schedule</h5>
                        </div>
                        <div class="card-body">
                            <div class="schedule-list">
                                <?php foreach ($schedules as $schedule): ?>
                                    <div class="schedule-item">
                                        <h6><?php echo htmlspecialchars($schedule['subject_code'] . ' - ' . $schedule['section_name']); ?></h6>
                                        <div class="text-muted">
                                            <i class="fas fa-calendar-day"></i> 
                                            <?php echo htmlspecialchars($schedule['schedule_day']); ?>
                                        </div>
                                        <div class="text-muted">
                                            <i class="fas fa-clock"></i> 
                                            <?php echo htmlspecialchars($schedule['schedule_time']); ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">Upcoming Assignments</h5>
                        </div>
                        <div class="card-body">
                            <div class="schedule-list">
                                <?php foreach ($assignments as $assignment): ?>
                                    <div class="assignment-item">
                                        <h6><?php echo htmlspecialchars($assignment['title']); ?></h6>
                                        <div class="text-muted">
                                            <i class="fas fa-book"></i> 
                                            <?php echo htmlspecialchars($assignment['subject_code'] . ' - ' . $assignment['section_name']); ?>
                                        </div>
                                        <div class="text-muted">
                                            <i class="fas fa-calendar-alt"></i> 
                                            Due: <?php echo date('M d, Y', strtotime($assignment['due_date'])); ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js'></script>
    
    <script>
    $(document).ready(function() {
        // Prepare events data
        var classEvents = <?php echo json_encode(array_map(function($schedule) {
            return [
                'title' => $schedule['subject_code'] . ' - ' . $schedule['section_name'],
                'daysOfWeek' => [array_search(strtolower($schedule['schedule_day']), ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'])],
                'startTime' => $schedule['schedule_time'],
                'color' => '#007bff',
                'textColor' => '#ffffff'
            ];
        }, $schedules)); ?>;

        var assignmentEvents = <?php echo json_encode(array_map(function($assignment) {
            return [
                'title' => $assignment['title'],
                'start' => $assignment['due_date'],
                'color' => '#28a745',
                'textColor' => '#ffffff'
            ];
        }, $assignments)); ?>;

        // Initialize calendar
        $('#calendar').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            defaultView: 'agendaWeek',
            navLinks: true,
            editable: false,
            eventLimit: true,
            events: [...classEvents, ...assignmentEvents],
            eventClick: function(event) {
                // Show event details in a modal or alert
                alert(event.title + '\n' + 
                      (event.daysOfWeek ? 'Weekly Schedule' : 'Due Date: ' + moment(event.start).format('MMMM D, YYYY')));
            }
        });
    });
    </script>
</body>
</html> 