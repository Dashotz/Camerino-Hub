<?php
session_start();

// Initialize login status
$isLoggedIn = isset($_SESSION['id']);

// Only redirect if trying to access protected pages
if (isset($requireLogin) && $requireLogin && !$isLoggedIn) {
    header("Location: ../Student/Teacher-Login.php");
    exit();
}

// Get user data if logged in
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
    <link rel="stylesheet" href="home.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                    <h1>Welcome<?php echo $isLoggedIn ? ', ' . htmlspecialchars($userData['firstname']) : ''; ?> to<br>
                        <span class="highlight">Gov D.M. Camerino</span>
                    </h1>
                    <p class="lead">Learn Anywhere, Anytime: Empower Your Education</p>
                    <div class="cta-buttons">
                        <?php if ($isLoggedIn): ?>
                            <a href="teacher_dashboard.php" class="btn btn-primary">Dashboard</a>
                            <a href="class.php" class="btn btn-outline-primary">Manage Classes</a>
                        <?php else: ?>
                            <a href="../Student/Teacher-Login.php" class="btn btn-primary">Login Now</a>
                        <?php endif; ?>
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
                    <a href="home.php" class="link-item active">
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
                    <a href="student.php" class="link-item">
                        <i class="fas fa-user-graduate"></i>
                        <span>Student</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

<!-- Photo Section -->
<section class="photos full-width section-gap">
        <div class="container">
            <h2>Photos</h2>
            <div class="custom-carousel" id="photoCarousel">
                <div class="carousel-container">
                    <div class="carousel-slide">
                        <img src="../images/1.jpg" alt="School Photo 1">
                    </div>
                    <div class="carousel-slide">
                        <img src="../images/2.jpg" alt="School Photo 2">
                    </div>
                    <div class="carousel-slide">
                        <img src="../images/3.jpg" alt="School Photo 3">
                    </div>
                </div>
                <button class="carousel-button prev">&lt;</button>
                <button class="carousel-button next">&gt;</button>
            </div>
        </div>
    </section>

    <!-- School Officials Section -->
    <section class="officials full-width section-gap">
        <div class="container">
            <h2>Our School Officials</h2>
            <div id="officialsCarousel" class="carousel slide" data-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <div class="row">
                            <div class="col">
                                <div class="profile">
                                    <img src="../images/wine.jpg" alt="Official 1">
                                    <h3>Tom Hiddleston</h3>
                                    <p>Campus Registrar</p>
                                </div>
                            </div>
                            <div class="col">
                                <div class="profile">
                                    <img src="../images/wine.jpg" alt="Official 2">
                                    <h3>Jesus V. Bergado</h3>
                                    <p>Principal</p>
                                </div>
                            </div>
                            <div class="col">
                                <div class="profile">
                                    <img src="../images/wine.jpg" alt="Official 3">
                                    <h3>Dr. Leona Uy</h3>
                                    <p>Campus Administrator</p>
                                </div>
                            </div>
                            <div class="col">
                                <div class="profile">
                                    <img src="../images/wine.jpg" alt="Official 4">
                                    <h3>River Phoenix</h3>
                                    <p>Campus Director</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Add more carousel items if needed -->
                </div>
                <ol class="carousel-indicators">
                    <li data-target="#officialsCarousel" data-slide-to="0" class="active"></li>
                    <li data-target="#officialsCarousel" data-slide-to="1"></li>
                    <li data-target="#officialsCarousel" data-slide-to="2"></li>
                </ol>
            </div>
        </div>
    </section>

    <!-- School Statistics Section -->
    <section class="school-statistics bg-light py-5 section-gap">
        <div class="container">
            <h2 class="text-center mb-5">Our School at a Glance</h2>
            <div class="row justify-content-center">
                <div class="col-md-4 col-sm-6 mb-4">
                    <div class="card text-center h-100">
                        <div class="card-body">
                            <i class="fas fa-user-graduate fa-3x text-primary mb-3"></i>
                            <h3 class="card-title">1200+</h3>
                            <p class="card-text">Enrolled Students</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 mb-4">
                    <div class="card text-center h-100">
                        <div class="card-body">
                            <i class="fas fa-book fa-3x text-danger mb-3"></i>
                            <h3 class="card-title">200+</h3>
                            <p class="card-text">Academic Programs</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 mb-4">
                    <div class="card text-center h-100">
                        <div class="card-body">
                            <i class="fas fa-chalkboard-teacher fa-3x text-success mb-3"></i>
                            <h3 class="card-title">600+</h3>
                            <p class="card-text">Faculty and Staff</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Leave a Comment and Suggestion Section -->
    <section class="comment-suggestion section-gap">
        <div class="container">
            <div class="logo-bar">
                <img src="../images/Logo.png" alt="DepEd Logo">
                <img src="../images/Logo.png" alt="Bagong Pilipinas Logo">
                <img src="../images/Logo.png" alt="Seal 1">
                <img src="../images/Logo.png" alt="Seal 2">
                <img src="../images/Logo.png" alt="Seal 3">
            </div>
            <h2>Leave a <span class="text-primary">Comment</span> and <span class="text-primary">Suggestion</span></h2>
            <div class="row">
                <div class="col-md-6">
                    <div class="image-container">
                        <img src="../images/students.jpg" alt="Students" class="img-fluid">
                        <div class="overlay">
                            <span class="emoji">😊</span>
                            <span class="text">84k+</span>
                            <span class="subtext">We're happy to help!</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <form id="commentForm">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <input type="text" class="form-control" id="firstName" placeholder="First Name" required>
                            </div>
                            <div class="form-group col-md-6">
                                <input type="text" class="form-control" id="lastName" placeholder="Last Name" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="email" class="form-control" id="email" placeholder="Email Address" required>
                        </div>
                        <div class="form-group">
                            <textarea class="form-control" id="comment" rows="4" placeholder="Comment" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

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
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const carousel = document.querySelector('#photoCarousel .carousel-container');
        const slides = carousel.querySelectorAll('.carousel-slide');
        const prevButton = document.querySelector('#photoCarousel .prev');
        const nextButton = document.querySelector('#photoCarousel .next');
        let currentIndex = 0;

        function showSlide(index) {
            carousel.style.transform = `translateX(-${index * 100}%)`;
        }

        function nextSlide() {
            currentIndex = (currentIndex + 1) % slides.length;
            showSlide(currentIndex);
        }

        function prevSlide() {
            currentIndex = (currentIndex - 1 + slides.length) % slides.length;
            showSlide(currentIndex);
        }

        nextButton.addEventListener('click', nextSlide);
        prevButton.addEventListener('click', prevSlide);

        // Optional: Auto-play
        setInterval(nextSlide, 5000);
    });

    // Welcome Alert Function
    function showWelcomeAlert() {
        Swal.fire({
            title: 'Welcome to Camerino Hub LMS!',
            html: `
                <div class="welcome-content">
                    <p class="welcome-text">
                        Hello Sir/Mam <?php echo htmlspecialchars($userData['firstname'] ?? ''); ?>!
                        Welcome to Gov. D. M. Camerino Learning Management System.
                        Your gateway to digital education excellence.
                    </p>
                    <p class="welcome-subtext">
                        Manage your classes, connect with students, and enhance the learning experience.
                    </p>
                </div>
            `,
            icon: 'success',
            confirmButtonText: 'Get Started',
            confirmButtonColor: '#007bff',
            allowOutsideClick: false,
            customClass: {
                popup: 'welcome-popup',
                title: 'welcome-title',
                content: 'welcome-content',
                confirmButton: 'welcome-button'
            }
        });
    }

    // Show alert when page loads if user just logged in
    <?php if ($isLoggedIn && isset($_SESSION['just_logged_in'])): ?>
    window.addEventListener('DOMContentLoaded', (event) => {
        showWelcomeAlert();
    });
    <?php 
        // Remove the flag so alert won't show again on refresh
        unset($_SESSION['just_logged_in']);
    endif; 
    ?>
    </script>

    <style>
    .welcome-popup {
        padding: 2rem;
        border-radius: 15px;
    }

    .welcome-title {
        color: #007bff;
        font-size: 1.8rem;
        margin-bottom: 1rem;
    }

    .welcome-text {
        font-size: 1.1rem;
        color: #333;
        margin-bottom: 1rem;
        line-height: 1.5;
    }

    .welcome-subtext {
        color: #666;
        font-size: 1rem;
        margin-bottom: 1.5rem;
    }

    .welcome-button {
        padding: 10px 30px;
        font-size: 1.1rem;
        border-radius: 25px;
        text-transform: uppercase;
        font-weight: 500;
    }

    .swal2-icon.swal2-success {
        border-color: #007bff;
        color: #007bff;
    }

    .swal2-icon.swal2-success [class^='swal2-success-line'] {
        background-color: #007bff;
    }

    .swal2-icon.swal2-success .swal2-success-ring {
        border-color: rgba(0, 123, 255, 0.3);
    }
    </style>
</body>
</html>