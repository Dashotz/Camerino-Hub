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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_code = $db->escapeString($_POST['subject_code']);
    $subject_title = $db->escapeString($_POST['subject_title']);
    $category = $db->escapeString($_POST['category']);

    // Insert into student table
    $query = "INSERT INTO student (subject_code, subject_title, category) 
              VALUES ('$subject_code', '$subject_title', '$category')";
    
    if ($db->query($query)) {
        $_SESSION['message'] = "Subject added successfully";
        header("Location: subject.php");
        exit();
    } else {
        $error = "Error adding subject";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Subject - Gov D.M. Camerino</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="class-subject.css">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h2>Add New Subject</h2>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" action="add_subject.php">
                    <div class="form-group">
                        <label for="subject_code">Subject Code</label>
                        <input type="text" class="form-control" id="subject_code" name="subject_code" 
                               required placeholder="Enter subject code">
                    </div>

                    <div class="form-group">
                        <label for="subject_title">Subject Title</label>
                        <input type="text" class="form-control" id="subject_title" name="subject_title" 
                               required placeholder="Enter subject title">
                    </div>

                    <div class="form-group">
                        <label for="category">Category</label>
                        <select class="form-control" id="category" name="category" required>
                            <option value="">Select category</option>
                            <option value="Mathematics">Mathematics</option>
                            <option value="Science">Science</option>
                            <option value="English">English</option>
                            <option value="Filipino">Filipino</option>
                            <option value="Social Studies">Social Studies</option>
                            <option value="Physical Education">Physical Education</option>
                            <option value="Values Education">Values Education</option>
                            <option value="Technology and Livelihood Education">TLE</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <a href="subject.php" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Add Subject</button>
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
