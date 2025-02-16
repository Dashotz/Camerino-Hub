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
    (SELECT 
        a.type as event_type,
        a.activity_id as event_id,
        a.title as event_title,
        a.description as event_description,
        a.due_date as event_date,
        s.subject_name,
        CASE 
            WHEN sas.submission_id IS NOT NULL THEN 'completed'
            WHEN a.due_date < NOW() THEN 'overdue'
            ELSE 'pending'
        END as status,
        'activity' as source_type
    FROM student_sections ss
    JOIN section_subjects ssub ON ss.section_id = ssub.section_id
    JOIN subjects s ON ssub.subject_id = s.id
    JOIN activities a ON ssub.id = a.section_subject_id
    LEFT JOIN student_activity_submissions sas ON a.activity_id = sas.activity_id 
        AND sas.student_id = ?
    WHERE ss.student_id = ?
        AND ss.status = 'active'
        AND ssub.status = 'active'
        AND a.status = 'active')
    
    UNION ALL
    
    (SELECT 
        'attendance' as event_type,
        att.id as event_id,
        CONCAT(s.subject_name, ' Attendance') as event_title,
        CONCAT('Marked as ', att.status, ' at ', TIME_FORMAT(att.time_in, '%h:%i %p')) as event_description,
        att.date as event_date,
        s.subject_name,
        att.status,
        'attendance' as source_type
    FROM attendance att
    JOIN section_subjects ss ON att.section_subject_id = ss.id
    JOIN subjects s ON ss.subject_id = s.id
    WHERE att.student_id = ?
    ORDER BY event_date DESC)";

$stmt = $db->prepare($events_query);
$stmt->bind_param("iii", $student_id, $student_id, $student_id);
$stmt->execute();
$events_result = $stmt->get_result();
$calendar_events = [];

while ($event = $events_result->fetch_assoc()) {
    $eventColor = '';
    if ($event['source_type'] === 'attendance') {
        // Set colors for attendance events
        switch ($event['status']) {
            case 'present':
                $eventColor = '#28a745'; // green
                break;
            case 'late':
                $eventColor = '#ffc107'; // yellow
                break;
            case 'absent':
                $eventColor = '#dc3545'; // red
                break;
            default:
                $eventColor = '#6c757d'; // gray
        }
    } else {
        // Existing colors for activities
        $eventColor = getEventStatusColor($event['status'], $event['event_type']);
    }

    $calendar_events[] = [
        'id' => $event['event_id'],
        'title' => $event['event_title'],
        'start' => $event['event_date'],
        'description' => $event['event_description'],
        'type' => $event['event_type'],
        'course' => $event['subject_name'],
        'status' => $event['status'],
        'className' => 'event-' . $event['status'] . ' event-' . $event['event_type'],
        'backgroundColor' => $eventColor,
        'source_type' => $event['source_type']
    ];
}

// Add this helper function
function getEventStatusColor($status, $type) {
    switch ($status) {
        case 'completed':
            return '#28a745';
        case 'overdue':
            return '#dc3545';
        case 'pending':
            switch ($type) {
                case 'quiz':
                    return '#17a2b8';
                case 'assignment':
                    return '#007bff';
                case 'activity':
                    return '#6610f2';
                default:
                    return '#6c757d';
            }
        default:
            return '#6c757d';
    }
}

// Add this after your existing session checks
if (isset($_GET['action']) && $_GET['action'] === 'mark_attendance') {
    // Get today's classes
    $today_classes_query = "SELECT 
        ss.id as section_subject_id,
        sub.subject_name,
        ss.schedule_time,
        CASE 
            WHEN a.id IS NOT NULL THEN 'marked'
            ELSE 'not_marked'
        END as attendance_status
    FROM student_sections sts
    JOIN section_subjects ss ON sts.section_id = ss.section_id
    JOIN subjects sub ON ss.subject_id = sub.id
    LEFT JOIN attendance a ON ss.id = a.section_subject_id 
        AND a.student_id = ? 
        AND a.date = CURRENT_DATE
    WHERE sts.student_id = ?
        AND sts.status = 'active'
        AND ss.schedule_day = DAYNAME(NOW())
    ORDER BY ss.schedule_time ASC";

    $stmt = $db->prepare($today_classes_query);
    $stmt->bind_param("ii", $student_id, $student_id);
    $stmt->execute();
    $today_classes = $stmt->get_result();

    if ($today_classes->num_rows > 0) {
        echo '<div class="attendance-section mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Today\'s Classes Attendance</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Subject</th>
                                    <th>Schedule</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>';
        
        while ($class = $today_classes->fetch_assoc()) {
            $schedule_time = date('h:i A', strtotime($class['schedule_time']));
            echo "<tr>
                <td>{$class['subject_name']}</td>
                <td>{$schedule_time}</td>
                <td>";
            
            if ($class['attendance_status'] === 'marked') {
                echo '<span class="badge badge-success">Present</span>';
            } else {
                echo '<span class="badge badge-warning">Not Marked</span>';
            }
            
            echo "</td>
                <td>";
            
            if ($class['attendance_status'] !== 'marked') {
                echo "<button class='btn btn-sm btn-primary markAttendanceBtn' 
                    data-section-subject-id='{$class['section_subject_id']}'>
                    <i class='fas fa-check'></i> Mark Attendance
                </button>";
            } else {
                echo "<small class='text-muted'>Already marked</small>";
            }
            
            echo "</td></tr>";
        }
        
        echo '</tbody>
                    </table>
                </div>
            </div>
        </div>';
    }
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
    <link rel="icon" href="../images/light-logo.png">
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
                        <div class="d-flex align-items-center mb-2">
                            <span class="event-type-indicator" id="eventTypeIndicator"></span>
                            <h6 id="eventTitle" class="mb-0"></h6>
                        </div>
                        <p class="course-name" id="eventCourse"></p>
                        <p class="event-description" id="eventDescription"></p>
                        <div class="event-meta">
                            <div>
                                <i class="far fa-clock"></i>
                                <span class="event-date" id="eventDate"></span>
                            </div>
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
                    // Highlight events due today
                    const today = new Date();
                    const dueDate = new Date(info.event.start);
                    if (today.toDateString() === dueDate.toDateString()) {
                        info.el.style.backgroundColor = '#fff9c4';
                    }
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
                const viewDetailsBtn = modal.find('#viewEventDetails');
                
                if (event.extendedProps.source_type === 'attendance') {
                    // Attendance event display
                    modal.find('#eventTypeIndicator')
                        .text('Attendance')
                        .removeClass()
                        .addClass('event-type-indicator type-attendance');
                    
                    viewDetailsBtn.hide(); // Hide view details button for attendance events
                } else {
                    // Regular activity event display
                    const typeLabels = {
                        'assignment': 'Assignment',
                        'quiz': 'Quiz',
                        'activity': 'Activity'
                    };
                    
                    modal.find('#eventTypeIndicator')
                        .text(typeLabels[event.extendedProps.type])
                        .removeClass()
                        .addClass('event-type-indicator type-' + event.extendedProps.type);
                    
                    viewDetailsBtn.show();
                }
                
                modal.find('#eventTitle').text(event.title);
                modal.find('#eventCourse').text(event.extendedProps.course);
                modal.find('#eventDescription').text(event.extendedProps.description);
                modal.find('#eventDate').text(event.start.toLocaleDateString('en-US', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                }));
                
                modal.find('#eventStatus')
                    .text(event.extendedProps.status)
                    .removeClass()
                    .addClass('event-status status-' + event.extendedProps.status);

                // Update view details button behavior for non-attendance events
                if (event.extendedProps.source_type !== 'attendance') {
                    viewDetailsBtn.off('click').on('click', function() {
                        switch(event.extendedProps.type) {
                            case 'assignment':
                                window.location.href = 'student_assignments.php?id=' + event.id;
                                break;
                            case 'quiz':
                                window.location.href = 'student_quizzes.php?id=' + event.id;
                                break;
                            case 'activity':
                                window.location.href = 'student_activities.php?id=' + event.id;
                                break;
                        }
                    });
                }

                modal.modal('show');
            }

            // Initial date update
            updateCurrentDate();
        });
    </script>

    <script>
    $(document).ready(function() {
        $('.markAttendanceBtn').click(function() {
            const sectionSubjectId = $(this).data('section-subject-id');
            const button = $(this);
            
            $.ajax({
                url: 'handlers/mark_attendance.php',
                method: 'POST',
                data: {
                    section_subject_id: sectionSubjectId,
                    time: new Date().toLocaleTimeString('en-US', { 
                        hour12: false,
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit'
                    })
                },
                success: function(response) {
                    try {
                        const result = JSON.parse(response);
                        if (result.success) {
                            // Update button to show marked
                            button.replaceWith('<span class="badge badge-success">Marked</span>');
                            alert('Attendance marked successfully!');
                        } else {
                            alert(result.message || 'Failed to mark attendance');
                        }
                    } catch (e) {
                        console.error('Error parsing response:', e);
                        alert('Error processing response');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    alert('Error marking attendance: ' + error);
                }
            });
        });
    });
    </script>
</body>
</html>
