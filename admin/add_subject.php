<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$db = new DbConnector();
$admin_id = $_SESSION['admin_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Subject - Admin Dashboard</title>
    
    <!-- External CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href="css/dashboard-shared.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4 class="mb-0">Add New Subject</h4>
                                <a href="manage_subjects.php" class="btn btn-secondary">Back to Subjects</a>
                            </div>
                            <div class="card-body">
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

                                <form id="addSubjectForm" method="POST">
                                    <input type="hidden" name="action" value="add_subject">
                                    
                                    <div class="form-group">
                                        <label>Subject Code*</label>
                                        <input type="text" class="form-control" name="subject_code" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Subject Title*</label>
                                        <input type="text" class="form-control" name="subject_title" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Category*</label>
                                        <select class="form-control" name="category" required>
                                            <option value="">Select Category</option>
                                            <option value="Core">Core Subject</option>
                                            <option value="Major">Major Subject</option>
                                            <option value="Minor">Minor Subject</option>
                                            <option value="Elective">Elective</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Grade Level* (Hold Ctrl/Cmd to select multiple)</label>
                                        <select class="form-control" name="grade_level[]" multiple required>
                                            <option value="7">Grade 7</option>
                                            <option value="8">Grade 8</option>
                                            <option value="9">Grade 9</option>
                                            <option value="10">Grade 10</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Description</label>
                                        <textarea class="form-control" name="description" rows="3"></textarea>
                                    </div>

                                    <button type="submit" class="btn btn-primary">Save Subject</button>
                                    <a href="manage_subjects.php" class="btn btn-secondary">Cancel</a>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        select[multiple] {
            min-height: 120px;
            padding: 8px;
        }
        
        /* Optional: Style for selected options */
        select[multiple] option:checked {
            background: #007bff linear-gradient(0deg, #007bff 0%, #007bff 100%);
            color: #fff;
        }
    </style>

    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('select[name="grade_level[]"]').select2({
                placeholder: "Select Grade Levels",
                allowClear: true,
                width: '100%'
            });

            $('#addSubjectForm').on('submit', function(e) {
                e.preventDefault();
                
                // Validate grade levels
                var selectedGrades = $('select[name="grade_level[]"]').val();
                if (!selectedGrades || selectedGrades.length === 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please select at least one grade level'
                    });
                    return false;
                }

                // Show loading state
                Swal.fire({
                    title: 'Saving...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Get form data
                var formData = new FormData(this);

                $.ajax({
                    url: 'handlers/add_subject_handler.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.close();
                        
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                showConfirmButton: true
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = 'manage_subjects.php';
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Failed to add subject'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', xhr.responseText);
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Server Error',
                            text: 'Failed to connect to server. Please check the console for details.'
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
