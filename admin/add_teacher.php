<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$db = new DbConnector();
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
                            <div class="card-header">
                                <h4>Add New Teacher</h4>
                            </div>
                            <div class="card-body">
                                <form id="addTeacherForm" action="handlers/teacher_account_handler.php" method="POST">
                                    <input type="hidden" name="action" value="create_teacher">
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Username*</label>
                                                <input type="text" class="form-control" name="username" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Email*</label>
                                                <input type="email" class="form-control" name="email" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Department*</label>
                                                <input type="text" class="form-control" name="department" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>First Name*</label>
                                                <input type="text" class="form-control" name="firstname" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Middle Name</label>
                                                <input type="text" class="form-control" name="middlename">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Last Name*</label>
                                                <input type="text" class="form-control" name="lastname" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Password*</label>
                                                <input type="password" class="form-control" name="password" required>
                                                <small class="form-text text-muted">Password must be at least 8 characters</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Confirm Password*</label>
                                                <input type="password" class="form-control" name="confirm_password" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">Create Account</button>
                                        <a href="manage_teachers.php" class="btn btn-secondary">Cancel</a>
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

    <script>
    $(document).ready(function() {
        $('#addTeacherForm').on('submit', function(e) {
            e.preventDefault();
            
            // Basic validation
            var password = $('input[name="password"]').val();
            var confirmPassword = $('input[name="confirm_password"]').val();
            
            if (password.length < 8) {
                alert('Password must be at least 8 characters long');
                return false;
            }
            
            if (password !== confirmPassword) {
                alert('Passwords do not match');
                return false;
            }
            
            // Submit form
            $.ajax({
                url: 'handlers/teacher_account_handler.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        alert('Teacher account created successfully');
                        window.location.href = 'manage_teachers.php';
                    } else {
                        alert(response.message || 'Failed to create teacher account');
                    }
                },
                error: function() {
                    alert('Failed to process request');
                }
            });
        });
    });
    </script>
</body>
</html>