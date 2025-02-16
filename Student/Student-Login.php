<?php
require_once('cleanup_sessions.php');
session_start();
require_once('../db/dbConnector.php');

// Initialize database connection
$db = new DbConnector();

// Redirect if already logged in
if (isset($_SESSION['id'])) {
    header("Location: student_dashboard.php");
    exit();
}

// Initialize attempt counter if not exists
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}

// Check if the user is already logged in on another device
if (isset($_POST['login'])) {
    $student_id = $_POST['student_id'];
    
    // Check if user is already online
    $check_online_query = "SELECT user_online, student_id FROM student WHERE student_id = ?";
    $stmt = $db->prepare($check_online_query);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if ($user['user_online'] == 1) {
            $_SESSION['error_type'] = 'already_logged_in';
            $_SESSION['error_message'] = 'This account is already logged in on another device.';
            header("Location: Student-Login.php");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Student Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/student-login.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
	<link rel="icon" href="../images/light-logo.png">
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
    
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

    /* Custom styles for the animated alerts */
    .animated-popup {
        border-radius: 15px;
        padding: 2rem;
        background: linear-gradient(145deg, #ffffff, #f3f4f6);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .success-title {
        color: #2c5282;
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
    }

    .login-success-content {
        text-align: center;
    }

    .success-icon {
        width: 120px;
        height: 120px;
        margin-bottom: 1.5rem;
    }

    .welcome-text {
        color: #2d3748;
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .welcome-subtext {
        color: #4a5568;
        font-size: 1.1rem;
    }

    .success-button {
        padding: 12px 30px;
        font-size: 1.1rem;
        font-weight: 600;
        border-radius: 50px;
        text-transform: uppercase;
        transition: all 0.3s ease;
    }

    .success-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(76, 175, 80, 0.3);
    }

    .error-popup {
        border-radius: 15px;
        padding: 2rem;
        background: linear-gradient(145deg, #ffffff, #fff5f5);
    }

    .error-title {
        color: #c53030;
        font-size: 1.8rem;
        font-weight: 700;
    }

    .error-icon {
        font-size: 3rem;
        color: #dc3545;
        margin-bottom: 1rem;
    }

    .error-message {
        color: #2d3748;
        font-size: 1.1rem;
        margin-bottom: 1rem;
    }

    .error-button {
        padding: 10px 25px;
        font-size: 1rem;
        font-weight: 600;
        border-radius: 50px;
        text-transform: uppercase;
    }

    /* Add animation classes */
    .animate__animated {
        animation-duration: 0.8s;
    }

    .animate__faster {
        animation-duration: 0.5s;
    }

    .password-container {
        position: relative;
        width: 100%;
    }

    .password-toggle {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #666;
    }

    .password-toggle:hover {
        color: #333;
    }

    .input-group {
        margin-bottom: 1.5rem;
        width: 100%;
        max-width: 400px;
    }

    .input-group label {
        display: block;
        margin-bottom: 0.5rem;
        color: #4a5568;
        font-size: 0.9rem;
        font-weight: 500;
    }

    .input-group input,
    .password-container {
        width: 100%;
        height: 40px;
        padding: 0.5rem 1rem;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 0.95rem;
        transition: border-color 0.3s ease;
        box-sizing: border-box;
    }

    .input-group input:focus,
    .password-container:focus-within {
        border-color: #4a90e2;
        outline: none;
        box-shadow: 0 0 0 2px rgba(74, 144, 226, 0.1);
    }

    .password-container {
        display: flex;
        align-items: center;
        padding: 0;
        max-width: 400px;
    }

    .password-container input {
        border: none;
        height: 100%;
        padding: 0 1rem;
        width: calc(100% - 40px);
        background: transparent;
    }

    .password-container input:focus {
        box-shadow: none;
    }

    .password-toggle {
        width: 40px;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: #666;
    }

    .signin-btn {
        width: 100%;
        max-width: 400px;
        height: 40px;
        background: #4a90e2;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.3s ease;
        margin-top: 1rem;
    }

    .signin-btn:hover {
        background: #357abd;
    }

    /* Social login buttons */
    .social-login {
        width: 100%;
        max-width: 400px;
        margin-bottom: 2rem;
    }

    .google-btn {
        width: 100%;
        height: 40px;
        background-color: #ffffff;
        border: 1px solid #dadce0;
        border-radius: 4px;
        display: flex;
        align-items: center;
        padding: 0 12px;
        cursor: pointer;
        transition: background-color 0.2s;
        margin-bottom: 20px;
    }

    .google-btn:hover {
        background-color: #f8f9fa;
    }

    .google-btn img {
        width: 18px;
        height: 18px;
        margin-right: 24px;
    }

    .google-btn span {
        color: #3c4043;
        font-size: 14px;
        font-weight: 500;
        flex-grow: 1;
        text-align: center;
    }

    /* Social Icons */
    .social-icons {
        display: flex;
        gap: 12px;
        justify-content: center;
        margin-bottom: 24px;
    }

    .social-icons a {
        color: #1a73e8;
        font-size: 24px;
        text-decoration: none;
        transition: color 0.2s;
    }

    .social-icons a:hover {
        color: #174ea6;
    }

    /* Form Container */
    .form-container {
        max-width: 400px;
        width: 100%;
        margin: 0 auto;
        padding: 40px 20px;
    }

    .form-container h1 {
        color: #202124;
        font-size: 24px;
        margin-bottom: 10px;
    }

    .form-container h2 {
        color: #202124;
        font-size: 24px;
        margin-bottom: 30px;
    }

    /* Input Fields */
    .input-group {
        margin-bottom: 20px;
    }

    .input-group label {
        display: block;
        color: #5f6368;
        font-size: 14px;
        margin-bottom: 8px;
    }

    .input-group input {
        width: 100%;
        height: 40px;
        padding: 8px 12px;
        border: 1px solid #dadce0;
        border-radius: 4px;
        font-size: 14px;
    }

    /* Sign In Button */
    .signin-btn {
        width: 100%;
        height: 40px;
        background-color: #1a73e8;
        color: white;
        border: none;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .signin-btn:hover {
        background-color: #174ea6;
    }
	
	.container a{ 
		text-decoration: none; 
	}

    /* Base container styles */
    .container {
        display: flex;
        min-height: 100vh;
        overflow-x: hidden; /* Prevent horizontal scroll */
    }

    .right-section {
        padding: 20px;
        overflow-y: auto; /* Allow vertical scrolling if needed */
    }

    .form-container {
        width: 100%;
        max-width: 400px;
        padding: 20px;
        margin: 0 auto;
    }

    /* Updated responsive styles */
    @media (max-width: 768px) {
        .container {
            flex-direction: column;
        }

        .left-section {
            padding: 1rem;
            min-height: auto;
            max-height: 40vh; /* Limit height of left section */
        }

        .right-section {
            flex: 1;
            padding: 1rem;
            height: auto;
            min-height: 60vh; /* Ensure enough space for form */
        }

        .form-container {
            padding: 15px;
            margin-bottom: 30px; /* Add space at bottom */
        }

        /* Adjust form elements */
        .input-group {
            margin-bottom: 15px;
            width: 100%;
        }

        .input-group input,
        .password-container {
            width: 100%;
            max-width: none;
        }

        /* Make illustration smaller on mobile */
        .illustration img {
            max-width: 120px;
            height: auto;
        }
    }

    /* Additional adjustments for very small screens */
    @media (max-width: 480px) {
        .left-section {
            max-height: 35vh;
            padding: 0.5rem;
        }

        .right-section {
            padding: 0.5rem;
        }

        .form-container {
            padding: 10px;
        }

        /* Adjust input fields */
        .input-group {
            margin-bottom: 12px;
        }

        .input-group label {
            margin-bottom: 4px;
        }

        .input-group input,
        .password-container,
        .signin-btn {
            height: 40px;
        }

        /* Make logo and text smaller */
        .logo-text {
            font-size: 20px;            
        }

        .logo-subtext {
            font-size: 12px;
        }

        /* Adjust back button position */
        .back-button {
            bottom: 10px;
            left: 10px;
            padding: 6px 12px;
            font-size: 14px;
        }
    }

    /* Fix for mobile browsers */
    @supports (-webkit-touch-callout: none) {
        .container {
            min-height: -webkit-fill-available;
        }
        
        .right-section {
            min-height: fit-content;
        }
    }

    /* Additional height fixes */
    html, body {
        height: 100%;
        margin: 0;
        padding: 0;
    }

    body {
        overflow-x: hidden;
        position: relative;
        min-height: 100vh;
    }

    /* Form element spacing */
    .input-group {
        margin-bottom: 15px;
        width: 100%;
    }

    .password-container {
        width: 100%;
        position: relative;
    }

    .signin-btn {
        width: 100%;
        margin-top: 15px;
    }

    /* Left section background and content */
    .left-section {
        flex: 1;
        background-color: #1a237e;
        color: white;
        padding: 2rem;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: center;
        position: relative;
        min-height: 100vh;
    }

    

    .logo h2.logo-text {
        font-size: 2.5rem;
        color: #ffffff;
        font-weight: bold;
        margin: 0;
        letter-spacing: 2px;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        text-align: left;         
    }

    .logo .logo-subtext {
        font-size: 1.2rem;
        color: #ffffff;
        opacity: 0.9;
        margin-top: 8px;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
        text-align: left;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .logo {
            padding: 25px;
            margin-top: 30px;
            margin-left: 15px;
        }
    }

    @media (max-width: 480px) {
        .logo {
            padding: 20px;
            margin-top: 25px;
            margin-left: 10px;
        }
    }

    /* Left section adjustment */
    .left-section {
        padding-top: 30px;
    }

    /* Content container */
    .content {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 100%;
        text-align: center;
        flex-grow: 1;
    }

    .sign-in-as {
        font-size: 24px;
        margin-bottom: 2rem;
    }

    /* Illustration container */
    .illustration {
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        margin: 1rem 0;
        padding-bottom: 2rem;
    }

    .illustration img {
        max-width: 280px;
        width: 100%;
        height: auto;
        object-fit: contain;
        margin-bottom: 1rem;
    }

    .student-label {
        font-size: 18px;
        margin-top: 1rem;
        padding-bottom: 1rem;
    }

    /* Back button */
    .back-button {
        position: absolute;
        bottom: 2rem;
        left: 2rem;
        top: 80%;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .left-section {
            min-height: 70vh;
            padding: 1.5rem;
            justify-content: center;
        }

        .logo {
            margin-bottom: 1rem;
        }

        .content {
            margin: 1rem 0;
        }

        .illustration {
            margin: 0.5rem 0;
        }

        .illustration img {
            max-width: 220px;
        }

        .back-button {
            bottom: 1rem;
            left: 1rem;
        }
    }

    @media (max-width: 480px) {
        .left-section {
            min-height: 80vh;
            padding: 1rem;
        }

        .logo {
            margin-bottom: 0.5rem;
        }

        .sign-in-as {
            font-size: 20px;
            margin-bottom: 1rem;
        }

        .illustration img {
            max-width: 180px;
        }

        .student-label {
            font-size: 16px;
            margin-top: 0.5rem;
        }
    }

    /* Fix for iOS devices */
    @supports (-webkit-touch-callout: none) {
        .left-section {
            min-height: -webkit-fill-available;
        }
    }

    /* Container adjustments */
    .container {
        display: flex;
        min-height: 100vh;
        overflow: hidden;
    }

    .right-section {
        flex: 1;
        overflow-y: auto;
    }

    /* Decoration images */
    .decoration {
        position: absolute;
        z-index: 1;
        pointer-events: none;
    }

    .decoration-1 {
        right: -50px;
        top: 20%;
        width: 200px;
        height: auto;
    }

    .decoration-2 {
        left: -30px;
        bottom: 10%;
        width: 180px;
        height: auto;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .container {
            flex-direction: column;
        }

        .left-section {
            min-height: 40vh;
            padding: 1rem;
            width: 100%;
        }

        .decoration {
            opacity: 0.6;
        }

        .decoration-1 {
            width: 150px;
            right: -30px;
            top: 15%;
        }

        .decoration-2 {
            width: 130px;
            left: -20px;
            bottom: 5%;
        }

        /* Ensure form content stays above decorations */
        .form-container {
            position: relative;
            z-index: 2;
            background-color: rgba(255, 255, 255, 0.95);
        }
    }

    @media (max-width: 480px) {
        .left-section {
            min-height: 35vh;
        }

        .decoration-1 {
            width: 120px;
            right: -20px;
        }

        .decoration-2 {
            width: 100px;
            left: -15px;
        }

        /* Adjust logo and text for better visibility */
        .logo {
            position: relative;
            z-index: 2;
        }

        .sign-in-as {
            position: relative;
            z-index: 2;
            text-align: center;
            margin: 1rem 0;
        }
    }

    /* Fix for iOS devices */
    @supports (-webkit-touch-callout: none) {
        .left-section {
            min-height: 35vh;
        }
    }

    /* Ensure content stays visible */
    .right-section {
        flex: 1;
        background-color: white;
        padding: 1.5rem;
        position: relative;
        z-index: 2;
        min-height: 60vh;
    }

    .forgot-password {
        text-align: right;
        margin-top: 10px;
    }
    
    .forgot-password a {
        color: #3498db;
        text-decoration: none;
        font-size: 0.9em;
    }
    
    .forgot-password a:hover {
        text-decoration: underline;
    }
    </style>
</head>
<body>
    <div class="container">
        <div class="left-section">
            <div class="logo">				
				<a href="student_dashboard.php"><h2 class="logo-text">GDMC</h2></a>
               <a href="student_dashboard.php"> <span class="logo-subtext">Gov. D M Camerino</span></a>
            </div>
            <div class="content">
                <h1 class="sign-in-as">Sign in as</h1>
                <div class="illustration">
                    <img src="../images/studentbg.png" alt="Student Illustration">
                </div>
                <div class="student-label">Student</div>
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
              

                <form action="student_login_action.php" method="POST">
                    <div class="input-group">
                        <label for="lrn">LRN (Learner Reference Number)</label>
                        <input type="text" 
                               id="lrn" 
                               name="lrn" 
                               required 
                               pattern="\d{12}" 
                               maxlength="12"
                               onkeypress="return onlyNumbers(event)"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                               title="Please enter a valid 12-digit LRN">
                    </div>
                    <div class="input-group">
                        <label for="password">Password</label>
                        <div class="password-container">
                            <input type="password" id="password" name="password" required>
                            <span class="password-toggle" onclick="togglePassword()">
                                <i class="far fa-eye" id="toggleIcon"></i>
                            </span>
                        </div>
                    </div>
                    <div class="forgot-password">
                        <a href="forgot-password.php">Forgot Password?</a>
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
            case 'already_logged_in':
                $errorMessage = 'This account is already logged in on another device. Please logout from other devices first.';
                $icon = 'warning';
                break;
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
            case 'teacher_account':
                $errorMessage = 'This appears to be a teacher account.';
                $icon = 'info';
                ?>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: 'Wrong Login Page',
                        text: 'This appears to be a teacher account. Would you like to go to the teacher login page?',
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, take me there',
                        cancelButtonText: 'No, stay here',
                        customClass: {
                            popup: 'error-popup',
                            title: 'error-title',
                            confirmButton: 'error-button'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'Teacher-Login.php';
                        }
                    });
                });
                <?php
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
        const lrn = document.getElementById('lrn').value;
        const password = document.getElementById('password').value;
        
        if (!lrn || !password) {
            e.preventDefault();
            showError('Please fill in all fields');
        }
    });

    // Add event listener for beforeunload to handle unexpected closures
    window.addEventListener('beforeunload', function() {
        // You might want to send an async request to update online status
        navigator.sendBeacon('update_online_status.php?status=offline');
    });

    // Function to allow only numbers
    function onlyNumbers(evt) {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    }

    // Add these event listeners after your existing script
    document.getElementById('lrn').addEventListener('paste', function(e) {
        // Prevent paste
        e.preventDefault();
        
        // Get pasted data
        let pastedData = (e.clipboardData || window.clipboardData).getData('text');
        
        // Remove any non-numeric characters
        pastedData = pastedData.replace(/[^0-9]/g, '');
        
        // Truncate to 12 digits if longer
        if (pastedData.length > 12) {
            pastedData = pastedData.substring(0, 12);
        }
        
        // Set the value
        this.value = pastedData;
    });

    document.getElementById('lrn').addEventListener('drop', function(e) {
        e.preventDefault();
    });

    // Function to show animated login success
    function showLoginSuccess(studentName) {
        Swal.fire({
            title: '<div class="animate__animated animate__fadeInDown">Welcome Back!</div>',
            html: `
                <div class="login-success-content">
                    <div class="animate__animated animate__zoomIn">
                        <img src="../images/success-student.gif" alt="Success" class="success-icon">
                    </div>
                    <div class="animate__animated animate__fadeInUp">
                        <p class="welcome-text">Hello, ${studentName}!</p>
                        <p class="welcome-subtext">Welcome to Gov. D.M. Camerino Learning Hub</p>
                    </div>
                </div>
            `,
            showConfirmButton: true,
            confirmButtonText: 'Let\'s Start Learning!',
            confirmButtonColor: '#4CAF50',
            allowOutsideClick: false,
            timer: 3000,
            timerProgressBar: true,
            customClass: {
                popup: 'animated-popup',
                title: 'success-title',
                confirmButton: 'success-button'
            },
            showClass: {
                popup: 'animate__animated animate__fadeInDown animate__faster'
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOutUp animate__faster'
            },
            didOpen: () => {
                // Add particles or confetti effect
                confetti({
                    particleCount: 100,
                    spread: 70,
                    origin: { y: 0.6 }
                });
            }
        }).then((result) => {
            // Redirect to dashboard after animation
            window.location.href = 'student_dashboard.php';
        });
    }

    // Function to show animated login error
    function showLoginError(message) {
        Swal.fire({
            title: '<div class="animate__animated animate__shakeX">Login Failed</div>',
            html: `
                <div class="login-error-content animate__animated animate__fadeIn">
                    <div class="error-icon">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <p class="error-message">${message}</p>
                </div>
            `,
            showConfirmButton: true,
            confirmButtonText: 'Try Again',
            confirmButtonColor: '#dc3545',
            showClass: {
                popup: 'animate__animated animate__fadeInDown animate__faster'
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOut animate__faster'
            },
            customClass: {
                popup: 'error-popup',
                title: 'error-title',
                confirmButton: 'error-button'
            }
        });
    }

    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.className = 'far fa-eye-slash';
        } else {
            passwordInput.type = 'password';
            toggleIcon.className = 'far fa-eye';
        }
    }
    </script>
</body>
</html>
