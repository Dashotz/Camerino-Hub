<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Subject - Gov D.M. Camerino</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="class-subject.css"> <!-- Updated to use class-subject.css -->
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
                    <li class="nav-item"><a class="nav-link" href="student.php">Student</a></li>
                    <li class="nav-item"><a class="nav-link btn-signup" href="#">Log Out</a></li>
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
                    <a href="class.php" class="link-item active">
                        <i class="fas fa-users"></i>
                        <span>Class</span>
                    </a>
                    <a href="subject.php" class="link-item">
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
                        <th>Photo</th>
                        <th>Name</th>
                        <th>Actions</th>
                        <th>Chapter</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Get subject ID from URL
                    $subject_id = isset($_GET['subject']) ? $_GET['subject'] : '';
                    
                    // Fetch students enrolled in this subject
                    $query = "SELECT s.*, e.* FROM students s 
                             JOIN enrollments e ON s.student_id = e.student_id 
                             WHERE e.subject_id = '$subject_id'";
                    $result = $db->query($query);
                    
                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td><img src='" . htmlspecialchars($row['photo_url']) . "' class='student-photo' alt='Student'></td>";
                            echo "<td>" . htmlspecialchars($row['firstname'] . ' ' . $row['lastname']) . "</td>";
                            echo "<td>
                                    <button class='btn btn-danger btn-sm' onclick='deleteStudent(" . $row['student_id'] . ")'>Delete</button>
                                  </td>";
                            // Add chapters and their actions here
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' class='text-center'>No students enrolled</td></tr>";
                    }
                    ?>
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

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
