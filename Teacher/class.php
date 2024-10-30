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
$userData = null;
if ($isLoggedIn) {
    require_once('../db/dbConnector.php');
    $db = new DbConnector();
    
    $teacher_id = $_SESSION['id'];
    $query = "SELECT * FROM teacher WHERE teacher_id = '$teacher_id'";
    $result = $db->query($query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $userData = mysqli_fetch_array($result);
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
    <link rel="stylesheet" href="class.css">
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
                    <li class="nav-item"><a class="nav-link active" href="class.php">Class</a></li>
                    <li class="nav-item"><a class="nav-link" href="subject.php">Subject</a></li>
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
                                <a class="dropdown-item" href="../Student/logout.php">Logout</a>
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
                    <h1>Welcome<?php echo $isLoggedIn ? ', ' . htmlspecialchars($userData['firstname']) : ''; ?> to<br>
                        <span class="highlight">Gov D.M. Camerino</span>
                    </h1>
                    <p class="lead">Learn Anywhere, Anytime: Empower Your Education</p>
                    <?php if ($isLoggedIn): ?>
                    <div class="cta-buttons">
                        <a href="teacher_dashboard.php" class="btn btn-primary">Dashboard</a>
                        <a href="class.php" class="btn btn-outline-primary">Manage Classes</a>
                    </div>
                    <?php endif; ?>
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

    
    <!-- Table Class Section -->
    <div class="container mt-5">
        <h2>Class Table</h2>
        <div class="d-flex justify-content-between align-items-center">
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle" type="button" id="recordsPerPage" data-bs-toggle="dropdown" aria-expanded="false">
                    Records per page
                </button>
                <ul class="dropdown-menu" aria-labelledby="recordsPerPage">
                    <li><a class="dropdown-item" href="#">10</a></li>
                    <li><a class="dropdown-item" href="#">25</a></li>
                    <li><a class="dropdown-item" href="#">50</a></li>
                </ul>
            </div>
            <div>
                <button class="btn btn-danger">Delete</button>
                <button class="btn btn-light">Filters</button>
                <button class="btn btn-light">Export</button>
                <a href="class_add_subject.php" class="btn btn-primary">+ Add Class</a>
            </div>
            <div>
                <input type="text" class="form-control" placeholder="Search">
            </div>
        </div>

        <table class="table mt-3">
            <thead class="table-light">
                <tr>
                    <th>Class</th>
                    <th>Subjects</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><button class='btn btn-primary'>SST-4A</button></td>
                    <td><a href='class-subject.php?subject=Mathematics' class='link-item'>Mathematics</a></td>
                    <td><button class='btn btn-danger'>Delete</button></td>
                </tr>
                <tr>
                    <td><button class='btn btn-primary'>MT-4A</button></td>
                    <td><a href='class-subject.php?subject=Filipino' class='link-item'>Filipino</a></td>
                    <td><button class='btn btn-danger'>Delete</button></td>
                </tr>
                <tr>
                    <td><button class='btn btn-primary'>SCT-4A</button></td>
                    <td><a href='class-subject.php?subject=Araling Panlipunan' class='link-item'>Araling Panlipunan</a></td>
                    <td><button class='btn btn-danger'>Delete</button></td>
                </tr>
                <tr>
                    <td><button class='btn btn-primary'>GCT-4A</button></td>
                    <td><a href='class-subject.php?subject=English' class='link-item'>English</a></td>
                    <td><button class='btn btn-danger'>Delete</button></td>
                </tr>
                <tr>
                    <td><button class='btn btn-primary'>SCT-4A</button></td>
                    <td><a href='class-subject.php?subject=Science' class='link-item'>Science</a></td>
                    <td><button class='btn btn-danger'>Delete</button></td>
                </tr>
                <tr>
                    <td><button class='btn btn-primary'>SHED-4A</button></td>
                    <td><a href='class-subject.php?subject=Edukasyon sa Pagpapakatao' class='link-item'>Edukasyon sa Pagpapakatao</a></td>
                    <td><button class='btn btn-danger'>Delete</button></td>
                </tr>
                <tr>
                    <td><button class='btn btn-primary'>TLE-4A</button></td>
                    <td><a href='class-subject.php?subject=TLE' class='link-item'>TLE</a></td>
                    <td><button class='btn btn-danger'>Delete</button></td>
                </tr>
                <tr>
                    <td><button class='btn btn-primary'>MAPEH-4A</button></td>
                    <td><a href='class-subject.php?subject=MAPEH' class='link-item'>MAPEH</a></td>
                    <td><button class='btn btn-danger'>Delete</button></td>
                </tr>
                <tr>
                    <td><button class='btn btn-primary'>PHIL-4A</button></td>
                    <td><a href='class-subject.php?subject=Philosophy' class='link-item'>Philosophy</a></td>
                    <td><button class='btn btn-danger'>Delete</button></td>
                </tr>
            </tbody>
        </table>

        <nav>
            <ul class="pagination">
                <li class="page-item"><a class="page-link" href="#">Previous</a></li>
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

</body>
</html>
