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
$query = "SELECT * FROM teacher WHERE teacher_id = '$teacher_id'";
$result = $db->query($query);
$userData = mysqli_fetch_array($result);

// Get students data
$students_query = "SELECT * FROM student WHERE teacher_id = ?";
$stmt = $db->prepare($students_query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$students_result = $stmt->get_result();

// Handle student deletion
if (isset($_POST['delete_student'])) {
    $student_id = $_POST['student_id'];
    $delete_query = "DELETE FROM student WHERE student_id = ? AND teacher_id = ?";
    $stmt = $db->prepare($delete_query);
    $stmt->bind_param("ii", $student_id, $teacher_id);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Student deleted successfully";
    } else {
        $_SESSION['error'] = "Error deleting student";
    }
    header("Location: student.php");
    exit();
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
    <link rel="stylesheet" href="student.css">
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
                    <li class="nav-item"><a class="nav-link" href="subject.php">Subject</a></li>
                    <li class="nav-item"><a class="nav-link active" href="student.php">Student</a></li>
                    <?php if ($isLoggedIn): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" 
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <?php echo htmlspecialchars($userData['firstname'] ?? 'My Account'); ?>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="teacher_dashboard.php">
                                    <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                                </a>
                                <a class="dropdown-item" href="teacher_profile.php">
                                    <i class="fas fa-user mr-2"></i>Profile
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="logout.php">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                </a>
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
                        <a href="profile.php" class="btn btn-primary"> Hello! Sir/Mam
                            <?php 
                            if ($isLoggedIn && isset($userData['firstname'])) {
                                echo htmlspecialchars($userData['firstname']);
                            } else {
                                echo 'Guest User';
                            }
                            ?>
                        </a>
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
                    <a href="subject.php" class="link-item">
                        <i class="fas fa-book"></i>
                        <span>Subjects</span>
                    </a>
                    <a href="student.php" class="link-item active">
                        <i class="fas fa-user-graduate"></i>
                        <span>Student</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

   <!-- Content Section -->
   <div class="container">
        <!-- Header Section -->
        <div class="table-header">
            <h2>Subjects: <span style="font-weight: bold;">Mathematics</span></h2>
            <h4>Students</h4>
        </div>

        <!-- Action Buttons Section -->
        <div class="table-actions d-flex justify-content-between">
            <div>
                <button class="btn btn-light dropdown-toggle" data-toggle="dropdown">10</button>
                <button class="btn btn-danger">Delete</button>
                <button class="btn btn-light">Filters</button>
                <button class="btn btn-light">Upload a File</button>
            </div>
            <div>
                <button class="btn btn-primary">+ Add New Students</button>
            </div>
            <div>
                <input type="text" class="form-control" placeholder="Search">
            </div>
        </div>

        <!-- Table Section -->
        <div class="table-responsive mt-3">
            <table class="table table-bordered custom-table">
                <thead class="thead-light">
                    <tr>
                        <th><input type="checkbox" id="selectAll"></th>
                        <th>Photo</th>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Class</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($students_result->num_rows > 0): ?>
                        <?php while ($student = $students_result->fetch_assoc()): ?>
                            <tr>
                                <td><input type="checkbox" class="student-select" value="<?php echo $student['student_id']; ?>"></td>
                                <td>
                                    <img src="<?php echo htmlspecialchars($student['photo_url'] ?? '../images/default-avatar.png'); ?>" 
                                         alt="Student Photo" class="student-photo" style="width: 50px; height: 50px; border-radius: 50%;">
                                </td>
                                <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                                <td><?php echo htmlspecialchars($student['firstname'] . ' ' . ($student['middle_name'] ? $student['middle_name'] . ' ' : '') . $student['lastname']); ?></td>
                                <td><?php echo htmlspecialchars($student['cys'] ?? 'Not Assigned'); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="viewStudent(<?php echo $student['student_id']; ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteStudent(<?php echo $student['student_id']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No students found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
                <li class="page-item"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">Next</a></li>
            </ul>
        </nav>
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

    <!-- Add JavaScript for student management -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
    function viewStudent(studentId) {
        window.location.href = `view_student.php?id=${studentId}`;
    }

    function deleteStudent(studentId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#333',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            showLoaderOnConfirm: true,
            preConfirm: () => {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'student.php';
                
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'delete_student';
                input.value = studentId;
                
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    // Bulk delete functionality
    document.querySelector('.btn-danger').addEventListener('click', function() {
        const selectedStudents = Array.from(document.querySelectorAll('.student-select:checked'))
                                    .map(checkbox => checkbox.value);
        
        if (selectedStudents.length === 0) {
            Swal.fire({
                icon: 'info',
                title: 'No Selection',
                text: 'Please select students to delete',
                confirmButtonColor: '#333'
            });
            return;
        }

        Swal.fire({
            title: 'Delete Selected Students?',
            text: `You are about to delete ${selectedStudents.length} students. This cannot be undone!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#333',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete them!',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Add your bulk delete logic here
                Swal.fire(
                    'Deleted!',
                    'Selected students have been deleted.',
                    'success'
                );
            }
        });
    });

    // Select all functionality
    document.getElementById('selectAll').addEventListener('change', function() {
        document.querySelectorAll('.student-select').forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Show success/error messages
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

    // Search functionality enhancement
    document.querySelector('input[placeholder="Search"]').addEventListener('keyup', function() {
        const searchText = this.value.toLowerCase();
        document.querySelectorAll('.custom-table tbody tr').forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchText) ? '' : 'none';
        });
    });

    $(document).ready(function() {
        // Initialize dropdowns
        $('.dropdown-toggle').dropdown();

        // Add hover functionality
        $('.dropdown').hover(
            function() { 
                $(this).find('.dropdown-menu').stop(true, true).delay(200).fadeIn(300); 
            },
            function() { 
                $(this).find('.dropdown-menu').stop(true, true).delay(200).fadeOut(300); 
            }
        );

        // Ensure dropdown works on click as well
        $('.dropdown-toggle').click(function(e) {
            e.preventDefault();
            $(this).parent().toggleClass('show');
            $(this).next('.dropdown-menu').toggleClass('show');
        });

        // Close dropdown when clicking outside
        $(document).click(function(e) {
            if (!$(e.target).closest('.dropdown').length) {
                $('.dropdown-menu').removeClass('show');
                $('.dropdown').removeClass('show');
            }
        });
    });
    </script>
</body>
</html>
