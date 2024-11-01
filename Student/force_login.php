<?php
session_start();
require_once('../db/dbConnector.php');

$db = new DbConnector();

if (isset($_GET['username']) && isset($_GET['password'])) {
    $username = $db->escapeString($_GET['username']);
    $password = $db->escapeString($_GET['password']);
    $remember = isset($_GET['remember']) && $_GET['remember'] === '1';

    // Get user data
    $query = "SELECT * FROM student WHERE username = '$username' AND password = '$password'";
    $result = $db->query($query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_array($result);

        // Clear existing sessions for this user
        $delete_sessions = "DELETE FROM active_sessions WHERE student_id = ?";
        $stmt = $db->prepare($delete_sessions);
        $stmt->bind_param("i", $user['student_id']);
        $stmt->execute();

        // Create new session
        $_SESSION['id'] = $user['student_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['last_activity'] = time();
        $_SESSION['just_logged_in'] = true;

        // Log the new session
        $ip = $_SERVER['REMOTE_ADDR'];
        $insert_session = "INSERT INTO active_sessions (student_id, session_id, ip_address, last_activity) 
                          VALUES (?, ?, ?, NOW())";
        $stmt = $db->prepare($insert_session);
        $session_id = session_id();
        $stmt->bind_param("iss", $user['student_id'], $session_id, $ip);
        $stmt->execute();

        // Handle Remember Me
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+30 days'));
            
            $query = "INSERT INTO remember_tokens (student_id, token, expiry) 
                     VALUES (?, ?, ?)";
            $stmt = $db->prepare($query);
            $stmt->bind_param("iss", $user['student_id'], $token, $expiry);
            $stmt->execute();
            
            setcookie('remember_token', $token, time() + (86400 * 30), '/', '', true, true);
        }

        // Show success message and redirect
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Logging In...</title>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        </head>
        <body>
            <script>
            Swal.fire({
                title: 'Success!',
                text: 'Successfully logged in. Previous sessions have been terminated.',
                icon: 'success',
                timer: 2000,
                timerProgressBar: true,
                showConfirmButton: false
            }).then(() => {
                window.location.href = 'home.php';
            });
            </script>
        </body>
        </html>
        <?php
        exit();
    }
}

// If something goes wrong, redirect to login
header("Location: Student-Login.php");
exit();
?>
