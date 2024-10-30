<?php
session_start();
// Redirect if already logged in
if (isset($_SESSION['id'])) {
    header("Location: teacher_home.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/teacher-login.css">
</head>
<body>
    <div class="container">
        <div class="left-section">
            <div class="logo">
                <h2 class="logo-text">GDMC</h2>
                <span class="logo-subtext">Gov. D M Camerino</span>
            </div>
            <div class="content">
                <h1 class="sign-in-as">Sign in as</h1>
                <div class="illustration">
                    <img src="../images/teacherbg.png" alt="Teacher Illustration">
                </div>
                <div class="student-label">TEACHER</div>
            </div>
            <a href="login.php" class="back-button">
                <i class="fas fa-arrow-left"></i> BACK
            </a>
        </div>
        <div class="right-section">
            <img src="../images/human3.png" alt="Decoration" class="decoration decoration-1">
            <img src="../images/human2.png" alt="Decoration" class="decoration decoration-2">
            <div class="form-container">
                <h1>Welcome to <span class="school-name">Gov. D M Camerino</span></h1>
                <h2>Sign in</h2>
                <!-- Updated error message display -->
                <?php
                if (isset($_SESSION['error_message'])) {
                    echo '<div class="message-box fade-in">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
                    unset($_SESSION['error_message']); // Clear the message after displaying
                }
                ?>
                
                <div class="social-login">
                    <button class="google-btn">
                        <img src="../images/google.png" alt="Google">
                        Sign in with Google
                    </button>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-github"></i></a>
                    </div>
                </div>
                <form action="student_login_action.php" method="POST">
                    <div class="input-group">
                        <label for="username">Enter your Username or Email Address</label>
                        <input type="text" id="username" name="username" placeholder="Username or Email Address" required>
                    </div>
                    <div class="input-group">
                        <label for="password">Enter your Password</label>
                        <input type="password" id="password" name="password" placeholder="Password" required>
                        <a href="#" class="forgot-password">Forgot Password?</a>
                    </div>
                    <button type="submit" class="signin-btn">Sign in</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
