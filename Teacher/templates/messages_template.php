<div class="messages-header">
    <div class="recipient-info">
        <img src="<?php echo $student_picture; ?>" alt="Student" class="avatar-img">
        <div>
            <h6><?php echo htmlspecialchars($student_name); ?></h6>
            <p class="subject"><?php echo htmlspecialchars($subject); ?></p>
        </div>
    </div>
</div>

<div class="messages-list">
    <?php foreach ($messages as $message): ?>
        <div class="message <?php echo $message['message_type']; ?>">
            <div class="message-content">
                <p><?php echo htmlspecialchars($message['message']); ?></p>
                <span class="time"><?php echo $message['formatted_time']; ?></span>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="message-input">
    <form id="replyForm">
        <input type="hidden" name="conversation_id" value="<?php echo $conversation_id; ?>">
        <textarea class="form-control" name="message" placeholder="Type your message..." required></textarea>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-paper-plane"></i>
        </button>
    </form>
</div> 