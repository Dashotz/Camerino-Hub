<?php
session_start();
require_once('../db/dbConnector.php');

// Ensure no output before headers
ob_clean();
header('Content-Type: application/json');

// Function to send JSON response and exit
function sendJsonResponse($success, $error = null) {
    echo json_encode([
        'success' => $success,
        'error' => $error
    ]);
    exit();
}

// Check authentication
if (!isset($_SESSION['id'])) {
    sendJsonResponse(false, 'Unauthorized');
}

// Validate input
if (!isset($_POST['teacher_id']) || !isset($_POST['message']) || trim($_POST['message']) === '') {
    error_log('Missing fields: ' . json_encode($_POST)); // Debug log
    sendJsonResponse(false, 'Missing required fields: ' . 
        (!isset($_POST['teacher_id']) ? 'teacher_id ' : '') . 
        (!isset($_POST['message']) ? 'message ' : '') . 
        (isset($_POST['message']) && trim($_POST['message']) === '' ? '(empty message)' : '')
    );
}

try {
    $msgDb = new DbConnector(true);   // Message database
    $mainDb = new DbConnector(false); // Main database

    $student_id = $_SESSION['id'];
    $teacher_id = (int)$_POST['teacher_id'];
    $message = trim($_POST['message']);

    // Verify student
    $verify_query = "SELECT student_id FROM student WHERE student_id = ?";
    $stmt = $mainDb->prepare($verify_query, false);
    if (!$stmt) {
        throw new Exception("Student verification prepare failed");
    }
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        throw new Exception("Student not found");
    }
    $stmt->close();
    $result->close();
    
    // Verify teacher
    $verify_query = "
        SELECT t.teacher_id 
        FROM teacher t
        JOIN courses c ON t.teacher_id = c.teacher_id
        JOIN student_courses sc ON c.course_id = sc.course_id
        WHERE t.teacher_id = ? AND sc.student_id = ?
        LIMIT 1";
    $stmt = $mainDb->prepare($verify_query, false);
    if (!$stmt) {
        throw new Exception("Teacher verification prepare failed");
    }
    $stmt->bind_param("ii", $teacher_id, $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        throw new Exception("Teacher not found or not authorized");
    }
    $stmt->close();
    $result->close();

    // Start transaction
    $msgDb->beginTransaction(true);

    // Check existing conversation
    $query = "SELECT conversation_id 
              FROM conversations 
              WHERE teacher_id = ? 
              AND student_id = ? 
              AND status = 'active'";
    
    $stmt = $msgDb->prepare($query, true);
    if (!$stmt) {
        throw new Exception("Conversation check prepare failed");
    }
    
    $stmt->bind_param("ii", $teacher_id, $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $stmt->close();
        $result->close();
        
        // Create new conversation
        $query = "INSERT INTO conversations 
                 (teacher_id, student_id, subject, last_message_time, created_at, status) 
                 VALUES (?, ?, 'New Conversation', NOW(), CURRENT_TIMESTAMP, 'active')";
        
        $stmt = $msgDb->prepare($query, true);
        if (!$stmt) {
            throw new Exception("Conversation creation prepare failed");
        }
        
        $stmt->bind_param("ii", $teacher_id, $student_id);
        $stmt->execute();
        $conversation_id = $stmt->insert_id;
        $stmt->close();
    } else {
        $conversation = $result->fetch_assoc();
        $conversation_id = $conversation['conversation_id'];
        $stmt->close();
        $result->close();
    }
    
    // Insert message
    $query = "INSERT INTO messages 
             (conversation_id, sender_id, message, sender_type, receiver_id, 
              receiver_type, sent_at, read_status, is_deleted, created_at) 
             VALUES 
             (?, ?, ?, 'student', ?, 'teacher', NOW(), 0, 0, CURRENT_TIMESTAMP)";
    
    $stmt = $msgDb->prepare($query, true);
    if (!$stmt) {
        throw new Exception("Message insertion prepare failed");
    }
    
    $stmt->bind_param("iisi", 
        $conversation_id, 
        $student_id, 
        $message,
        $teacher_id
    );
    $stmt->execute();
    $stmt->close();
    
    // Update conversation time
    $query = "UPDATE conversations 
             SET last_message_time = NOW() 
             WHERE conversation_id = ?";
    
    $stmt = $msgDb->prepare($query, true);
    if (!$stmt) {
        throw new Exception("Conversation update prepare failed");
    }
    
    $stmt->bind_param("i", $conversation_id);
    $stmt->execute();
    $stmt->close();
    
    $msgDb->commit(true);
    sendJsonResponse(true);
    
} catch (Exception $e) {
    if (isset($msgDb)) {
        $msgDb->rollback(true);
    }
    error_log("Message sending error: " . $e->getMessage());
    sendJsonResponse(false, $e->getMessage());
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($result)) $result->close();
    if (isset($mainDb)) $mainDb->close();
    if (isset($msgDb)) $msgDb->close();
}
?>
