<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$db = new DbConnector();
$admin_id = $_SESSION['admin_id'];

// Get admin info
$query = "SELECT * FROM admin WHERE admin_id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Settings - Gov D.M. Camerino</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        /* Add these base styles at the top */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #f4f6f9;
            min-height: 100vh;
        }

        /* Sidebar Styles - Updated to match dashboard */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100%;
            width: 250px;
            background: #2c3e50;
            padding: 20px;
            transition: all 0.3s ease;
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            gap: 15px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }

        .sidebar-header img {
            width: 40px;
            height: 40px;
            object-fit: contain;
            background: white;
            padding: 5px;
            border-radius: 8px;
        }

        .sidebar-header h3 {
            color: white;
            font-size: 1.2rem;
            font-weight: 600;
            margin: 0;
            white-space: nowrap;
        }

        .menu-items {
            list-style: none;
        }

        .menu-items li {
            margin-bottom: 5px;
        }

        .menu-items a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 15px;
            color: #ecf0f1;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .menu-items a:hover, .menu-items a.active {
            background: rgba(255,255,255,0.1);
            transform: translateX(5px);
        }

        /* Settings Container Styles - Updated */
        .settings-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .settings-section {
            margin-bottom: 30px;
        }

        .settings-section h3 {
            color: #2c3e50;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eee;
            font-size: 1.2rem;
            font-weight: 600;
        }

        /* Form Styles - Updated */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #34495e;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #3498db;
        }

        .btn-save {
            background: #3498db;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s;
            font-weight: 500;
        }

        .btn-save:hover {
            background: #2980b9;
        }

        /* Profile Image Styles */
        .profile-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
            border: 3px solid #3498db;
        }

        /* Header Styles - Updated to match dashboard */
        .header {
            background: white;
            padding: 15px 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .header h2 {
            color: #2c3e50;
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
        }

        /* Main Content Styles */
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
                padding: 10px;
            }

            .sidebar-header h3, .menu-items span {
                display: none;
            }

            .main-content {
                margin-left: 70px;
            }

            .hamburger-btn {
                display: block;
                background: none;
                border: none;
                color: #2c3e50;
                font-size: 1.5rem;
                cursor: pointer;
                margin-right: 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="../images/Logo.png" alt="Logo">
            <h3>Admin Panel</h3>
        </div>
        <ul class="menu-items">
            <li><a href="admin_dashboard.php"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
            <li><a href="manage_students.php"><i class="fas fa-users"></i> <span>Students</span></a></li>
            <li><a href="manage_teachers.php"><i class="fas fa-chalkboard-teacher"></i> <span>Teachers</span></a></li>
            <li><a href="manage_subjects.php"><i class="fas fa-book"></i> <span>Subjects</span></a></li>
            <li><a href="settings.php" class="active"><i class="fas fa-cog"></i> <span>Settings</span></a></li>
            <li>
                <a href="#" onclick="confirmLogout(event)">
                    <i class="fas fa-sign-out-alt"></i> 
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="main-content">
        <div class="header">
            <h2>Settings</h2>
        </div>

        <div class="settings-container">
            <!-- Profile Settings -->
            <div class="settings-section">
                <h3>Profile Settings</h3>
                <form id="profileForm">
                    <div class="form-group">
                        <img src="<?php echo $admin['profile_image'] ?? 'assets/default-profile.png'; ?>" 
                             alt="Profile" class="profile-image" id="profilePreview">
                        <input type="file" id="profileImage" name="profile_image" accept="image/*">
                    </div>
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($admin['username']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>">
                    </div>
                    <button type="submit" class="btn-save">Save Profile</button>
                </form>
            </div>

            <!-- Security Settings -->
            <div class="settings-section">
                <h3>Security Settings</h3>
                <form id="securityForm">
                    <div class="form-group">
                        <label for="currentPassword">Current Password</label>
                        <input type="password" id="currentPassword" name="current_password">
                    </div>
                    <div class="form-group">
                        <label for="newPassword">New Password</label>
                        <input type="password" id="newPassword" name="new_password">
                    </div>
                    <div class="form-group">
                        <label for="confirmPassword">Confirm New Password</label>
                        <input type="password" id="confirmPassword" name="confirm_password">
                    </div>
                    <button type="submit" class="btn-save">Update Password</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Profile image preview
        document.getElementById('profileImage').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profilePreview').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        // Profile update
        $('#profileForm').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'update_profile');

            $.ajax({
                url: 'handlers/settings_handler.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    const data = JSON.parse(response);
                    if (data.status === 'success') {
                        Swal.fire('Success', 'Profile updated successfully', 'success');
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Failed to update profile', 'error');
                }
            });
        });

        // Password update
        $('#securityForm').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'update_password');

            if (formData.get('new_password') !== formData.get('confirm_password')) {
                Swal.fire('Error', 'Passwords do not match', 'error');
                return;
            }

            $.ajax({
                url: 'handlers/settings_handler.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    const data = JSON.parse(response);
                    if (data.status === 'success') {
                        Swal.fire('Success', 'Password updated successfully', 'success');
                        $('#securityForm')[0].reset();
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Failed to update password', 'error');
                }
            });
        });

        // Add this to your existing JavaScript
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('active');
            document.querySelector('.main-content').classList.toggle('active');
        }

        // Add hamburger menu for mobile if needed
        if (window.innerWidth <= 768) {
            const header = document.querySelector('.header');
            const hamburger = document.createElement('button');
            hamburger.innerHTML = '<i class="fas fa-bars"></i>';
            hamburger.className = 'hamburger-btn';
            hamburger.onclick = toggleSidebar;
            header.insertBefore(hamburger, header.firstChild);
        }
    </script>
</body>
</html>
