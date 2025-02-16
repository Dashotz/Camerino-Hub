<?php
session_start();
require_once('../db/dbConnector.php');

// Check if teacher is logged in
if (!isset($_SESSION['teacher_id'])) {
    header('Location: Teacher-Login.php');
    exit();
}

// Initialize database connection
$db = new DbConnector();

// Get teacher data
$teacher_id = $_SESSION['teacher_id'];
$stmt = $db->prepare("SELECT t.*, d.department_name FROM teacher t 
                     LEFT JOIN departments d ON t.department_id = d.department_id 
                     WHERE t.teacher_id = ?");
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$teacher = $result->fetch_assoc();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Sanitize and validate input
        $firstname = htmlspecialchars(trim($_POST['firstname']));
        $middlename = htmlspecialchars(trim($_POST['middlename']));
        $lastname = htmlspecialchars(trim($_POST['lastname']));
        $email = htmlspecialchars(trim($_POST['email']));
        
        // Handle profile image upload
        $profile_image_path = $teacher['profile_image']; // Keep existing image by default
        
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = "../uploads/teachers/profile/";
            
            // Create directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');
            
            if (in_array($file_extension, $allowed_extensions)) {
                // Delete old profile picture if it exists
                if (!empty($teacher['profile_image']) && 
                    file_exists($teacher['profile_image']) && 
                    strpos($teacher['profile_image'], 'default-avatar.png') === false) {
                    unlink($teacher['profile_image']);
                }
                
                $new_filename = "teacher_" . $teacher_id . "_" . time() . "." . $file_extension;
                $upload_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_path)) {
                    $profile_image_path = $upload_path;
                    
                    // Update the profile image in database
                    $updateImageStmt = $db->prepare("UPDATE teacher SET profile_image = ? WHERE teacher_id = ?");
                    $updateImageStmt->bind_param("si", $profile_image_path, $teacher_id);
                    $updateImageStmt->execute();
                }
            }
        }

        // Update profile
        $updateStmt = $db->prepare("UPDATE teacher SET 
            firstname = ?, 
            middlename = ?,
            lastname = ?, 
            email = ?,
            profile_image = ?
            WHERE teacher_id = ?");
            
        $updateStmt->bind_param("sssssi", 
            $firstname,
            $middlename, 
            $lastname, 
            $email,
            $profile_image_path,
            $teacher_id
        );
        
        if ($updateStmt->execute()) {
            $_SESSION['success_message'] = "Profile updated successfully!";
        } else {
            $_SESSION['error_message'] = "Failed to update profile.";
        }
        
        header('Location: teacher_profile.php');
        exit();
    } catch (Exception $e) {
        $_SESSION['error_message'] = "An error occurred. Please try again.";
    }
}

// Add this at the top of the file
if (isset($_SESSION['require_password_change']) && $_SESSION['require_password_change']) {
    echo "<script>
        Swal.fire({
            title: 'Password Change Required',
            text: 'You are using a temporary password. Please change your password now for security purposes.',
            icon: 'warning',
            confirmButtonText: 'OK',
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('password-section').scrollIntoView();
            }
        });
    </script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Profile - CamerinoHub</title>
    
    <!-- External CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Product+Sans:wght@400;700&display=swap" rel="stylesheet">
    <link rel="icon" href="../images/light-logo.png">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/dashboard-shared.css">
    
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
        
        /* Profile Form Layout */
        .profile-form {
            max-width: 600px;
            width: 100%;
            margin: 0 auto;
        }
        
        .form-fields {
            max-width: 500px;
            margin: 0 auto;
        }
        
        .profile-image-container {
            width: 150px;
            height: 150px;
            margin: 0 auto;
            position: relative;
            display: inline-block;
            margin-bottom: 40px;
        }
        
        .profile-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            display: block;
        }
        
        /* Form Group Styles */
        .form-group label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 0.5rem;
        }
        
        .form-control {
            border: 1px solid #ced4da;
            border-radius: 5px;
            padding: 0.5rem 0.75rem;
        }
        
        .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }
        
        /* Change Photo Button */
        .change-photo-btn {
            position: absolute;
            bottom: -30px;
            left: 50%;
            transform: translateX(-50%);
            background: #fff;
            border: 1px solid #007bff;
            color: #007bff;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            white-space: nowrap;
            z-index: 2;
            text-decoration: none;
        }
        
        .change-photo-btn:hover {
            background: #007bff;
            color: #fff;
            text-decoration: none;
        }
        
        /* Button Styles */
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            padding: 0.5rem 1rem;
            font-weight: 500;
        }
        
        .btn-primary:hover {
            background-color: #0069d9;
            border-color: #0062cc;
        }
        
        /* Tooltip custom style */
        .tooltip .tooltip-inner {
            background-color: #2d2d2d;
            color: #fff;
            padding: 8px 12px;
            font-size: 0.875rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        /* Responsive Styles */
        @media (max-width: 768px) {
            .profile-form {
                max-width: 100%;
                padding: 0 15px;
            }
            
            .form-fields {
                max-width: 100%;
            }
            
            .profile-card {
                padding: 20px;
            }
            
            .profile-image-container {
                width: 120px;
                height: 120px;
                margin-bottom: 35px;
            }
            
            .profile-avatar {
                width: 120px;
                height: 120px;
            }
            
            .change-photo-btn {
                font-size: 0.75rem;
                padding: 3px 10px;
                bottom: -25px;
            }
        }
        
        @media (max-width: 576px) {
            .profile-image-container {
                width: 100px;
                height: 100px;
                margin-bottom: 30px;
            }
            
            .profile-avatar {
                width: 100px;
                height: 100px;
            }
            
            .change-photo-btn {
                font-size: 0.7rem;
                padding: 2px 8px;
                bottom: -22px;
            }
        }
        
        /* Navigation Styles */
        .navbar {
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .navbar-logo {
            height: 40px;
            width: auto;
        }
        
        .logo-text {
            font-size: 1.2rem;
            color: #333;
            font-weight: 500;
        }
        
        .navbar-nav .nav-link {
            color: #333;
            font-weight: 500;
            padding: 0.5rem 1rem;
        }
        
        .navbar-nav .nav-link:hover {
            color: #007bff;
        }
        
        .dropdown-menu {
            right: 0;
            left: auto;
            margin-top: 0.5rem;
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        
        .dropdown-item {
            padding: 0.5rem 1.5rem;
            color: #333;
        }
        
        .dropdown-item:hover {
            background-color: #f8f9fa;
            color: #007bff;
        }
        
        .dropdown-divider {
            margin: 0.5rem 0;
            border-top: 1px solid #eee;
        }
        
        .btn-signup {
            background-color: #007bff;
            color: white !important;
            border-radius: 20px;
            padding: 0.5rem 1.5rem !important;
            transition: all 0.3s ease;
        }
        
        .btn-signup:hover {
            background-color: #0056b3;
            transform: translateY(-1px);
        }
        
        @media (max-width: 991px) {
            .navbar-collapse {
                background: white;
                padding: 1rem;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                margin-top: 1rem;
            }
            
            .btn-signup {
                display: inline-block;
                margin-top: 0.5rem;
            }
        }
        
        /* Profile Section and Card Styles */
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
        
        /* Profile Image and Button Styles */
        .profile-form {
            max-width: 600px;
            width: 100%;
            margin: 0 auto;
        }
        
        .form-group.text-center.mb-4 {
            position: relative;
            margin-bottom: 2rem !important;
        }
        
        .profile-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin-bottom: 20px;
            object-fit: cover;
            border: 5px solid #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            display: block;
            margin: 0 auto;
        }
        
        /* Change Photo Button */
        .btn-outline-primary.btn-sm {
            position: relative;
            display: inline-block;
            padding: 0.4rem 1rem;
            font-size: 0.875rem;
            border-radius: 20px;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        .btn-outline-primary.btn-sm:hover {
            background-color: #007bff;
            color: white;
            transform: translateY(-1px);
        }
        
        /* Form Fields Layout */
        .form-fields {
            max-width: 500px;
            margin: 0 auto;
        }
        
        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .profile-avatar {
                width: 120px;
                height: 120px;
            }
            
            .btn-outline-primary.btn-sm {
                font-size: 0.8rem;
                padding: 0.3rem 0.8rem;
            }
        }
        
        @media (max-width: 576px) {
            .profile-avatar {
                width: 100px;
                height: 100px;
            }
        }
        
        /* For mobile view */
        @media (max-width: 991.98px) {
            .dropdown-menu {
                right: auto;
                left: 0;
                width: 100%;
                margin-top: 0.5rem;
            }
        }

        .settings-section {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        .settings-title {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e9ecef;
        }

        .custom-switch {
            padding-left: 2.75rem;
        }

        .custom-control-label {
            font-weight: 500;
            color: #2c3e50;
        }

        .form-text {
            margin-left: 2.75rem;
            font-size: 0.85rem;
        }

        .dark-mode {
            background-color: #1a1a1a;
            color: #ffffff;
        }

        .dark-mode .settings-section {
            background: #2d2d2d;
        }

        .dark-mode .settings-title {
            color: #ffffff;
            border-bottom-color: #404040;
        }

        .dark-mode .custom-control-label {
            color: #ffffff;
        }

        .dark-mode .form-control {
            background-color: #333333;
            border-color: #404040;
            color: #ffffff;
        }

        /* SweetAlert2 Custom Styles */
        .saving-spinner {
            margin: 20px auto;
            font-size: 2rem;
            color: #3498db;
        }

        .success-animation {
            font-size: 3rem;
            color: #28a745;
            margin: 20px auto;
            animation: bounceIn 0.5s;
        }

        .error-animation {
            font-size: 3rem;
            color: #dc3545;
            margin: 20px auto;
            animation: shake 0.5s;
        }

        /* Animations */
        @keyframes bounceIn {
            0% { transform: scale(0.3); opacity: 0; }
            50% { transform: scale(1.1); }
            70% { transform: scale(0.9); }
            100% { transform: scale(1); opacity: 1; }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }

        /* SweetAlert2 Theme Overrides */
        .swal2-popup {
            border-radius: 15px;
            padding: 2rem;
        }

        .swal2-title {
            font-size: 1.5rem !important;
            margin-bottom: 1rem !important;
        }

        .swal2-html-container {
            margin: 1rem 0 !important;
        }

        .swal2-confirm {
            padding: 0.5rem 2rem !important;
            border-radius: 50px !important;
        }

        /* Animation Classes */
        .animated {
            animation-duration: 0.5s;
            animation-fill-mode: both;
        }

        .fadeInUp {
            animation-name: fadeInUp;
        }

        .fadeInDown {
            animation-name: fadeInDown;
        }

        .shake {
            animation-name: shake;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translate3d(0, 20px, 0);
            }
            to {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translate3d(0, -20px, 0);
            }
            to {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }

        .backup-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .backup-history {
            max-height: 300px;
            overflow-y: auto;
        }

        .table-responsive {
            margin-top: 20px;
        }

        .table th {
            background-color: #f8f9fa;
            border-top: none;
        }

        .dark-mode .backup-section {
            background: #2d2d2d;
        }

        .dark-mode .table th {
            background-color: #2d2d2d;
            color: #fff;
        }

        .swal2-popup {
            font-size: 0.9rem;
        }

        .swal2-title {
            font-size: 1.4rem;
        }

        .swal2-content {
            font-size: 1rem;
        }

        .swal2-icon {
            margin: 1.5rem auto 0.5rem;
        }

        .swal2-actions {
            margin: 1.5rem auto 0;
        }

        .restore-progress {
            width: 100%;
            height: 4px;
            margin-top: 1rem;
            background: #f0f0f0;
            border-radius: 2px;
            overflow: hidden;
        }

        .restore-progress-bar {
            height: 100%;
            background: #4CAF50;
            width: 0;
            transition: width 0.3s ease;
        }

        .pagination {
            margin-bottom: 0;
        }

        .page-link {
            padding: 0.5rem 0.75rem;
            margin-left: -1px;
            line-height: 1.25;
            color: #007bff;
            background-color: #fff;
            border: 1px solid #dee2e6;
        }

        .page-item.active .page-link {
            z-index: 3;
            color: #fff;
            background-color: #007bff;
            border-color: #007bff;
        }

        .page-item.disabled .page-link {
            color: #6c757d;
            pointer-events: none;
            cursor: not-allowed;
            background-color: #fff;
            border-color: #dee2e6;
        }

        .dataTables_info {
            padding-top: 0.85em;
        }

        .pagination {
            margin-bottom: 0;
        }

        .pagination .page-link {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        .btn-group-sm > .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        .table td {
            vertical-align: middle;
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white">
        <div class="container">
            <a class="navbar-brand" href="teacher_dashboard.php">
                <img src="../images/logo.png" alt="Gov D.M. Camerino" class="navbar-logo">
                <span class="logo-text">Gov D.M. Camerino</span>
            </a>
            <button class="navbar-toggler" type="button" 
                    data-bs-toggle="collapse" 
                    data-bs-target="#navbarNav" 
                    aria-controls="navbarNav" 
                    aria-expanded="false" 
                    aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if (isset($_SESSION['teacher_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" 
                               id="navbarDropdown" 
                               role="button"
                               data-bs-toggle="dropdown" 
                               aria-expanded="false">
                                <?php echo htmlspecialchars($teacher['firstname'] ?? 'My Account'); ?>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="teacher_dashboard.php">Dashboard</a></li>
                                <li><a class="dropdown-item" href="teacher_profile.php">Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link btn-signup" href="Teacher-Login.php">Log In</a>
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
                            <img src="<?php echo $teacher['profile_image'] ?? '../images/default-avatar.png'; ?>" 
                                 alt="Profile Picture" 
                                 class="profile-avatar">
                            <h4><?php echo htmlspecialchars($teacher['firstname'] . ' ' . $teacher['lastname']); ?></h4>
                            <p class="text-muted"><?php echo htmlspecialchars($teacher['department_name'] ?? 'No Department'); ?></p>
                        </div>
                        <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                            <button class="nav-link active" id="v-pills-profile-tab" data-bs-toggle="pill" data-bs-target="#profile" type="button" role="tab">
                                <i class="fas fa-user me-2"></i> Profile
                            </button>
                            <button class="nav-link" id="v-pills-settings-tab" data-bs-toggle="pill" data-bs-target="#settings" type="button" role="tab">
                                <i class="fas fa-cog me-2"></i> Settings
                            </button>
                            <button class="nav-link" id="v-pills-security-tab" data-bs-toggle="pill" data-bs-target="#security" type="button" role="tab">
                                <i class="fas fa-shield-alt me-2"></i> Security
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-8">
                    <div class="tab-content" id="v-pills-tabContent">
                        <!-- Profile Tab -->
                        <div class="tab-pane fade show active" id="profile" role="tabpanel">
                            <div class="profile-card">
                                <div class="d-flex justify-content-center">
                                    <div class="profile-form">
                                        <h5 class="text-center mb-4">Profile Information</h5>
                                        
                                        <?php if (isset($_SESSION['success_message'])): ?>
                                            <div class="alert alert-success">
                                                <?php 
                                                    echo $_SESSION['success_message'];
                                                    unset($_SESSION['success_message']);
                                                ?>
                                            </div>
                                        <?php endif; ?>

                                        <form method="POST" action="teacher_profile.php" enctype="multipart/form-data" class="text-center">
                                            <!-- Profile Image -->
                                            <div class="profile-image-container">
                                                <img src="<?php 
                                                    if (!empty($teacher['profile_image']) && file_exists($teacher['profile_image'])) {
                                                        echo $teacher['profile_image'];
                                                    } else {
                                                        echo '../images/default-avatar.png';
                                                    }
                                                ?>" 
                                                alt="Profile Picture" 
                                                class="profile-avatar"
                                                id="preview-image">
                                                <label for="profile_image" class="change-photo-btn">
                                                    <i class="fas fa-camera"></i> Change Photo
                                                </label>
                                                <input type="file" 
                                                       id="profile_image" 
                                                       name="profile_image" 
                                                       class="d-none" 
                                                       accept="image/*" 
                                                       onchange="previewImage(this)">
                                            </div>

                                            <!-- Personal Information -->
                                            <div class="form-fields">
                                                <div class="row mb-3">
                                                    <div class="col-md-4">
                                                        <div class="form-group text-left">
                                                            <label>First Name</label>
                                                            <input type="text" class="form-control" name="firstname" 
                                                                   value="<?php echo htmlspecialchars($teacher['firstname']); ?>" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group text-left">
                                                            <label>Middle Name</label>
                                                            <input type="text" class="form-control" name="middlename" 
                                                                   value="<?php echo htmlspecialchars($teacher['middlename']); ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group text-left">
                                                            <label>Last Name</label>
                                                            <input type="text" class="form-control" name="lastname" 
                                                                   value="<?php echo htmlspecialchars($teacher['lastname']); ?>" required>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-4 text-left">
                                                    <label>Email</label>
                                                    <input type="email" class="form-control" name="email" 
                                                           value="<?php echo htmlspecialchars($teacher['email']); ?>" required>
                                                </div>

                                                <button type="submit" class="btn btn-primary d-block w-100">
                                                    <i class="fas fa-save mr-2"></i>Save Changes
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Settings Tab -->
                        <div class="tab-pane fade" id="settings" role="tabpanel">
                            <div class="profile-card">
                                <h5 class="mb-4">Database Backup and Recovery</h5>
                                
                                <div class="settings-section mb-4">
                                    <h6 class="settings-title">Database Management</h6>
                                    
                                    <!-- Backup Database -->
                                    <div class="mb-4">
                                        <h6 class="mb-3">Backup Database</h6>
                                        <p class="text-muted mb-3">Select the type of data to backup</p>
                                        <form id="backupForm" action="handlers/backup_database.php" method="POST">
                                            <div class="mb-3">
                                                <select class="form-select" name="backup_type" required>
                                                    <option value="students">My Students Data</option>
                                                    <option value="activities">My Activities & Quizzes</option>
                                                    <option value="classes">My Classes Data</option>
                                                    <option value="full">Full Database Backup</option>
                                                </select>
                                            </div>
                                            <button type="submit" class="btn btn-primary" name="backup_db">
                                                <i class="fas fa-download me-2"></i>Create Backup
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Upload Backup -->
                                    <div class="mb-4">
                                        <h6 class="mb-3">Restore Database</h6>
                                        <p class="text-muted mb-3">Restore database from a previous backup file</p>
                                        <form action="handlers/restore_database.php" method="POST" enctype="multipart/form-data" id="restoreForm">
                                            <div class="mb-3">
                                                <input type="file" class="form-control" name="backup_file" accept=".sql" required>
                                                <small class="text-muted">Only .sql files are allowed (max 10MB)</small>
                                            </div>
                                            <button type="submit" class="btn btn-warning" name="restore_db">
                                                <i class="fas fa-upload me-2"></i>Restore Backup
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Backup History -->
                                    <div>
                                        <h6 class="mb-3">Backup History</h6>
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Size</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="backup-history">
                                                    <!-- Backup history will be loaded dynamically -->
                                                </tbody>
                                            </table>
                                            <nav aria-label="Backup history navigation" class="mt-3">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="text-muted small">
                                                        Showing <span id="showing-start">0</span> to <span id="showing-end">0</span> of <span id="total-entries">0</span> entries
                                                    </div>
                                                    <ul class="pagination pagination-sm mb-0">
                                                        <li class="page-item" id="prev-page">
                                                            <a class="page-link" href="#" aria-label="Previous">
                                                                <span aria-hidden="true">&laquo;</span>
                                                            </a>
                                                        </li>
                                                        <div id="page-numbers" class="d-flex"></div>
                                                        <li class="page-item" id="next-page">
                                                            <a class="page-link" href="#" aria-label="Next">
                                                                <span aria-hidden="true">&raquo;</span>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </nav>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Security Tab -->
                        <div class="tab-pane fade" id="security" role="tabpanel">
                            <div class="profile-card">
                                <h5 class="mb-4">Security Settings</h5>
                                
                                <form method="POST" action="update_password.php" id="passwordForm">
                                    <div class="form-group">
                                        <label>Current Password</label>
                                        <input type="password" class="form-control" name="current_password" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>New Password</label>
                                        <input type="password" class="form-control" name="new_password" 
                                               pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" 
                                               title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters"
                                               required>
                                        <small class="form-text text-muted">
                                            Password must contain at least 8 characters, including uppercase, lowercase, and numbers
                                        </small>
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

                        <!-- Reports Tab -->
                        <div class="tab-pane fade" id="reports" role="tabpanel">
                            <div class="profile-card">
                                <h5 class="mb-4">Generate Reports</h5>
                                
                                <div class="settings-section mb-4">
                                    <h6 class="settings-title">Report Options</h6>
                                    
                                    <!-- Report Generation Form -->
                                    <form action="handlers/generate_report.php" method="POST">
                                        <div class="mb-3">
                                            <label class="form-label">Report Type</label>
                                            <select class="form-select" name="report_type" required>
                                                <option value="student_performance">Student Performance</option>
                                                <option value="class_overview">Class Overview</option>
                                                <option value="activity_summary">Activity Summary</option>
                                                <option value="attendance_report">Attendance Report</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Section</label>
                                            <select class="form-select" name="section_id" required>
                                                <?php
                                                $sections_query = "SELECT DISTINCT s.section_id, s.section_name 
                                                         FROM sections s 
                                                         JOIN section_subjects ss ON s.section_id = ss.section_id 
                                                         WHERE ss.teacher_id = ?";
                                                $stmt = $db->prepare($sections_query);
                                                $stmt->bind_param("i", $_SESSION['teacher_id']);
                                                $stmt->execute();
                                                $result = $stmt->get_result();
                                                while ($section = $result->fetch_assoc()) {
                                                    echo "<option value='{$section['section_id']}'>{$section['section_name']}</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Date Range</label>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <input type="date" class="form-control" name="start_date" required>
                                                    <small class="text-muted">Start Date</small>
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="date" class="form-control" name="end_date" required>
                                                    <small class="text-muted">End Date</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Report Format</label>
                                            <select class="form-select" name="format" required>
                                                <option value="pdf">PDF</option>
                                                <option value="excel">Excel</option>
                                                <option value="csv">CSV</option>
                                            </select>
                                        </div>

                                        <button type="submit" class="btn btn-primary" name="generate_report">
                                            <i class="fas fa-file-download me-2"></i>Generate Report
                                        </button>
                                    </form>

                                    <!-- Report History -->
                                    <div class="mt-4">
                                        <h6 class="mb-3">Recent Reports</h6>
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Type</th>
                                                        <th>Format</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="report-history">
                                                    <!-- Report history will be loaded dynamically -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>
    
    <script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('preview-image');
                preview.style.opacity = '0';
                preview.src = e.target.result;
                setTimeout(() => {
                    preview.style.opacity = '1';
                }, 50);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Add smooth transition for image
    document.getElementById('preview-image').style.transition = 'opacity 0.3s ease';

    // Handle password form submission
    $(document).ready(function() {
        // Initialize Bootstrap tabs
        var triggerTabList = [].slice.call(document.querySelectorAll('[data-bs-toggle="pill"]'));
        triggerTabList.forEach(function(triggerEl) {
            new bootstrap.Tab(triggerEl);
        });

        // Handle password form submission
        $('#passwordForm').on('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const newPassword = formData.get('new_password');
            const confirmPassword = formData.get('confirm_password');
            
            // Check if passwords match
            if (newPassword !== confirmPassword) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Passwords do not match',
                    icon: 'error',
                    confirmButtonColor: '#007bff'
                });
                return;
            }
            
            // Hash passwords using MD5
            const hashedCurrentPassword = CryptoJS.MD5(formData.get('current_password')).toString();
            const hashedNewPassword = CryptoJS.MD5(newPassword).toString();
            
            // Show loading state
            Swal.fire({
                title: 'Processing...',
                text: 'Please wait',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            $.ajax({
                url: 'update_password.php',
                type: 'POST',
                data: {
                    current_password: hashedCurrentPassword,
                    new_password: hashedNewPassword
                },
                success: function(response) {
                    try {
                        const data = typeof response === 'string' ? JSON.parse(response) : response;
                        if (data.success) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Password updated successfully',
                                icon: 'success',
                                confirmButtonColor: '#007bff'
                            });
                            $('#passwordForm')[0].reset();
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: data.message || 'Failed to update password',
                                icon: 'error',
                                confirmButtonColor: '#007bff'
                            });
                        }
                    } catch (error) {
                        console.error('Error:', error, 'Response:', response);
                        Swal.fire({
                            title: 'Error!',
                            text: 'An unexpected error occurred',
                            icon: 'error',
                            confirmButtonColor: '#007bff'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to connect to the server',
                        icon: 'error',
                        confirmButtonColor: '#007bff'
                    });
                }
            });
        });

        // Function to load backup history
        function loadBackupHistory() {
            $.ajax({
                url: 'handlers/get_backup_history.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success && Array.isArray(response.data)) {
                        backupData = response.data; // Store backup data array
                        displayCurrentPage(); // Display current page of data
                    } else {
                        backupData = []; // Set empty array if no data
                        $('#backup-history').html(
                            '<tr><td colspan="3" class="text-center">No backup history found</td></tr>'
                        );
                        updatePaginationInfo();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading backup history:', error);
                    backupData = []; // Set empty array on error
                    $('#backup-history').html(
                        '<tr><td colspan="3" class="text-center text-danger">Failed to load backup history</td></tr>'
                    );
                    updatePaginationInfo();
                }
            });
        }

        // Delete backup function
        window.deleteBackup = function(filename) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This backup will be permanently deleted!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'handlers/delete_backup.php',
                        type: 'POST',
                        data: { filename: filename },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire(
                                    'Deleted!',
                                    'Backup has been deleted.',
                                    'success'
                                );
                                loadBackupHistory();
                            } else {
                                throw new Error(response.message || 'Failed to delete backup');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Delete error:', error);
                            Swal.fire(
                                'Error!',
                                'Failed to delete backup: ' + error,
                                'error'
                            );
                        }
                    });
                }
            });
        };

        // Make sure loadBackupHistory is called when the settings tab is shown
        $('button[data-bs-target="#settings"]').on('click', function() {
            loadBackupHistory();
        });

        // Initial load if settings tab is active
        if ($('#settings').hasClass('active')) {
            loadBackupHistory();
        }

        // Handle backup form submission
        $('form[action="handlers/backup_database.php"]').on('submit', function(e) {
            e.preventDefault(); // Prevent form from submitting normally
            
            const formData = new FormData(this);
            formData.append('backup_db', '1');
            
            // Show loading modal
            Swal.fire({
                title: 'Creating Backup...',
                text: 'Please wait while we process your backup',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Make AJAX request
            $.ajax({
                url: 'handlers/backup_database.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    try {
                        const result = typeof response === 'string' ? JSON.parse(response) : response;
                        if (result.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Backup Created!',
                                text: `Backup file ${result.file} has been created successfully.`,
                                showDenyButton: true,
                                confirmButtonText: 'Download Now',
                                denyButtonText: 'Close',
                                confirmButtonColor: '#28a745',
                                denyButtonColor: '#6c757d'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = `handlers/download_backup.php?file=${encodeURIComponent(response.file)}`;
                                }
                            });
                            
                            // Refresh the backup history
                            loadBackupHistory();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Backup Failed',
                                text: result.message || 'An error occurred while creating the backup',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    } catch (error) {
                        console.error('Error parsing response:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An unexpected error occurred',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to connect to the server',
                        confirmButtonColor: '#dc3545'
                    });
                }
            });
        });
    });
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
                allowEscapeKey: false,
                didOpen: () => {
                    // Ensure the settings tab is active
                    document.querySelector('a[href="#settings"]').click();
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Scroll to password section
                    const passwordSection = document.getElementById('password-section');
                    if (passwordSection) {
                        passwordSection.scrollIntoView({ behavior: 'smooth' });
                    }
                }
            });
        <?php endif; ?>
    });
    </script>
    <script>
    function loadReportHistory() {
        $.ajax({
            url: 'handlers/get_report_history.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                let html = '';
                if (Array.isArray(response) && response.length > 0) {
                    response.forEach(report => {
                        html += `
                            <tr>
                                <td>${report.date}</td>
                                <td>${report.type}</td>
                                <td>${report.format}</td>
                                <td>
                                    <a href="handlers/download_report.php?file=${encodeURIComponent(report.filename)}" 
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    html = '<tr><td colspan="4" class="text-center">No report history found</td></tr>';
                }
                
                $('#report-history').html(html);
            },
            error: function(xhr, status, error) {
                console.error('Error loading report history:', error);
                console.error('Status:', status);
                console.error('Response:', xhr.responseText);
                
                $('#report-history').html(
                    '<tr><td colspan="4" class="text-center text-danger">Failed to load report history</td></tr>'
                );
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load report history. Please try again.'
                });
            }
        });
    }
    </script>
    <script>
    // Add this to your existing script section
    $(document).ready(function() {
        // Handle restore form submission
        $('form[action="handlers/restore_database.php"]').on('submit', function(e) {
            e.preventDefault();
            
            Swal.fire({
                title: 'Are you sure?',
                text: "This will overwrite the current database. Make sure you have a backup!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, restore it!',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    const formData = new FormData(this);
                    return fetch('handlers/restore_database.php', {
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
                        if (!data.success) {
                            throw new Error(data.message || 'Restore failed');
                        }
                        return data;
                    })
                    .catch(error => {
                        Swal.showValidationMessage(
                            `Restore failed: ${error.message}`
                        );
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed && result.value.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: result.value.message,
                        showConfirmButton: true,
                        timer: 2000
                    }).then(() => {
                        // Reset form and reload backup history
                        this.reset();
                        loadBackupHistory();
                    });
                }
            });
        });
    });

    // Add this at the end of your script section
    <?php if (isset($_SESSION['alert'])): ?>
        Swal.fire({
            icon: '<?php echo $_SESSION['alert']['type']; ?>',
            title: '<?php echo $_SESSION['alert']['title']; ?>',
            text: '<?php echo $_SESSION['alert']['message']; ?>',
            showConfirmButton: true,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });
        <?php unset($_SESSION['alert']); ?>
    <?php endif; ?>
    </script>
    <script>
    // Add these variables at the start of your script
    let currentPage = 1;
    const itemsPerPage = 4; // Show 4 items per page
    let backupData = [];

    // Update the loadBackupHistory function
    function loadBackupHistory() {
        $.ajax({
            url: 'handlers/get_backup_history.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success && Array.isArray(response.data)) {
                    backupData = response.data; // Store backup data array
                    displayCurrentPage(); // Display current page of data
                } else {
                    backupData = []; // Set empty array if no data
                    $('#backup-history').html(
                        '<tr><td colspan="3" class="text-center">No backup history found</td></tr>'
                    );
                    updatePaginationInfo();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading backup history:', error);
                backupData = []; // Set empty array on error
                $('#backup-history').html(
                    '<tr><td colspan="3" class="text-center text-danger">Failed to load backup history</td></tr>'
                );
                updatePaginationInfo();
            }
        });
    }

    function displayCurrentPage() {
        if (!Array.isArray(backupData)) {
            console.error('backupData is not an array:', backupData);
            backupData = [];
            $('#backup-history').html(
                '<tr><td colspan="3" class="text-center text-danger">Invalid backup data format</td></tr>'
            );
            updatePaginationInfo();
            return;
        }

        const start = (currentPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const pageData = backupData.slice(start, end);
        
        let html = '';
        if (pageData.length > 0) {
            pageData.forEach(backup => {
                html += `
                    <tr>
                        <td>${backup.date}</td>
                        <td>${backup.size}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-primary download-backup" 
                                        data-filename="${backup.filename}" 
                                        title="Download">
                                    <i class="fas fa-download"></i>
                                </button>
                                <button class="btn btn-danger" 
                                        onclick="deleteBackup('${encodeURIComponent(backup.filename)}')"
                                        title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
        } else {
            html = '<tr><td colspan="3" class="text-center">No backup history found</td></tr>';
        }
        
        $('#backup-history').html(html);
        updatePaginationInfo();
    }

    // Add error handling to updatePaginationInfo
    function updatePaginationInfo() {
        if (!Array.isArray(backupData)) {
            backupData = [];
        }
        
        const totalItems = backupData.length;
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        const start = totalItems === 0 ? 0 : (currentPage - 1) * itemsPerPage + 1;
        const end = Math.min(currentPage * itemsPerPage, totalItems);
        
        $('#showing-start').text(start);
        $('#showing-end').text(end);
        $('#total-entries').text(totalItems);
        
        $('#prev-page').toggleClass('disabled', currentPage === 1);
        $('#next-page').toggleClass('disabled', currentPage === totalPages || totalItems === 0);
        
        const $pageNumbers = $('#page-numbers');
        $pageNumbers.empty();
        
        if (totalPages > 1) {
            for (let i = 1; i <= totalPages; i++) {
                $pageNumbers.append(`
                    <li class="page-item ${i === currentPage ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                    </li>
                `);
            }
        }
    }

    // Event handlers
    $(document).ready(function() {
        // Previous page
        $('#prev-page').on('click', function(e) {
            e.preventDefault();
            if (!$(this).hasClass('disabled') && currentPage > 1) {
                currentPage--;
                displayCurrentPage();
            }
        });

        // Next page
        $('#next-page').on('click', function(e) {
            e.preventDefault();
            const totalPages = Math.ceil(backupData.length / itemsPerPage);
            if (!$(this).hasClass('disabled') && currentPage < totalPages) {
                currentPage++;
                displayCurrentPage();
            }
        });

        // Page numbers
        $('#page-numbers').on('click', 'a', function(e) {
            e.preventDefault();
            currentPage = parseInt($(this).data('page'));
            displayCurrentPage();
        });
    });
    </script>
</body>
</html> 