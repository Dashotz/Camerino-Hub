<?php
session_start();
require_once('../db/dbConnector.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$db = new DbConnector();

// Get admin info
$admin_id = $_SESSION['admin_id'];
$admin_query = "SELECT * FROM admin WHERE admin_id = ?";
$stmt = $db->prepare($admin_query);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();

// Handle form submissions
$message = '';
$error = '';

// Function to backup database
function backupDatabase($db) {
    $tables = array();
    $result = $db->query("SHOW TABLES");
    while ($row = $result->fetch_row()) {
        $tables[] = $row[0];
    }

    $backup = "";
    
    // Add SQL header and database selection
    $backup .= "-- PHP MySQL Backup\n";
    $backup .= "-- Generated: " . date('Y-m-d H:i:s') . "\n\n";
    $backup .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
    $backup .= "SET time_zone = \"+00:00\";\n\n";
    
    // Process each table
    foreach ($tables as $table) {
        $result = $db->query("SELECT * FROM $table");
        $numFields = $result->field_count;
        
        $backup .= "-- Table structure for table `$table`\n";
        
        $backup .= "DROP TABLE IF EXISTS `$table`;\n";
        $row2 = $db->query("SHOW CREATE TABLE $table")->fetch_row();
        $backup .= $row2[1] . ";\n\n";
        
        // Add table data
        if ($result->num_rows > 0) {
            $backup .= "-- Dumping data for table `$table`\n";
            while ($row = $result->fetch_row()) {
                $backup .= "INSERT INTO `$table` VALUES(";
                for ($j=0; $j<$numFields; $j++) {
                    if (isset($row[$j])) {
                        $row[$j] = addslashes($row[$j]);
                        $row[$j] = str_replace("\n","\\n",$row[$j]);
                        $backup .= '"'.$row[$j].'"' ;
                    } else {
                        $backup .= 'NULL';
                    }
                    if ($j<($numFields-1)) {
                        $backup .= ',';
                    }
                }
                $backup .= ");\n";
            }
        }
        $backup .= "\n\n";
    }

    // Generate backup file
    $date = date("Y-m-d-H-i-s");
    $backup_path = "../backups/";
    
    if (!file_exists($backup_path)) {
        mkdir($backup_path, 0777, true);
    }
    
    $backup_file = $backup_path . "backup_" . $date . ".sql";
    file_put_contents($backup_file, $backup);
    
    return basename($backup_file);
}

// Function to restore database
function restoreDatabase($db, $filename) {
    $backup_path = "../backups/";
    $sql = file_get_contents($backup_path . $filename);
    
    // Split SQL by semicolon
    $queries = explode(';', $sql);
    
    try {
        foreach ($queries as $query) {
            $query = trim($query);
            if (!empty($query)) {
                $db->query($query);
                if ($db->error) {
                    throw new Exception($db->error);
                }
            }
        }
        return true;
    } catch (Exception $e) {
        return $e->getMessage();
    }
}

// Add this function at the top of the file
function validatePassword($password) {
    // Minimum 8 characters
    if (strlen($password) < 8) {
        return "Password must be at least 8 characters long";
    }
    
    // Must contain at least one uppercase letter
    if (!preg_match('/[A-Z]/', $password)) {
        return "Password must contain at least one uppercase letter";
    }
    
    // Must contain at least one lowercase letter
    if (!preg_match('/[a-z]/', $password)) {
        return "Password must contain at least one lowercase letter";
    }
    
    // Must contain at least one number
    if (!preg_match('/[0-9]/', $password)) {
        return "Password must contain at least one number";
    }
    
    // Must contain at least one special character
    if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
        return "Password must contain at least one special character";
    }
    
    return true;
}

// Handle backup and restore actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_profile'])) {
        $firstname = $db->escapeString($_POST['firstname']);
        $lastname = $db->escapeString($_POST['lastname']);
        $email = $db->escapeString($_POST['email']);

        $update_query = "UPDATE admin SET 
                        firstname = ?, 
                        lastname = ?, 
                        email = ? 
                        WHERE admin_id = ?";
        
        $stmt = $db->prepare($update_query);
        $stmt->bind_param("sssi", $firstname, $lastname, $email, $admin_id);
        
        if ($stmt->execute()) {
            $message = "Profile updated successfully!";
            // Update session data
            $_SESSION['admin_firstname'] = $firstname;
            $_SESSION['admin_lastname'] = $lastname;
        } else {
            $error = "Error updating profile";
        }
    }

    if (isset($_POST['change_password'])) {
        $current_password = md5($_POST['current_password']);
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // Verify current password
        $verify_query = "SELECT password FROM admin WHERE admin_id = ?";
        $stmt = $db->prepare($verify_query);
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if ($result['password'] === $current_password) {
            if ($new_password === $confirm_password) {
                // Validate password strength
                $password_validation = validatePassword($new_password);
                if ($password_validation === true) {
                    $hashed_password = md5($new_password);
                    $update_query = "UPDATE admin SET password = ? WHERE admin_id = ?";
                    $stmt = $db->prepare($update_query);
                    $stmt->bind_param("si", $hashed_password, $admin_id);
                    
                    if ($stmt->execute()) {
                        $message = "Password changed successfully!";
                    } else {
                        $error = "Error changing password";
                    }
                } else {
                    $error = $password_validation;
                }
            } else {
                $error = "New passwords do not match";
            }
        } else {
            $error = "Current password is incorrect";
        }
    }

    if (isset($_POST['backup_db'])) {
        $backup_file = backupDatabase($db);
        if ($backup_file) {
            $message = "Database backup created successfully: " . $backup_file;
        } else {
            $error = "Error creating database backup";
        }
    }

    if (isset($_POST['restore_db']) && isset($_FILES['backup_file'])) {
        $file = $_FILES['backup_file'];
        if ($file['error'] == 0 && pathinfo($file['name'], PATHINFO_EXTENSION) == 'sql') {
            $temp_name = $file['tmp_name'];
            $backup_path = "../backups/";
            
            if (!file_exists($backup_path)) {
                mkdir($backup_path, 0777, true);
            }
            
            $dest = $backup_path . basename($file['name']);
            if (move_uploaded_file($temp_name, $dest)) {
                $restore_result = restoreDatabase($db, basename($file['name']));
                if ($restore_result === true) {
                    $message = "Database restored successfully";
                } else {
                    $error = "Error restoring database: " . $restore_result;
                }
            } else {
                $error = "Error uploading backup file";
            }
        } else {
            $error = "Invalid backup file";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Settings - CamerinoHub</title>
    
    <!-- External CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="icon" href="../images/light-logo.png">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/dashboard-shared.css">
    <style>
        .settings-container {
            padding: 20px;
        }
        .settings-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .settings-card .card-header {
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            padding: 15px 20px;
        }
        .settings-card .card-body {
            padding: 20px;
        }
        .form-group label {
            font-weight: 500;
        }
        .alert {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include 'includes/navigation.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="settings-container">
                <h2 class="mb-4">Settings</h2>

                <?php if ($message): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $message; ?>
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $error; ?>
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <!-- Profile Settings -->
                <div class="settings-card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-user-circle mr-2"></i>Profile Settings</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="form-group">
                                <label for="firstname">First Name</label>
                                <input type="text" class="form-control" id="firstname" name="firstname" 
                                       value="<?php echo htmlspecialchars($admin['firstname']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="lastname">Last Name</label>
                                <input type="text" class="form-control" id="lastname" name="lastname" 
                                       value="<?php echo htmlspecialchars($admin['lastname']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                            </div>
                            <button type="submit" name="update_profile" class="btn btn-primary">
                                Update Profile
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Password Settings -->
                <div class="settings-card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-lock mr-2"></i>Change Password</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="" id="passwordForm">
                            <div class="form-group">
                                <label for="current_password">Current Password</label>
                                <input type="password" class="form-control" id="current_password" 
                                       name="current_password" required>
                            </div>
                            <div class="form-group">
                                <label for="new_password">New Password</label>
                                <input type="password" class="form-control" id="new_password" 
                                       name="new_password" required>
                            </div>
                            <div class="form-group">
                                <label for="confirm_password">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirm_password" 
                                       name="confirm_password" required>
                            </div>
                            <button type="submit" name="change_password" class="btn btn-primary">
                                Change Password
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Database Management -->
                <div class="settings-card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-database mr-2"></i>Database Management</h5>
                    </div>
                    <div class="card-body">
                        <!-- Backup Database -->
                        <form method="POST" action="" class="mb-4">
                            <h6 class="mb-3">Backup Database</h6>
                            <p class="text-muted">Create a backup of the current database state</p>
                            <button type="submit" name="backup_db" class="btn btn-primary">
                                <i class="fas fa-download mr-2"></i>Create Backup
                            </button>
                        </form>

                        <!-- Restore Database -->
                        <form method="POST" action="" enctype="multipart/form-data">
                            <h6 class="mb-3">Restore Database</h6>
                            <p class="text-muted">Restore database from a previous backup file</p>
                            <div class="form-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="backup_file" name="backup_file" accept=".sql" required>
                                    <label class="custom-file-label" for="backup_file">Choose backup file</label>
                                </div>
                            </div>
                            <button type="submit" name="restore_db" class="btn btn-warning" onclick="return confirm('Warning: This will overwrite the current database. Are you sure you want to proceed?')">
                                <i class="fas fa-upload mr-2"></i>Restore Database
                            </button>
                        </form>
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
    
    <script>
    $(document).ready(function() {
        // Password validation
        $('#passwordForm').on('submit', function(e) {
            const newPassword = $('#new_password').val();
            const confirmPassword = $('#confirm_password').val();

            // Check password match
            if (newPassword !== confirmPassword) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Password Mismatch',
                    text: 'New password and confirmation password do not match!'
                });
                return;
            }

            // Check password strength
            if (newPassword.length < 8) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Weak Password',
                    text: 'Password must be at least 8 characters long'
                });
                return;
            }

            if (!/[A-Z]/.test(newPassword)) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Weak Password',
                    text: 'Password must contain at least one uppercase letter'
                });
                return;
            }

            if (!/[a-z]/.test(newPassword)) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Weak Password',
                    text: 'Password must contain at least one lowercase letter'
                });
                return;
            }

            if (!/[0-9]/.test(newPassword)) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Weak Password',
                    text: 'Password must contain at least one number'
                });
                return;
            }

            if (!/[!@#$%^&*(),.?":{}|<>]/.test(newPassword)) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Weak Password',
                    text: 'Password must contain at least one special character'
                });
                return;
            }
        });

        // Add password requirements helper text
        $('#new_password').after(`
            <small class="form-text text-muted">
                Password must contain:
                <ul>
                    <li>At least 8 characters</li>
                    <li>At least one uppercase letter</li>
                    <li>At least one lowercase letter</li>
                    <li>At least one number</li>
                    <li>At least one special character (!@#$%^&*(),.?":{}|<>)</li>
                </ul>
            </small>
        `);

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);

        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });
    });

    function confirmLogout(event) {
        event.preventDefault();
        
        Swal.fire({
            title: 'Ready to Leave?',
            text: "Are you sure you want to logout?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, logout',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'logout.php';
            }
        });
    }
    </script>
</body>
</html>
