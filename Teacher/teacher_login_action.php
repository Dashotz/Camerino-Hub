<?php
session_start();
require_once('../db/dbConnector.php');

if (isset($_POST['login'])) {
    $db = new DbConnector();
    
    $username = $db->escapeString($_POST['username']);
    $password = md5($db->escapeString($_POST['password']));
    
    // First check if username exists in student table
    $student_check = "SELECT * FROM student WHERE LOWER(username) = LOWER('$username')";
    $student_result = $db->query($student_check);
    
    if ($student_result && mysqli_num_rows($student_result) > 0) {
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Wrong Login Portal</title>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        </head>
        <body>
            <script>
                Swal.fire({
                    title: 'Wrong Login Portal',
                    text: 'This account belongs to a student. Please use the student login page.',
                    icon: 'error',
                    confirmButtonText: 'Go to Student Login',
                    allowOutsideClick: false
                }).then((result) => {
                    window.location.href = '../Student/Student-Login.php';
                });
            </script>
        </body>
        </html>
        <?php
        exit();
    }
    
    // Check teacher credentials
    $query = "SELECT * FROM teacher WHERE LOWER(username) = LOWER('$username')";
    $result = $db->query($query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        // Check if account is locked
        if ($user['login_attempts'] >= 3 && $user['lockout_time'] > date('Y-m-d H:i:s')) {
            header("Location: Teacher-Login.php");
            $_SESSION['error_type'] = 'max_attempts';
            exit();
        } else if ($user['lockout_time'] !== null && $user['lockout_time'] <= date('Y-m-d H:i:s')) {
            // Reset lockout if time has passed
            $reset_query = "UPDATE teacher SET login_attempts = 0, lockout_time = NULL 
                          WHERE teacher_id = '{$user['teacher_id']}'";
            $db->query($reset_query);
            $user['login_attempts'] = 0;
        }
        
        // Verify credentials with MD5 hash
        if ($password === $user['password']) {
            // Log successful login
            $ip = $_SERVER['REMOTE_ADDR'];
            $log_query = "INSERT INTO teacher_login_logs (teacher_id, ip_address, status) 
                         VALUES ('{$user['teacher_id']}', '$ip', 'success')";
            $db->query($log_query);
            
            // Reset attempts
            $reset_query = "UPDATE teacher SET login_attempts = 0, lockout_time = NULL 
                          WHERE teacher_id = '{$user['teacher_id']}'";
            $db->query($reset_query);
            
            // Set session variables based on actual table structure
            $_SESSION['teacher_id'] = $user['teacher_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['firstname'] = $user['firstname'];
            $_SESSION['lastname'] = $user['lastname'];
            $_SESSION['middlename'] = $user['middlename'];
            $_SESSION['department'] = $user['department'];
            $_SESSION['just_logged_in'] = true;
            $_SESSION['last_activity'] = time();
            
            header("Location: teacher_dashboard.php");
            exit();
        }
        
        // Failed login - increment attempts
        $attempts = $user['login_attempts'] + 1;
        $lockout_time = ($attempts >= 3) ? date('Y-m-d H:i:s', strtotime('+2 minutes')) : null;
        
        $update_query = "UPDATE teacher SET login_attempts = $attempts, 
                        lockout_time = " . ($lockout_time ? "'$lockout_time'" : "NULL") . " 
                        WHERE teacher_id = '{$user['teacher_id']}'";
        $db->query($update_query);
        
        if ($attempts >= 3) {
            header("Location: Teacher-Login.php");
            $_SESSION['error_type'] = 'max_attempts';
        } else {
            header("Location: Teacher-Login.php");
            $_SESSION['error_type'] = 'wrong_password';
        }
        exit();
    }
    
    header("Location: Teacher-Login.php");
    $_SESSION['error_type'] = 'wrong_username';
    exit();
}

header("Location: Teacher-Login.php");
$_SESSION['error_type'] = 'wrong_username';
exit();
?>
