<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: Student-Login.php");
    exit();
}

// Get user data
require_once('../db/dbConnector.php');
$mainDb = new DbConnector(false); // Main database connection
$msgDb = new DbConnector(true);   // Message database connection

$student_id = $_SESSION['id'];

// Fetch student data
$query = "SELECT * FROM student WHERE student_id = ?";
$stmt = $mainDb->prepare($query, false);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();

// Fetch conversations with latest message
$conversations_query = "
    SELECT DISTINCT 
        c.conversation_id,
        c.last_message_time,
        c.subject,
        t.teacher_id,
        t.firstname as teacher_firstname,
        t.lastname as teacher_lastname,
        '../images/teacher.png' as teacher_picture,
        (SELECT COUNT(*) FROM messages m 
         WHERE m.conversation_id = c.conversation_id 
         AND m.receiver_id = ? 
         AND m.read_status = 0) as unread_count,
        (SELECT message FROM messages 
         WHERE conversation_id = c.conversation_id 
         ORDER BY sent_at DESC LIMIT 1) as latest_message
    FROM conversations c
    JOIN elearning.teacher t ON c.teacher_id = t.teacher_id
    WHERE c.student_id = ?
    AND c.status = 'active'
    ORDER BY c.last_message_time DESC";

$stmt = $msgDb->prepare($conversations_query, true);
$stmt->bind_param("ii", $student_id, $student_id);
$stmt->execute();
$conversations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch teachers for the select dropdown
$teachers_query = "
    SELECT DISTINCT t.teacher_id, t.firstname, t.lastname
    FROM teacher t
    JOIN courses c ON t.teacher_id = c.teacher_id
    JOIN student_courses sc ON c.course_id = sc.course_id
    WHERE sc.student_id = ?
    ORDER BY t.lastname, t.firstname";

$stmt = $mainDb->prepare($teachers_query, false);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$teachers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

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
                                        <img src="<?php echo htmlspecialchars($conv['teacher_picture']); ?>" 
                                             onerror="this.src='../images/default-avatar.png'" 
                                             alt="Teacher" class="avatar-img">
                                        <span class="status-indicator online"></span>
                                    </div>
                                    <div class="conversation-content">
                                        <div class="conversation-header">
                                            <h6><?php echo htmlspecialchars($conv['teacher_firstname'] . ' ' . $conv['teacher_lastname']); ?></h6>
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
                            <select class="form-control" name="teacher_id" required>
                                <option value="">Select Teacher</option>
                                <?php foreach ($teachers as $teacher): ?>
                                    <option value="<?php echo $teacher['teacher_id']; ?>">
                                        <?php echo htmlspecialchars($teacher['firstname'] . ' ' . $teacher['lastname']); ?>
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
                formData.append('sender_id', '<?php echo $student_id; ?>');
                formData.append('sender_type', 'student');

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