<?php
session_start();
require_once('../db/dbConnector.php');

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    header('Location: Student-Login.php');
    exit();
}

// Initialize database connection
$db = new DbConnector();

// Get student data
$student_id = $_SESSION['id'];
$stmt = $db->prepare("SELECT * FROM student WHERE student_id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Sanitize and validate input
        $firstname = htmlspecialchars(trim($_POST['firstname']));
        $lastname = htmlspecialchars(trim($_POST['lastname']));
        $email = htmlspecialchars(trim($_POST['email']));
        $phone = htmlspecialchars(trim($_POST['phone']));
        $address = htmlspecialchars(trim($_POST['address']));

        // Update profile
        $updateStmt = $db->prepare("UPDATE student SET firstname=?, lastname=?, email=?, phone=?, address=? WHERE student_id=?");
        $updateStmt->bind_param("sssssi", $firstname, $lastname, $email, $phone, $address, $student_id);
        
        if ($updateStmt->execute()) {
            $_SESSION['success_message'] = "Profile updated successfully!";
        } else {
            $_SESSION['error_message'] = "Failed to update profile.";
        }
        
        header('Location: student_profile.php');
        exit();
    } catch (Exception $e) {
        $_SESSION['error_message'] = "An error occurred. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile - Gov D.M. Camerino</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/home.css">
    <style>
        .profile-section {
            padding: 50px 0;
            background-color: #f8f9fa;
        }
        .profile-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 30px;
        }
        .profile-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .profile-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin-bottom: 20px;
            object-fit: cover;
            border: 5px solid #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .nav-pills .nav-link.active {
            background-color: #007bff;
        }
        /* Dark mode styles */
        body.dark-mode {
            background-color: #1a1a1a;
            color: #ffffff;
        }
        
        body.dark-mode .profile-card {
            background-color: #2d2d2d;
            color: #ffffff;
        }
        
        body.dark-mode .navbar {
            background-color: #2d2d2d !important;
        }
        
        body.dark-mode .navbar-light .navbar-nav .nav-link {
            color: #ffffff;
        }
        
        body.dark-mode .logo-text {
            color: #ffffff;
        }
        
        body.dark-mode .form-control {
            background-color: #3d3d3d;
            border-color: #4d4d4d;
            color: #ffffff;
        }
        
        body.dark-mode .dropdown-menu {
            background-color: #2d2d2d;
        }
        
        body.dark-mode .dropdown-item {
            color: #ffffff;
        }
        
        body.dark-mode .dropdown-item:hover {
            background-color: #3d3d3d;
        }
        
        .badge-warning {
            background-color: #ffc107;
            color: #000;
            padding: 0.35em 0.65em;
            border-radius: 10px;
        }
        
        .custom-control-input:disabled ~ .custom-control-label {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .custom-switch {
            opacity: 0.7;
        }
        
        /* Tooltip custom style */
        .tooltip .tooltip-inner {
            background-color: #2d2d2d;
            color: #fff;
            padding: 8px 12px;
            font-size: 0.875rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white">
    <div class="container">
        <a class="navbar-brand" href="#">
            <img src="../images/logo.png" alt="Gov D.M. Camerino" class="navbar-logo">
            <span class="logo-text">Gov D.M. Camerino</span>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="home.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="site-map.php">Site Map</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="News.php">News</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="aboutus.php">About Us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contactus.php">Contact Us</a>
                </li>
                
                <?php if (isset($_SESSION['id'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" 
                           data-toggle="dropdown" aria-expanded="false">
                            <?php echo htmlspecialchars($student['firstname'] ?? 'My Account'); ?>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="student_dashboard.php">
                                <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                            </a>
                            <a class="dropdown-item active" href="student_profile.php">
                                <i class="fas fa-user mr-2"></i>Profile
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="logout.php">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </a>
                        </div>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link btn-signup" href="Student-Login.php">Log In</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<div class="profile-section">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="profile-card">
                    <div class="profile-header">
                        <img src="<?php echo $student['profile_image'] ?? '../images/default-avatar.png'; ?>" 
                             alt="Profile Picture" 
                             class="profile-avatar">
                        <h4><?php echo htmlspecialchars($student['firstname'] . ' ' . $student['lastname']); ?></h4>
                        <p class="text-muted">Student</p>
                    </div>
                    
                    <div class="nav flex-column nav-pills" role="tablist">
                        <a class="nav-link active" data-toggle="pill" href="#profile">
                            <i class="fas fa-user mr-2"></i> Profile
                        </a>
                        <a class="nav-link" data-toggle="pill" href="#settings">
                            <i class="fas fa-cog mr-2"></i> Settings
                        </a>
                        <a class="nav-link" data-toggle="pill" href="#security">
                            <i class="fas fa-shield-alt mr-2"></i> Security
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <div class="tab-content">
                    <!-- Profile Tab -->
                    <div class="tab-pane fade show active" id="profile">
                        <div class="profile-card">
                            <h5 class="mb-4">Profile Information</h5>
                            
                            <?php if (isset($_SESSION['success_message'])): ?>
                                <div class="alert alert-success">
                                    <?php 
                                        echo $_SESSION['success_message'];
                                        unset($_SESSION['success_message']);
                                    ?>
                                </div>
                            <?php endif; ?>

                            <?php if (isset($_SESSION['error_message'])): ?>
                                <div class="alert alert-danger">
                                    <?php 
                                        echo $_SESSION['error_message'];
                                        unset($_SESSION['error_message']);
                                    ?>
                                </div>
                            <?php endif; ?>

                            <form method="POST" action="student_profile.php">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>First Name</label>
                                            <input type="text" class="form-control" name="firstname" 
                                                   value="<?php echo htmlspecialchars($student['firstname']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Last Name</label>
                                            <input type="text" class="form-control" name="lastname" 
                                                   value="<?php echo htmlspecialchars($student['lastname']); ?>" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" class="form-control" name="email" 
                                           value="<?php echo htmlspecialchars($student['email']); ?>" required>
                                </div>

                                <div class="form-group">
                                    <label>Phone</label>
                                    <input type="tel" class="form-control" name="phone" 
                                           value="<?php echo htmlspecialchars($student['phone'] ?? ''); ?>">
                                </div>

                                <div class="form-group">
                                    <label>Address</label>
                                    <textarea class="form-control" name="address" rows="3"><?php echo htmlspecialchars($student['address'] ?? ''); ?></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-2"></i>Save Changes
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Settings Tab -->
                    <div class="tab-pane fade" id="settings">
                        <div class="profile-card">
                            <h5 class="mb-4">Account Settings</h5>
                            
                            <!-- Dark Mode Toggle with Coming Soon -->
                            <div class="form-group">
                                <label class="d-flex justify-content-between align-items-center">
                                    <span>
                                        <i class="fas fa-moon mr-2"></i>
                                        Dark Mode 
                                        <span class="badge badge-warning ml-2" style="font-size: 0.75em;">Coming Soon</span>
                                    </span>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="darkModeToggle" disabled 
                                               data-toggle="tooltip" data-placement="left" 
                                               title="This feature will be available soon!">
                                        <label class="custom-control-label" for="darkModeToggle"></label>
                                    </div>
                                </label>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Dark mode feature is under development and will be available soon
                                </small>
                            </div>
                            
                            <hr>
                            
                            <!-- Notification Settings (optional) -->
                            <div class="form-group">
                                <label class="d-flex justify-content-between align-items-center">
                                    <span>
                                        <i class="fas fa-bell mr-2"></i>
                                        Email Notifications
                                    </span>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="emailNotifications" checked>
                                        <label class="custom-control-label" for="emailNotifications"></label>
                                    </div>
                                </label>
                                <small class="form-text text-muted">Receive email notifications about your account</small>
                            </div>
                        </div>
                    </div>

                    <!-- Security Tab -->
                    <div class="tab-pane fade" id="security">
                        <div class="profile-card">
                            <h5 class="mb-4">Security Settings</h5>
                            <form method="POST" action="change_password.php">
                                <div class="form-group">
                                    <label>Current Password</label>
                                    <input type="password" class="form-control" name="current_password" required>
                                </div>
                                <div class="form-group">
                                    <label>New Password</label>
                                    <input type="password" class="form-control" name="new_password" required>
                                </div>
                                <div class="form-group">
                                    <label>Confirm New Password</label>
                                    <input type="password" class="form-control" name="confirm_password" required>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-key mr-2"></i>Change Password
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add this script for dropdown functionality -->
<script>
$(document).ready(function() {
    // Initialize Bootstrap dropdowns
    $('.dropdown-toggle').dropdown();
    
    // Add active class to current nav item
    const currentLocation = window.location.pathname;
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        if (link.getAttribute('href') === currentLocation.split('/').pop()) {
            link.classList.add('active');
        }
    });
});
</script>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<!-- Add this before the closing body tag -->
<script>
// Dark mode functionality
document.addEventListener('DOMContentLoaded', function() {
    const darkModeToggle = document.getElementById('darkModeToggle');
    
    // Check for saved dark mode preference
    if (localStorage.getItem('darkMode') === 'enabled') {
        document.body.classList.add('dark-mode');
        darkModeToggle.checked = true;
    }
    
    // Dark mode toggle handler
    darkModeToggle.addEventListener('change', function() {
        if (this.checked) {
            document.body.classList.add('dark-mode');
            localStorage.setItem('darkMode', 'enabled');
        } else {
            document.body.classList.remove('dark-mode');
            localStorage.setItem('darkMode', null);
        }
    });
});

// Initialize tooltips
$(function () {
    $('[data-toggle="tooltip"]').tooltip();
});
</script>

<script>
$(document).ready(function(){
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Optional: Add a click handler to show a message when clicked
    $('#darkModeToggle').parent().on('click', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Coming Soon!',
            text: 'Dark mode feature is currently under development. Stay tuned for updates!',
            icon: 'info',
            confirmButtonColor: '#007bff'
        });
    });
});
</script>

<script>
$(document).ready(function() {
    // Get tab from URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const tab = urlParams.get('tab');
    
    // If tab parameter exists, show that tab
    if (tab) {
        $('.nav-pills a[href="#' + tab + '"]').tab('show');
    }
});
</script>

</body>
</html> 