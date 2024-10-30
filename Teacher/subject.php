<?php
// Initialize an array to hold subjects
$subjects = [
    "GDM - 200" => "Mathematics",
    "GDM - 201" => "Filipino",
    "GDM - 202" => "Aralin Panlipunan",
    "GDM - 203" => "English",
    "GDM - 204" => "Science",
    "GDM - 205" => "Eduaksyon sa Pagpapakatao",
    "GDM - 206" => "Technology and Livelihood Education (TLE)",
    "GDM - 207" => "Music, Arts, PE, and Health (MAPEH)"
];

// Check if a subject has been added
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['subject'])) {
    $new_subject_code = $_POST['subject'];
    // Add the new subject to the array (you can implement a database insert here)
    // For demonstration, we will just append it to the array
    $subjects[$new_subject_code] = $new_subject_code; // Adjust as needed
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
    <link rel="stylesheet" href="class-subject.css">
    <link rel="stylesheet" href="subject.css"> <!-- Link to subject.css -->
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
                    <li class="nav-item"><a class="nav-link" href="site-map.php">Class</a></li>
                    <li class="nav-item"><a class="nav-link" href="News.php">Subject</a></li>
                    <li class="nav-item"><a class="nav-link" href="aboutus.php">Student</a></li>
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
                    <div class="cta-buttons">
                        <a href="profile.php" class="btn btn-primary">User Name</a>
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
        <div class="table-header">
            <h2>Subject Table</h2>
            <div class="d-flex align-items-center">
                <select class="form-control mr-2" style="width: auto;">
                    <option>10</option>
                    <option>20</option>
                    <option>50</option>
                </select>
                <button class="btn btn-light">Delete</button>
                <button class="btn btn-light">Filters</button>
                <button class="btn btn-light">Export</button>
                <a href="add_subject.php" class="btn btn-primary">+ Add Subjects</a>
                <input type="text" class="form-control ml-2" placeholder="Search">
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Subject Code</th>
                        <th>Subject Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($subjects as $code => $description): ?>
                    <tr>
                        <td><?php echo $code; ?></td>
                        <td><?php echo $description; ?></td>
                        <td>
                            <button class="btn btn-danger">Delete</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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
</body>
</html>
