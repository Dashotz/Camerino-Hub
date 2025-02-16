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
        $middlename = htmlspecialchars(trim($_POST['middlename']));
        $lastname = htmlspecialchars(trim($_POST['lastname']));
        $contact_number = htmlspecialchars(trim($_POST['contact_number']));
        $gender = htmlspecialchars(trim($_POST['gender']));
        $birthdate = $_POST['birthdate'];
        
        // Handle profile image upload
        $profile_image_path = $student['profile_image']; // Keep existing image by default
        
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = "../uploads/students/profile/";
            
            // Create directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');
            
            if (in_array($file_extension, $allowed_extensions)) {
                // Generate unique filename
                $new_filename = "student_" . $student_id . "_" . time() . "." . $file_extension;
                $upload_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_path)) {
                    // Delete old profile picture if it exists
                    if ($student['profile_image'] && file_exists($student['profile_image'])) {
                        unlink($student['profile_image']);
                    }
                    $profile_image_path = $upload_path;
                }
            }
        }

        // Update profile
        $updateStmt = $db->prepare("UPDATE student SET 
            firstname = ?, 
            middlename = ?,
            lastname = ?, 
            contact_number = ?, 
            gender = ?,
            birthdate = ?,
            profile_image = ?
            WHERE student_id = ?");
            
        $updateStmt->bind_param("sssssssi", 
            $firstname,
            $middlename, 
            $lastname, 
            $contact_number,
            $gender,
            $birthdate,
            $profile_image_path,
            $student_id
        );
        
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

if (isset($_SESSION['require_password_change']) || isset($_GET['force_change'])) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Password Change Required',
                text: 'You must change your temporary password before continuing.',
                icon: 'warning',
                allowOutsideClick: false,
                allowEscapeKey: false,
                confirmButtonText: 'Change Password Now',
                customClass: {
                    confirmButton: 'btn btn-primary'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Scroll to password change section
                    document.querySelector('#security-tab').click();
                    document.querySelector('#current-password').focus();
                }
            });
        });
    </script>";
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
	<link rel="icon" href="../images/light-logo.png">
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
        
        .feature-title {
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .contact-info {
            background: #e8f4f8;
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
        }
        
        .btn-outline-primary {
            transition: all 0.3s ease;
        }
        
        .btn-outline-primary:hover {
            transform: translateY(-2px);
        }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white">
    <div class="container">
        <a class="navbar-brand" href="student_dashboard.php">
            <img src="../images/logo.png" alt="Gov D.M. Camerino" class="navbar-logo">
            <span class="logo-text">Gov D.M. Camerino</span>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                
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

                            <form method="POST" action="student_profile.php" enctype="multipart/form-data">
                                <div class="form-group text-center mb-4">
                                    <img src="<?php echo $student['profile_image'] ?? '../images/default-avatar.png'; ?>" 
                                         alt="Profile Picture" 
                                         class="profile-avatar mb-3" 
                                         id="preview-image">
                                    <div>
                                        <label for="profile_image" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-camera mr-2"></i>Change Photo
                                        </label>
                                        <input type="file" id="profile_image" name="profile_image" 
                                               class="d-none" accept="image/*" onchange="previewImage(this);">
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>First Name</label>
                                            <input type="text" class="form-control" name="firstname" 
                                                   value="<?php echo htmlspecialchars($student['firstname']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Middle Name</label>
                                            <input type="text" class="form-control" name="middlename" 
                                                   value="<?php echo htmlspecialchars($student['middlename']); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Last Name</label>
                                            <input type="text" class="form-control" name="lastname" 
                                                   value="<?php echo htmlspecialchars($student['lastname']); ?>" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Contact Number</label>
                                            <input type="tel" class="form-control" name="contact_number" 
                                                   pattern="[0-9]{11}"
                                                   title="Please enter a valid 11-digit phone number"
                                                   value="<?php echo htmlspecialchars($student['contact_number']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Gender</label>
                                            <select class="form-control" name="gender" required>
                                                <option value="">Select Gender</option>
                                                <option value="Male" <?php echo ($student['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                                                <option value="Female" <?php echo ($student['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Birthday</label>
                                    <input type="date" class="form-control" name="birthdate" 
                                           value="<?php echo $student['birthdate']; ?>" required>
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
                            <hr>
                                
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
                            
                            <!-- Notification Settings -->
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
                            
                            <?php if (isset($_SESSION['error_message'])): ?>
                                <div class="alert alert-danger">
                                    <?php 
                                        echo $_SESSION['error_message'];
                                        unset($_SESSION['error_message']);
                                    ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (isset($_SESSION['success_message'])): ?>
                                <div class="alert alert-success">
                                    <?php 
                                        echo $_SESSION['success_message'];
                                        unset($_SESSION['success_message']);
                                    ?>
                                </div>
                            <?php endif; ?>

                            <form method="POST" action="change_password.php">
                                <div class="form-group">
                                    <label>Current Password</label>
                                    <input type="password" class="form-control" name="current_password" required>
                                </div>
                                <div class="form-group">
                                    <label>New Password</label>
                                    <input type="password" class="form-control" name="new_password" 
                                           pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" 
                                           title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" required>
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

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-image').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if (isset($_SESSION['require_password_change']) && $_SESSION['require_password_change']): ?>
        Swal.fire({
            title: 'Password Change Required',
            text: 'You are using a temporary password. Please change your password now for security purposes.',
            icon: 'warning',
            confirmButtonText: 'OK',
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then((result) => {
            if (result.isConfirmed) {
                // Show security tab
                $('.nav-pills a[href="#security"]').tab('show');
                // Scroll to password section
                document.querySelector('.profile-card').scrollIntoView({ behavior: 'smooth' });
            }
        });
    <?php endif; ?>
});
</script>

</body>
</html> 