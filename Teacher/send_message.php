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
if (!isset($_SESSION['teacher_id'])) {
    sendJsonResponse(false, 'Unauthorized');
}

// Validate input
if (empty($_POST['student_id']) || empty($_POST['message'])) {
    sendJsonResponse(false, 'Missing required fields');
}

try {
    // Create connection to message database
    $msgDb = new DbConnector(true);  // true for message database
    // Create connection to main database for verification
    $mainDb = new DbConnector(false); // false for main database

    $teacher_id = $_SESSION['teacher_id'];
    $student_id = (int)$_POST['student_id'];
    $message = trim($_POST['message']);

    // Verify teacher
    $verify_query = "SELECT teacher_id FROM teacher WHERE teacher_id = ?";
    $stmt = $mainDb->prepare($verify_query);
    if (!$stmt) {
        throw new Exception("Teacher verification prepare failed");
    }
    $stmt->bind_param("i", $teacher_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        throw new Exception("Teacher not found");
    }
    $stmt->close();
    $result->close();
    
    // Verify student
    $verify_query = "SELECT student_id FROM student WHERE student_id = ?";
    $stmt = $mainDb->prepare($verify_query);
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
             (?, ?, ?, 'teacher', ?, 'student', NOW(), 0, 0, CURRENT_TIMESTAMP)";
    
    $stmt = $msgDb->prepare($query, true);
    if (!$stmt) {
        throw new Exception("Message insertion prepare failed");
    }
    
    $stmt->bind_param("iisi", 
        $conversation_id, 
        $teacher_id, 
        $message,
        $student_id
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
