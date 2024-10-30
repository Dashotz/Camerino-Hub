<?php
session_start(); // Start the session

// Dummy credentials for demonstration purposes
$valid_username = "user@example.com";
$valid_password = "password123"; // In a real application, use hashed passwords

// Initialize error message
$error_message = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Simple authentication check
    if ($username === $valid_username && $password === $valid_password) {
        // Set session variable and redirect to a protected page
        $_SESSION['username'] = $username;
        header("Location: dashboard.php"); // Redirect to a dashboard or home page
        exit();
    } else {
        $error_message = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="css/style.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="left-panel">
            <div class="logo">Your Logo</div>
            <div class="left-content">
                <h1>Sign in to</h1>
                <p>Lorem Ipsum is simply</p>
                <img src="path/to/3d-character.png" alt="3D Character" class="character-image">
            </div>
            <div class="login-as">
                <p>Login as</p>
                <div class="login-options">
                    <div class="login-option">
                        <img src="path/to/teacher-icon.png" alt="Teacher">
                        <span>TEACHER</span>
                    </div>
                    <div class="login-option">
                        <img src="path/to/students-icon.png" alt="Students">
                        <span>STUDENTS</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="right-panel">
            <h2>Welcome to <span class="school-name">SCHOOL NAME</span></h2>
            <h1>Sign in</h1>
            <div class="social-login">
                <button class="google-btn">
                    <img src="path/to/google-icon.png" alt="Google">
                    Sign in with Google
                </button>
                <div class="other-socials">
                    <button class="facebook-btn"><img src="path/to/facebook-icon.png" alt="Facebook"></button>
                    <button class="apple-btn"><img src="path/to/apple-icon.png" alt="Apple"></button>
                </div>
            </div>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <div class="form-group">
                    <label>Enter your Username or Email Address</label>
                    <input type="text" name="username" placeholder="Username or Email Address" required>
                </div>
                <div class="form-group">
                    <label>Enter your Password</label>
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <div class="form-footer">
                    <a href="#" class="forgot-password">Forgot Password</a>
                    <button type="submit" class="sign-in-btn">Sign in</button>
                </div>
            </form>
            <?php if ($error_message): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>
            <p class="signup-prompt">No Account? <a href="#">Sign up</a></p>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
