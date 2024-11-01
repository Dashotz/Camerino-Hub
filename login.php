<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="student/css/style.css">
    <!-- Add Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <!-- Left Section for Illustration and Login Options -->
        <div class="left-section">
            <div class="logo">
                <h2 class="logo-text">GDMC</h2>
                <span class="logo-subtext">Gov. D M Camerino</span>
            </div>
            <div class="intro-text">
                <h1>Sign in to</h1>
                <p>Learning Management System</p>
                <p class="welcome-text">Empowering education through digital innovation at Gov. D M Camerino High School</p>
            </div>
            <div class="illustration">
                <img src="images/HUMAN.png" alt="Illustration" class="illustration-img">
            </div>
            <div class="login-as">
                <h3>Login as</h3>
                <div class="login-options">
                    <a href="teacher/Teacher-Login.php" class="login-option button">
                        <img src="images/teacher.png" alt="Teacher">
                        <p>TEACHER</p>
                    </a>
                    <a href="student/Student-Login.php" class="login-option button">
                        <img src="images/student1.png" alt="Student">
                        <p>STUDENTS</p>
                    </a>
                </div>
            </div>
        </div>

        <!-- Right Section for Login Form -->
        <div class="right-section">
            <div class="welcome-container">
                <div class="nav-links">
                    <a href="student/home.php" class="home-link">
                        <i class="fas fa-home"></i> Back to Home
                    </a>
                </div>
                <h1>Welcome to <span class="school-name">Gov. D M Camerino</span></h1>
                <h2>Sign in</h2>
                <div class="social-login">
                    <button class="google-btn">
                        <img src="images/google.png" alt="Google">
                        Sign in with Google
                    </button>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-github"></i></a>
                    </div>
                </div>
                <p class="welcome-message">We are glad to have you here! Please sign in to continue.</p>
                <blockquote class="welcome-quote">
                    "Empowering minds, shaping futures at LMS Camerino."
                </blockquote>
                <div class="additional-links">
                    <a href="student/home.php" class="btn-explore">
                        <i class="fas fa-globe"></i> Explore Our Website
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
