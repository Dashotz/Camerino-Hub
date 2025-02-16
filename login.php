<?php
session_start();
require_once('db/dbConnector.php');

// Redirect if already logged in
if (isset($_SESSION['id'])) {
    header("Location: student/student_dashboard.php");
    exit();
} elseif (isset($_SESSION['teacher_id'])) {
    header("Location: teacher/teacher_dashboard.php");
    exit();
} elseif (isset($_SESSION['admin_id'])) {
    header("Location: admin/admin_dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - GDMC Learning Hub</title>
    <link rel="stylesheet" href="student/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="icon" href="images/light-logo.png">
    <style>
        /* Add your existing styles here */
        .login-form {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        .login-btn {
            width: 100%;
            padding: 1rem;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s;
        }

        .login-btn:hover {
            background: #2980b9;
        }

        .forgot-password {
            text-align: right;
            margin-top: 1rem;
        }

        .forgot-password a {
            color: #3498db;
            text-decoration: none;
        }

        .forgot-password a:hover {
            text-decoration: underline;
        }

        .user-type-select {
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .user-type-select select {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Left Section -->
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
        </div>

        <!-- Right Section -->
        <div class="right-section">
            <div class="welcome-container">
                <h1>Welcome to <span class="school-name">Gov. D M Camerino</span></h1>
                <h2>Sign in</h2>
                
                <div class="login-form">
                    <form id="loginForm" action="login_action.php" method="POST">
                        
                        <div class="form-group" id="lrnGroup">
                            <label for="username">Username/LRN</label>
                            <input type="text" id="username" name="username" required>
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" required>
                        </div>

                        <div class="forgot-password">
                            <a href="forgot-password.php">Forgot Password?</a>
                        </div>

                        <button type="submit" name="login" value="1" class="login-btn">Sign in</button>
                    </form>
                </div>

                <div class="additional-links">
                    <a href="https://drive.usercontent.google.com/download?id=1WcX2UQdMtVSdUcME_EhTa2_AoWSnR2I7&export=download&authuser=0" 
                       class="btn-explore">
                        <i class="fab fa-android"></i> Try Our Mobile App!
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Get the username input element
        const usernameInput = document.getElementById('username');
        
        // Add input event listener
        usernameInput.addEventListener('input', function(e) {
            const value = e.target.value;
            
            // Count letters in the input
            const letterCount = (value.match(/[a-zA-Z]/g) || []).length;
            
            // If all numbers or only one letter, limit to 12 characters
            if (/^\d+$/.test(value) || letterCount === 1) {
                this.maxLength = 12;
                
                // If more than 12 digits, truncate to 12
                if (value.length > 12) {
                    this.value = value.slice(0, 12);
                }
            } else if (letterCount >= 2) {
                // Input contains at least two letters
                this.removeAttribute('maxLength');
            }
        });
        
        // Add paste event listener
        usernameInput.addEventListener('paste', function(e) {
            // Get pasted content
            const pastedText = (e.clipboardData || window.clipboardData).getData('text');
            
            // Count letters in pasted content
            const letterCount = (pastedText.match(/[a-zA-Z]/g) || []).length;
            
            // If all numbers or only one letter
            if (/^\d+$/.test(pastedText) || letterCount === 1) {
                e.preventDefault();
                // Only take first 12 digits if longer
                this.value = pastedText.slice(0, 12);
            }
        });
        
        <?php if (isset($_SESSION['error_type'])): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '<?php echo $_SESSION['error_message'] ?? "Invalid credentials" ?>',
                confirmButtonColor: '#3498db'
            });
            <?php 
            unset($_SESSION['error_type']);
            unset($_SESSION['error_message']);
            ?>
        <?php endif; ?>
    </script>
</body>
</html>
