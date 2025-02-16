<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$db = new DbConnector();

// Fetch departments from database
$query = "SELECT department_id, department_name, department_code FROM departments WHERE status = 'active' ORDER BY department_name";
$result = $db->query($query);
$departments = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $departments[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Teacher - Admin Dashboard</title>
    
    <!-- External CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/dashboard-shared.css">
    <link rel="stylesheet" href="css/dashboard.css">
	<link rel="icon" href="../images/light-logo.png">
    <style>
        .card {
            width: 100%;
            max-width: 1600px;
            margin: auto;
            margin-bottom: 2rem;
            margin-left: -1%;
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .card-header {
            padding: 1rem 1.5rem;
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        .card-body {
            padding: 1.5rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .form-control {
            padding: 0.5rem 0.75rem;
        }

        .form-control:focus {
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }

        .input-group-text {
            background-color: #f8f9fa;
        }

        .btn {
            padding: 0.5rem 1rem;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .text-muted {
            color: #6c757d !important;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .card-body {
                padding: 1rem;
            }

            .row {
                margin-left: -0.5rem;
                margin-right: -0.5rem;
            }

            .col-12, .col-md-6, .col-lg-4 {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }

            .form-group {
                margin-bottom: 0.75rem;
            }
        }

        /* Custom spacing utility */
        .gap-2 {
            gap: 0.5rem;
        }

        /* Form validation styles */
        .is-invalid {
            border-color: #dc3545;
        }

        .invalid-feedback {
            display: none;
            color: #dc3545;
            font-size: 80%;
        }

        .is-invalid ~ .invalid-feedback {
            display: block;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Include Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <div class="main-content">
            <!-- Include Navigation -->
            <?php include 'includes/navigation.php'; ?>

            <!-- Main Content -->
            <div class="container-fluid">
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h4 class="mb-0">Add New Teacher</h4>
                            </div>
                            <div class="card-body">
                                <form id="addTeacherForm" action="handlers/teacher_account_handler.php" method="POST">
                                    <input type="hidden" name="action" value="create_teacher">
                                    
                                    <!-- Personal Information -->
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <h5 class="text-muted mb-3">Personal Information</h5>
                                        </div>
                                        <div class="col-12 col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label class="form-label">First Name*</label>
                                                <input type="text" class="form-control" name="firstname" required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label class="form-label">Middle Name</label>
                                                <input type="text" class="form-control" name="middlename">
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label class="form-label">Last Name*</label>
                                                <input type="text" class="form-control" name="lastname" required>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Account Information -->
                                    <div class="row g-3 mt-4">
                                        <div class="col-12">
                                            <h5 class="text-muted mb-3">Account Information</h5>
                                        </div>
                                        <div class="col-12 col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label class="form-label">Username*</label>
                                                <input type="text" class="form-control" name="username" required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label class="form-label">Email*</label>
                                                <input type="email" class="form-control" name="email" required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label class="form-label">Department*</label>
                                                <select class="form-control" name="department" required>
                                                    <option value="">Select Department</option>
                                                    <?php foreach ($departments as $dept): ?>
                                                        <option value="<?php echo $dept['department_id']; ?>">
                                                            <?php echo htmlspecialchars($dept['department_name'] . ' (' . $dept['department_code'] . ')'); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Password Section -->
                                    <div class="row g-3 mt-4">
                                        <div class="col-12">
                                            <h5 class="text-muted mb-3">Password</h5>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Password*</label>
                                                <div class="input-group">
                                                    <input type="password" class="form-control" name="password" required>
                                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                                <small class="form-text text-muted">Password must be at least 8 characters</small>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Confirm Password*</label>
                                                <div class="input-group">
                                                    <input type="password" class="form-control" name="confirm_password" required>
                                                    <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Form Actions -->
                                    <div class="row mt-4">
                                        <div class="col-12">
                                            <div class="form-group d-flex gap-2">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-save me-2"></i>Create Account
                                                </button>
                                                <a href="manage_teachers.php" class="btn btn-secondary">
                                                    <i class="fas fa-times me-2"></i>Cancel
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Add SweetAlert2 JS before your custom script -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    $(document).ready(function() {
        $('#addTeacherForm').on('submit', function(e) {
            e.preventDefault();
            
            // Basic validation
            var password = $('input[name="password"]').val();
            var confirmPassword = $('input[name="confirm_password"]').val();
            var department = $('select[name="department"]').val();
            
            // Department validation
            if (!department) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Department Required',
                    text: 'Please select a department',
                    confirmButtonColor: '#3085d6'
                });
                return false;
            }
            
            // Password length validation
            if (password.length < 8) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Password',
                    text: 'Password must be at least 8 characters long',
                    confirmButtonColor: '#3085d6'
                });
                return false;
            }
            
            // Password match validation
            if (password !== confirmPassword) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Password Mismatch',
                    text: 'Passwords do not match',
                    confirmButtonColor: '#3085d6'
                });
                return false;
            }

            // Show loading state
            Swal.fire({
                title: 'Processing...',
                html: 'Creating teacher account',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Submit form
            $.ajax({
                url: 'handlers/teacher_account_handler.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Teacher account created successfully',
                            confirmButtonColor: '#3085d6'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'manage_teachers.php';
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message || 'Failed to create teacher account',
                            confirmButtonColor: '#3085d6'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Failed to process request. Please try again.',
                        confirmButtonColor: '#3085d6'
                    });
                }
            });
        });

        // Cancel button confirmation
        $('.btn-secondary').on('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "You will lose all entered data!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, cancel',
                cancelButtonText: 'No, keep editing'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'manage_teachers.php';
                }
            });
        });

        // Optional: Add Select2 for better dropdown experience
        if ($.fn.select2) {
            $('select[name="department"]').select2({
                placeholder: "Select Department",
                allowClear: true,
                theme: "bootstrap4"
            });
        }

        // Form field validation on input
        $('input[name="password"], input[name="confirm_password"]').on('input', function() {
            var password = $('input[name="password"]').val();
            var confirmPassword = $('input[name="confirm_password"]').val();
            
            if (password.length > 0 && password.length < 8) {
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
            
            if (confirmPassword.length > 0 && password !== confirmPassword) {
                $('input[name="confirm_password"]').addClass('is-invalid');
            } else {
                $('input[name="confirm_password"]').removeClass('is-invalid');
            }
        });

        // Password toggle
        $('#togglePassword').click(function() {
            const password = $('input[name="password"]');
            const icon = $(this).find('i');
            
            if (password.attr('type') === 'password') {
                password.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                password.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        // Confirm password toggle
        $('#toggleConfirmPassword').click(function() {
            const password = $('input[name="confirm_password"]');
            const icon = $(this).find('i');
            
            if (password.attr('type') === 'password') {
                password.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                password.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });
    });
    </script>

    <!-- Add Select2 CSS and JS (Optional but recommended for better UX) -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</body>
</html>