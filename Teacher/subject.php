<?php
session_start();

// Initialize login status
$isLoggedIn = isset($_SESSION['id']);

// Redirect if not logged in
if (!$isLoggedIn) {
    header("Location: ../Student/Teacher-Login.php");
    exit();
}

// Get teacher data
require_once('../db/dbConnector.php');
$db = new DbConnector();

$teacher_id = $_SESSION['id'];
$query = "SELECT * FROM teacher WHERE teacher_id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();

// Add this debug line temporarily
error_log("User Data: " . print_r($userData, true));

// First, let's debug the table structure
$debug_query = "SHOW TABLES";
$result = $db->query($debug_query);
while ($row = $db->fetchArray($result)) {
    error_log("Table found: " . $row[0]); // This will write to PHP error log
}

// Now let's check the structure of the subjects table
$structure_query = "DESCRIBE subject";
$result = $db->query($structure_query);
if ($result) {
    while ($row = $db->fetchArray($result)) {
        error_log("Column: " . $row['Field']);
    }
}

// Simplified query to fetch subjects
$query = "SELECT subject_id, subject_code, subject_title, category FROM subject";
$result = $db->query($query);
$subjects = [];
if ($result) {
    while ($row = $db->fetchArray($result)) {
        $subjects[] = $row;
    }
}

// Handle subject deletion
if (isset($_POST['delete_subject'])) {
    $subject_id = $db->escapeString($_POST['subject_id']);
    $delete_query = "DELETE FROM subject WHERE subject_id = '$subject_id'";
    
    if ($db->query($delete_query)) {
        $_SESSION['message'] = "Subject deleted successfully";
    } else {
        $_SESSION['error'] = "Error deleting subject";
    }
    header("Location: subject.php");
    exit();
}

// Add this after your database connection
$tables_query = "SHOW TABLES";
$tables_result = $db->query($tables_query);
while ($table = mysqli_fetch_array($tables_result)) {
    echo "<!-- Table: " . $table[0] . " -->";
}

// Then let's see the structure of the specific table
$structure_query = "DESCRIBE subject";
$structure_result = $db->query($structure_query);
if ($structure_result) {
    while ($row = mysqli_fetch_assoc($structure_result)) {
        echo "<!-- Column: " . $row['Field'] . " -->";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gov D.M. Camerino</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="subject.css"> <!-- Link to subject.css -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .table-header h2 {
            margin: 0;
        }
        .table-header .btn {
            margin-left: 10px;
        }
        .records-info {
            color: #007bff; /* Blue color for records info */
        }

        /* Enhanced Table Container */
        .table-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 0 30px rgba(0,0,0,0.1);
            padding: 25px;
            margin: 30px 0;
        }

        /* Enhanced Table Styles */
        .table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            border-radius: 8px;
            overflow: hidden;
        }

        .table thead th {
            background: linear-gradient(45deg, #007bff, #0056b3);
            color: white;
            padding: 15px;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
            border: none;
        }

        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
            transform: scale(1.01);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .table td {
            padding: 15px;
            vertical-align: middle;
            border: 1px solid #dee2e6;
        }

        /* Enhanced Buttons */
        .btn-action {
            border-radius: 20px;
            padding: 8px 15px;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.3s ease;
            margin: 0 3px;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        /* Search Box Enhancement */
        .search-box {
            position: relative;
            margin-bottom: 20px;
        }

        .search-box input {
            padding: 10px 40px 10px 20px;
            border-radius: 25px;
            border: 2px solid #e9ecef;
            width: 100%;
            transition: all 0.3s ease;
        }

        .search-box input:focus {
            border-color: #007bff;
            box-shadow: 0 0 15px rgba(0,123,255,0.1);
        }

        .search-box i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }

        /* Records Per Page Selector */
        #recordsPerPage {
            border-radius: 20px;
            padding: 8px 15px;
            border: 2px solid #e9ecef;
            cursor: pointer;
            background: white;
        }
    </style>
</head>
<body>
    <!-- Header and Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white">
        <div class="container">
            <a class="navbar-brand" href="#">
            <img src="../images/logo.png" alt="Gov D.M. Camerino" class="navbar-logo">
            <span class="logo-text">Gov D.M. Camerino</span>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a class="nav-link" href="home.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="class.php">Class</a></li>
                    <li class="nav-item"><a class="nav-link active" href="subject.php">Subject</a></li>
                    <li class="nav-item"><a class="nav-link" href="student.php">Student</a></li>
                    <?php if ($isLoggedIn): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" 
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <?php echo htmlspecialchars($userData['firstname'] ?? 'My Account'); ?>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="teacher_dashboard.php">Dashboard</a>
                                <a class="dropdown-item" href="teacher_profile.php">Profile</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="logout.php">Logout</a>
                            </div>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

        <!-- Hero Section -->
        <section class="hero section-gap">
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <h1>Welcome to<br><span class="highlight">Gov D.M. Camerino</span></h1>
                    <p class="lead">Learn Anywhere, Anytime: Empower Your Education</p>
                    <div class="cta-buttons">
                        <a href="profile.php" class="btn btn-primary">Hello! Sir/Mam <?php echo htmlspecialchars($userData['firstname'] ?? 'My Account'); ?></a>
                    </div>
                </div>
                <div class="hero-image">
                    <img src="../images/student.png" alt="Students">
                </div>
            </div>
            
            <div class="search-container">
                <input type="text" placeholder="Search something...">
                <button class="btn-search">Search</button>
            </div>
            
            <div class="quick-links">
                <p>You may be looking for</p>
                <div class="links">
                    <a href="home.php" class="link-item">
                        <i class="fas fa-home"></i>
                        <span>Home</span>
                    </a>
                    <a href="class.php" class="link-item">
                        <i class="fas fa-users"></i>
                        <span>Class</span>
                    </a>
                    <a href="subject.php" class="link-item active">
                        <i class="fas fa-book"></i>
                        <span>Subjects</span>
                    </a>
                    <a href="student.php" class="link-item">
                        <i class="fas fa-user-graduate"></i>
                        <span>Student</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Table Section -->
    <div class="container mt-5">
        <div class="table-container">
            <div class="table-header mb-4">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h2 class="mb-0"><i class="fas fa-book mr-2"></i>Subject Management</h2>
                    </div>
                    <div class="col-md-6 text-md-right">
                        <button class="btn btn-action btn-danger mr-2" id="bulkDelete">
                            <i class="fas fa-trash-alt"></i> Bulk Delete
                        </button>
                        <a href="add_subject.php" class="btn btn-action btn-primary">
                            <i class="fas fa-plus"></i> Add Subject
                        </a>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="search-box">
                        <input type="text" id="searchSubject" class="form-control" placeholder="Search subjects...">
                        <i class="fas fa-search"></i>
                    </div>
                </div>
                <div class="col-md-2">
                    <select class="form-control" id="recordsPerPage">
                        <option value="10">10 per page</option>
                        <option value="20">20 per page</option>
                        <option value="50">50 per page</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll"></th>
                            <th>Subject ID</th>
                            <th>Subject Code</th>
                            <th>Subject Title</th>
                            <th>Category</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($subjects as $subject): ?>
                        <tr>
                            <td><input type="checkbox" class="subject-select" value="<?php echo $subject['subject_id']; ?>"></td>
                            <td><?php echo htmlspecialchars($subject['subject_id']); ?></td>
                            <td><?php echo htmlspecialchars($subject['subject_code']); ?></td>
                            <td><?php echo htmlspecialchars($subject['subject_title']); ?></td>
                            <td><span class="badge badge-primary"><?php echo htmlspecialchars($subject['category']); ?></span></td>
                            <td>
                                <button class="btn btn-action btn-info btn-sm" onclick="editSubject(<?php echo $subject['subject_id']; ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-action btn-danger btn-sm" onclick="deleteSubject(<?php echo $subject['subject_id']; ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-primary text-light py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5 class="mb-3">Gov D.M. Camerino</h5>
                    <p>Medicion 2, A.Imus City, Cavite 4103</p>
                    <p>+(64) 456 - 5874</p>
                    <p>profcamerino@yahoo.com</p>
                    <div class="social-icons">
                        <a href="#" class="text-light"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-light"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-light"><i class="fab fa-youtube"></i></a>
                        <a href="#" class="text-light"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="col-md-4">
                    <h5 class="mb-3">Quicklinks</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-light">Home</a></li>
                        <li><a href="#" class="text-light">About Us</a></li>
                        <li><a href="#" class="text-light">Our Gallery</a></li>
                        <li><a href="#" class="text-light">News and Updates</a></li>
                        <li><a href="#" class="text-light">Contact Us</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5 class="mb-3">Government Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-light">Learner Information System</a></li>
                        <li><a href="#" class="text-light">DepEd CALABARZON</a></li>
                        <li><a href="#" class="text-light">DepEd Imus City</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="text-center mt-4">
            <p>&copy; 2024 All Rights Reserved</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    // Delete subject function
    function deleteSubject(subjectId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            backdrop: true,
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return new Promise((resolve) => {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.innerHTML = `<input type="hidden" name="subject_id" value="${subjectId}">
                                    <input type="hidden" name="delete_subject" value="1">`;
                    document.body.appendChild(form);
                    form.submit();
                });
            }
        });
    }

    // Edit subject function
    function editSubject(subjectId) {
        window.location.href = `edit_subject.php?id=${subjectId}`;
    }

    // Select all functionality
    document.getElementById('selectAll').addEventListener('change', function() {
        document.querySelectorAll('.subject-select').forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Bulk delete
    document.getElementById('bulkDelete').addEventListener('click', function() {
        const selectedSubjects = Array.from(document.querySelectorAll('.subject-select:checked'))
                                     .map(checkbox => checkbox.value);
        
        if (selectedSubjects.length === 0) {
            Swal.fire({
                icon: 'info',
                title: 'No Selection',
                text: 'Please select subjects to delete',
                timer: 2000,
                showConfirmButton: false
            });
            return;
        }

        Swal.fire({
            title: 'Delete Selected Subjects?',
            text: `You are about to delete ${selectedSubjects.length} subjects. This cannot be undone!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete them!',
            cancelButtonText: 'Cancel',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                // Add your bulk delete logic here
            }
        });
    });

    // Enhanced success message
    <?php if (isset($_SESSION['message'])): ?>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '<?php echo $_SESSION['message']; ?>',
            timer: 3000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end',
            timerProgressBar: true
        });
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    // Enhanced error message
    <?php if (isset($_SESSION['error'])): ?>
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '<?php echo $_SESSION['error']; ?>',
            timer: 3000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end',
            timerProgressBar: true
        });
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    // Search functionality
    document.getElementById('searchSubject').addEventListener('keyup', function() {
        const searchText = this.value.toLowerCase();
        const tableRows = document.querySelectorAll('tbody tr');
        
        tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchText) ? '' : 'none';
        });
    });

    // Records per page functionality
    document.getElementById('recordsPerPage').addEventListener('change', function() {
        const rowsToShow = parseInt(this.value);
        const tableRows = document.querySelectorAll('tbody tr');
        
        tableRows.forEach((row, index) => {
            row.style.display = index < rowsToShow ? '' : 'none';
        });
    });
    </script>

    <!-- Add these before closing </body> tag -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

