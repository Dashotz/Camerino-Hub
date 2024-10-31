<?php
session_start();
// Redirect if already logged in
if (isset($_SESSION['id'])) {
    header("Location: home.php");
    exit();
}

// Initialize attempt counter if not exists
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/student-login.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
    body {
        overflow-x: hidden;
        position: relative;
        min-height: 100vh;
    }

    .container {
        position: relative;
        min-height: 100vh;
        z-index: 1;
    }

    /* SweetAlert Customization */
    .swal2-popup {
        z-index: 9999;
    }

    .swal2-container {
        z-index: 10000;
    }

    .error-popup {
        border-radius: 15px;
        padding: 1.5rem;
    }

    .error-title {
        color: #dc3545;
        font-size: 1.5rem;
    }

    .error-button {
        border-radius: 25px;
        padding: 10px 30px;
        font-weight: 500;
    }

    .swal2-toast {
        background: #fff;
        color: #333;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }

    /* Prevent page shift when SweetAlert appears */
    .swal2-shown {
        padding-right: 0 !important;
    }

    .swal2-shown .container {
        padding-right: 0 !important;
    }

    .remember-me {
        display: flex;
    }
    </style>
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
                    <img src="../images/studentbg.png" alt="Student Illustration">
                </div>
                <div class="student-label">Student</div>
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
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    <div class="input-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Remember me</label>
                    </div>
                    <button type="submit" name="login" class="signin-btn">Sign in</button>
                </form>
            </div>
        </div>
    </div>

    <script>
    // Function to show error messages using SweetAlert2
    function showError(message, type = 'error') {
        Swal.fire({
            title: 'Login Error',
            text: message,
            icon: type,
            confirmButtonText: 'Try Again',
            confirmButtonColor: '#007bff',
            customClass: {
                popup: 'error-popup',
                title: 'error-title',
                confirmButton: 'error-button'
            },
            allowOutsideClick: false,
            allowEscapeKey: false
        });
    }

    // Show different error messages based on the error type
    <?php if (isset($_SESSION['error_type'])): ?>
        <?php
        $errorType = $_SESSION['error_type'];
        $errorMessage = '';
        $icon = 'error';
        
        switch ($errorType) {
            case 'wrong_password':
                $errorMessage = 'Incorrect password. Please try again.';
                break;
            case 'wrong_username':
                $errorMessage = 'Username not found. Please check and try again.';
                break;
            case 'not_registered':
                $errorMessage = 'Account not registered to school. Please contact administration.';
                $icon = 'warning';
                break;
            case 'max_attempts':
                $errorMessage = 'Account has been locked due to too many failed attempts. Please try again in 2 minutes.';
                $icon = 'warning';
                break;
            default:
                $errorMessage = $_SESSION['error_message'] ?? 'An error occurred. Please try again.';
        }
        ?>
        
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Login Error',
                text: '<?php echo $errorMessage; ?>',
                icon: '<?php echo $icon; ?>',
                confirmButtonText: 'Try Again',
                confirmButtonColor: '#007bff',
                customClass: {
                    popup: 'error-popup',
                    title: 'error-title',
                    confirmButton: 'error-button'
                }
            });
        });
        
        <?php 
        unset($_SESSION['error_type']);
        unset($_SESSION['error_message']);
        ?>
    <?php endif; ?>

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;
        
        if (!username || !password) {
            e.preventDefault();
            showError('Please fill in all fields');
        }
    });
    </script>
</body>
</html>
