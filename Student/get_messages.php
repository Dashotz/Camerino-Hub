<?php
session_start();

if (!isset($_SESSION['id']) || !isset($_GET['conversation_id'])) {
    echo "Unauthorized or missing conversation ID";
    exit();
}

require_once('../db/dbConnector.php');

try {
    $mainDb = new DbConnector(false);
    $msgDb = new DbConnector(true);
    
    $student_id = $_SESSION['id'];
    $conversation_id = (int)$_GET['conversation_id'];

    // Verify conversation
    $verify_query = "
        SELECT c.*, t.firstname, t.lastname, t.teacher_id,
               '../images/teacher.png' as teacher_picture
        FROM conversations c
        JOIN elearning.teacher t ON c.teacher_id = t.teacher_id
        WHERE c.conversation_id = ? 
        AND c.student_id = ?
        AND c.status = 'active'";

    $stmt = $msgDb->prepare($verify_query, true);
    if (!$stmt) {
        throw new Exception("Failed to prepare conversation verification");
    }
    
    $stmt->bind_param("ii", $conversation_id, $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Conversation not found");
    }
    
    $conversation = $result->fetch_assoc();
    $stmt->close();
    $result->close();

    // Get messages
    $messages_query = "
        SELECT 
            m.*,
            CASE 
                WHEN m.sender_type = 'student' THEN s.firstname
                ELSE t.firstname
            END as sender_name,
            CASE 
                WHEN m.sender_type = 'student' THEN CONCAT('../images/student', s.student_id, '.png')
                ELSE '../images/teacher.png'
            END as sender_picture
        FROM messages m
        LEFT JOIN elearning.teacher t ON m.sender_id = t.teacher_id AND m.sender_type = 'teacher'
        LEFT JOIN elearning.student s ON m.sender_id = s.student_id AND m.sender_type = 'student'
        WHERE m.conversation_id = ?
        ORDER BY m.sent_at ASC";

    $stmt = $msgDb->prepare($messages_query, true);
    if (!$stmt) {
        throw new Exception("Failed to prepare messages query");
    }
    
    $stmt->bind_param("i", $conversation_id);
    $stmt->execute();
    $messages = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    ?>

    <div class="messages-header">
        <div class="recipient-info">
            <img src="<?php echo htmlspecialchars($conversation['teacher_picture']); ?>" 
                 onerror="this.src='../images/default-avatar.png'" 
                 alt="Teacher" 
                 class="avatar-img">
            <div class="recipient-details">
                <h6><?php echo htmlspecialchars($conversation['firstname'] . ' ' . $conversation['lastname']); ?></h6>
                <span class="status">Active now</span>
            </div>
        </div>
    </div>

    <div class="messages-list" id="messagesList">
        <?php foreach ($messages as $message): ?>
            <div class="message <?php echo $message['sender_type'] === 'student' ? 'sent' : 'received'; ?>">
                <img src="<?php echo htmlspecialchars($message['sender_picture']); ?>" 
                     onerror="this.src='../images/default-avatar.png'" 
                     alt="<?php echo htmlspecialchars($message['sender_name']); ?>" 
                     class="message-avatar">
                <div class="message-content">
                    <div class="message-bubble">
                        <p><?php echo nl2br(htmlspecialchars($message['message'])); ?></p>
                    </div>
                    <div class="message-info">
                        <span class="message-time">
                            <?php echo date('g:i A', strtotime($message['sent_at'])); ?>
                        </span>
                        <?php if ($message['read_status'] && $message['sender_type'] === 'student'): ?>
                            <span class="message-status">
                                <i class="fas fa-check-double"></i>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="message-input">
        <form id="replyForm" class="reply-form">
            <input type="hidden" name="conversation_id" value="<?php echo $conversation_id; ?>">
            <input type="hidden" name="teacher_id" value="<?php echo $conversation['teacher_id']; ?>">
            <div class="input-group">
                <button type="button" class="btn btn-link attachment-btn" title="Add attachment">
                    <i class="fas fa-paperclip"></i>
                </button>
                <textarea class="form-control" name="message" placeholder="Type a message..." rows="1"></textarea>
                <button type="submit" class="btn btn-primary send-btn" title="Send message">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </form>
    </div>

    <style>
    .messages-list {
        height: calc(100vh - 220px);
        overflow-y: auto;
        padding: 1rem;
        background: #f0f2f5;
    }

    .messages-header {
        background: #1a237e;
        color: white;
        padding: 1rem;
        position: sticky;
        top: 0;
        z-index: 1000;
    }

    .recipient-info {
        display: flex;
        align-items: center;
    }

    .avatar-img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: 2px solid #fff;
        margin-right: 10px;
    }

    .message {
        display: flex;
        align-items: flex-end;
        margin-bottom: 1rem;
        opacity: 0;
        transform: translateY(20px);
        animation: messageAppear 0.3s forwards;
    }

    @keyframes messageAppear {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .message.sent {
        flex-direction: row-reverse;
    }

    .message-avatar {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        margin: 0 8px;
    }

    .message-content {
        max-width: 60%;
    }

    .message-bubble {
        padding: 0.8rem 1rem;
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        position: relative;
    }

    .message.sent .message-bubble {
        background: #1a237e;
        color: #fff;
    }

    .message.received .message-bubble {
        background: #fff;
        color: #000;
    }

    .message-info {
        font-size: 0.75rem;
        color: #6c757d;
        margin-top: 0.25rem;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .message.sent .message-info {
        justify-content: flex-end;
    }

    .reply-form {
        padding: 1rem;
        background: #fff;
        border-top: 1px solid #dee2e6;
        position: sticky;
        bottom: 0;
    }

    .reply-form .input-group {
        background: #f0f2f5;
        border-radius: 24px;
        padding: 0.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .reply-form textarea {
        border: none;
        background: transparent;
        resize: none;
        max-height: 100px;
        padding: 8px 12px;
        font-size: 0.95rem;
    }

    .reply-form textarea:focus {
        box-shadow: none;
        outline: none;
    }

    .attachment-btn, .send-btn {
        background: none;
        border: none;
        padding: 0.5rem;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .attachment-btn {
        color: #1a237e;
    }

    .send-btn {
        color: #fff;
        background: #1a237e;
        border-radius: 50%;
        margin-left: 8px;
    }

    .send-btn:hover {
        background: #283593;
    }

    .message-loading {
        display: none;
        padding: 1rem;
        text-align: center;
        color: #6c757d;
    }

    .typing-indicator {
        display: none;
        padding: 0.5rem;
        color: #6c757d;
        font-size: 0.8rem;
    }
    </style>

    <script>
    $(document).ready(function() {
        const messagesList = document.getElementById('messagesList');
        
        // Auto-resize textarea
        $('textarea').on('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });

        // Scroll to bottom of messages
        function scrollToBottom(smooth = true) {
            messagesList.scrollTop = messagesList.scrollHeight;
        }
        scrollToBottom(false);

        // Handle form submission
        $('#replyForm').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const textarea = form.find('textarea[name="message"]');
            const message = textarea.val().trim();
            const sendBtn = form.find('.send-btn');

            if (!message) return;

            // Debug log
            console.log('Sending form data:', {
                conversation_id: form.find('[name="conversation_id"]').val(),
                teacher_id: form.find('[name="teacher_id"]').val(),
                message: message
            });

            // Disable send button and show loading state
            sendBtn.prop('disabled', true);
            
            const formData = new FormData();
            formData.append('conversation_id', form.find('[name="conversation_id"]').val());
            formData.append('teacher_id', form.find('[name="teacher_id"]').val());
            formData.append('message', message);

            // Create temporary message
            const tempMessage = $(`
                <div class="message sent" style="opacity: 0.5">
                    <img src="../images/student${form.find('[name="sender_id"]').val()}.png" 
                         onerror="this.src='../images/default-avatar.png'" 
                         class="message-avatar">
                    <div class="message-content">
                        <div class="message-bubble">
                            <p>${message}</p>
                        </div>
                        <div class="message-info">
                            <span class="message-time">Sending...</span>
                        </div>
                    </div>
                </div>
            `);
            
            // Add temporary message and scroll
            $('#messagesList').append(tempMessage);
            scrollToBottom();

            $.ajax({
                url: 'send_message.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    console.log('Response:', response); // Debug log
                    if (response.success) {
                        textarea.val('').trigger('input');
                        loadMessages(form.find('[name="conversation_id"]').val());
                    } else {
                        tempMessage.remove();
                        alert(response.error || 'Error sending message');
                    }
                },
                error: function(xhr, status, error) {
                    tempMessage.remove();
                    console.error('Full error details:', {
                        error: error,
                        status: status,
                        response: xhr.responseText,
                        readyState: xhr.readyState,
                        statusText: xhr.statusText
                    });
                    alert('Error sending message. Please try again.');
                },
                complete: function() {
                    sendBtn.prop('disabled', false);
                }
            });
        });

        // Update loadMessages function to maintain scroll position
        window.loadMessages = function(conversationId) {
            const wasAtBottom = messagesList.scrollHeight - messagesList.scrollTop === messagesList.clientHeight;
            
            $.ajax({
                url: 'get_messages.php',
                type: 'GET',
                data: { conversation_id: conversationId },
                success: function(response) {
                    $('#messageContent').html(response);
                    if (wasAtBottom) {
                        scrollToBottom();
                    }
                },
                error: function() {
                    alert('Error loading messages');
                }
            });
        };

        // Handle enter key
        $('textarea').keypress(function(e) {
            if (e.which === 13 && !e.shiftKey) {
                e.preventDefault();
                $('#replyForm').submit();
            }
        });
    });
    </script>
    <?php

} catch (Exception $e) {
    echo "Error: " . htmlspecialchars($e->getMessage());
} finally {
    if (isset($mainDb)) $mainDb->close();
    if (isset($msgDb)) $msgDb->close();
}
?>
