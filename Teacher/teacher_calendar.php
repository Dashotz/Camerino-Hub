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
    <link rel="icon" href="../images/light-logo.png">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/dashboard-shared.css">
    <style>
        .calendar-container {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .schedule-list {
            margin-top: 20px;
        }
        .schedule-item, .assignment-item {
            padding: 10px;
            margin-bottom: 10px;
            background: #f8f9fa;
            border-radius: 4px;
        }
        .schedule-item {
            border-left: 4px solid #007bff;
        }
        .assignment-item {
            border-left: 4px solid #28a745;
        }
        .fc-view-container {
            background: #fff;
            border-radius: 4px;
        }
        .fc-event {
            cursor: pointer;
            padding: 2px 4px;
        }
        .fc-time-grid-event {
            min-height: 25px;
        }
        .legend {
            margin-top: 20px;
            padding: 10px;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        .legend-item {
            display: flex;
            align-items: center;
            margin-right: 20px;
        }
        .legend-color {
            width: 15px;
            height: 15px;
            margin-right: 5px;
            border-radius: 3px;
        }
        @media (max-width: 768px) {
            .content-header {
                padding: 15px;
            }
            .content-header h1 {
                font-size: 1.5rem;
            }
            .calendar-container {
                padding: 10px;
                margin: 10px;
            }
            .fc-toolbar {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }
            .fc-toolbar .fc-left,
            .fc-toolbar .fc-right,
            .fc-toolbar .fc-center {
                float: none;
                display: flex;
                justify-content: center;
                width: 100%;
                margin: 5px 0;
            }
            .fc-toolbar h2 {
                font-size: 1.2rem;
            }
            .fc-toolbar button {
                padding: 5px 8px;
                font-size: 0.9rem;
            }
            .fc-agenda-view .fc-day-grid {
                display: none;
            }
            .fc-time-grid-event {
                margin: 1px 0;
            }
            .schedule-item, .assignment-item {
                padding: 8px;
            }
            .schedule-item h6, .assignment-item h6 {
                font-size: 0.9rem;
                margin-bottom: 5px;
            }
            .text-muted {
                font-size: 0.8rem;
            }
            .legend {
                padding: 5px;
                justify-content: center;
            }
            .legend-item {
                font-size: 0.8rem;
                margin: 5px 10px;
            }
        }
        @media (max-width: 480px) {
            .fc-toolbar.fc-header-toolbar {
                margin-bottom: .5em;
                display: flex;
                flex-direction: column;
                align-items: center;
            }
            .fc-toolbar-chunk {
                display: flex;
                justify-content: center;
                margin-bottom: 5px;
            }
            .fc button {
                padding: 0.2em 0.4em;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/navigation.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="content-header">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                    <div>
                        <h1>Calendar</h1>
                        <p>View your schedule and upcoming events</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-8 mb-4">
                    <div class="calendar-container">
                        <div id="calendar"></div>
                        <div class="legend">
                            <div class="legend-item">
                                <span class="legend-color" style="background: #007bff;"></span>
                                <span>Classes</span>
                            </div>
                            <div class="legend-item">
                                <span class="legend-color" style="background: #28a745;"></span>
                                <span>Assignments Due</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card mb-4">
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

                    <div class="card mb-4">
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
            defaultView: window.innerWidth < 768 ? 'agendaDay' : 'agendaWeek',
            navLinks: true,
            editable: false,
            eventLimit: true,
            events: [...classEvents, ...assignmentEvents],
            eventClick: function(event) {
                // Show event details in a modal or alert
                alert(event.title + '\n' + 
                      (event.daysOfWeek ? 'Weekly Schedule' : 'Due Date: ' + moment(event.start).format('MMMM D, YYYY')));
            },
            windowResize: function(view) {
                if (window.innerWidth < 768) {
                    $('#calendar').fullCalendar('changeView', 'agendaDay');
                } else {
                    $('#calendar').fullCalendar('changeView', 'agendaWeek');
                }
            }
        });
    });
    </script>
</body>
</html> 