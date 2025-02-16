<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$db = new DbConnector();
$admin_id = $_SESSION['admin_id'];

// Get subject details
$subject = null;
if (isset($_GET['id'])) {
    $query = "SELECT * FROM subjects WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $_GET['id']);
    $stmt->execute();
    $subject = $stmt->get_result()->fetch_assoc();
}

if (!$subject) {
    $_SESSION['error_message'] = "Subject not found";
    header("Location: manage_subjects.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Subject - Admin Dashboard</title>
    
    <!-- External CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href="css/dashboard-shared.css" rel="stylesheet">
	<link rel="icon" href="../images/light-logo.png">
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
                                <h4 class="mb-0">Edit Subject</h4>
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

                                <form action="handlers/subject_handler.php" method="POST">
                                    <input type="hidden" name="action" value="edit_subject">
                                    <input type="hidden" name="subject_id" value="<?php echo htmlspecialchars($subject['id']); ?>">
                                    
                                    <div class="form-group">
                                        <label>Subject Code*</label>
                                        <input type="text" class="form-control" name="subject_code" 
                                               value="<?php echo htmlspecialchars($subject['subject_code']); ?>" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Subject Title*</label>
                                        <input type="text" class="form-control" name="subject_title" 
                                               value="<?php echo htmlspecialchars($subject['subject_title']); ?>" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Category*</label>
                                        <select class="form-control" name="category" required>
                                            <option value="Core" <?php echo $subject['category'] == 'Core' ? 'selected' : ''; ?>>Core Subject</option>
                                            <option value="Major" <?php echo $subject['category'] == 'Major' ? 'selected' : ''; ?>>Major Subject</option>
                                            <option value="Minor" <?php echo $subject['category'] == 'Minor' ? 'selected' : ''; ?>>Minor Subject</option>
                                            <option value="Elective" <?php echo $subject['category'] == 'Elective' ? 'selected' : ''; ?>>Elective</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Description</label>
                                        <textarea class="form-control" name="description" rows="3"><?php echo htmlspecialchars($subject['description'] ?? ''); ?></textarea>
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">Update Subject</button>
                                        <a href="manage_subjects.php" class="btn btn-secondary">Cancel</a>
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
</body>
</html>