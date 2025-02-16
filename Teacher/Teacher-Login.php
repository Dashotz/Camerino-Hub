<?php
session_start();
// Redirect if already logged in
if (isset($_SESSION['id'])) {
    header("Location: ../teacher/teacher_dashboard.php");
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
    <!-- Add SweetAlert2 CSS and JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<link rel="icon" href="../images/light-logo.png">
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
		.container a{ 
		text-decoration: none; 
	}
    </style>
</head>
<body>
    <div class="container">
        <div class="left-section">
            <div class="logo">
               <a href="../teacher/teacher_dashboard.php"><h2 class="logo-text">GDMC</h2></a>
               <a href="../teacher/teacher_dashboard.php"> <span class="logo-subtext">Gov. D M Camerino</span></a>
            </div>
            <div class="content">
                <h1 class="sign-in-as">Sign in as</h1>
                <div class="illustration">
                    <img src="../images/teacherbg.png" alt="Teacher Illustration">
                </div>
                <div class="student-label">TEACHER</div>
            </div>
            <a href="../login.php" class="back-button">
                <i class="fas fa-arrow-left"></i> BACK
            </a>
        </div>
        <div class="right-section">
            <img src="../images/human3.png" alt="Decoration" class="decoration decoration-1">
            <img src="../images/human2.png" alt="Decoration" class="decoration decoration-2">
            <div class="form-container">
                <h1>Welcome to <span class="school-name">Gov. D M Camerino</span></h1>
                <h2>Sign in</h2>
                
                <!-- Error handling with SweetAlert -->
                <?php
                if (isset($_SESSION['error_type'])) {
                    echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'error',
                                title: '";
                    
                    // Set appropriate titles
                    switch($_SESSION['error_type']) {
                        case 'archived_account':
                            echo "Account Archived";
                            break;
                        case 'inactive_account':
                            echo "Account Inactive";
                            break;
                        case 'student_account':
                            echo "Wrong Login Portal";
                            break;
                        case 'wrong_username':
                            echo "Invalid Credentials";
                            break;
                        case 'wrong_password':
                            echo "Login Failed";
                            break;
                        case 'max_attempts':
                            echo "Account Locked";
                            break;
                        default:
                            echo "Error";
                    }
                    
                    echo "',
                                text: '";
                    
                    // Use custom message if set, otherwise use default messages
                    if (isset($_SESSION['error_message'])) {
                        echo $_SESSION['error_message'];
                    } else {
                        switch($_SESSION['error_type']) {
                            case 'student_account':
                                echo "This is a student account. Please use the student login page.";
                                break;
                            case 'wrong_username':
                                echo "The username you entered is incorrect. Please check your credentials and try again.";
                                break;
                            case 'wrong_password':
                                echo "The password you entered is incorrect. Please try again.";
                                break;
                            case 'max_attempts':
                                echo "Your account has been temporarily locked due to multiple failed login attempts. Please try again in 2 minutes.";
                                break;
                            default:
                                echo "An unexpected error occurred. Please try again later.";
                        }
                    }
                    
                    echo "',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'OK',
                                customClass: {
                                    popup: 'error-popup',
                                    title: 'error-title',
                                    confirmButton: 'error-button'
                                }
                            });
                        });
                    </script>";
                    unset($_SESSION['error_type']);
                    unset($_SESSION['error_message']);
                }
                ?>
                
                <form action="teacher_login_action.php" method="POST">
                    <div class="input-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" placeholder="Username" required>
                    </div>
                    <div class="input-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Password" required>
                        <a href="forgot-password.php" class="forgot-password">Forgot Password?</a>
                    </div>
                    <button type="submit" name="login" class="signin-btn">Sign in</button>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
