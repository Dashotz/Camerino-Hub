<?php
session_start();
require_once('db/dbConnector.php');

if (isset($_POST['login'])) {
    $db = new DbConnector();
    
    $username = $db->escapeString($_POST['username']);
    $password = $_POST['password'];
    
    // First try student login (LRN)
    if (is_numeric($username) && strlen($username) == 12) {
        if (handleStudentLogin($db, $username, $password)) {
            exit(); // Exit if student login was successful
        }
    }
    
    // Then try teacher login
    $teacher_query = "SELECT * FROM teacher WHERE username = ? AND status = 'active'";
    $stmt = $db->prepare($teacher_query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (md5($password) === $user['password']) {
            $_SESSION['teacher_id'] = $user['teacher_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['firstname'] = $user['firstname'];
            $_SESSION['user_type'] = 'teacher';
            
            // Check if password needs to be changed
            if ($user['password_recovery'] === 'yes') {
                $_SESSION['require_password_change'] = true;
                header("Location: teacher/teacher_profile.php?tab=security&force_change=1");
                exit();
            }
            
            // Log successful login
            logLogin($db, 'teacher', $user['teacher_id'], 'success');
            
            if ($user['password_recovery'] === 'yes') {
                header("Location: teacher/teacher_profile.php?tab=security");
                exit();
            } else {
                header("Location: teacher/teacher_dashboard.php");
                exit();
            }
        }
    }
    
    // Finally try admin login
    $admin_query = "SELECT * FROM admin WHERE username = ? AND status = 'active'";
    $stmt = $db->prepare($admin_query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        if (md5($password) === $admin['password']) {
            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['firstname'] = $admin['firstname'];
            $_SESSION['user_type'] = 'admin';
            
            // Log successful login
            logLogin($db, 'admin', $admin['admin_id'], 'success');
            
            header("Location: admin/admin_dashboard.php");
            exit();
        }
    }
    
    // If we get here, no valid login was found
    setError('Invalid username/LRN or password');
    header("Location: login.php");
    exit();
}

function handleStudentLogin($db, $lrn, $password) {
    // Debug log
    error_log("Attempting student login with LRN: " . $lrn);
    
    $query = "SELECT * FROM student WHERE lrn = ? AND status = 'active'";
    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $lrn);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Debug log
        error_log("Found student: " . json_encode($user));
        
        $hashed_password = md5($password);
        error_log("Comparing passwords - Hash: " . $hashed_password . " vs Stored: " . $user['password']);
        
        if ($hashed_password === $user['password']) {
            error_log("Password match successful");
            
            // Start a new session
            session_regenerate_id(true);
            
            $_SESSION['id'] = $user['student_id'];
            $_SESSION['firstname'] = $user['firstname'];
            $_SESSION['lastname'] = $user['lastname'];
            $_SESSION['user_type'] = 'student';
            $_SESSION['session_id'] = session_id();
            
            // Check if password needs to be changed
            if ($user['password_recovery'] === 'yes') {
                $_SESSION['require_password_change'] = true;
                header("Location: student/student_profile.php?tab=security&force_change=1");
                exit();
            }
            
            // Update student's session in database
            $update_session = "UPDATE student SET 
                user_online = 1,
                session_id = ?,
                last_activity = NOW()
                WHERE student_id = ?";
            $stmt = $db->prepare($update_session);
            $stmt->bind_param("si", $_SESSION['session_id'], $user['student_id']);
            $stmt->execute();
            
            // Log successful login
            logLogin($db, 'student', $user['student_id'], 'success');
            
            error_log("Redirecting to student dashboard...");
            ob_clean(); // Clear any output buffers
            header("Location: student/student_dashboard.php");
            exit();
        }
        error_log("Password mismatch");
    }
    error_log("Student login failed");
    
    // Don't return - throw error to main login handler
    return false;
}

function logLogin($db, $user_type, $user_id, $status) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $table = $user_type . '_login_logs';
    $id_field = $user_type . '_id';
    
    $query = "INSERT INTO $table ($id_field, ip_address, status) VALUES (?, ?, ?)";
    $stmt = $db->prepare($query);
    $stmt->bind_param("iss", $user_id, $ip, $status);
    $stmt->execute();
}

function setError($message) {
    $_SESSION['error_type'] = 'error';
    $_SESSION['error_message'] = $message;
}
?> 