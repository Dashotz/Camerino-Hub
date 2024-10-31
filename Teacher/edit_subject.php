<?php
session_start();

// Initialize login status
$isLoggedIn = isset($_SESSION['id']);

// Redirect if not logged in
if (!$isLoggedIn) {
    header("Location: ../Student/Teacher-Login.php");
    exit();
}

require_once('../db/dbConnector.php');
$db = new DbConnector();

// Get subject ID from URL
$subject_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_id = $db->escapeString($_POST['subject_id']);
    $subject_code = $db->escapeString($_POST['subject_code']);
    $subject_title = $db->escapeString($_POST['subject_title']);
    $category = $db->escapeString($_POST['category']);

    // Update subject
    $query = "UPDATE subject 
              SET subject_code = '$subject_code', 
                  subject_title = '$subject_title', 
                  category = '$category' 
              WHERE subject_id = '$subject_id'";
    
    if ($db->query($query)) {
        $_SESSION['message'] = "Subject updated successfully";
        header("Location: subject.php");
        exit();
    } else {
        $error = "Error updating subject";
    }
}

// Get subject data
$query = "SELECT * FROM subject WHERE subject_id = '$subject_id'";
$result = $db->query($query);
$subject = mysqli_fetch_array($result);

// If subject not found, redirect
if (!$subject) {
    header("Location: subject.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Subject - Gov D.M. Camerino</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="class-subject.css">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h2>Edit Subject</h2>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" action="edit_subject.php">
                    <input type="hidden" name="subject_id" value="<?php echo $subject['subject_id']; ?>">
                    
                    <div class="form-group">
                        <label for="subject_code">Subject Code</label>
                        <input type="text" class="form-control" id="subject_code" name="subject_code" 
                               required value="<?php echo htmlspecialchars($subject['subject_code']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="subject_title">Subject Title</label>
                        <input type="text" class="form-control" id="subject_title" name="subject_title" 
                               required value="<?php echo htmlspecialchars($subject['subject_title']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="category">Category</label>
                        <select class="form-control" id="category" name="category" required>
                            <option value="">Select category</option>
                            <?php
                            $categories = [
                                'Mathematics', 'Science', 'English', 'Filipino',
                                'Social Studies', 'Physical Education', 'Values Education',
                                'Technology and Livelihood Education'
                            ];
                            foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat; ?>" 
                                    <?php echo ($subject['category'] === $cat) ? 'selected' : ''; ?>>
                                    <?php echo $cat; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <a href="subject.php" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Subject</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html> 