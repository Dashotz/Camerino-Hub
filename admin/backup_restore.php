<?php
session_start();
require_once('../db/dbConnector.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$db = new DbConnector();
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
    $backup .= "-- CamerinoHub Database Backup\n";
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

// Function to get list of backup files
function getBackupFiles() {
    $backup_path = "../backups/";
    $files = array();
    
    if (file_exists($backup_path)) {
        foreach (scandir($backup_path) as $file) {
            if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
                $files[] = array(
                    'name' => $file,
                    'size' => filesize($backup_path . $file),
                    'date' => date("Y-m-d H:i:s", filemtime($backup_path . $file))
                );
            }
        }
    }
    
    return $files;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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

    if (isset($_POST['delete_backup'])) {
        $file = $_POST['file_name'];
        $backup_path = "../backups/";
        if (unlink($backup_path . $file)) {
            $message = "Backup file deleted successfully";
        } else {
            $error = "Error deleting backup file";
        }
    }
}

// Get list of existing backups
$backup_files = getBackupFiles();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Backup & Restore - CamerinoHub</title>
    
    <!-- External CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="icon" href="../images/light-logo.png">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/dashboard-shared.css">
    <style>
        .backup-container {
            padding: 20px;
        }
        .backup-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .backup-card .card-header {
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            padding: 15px 20px;
        }
        .backup-card .card-body {
            padding: 20px;
        }
        .backup-list {
            max-height: 400px;
            overflow-y: auto;
        }
        .backup-item {
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }
        .backup-item:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <?php include 'includes/navigation.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="backup-container">
                <h2 class="mb-4">Database Backup & Restore</h2>

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

                <div class="row">
                    <!-- Backup Operations -->
                    <div class="col-md-6">
                        <div class="backup-card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-download mr-2"></i>Create Backup</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="">
                                    <p class="text-muted">Create a new backup of the current database state</p>
                                    <button type="submit" name="backup_db" class="btn btn-primary">
                                        <i class="fas fa-database mr-2"></i>Create Backup
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="backup-card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-upload mr-2"></i>Restore Database</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="" enctype="multipart/form-data">
                                    <p class="text-muted">Restore database from a backup file</p>
                                    <div class="form-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="backup_file" name="backup_file" accept=".sql" required>
                                            <label class="custom-file-label" for="backup_file">Choose backup file</label>
                                        </div>
                                    </div>
                                    <button type="submit" name="restore_db" class="btn btn-warning" onclick="return confirm('Warning: This will overwrite the current database. Are you sure you want to proceed?')">
                                        <i class="fas fa-undo mr-2"></i>Restore Database
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Backup Files List -->
                    <div class="col-md-6">
                        <div class="backup-card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-history mr-2"></i>Backup History</h5>
                            </div>
                            <div class="card-body">
                                <div class="backup-list">
                                    <?php if (empty($backup_files)): ?>
                                        <p class="text-muted text-center">No backup files found</p>
                                    <?php else: ?>
                                        <?php foreach ($backup_files as $file): ?>
                                            <div class="backup-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong><?php echo $file['name']; ?></strong><br>
                                                    <small class="text-muted">
                                                        Size: <?php echo number_format($file['size'] / 1024, 2); ?> KB<br>
                                                        Date: <?php echo $file['date']; ?>
                                                    </small>
                                                </div>
                                                <div>
                                                    <a href="../backups/<?php echo $file['name']; ?>" download class="btn btn-sm btn-success">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    <form method="POST" action="" style="display: inline;">
                                                        <input type="hidden" name="file_name" value="<?php echo $file['name']; ?>">
                                                        <button type="submit" name="delete_backup" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this backup?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
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
    
    <script>
    $(document).ready(function() {
        // File input label
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);
    });
    </script>
</body>
</html> 