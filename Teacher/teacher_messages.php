<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['teacher_id'])) {
    header("Location: Teacher-Login.php");
    exit();
}

// Get user data
require_once('../db/dbConnector.php');
$mainDb = new DbConnector(false); // Main database connection
$msgDb = new DbConnector(true);   // Message database connection

$teacher_id = $_SESSION['teacher_id'];

// Fetch teacher data from main database
$query = "SELECT * FROM teacher WHERE teacher_id = ?";
$stmt = $mainDb->prepare($query, false);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();

// Fetch conversations with latest message from message database
$conversations_query = "
    SELECT DISTINCT 
        c.conversation_id,
        c.last_message_time,
        c.subject,
        s.student_id,
        s.firstname as student_firstname,
        s.lastname as student_lastname,
        CONCAT('../images/student', s.student_id, '.png') as student_picture,
        (SELECT COUNT(*) FROM messages m 
         WHERE m.conversation_id = c.conversation_id 
         AND m.receiver_id = ? 
         AND m.read_status = 0) as unread_count,
        (SELECT message FROM messages 
         WHERE conversation_id = c.conversation_id 
         ORDER BY sent_at DESC LIMIT 1) as latest_message
    FROM conversations c
    JOIN elearning.student s ON c.student_id = s.student_id
    WHERE c.teacher_id = ?
    AND c.status = 'active'
    ORDER BY c.last_message_time DESC";

$stmt = $msgDb->prepare($conversations_query, true);
$stmt->bind_param("ii", $teacher_id, $teacher_id);
$stmt->execute();
$conversations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch students for the select dropdown from main database
$students_query = "
    SELECT DISTINCT s.student_id, s.firstname, s.lastname
    FROM student s
    JOIN student_courses sc ON s.student_id = sc.student_id
    JOIN courses c ON sc.course_id = c.course_id
    WHERE c.teacher_id = ?
    ORDER BY s.lastname, s.firstname";

$stmt = $mainDb->prepare($students_query, false);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$students = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Helper function for time formatting
function formatMessageTime($timestamp) {
    $messageTime = strtotime($timestamp);
    $now = time();
    $diff = $now - $messageTime;
    
    if ($diff < 60) {
        return "Just now";
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . "m ago";
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . "h ago";
    } else {
        return date("M j", $messageTime);
    }
}

// Close database connections
$mainDb->close();
$msgDb->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - Teacher Dashboard</title>
    
    <!-- External CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/dashboard-shared.css">
    <link rel="stylesheet" href="css/messages.css">
</head>
<body>
    <?php include 'includes/navigation.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="messages-container">
                <!-- Conversations List -->
                <div class="conversations-list">
                    <div class="conversations-header">
                        <h5>Messages</h5>
                        <button class="btn btn-primary btn-sm" id="newMessageBtn">
                            <i class="fas fa-plus"></i> New Message
                        </button>
                    </div>
                    
                    <div class="search-box">
                        <input type="text" placeholder="Search messages...">
                        <i class="fas fa-search"></i>
                    </div>

                    <div class="conversations">
                        <?php if (empty($conversations)): ?>
                            <div class="no-conversations">
                                <i class="fas fa-inbox"></i>
                                <p>No messages yet</p>
                                <button class="btn btn-primary btn-sm" id="startConversation">
                                    Start a Conversation
                                </button>
                            </div>
                        <?php else: ?>
                            <?php foreach ($conversations as $conv): ?>
                                <div class="conversation-item" data-conversation-id="<?php echo $conv['conversation_id']; ?>">
                                    <div class="conversation-avatar">
                                        <img src="<?php echo htmlspecialchars($conv['student_picture']); ?>" 
                                             onerror="this.src='../images/default-avatar.png'" 
                                             alt="Student" class="avatar-img">
                                        <span class="status-indicator online"></span>
                                    </div>
                                    <div class="conversation-content">
                                        <div class="conversation-header">
                                            <h6><?php echo htmlspecialchars($conv['student_firstname'] . ' ' . $conv['student_lastname']); ?></h6>
                                            <span class="time">
                                                <?php echo formatMessageTime($conv['last_message_time']); ?>
                                            </span>
                                        </div>
                                        <div class="message-preview">
                                            <p class="subject"><?php echo htmlspecialchars($conv['subject']); ?></p>
                                            <p class="latest-message">
                                                <?php 
                                                $preview = $conv['latest_message'] ?? 'No messages yet';
                                                echo htmlspecialchars(substr($preview, 0, 50)) . (strlen($preview) > 50 ? '...' : '');
                                                ?>
                                            </p>
                                        </div>
                                    </div>
                                    <?php if ($conv['unread_count'] > 0): ?>
                                        <span class="unread-badge"><?php echo $conv['unread_count']; ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Message Content -->
                <div class="message-content" id="messageContent">
                    <div class="empty-state">
                        <i class="fas fa-comments"></i>
                        <h4>Select a conversation</h4>
                        <p>Choose a conversation from the list to view messages</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- New Message Modal -->
    <div class="modal fade" id="newMessageModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">New Message</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="newMessageForm">
                        <div class="form-group">
                            <label>To:</label>
                            <select class="form-control" name="student_id" required>
                                <option value="">Select Student</option>
                                <?php foreach ($students as $student): ?>
                                    <option value="<?php echo $student['student_id']; ?>">
                                        <?php echo htmlspecialchars($student['firstname'] . ' ' . $student['lastname']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Message:</label>
                            <textarea class="form-control" name="message" rows="5" required></textarea>
                        </div>
                        <input type="hidden" name="subject" value="New Conversation">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="sendMessage">Send Message</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // New Message Button
            $('#newMessageBtn, #startConversation').click(function() {
                $('#newMessageModal').modal('show');
            });

            // Load Conversation
            $('.conversation-item').click(function() {
                const conversationId = $(this).data('conversation-id');
                loadMessages(conversationId);
                
                // Add active class
                $('.conversation-item').removeClass('active');
                $(this).addClass('active');
                
                // Mark messages as read
                markMessagesAsRead(conversationId);
            });

            // Send Message
            $('#sendMessage').click(function() {
                const form = $('#newMessageForm');
                
                if (!form[0].checkValidity()) {
                    form[0].reportValidity();
                    return;
                }

                $('#sendMessage').prop('disabled', true);
                const formData = new FormData(form[0]);

                $.ajax({
                    url: 'send_message.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response && response.success) {
                            $('#newMessageModal').modal('hide');
                            form[0].reset();
                            location.reload();
                        } else {
                            alert(response.error || 'Error sending message');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                        console.error('Response:', xhr.responseText);
                        alert('Error sending message. Please try again.');
                    },
                    complete: function() {
                        $('#sendMessage').prop('disabled', false);
                    }
                });
            });

            function loadMessages(conversationId) {
                $.ajax({
                    url: 'get_messages.php',
                    type: 'GET',
                    data: { conversation_id: conversationId },
                    success: function(response) {
                        $('#messageContent').html(response);
                        scrollToBottom();
                    },
                    error: function() {
                        alert('Error loading messages');
                    }
                });
            }

            function markMessagesAsRead(conversationId) {
                $.ajax({
                    url: 'mark_messages_read.php',
                    type: 'POST',
                    data: { conversation_id: conversationId },
                    dataType: 'json'
                });
            }

            function scrollToBottom() {
                const messagesList = document.querySelector('.messages-list');
                if (messagesList) {
                    messagesList.scrollTop = messagesList.scrollHeight;
                }
            }

            function checkNewMessages() {
                const activeConversation = $('.conversation-item.active').data('conversation-id');
                
                $.ajax({
                    url: 'check_new_messages.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.hasNewMessages) {
                            // Reload conversations list
                            location.reload();
                            
                            // If in a conversation, reload it
                            if (activeConversation) {
                                loadMessages(activeConversation);
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error checking messages:', error);
                    }
                });
            }

            // Check for new messages every 30 seconds
            setInterval(checkNewMessages, 30000);

            // Reset form when modal is closed
            $('#newMessageModal').on('hidden.bs.modal', function() {
                $('#newMessageForm')[0].reset();
            });

            // Handle form submission on enter key in textarea
            $('#newMessageForm textarea').keypress(function(e) {
                if (e.which === 13 && !e.shiftKey) {
                    e.preventDefault();
                    $('#sendMessage').click();
                }
            });
        });
    </script>
</body>
</html> 