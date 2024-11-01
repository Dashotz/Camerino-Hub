<?php
session_start();
if (isset($_SESSION['admin_id'])) {
    header("Location: admin_dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Gov D.M. Camerino</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .nav-container {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            padding: 15px 30px;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
        }

        .nav-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo-section img {
            height: 45px;
            width: auto;
        }

        .logo-text {
            font-size: 1.2rem;
            font-weight: 600;
            color: #2c3e50;
        }

        .nav-links {
            display: flex;
            gap: 30px;
        }

        .nav-link {
            text-decoration: none;
            color: #2c3e50;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-link:hover {
            color: #3498db;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -5px;
            left: 0;
            background-color: #3498db;
            transition: width 0.3s ease;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .login-container {
            margin-top: 100px;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
            flex: 1;
        }

        .login-box {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            position: relative;
            overflow: hidden;
        }

        .login-box::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, #3498db, #2ecc71);
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header img {
            width: 80px;
            margin-bottom: 15px;
        }

        .login-header h2 {
            color: #2c3e50;
            font-size: 1.8rem;
            margin-bottom: 10px;
        }

        .login-header p {
            color: #7f8c8d;
            font-size: 0.9rem;
        }

        .input-group {
            margin-bottom: 20px;
            position: relative;
        }

        .input-group input {
            width: 100%;
            padding: 12px 40px 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .input-group input:focus {
            border-color: #3498db;
            box-shadow: 0 0 10px rgba(52, 152, 219, 0.1);
        }

        .input-group i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #95a5a6;
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(90deg, #3498db, #2ecc71);
            border: none;
            border-radius: 10px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
        }

        .back-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            padding: 8px 15px;
            background: white;
            border: none;
            border-radius: 20px;
            color: #2c3e50;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.9rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            transform: translateX(-5px);
        }

        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }

            .login-box {
                padding: 30px;
            }
        }
    </style>
</head>
<body>
    <nav class="nav-container">
        <div class="nav-content">
            <div class="logo-section">
                <img src="../images/Logo.png" alt="School Logo">
                <span class="logo-text">Gov D.M. Camerino</span>
            </div>
            <div class="nav-links">
                <a href="../index.php" class="nav-link">Home</a>
                <a href="../about.php" class="nav-link">About</a>
                <a href="../contact.php" class="nav-link">Contact</a>
            </div>
        </div>
    </nav>

    <a href="../index.php" class="back-btn">
        <i class="fas fa-arrow-left"></i> Back
    </a>

    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <img src="../images/admin.png" alt="Admin Icon">
                <h2>Admin Login</h2>
                <p>Welcome back! Please login to your account.</p>
            </div>
            
            <form action="admin_login_action.php" method="POST">
                <div class="input-group">
                    <input type="text" name="username" placeholder="Username" required>
                    <i class="fas fa-user"></i>
                </div>
                <div class="input-group">
                    <input type="password" name="password" placeholder="Password" required>
                    <i class="fas fa-lock"></i>
                </div>
                <button type="submit" name="login" class="login-btn">
                    Login <i class="fas fa-arrow-right"></i>
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    <?php if (isset($_SESSION['error_type'])): ?>
        Swal.fire({
            title: 'Error',
            text: '<?php echo $_SESSION['error_message'] ?? "An error occurred"; ?>',
            icon: 'error',
            confirmButtonColor: '#3498db',
            confirmButtonText: 'Try Again'
        });
        <?php 
        unset($_SESSION['error_type']);
        unset($_SESSION['error_message']);
        ?>
    <?php endif; ?>

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const username = document.querySelector('input[name="username"]').value;
        const password = document.querySelector('input[name="password"]').value;
        
        if (!username || !password) {
            e.preventDefault();
            Swal.fire({
                title: 'Error',
                text: 'Please fill in all fields',
                icon: 'error',
                confirmButtonColor: '#3498db'
            });
        }
    });
    </script>
</body>
</html>
