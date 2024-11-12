<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: Student-Login.php");
    exit();
}

// Get user data
require_once('../db/dbConnector.php');
$db = new DbConnector();

$student_id = $_SESSION['id'];

// Fetch student data
$query = "SELECT * FROM student WHERE student_id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();

// Fetch events (assignments, activities, etc.)
$events_query = "
    SELECT 
        'assignment' as event_type,
        a.activity_id as event_id,
        a.title as event_title,
        a.description as event_description,
        a.due_date as event_date,
        s.subject_name,
        CASE 
            WHEN sas.submission_id IS NOT NULL THEN 'completed'
            WHEN a.due_date < NOW() THEN 'overdue'
            ELSE 'pending'
        END as status
    FROM student_sections ss
    JOIN section_subjects ssub ON ss.section_id = ssub.section_id
    JOIN subjects s ON ssub.subject_id = s.id
    JOIN activities a ON ssub.teacher_id = a.teacher_id
    LEFT JOIN student_activity_submissions sas ON a.activity_id = sas.activity_id 
        AND sas.student_id = ?
    WHERE ss.student_id = ?
        AND ss.status = 'active'
        AND ssub.status = 'active'
        AND a.status = 'active'
        AND a.type = 'assignment'
    
    UNION
    
    SELECT 
        'quiz' as event_type,
        a.activity_id as event_id,
        a.title as event_title,
        a.description as event_description,
        a.due_date as event_date,
        s.subject_name,
        CASE 
            WHEN sas.submission_id IS NOT NULL THEN 'completed'
            WHEN a.due_date < NOW() THEN 'overdue'
            ELSE 'pending'
        END as status
    FROM student_sections ss
    JOIN section_subjects ssub ON ss.section_id = ssub.section_id
    JOIN subjects s ON ssub.subject_id = s.id
    JOIN activities a ON ssub.teacher_id = a.teacher_id
    LEFT JOIN student_activity_submissions sas ON a.activity_id = sas.activity_id 
        AND sas.student_id = ?
    WHERE ss.student_id = ?
        AND ss.status = 'active'
        AND ssub.status = 'active'
        AND a.status = 'active'
        AND a.type = 'quiz'
    ORDER BY event_date";

$stmt = $db->prepare($events_query);
$stmt->bind_param("iiii", $student_id, $student_id, $student_id, $student_id);
$stmt->execute();
$events_result = $stmt->get_result();
$calendar_events = [];

while ($event = $events_result->fetch_assoc()) {
    $calendar_events[] = [
        'id' => $event['event_id'],
        'title' => $event['event_title'],
        'start' => $event['event_date'],
        'description' => $event['event_description'],
        'type' => $event['event_type'],
        'course' => $event['subject_name'],
        'status' => $event['status'],
        'className' => 'event-' . $event['status'] . ' event-' . $event['event_type']
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar - Gov D.M. Camerino</title>
    
    <!-- External CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet'>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/dashboard-shared.css">
    <link rel="stylesheet" href="css/calendar.css">
</head>
<body>
    <?php include 'includes/navigation.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="content-header">
                <h1>Academic Calendar</h1>
                <p>View and manage your academic schedule</p>
            </div>

            <!-- Calendar Tools -->
            <div class="calendar-tools mb-4">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <div class="btn-group">
                            <button id="prevMonth" class="btn btn-outline-primary">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button id="today" class="btn btn-outline-primary">Today</button>
                            <button id="nextMonth" class="btn btn-outline-primary">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <h4 id="currentDate" class="mb-0"></h4>
                    </div>
                    <div class="col-md-4 text-right">
                        <div class="btn-group">
                            <button class="btn btn-outline-primary" data-calendar-view="dayGridMonth">Month</button>
                            <button class="btn btn-outline-primary" data-calendar-view="timeGridWeek">Week</button>
                            <button class="btn btn-outline-primary" data-calendar-view="timeGridDay">Day</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Calendar Container -->
            <div class="calendar-container">
                <div id="calendar"></div>
            </div>
        </div>
    </div>

    <!-- Event Details Modal -->
    <div class="modal fade" id="eventModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Event Details</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="event-info">
                        <h6 id="eventTitle"></h6>
                        <p class="course-name" id="eventCourse"></p>
                        <p class="event-description" id="eventDescription"></p>
                        <div class="event-meta">
                            <span class="event-date" id="eventDate"></span>
                            <span class="event-status" id="eventStatus"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="viewEventDetails">View Details</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: false,
                events: <?php echo json_encode($calendar_events); ?>,
                eventClick: function(info) {
                    showEventDetails(info.event);
                },
                eventDidMount: function(info) {
                    // Add tooltips
                    $(info.el).tooltip({
                        title: info.event.title,
                        placement: 'top',
                        trigger: 'hover',
                        container: 'body'
                    });
                }
            });
            calendar.render();

            // Calendar Navigation
            document.getElementById('prevMonth').addEventListener('click', function() {
                calendar.prev();
                updateCurrentDate();
            });

            document.getElementById('nextMonth').addEventListener('click', function() {
                calendar.next();
                updateCurrentDate();
            });

            document.getElementById('today').addEventListener('click', function() {
                calendar.today();
                updateCurrentDate();
            });

            // View Buttons
            document.querySelectorAll('[data-calendar-view]').forEach(button => {
                button.addEventListener('click', function() {
                    const view = this.dataset.calendarView;
                    calendar.changeView(view);
                    updateCurrentDate();
                });
            });

            function updateCurrentDate() {
                const date = calendar.getDate();
                document.getElementById('currentDate').textContent = 
                    date.toLocaleDateString('en-US', { 
                        month: 'long', 
                        year: 'numeric' 
                    });
            }

            function showEventDetails(event) {
                const modal = $('#eventModal');
                modal.find('#eventTitle').text(event.title);
                modal.find('#eventCourse').text(event.extendedProps.course);
                modal.find('#eventDescription').text(event.extendedProps.description);
                modal.find('#eventDate').text(event.start.toLocaleDateString('en-US', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                }));
                modal.find('#eventStatus').text(event.extendedProps.status)
                    .removeClass()
                    .addClass('event-status status-' + event.extendedProps.status);

                // Set up view details button
                modal.find('#viewEventDetails').off('click').on('click', function() {
                    if (event.extendedProps.type === 'assignment') {
                        window.location.href = 'student_assignments.php?id=' + event.id;
                    }
                });

                modal.modal('show');
            }

            // Initial date update
            updateCurrentDate();
        });
    </script>
</body>
</html>
