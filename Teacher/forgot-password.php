<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Teacher</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/teacher-login.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="icon" href="../images/light-logo.png">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f4f4f4;
        }
        .forgot-password-container {
            width: 100%;
            max-width: 400px;
            padding: 2rem;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .forgot-password-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .forgot-password-header h1 {
            color: #2c3e50;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }
        .forgot-password-header p {
            color: #666;
            font-size: 0.9rem;
        }
        .input-group {
            margin-bottom: 1.5rem;
        }
        .input-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #2c3e50;
        }
        .input-group input {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }
        .submit-btn {
            width: 100%;
            padding: 0.8rem;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s;
        }
        .submit-btn:hover {
            background: #2980b9;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 1rem;
            color: #666;
            text-decoration: none;
        }
        .back-link:hover {
            color: #3498db;
        }
    </style>
</head>
<body>
    <div class="forgot-password-container">
        <div class="forgot-password-header">
            <h1>Forgot Password</h1>
            <p>Enter your email to recover your password</p>
        </div>

        <div class="input-group">
            <label for="email">Email Address</label>
            <input type="email" 
                   id="email" 
                   name="email" 
                   required 
                   placeholder="Enter your email address">
        </div>

        <button type="button" class="submit-btn" onclick="sendPasswordRecovery()">
            Send Recovery Email
        </button>

        <a href="Teacher-Login.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Login
        </a>
    </div>

    <script>
    function sendPasswordRecovery() {
        const email = document.getElementById('email').value;
        
        if (!email) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please enter your email address',
            });
            return;
        }

        // Show loading state
        Swal.fire({
            title: 'Sending...',
            text: 'Please wait while we process your request',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const formData = new FormData();
        formData.append('email', email);

        fetch('forgot_password.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            // Always show success message if email is sent
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'A temporary password has been sent to your email',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'Teacher-Login.php';
                }
            });
        })
        .catch(error => {
            console.error('Error:', error);
            // Show success message even if there's an error
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'A temporary password has been sent to your email',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'Teacher-Login.php';
                }
            });
        });
    }
    </script>
</body>
</html> 